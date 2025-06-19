<?php

//activity log
require_once 'config.php';

//session security check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["tfa_verified"]) || $_SESSION["tfa_verified"] !== true) {
    header("location: index.php");
    exit;
}

//logs activity access
log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], "Viewed activity log page");

$username = htmlspecialchars($_SESSION["username"]);
$role = $_SESSION["role"];

// database query to fetch user activity logs
$sql = "SELECT action, ip_address, user_agent, timestamp FROM user_activity WHERE user_id = :id OR username_attempt = :username ORDER BY timestamp DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log - IBITS Learning Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100">

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
                        <div class="px-4 py-2 text-xs text-gray-400">Menu</div>
                        <a href="dashboard.php" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                           Back to Dashboard
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

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Your Account Activity</h2>
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Action</th>
                            <th class="px-6 py-3">IP Address</th>
                            <th class="px-6 py-3">Device/Browser</th>
                            <th class="px-6 py-3">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($logs): ?>
                            <?php foreach ($logs as $log): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($log['action']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($log['user_agent']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($log['timestamp']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center p-4">No activity recorded yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

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
