<?php
require_once 'includes/init.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
$flash    = ambil_flash();
$username = $_SESSION['old_username'] ?? ($_COOKIE['remember_username'] ?? '');
$ingat    = isset($_COOKIE['remember_username']);
unset($_SESSION['old_username']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk | Mini Digital Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body class="login-body">
  <div class="login-wrap">
    <aside class="login-shelf" aria-hidden="true">
      <div class="shelf-title">Mini Digital Library</div>
      <div class="spines">
        <span style="--h:62%;--c:#0E7C86"></span><span style="--h:78%;--c:#F2A900"></span>
        <span style="--h:55%;--c:#E4572E"></span><span style="--h:88%;--c:#14213D;outline:1px solid #fff3"></span>
        <span style="--h:70%;--c:#6BA292"></span><span style="--h:92%;--c:#F2A900"></span>
        <span style="--h:60%;--c:#3A86FF"></span><span style="--h:80%;--c:#E4572E"></span>
        <span style="--h:66%;--c:#0E7C86"></span><span style="--h:74%;--c:#F5F7FA"></span>
      </div>
      <p class="shelf-note">Kelola koleksi buku kampus dalam satu tempat.</p>
    </aside>

    <main class="login-form-area">
      <form action="proses_login.php" method="post" class="login-card">
        <h1>Masuk</h1>
        <p class="text-muted">Gunakan akun petugas perpustakaan.</p>

        <?php if ($flash): ?>
          <div class="alert alert-<?= e($flash['tipe']) ?>" role="alert"><?= e($flash['pesan']) ?></div>
        <?php endif; ?>

        <?= csrf_field() ?>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" value="<?= e($username) ?>" required autofocus autocomplete="username">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
            <button class="btn btn-outline-secondary" type="button" id="lihatPassword" aria-label="Tampilkan password">Lihat</button>
          </div>
        </div>
        <div class="form-check mb-4">
          <input class="form-check-input" type="checkbox" id="remember" name="remember" <?= $ingat ? 'checked' : '' ?>>
          <label class="form-check-label" for="remember">Ingat username saya</label>
        </div>
        <button type="submit" class="btn btn-primary w-100">Masuk</button>
      </form>
    </main>
  </div>
  <script>
    const tombol = document.getElementById('lihatPassword');
    const isian  = document.getElementById('password');
    tombol.addEventListener('click', () => {
      const tampil = isian.type === 'password';
      isian.type = tampil ? 'text' : 'password';
      tombol.textContent = tampil ? 'Sembunyikan' : 'Lihat';
    });
  </script>
</body>
</html>
