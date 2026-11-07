<?php
/**
 * Fungsi bantu: session, autentikasi, flash message, CSRF, label status, upload file.
 */

const UPLOAD_DIR   = __DIR__ . '/../uploads/';
const MAKS_UKURAN  = 2 * 1024 * 1024;               // 2 MB
const EKSTENSI_OK  = ['jpg', 'jpeg', 'png'];
const MIME_OK      = ['image/jpeg', 'image/png'];

/** Daftar kategori (array) dipakai di form dan filter. */
function daftar_kategori(): array
{
    return ['Pemrograman', 'Basis Data', 'Jaringan', 'Sistem Informasi', 'Desain', 'Bisnis', 'Umum'];
}

/** Escape output HTML. */
function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

/** Label status ketersediaan berdasarkan stok (percabangan if-else). */
function label_status(int $stok): array
{
    if ($stok > 0) {
        return ['teks' => 'Tersedia', 'kelas' => 'status-ada'];
    }
    return ['teks' => 'Dipinjam', 'kelas' => 'status-habis'];
}

/* ---------- Autentikasi ---------- */

function wajib_login(): void
{
    if (empty($_SESSION['user_id'])) {
        flash('warning', 'Silakan login terlebih dahulu.');
        header('Location: index.php');
        exit;
    }
}

function wajib_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: dashboard.php');
        exit;
    }
}

/* ---------- Flash message ---------- */

function flash(string $tipe, string $pesan): void
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function ambil_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/* ---------- CSRF ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function cek_csrf(): void
{
    $kirim = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $kirim)) {
        http_response_code(419);
        exit('Token keamanan tidak valid. Muat ulang halaman lalu coba lagi.');
    }
}

/* ---------- Upload sampul ---------- */

/**
 * Validasi dan simpan file sampul.
 * @return array [bool sukses, string namaFileAtauPesanError]
 */
function upload_sampul(array $file): array
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return [false, 'Pilih file sampul buku terlebih dahulu.'];
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        return [false, 'Ukuran file melebihi batas 2 MB.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'Upload gagal (kode error ' . $file['error'] . ').'];
    }
    if ($file['size'] > MAKS_UKURAN) {
        return [false, 'Ukuran file melebihi batas 2 MB.'];
    }

    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ekstensi, EKSTENSI_OK, true)) {
        return [false, 'Format file harus .jpg atau .png.'];
    }

    // Cek tipe MIME asli dari isi file, bukan dari nama/ekstensi saja.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (!in_array($finfo->file($file['tmp_name']), MIME_OK, true) || @getimagesize($file['tmp_name']) === false) {
        return [false, 'Isi file bukan gambar JPG/PNG yang valid.'];
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    $namaBaru = 'sampul_' . bin2hex(random_bytes(8)) . '.' . ($ekstensi === 'jpeg' ? 'jpg' : $ekstensi);
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $namaBaru)) {
        return [false, 'Gagal menyimpan file ke folder uploads.'];
    }
    return [true, $namaBaru];
}

/** Hapus berkas sampul fisik dari folder uploads (aman terhadap path traversal). */
function hapus_sampul(?string $nama): void
{
    if (!$nama) {
        return;
    }
    $path = UPLOAD_DIR . basename($nama);
    if (is_file($path)) {
        unlink($path);
    }
}

/* ---------- Validasi input buku ---------- */

/**
 * Validasi field teks buku. Mengembalikan [dataBersih, daftarError].
 */
function validasi_buku(array $in): array
{
    $data = [
        'judul'        => trim($in['judul'] ?? ''),
        'kategori'     => trim($in['kategori'] ?? ''),
        'penulis'      => trim($in['penulis'] ?? ''),
        'tahun_terbit' => trim($in['tahun_terbit'] ?? ''),
        'stok'         => trim($in['stok'] ?? ''),
    ];
    $error = [];
    $tahunMaks = (int) date('Y');

    if ($data['judul'] === '' || mb_strlen($data['judul']) > 150) {
        $error[] = 'Judul wajib diisi (maksimal 150 karakter).';
    }
    if (!in_array($data['kategori'], daftar_kategori(), true)) {
        $error[] = 'Pilih kategori dari daftar.';
    }
    if ($data['penulis'] === '' || mb_strlen($data['penulis']) > 100) {
        $error[] = 'Penulis wajib diisi (maksimal 100 karakter).';
    }
    if (!ctype_digit($data['tahun_terbit']) || (int) $data['tahun_terbit'] < 1900 || (int) $data['tahun_terbit'] > $tahunMaks) {
        $error[] = "Tahun terbit harus berupa angka antara 1900 dan $tahunMaks.";
    }
    if (!ctype_digit($data['stok'])) {
        $error[] = 'Stok harus berupa angka 0 atau lebih.';
    }
    return [$data, $error];
}
