<?php
require_once 'includes/init.php';
wajib_post();
cek_csrf();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$ingat    = isset($_POST['remember']);

$stmt = $koneksi->prepare('SELECT id, username, password, nama_lengkap FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user && $password === $user['password']) {
    session_regenerate_id(true);               // cegah session fixation
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama']     = $user['nama_lengkap'];

    // Remember Me: simpan username (bukan password) selama 30 hari.
    if ($ingat) {
        setcookie('remember_username', $user['username'], [
            'expires'  => time() + 60 * 60 * 24 * 30,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        setcookie('remember_username', '', time() - 3600, '/');
    }

    header('Location: dashboard.php');
    exit;
}

flash('danger', 'Username atau password salah.');
$_SESSION['old_username'] = $username;
header('Location: index.php');
exit;
