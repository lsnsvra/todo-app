<?php
include '../middleware.php';
auth();
include '../config/db.php';
$task=null;
if(isset($_GET['id'])){
$stmt=$conn->prepare("SELECT * FROM tasks WHERE id=? AND user_id=?");
$stmt->execute([$_GET['id'],$_SESSION['user']['id']]);
$task=$stmt->fetch();}

$isAdmin = $_SESSION['user']['role'] === 'admin';
$users = $isAdmin ? $conn->query("SELECT id, name FROM users WHERE role != 'admin'")->fetchAll() : [];
$isEdit = isset($task);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?= $isEdit ? 'Edit' : 'Add' ?> Task - Todo App</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-bg">
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg auth-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-<?= $isEdit ? 'pencil-square' : 'plus-circle-fill' ?> display-4 text-primary mb-3"></i>
                        <h2 class="fw-bold text-primary"><?= $isEdit ? 'Edit' : 'Add' ?> Task</h2>
                        <p class="text-muted"><?= $isEdit ? 'Update your task details' : 'Create a new task to manage' ?></p>
                    </div>

                    <form method="POST" action="action.php" onsubmit="return validateTask()" class="auth-form">
                        <input type="hidden" name="id" value="<?= $task['id']??'' ?>">

                        <?php if($isAdmin): ?>
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>Assign to User
                            </label>
                            <select name="user_id" id="user_id" class="form-select form-select-lg" required>
                                <option value="">Select User</option>
                                <?php foreach($users as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= isset($_GET['user_id']) && $_GET['user_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="user_id" value="<?= $_SESSION['user']['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">
                                <i class="bi bi-card-text me-1"></i>Task Title
                            </label>
                            <input type="text" id="title" name="title" class="form-control form-control-lg" value="<?= $task['title']??'' ?>" placeholder="Enter task title" required>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                <i class="bi bi-textarea me-1"></i>Description
                            </label>
                            <textarea id="description" name="description" class="form-control form-control-lg" rows="4" placeholder="Enter task description (optional)"><?= $task['description']??'' ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-<?= $isEdit ? 'check-circle' : 'plus-circle' ?> me-2"></i><?= $isEdit ? 'Update' : 'Create' ?> Task
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="<?= $isAdmin ? '../admin/dashboard.php' : '../index.php' ?>" class="text-primary fw-semibold text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
