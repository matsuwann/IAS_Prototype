<?php
//verify checks for 2FA
require_once 'config.php';

//session security check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || (isset($_SESSION["tfa_verified"]) && $_SESSION["tfa_verified"] === true)) {
    header("location: index.php");
    exit;
}

$error_message = '';

//form sub
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submitted_code = trim($_POST['tfa_code']);
    
    if (time() > $_SESSION['tfa_code_expiry']) {
        log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], "2FA failed (Code expired)");
        $error_message = "The verification code has expired. Please log in again.";
        
        session_destroy();
    } elseif ($submitted_code == $_SESSION['tfa_code']) {

        $_SESSION['tfa_verified'] = true;
        unset($_SESSION['tfa_code'], $_SESSION['tfa_code_expiry'], $_SESSION['tfa_code_for_demo']);
        log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], "Login successful (2FA verified)");

        header("location: dashboard.php");
        exit;
    } else {
        log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], "2FA failed (Incorrect code)");
        $error_message = "Invalid verification code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head> <!-- 2FA form -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body class="flex flex-col min-h-screen">


    <div class="flex-grow flex items-center justify-center">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-lg">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">Verify Your Identity</h1>
                <p class="mt-2 text-gray-600">A verification code has been generated. Please enter it below.</p>
            </div>

            <?php 
            if(isset($_SESSION['tfa_code_for_demo'])){

                echo '<div class="p-4 my-4 text-sm text-blue-700 bg-blue-100 rounded-lg" role="alert">';
                echo 'Your verification code is <strong>' . htmlspecialchars($_SESSION['tfa_code_for_demo']) . '</strong>';
                echo '</div>';
            }

            if(!empty($error_message)){
                echo '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">';
                echo htmlspecialchars($error_message);
                echo '</div>';
                if (!isset($_SESSION['loggedin'])) {
                    echo '<a href="index.php" class="block text-center text-blue-600 hover:underline">Return to Login</a>';
                }
            }
            ?>

            <?php if (isset($_SESSION['loggedin'])): ?>
            <form action="verify.php" method="post" class="space-y-6">
                <div>
                    <label for="tfa_code" class="text-sm font-medium text-gray-700">6-Digit Code</label>
                    <input type="text" name="tfa_code" id="tfa_code" required autofocus inputmode="numeric" pattern="[0-9]{6}"
                           class="mt-1 block w-full px-4 py-2 text-gray-900 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 transition text-center text-2xl tracking-widest">
                </div>
                <div>
                    <button type="submit" class="w-full px-4 py-2 font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        Verify
                    </button>
                </div>
            </form>
            <?php endif; ?>
            <p class="text-center mt-4"><a href="logout.php" class="text-sm text-gray-600 hover:underline">Cancel and Log Out</a></p>
        </div>
    </div>


     <footer class="text-center text-sm text-white py-4 bg-black bg-opacity-25">
        &copy; <?php echo date("Y"); ?> IBITS Learning Hub. All Rights Reserved.
    </footer>

</body>
</html>