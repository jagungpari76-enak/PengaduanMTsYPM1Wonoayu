<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Ambil data dari form
$nama_pelapor    = bersihkanInput($_POST['nama_pelapor'] ?? '');
$kelas           = bersihkanInput($_POST['kelas'] ?? '');
$lokasi_kejadian = bersihkanInput($_POST['lokasi_kejadian'] ?? '');
$kategori_id     = (int)($_POST['kategori_id'] ?? 0);
$deskripsi       = bersihkanInput($_POST['deskripsi'] ?? '');

// --- Validasi dasar ---
if (
    $nama_pelapor === '' ||
    $kelas === '' ||
    $lokasi_kejadian === '' ||
    $kategori_id === 0 ||
    $deskripsi === ''
) {
    header("Location: index.php?error=" . urlencode("Mohon lengkapi semua field yang wajib diisi."));
    exit;
}

// Validasi kelas
$kelasValid = ['7', '8', '9'];

if (!in_array($kelas, $kelasValid)) {
    header("Location: index.php?error=" . urlencode("Kelas tidak valid."));
    exit;
}

// --- Upload foto (opsional) ---
$namaFoto = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {

    $namaFoto = uploadFotoPengaduan($_FILES['foto']);

    if ($namaFoto === false) {
        header("Location: index.php?error=" . urlencode(
            "Upload foto gagal. Pastikan format JPG/PNG dan ukuran maksimal 2MB."
        ));
        exit;
    }
}

// --- Generate nomor tiket ---
$noTiket = generateNoTiket($conn);

// --- Simpan ke database ---
$stmt = $conn->prepare("
    INSERT INTO pengaduan
    (no_tiket, nama_pelapor, kelas, lokasi_kejadian, kategori_id, deskripsi, foto, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'baru')
");

$stmt->bind_param(
    "ssssiss",
    $noTiket,
    $nama_pelapor,
    $kelas,
    $lokasi_kejadian,
    $kategori_id,
    $deskripsi,
    $namaFoto
);

// Jalankan query
if ($stmt->execute()) {

    $pengaduanId = $stmt->insert_id;

    // Catat log status awal
    $stmtLog = $conn->prepare("
        INSERT INTO status_log
        (pengaduan_id, status_baru, catatan)
        VALUES (?, 'baru', 'Pengaduan diterima sistem')
    ");

    $stmtLog->bind_param("i", $pengaduanId);
    $stmtLog->execute();
    $stmtLog->close();

    // Arahkan ke halaman sukses
    header("Location: sukses.php?tiket=" . urlencode($noTiket));
    exit;

} else {

    header("Location: index.php?error=" . urlencode(
        "Terjadi kesalahan. Silakan coba lagi."
    ));
    exit;
}

$stmt->close();