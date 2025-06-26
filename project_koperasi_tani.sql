-- =================================================================
-- BAGIAN 1: SETUP DATABASE
-- =================================================================
CREATE DATABASE koperasi_tani;
USE koperasi_tani;


-- =================================================================
-- BAGIAN 2: PEMBUATAN STRUKTUR TABEL
-- =================================================================

CREATE TABLE Kelompok_Tani (
    ID_Kelompok         INT AUTO_INCREMENT PRIMARY KEY,
    Nama_Kelompok       VARCHAR(100) NOT NULL,
    Alamat_Kelompok     VARCHAR(50) NOT NULL,
    UNIQUE KEY uq_nama_kelompok (Nama_Kelompok) 
);

CREATE TABLE Anggota (
    NIK                 VARCHAR(16) PRIMARY KEY,
    Nama                VARCHAR(100) NOT NULL,
    Dusun               VARCHAR(50) NOT NULL,
    RT                  VARCHAR(3) NOT NULL,
    RW                  VARCHAR(3) NOT NULL,
    Tanggal_Lahir       DATE NOT NULL,
    No_HP               VARCHAR(15) NOT NULL,
    Jenis_Kelamin       ENUM('Laki-laki', 'Perempuan') NOT NULL,
    profile_picture     VARCHAR(255) NULL DEFAULT 'default.png',
    ID_Kelompok         INT NOT NULL,
    FOREIGN KEY (ID_Kelompok) REFERENCES Kelompok_Tani(ID_Kelompok) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE Pengurus (
    NIK                   VARCHAR(16) PRIMARY KEY,
    Jabatan               VARCHAR(50) NOT NULL,
    Tanggal_Mulai_Jabatan DATE,
    Tanggal_Akhir_Jabatan DATE,
    FOREIGN KEY (NIK) REFERENCES Anggota(NIK) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Pinjaman (
    ID_Pinjaman               INT AUTO_INCREMENT PRIMARY KEY,
    NIK                       VARCHAR(16) NOT NULL,
    Tanggal_Pinjaman          DATE NOT NULL,
    Pokok_Pinjaman            DECIMAL(10,2) NOT NULL,
    Bunga_Total               DECIMAL(10,2) GENERATED ALWAYS AS (Pokok_Pinjaman * 0.10) STORED, 
    Lama_Angsuran             INT NOT NULL DEFAULT 10,
    Besar_Angsuran_Per_Bulan  DECIMAL(10,2) GENERATED ALWAYS AS ((Pokok_Pinjaman * (1 + 0.10)) / Lama_Angsuran) STORED,
    FOREIGN KEY (NIK) REFERENCES Anggota(NIK) ON DELETE RESTRICT ON UPDATE CASCADE,
    CHECK (Lama_Angsuran > 0)
);

CREATE TABLE Verifikasi (
    ID_Verifikasi       INT AUTO_INCREMENT PRIMARY KEY,
    ID_Pinjaman         INT NOT NULL UNIQUE,
    NIK_Penyetuju       VARCHAR(16) NULL,
    Status              ENUM('Disetujui', 'Ditolak', 'Menunggu') NOT NULL DEFAULT 'Menunggu',
    Tanggal_Verifikasi  DATE NULL,
    FOREIGN KEY (ID_Pinjaman) REFERENCES Pinjaman(ID_Pinjaman) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (NIK_Penyetuju) REFERENCES Pengurus(NIK) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Angsuran (
    ID_Angsuran         INT AUTO_INCREMENT PRIMARY KEY,
    ID_Pinjaman         INT NOT NULL,
    Tanggal_Bayar       DATE NOT NULL,
    Jumlah_Bayar        DECIMAL(10,2) NOT NULL, 
    Angsuran_Ke         INT,
    FOREIGN KEY (ID_Pinjaman) REFERENCES Pinjaman(ID_Pinjaman) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE users (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    username            VARCHAR(50) NOT NULL UNIQUE,
    password            VARCHAR(255) NOT NULL,
    role                ENUM('admin', 'pengurus', 'anggota') NOT NULL,
    nik_anggota         VARCHAR(16) NULL UNIQUE,
    FOREIGN KEY (nik_anggota) REFERENCES Anggota(NIK) ON DELETE SET NULL ON UPDATE CASCADE
);


-- =================================================================
-- BAGIAN 3: PEMBUATAN VIEW (TABEL VIRTUAL)
-- =================================================================

CREATE VIEW View_Anggota AS
SELECT 
    a.NIK, a.Nama, a.Jenis_Kelamin, a.Tanggal_Lahir, a.No_HP, a.Dusun, a.RT, a.RW, a.profile_picture,
    kt.Alamat_Kelompok AS Desa,
    CONCAT('Dusun ', a.Dusun, ', RT ', a.RT, ' RW ', a.RW, ', ', kt.Alamat_Kelompok) AS Alamat_Lengkap,
    kt.Nama_Kelompok,
    kt.ID_Kelompok 
FROM Anggota a
JOIN Kelompok_Tani kt ON a.ID_Kelompok = kt.ID_Kelompok;

CREATE VIEW View_Pinjaman AS
SELECT 
    p.ID_Pinjaman, p.Tanggal_Pinjaman, p.Pokok_Pinjaman, p.Bunga_Total, p.Lama_Angsuran, p.Besar_Angsuran_Per_Bulan,
    a.NIK, a.Nama AS Nama_Anggota, 
    v.Status AS Status_Verifikasi, v.Tanggal_Verifikasi,
    (SELECT Nama FROM Anggota WHERE NIK = v.NIK_Penyetuju) AS Nama_Penyetuju,
    (p.Pokok_Pinjaman + p.Bunga_Total) AS Total_Tagihan,
    COALESCE((SELECT SUM(Jumlah_Bayar) FROM Angsuran WHERE ID_Pinjaman = p.ID_Pinjaman), 0) AS Total_Sudah_Bayar,
    ((p.Pokok_Pinjaman + p.Bunga_Total) - COALESCE((SELECT SUM(Jumlah_Bayar) FROM Angsuran WHERE ID_Pinjaman = p.ID_Pinjaman), 0)) AS Sisa_Tagihan
FROM Pinjaman p
JOIN Anggota a ON p.NIK = a.NIK
LEFT JOIN Verifikasi v ON p.ID_Pinjaman = v.ID_Pinjaman;

CREATE VIEW View_SHU_Tahunan AS
SELECT
    YEAR(angs.Tanggal_Bayar) AS Tahun,
    SUM(pin.Bunga_Total / pin.Lama_Angsuran) AS Total_SHU
FROM Angsuran angs
JOIN Pinjaman pin ON angs.ID_Pinjaman = pin.ID_Pinjaman
WHERE (SELECT v.Status FROM Verifikasi v WHERE v.ID_Pinjaman = pin.ID_Pinjaman) = 'Disetujui'
GROUP BY YEAR(angs.Tanggal_Bayar)
ORDER BY Tahun DESC;


-- =================================================================
-- BAGIAN 4: PENGISIAN DATA CONTOH
-- =================================================================

INSERT INTO Kelompok_Tani (Nama_Kelompok, Alamat_Kelompok) VALUES
('Tani Makmur', 'Desa Argomulyo'),
('Sumber Rejeki', 'Desa Ledok');

INSERT INTO Anggota (NIK, Nama, Dusun, RT, RW, Tanggal_Lahir, No_HP, Jenis_Kelamin, ID_Kelompok) VALUES
('3373011203880001', 'Budi Santoso', 'Krajan', '01', '01', '1988-03-12', '081234567890', 'Laki-laki', 1),
('3373021507920002', 'Siti Aminah', 'Sidomulyo', '02', '01', '1992-07-15', '081234567891', 'Perempuan', 1),
('3373032010850003', 'Ahmad Fauzi', 'Nggalek', '03', '02', '1985-10-20', '081234567892', 'Laki-laki', 2);

INSERT INTO Pengurus (NIK, Jabatan, Tanggal_Mulai_Jabatan) VALUES
('3373011203880001', 'Ketua', '2025-01-15'),
('3373032010850003', 'Bendahara', '2025-01-15'); -- Jabatan diubah agar lebih realistis

INSERT INTO Pinjaman (ID_Pinjaman, NIK, Tanggal_Pinjaman, Pokok_Pinjaman, Lama_Angsuran) VALUES
(1, '3373021507920002', '2025-02-10', 1000000.00, 10),
(2, '3373032010850003', '2025-03-05', 500000.00, 5); -- Lama angsuran diubah

INSERT INTO Verifikasi (ID_Pinjaman, NIK_Penyetuju, Status, Tanggal_Verifikasi) VALUES
(1, '3373011203880001', 'Disetujui', '2025-02-11'),
(2, NULL, 'Menunggu', NULL);

INSERT INTO Angsuran (ID_Angsuran, ID_Pinjaman, Tanggal_Bayar, Jumlah_Bayar, Angsuran_Ke) VALUES
(1, 1, '2025-03-09', 110000.00, 1),
(2, 1, '2025-04-10', 110000.00, 2);


-- =================================================================
-- BAGIAN 5: PENGATURAN PENGGUNA DATABASE (DCL)
-- =================================================================

CREATE USER 'admin_koperasi'@'localhost' IDENTIFIED BY 'PasswordAdmin123!';
GRANT ALL PRIVILEGES ON koperasi_tani.* TO 'admin_koperasi'@'localhost';
FLUSH PRIVILEGES;
