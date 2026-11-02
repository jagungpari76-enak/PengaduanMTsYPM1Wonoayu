<?php
require_once '../config/database.php';

$pengaduan = null;
$riwayat = null;
$notFound = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $noTiket = trim($_POST['no_tiket'] ?? '');

    if ($noTiket !== '') {

        // Cari pengaduan berdasarkan nomor tiket
        $stmt = $conn->prepare("
            SELECT id, no_tiket, status, created_at, updated_at
            FROM pengaduan
            WHERE no_tiket = ?
        ");

        $stmt->bind_param("s", $noTiket);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $pengaduan = $result->fetch_assoc();

            // Ambil riwayat status
            $stmtLog = $conn->prepare("
                SELECT status_baru, catatan, created_at
                FROM status_log
                WHERE pengaduan_id = ?
                ORDER BY created_at ASC
            ");

            $stmtLog->bind_param("i", $pengaduan['id']);
            $stmtLog->execute();

            $riwayat = $stmtLog->get_result();

        } else {

            $notFound = true;
        }

        $stmt->close();
    }
}

// Label status
$labelStatus = [
    'baru'         => 'Baru',
    'diverifikasi' => 'Diverifikasi',
    'diproses'     => 'Diproses',
    'selesai'      => 'Selesai',
    'ditolak'      => 'Ditolak',
];
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Cek Status Pengaduan Sekolah</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f0f2f1;
            margin: 0;
            padding: 24px;
            color: #1f2937;
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
        }

        .top {
            text-align: center;
            margin-bottom: 24px;
        }

        .top h1 {
            font-size: 20px;
            font-weight: 700;
            color: #14342a;
            margin: 0 0 4px;
        }

        .top p {
            font-size: 13.5px;
            color: #6b7280;
            margin: 0;
        }

        .back-link {
            text-align: center;
            margin-bottom: 18px;
        }

        .back-link a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            background: #1f4d3c;
            padding: 8px 16px;
            border-radius: 999px;
            transition: background 0.15s;
        }

        .back-link a:hover {
            background: #17392c;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 24px;
            box-shadow:
                0 1px 3px rgba(0,0,0,0.06),
                0 1px 2px rgba(0,0,0,0.04);
        }

        label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        input[type=text] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            background: #fafafa;
            text-transform: uppercase;
        }

        input:focus {
            outline: none;
            border-color: #1f4d3c;
            background: #fff;
        }

        button {
            width: 100%;
            margin-top: 14px;
            padding: 12px;
            background: #1f4d3c;
            color: #fff;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
        }

        button:hover {
            background: #17392c;
        }

        .notfound {
            background: #fef2f2;
            color: #b91c1c;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 13.5px;
            margin-top: 16px;
        }

        .hasil {
            margin-top: 22px;
            padding-top: 22px;
            border-top: 1px solid #eef0ef;
        }

        .hasil .tiket {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
        }

        .hasil .kode {
            font-size: 18px;
            font-weight: 700;
            color: #1f4d3c;
            margin-bottom: 14px;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            color: #fff;
            display: inline-block;
        }

        .badge-baru {
            background: #6b7280;
        }

        .badge-diverifikasi {
            background: #2563eb;
        }

        .badge-diproses {
            background: #ea580c;
        }

        .badge-selesai {
            background: #15803d;
        }

        .badge-ditolak {
            background: #b91c1c;
        }

        .riwayat {
            margin-top: 20px;
        }

        .riwayat h3 {
            font-size: 13.5px;
            font-weight: 600;
            color: #374151;
            margin: 0 0 10px;
        }

        .riwayat-item {
            display: flex;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px dashed #eef0ef;
            font-size: 13px;
        }

        .riwayat-item:last-child {
            border-bottom: none;
        }

        .riwayat-item .waktu {
            color: #9ca3af;
            min-width: 110px;
            flex-shrink: 0;
        }

        .riwayat-item .isi {
            color: #374151;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="top">

        <h1>Cek Status Pengaduan Sekolah</h1>

        <p>
            Masukkan nomor tiket yang Anda dapat saat mengirim pengaduan
        </p>

    </div>

    <div class="back-link">

        <a href="index.php">
            &larr; Kembali ke Form Pengaduan
        </a>

    </div>

    <div class="card">

        <form method="POST">

            <label for="no_tiket">
                Nomor Tiket
            </label>

            <input
                type="text"
                id="no_tiket"
                name="no_tiket"
                placeholder="PGD-20260810-0001"
                value="<?= isset($_POST['no_tiket']) ? htmlspecialchars($_POST['no_tiket']) : '' ?>"
                required
            >

            <button type="submit">
                Cek Status Pengaduan
            </button>

            <?php if ($notFound): ?>

                <div class="notfound">
                    Nomor tiket tidak ditemukan.
                    Periksa kembali penulisannya.
                </div>

            <?php endif; ?>

        </form>

        <?php if ($pengaduan): ?>

            <div class="hasil">

                <div class="tiket">
                    NOMOR TIKET
                </div>

                <div class="kode">
                    <?= htmlspecialchars($pengaduan['no_tiket']) ?>
                </div>

                <?php
                $status = $pengaduan['status'];
                $label = $labelStatus[$status] ?? ucfirst($status);
                ?>

                <span class="badge badge-<?= htmlspecialchars($status) ?>">
                    <?= htmlspecialchars($label) ?>
                </span>

                <div class="riwayat">

                    <h3>
                        Riwayat Pengaduan
                    </h3>

                    <?php if ($riwayat && $riwayat->num_rows > 0): ?>

                        <?php while ($log = $riwayat->fetch_assoc()): ?>

                            <?php
                            $statusLog = $log['status_baru'];
                            $labelLog = $labelStatus[$statusLog] ?? ucfirst($statusLog);
                            ?>

                            <div class="riwayat-item">

                                <div class="waktu">
                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime($log['created_at'])
                                    ) ?>
                                </div>

                                <div class="isi">

                                    <?= htmlspecialchars($labelLog) ?>

                                    <?php if (!empty($log['catatan'])): ?>

                                        — <?= htmlspecialchars($log['catatan']) ?>

                                    <?php endif; ?>

                                </div>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="riwayat-item">

                            <div class="isi">
                                Belum ada riwayat perubahan status.
                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>