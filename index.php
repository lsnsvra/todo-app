// Section: Includes and Authentication
<?php
include 'middleware.php';
auth();
include 'config/db.php';

// Section: Fetch User Tasks
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user']['id']]);
$tasks = $stmt->fetchAll();

// Section: Calculate Progress Statistics
$totalTasks = count($tasks);
$completedTasks = count(array_filter($tasks, function($task) {
    return $task['status'] === 'completed';
}));
$progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
?>
// Section: HTML Head
<!DOCTYPE html>
<html lang="en">

<head>
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-5">
    <div class="row mb-4">
      <div class="col-md-8">
        <h2 class="text-primary fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Task Saya</h2>
      </div>
      <div class="col-md-4 text-end">
        <a href="tasks/form.php" class="btn btn-primary btn-lg me-2"><i class="bi bi-plus-circle me-1"></i>Tambah
          Task</a>
        <a href="auth/logout.php" class="btn btn-outline-danger btn-lg"><i
            class="bi bi-box-arrow-right me-1"></i>Logout</a>
      </div>
    </div>

    <!-- Section: Progress Bar -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="card-title"><i class="bi bi-bar-chart-line me-2"></i>Progress Task</h5>
        <div class="progress" style="height: 25px;">
          <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progressPercentage ?>%;"
            aria-valuenow="<?= $progressPercentage ?>" aria-valuemin="0" aria-valuemax="100">
            <?= $progressPercentage ?>% Selesai
          </div>
        </div>
        <p class="mt-2 mb-0 text-muted">Total Task: <?= $totalTasks ?> | Selesai: <?= $completedTasks ?></p>
      </div>
    </div>

    <!-- Section: Task Cards -->
    <div class="row">
      <?php if (empty($tasks)): ?>
      <div class="col-12">
        <div class="card shadow-sm text-center py-5">
          <div class="card-body">
            <i class="bi bi-clipboard-x display-1 text-muted"></i>
            <h5 class="card-title mt-3">Belum ada task</h5>
            <p class="card-text text-muted">Mulai tambahkan task pertama Anda!</p>
            <a href="tasks/form.php" class="btn btn-primary">Tambah Task</a>
          </div>
        </div>
      </div>
      <?php else: ?>
      <?php foreach($tasks as $t): ?>
      <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm task-card">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title text-truncate"><?= htmlspecialchars($t['title']) ?></h5>
            <p class="card-text flex-grow-1">
              <?php if (!empty($t['description'])): ?>
              <?= htmlspecialchars(substr($t['description'], 0, 100)) ?><?php if (strlen($t['description']) > 100): ?>...<?php endif; ?>
              <?php else: ?>
              <em class="text-muted">Tidak ada deskripsi</em>
              <?php endif; ?>
            </p>
            <div class="mt-auto">
              <span class="badge fs-6 mb-3 <?= $t['status']=='completed'?'bg-success':'bg-warning text-dark' ?>">
                <i class="bi <?= $t['status']=='completed'?'bi-check-circle-fill':'bi-clock' ?> me-1"></i>
                <?= $t['status']=='completed'?'Selesai':'Belum Selesai' ?>
              </span>
              <div class="d-flex gap-2">
                <a href="tasks/form.php?id=<?= $t['id'] ?>" class="btn btn-outline-warning btn-sm flex-fill">
                  <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <?php if ($t['status'] != 'completed'): ?>
                <a href="tasks/action.php?done=<?= $t['id'] ?>" class="btn btn-outline-success btn-sm flex-fill">
                  <i class="bi bi-check-circle me-1"></i>Done
                </a>
                <?php endif; ?>
                <a href="#"
                  onclick="showDeleteModal(<?= $t['id'] ?>, '<?= htmlspecialchars(addslashes($t['title'])) ?>')"
                  class="btn btn-outline-danger btn-sm">
                  <i class="bi bi-trash me-1"></i>Hapus
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  // Section: Delete Confirmation Modal
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteModalLabel">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <i class="bi bi-trash-fill text-danger display-4 mb-3"></i>
          <h6 class="mb-3">Apakah Anda yakin ingin menghapus task ini?</h6>
          <p class="text-muted mb-0">Task: <strong id="deleteTaskTitle"></strong></p>
          <p class="text-danger small mt-2">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Batal
          </button>
          <a id="confirmDeleteBtn" href="#" class="btn btn-danger">
            <i class="bi bi-trash me-1"></i>Hapus Task
          </a>
        </div>
      </div>
    </div>
  </div>

  // Section: Scripts
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/app.js"></script>
</body>

</html>