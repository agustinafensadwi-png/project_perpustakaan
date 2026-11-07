-- Database: db_perpustakaan
CREATE DATABASE IF NOT EXISTS db_perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_perpustakaan;

DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,          -- hash bcrypt (password_hash)
  nama_lengkap VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE buku (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  kategori VARCHAR(50) NOT NULL,
  penulis VARCHAR(100) NOT NULL,
  tahun_terbit YEAR NOT NULL,
  stok INT UNSIGNED NOT NULL DEFAULT 0,    -- stok > 0 = Tersedia, 0 = Dipinjam
  sampul VARCHAR(255) NOT NULL,            -- nama file di folder uploads/
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Akun awal: username "admin", password "admin123"
INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', '$2y$10$KpjrdNKtOaLkN.uuA6Tm1u/.LktCwH8AcTCrSLvZR16wNrILzqH4.', 'Administrator Perpustakaan');
