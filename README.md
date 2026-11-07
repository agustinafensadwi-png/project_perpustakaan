# Mini Digital Library

Aplikasi manajemen koleksi buku perpustakaan berbasis **PHP Native + MySQL (mysqli)** dengan autentikasi session, cookie *Remember Me*, CRUD, dan upload sampul buku.
Tugas Terstruktur — Pemrograman Web, Program Studi Sistem Informasi.

- **Video demo:** _tempel link YouTube di sini_
- **Login awal:** username `admin`, password `admin123`

## Fitur
| Modul | Keterangan |
|---|---|
| Login | Form POST, password di-hash (`password_hash` / `password_verify`), `session_regenerate_id` |
| Session | Dashboard dan semua halaman CRUD hanya bisa diakses setelah login |
| Cookie | *Remember Me* menyimpan **username** (bukan password) selama 30 hari |
| Logout | Menghapus session, cookie session, dan cookie Remember Me, lalu redirect ke login |
| Create / Read / Update / Delete | Data buku dengan pencarian dan filter kategori |
| Upload | Hanya `.jpg/.png`, maksimal 2 MB, dicek ekstensi + MIME asli + `getimagesize`, nama file diacak |
| Hapus / ganti file | Hapus data otomatis `unlink` sampul; edit dengan sampul baru menghapus sampul lama |
| Logika status | `if-else`: stok > 0 → **Tersedia**, stok 0 → **Dipinjam** |
| Keamanan | Prepared statement, `htmlspecialchars`, token CSRF, hapus lewat POST |

## Cara menjalankan
1. Pasang XAMPP/Laragon, aktifkan Apache dan MySQL.
2. Salin folder proyek ke `htdocs/` (contoh: `htdocs/project_perpustakaan`).
3. Impor `db_perpustakaan.sql` lewat phpMyAdmin.
4. Sesuaikan kredensial di `config/koneksi.php` bila perlu.
5. Buka `http://localhost/project_perpustakaan/`.

## Struktur
```
project_perpustakaan/
├── config/koneksi.php
├── includes/ (init, fungsi, header, footer, form_buku)
├── assets/style.css
├── uploads/
├── index.php            # Login
├── proses_login.php
├── logout.php
├── dashboard.php        # Read
├── tambah_buku.php  + proses_tambah.php   # Create
├── edit_buku.php    + proses_edit.php     # Update
├── hapus_buku.php                          # Delete
└── db_perpustakaan.sql
```

## Struktur tabel database
**users**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT UNSIGNED, PK, AI | |
| username | VARCHAR(50), UNIQUE | |
| password | VARCHAR(255) | hash bcrypt |
| nama_lengkap | VARCHAR(100) | |
| created_at | TIMESTAMP | |

**buku**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT UNSIGNED, PK, AI | |
| judul | VARCHAR(150) | |
| kategori | VARCHAR(50) | |
| penulis | VARCHAR(100) | |
| tahun_terbit | YEAR | |
| stok | INT UNSIGNED | 0 = Dipinjam |
| sampul | VARCHAR(255) | nama file di `uploads/` |
| created_at / updated_at | TIMESTAMP | |

## Screenshot
> Ambil screenshot setelah aplikasi berjalan, simpan di folder `docs/`, lalu ganti placeholder di bawah.

| Halaman | Screenshot |
|---|---|
| Login | `![Login](docs/login.png)` |
| Dashboard | `![Dashboard](docs/dashboard.png)` |
| Tambah buku | `![Tambah](docs/tambah.png)` |
| Edit buku | `![Edit](docs/edit.png)` |
| Konfirmasi hapus | `![Hapus](docs/hapus.png)` |
