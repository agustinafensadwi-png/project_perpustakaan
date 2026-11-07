<?php
require_once 'includes/init.php';
wajib_login();
$judul_halaman = 'Tambah buku';

$buku   = $_SESSION['old_buku'] ?? [];      // isian sebelumnya jika validasi gagal
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old_buku'], $_SESSION['errors']);

$aksi = 'proses_tambah.php';
$teks_tombol = 'Simpan buku';
$sampul_wajib = true;

require 'includes/header.php';
?>
<h1 class="h3 mb-4">Tambah buku</h1>
<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<?php
require 'includes/form_buku.php';
require 'includes/footer.php';
