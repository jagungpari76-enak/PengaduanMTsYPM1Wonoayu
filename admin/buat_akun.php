<?php
require_once '../config/database.php';

$akun = [
    [
        'username' => 'admin_sekolah',
        'password' => 'password',
        'nama'     => 'Administrator MTs'
    ],
    [
        'username' => 'bk_mts_ypm1',
        'password' => 'ypm1wonoayu',
        'nama'     => 'Guru BK'
    ],
    [
        'username' => 'guru_mts',
        'password' => 'guru123',
        'nama'     => 'Guru MTs'
    ],
    [
        'username' => 'tu_mts',
        'password' => 'tu123',
        'nama'     => 'Tata Usaha'
    ]
];

foreach ($akun as $data) {

    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $nama     = $data['nama'];

    $cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $hasil = $cek->get_result();

    if ($hasil->num_rows > 0) {

        $stmt = $conn->prepare("
            UPDATE users
            SET password = ?, nama_lengkap = ?
            WHERE username = ?
        ");

        $stmt->bind_param("sss", $password, $nama, $username);
        $stmt->execute();

        echo "✓ Akun diperbarui: <b>$username</b><br>";

        $stmt->close();

    } else {

        $stmt = $conn->prepare("
            INSERT INTO users (username, password, nama_lengkap)
            VALUES (?, ?, ?)
        ");

        $stmt->bind_param("sss", $username, $password, $nama);
        $stmt->execute();

        echo "✓ Akun dibuat: <b>$username</b><br>";

        $stmt->close();
    }

    $cek->close();
}

echo "<br><b>Semua akun selesai diproses.</b><br>";
echo '<a href="login.php">Kembali ke Login</a>';

$conn->close();
?>