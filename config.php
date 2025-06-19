<?php

//Contains database configurations

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'ias_prototype');

//database connection
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//constants
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_PERIOD', '10 seconds'); 
define('TFA_CODE_VALIDITY', 300); 


//activity logger
function log_activity($pdo, $userId, $usernameAttempt, $action) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $sql = "INSERT INTO user_activity (user_id, username_attempt, action, ip_address, user_agent) VALUES (:user_id, :username_attempt, :action, :ip_address, :user_agent)";
    
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':username_attempt', $usernameAttempt, PDO::PARAM_STR);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $stmt->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
        
        $stmt->execute();
        unset($stmt);
    }
}

//encrypt decrypt
function secure_encrypt($data, $key) {
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($data, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $ciphertext_raw);
}

function secure_decrypt($data, $key) {
    $c = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $ciphertext_raw = substr($c, $ivlen);
    return openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
}

?>