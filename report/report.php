<?php
include '../middleware.php';
adminOnly();
include '../config/db.php';

// Set timezone to GMT+7 (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

// Section: Fetch Users and Tasks Data
$users = $conn->query("SELECT id,name,email,role FROM users")->fetchAll();
$tasks = $conn->query("SELECT tasks.id,tasks.title,tasks.status,tasks.user_id,users.name,tasks.created_at FROM tasks JOIN users ON tasks.user_id=users.id ORDER BY users.name, tasks.created_at DESC")->fetchAll();

// Section: Calculate Statistics
$totalUsers = count($users);
$totalTasks = count($tasks);
$completedTasks = count(array_filter($tasks, function($task) {
    return $task['status'] === 'completed';
}));
$pendingTasks = $totalTasks - $completedTasks;

// Section: Group Tasks by User
$tasksByUser = [];
foreach ($tasks as $task) {
    $tasksByUser[$task['user_id']][] = $task;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Tasks Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
  @media print {
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      color: #000;
    }

    .summary-card,
    .row.mb-4 .col-md-3,
    .row .col-lg-6,
    .col-xl-4,
    .btn,
    .card-header .btn,
    .card-body .btn,
    .card-header,
    .progress,
    .list-group,
    .text-center.py-4,
    .card.shadow-sm.mb-4 {
      display: none !important;
    }

    .d-print-block {
      display: block !important;
    }

    .row .col-12.mb-4 {
      display: none !important;
    }

    h2 {
      font-size: 24px;
      margin-bottom: 20px;
      text-align: center;
      border-bottom: 2px solid #007bff;
      padding-bottom: 10px;
    }

    h4 {
      font-size: 18px;
      margin-top: 30px;
      margin-bottom: 15px;
      color: #007bff;
    }

    h5 {
      font-size: 16px;
      margin-bottom: 10px;
      background-color: #007bff;
      color: white;
      padding: 8px;
      border-radius: 4px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 12px;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f8f9fa;
      font-weight: bold;
    }

    tr:nth-child(even) {
      background-color: #f8f9fa;
    }

    .badge {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 10px;
    }

    .bg-success {
      background-color: #28a745 !important;
      color: white !important;
    }

    .bg-warning {
      background-color: #ffc107 !important;
      color: #212529 !important;
    }

    .bg-danger {
      background-color: #dc3545 !important;
      color: white !important;
    }

    .bg-secondary {
      background-color: #6c757d !important;
      color: white !important;
    }

    @page {
      margin: 1in;
    }
  }
  </style>
</head>

<body class="bg-light">
  <div class="container py-5">
    <!-- Report Header -->
    <div class="text-center mb-4">
      <h1 class="text-primary fw-bold mb-2"><i class="bi bi-file-earmark-text me-2"></i>Tasks Report</h1>
      <p class="text-muted mb-3">Generated on <?= date('F j, Y, g:i a') ?></p>
      <div class="row justify-content-center">
        <div class="col-md-3">
          <div class="card border-primary">
            <div class="card-body text-center">
              <h5 class="card-title text-primary"><?= $totalTasks ?></h5>
              <p class="card-text">Total Tasks</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-success">
            <div class="card-body text-center">
              <h5 class="card-title text-success"><?= $completedTasks ?></h5>
              <p class="card-text">Completed</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-warning">
            <div class="card-body text-center">
              <h5 class="card-title text-warning"><?= $pendingTasks ?></h5>
              <p class="card-text">Pending</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Print-only Tasks Table -->
    <div class="d-print-block" style="display: none;">
      <h3 class="text-success fw-bold mb-4"><i class="bi bi-list-task me-2"></i>All Tasks</h3>
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th><i class="bi bi-card-text me-1"></i>Title</th>
              <th><i class="bi bi-person me-1"></i>User</th>
              <th><i class="bi bi-flag me-1"></i>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($tasks as $task){ ?>
            <tr>
              <td><?= htmlspecialchars($task['title']) ?></td>
              <td><?= htmlspecialchars($task['name']) ?></td>
              <td>
                <?php if($task['status'] == 'completed'){ ?>
                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                <?php } else { ?>
                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Belum Selesai</span>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  window.onload = function() {
    window.print();
  };
  </script>
</body>

</html>