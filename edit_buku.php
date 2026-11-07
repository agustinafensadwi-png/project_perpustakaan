<?php
require_once 'includes/init.php';
wajib_login();
$judul_halaman = 'Edit buku';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $koneksi->prepare('SELECT * FROM buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();

if (!$buku) {
    flash('warning', 'Data buku tidak ditemukan.');
    header('Location: dashboard.php');
    exit;
}

$errors = $_SESSION['errors'] ?? [];
if (!empty($_SESSION['old_buku'])) {
    $buku = array_merge($buku, $_SESSION['old_buku']);   // pertahankan isian, sampul tetap dari DB
}
unset($_SESSION['old_buku'], $_SESSION['errors']);

$aksi = 'proses_edit.php';
$teks_tombol = 'Simpan perubahan';
$sampul_wajib = false;

require 'includes/header.php';
?>
<h1 class="h3 mb-4">Edit buku</h1>
<?php if ($errors): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<?php
require 'includes/form_buku.php';
require 'includes/footer.php';
