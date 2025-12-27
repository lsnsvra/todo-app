<?php
session_start();

function auth() {
  if (!isset($_SESSION['user'])) {
    header('Location: auth/login.php');
    exit;
  }
}

function adminOnly() {
  auth();
  if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
  }
}
