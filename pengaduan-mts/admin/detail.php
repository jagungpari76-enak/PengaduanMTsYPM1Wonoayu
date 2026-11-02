<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

// Ambil data pengaduan berdasarkan id
$stmt = $conn->prepare("SELECT p.*, k.nama_kategori
                         FROM pengaduan p
                         JOIN kategori k ON p.kategori_id = k.id
                         WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Pengaduan tidak ditemukan.");
}
$data = $result->fetch_assoc();
$stmt->close();

$pesan = '';

// --- Proses update status ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statusBaru = $_POST['status'] ?? '';
    $catatan    = trim($_POST['catatan'] ?? '');
    $statusValid = ['baru', 'diverifikasi', 'diproses', 'selesai', 'ditolak'];

    if (in_array($statusBaru, $statusValid)) {
        $upd = $conn->prepare("UPDATE pengaduan SET status = ? WHERE id = ?");
        $upd->bind_param("si", $statusBaru, $id);
        $upd->execute();
        $upd->close();

        $log = $conn->prepare("INSERT INTO status_log (pengaduan_id, status_baru, catatan, updated_by) VALUES (?, ?, ?, ?)");
        $log->bind_param("issi", $id, $statusBaru, $catatan, $_SESSION['admin_id']);
        $log->execute();
        $log->close();

        $pesan = "Status berhasil diperbarui.";
        $data['status'] = $statusBaru; // refresh tampilan tanpa reload query
    }
}

// Ambil riwayat status
$stmtLog = $conn->prepare("SELECT sl.status_baru, sl.catatan, sl.created_at, u.nama_lengkap
                            FROM status_log sl
                            LEFT JOIN users u ON sl.updated_by = u.id
                            WHERE sl.pengaduan_id = ? ORDER BY sl.created_at ASC");
$stmtLog->bind_param("i", $id);
$stmtLog->execute();
$riwayat = $stmtLog->get_result();

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
    <title>Detail Pengaduan - <?= htmlspecialchars($data['no_tiket']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', Arial, sans-serif; background: #f0f2f1; margin: 0; padding: 28px; color: #1f2937; }
        .container { max-width: 640px; margin: 0 auto; }
        .back-link { margin-bottom: 16px; }
        .back-link a {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; font-weight: 600; color: #fff; text-decoration: none;
            background: #1f4d3c; padding: 8px 16px; border-radius: 999px;
            transition: background 0.15s;
        }
        .back-link a:hover { background: #17392c; }
        .card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04); margin-bottom: 18px; }
        .tiket-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
        .tiket-header .kode { font-size: 18px; font-weight: 700; color: #14342a; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12.5px; font-weight: 600; color: #fff; display: inline-block; }
        .badge-baru { background: #6b7280; }
        .badge-diverifikasi { background: #2563eb; }
        .badge-diproses { background: #ea580c; }
        .badge-selesai { background: #15803d; }
        .badge-ditolak { background: #b91c1c; }

        .field { margin-bottom: 14px; }
        .field .label { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 3px; }
        .field .value { font-size: 14.5px; color: #1f2937; }

        .foto-preview { max-width: 100%; border-radius: 8px; margin-top: 6px; border: 1px solid #eef0ef; }

        select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-family: inherit; font-size: 14px; background: #fafafa; }
        select:focus, textarea:focus { outline: none; border-color: #1f4d3c; background: #fff; }
        label.form-label { display: block; font-size: 13.5px; font-weight: 600; color: #374151; margin-top: 14px; margin-bottom: 6px; }
        label.form-label:first-child { margin-top: 0; }
        button { margin-top: 16px; padding: 10px 20px; background: #1f4d3c; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; }
        button:hover { background: #17392c; }
        .sukses-msg { background: #f0fdf4; color: #15803d; padding: 10px 14px; border-radius: 6px; font-size: 13.5px; margin-bottom: 16px; }

        .riwayat h3 { font-size: 14px; font-weight: 700; color: #14342a; margin: 0 0 12px; }
        .riwayat-item { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px dashed #eef0ef; font-size: 13.5px; }
        .riwayat-item:last-child { border-bottom: none; }
        .riwayat-item .waktu { color: #9ca3af; min-width: 120px; flex-shrink: 0; }
        .riwayat-item .isi { color: #374151; }
        .riwayat-item .oleh { color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="back-link"><a href="dashboard.php">&larr; Kembali ke dashboard</a></div>

    <?php if ($pesan): ?><div class="sukses-msg"><?= htmlspecialchars($pesan) ?></div><?php endif; ?>

    <div class="card">
        <div class="tiket-header">
            <div class="kode"><?= htmlspecialchars($data['no_tiket']) ?></div>
            <span class="badge badge-<?= $data['status'] ?>"><?= $labelStatus[$data['status']] ?></span>
        </div>

        <div class="field">
            <div class="label">Nama Pelapor</div>
            <div class="value"><?= htmlspecialchars($data['nama_pelapor']) ?></div>
        </div>
        <div class="field">
            <div class="label">Kelas</div>
            <div class="value"><?= htmlspecialchars($data['kelas'] ?: '-') ?></div>
        </div>
        <div class="field">
            <div class="label">Lokasi Kejadian</div>
            <div class="value"><?= htmlspecialchars($data['lokasi_kejadian'] ?: '-') ?></div>
        </div>
        <div class="field">
            <div class="label">Kategori</div>
            <div class="value"><?= htmlspecialchars($data['nama_kategori']) ?></div>
        </div>
        <div class="field">
            <div class="label">Deskripsi</div>
            <div class="value"><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></div>
        </div>
        <?php if ($data['foto']): ?>
            <div class="field">
                <div class="label">Foto Bukti</div>
                <img class="foto-preview" src="../assets/uploads/<?= htmlspecialchars($data['foto']) ?>" alt="Foto bukti">
            </div>
        <?php endif; ?>
        <div class="field">
            <div class="label">Tanggal Lapor</div>
            <div class="value"><?= date('d/m/Y H:i', strtotime($data['created_at'])) ?></div>
        </div>
    </div>

    <div class="card">
        <form method="POST">
            <label class="form-label" for="status">Ubah Status</label>
            <select name="status" id="status">
                <?php foreach ($labelStatus as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $data['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>

            <label class="form-label" for="catatan">Catatan (opsional)</label>
            <textarea name="catatan" id="catatan" rows="3" placeholder="Contoh: sudah ditinjau, akan ditindaklanjuti minggu ini"></textarea>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>

    <div class="card riwayat">
        <h3>Riwayat Status</h3>
        <?php while ($log = $riwayat->fetch_assoc()): ?>
            <div class="riwayat-item">
                <div class="waktu"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></div>
                <div class="isi">
                    <?= $labelStatus[$log['status_baru']] ?>
                    <?php if (!empty($log['catatan'])): ?> — <?= htmlspecialchars($log['catatan']) ?><?php endif; ?>
                    <?php if (!empty($log['nama_lengkap'])): ?>
                        <div class="oleh">oleh <?= htmlspecialchars($log['nama_lengkap']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>