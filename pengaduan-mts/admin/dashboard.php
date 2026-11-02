<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$filterStatus = $_GET['status'] ?? '';

$labelStatus = [
    'baru'         => 'Baru',
    'diverifikasi' => 'Diverifikasi',
    'diproses'     => 'Diproses',
    'selesai'      => 'Selesai',
    'ditolak'      => 'Ditolak'
];

$sql = "
    SELECT
        p.id,
        p.no_tiket,
        p.nama_pelapor,
        p.deskripsi,
        p.status,
        p.created_at,
        k.nama_kategori
    FROM pengaduan p
    LEFT JOIN kategori k ON p.kategori_id = k.id
";

if ($filterStatus !== '') {
    $sql .= " WHERE p.status = ?";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);

if ($filterStatus !== '') {
    $stmt->bind_param("s", $filterStatus);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard - Pengaduan Sekolah</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 20px;
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    color: #333;
}

.container {
    max-width: 1200px;
    margin: auto;
}

/* HEADER */

.header {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h2 {
    margin: 0 0 6px;
    color: #2d6a4f;
}

.info {
    color: #666;
    font-size: 14px;
}

.logout {
    background: #dc3545;
    color: white;
    padding: 9px 15px;
    border-radius: 7px;
    text-decoration: none;
    font-size: 14px;
}

.logout:hover {
    background: #bb2d3b;
}

/* FILTER */

.filter {
    background: white;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 20px;
}

.filter a {
    display: inline-block;
    margin: 5px;
    padding: 8px 13px;
    border-radius: 7px;

    text-decoration: none;

    color: #2d6a4f;
    background: #eaf4ef;

    font-size: 13px;
}

.filter a:hover {
    background: #d7ebdf;
}

.filter a.active {
    background: #2d6a4f;
    color: white;
}

/* TABLE */

.table-box {
    background: white;
    border-radius: 12px;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #2d6a4f;
    color: white;
    padding: 13px;
    text-align: left;
    font-size: 14px;
}

td {
    padding: 13px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

tr:hover {
    background: #f8f9fa;
}

/* STATUS */

.badge {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 6px;
    color: white;
    font-size: 12px;
}

.badge-baru {
    background: #6c757d;
}

.badge-diverifikasi {
    background: #0d6efd;
}

.badge-diproses {
    background: #fd7e14;
}

.badge-selesai {
    background: #198754;
}

.badge-ditolak {
    background: #dc3545;
}

/* DETAIL */

.aksi a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: bold;
}

.aksi a:hover {
    text-decoration: underline;
}

.kosong {
    text-align: center;
    padding: 35px;
    color: #888;
}

/* MOBILE */

@media (max-width: 700px) {

    body {
        padding: 10px;
    }

    .header {
        align-items: flex-start;
        flex-direction: column;
    }

    .logout {
        align-self: flex-end;
    }

    th,
    td {
        white-space: nowrap;
    }

}

</style>

</head>

<body>

<div class="container">

    <!-- HEADER -->

    <div class="header">

        <div>

            <h2>
                Dashboard Pengaduan Sekolah
            </h2>

            <div class="info">

                MTs YPM 1 Wonoayu

                <br>

                Login sebagai:
                <strong>
                    <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin') ?>
                </strong>

            </div>

        </div>

        <a
            class="logout"
            href="logout.php"
        >
            Logout
        </a>

    </div>


    <!-- FILTER STATUS -->

    <div class="filter">

        <strong>
            Filter Status:
        </strong>

        <a
            href="dashboard.php"
            class="<?= $filterStatus === '' ? 'active' : '' ?>"
        >
            Semua
        </a>

        <?php foreach ($labelStatus as $key => $label): ?>

            <a
                href="dashboard.php?status=<?= urlencode($key) ?>"
                class="<?= $filterStatus === $key ? 'active' : '' ?>"
            >
                <?= htmlspecialchars($label) ?>
            </a>

        <?php endforeach; ?>

    </div>


    <!-- DATA PENGADUAN -->

    <div class="table-box">

        <table>

            <thead>

                <tr>

                    <th>
                        No Tiket
                    </th>

                    <th>
                        Pelapor
                    </th>

                    <th>
                        Kategori
                    </th>

                    <th>
                        Pengaduan
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Tanggal
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>

            <?php if ($result->num_rows === 0): ?>

                <tr>

                    <td
                        colspan="7"
                        class="kosong"
                    >
                        Belum ada pengaduan.
                    </td>

                </tr>

            <?php else: ?>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <?php

                    $status = $row['status'];

                    $label = $labelStatus[$status] ?? $status;

                    ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($row['no_tiket']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama_pelapor']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $row['nama_kategori'] ?? '-'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                mb_strimwidth(
                                    $row['deskripsi'],
                                    0,
                                    60,
                                    '...'
                                )
                            ) ?>
                        </td>

                        <td>

                            <span
                                class="badge badge-<?= htmlspecialchars($status) ?>"
                            >
                                <?= htmlspecialchars($label) ?>
                            </span>

                        </td>

                        <td>
                            <?= date(
                                'd/m/Y H:i',
                                strtotime($row['created_at'])
                            ) ?>
                        </td>

                        <td class="aksi">

                            <a
                                href="detail.php?id=<?= (int)$row['id'] ?>"
                            >
                                Lihat Detail
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>

<?php
$stmt->close();
?>