<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$uploadDir = __DIR__ . '/../assets/images/products/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  csrf_check();
  $id = (int)($_POST['id'] ?? 0);

  $stmt = $pdo->prepare('SELECT image FROM products WHERE id = ?');
  $stmt->execute([$id]);
  $image = $stmt->fetchColumn();

  $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);

  // delete the photo file too, unless another product uses the same file
  if ($image) {
    $c = $pdo->prepare('SELECT COUNT(*) FROM products WHERE image = ?');
    $c->execute([$image]);
    if ((int)$c->fetchColumn() === 0) {
      $path = $uploadDir . basename($image);
      if (is_file($path)) {
        unlink($path);
      }
    }
  }

  $_SESSION['flash'] = 'Product deleted.';
  header('Location: products.php');
  exit;
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$products = $pdo->query('SELECT id, name, category, image FROM products ORDER BY category, name')
                ->fetchAll(PDO::FETCH_ASSOC);

$adminTitle = 'Products';
include 'admin-header.php';
?>

<div class="admin-top">
  <h1>Products (<?php echo count($products); ?>)</h1>
  <a href="product-form.php" class="btn-small">+ Add Product</a>
</div>

<?php if ($flash): ?>
  <div class="alert alert-success"><?php echo htmlspecialchars($flash); ?></div>
<?php endif; ?>

<div class="table-wrap">
  <table class="admin-table">
    <thead>
      <tr><th>Photo</th><th>Name</th><th>Category</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td>
            <?php if (!empty($p['image'])): ?>
              <img class="thumb" src="../assets/images/products/<?php echo htmlspecialchars($p['image']); ?>" alt="">
            <?php else: ?>
              <div class="thumb"></div>
            <?php endif; ?>
          </td>
          <td><?php echo htmlspecialchars($p['name']); ?></td>
          <td><?php echo htmlspecialchars($p['category']); ?></td>
          <td class="actions">
            <a href="product-form.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
            <form method="POST" onsubmit="return confirm('Delete this product? This cannot be undone.');">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
              <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
              <button type="submit" class="link-btn">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include 'admin-footer.php'; ?>