<?php
require_once 'includes/init.php';
wajib_login();
$judul_halaman = 'Dashboard';

// Filter pencarian (GET)
$q        = trim($_GET['q'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

$sql    = 'SELECT * FROM buku WHERE 1=1';
$tipe   = '';
$param  = [];
if ($q !== '') {
    $sql   .= ' AND (judul LIKE ? OR penulis LIKE ?)';
    $tipe  .= 'ss';
    $param[] = "%$q%";
    $param[] = "%$q%";
}
if ($kategori !== '' && in_array($kategori, daftar_kategori(), true)) {
    $sql   .= ' AND kategori = ?';
    $tipe  .= 's';
    $param[] = $kategori;
}
$sql .= ' ORDER BY id DESC';

$stmt = $koneksi->prepare($sql);
if ($param) {
    $stmt->bind_param($tipe, ...$param);
}
$stmt->execute();
$daftar = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Ringkasan
$ringkas = $koneksi->query('SELECT COUNT(*) AS total, SUM(stok > 0) AS ada, SUM(stok = 0) AS habis FROM buku')->fetch_assoc();

require 'includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <h1 class="h3 m-0">Koleksi buku</h1>
  <a href="tambah_buku.php" class="btn btn-primary">Tambah buku</a>
</div>

<div class="row g-3 mb-4">
  <div class="col-12 col-md-4"><div class="stat total"><div class="angka"><?= (int) $ringkas['total'] ?></div><div class="label">Judul terdaftar</div></div></div>
  <div class="col-6 col-md-4"><div class="stat ada"><div class="angka"><?= (int) $ringkas['ada'] ?></div><div class="label">Tersedia</div></div></div>
  <div class="col-6 col-md-4"><div class="stat habis"><div class="angka"><?= (int) $ringkas['habis'] ?></div><div class="label">Sedang dipinjam</div></div></div>
</div>

<div class="panel p-3">
  <form method="get" class="row g-2 mb-3">
    <div class="col-12 col-md-6">
      <input type="search" name="q" class="form-control" placeholder="Cari judul atau penulis" value="<?= e($q) ?>" aria-label="Cari buku">
    </div>
    <div class="col-8 col-md-4">
      <select name="kategori" class="form-select" aria-label="Filter kategori">
        <option value="">Semua kategori</option>
        <?php foreach (daftar_kategori() as $k): ?>
          <option value="<?= e($k) ?>" <?= $k === $kategori ? 'selected' : '' ?>><?= e($k) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-4 col-md-2 d-grid"><button class="btn btn-outline-primary" type="submit">Cari</button></div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr><th>Sampul</th><th>Judul</th><th>Kategori</th><th>Penulis</th><th>Tahun</th><th>Stok</th><th>Status</th><th class="text-end">Aksi</th></tr>
      </thead>
      <tbody>
      <?php if (!$daftar): ?>
        <tr><td colspan="8" class="text-center py-5 text-muted">
          <?= ($q !== '' || $kategori !== '') ? 'Tidak ada buku yang cocok dengan pencarian.' : 'Belum ada buku. Mulai dengan menambahkan buku pertama.' ?>
        </td></tr>
      <?php endif; ?>
      <?php foreach ($daftar as $b): $s = label_status((int) $b['stok']); ?>
        <tr>
          <td><img class="thumb" src="uploads/<?= e($b['sampul']) ?>" alt="Sampul <?= e($b['judul']) ?>"></td>
          <td class="fw-bold"><?= e($b['judul']) ?></td>
          <td><?= e($b['kategori']) ?></td>
          <td><?= e($b['penulis']) ?></td>
          <td><?= e((string) $b['tahun_terbit']) ?></td>
          <td><?= (int) $b['stok'] ?></td>
          <td><span class="<?= $s['kelas'] ?>"><?= $s['teks'] ?></span></td>
          <td class="text-end text-nowrap">
            <a href="edit_buku.php?id=<?= (int) $b['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                    data-id="<?= (int) $b['id'] ?>" data-judul="<?= e($b['judul']) ?>"
                    data-bs-toggle="modal" data-bs-target="#modalHapus">Hapus</button>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Konfirmasi hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="judulModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" action="hapus_buku.php" method="post">
      <div class="modal-header"><h2 class="modal-title h5" id="judulModal">Hapus buku</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
      <div class="modal-body">
        <p class="mb-0">Buku <strong id="namaBuku"></strong> dan file sampulnya akan dihapus permanen.</p>
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="idBuku">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus buku</button>
      </div>
    </form>
  </div>
</div>
<?php
$skrip_tambahan = <<<'JS'
<script>
document.querySelectorAll('.btn-hapus').forEach(function (tombol) {
  tombol.addEventListener('click', function () {
    document.getElementById('idBuku').value = tombol.dataset.id;
    document.getElementById('namaBuku').textContent = tombol.dataset.judul;
  });
});
</script>
JS;
require 'includes/footer.php';
