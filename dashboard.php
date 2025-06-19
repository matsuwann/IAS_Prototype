<?php
//dashboard for IBITS Learning Hub
require_once 'config.php';

//session security check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["tfa_verified"]) || $_SESSION["tfa_verified"] !== true) {
    header("location: index.php");
    exit;
}
//logs dashboard access
log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], "Viewed dashboard");

//fetch user role and username
$role = $_SESSION["role"];
$username = htmlspecialchars($_SESSION["username"]);

?>
<!DOCTYPE html>
<html lang="en">
<head> <!-- Dashboard page -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IBITS Learning Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body class="flex flex-col min-h-screen">

    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <h1 class="text-xl font-bold text-gray-800">IBITS Learning Hub</h1>
                
                <div class="relative">
                    <button id="dropdown-button" class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100">
                        <span class="text-gray-700">Welcome, <strong><?php echo $username; ?></strong></span>
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="dropdown-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                        <div class="px-4 py-2 text-xs text-gray-400">My Account</div>
                        <a href="activity_log.php" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            View Activity Log
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="logout.php" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            Logout
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </nav>


    <main class="flex-grow max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 w-full">
        <div class="px-4 py-6 sm:px-0">
            
            <div class="bg-white/90 backdrop-blur-sm border border-white/20 rounded-lg shadow-lg p-8">
                <div class="mb-6">
                     <h2 class="text-2xl font-bold text-gray-800">Your Dashboard</h2>
                </div>
                
                <?php if ($role == 'teacher'): ?>
                    <div id="teacher-content" class="p-6 border-t">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Teacher Panel</h3>
                        <p class="text-gray-600 mb-4">You have access to teacher-specific tools and content.</p>
                        <button class="px-4 py-2 font-semibold text-white bg-green-600 rounded-md hover:bg-green-700">Manage Content (Future)</button>
                    </div>
                <?php elseif ($role == 'student'): ?>
                    <div id="student-content" class="p-6 border-t">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Student Panel</h3>
                        <p class="text-gray-600 mb-4">Welcome to your learning dashboard.</p>
                        <div class="flex space-x-4">
                            <button class="px-4 py-2 font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700">My Courses (Future)</button>
                            <button class="px-4 py-2 font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">Browse Courses (Future)</button>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>
    
    <footer class="text-center text-sm text-white py-4 bg-black bg-opacity-25">
        &copy; <?php echo date("Y"); ?> IBITS Learning Hub. All Rights Reserved.
    </footer>
<!-- JS for dropdown -->
    <script>
        const dropdownButton = document.getElementById('dropdown-button');
        const dropdownMenu = document.getElementById('dropdown-menu');

        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        window.addEventListener('click', (event) => {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>