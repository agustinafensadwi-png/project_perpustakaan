<?php
require_once 'includes/init.php';
wajib_login();
wajib_post();
cek_csrf();

$id = (int) ($_POST['id'] ?? 0);
$stmt = $koneksi->prepare('SELECT sampul FROM buku WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$lama = $stmt->get_result()->fetch_assoc();
if (!$lama) {
    flash('warning', 'Data buku tidak ditemukan.');
    header('Location: dashboard.php');
    exit;
}

[$data, $errors] = validasi_buku($_POST);

// Sampul bersifat opsional saat edit.
$sampulBaru = null;
$adaFile = isset($_FILES['sampul']) && $_FILES['sampul']['error'] !== UPLOAD_ERR_NO_FILE;
if ($adaFile) {
    [$ok, $hasil] = upload_sampul($_FILES['sampul']);
    if ($ok) {
        $sampulBaru = $hasil;
    } else {
        $errors[] = $hasil;
    }
}

if ($errors) {
    hapus_sampul($sampulBaru);
    $_SESSION['errors']   = $errors;
    $_SESSION['old_buku'] = $data;
    header('Location: edit_buku.php?id=' . $id);
    exit;
}

$sampulAkhir = $sampulBaru ?? $lama['sampul'];
$tahun = (int) $data['tahun_terbit'];
$stok  = (int) $data['stok'];

$stmt = $koneksi->prepare('UPDATE buku SET judul = ?, kategori = ?, penulis = ?, tahun_terbit = ?, stok = ?, sampul = ? WHERE id = ?');
$stmt->bind_param('sssiisi', $data['judul'], $data['kategori'], $data['penulis'], $tahun, $stok, $sampulAkhir, $id);
$stmt->execute();

// Hapus file lama hanya setelah database berhasil diperbarui.
if ($sampulBaru !== null) {
    hapus_sampul($lama['sampul']);
}

flash('success', 'Data buku berhasil diperbarui.');
header('Location: dashboard.php');
exit;
