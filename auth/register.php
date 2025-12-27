<?php
include '../config/db.php';

if (isset($_POST['register'])) {
  $stmt = $conn->prepare(
    "INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)"
  );
  $stmt->execute([
    $_POST['name'],
    $_POST['email'],
    $_POST['password'], // TANPA HASH
    'user'
  ]);

  header('Location: login.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Register - Todo App</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="auth-bg">
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg auth-card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill display-4 text-primary mb-3"></i>
                        <h2 class="fw-bold text-primary">Create Account</h2>
                        <p class="text-muted">Join us and start managing your tasks</p>
                    </div>

                    <form method="POST" class="auth-form" autocomplete="off">
                        <input type="hidden" autocomplete="off">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>Full Name
                            </label>
                            <input type="text" id="name" name="name" class="form-control form-control-lg" placeholder="Enter your full name" autocomplete="off" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="Enter your email" autocomplete="off" required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1"></i>Password
                            </label>
                            <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Create a password" autocomplete="new-password" required>
                        </div>

                        <button type="submit" name="register" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-0 text-muted">Already have an account?
                            <a href="login.php" class="text-primary fw-semibold text-decoration-none">Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
