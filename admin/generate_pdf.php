<?php
include '../middleware.php';
adminOnly();
include '../config/db.php';
require '../lib/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Fetch data
$users = $conn->query("SELECT id, name, email, role FROM users")->fetchAll();
$tasks = $conn->query("SELECT tasks.id, tasks.title, tasks.status, tasks.user_id, users.name FROM tasks JOIN users ON tasks.user_id = users.id ORDER BY users.name, tasks.created_at DESC")->fetchAll();

// Group tasks by user
$tasksByUser = [];
foreach ($tasks as $task) {
    $tasksByUser[$task['user_id']][] = $task;
}

// Calculate statistics
$totalUsers = count($users);
$totalTasks = count($tasks);
$completedTasks = count(array_filter($tasks, function($task) {
    return $task['status'] === 'completed';
}));
$pendingTasks = $totalTasks - $completedTasks;

// Section: Generate HTML Content for PDF
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #007bff; text-align: center; }
        .summary { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .summary div { text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .user-section { margin-bottom: 30px; }
        .user-section h3 { color: #28a745; }
        .task-list { list-style-type: none; padding: 0; }
        .task-list li { margin-bottom: 5px; }
        .completed { text-decoration: line-through; color: #6c757d; }
    </style>
</head>
<body>
    <h1>Admin Dashboard Report</h1>
    <div class="summary">
        <div><strong>Total Users:</strong> ' . $totalUsers . '</div>
        <div><strong>Total Tasks:</strong> ' . $totalTasks . '</div>
        <div><strong>Completed Tasks:</strong> ' . $completedTasks . '</div>
        <div><strong>Pending Tasks:</strong> ' . $pendingTasks . '</div>
    </div>

    <h2>Users</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>';
foreach ($users as $user) {
    $html .= '<tr>
                <td>' . htmlspecialchars($user['name']) . '</td>
                <td>' . htmlspecialchars($user['email']) . '</td>
                <td>' . htmlspecialchars($user['role']) . '</td>
              </tr>';
}
$html .= '</tbody>
    </table>

    <h2>Tasks by User</h2>';
foreach ($users as $user) {
    $userTasks = $tasksByUser[$user['id']] ?? [];
    $userCompleted = count(array_filter($userTasks, function($t) { return $t['status'] === 'completed'; }));
    $userTotal = count($userTasks);
    $html .= '<div class="user-section">
                <h3>' . htmlspecialchars($user['name']) . ' (' . htmlspecialchars($user['email']) . ')</h3>
                <p>Tasks: ' . $userCompleted . '/' . $userTotal . ' completed</p>
                <ul class="task-list">';
    foreach ($userTasks as $task) {
        $completedClass = $task['status'] === 'completed' ? 'completed' : '';
        $html .= '<li class="' . $completedClass . '">' . htmlspecialchars($task['title']) . ' - ' . ($task['status'] === 'completed' ? 'Completed' : 'Pending') . '</li>';
    }
    $html .= '</ul></div>';
}
$html .= '
</body>
</html>';

// Create PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('admin_report.pdf', array('Attachment' => 0));
?>