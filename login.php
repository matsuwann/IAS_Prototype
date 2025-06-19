<?php

//login
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("location: index.php");
    exit;
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = "Username and password are required.";
    header("location: index.php");
    exit;
}

$sql = "SELECT id, password, role, failed_login_attempts, lockout_time FROM users WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":username", $username, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() !== 1) {
    log_activity($pdo, null, $username, "Failed login (Unknown user)");
    $_SESSION['login_error'] = "You entered a wrong username or password.";
    header("location: index.php");
    exit;
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);
$id = $user['id'];
$hashed_password = $user['password'];
$failed_attempts = $user['failed_login_attempts'];
$lockout_time = $user['lockout_time'];

if ($lockout_time) {
    $now = new DateTime();
    $lockout_end = new DateTime($lockout_time);

    if ($now < $lockout_end) {
        log_activity($pdo, $id, $username, "Login attempt on locked account");
        $interval = $now->diff($lockout_end);
        $_SESSION['login_error'] = "Account locked. Please try again in " . $interval->format('%i minutes and %s seconds') . ".";
        header("location: index.php");
        exit;
    } else {
        $failed_attempts = 0;
        $pdo->prepare("UPDATE users SET failed_login_attempts = 0, lockout_time = NULL WHERE id = ?")->execute([$id]);
        log_activity($pdo, $id, $username, "Account lockout expired and was reset");
    }
}

if (password_verify($password, $hashed_password)) {
    
    $pdo->prepare("UPDATE users SET failed_login_attempts = 0, lockout_time = NULL WHERE id = ?")->execute([$id]);

    $_SESSION["loggedin"] = true;
    $_SESSION["tfa_verified"] = false;
    $_SESSION["user_id"] = $id;
    $_SESSION["username"] = $username;
    $_SESSION["role"] = $user['role'];
    $_SESSION["tfa_code"] = rand(100000, 999999);
    $_SESSION["tfa_code_expiry"] = time() + TFA_CODE_VALIDITY;
    $_SESSION['tfa_code_for_demo'] = $_SESSION["tfa_code"];
    log_activity($pdo, $id, $username, "Password correct, 2FA initiated");
    
    header("location: verify.php");
    exit;

} else {

    $failed_attempts++;
    
    if ($failed_attempts >= MAX_LOGIN_ATTEMPTS) {
        $lockout_until = (new DateTime())->add(DateInterval::createFromDateString(LOCKOUT_PERIOD))->format('Y-m-d H:i:s');
        $pdo->prepare("UPDATE users SET failed_login_attempts = ?, lockout_time = ? WHERE id = ?")->execute([$failed_attempts, $lockout_until, $id]);
        log_activity($pdo, $id, $username, "Account locked due to too many failed attempts");
        $_SESSION['login_error'] = "You have made too many failed attempts. Your account is now locked for " . LOCKOUT_PERIOD . ".";
    } else {
        $pdo->prepare("UPDATE users SET failed_login_attempts = ? WHERE id = ?")->execute([$failed_attempts, $id]);
        log_activity($pdo, $id, $username, "Failed login attempt (Incorrect credentials)");
        $_SESSION['login_error'] = "You entered a wrong username or password.";
    }
    
    header("location: index.php");
    exit;
}
?>
