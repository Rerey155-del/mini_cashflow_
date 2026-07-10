<?php
include 'koneksi.php';

$where = "";
$bulan_filter = "";
$tahun_filter = "";

if (isset($_GET['filter'])) {
    $bulan_filter = isset($_GET['bulan']) ? intval($_GET['bulan']) : 0;
    $tahun_filter = isset($_GET['tahun']) ? intval($_GET['tahun']) : 0;

    $conditions = [];
    if ($bulan_filter > 0) {
        $conditions[] = "MONTH(tanggal) = '$bulan_filter'";
    }
    if ($tahun_filter > 0) {
        $conditions[] = "YEAR(tanggal) = '$tahun_filter'";
    }
    if (!empty($conditions)) {
        $where = "WHERE " . implode(" AND ", $conditions);
    }
}

// total masuk
$q = mysqli_query($conn, "SELECT SUM(nominal) as total FROM transaksi WHERE jenis='masuk'");
$masuk = mysqli_fetch_assoc($q);
$total_masuk = $masuk['total'] ?? 0;

// total keluar
$q = mysqli_query($conn, "SELECT SUM(nominal) as total FROM transaksi WHERE jenis='keluar'");
$keluar = mysqli_fetch_assoc($q);
$total_keluar = $keluar['total'] ?? 0;

$saldo = $total_masuk - $total_keluar;

// data transaksi
$data = mysqli_query($conn, "SELECT * FROM transaksi $where ORDER BY tanggal DESC");

$nama_bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Cashflow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-5xl mx-auto p-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">Mini Cashflow Dashboard</h1>

        <!-- Kartu Ringkasan -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 shadow text-center">
                <p class="text-sm text-gray-500 mb-1">Total Masuk</p>
                <p class="text-xl font-bold text-green-600">Rp <?= number_format($total_masuk, 0, ',', '.') ?></p>
            </div>
            <div class="bg-white rounded-lg p-4 shadow text-center">
                <p class="text-sm text-gray-500 mb-1">Total Keluar</p>
                <p class="text-xl font-bold text-red-500">Rp <?= number_format($total_keluar, 0, ',', '.') ?></p>
            </div>
            <div class="bg-white rounded-lg p-4 shadow text-center">
                <p class="text-sm text-gray-500 mb-1">Saldo</p>
                <p class="text-xl font-bold <?= $saldo >= 0 ? 'text-blue-600' : 'text-red-600' ?>">
                    Rp <?= number_format($saldo, 0, ',', '.') ?>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">

            <!-- Kiri: Form Tambah & Filter -->
            <div class="col-span-1 space-y-4">

                <!-- Form Tambah Transaksi -->
                <div class="bg-white rounded-lg p-4 shadow">
                    <h2 class="text-lg font-semibold text-gray-700 mb-3">Tambah Transaksi</h2>
                    <form action="simpan.php" method="POST" class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Nama Transaksi</label>
                            <input type="text" name="nama" placeholder="Nama transaksi" required
                                class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Jenis</label>
                            <select name="jenis" class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                                <option value="masuk">Masuk</option>
                                <option value="keluar">Keluar</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Nominal</label>
                            <input type="number" name="nominal" placeholder="Contoh: 50000" min="1" required
                                class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Tanggal</label>
                            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required
                                class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded transition">
                            Simpan
                        </button>
                    </form>
                </div>

                <!-- Form Filter -->
                <div class="bg-white rounded-lg p-4 shadow">
                    <h2 class="text-lg font-semibold text-gray-700 mb-3">Filter</h2>
                    <form method="GET" class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Bulan</label>
                            <select name="bulan" class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                                <option value="">Semua Bulan</option>
                                <?php foreach ($nama_bulan as $num => $nama) { ?>
                                    <option value="<?= $num ?>" <?= ($bulan_filter == $num) ? 'selected' : '' ?>>
                                        <?= $nama ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Tahun</label>
                            <input type="number" name="tahun" placeholder="2025"
                                value="<?= $tahun_filter ?: '' ?>"
                                class="w-full mt-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" name="filter"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 rounded transition">
                                Filter
                            </button>
                            <a href="index.php"
                                class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm py-2 rounded transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

            </div>

            <!-- Kanan: Tabel Transaksi -->
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-700">Riwayat Transaksi</h2>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                            <?= mysqli_num_rows($data) ?> data
                        </span>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-center w-10">No</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-center">Jenis</th>
                                <th class="px-4 py-3 text-right">Nominal</th>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php
                            $no = 1;
                            if (mysqli_num_rows($data) > 0) {
                                while ($d = mysqli_fetch_assoc($data)) {
                            ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-center text-gray-400"><?= $no++ ?></td>
                                    <td class="px-4 py-3 text-gray-800"><?= htmlspecialchars($d['nama_transaksi']) ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <?php if ($d['jenis'] == 'masuk') { ?>
                                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Masuk</span>
                                        <?php } else { ?>
                                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded">Keluar</span>
                                        <?php } ?>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium <?= $d['jenis'] == 'masuk' ? 'text-green-600' : 'text-red-500' ?>">
                                        Rp <?= number_format($d['nominal'], 0, ',', '.') ?>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500"><?= $d['tanggal'] ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <button onclick="konfirmasiHapus(<?= $d['id'] ?>, '<?= addslashes(htmlspecialchars($d['nama_transaksi'])) ?>')"
                                            class="text-red-500 hover:text-red-700 text-xs underline">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                        Tidak ada data transaksi.
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function konfirmasiHapus(id, nama) {
            Swal.fire({
                title: 'Yakin ingin hapus?',
                text: nama + ' akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e53e3e',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus.php?id=' + id;
                }
            });
        }
    </script>

</body>

</html>