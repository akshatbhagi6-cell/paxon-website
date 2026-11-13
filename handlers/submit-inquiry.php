<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../contact.php');
  exit;
}

// Honeypot: real people never see this field, bots fill it
if (!empty($_POST['website'])) {
  header('Location: ../contact.php?status=success');
  exit;
}

$name     = trim($_POST['name'] ?? '');
$company  = trim($_POST['company'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$country  = trim($_POST['country'] ?? '');
$product  = trim($_POST['product'] ?? '');
$quantity = trim($_POST['quantity'] ?? '');
$message  = trim($_POST['message'] ?? '');

if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: ../contact.php?status=error');
  exit;
}

$stmt = $pdo->prepare(
  'INSERT INTO inquiries (name, company, email, phone, country, product, quantity, message)
   VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$name, $company, $email, $phone, $country, $product, $quantity, $message]);

header('Location: ../contact.php?status=success');
exit;