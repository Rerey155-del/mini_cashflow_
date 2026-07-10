<?php
require_once 'koneksi.php';


$nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
$jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
$nominal = intval($_POST['nominal']);
$tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);

if (empty($nama) || empty($jenis) || empty($tanggal)) {
    die("Semua field wajib diisi");
}

if ($nominal <= 0) {
    die("Nominal harus lebih dari 0");
}


$sql = "INSERT INTO transaksi (nama_transaksi, jenis, nominal, tanggal) VALUES ('$nama', '$jenis', '$nominal', '$tanggal')";

if (mysqli_query($conn, $sql)) {
    header("Location:index.php");
} else {
    die("Gagal menyimpan data: " . mysqli_error($conn));
}
