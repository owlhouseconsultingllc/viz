<?php
 
if (isset($_POST["username"]) && isset($_POST["password"])) {

    // Data base connetion and Auth class
    
    require("config.php");
	
    //Sanitize input data using PHP filter_var().
    $email = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
    $remember = isset($_POST["remember"]);
		
    $output='';
	
	$result = $auth->login($email, $password, $remember);
    // 1 - error
    // 0 - ok
    
	if ($result['error']) {
        $output = json_encode(array("type" => 1, "result" => $result['message']));
    } else {
        
			// Logged in successfully, set cookie, display success message
			//echo "Hello!!!!!";
			setcookie($config->cookie_name, $result['hash'], $result['expire'], $config->cookie_path, $config->cookie_domain, $config->cookie_secure, $config->cookie_http);
			echo '<div class="success">' . $result['message'] . '</div>';
			header('Location: index.php');
    }
    
	include ("login.php");
	echo "<center><div>".$result['message']."</div></center>";
} else {
    // somthing wrong #1
    $output = json_encode(array("type" => 1, "result" => 'Something wrong Try again later #1'));
    die($output);
}