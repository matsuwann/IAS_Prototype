<?php

require_once 'config.php';

define('ENCRYPTION_KEY', '7c6f52a27e539549c57a21bcb9ede050814f8bda137ab51ca53ad485140d9ace');
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; 
    $confirm_password = $_POST['confirm_password'];
    $role = 'student';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">All fields are required.</div>';
    } elseif (strlen($password) < 8) {
        $message = '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">Password must be at least 8 characters long.</div>';
    } elseif ($password !== $confirm_password) {
        $message = '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">Passwords do not match.</div>';
    } else {
        $sql_user = "SELECT id FROM users WHERE username = :username";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt_user->execute();

        if ($stmt_user->rowCount() > 0) {
            $message = '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">This username is already taken.</div>';
        } else {

            $sql_email = "SELECT email FROM users";
            $stmt_email = $pdo->prepare($sql_email);
            $stmt_email->execute();
            $all_users = $stmt_email->fetchAll(PDO::FETCH_ASSOC);
            $email_exists = false;
            foreach ($all_users as $user) {
                $decrypted_email = secure_decrypt($user['email'], ENCRYPTION_KEY);
                if ($decrypted_email !== false && $decrypted_email === $email) {
                    $email_exists = true;
                    break;
                }
            }

            if ($email_exists) {
                $message = '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">This email is already registered.</div>';
            } else {

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $encrypted_email = secure_encrypt($email, ENCRYPTION_KEY);
                try {
                    $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->bindParam(':email', $encrypted_email, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        $message = '<div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Registration successful! You can now log in.</div>';
                        log_activity($pdo, null, $username, "User registered successfully");
                    }
                } catch (PDOException $e) {
                     $message = '<div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">An error occurred during registration.</div>';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IBITS Learning Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-12">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-lg">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800">Create a Student Account</h1>
            <p class="mt-2 text-gray-600">Join the IBITS Learning Hub</p>
        </div>

        <?php echo $message; ?>

        <form id="registerForm" action="register.php" method="post" class="space-y-4" novalidate>
            <div>
                <label for="username" class="text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" required 
                       class="mt-1 block w-full px-4 py-2 text-gray-900 bg-gray-50 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                       class="mt-1 block w-full px-4 py-2 text-gray-900 bg-gray-50 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full px-4 py-2 pr-10 text-gray-900 bg-gray-50 border border-gray-300 rounded-md">
                     <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                        <svg id="eye-icon-password" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>
                        <svg id="eye-slash-icon-password" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill hidden" viewBox="0 0 16 16"><path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/><path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/></svg>
                    </button>
                </div>
                <p id="password-length-error" class="text-xs text-red-600 mt-1 hidden">Password must be at least 8 characters long.</p>
            </div>
            <div>
                <label for="confirm_password" class="text-sm font-medium text-gray-700">Confirm Password</label>
                 <div class="relative">
                    <input type="password" name="confirm_password" id="confirm_password" required
                           class="mt-1 block w-full px-4 py-2 pr-10 text-gray-900 bg-gray-50 border border-gray-300 rounded-md">
                    <button type="button" onclick="togglePasswordVisibility('confirm_password')" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                        <svg id="eye-icon-confirm_password" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>
                        <svg id="eye-slash-icon-confirm_password" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill hidden" viewBox="0 0 16 16"><path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/><path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/></svg>
                    </button>
                </div>
                <p id="password-match-error" class="text-xs text-red-600 mt-1 hidden">Passwords do not match.</p>
            </div>
            <div>
                <button type="submit" id="submitBtn" class="w-full px-4 py-2 font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:bg-gray-400">
                    Register
                </button>
            </div>
        </form>
        <p class="text-xs text-center text-gray-500">
            Already have an account? <a href="index.php" class="text-blue-600 hover:underline">Log In</a>
        </p>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById('eye-icon-' + fieldId);
            const eyeSlashIcon = document.getElementById('eye-slash-icon-' + fieldId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }

        const form = document.getElementById('registerForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordLengthError = document.getElementById('password-length-error');
        const passwordMatchError = document.getElementById('password-match-error');
        const submitBtn = document.getElementById('submitBtn');

        function validatePasswords() {
            let isValid = true;

            if (password.value.length > 0 && password.value.length < 8) {
                passwordLengthError.classList.remove('hidden');
                password.classList.add('border-red-500');
                isValid = false;
            } else {
                passwordLengthError.classList.add('hidden');
                password.classList.remove('border-red-500');
            }

            if (confirmPassword.value.length > 0 && password.value !== confirmPassword.value) {
                passwordMatchError.classList.remove('hidden');
                confirmPassword.classList.add('border-red-500');
                isValid = false;
            } else {
                passwordMatchError.classList.add('hidden');
                confirmPassword.classList.remove('border-red-500');
            }
            
            submitBtn.disabled = !isValid;
        }

        password.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);

        form.addEventListener('submit', function(event) {
            validatePasswords();
            if (submitBtn.disabled) {
                event.preventDefault(); 
            }
        });
    </script>
</body>
</html>
