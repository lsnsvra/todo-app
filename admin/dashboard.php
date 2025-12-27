<?php
include '../middleware.php';
adminOnly();
include '../config/db.php';

$users = $conn->query("SELECT id,name,email,role FROM users")->fetchAll();
$tasks = $conn->query("SELECT tasks.id,tasks.title,tasks.status,tasks.user_id,users.name FROM tasks JOIN users ON tasks.user_id=users.id ORDER BY users.name, tasks.created_at DESC")->fetchAll();

// Calculate statistics
$totalUsers = count($users);
$totalTasks = count($tasks);
$completedTasks = count(array_filter($tasks, function($task) {
    return $task['status'] === 'completed';
}));
$pendingTasks = $totalTasks - $completedTasks;

// Group tasks by user
$tasksByUser = [];
foreach ($tasks as $task) {
    $tasksByUser[$task['user_id']][] = $task;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-primary fw-bold"><i class="bi bi-shield-check me-2"></i>Admin Dashboard</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="generate_pdf.php" class="btn btn-primary btn-lg me-2"><i class="bi bi-file-earmark-pdf me-1"></i>Print PDF</a>
            <a href="../auth/logout.php" class="btn btn-outline-danger btn-lg"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center summary-card">
                <div class="card-body">
                    <i class="bi bi-people-fill display-4 text-primary mb-2"></i>
                    <h3 class="card-title text-primary"><?= $totalUsers ?></h3>
                    <p class="card-text">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center summary-card">
                <div class="card-body">
                    <i class="bi bi-list-check display-4 text-success mb-2"></i>
                    <h3 class="card-title text-success"><?= $totalTasks ?></h3>
                    <p class="card-text">Total Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center summary-card">
                <div class="card-body">
                    <i class="bi bi-check-circle-fill display-4 text-info mb-2"></i>
                    <h3 class="card-title text-info"><?= $completedTasks ?></h3>
                    <p class="card-text">Completed Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center summary-card">
                <div class="card-body">
                    <i class="bi bi-clock display-4 text-warning mb-2"></i>
                    <h3 class="card-title text-warning"><?= $pendingTasks ?></h3>
                    <p class="card-text">Pending Tasks</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Data Users</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><i class="bi bi-person me-1"></i>Nama</th>
                            <th><i class="bi bi-envelope me-1"></i>Email</th>
                            <th><i class="bi bi-person-badge me-1"></i>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><span class="badge bg-<?= $u['role']=='admin'?'danger':'secondary' ?>"><?= $u['role'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tasks by User -->
    <div class="row">
        <div class="col-12 mb-4">
            <h4 class="text-success fw-bold"><i class="bi bi-list-task me-2"></i>Tasks per User</h4>
        </div>
        <?php foreach($users as $user): ?>
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><i class="bi bi-person me-2"></i><?= htmlspecialchars($user['name']) ?></h6>
                            <small class="text-white-50"><?= htmlspecialchars($user['email']) ?></small>
                        </div>
                        <a href="../tasks/form.php?user_id=<?= $user['id'] ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Add Task
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        $userTasks = $tasksByUser[$user['id']] ?? [];
                        $userCompleted = count(array_filter($userTasks, function($t) { return $t['status'] === 'completed'; }));
                        $userTotal = count($userTasks);
                        ?>
                        <?php if (empty($userTasks)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-clipboard-x text-muted display-4"></i>
                                <p class="text-muted mt-2">Belum ada task</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Progress: <?= $userCompleted ?>/<?= $userTotal ?> selesai</small>
                                    <small class="text-muted fw-semibold">
                                        <?= $userTotal > 0 ? round(($userCompleted / $userTotal) * 100) : 0 ?>%
                                    </small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: <?= $userTotal > 0 ? ($userCompleted / $userTotal) * 100 : 0 ?>%;"></div>
                                </div>
                            </div>
                            <div class="list-group list-group-flush">
                                <?php foreach($userTasks as $task): ?>
                                    <div class="list-group-item px-0 py-2 border-0">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-<?= $task['status']=='completed'?'check-circle-fill text-success':'circle text-muted' ?> me-2"></i>
                                            <span class="flex-grow-1 <?= $task['status']=='completed'?'text-decoration-line-through text-muted':'' ?>">
                                                <?= htmlspecialchars($task['title']) ?>
                                            </span>
                                            <span class="badge bg-<?= $task['status']=='completed'?'success':'warning text-dark' ?> ms-2">
                                                <?= $task['status']=='completed'?'Selesai':'Pending' ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
