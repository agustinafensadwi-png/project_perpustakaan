<?php
/** Dipanggil di awal setiap halaman: session, koneksi, dan fungsi bantu. */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/fungsi.php';
