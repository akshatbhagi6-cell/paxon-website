<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$inquiryCount = $pdo->query('SELECT COUNT(*) FROM inquiries')->fetchColumn();
$productCount = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();

$adminTitle = 'Dashboard';
include 'admin-header.php';
?>

<h1>Dashboard</h1>

<div class="stat-grid">
  <div class="stat-card">
    <div class="num"><?php echo (int)$productCount; ?></div>
    <p>Products</p>
  </div>
  <div class="stat-card">
    <div class="num"><?php echo (int)$inquiryCount; ?></div>
    <p>Inquiries received</p>
  </div>
</div>

<?php include 'admin-footer.php'; ?>