<?php
require_once 'includes/init.php';
wajib_login();
wajib_post();
cek_csrf();

[$data, $errors] = validasi_buku($_POST);

$namaFile = null;
[$ok, $hasil] = upload_sampul($_FILES['sampul'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
if ($ok) {
    $namaFile = $hasil;
} else {
    $errors[] = $hasil;
}

if ($errors) {
    hapus_sampul($namaFile);                // jangan tinggalkan file yatim
    $_SESSION['errors']   = $errors;
    $_SESSION['old_buku'] = $data;
    header('Location: tambah_buku.php');
    exit;
}

$stmt = $koneksi->prepare('INSERT INTO buku (judul, kategori, penulis, tahun_terbit, stok, sampul) VALUES (?, ?, ?, ?, ?, ?)');
$tahun = (int) $data['tahun_terbit'];
$stok  = (int) $data['stok'];
$stmt->bind_param('sssiis', $data['judul'], $data['kategori'], $data['penulis'], $tahun, $stok, $namaFile);
$stmt->execute();

flash('success', 'Buku “' . $data['judul'] . '” berhasil ditambahkan.');
header('Location: dashboard.php');
exit;
