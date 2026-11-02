<?php
// =========================================================
// AUTH - Proteksi halaman admin
// Panggil file ini di baris paling atas tiap halaman admin
// (kecuali login.php sendiri)
// =========================================================

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Variabel siap pakai di halaman admin:
// $_SESSION['admin_id']
// $_SESSION['username']
// $_SESSION['nama_lengkap']