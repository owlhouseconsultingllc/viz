<?php

namespace PHPAuth;

class Auth
{
    protected $dbh;
    public $config;
    public $lang;

   
    public function __construct(\PDO $dbh, $config, $language = "en_GB")
    {
        $this->dbh = $dbh;
        $this->config = $config;

        if (version_compare(phpversion(), '5.5.0', '<')) {
            die('PHP 5.5.0 required for Auth engine!');
        }

        
        // Load language
        require "languages/{$language}.php";
        $this->lang = $lang;

        date_default_timezone_set($this->config->site_timezone);
    }

    public function login($email, $password, $remember = 0, $captcha = NULL)
    {
        $return['error'] = true;

        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            $return['message'] = $this->lang["user_verify_failed"];
            return $return;
        }

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];
            return $return;
        }

        $validateEmail = $this->validateEmail($email);
        $validatePassword = $this->validatePassword($password);

        if ($validateEmail['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_password_invalid"];

            return $return;
        } elseif ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_password_invalid"];

            return $return;
        } elseif ($remember != 0 && $remember != 1) {
            $this->addAttempt();
            $return['message'] = $this->lang["remember_me_invalid"];

            return $return;
        }

        $uid = $this->getUID(strtolower($email));

        if (!$uid) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_password_incorrect"];

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!password_verify($password, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_password_incorrect"];

            return $return;
        }

        if ($user['isactive'] != 1) {
            $this->addAttempt();
            $return['message'] = $this->lang["account_inactive"];

            return $return;
        }

        $sessiondata = $this->addSession($user['uid'], $remember);

        if ($sessiondata == false) {
            $return['message'] = $this->lang["system_error"] . " #01";

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->lang["logged_in"];

        $return['hash'] = $sessiondata['hash'];
        $return['expire'] = $sessiondata['expiretime'];
		
		$return['cookie_name'] = $this->config->cookie_name;

        return $return;
    }

	public function isEmailTaken($email)
    {
        $query = $this->dbh->prepare("SELECT count(*) FROM {$this->config->table_users} WHERE email = ?");
        $query->execute(array($email));

        if ($query->fetchColumn() == 0) {
            return false;
        }

        return true;
    }
	
	protected function addUser($email, $password, $params = array(), &$sendmail)
    {
        $return['error'] = true;

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_users} (isactive) VALUES (0)");

        if (!$query->execute()) {
            $return['message'] = $this->lang["system_error"] . " #03";
            return $return;
        }

        $uid = $this->dbh->lastInsertId("{$this->config->table_users}_id_seq");
        $email = htmlentities(strtolower($email));

        $isactive = 1;
        

        $password = $this->getHash($password);

        if (is_array($params)&& count($params) > 0) {
            $customParamsQueryArray = Array();

            foreach($params as $paramKey => $paramValue) {
                $customParamsQueryArray[] = array('value' => $paramKey . ' = ?');
            }

            $setParams = ', ' . implode(', ', array_map(function ($entry) {
                return $entry['value'];
            }, $customParamsQueryArray));
        } else { $setParams = ''; }

        $query = $this->dbh->prepare("UPDATE {$this->config->table_users} SET email = ?, password = ?, isactive = ? {$setParams} WHERE id = ?");

        $bindParams = array_values(array_merge(array($email, $password, $isactive), $params, array($uid)));

        if (!$query->execute($bindParams)) {
            $query = $this->dbh->prepare("DELETE FROM {$this->config->table_users} WHERE id = ?");
            $query->execute(array($uid));
            $return['message'] = $this->lang["system_error"] . " #04";

            return $return;
        }

        $return['error'] = false;
        return $return;
    }

	public function getHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->config->bcrypt_cost]);
    }
	
	public function getUID($email)
    {
        $query = $this->dbh->prepare("SELECT id FROM {$this->config->table_users} WHERE email = ?");
        $query->execute(array($email));

        if(!$row = $query->fetch(\PDO::FETCH_ASSOC)) {
            return false;
        }

        return $row['id'];
    }
	
	public function isLogged() 
	{
        return (isset($_COOKIE[$this->config->cookie_name]) && $this->checkSession($_COOKIE[$this->config->cookie_name]));
    }
	
	public function getSessionHash()
	{
        return $_COOKIE[$this->config->cookie_name];
    }
	
	protected function getBaseUser($uid)
    {
        $query = $this->dbh->prepare("SELECT email, password, isactive FROM {$this->config->table_users} WHERE id = ?");
        $query->execute(array($uid));

        $data = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        $data['uid'] = $uid;

        return $data;
    }
	
    protected function addSession($uid, $remember)
    {
        $ip = $this->getIp();
        $user = $this->getBaseUser($uid);

        if (!$user) {
            return false;
        }

        $data['hash'] = sha1($this->config->site_key . microtime());
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        $this->deleteExistingSessions($uid);

        if ($remember == true) {
            $data['expire'] = date("Y-m-d H:i:s", strtotime($this->config->cookie_remember));
            $data['expiretime'] = strtotime($data['expire']);
        } else {
            $data['expire'] = date("Y-m-d H:i:s", strtotime($this->config->cookie_forget));
            $data['expiretime'] = 0;
        }

        $data['cookie_crc'] = sha1($data['hash'] . $this->config->site_key);

        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_sessions} (uid, hash, expiredate, ip, agent, cookie_crc) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$query->execute(array($uid, $data['hash'], $data['expire'], $ip, $agent, $data['cookie_crc']))) {
            return false;
        }

        $data['expire'] = strtotime($data['expire']);

        return $data;
    }

	protected function deleteSession($hash)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE hash = ?");
        $query->execute(array($hash));

        return $query->rowCount() == 1;
    }
	
	public function checkSession($hash)
    {
        $ip = $this->getIp();
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];
            return false;
        }

        if (strlen($hash) != 40) {
            return false;
        }

        $query = $this->dbh->prepare("SELECT id, uid, expiredate, ip, agent, cookie_crc FROM {$this->config->table_sessions} WHERE hash = ?");
        $query->execute(array($hash));

		if (!$row = $query->fetch(\PDO::FETCH_ASSOC)) {
			return false;
		}

        $sid = $row['id'];
        $uid = $row['uid'];
        $expiredate = strtotime($row['expiredate']);
        $currentdate = strtotime(date("Y-m-d H:i:s"));
        $db_ip = $row['ip'];
        $db_agent = $row['agent'];
        $db_cookie = $row['cookie_crc'];

        if ($currentdate > $expiredate) {
            $this->deleteExistingSessions($uid);

            return false;
        }

        if ($ip != $db_ip) {
            return false;
        }

        if ($db_cookie == sha1($hash . $this->config->site_key)) {
            return true;
        }

        return false;
    }
	
	public function getSessionUID($hash)
    {
        $query = $this->dbh->prepare("SELECT uid FROM {$this->config->table_sessions} WHERE hash = ?");
        $query->execute(array($hash));
		
		if (!$row = $query->fetch(\PDO::FETCH_ASSOC)) {
			return false;
		}

		return $row['uid'];
    }
		
    protected function deleteExistingSessions($uid)
    {
        $query = $this->dbh->prepare("DELETE FROM {$this->config->table_sessions} WHERE uid = ?");
        $query->execute(array($uid));

        return $query->rowCount() == 1;
    }	

    public function register($email, $password, $repeatpassword, $params = Array(), $captcha = NULL, $sendmail = NULL)
    {
        $return['error'] = true;
        $block_status = $this->isBlocked();

        if ($block_status == "verify") {
            //if ($this->checkCaptcha($captcha) == false) {
                $return['message'] = $this->lang["user_verify_failed"];

                return $return;
            //}
        }

        if ($block_status == "block") {
            $return['message'] = $this->lang["user_blocked"];

            return $return;
        }

        if ($password !== $repeatpassword) {
            $return['message'] = $this->lang["password_nomatch"];

            return $return;
        }

        // Validate email
        $validateEmail = $this->validateEmail($email);

        if ($validateEmail['error'] == 1) {
            $return['message'] = $validateEmail['message'];

            return $return;
        }

        // Validate password
        $validatePassword = $this->validatePassword($password);

        if ($validatePassword['error'] == 1) {
            $return['message'] = $validatePassword['message'];

            return $return;
        }

        /*$zxcvbn = new Zxcvbn();

        if ($zxcvbn->passwordStrength($password)['score'] < intval($this->config->password_min_score)) {
            $return['message'] = $this->lang['password_weak'];

            return $return;
        }*/

        if ($this->isEmailTaken($email)) {
            $this->addAttempt();
            $return['message'] = $this->lang["email_taken"];

            return $return;
        }

        $addUser = $this->addUser($email, $password, $params, $sendmail);

        if ($addUser['error'] != 0) {
            $return['message'] = $addUser['message'];

            return $return;
        }

        $return['error'] = false;
        $return['message'] = ($sendmail == true ? $this->lang["register_success"] : $this->lang['register_success_emailmessage_suppressed'] );

        return $return;
    }
   
    public function logout($hash)
    {
        if (strlen($hash) != 40) {
            return false;
        }

        return $this->deleteSession($hash);
    }
    
	protected function validatePassword($password) 
	{
        $return['error'] = true;

        if (strlen($password) < (int)$this->config->verify_password_min_length ) {
            $return['message'] = $this->lang["password_short"];

            return $return;
        }

        $return['error'] = false;

        return $return;
    }

	protected function validateEmail($email) 
	{
        $return['error'] = true;

        if (strlen($email) < (int)$this->config->verify_email_min_length ) {
            $return['message'] = $this->lang["email_short"];

            return $return;
        } elseif (strlen($email) > (int)$this->config->verify_email_max_length ) {
            $return['message'] = $this->lang["email_long"];

            return $return;
        }/* elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return['message'] = $this->lang["email_invalid"];

            return $return;
        }

        if ( (int)$this->config->verify_email_use_banlist ) {
            $bannedEmails = json_decode(file_get_contents(__DIR__ . "/files/domains.json"));

            if (in_array(strtolower(explode('@', $email)[1]), $bannedEmails)) {
                $return['message'] = $this->lang["email_banned"];

                return $return;
            }
        }*/

        $return['error'] = false;

        return $return;
    }
   
    public function isBlocked()    
	{
        $ip = $this->getIp();
        $this->deleteAttempts($ip, false);
        $query = $this->dbh->prepare("SELECT count(*) FROM {$this->config->table_attempts} WHERE ip = ?");
        $query->execute(array($ip));
        $attempts = $query->fetchColumn();

        if ($attempts < intval($this->config->attempts_before_verify)) {
            return "allow";
        }

        if ($attempts < intval($this->config->attempts_before_ban)) {
            //return "verify";
			return "block";
        }

        return "block";
    }

    protected function addAttempt()
    {
        $ip = $this->getIp();
        $attempt_expiredate = date("Y-m-d H:i:s", strtotime($this->config->attack_mitigation_time));
        $query = $this->dbh->prepare("INSERT INTO {$this->config->table_attempts} (ip, expiredate) VALUES (?, ?)");

        return $query->execute(array($ip, $attempt_expiredate));
    }

	protected function deleteAttempts($ip, $all = false)
    {
        if ($all==true) {
            $query = $this->dbh->prepare("DELETE FROM {$this->config->table_attempts} WHERE ip = ?");

            return $query->execute(array($ip));
        }

        $query = $this->dbh->prepare("SELECT id, expiredate FROM {$this->config->table_attempts} WHERE ip = ?");
        $query->execute(array($ip));

        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $expiredate = strtotime($row['expiredate']);
            $currentdate = strtotime(date("Y-m-d H:i:s"));
            if ($currentdate > $expiredate) {
                $queryDel = $this->dbh->prepare("DELETE FROM {$this->config->table_attempts} WHERE id = ?");
                $queryDel->execute(array($row['id']));
            }
        }
    }
	
    protected function getIp()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
           return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
           return $_SERVER['REMOTE_ADDR'];
        }
    }	
	
}
