<?php

require_once 'config.php'; 

if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], 'Logged out');
}

$_SESSION = array();

session_destroy();

header("location: index.php");
exit;
?>