<?php
/**
 * Partial form buku (dipakai tambah & edit).
 * Variabel: $aksi, $buku (array), $teks_tombol, $sampul_wajib (bool)
 */
?>
<form action="<?= e($aksi) ?>" method="post" enctype="multipart/form-data" class="panel p-4 form-panel" novalidate>
  <?= csrf_field() ?>
  <?php if (!empty($buku['id'])): ?><input type="hidden" name="id" value="<?= (int) $buku['id'] ?>"><?php endif; ?>
  <input type="hidden" name="MAX_FILE_SIZE" value="<?= MAKS_UKURAN ?>">

  <div class="row g-3">
    <div class="col-12 col-md-8">
      <div class="mb-3">
        <label for="judul" class="form-label">Judul buku</label>
        <input type="text" class="form-control" id="judul" name="judul" maxlength="150" required value="<?= e($buku['judul'] ?? '') ?>">
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label for="kategori" class="form-label">Kategori</label>
          <select class="form-select" id="kategori" name="kategori" required>
            <option value="">Pilih kategori</option>
            <?php foreach (daftar_kategori() as $k): ?>
              <option value="<?= e($k) ?>" <?= ($buku['kategori'] ?? '') === $k ? 'selected' : '' ?>><?= e($k) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label for="penulis" class="form-label">Penulis</label>
          <input type="text" class="form-control" id="penulis" name="penulis" maxlength="100" required value="<?= e($buku['penulis'] ?? '') ?>">
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <label for="tahun_terbit" class="form-label">Tahun terbit</label>
          <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" min="1900" max="<?= date('Y') ?>" required value="<?= e((string) ($buku['tahun_terbit'] ?? '')) ?>">
        </div>
        <div class="col-md-6">
          <label for="stok" class="form-label">Stok</label>
          <input type="number" class="form-control" id="stok" name="stok" min="0" required value="<?= e((string) ($buku['stok'] ?? '1')) ?>">
          <div class="form-text">Stok 0 ditampilkan sebagai “Dipinjam”.</div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <label for="sampul" class="form-label">Sampul buku</label>
      <div class="mb-2">
        <img id="preview" class="preview-sampul" alt="Pratinjau sampul"
             src="<?= !empty($buku['sampul']) ? 'uploads/' . e($buku['sampul']) : 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' ?>">
      </div>
      <input type="file" class="form-control" id="sampul" name="sampul" accept=".jpg,.jpeg,.png,image/jpeg,image/png" <?= $sampul_wajib ? 'required' : '' ?>>
      <div class="form-text">
        <?= $sampul_wajib ? 'JPG atau PNG, maksimal 2 MB.' : 'Kosongkan jika tidak ingin mengganti sampul. JPG atau PNG, maksimal 2 MB.' ?>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><?= e($teks_tombol) ?></button>
    <a href="dashboard.php" class="btn btn-light">Batal</a>
  </div>
</form>
<?php
$skrip_tambahan = <<<'JS'
<script>
document.getElementById('sampul').addEventListener('change', function () {
  var file = this.files[0];
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    alert('Ukuran file melebihi 2 MB. Pilih file yang lebih kecil.');
    this.value = '';
    return;
  }
  document.getElementById('preview').src = URL.createObjectURL(file);
});
</script>
JS;
