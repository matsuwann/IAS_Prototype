<?php
require_once 'config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["tfa_verified"]) || $_SESSION["tfa_verified"] !== true) {
    header("location: index.php");
    exit;
}
log_activity($pdo, $_SESSION['user_id'], $_SESSION['username'], "Viewed dashboard");
$role = $_SESSION["role"];
$username = htmlspecialchars($_SESSION["username"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IBITS Learning Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <h1 class="text-xl font-bold text-gray-800">IBITS Learning Hub</h1>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-4">Welcome, <strong><?php echo $username; ?></strong> (<?php echo ucfirst($role); ?>)</span>
                    <a href="logout.php" class="px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Your Dashboard</h2>
                <?php if ($role == 'teacher'): ?>
                    <div id="teacher-content">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Teacher Panel</h3>
                        <p class="text-gray-600 mb-4">You have access to teacher-specific content.</p>
                    </div>
                <?php elseif ($role == 'student'): ?>
                    <div id="student-content">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Student Panel</h3>
                        <p class="text-gray-600 mb-4">Welcome to your learning dashboard.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-8 bg-white rounded-lg shadow-lg p-8">
                 <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Recent Activity</h3>
                 <div class="overflow-x-auto">
                     <table class="min-w-full text-sm text-left text-gray-500">
                         <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                             <tr>
                                 <th scope="col" class="px-6 py-3">Action</th>
                                 <th scope="col" class="px-6 py-3">IP Address</th>
                                 <th scope="col" class="px-6 py-3">Timestamp</th>
                             </tr>
                         </thead>
                         <tbody>
                             <?php
                                $sql = "SELECT action, ip_address, timestamp FROM user_activity WHERE user_id = :id OR username_attempt = :username ORDER BY timestamp DESC LIMIT 10";
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
                                $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
                                $stmt->execute();
                                $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($logs as $log) {
                                    echo '<tr class="bg-white border-b"><td class="px-6 py-4">' . htmlspecialchars($log['action']) . '</td><td class="px-6 py-4">' . htmlspecialchars($log['ip_address']) . '</td><td class="px-6 py-4">' . htmlspecialchars($log['timestamp']) . '</td></tr>';
                                }
                             ?>
                         </tbody>
                     </table>
                 </div>
            </div>
        </div>
    </main>
</body>
</html>