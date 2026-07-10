<?php
include 'koneksi.php';

/* ===========================
   FILTER BULAN & TAHUN
=========================== */

$where = "";

if (isset($_GET['filter'])) {

    $bulan = $_GET['bulan'];
    $tahun = $_GET['tahun'];

    if ($bulan != "" && $tahun != "") {

        $where = "WHERE MONTH(tanggal)='$bulan'
                AND YEAR(tanggal)='$tahun'";
    }
}

// total masuk 

$q = mysqli_query(
    $conn,

    "SELECT SUM(nominal) as total
FROM transaksi
WHERE jenis='masuk'"
);

$masuk = mysqli_fetch_assoc($q);

// total keluar

$q = mysqli_query(
    $conn,
    "SELECT SUM(nominal) as total
FROM transaksi
WHERE jenis='keluar'"
);

$keluar = mysqli_fetch_assoc($q);

$saldo = $masuk['total'] - $keluar['total'];

// data transaksi 

$data = mysqli_query(
    $conn,
    "SELECT *
FROM transaksi
$where
ORDER BY tanggal DESC"
); ?>

<!DOCTYPE html>

<html>

<head>

    <title>Mini Cashflow</title>

    <link rel="stylesheet"
        href="assets/css/style.css">

    <script src="assets/js/script.js"></script>

</head>

<body>

    <div class="container">

        <h1>Mini Cashflow Dashboard</h1>

        <div class="card">

            <h3>Total Masuk</h3>

            <h2>

                Rp <?= number_format($masuk['total'] ?? 0, 0, ',', '.') ?>

            </h2>

        </div>

        <div class="card">

            <h3>Total Keluar</h3>

            <h2>

                Rp <?= number_format($keluar['total'] ?? 0, 0, ',', '.') ?>

            </h2>

        </div>

        <div class="card">

            <h3>Saldo</h3>

            <h2>

                Rp <?= number_format($saldo, 0, ',', '.') ?>

            </h2>

        </div>

        <hr>

        <h2>Tambah Transaksi</h2>

        <form action="simpan.php" method="POST">

            <input
                type="text"
                name="nama"
                placeholder="Nama Transaksi"
                required>

            <select name="jenis">

                <option value="masuk">Masuk</option>

                <option value="keluar">Keluar</option>

            </select>

            <input
                type="number"
                name="nominal"
                placeholder="Nominal"
                required>

            <input
                type="date"
                name="tanggal"
                required>

            <button>Simpan</button>

        </form>

        <hr>

        <h2>Filter</h2>

        <form>

            <select name="bulan">

                <option value="">Semua Bulan</option>

                <?php
                for ($i = 1; $i <= 12; $i++) {
                ?>
                    <option value="<?= $i ?>">
                        <?= $i ?>
                    </option>
                <?php } ?>

            </select>

            <input
                type="number"
                name="tahun"
                placeholder="2025">

            <button
                name="filter">

                Filter

            </button>

        </form>

        <hr>

        <table>

            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Nominal</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>

            <?php

            $no = 1;

            while ($d = mysqli_fetch_assoc($data)) {

            ?>

                <tr class="<?= $d['jenis']; ?>">

                    <td><?= $no++ ?></td>

                    <td><?= htmlspecialchars($d['nama_transaksi']) ?></td>

                    <td><?= ucfirst($d['jenis']) ?></td>

                    <td>

                        Rp <?= number_format($d['nominal'], 0, ',', '.') ?>

                    </td>

                    <td><?= $d['tanggal'] ?></td>

                    <td>

                        <a
                            href="hapus.php?id=<?= $d['id'] ?>"
                            onclick="return konfirmasi()">

                            Hapus

                        </a>

                    </td>

                </tr>

            <?php } ?>

        </table>

    </div>

</body>

</html>