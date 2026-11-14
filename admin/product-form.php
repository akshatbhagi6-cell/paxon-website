<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$categories = ['Seasonings', 'Dehydrated Products', 'Other'];
$uploadDir  = __DIR__ . '/../assets/images/products/';
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$product = ['name' => '', 'category' => 'Seasonings', 'description' => '', 'forms' => '', 'packaging' => '', 'image' => ''];
$editing = false;

if ($id > 0) {
  $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
  $stmt->execute([$id]);
  $found = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$found) {
    header('Location: products.php');
    exit;
  }
  $product = $found;
  $editing = true;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();

  $oldImage = $product['image'] ?? '';

  $name        = trim($_POST['name'] ?? '');
  $category    = $_POST['category'] ?? '';
  $description = trim($_POST['description'] ?? '');
  $forms       = trim($_POST['forms'] ?? '');
  $packaging   = trim($_POST['packaging'] ?? '');

  // keep what was typed if there is an error
  $product['name']        = $name;
  $product['category']    = $category;
  $product['description'] = $description;
  $product['forms']       = $forms;
  $product['packaging']   = $packaging;

  if ($name === '' || mb_strlen($name) > 150) {
    $errors[] = 'Enter a product name (up to 150 characters).';
  }
  if (!in_array($category, $categories, true)) {
    $errors[] = 'Choose a valid category.';
  }
  if (mb_strlen($forms) > 255 || mb_strlen($packaging) > 255) {
    $errors[] = 'Forms and packaging must be 255 characters or fewer.';
  }

  $newImage = null;

  if (!$errors && !empty($_FILES['image']['name'])) {
    $f = $_FILES['image'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
      $errors[] = 'The photo could not be uploaded. Try a smaller file.';
    } elseif ($f['size'] > 2 * 1024 * 1024) {
      $errors[] = 'The photo must be under 2 MB.';
    } else {
      $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
      $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

      if (!isset($allowed[$mime])) {
        $errors[] = 'The photo must be a JPG, PNG or WebP image.';
      } else {
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-') ?: 'product';
        $newImage = $slug . '-' . bin2hex(random_bytes(3)) . '.' . $allowed[$mime];

        if (!move_uploaded_file($f['tmp_name'], $uploadDir . $newImage)) {
          $errors[] = 'Could not save the photo on the server.';
          $newImage = null;
        }
      }
    }
  }

  if (!$errors) {
    $image = $oldImage;
    if ($newImage) {
      $image = $newImage;
    } elseif (!empty($_POST['remove_image'])) {
      $image = null;
    }
    $image = $image ?: null;

    if ($editing) {
      $stmt = $pdo->prepare('UPDATE products SET name = ?, category = ?, description = ?, forms = ?, packaging = ?, image = ? WHERE id = ?');
      $stmt->execute([$name, $category, $description, $forms, $packaging, $image, $id]);
    } else {
      $stmt = $pdo->prepare('INSERT INTO products (name, category, description, forms, packaging, image) VALUES (?, ?, ?, ?, ?, ?)');
      $stmt->execute([$name, $category, $description, $forms, $packaging, $image]);
    }

    // remove the old photo file if it was replaced or removed
    if ($oldImage && $oldImage !== $image) {
      $c = $pdo->prepare('SELECT COUNT(*) FROM products WHERE image = ?');
      $c->execute([$oldImage]);
      if ((int)$c->fetchColumn() === 0) {
        $old = $uploadDir . basename($oldImage);
        if (is_file($old)) {
          unlink($old);
        }
      }
    }

    $_SESSION['flash'] = $editing ? 'Product updated.' : 'Product added.';
    header('Location: products.php');
    exit;
  }
}

$adminTitle = $editing ? 'Edit Product' : 'Add Product';
include 'admin-header.php';
?>

<h1><?php echo $editing ? 'Edit Product' : 'Add Product'; ?></h1>

<div class="admin-form">
  <?php foreach ($errors as $e): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($e); ?></div>
  <?php endforeach; ?>

  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="<?php echo (int)$id; ?>">

    <div class="form-grid">
      <div class="form-group">
        <label for="name">Product Name *</label>
        <input type="text" id="name" name="name" maxlength="150" value="<?php echo htmlspecialchars($product['name']); ?>" required>
      </div>

      <div class="form-group">
        <label for="category">Category *</label>
        <select id="category" name="category">
          <?php foreach ($categories as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $product['category'] === $cat ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cat); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group full">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
      </div>

      <div class="form-group">
        <label for="forms">Available Forms</label>
        <input type="text" id="forms" name="forms" maxlength="255" value="<?php echo htmlspecialchars($product['forms'] ?? ''); ?>">
      </div>

      <div class="form-group">
        <label for="packaging">Packaging</label>
        <input type="text" id="packaging" name="packaging" maxlength="255" value="<?php echo htmlspecialchars($product['packaging'] ?? ''); ?>">
      </div>

      <div class="form-group full">
        <label for="image">Photo (JPG, PNG or WebP, under 2 MB)</label>

        <?php if ($editing && !empty($product['image'])): ?>
          <div class="current-photo">
            <img src="../assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="">
            <label class="check">
              <input type="checkbox" name="remove_image" value="1"> Remove this photo
            </label>
          </div>
        <?php endif; ?>

        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
      </div>
    </div>

    <button type="submit" class="btn"><?php echo $editing ? 'Save Changes' : 'Add Product'; ?></button>
    <a href="products.php" class="link-arrow cancel">Cancel</a>
  </form>
</div>

<?php include 'admin-footer.php'; ?>