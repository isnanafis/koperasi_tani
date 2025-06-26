# Proyek Sistem Informasi Koperasi Tani

Ini adalah proyek tugas akhir mata kuliah Database untuk membangun sistem informasi koperasi tani berbasis web.

## Teknologi yang Digunakan
- Bahasa: PHP 8.2
- Database: MySQL
- Web Server: Apache (XAMPP)

## Cara Instalasi
1. Pastikan Anda telah menginstall XAMPP.
2. Letakkan folder proyek ini ke dalam direktori C:\xampp\htdocs\.
3. Buka phpMyAdmin, buat database baru bernama koperasi_tani.
4. Import file project_koperasi_tani.sql ke dalam database tersebut.
5. Setup Akun Admin: Buka browser dan jalankan file setup_admin.php sekali saja dengan mengakses http://localhost/koperasi_tani/setup_admin.php. File ini akan membuat akun administrator pertama untuk sistem.
6. (PENTING) Setelah berhasil dijalankan, segera hapus file setup_admin.php dari folder proyek Anda untuk mencegah pembuatan akun admin lain oleh pihak yang tidak berwenang.
7. Buka browser dan akses http://localhost/koperasi_tani untuk masuk ke halaman login.
