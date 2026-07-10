<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'mini_cashflow';


$conn = mysqli_connect($host, $user, $password);

if (!$conn) {
    die("Koneksi ke MySQL server gagal: " . mysqli_connect_error());
}

if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$database`")) {
    die("Gagal membuat database: " . mysqli_error($conn));
}


if (!mysqli_select_db($conn, $database)) {
    die("Gagal memilih database: " . mysqli_error($conn));
}


$table_sql = "CREATE TABLE IF NOT EXISTS transaksi(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_transaksi VARCHAR(100) NOT NULL,
    jenis ENUM('masuk','keluar') NOT NULL,
    nominal INT NOT NULL,
    tanggal DATE NOT NULL
)";
if (!mysqli_query($conn, $table_sql)) {
    die("Gagal membuat tabel transaksi: " . mysqli_error($conn));
}

$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi");
$count_data = mysqli_fetch_assoc($count_result);
if ($count_data['total'] == 0) {
    $seed_sql = "INSERT INTO transaksi(nama_transaksi, jenis, nominal, tanggal) VALUES
    ('Gaji', 'masuk', 5000000, '2025-07-01'),
    ('Makan', 'keluar', 50000, '2025-07-02'),
    ('Transport', 'keluar', 30000, '2025-07-03'),
    ('Bonus', 'masuk', 1000000, '2025-07-04')";
    mysqli_query($conn, $seed_sql);
}
