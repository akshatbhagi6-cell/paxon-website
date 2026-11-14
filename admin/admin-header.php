<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?php echo htmlspecialchars($adminTitle ?? 'Admin'); ?> | Paxon Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <header class="admin-bar">
    <strong>Paxon Admin</strong>
    <nav>
      <a href="index.php">Dashboard</a>
      <a href="inquiries.php">Inquiries</a>
      <a href="products.php">Products</a>
      <a href="../index.php" target="_blank">View Site</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>
  <main class="admin-main">