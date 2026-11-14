<?php
session_start();

function require_login() {
  if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
  }
}

function csrf_token() {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}

function csrf_check() {
  $token = $_POST['csrf'] ?? '';
  if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
    http_response_code(403);
    die('Invalid request. Go back and try again.');
  }
}