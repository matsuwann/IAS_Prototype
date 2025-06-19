<?php
//logout.php
require_once 'config.php'; 

if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], 'Logged out');
}//unsets session variables
$_SESSION = array();
//destory session
session_destroy();
//redirec to login
header("location: index.php");
exit;
?>