<?php
$tiket = $_GET['tiket'] ?? '';

if ($tiket === '') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaduan Berhasil Dikirim</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .card {
            background: #fff;
            max-width: 420px;
            width: 100%;
            border-radius: 10px;
            padding: 32px 28px;
            box-shadow:
                0 1px 3px rgba(0,0,0,0.06),
                0 1px 2px rgba(0,0,0,0.04);
            text-align: center;
        }

        .icon {
            font-size: 42px;
            margin-bottom: 12px;
        }

        h1 {
            font-size: 18px;
            font-weight: 700;
            color: #14342a;
            margin: 0 0 8px;
        }

        p {
            font-size: 13.5px;
            color: #6b7280;
            margin: 0 0 20px;
            line-height: 1.5;
        }

        .tiket-box {
            background: #f0f2f1;
            border: 1px dashed #1f4d3c;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 20px;
        }

        .tiket-box .label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
        }

        .tiket-box .kode {
            font-size: 20px;
            font-weight: 700;
            color: #1f4d3c;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        a.btn {
            display: block;
            padding: 11px;
            background: #1f4d3c;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 10px;
        }

        a.btn:hover {
            background: #17392c;
        }

        a.btn-outline {
            background: #fff;
            color: #1f4d3c;
            border: 1px solid #1f4d3c;
        }

        a.btn-outline:hover {
            background: #f0f2f1;
        }
    </style>
</head>

<body>

<div class="card">

    <div class="icon">✅</div>

    <h1>Pengaduan Berhasil Dikirim</h1>

    <p>
        Terima kasih. Pengaduan Anda telah berhasil diterima oleh pihak sekolah.
        Simpan nomor tiket berikut untuk mengecek status pengaduan.
    </p>

    <div class="tiket-box">
        <div class="label">NOMOR TIKET</div>

        <div class="kode">
            <?= htmlspecialchars($tiket) ?>
        </div>
    </div>

    <a class="btn btn-outline" href="cek_status.php">
        Cek Status Pengaduan
    </a>

    <a class="btn" href="index.php">
        Buat Pengaduan Baru
    </a>

</div>

</body>
</html>