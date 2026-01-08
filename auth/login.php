// Section: Session Initialization and Database Connection
<?php
session_start();
include '../config/db.php';

// Section: Handle Login Form Submission
if (isset($_POST['login'])) {
  $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
  $stmt->execute([$_POST['email']]);
  $user = $stmt->fetch();

  if ($user && $_POST['password'] === $user['password']) {
    $_SESSION['user'] = $user;

    if ($user['role'] === 'admin') {
      header('Location: ../admin/dashboard.php');
    } else {
      header('Location: ../index.php');
    }
    exit;
  }

  $error = 'Email atau password salah';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Login - Todo App</title>
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
              <i class="bi bi-box-arrow-in-right display-4 text-primary mb-3"></i>
              <h2 class="fw-bold text-primary">Welcome Back</h2>
              <p class="text-muted">Sign in to your account</p>
            </div>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" autocomplete="off">
              <input type="hidden" autocomplete="off">
              <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                  <i class="bi bi-envelope me-1"></i>Email Address
                </label>
                <input type="email" id="email" name="email" class="form-control form-control-lg"
                  placeholder="Enter your email" autocomplete="off" required>
              </div>

              <div class="mb-4">
                <label for="password" class="form-label fw-semibold">
                  <i class="bi bi-lock me-1"></i>Password
                </label>
                <input type="password" id="password" name="password" class="form-control form-control-lg"
                  placeholder="Enter your password" autocomplete="new-password" required>
              </div>

              <button type="submit" name="login" class="btn btn-primary btn-lg w-100 mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
              </button>
            </form>

            <div class="text-center">
              <p class="mb-0 text-muted">Don't have an account?
                <a href="register.php" class="text-primary fw-semibold text-decoration-none">Sign Up</a>
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