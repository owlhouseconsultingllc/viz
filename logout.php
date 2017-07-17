<?php

require("config.php");

$logout = $auth->logout($auth->getSessionHash());
header('Location: index.php');

?>