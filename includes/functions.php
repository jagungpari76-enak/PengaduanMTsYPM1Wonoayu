<?php
// =========================================================
// FUNCTIONS - Helper umum
// =========================================================

/**
 * Generate nomor tiket unik untuk pengaduan.
 * Format: PGD-YYYYMMDD-XXXX (XXXX = nomor urut hari itu, reset tiap hari)
 */
function generateNoTiket($conn) {
    $tanggal = date('Ymd');
    $prefix  = "PGD-{$tanggal}-";

    // Hitung berapa pengaduan yang sudah masuk hari ini
    $stmt = $conn->prepare("SELECT COUNT(*) AS jumlah FROM pengaduan WHERE no_tiket LIKE ?");
    $like = $prefix . '%';
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    $urutan = $row['jumlah'] + 1;
    $noUrut = str_pad($urutan, 4, '0', STR_PAD_LEFT); // 0001, 0002, dst

    $noTiket = $prefix . $noUrut;

    // Jaga-jaga kalau ada tabrakan (race condition), cek ulang keunikan
    $cekUnik = $conn->prepare("SELECT id FROM pengaduan WHERE no_tiket = ?");
    $cekUnik->bind_param("s", $noTiket);
    $cekUnik->execute();
    $cekUnik->store_result();
    if ($cekUnik->num_rows > 0) {
        // fallback: tambahkan random suffix kecil kalau kebetulan bentrok
        $noTiket = $prefix . $noUrut . '-' . substr(uniqid(), -3);
    }
    $cekUnik->close();

    return $noTiket;
}

/**
 * Bersihkan input teks dari user (basic sanitasi tampilan, bukan pengganti prepared statement)
 */
function bersihkanInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Upload foto bukti pengaduan (opsional). Return nama file kalau sukses, null kalau tidak ada file / gagal.
 */
function uploadFotoPengaduan($fileInput, $uploadDir = '../assets/uploads/') {
    if (!isset($fileInput) || $fileInput['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // foto memang opsional
    }

    if ($fileInput['error'] !== UPLOAD_ERR_OK) {
        return false; // ada error upload
    }

    $ekstensiDiizinkan = ['jpg', 'jpeg', 'png'];
    $ekstensi = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiDiizinkan)) {
        return false;
    }

    // Batas ukuran 50MB
    if ($fileInput['size'] > 50 * 1024 * 1024) {
        return false;
    }

    $namaBaru = uniqid('bukti_') . '.' . $ekstensi;
    $tujuan = $uploadDir . $namaBaru;

    if (move_uploaded_file($fileInput['tmp_name'], $tujuan)) {
        return $namaBaru;
    }

    return false;
}