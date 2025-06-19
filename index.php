<?php


require_once 'config.php';

//security checks
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["tfa_verified"]) && $_SESSION["tfa_verified"] === true) {
    header("location: dashboard.php");
    exit;
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && (!isset($_SESSION["tfa_verified"]) || $_SESSION["tfa_verified"] === false)) {
    header("location: verify.php");
    exit;
}

?>
<!-- Login page for IBITS Learning Hub -->
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IBITS Learning Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="flex flex-col min-h-screen">
    <div class="flex-grow flex items-center justify-center">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-lg">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">IBITS Learning Hub</h1>
            </div>

            <?php 
            if(!empty($_SESSION['login_error'])){
                echo '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">';
                echo htmlspecialchars($_SESSION['login_error']);
                echo '</div>';
                unset($_SESSION['login_error']);
            }
            ?>

            <form id="loginForm" action="login.php" method="post" class="space-y-6">
                <div>
                    <label for="username" class="text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" id="username" required 
                           class="mt-1 block w-full px-4 py-2 text-gray-900 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                <div>
                    <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               class="mt-1 block w-full px-4 py-2 pr-10 text-gray-900 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 transition">
                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <button id="loginBtn" type="submit" class="w-full flex justify-center items-center px-4 py-2 font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all disabled:bg-gray-400">
                        <span class="btn-text">Sign In</span>
                        <span class="spinner hidden"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
            </form>
            
            <div class="text-center text-sm text-gray-600 pt-4 border-t">
                <p>
                    Don't have an account? 
                    <a href="register.php" class="font-medium text-blue-600 hover:underline">Register here</a>
                </p>
            </div>
        </div>
    </div>

    <footer class="text-center text-sm text-white py-4 bg-black bg-opacity-25">
        &copy; <?php echo date("Y"); ?> IBITS Learning Hub. All Rights Reserved.
    </footer>

    <script>
        function togglePasswordVisibility(fieldId, button) {
            const passwordField = document.getElementById(fieldId);
            const icon = button.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>