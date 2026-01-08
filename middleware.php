// Section: Session Initialization
<?php
session_start();

// Section: Authentication Function
function auth() {
  if (!isset($_SESSION['user'])) {
    header('Location: auth/login.php');
    exit;
  }
}

// Section: Admin Access Control Function
function adminOnly() {
  auth();
  if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
  }
}