<?php
require_once 'includes/init.php';

// Hapus data session, cookie session, dan cookie Remember Me.
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
setcookie('remember_username', '', time() - 3600, '/');

session_start();                                // sesi baru hanya untuk pesan flash
flash('success', 'Anda sudah keluar dari sistem.');
header('Location: index.php');
exit;
