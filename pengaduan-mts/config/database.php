<?php
// =========================================================
// KONEKSI DATABASE - Sistem Pengaduan Sekolah MTs
// =========================================================

// --- Konfigurasi Database ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pengaduan_mts');

// --- Koneksi ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// --- Cek koneksi ---
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// --- Set charset ---
$conn->set_charset("utf8mb4");