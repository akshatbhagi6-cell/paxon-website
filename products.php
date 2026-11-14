<?php
require_once 'includes/db.php';

$categories = ['Seasonings', 'Dehydrated Products', 'Other'];
$active = $_GET['category'] ?? $categories[0];
if (!in_array($active, $categories, true)) {
  $active = $categories[0];
}

$stmt = $pdo->prepare('SELECT id, name, description, forms, packaging, image FROM products WHERE category = ? ORDER BY name');
$stmt->execute([$active]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Our Products | Paxon Industries LLP';
include 'includes/header.php';
?>

<main>
  <section class="section">
    <h1 class="section-title">Our Products</h1>

    <div class="tabs">
      <?php foreach ($categories as $cat): ?>
        <a href="products.php?category=<?php echo urlencode($cat); ?>"
           class="tab<?php echo $cat === $active ? ' active' : ''; ?>">
          <?php echo htmlspecialchars($cat); ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
      <p class="center">Products coming soon.</p>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($products as $p): ?>
          <article class="product-card">
            <div class="product-img">
              <?php if (!empty($p['image'])): ?>
                <img src="assets/images/products/<?php echo htmlspecialchars($p['image']); ?>"
                     alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
              <?php else: ?>
                Photo coming soon
              <?php endif; ?>
            </div>
            <h3><?php echo htmlspecialchars($p['name']); ?></h3>
            <p><?php echo htmlspecialchars($p['description'] ?? ''); ?></p>
            <?php if (!empty($p['forms'])): ?>
              <p class="product-meta"><strong>Forms:</strong> <?php echo htmlspecialchars($p['forms']); ?></p>
            <?php endif; ?>
            <?php if (!empty($p['packaging'])): ?>
              <p class="product-meta"><strong>Packaging:</strong> <?php echo htmlspecialchars($p['packaging']); ?></p>
            <?php endif; ?>
            <a href="contact.php?product=<?php echo urlencode($p['name']); ?>" class="btn-small">Enquire Now</a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php include 'includes/footer.php'; ?>