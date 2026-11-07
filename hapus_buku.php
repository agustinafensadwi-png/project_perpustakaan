<?php
require_once 'includes/init.php';
wajib_login();
wajib_post();
cek_csrf();

$id = (int) ($_POST['id'] ?? 0);

$stmt = $koneksi->prepare('SELECT judul, sampul FROM buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();

if ($buku) {
    $stmt = $koneksi->prepare('DELETE FROM buku WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    hapus_sampul($buku['sampul']);          // unlink berkas gambar
    flash('success', 'Buku “' . $buku['judul'] . '” beserta file sampulnya telah dihapus.');
} else {
    flash('warning', 'Data buku tidak ditemukan.');
}
header('Location: dashboard.php');
exit;
