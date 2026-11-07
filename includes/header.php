<?php /** Kerangka atas halaman setelah login. Variabel: $judul_halaman */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul_halaman ?? 'Dashboard') ?> | Mini Digital Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar app-nav">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Mini Digital Library</a>
    <div class="d-flex align-items-center gap-3">
      <span class="nav-user d-none d-sm-inline"><?= e($_SESSION['nama'] ?? '') ?></span>
      <a href="logout.php" class="btn btn-sm btn-outline-light">Keluar</a>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if ($f = ambil_flash()): ?>
  <div class="alert alert-<?= e($f['tipe']) ?> alert-dismissible fade show" role="alert">
    <?= e($f['pesan']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
  </div>
<?php endif; ?>
