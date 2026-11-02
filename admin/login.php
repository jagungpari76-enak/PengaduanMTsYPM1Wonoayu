<?php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {

        $error = 'Username dan password wajib diisi.';

    } else {

        $stmt = $conn->prepare("
            SELECT id, username, password, nama_lengkap
            FROM users
            WHERE username = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            /*
             * Password di database disimpan dalam bentuk hash
             * (lihat admin/buat_akun.php), jadi dibandingkan
             * dengan password_verify, bukan dengan ===.
             */
            if (password_verify($password, $user['password'])) {

                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

                header("Location: dashboard.php");
                exit;

            } else {

                $error = 'Username atau password salah.';

            }

        } else {

            $error = 'Username atau password salah.';

        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Admin - Pengaduan Sekolah MTs</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        :root {
            --warna: #2d6a4f;
            --warna-gelap: #245c43;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 20px;

            position: relative;
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";

            position: fixed;

            border-radius: 50%;

            background: var(--warna);

            box-shadow:
                -14px 14px 0 rgba(0,0,0,0.08);

            z-index: 0;
        }

        body::before {
            width: 280px;
            height: 280px;

            top: -110px;
            right: -90px;
        }

        body::after {
            width: 240px;
            height: 240px;

            bottom: -120px;
            left: -80px;

            box-shadow:
                14px -14px 0 rgba(0,0,0,0.08);
        }

        .box {
            position: relative;
            z-index: 1;

            background: #fff;

            padding: 32px 28px;

            border-radius: 16px;

            box-shadow:
                0 8px 28px rgba(0,0,0,0.12);

            width: 340px;
            max-width: 100%;
        }

        .brand {
            text-align: center;
            margin-bottom: 22px;
        }

        .brand img {
            width: 80px;
            height: 80px;

            object-fit: contain;

            margin-bottom: 8px;
        }

        .brand .nama-sekolah {
            font-size: 18px;
            font-weight: 700;

            color: var(--warna);

            letter-spacing: 1px;

            text-transform: uppercase;

            line-height: 1.3;
        }

        .brand .sub {
            font-size: 13px;

            color: #777;

            margin-top: 4px;
        }

        h2 {
            margin: 0 0 4px;

            font-size: 20px;

            letter-spacing: 1px;
        }

        label {
            display: block;

            margin-top: 14px;

            font-size: 13px;

            color: #444;
        }

        input[type=text],
        input[type=password] {

            width: 100%;

            padding: 11px 14px;

            margin-top: 5px;

            border: 1px solid #ddd;

            border-radius: 10px;

            background: #fafafa;

            font-family: inherit;

            font-size: 14px;
        }

        input:focus {
            outline: none;

            border-color: var(--warna);

            background: #fff;
        }

        button {

            width: 100%;

            margin-top: 22px;

            padding: 12px;

            background: var(--warna);

            color: #fff;

            border: none;

            border-radius: 999px;

            cursor: pointer;

            font-family: inherit;

            font-size: 15px;

            font-weight: 600;

            letter-spacing: 1px;
        }

        button:hover {
            background: var(--warna-gelap);
        }

        .error {

            color: #c0392b;

            font-size: 13px;

            margin-top: 12px;

            text-align: center;
        }

    </style>

</head>

<body>

<div class="box">

    <div class="brand">

        <img
            src="../assets/logo-sekolah.png"
            alt="Logo MTs YPM 1 Wonoayu"
            onerror="this.style.display='none'"
        >

        <div class="nama-sekolah">
            MTs YPM 1 Wonoayu
        </div>

        <div class="sub">
            Sistem Pengaduan Sekolah
        </div>

    </div>

    <h2>SIGN IN</h2>

    <form method="POST">

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            placeholder="Masukkan username"
            required
            autofocus
        >

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Masukkan password"
            required
        >

        <button type="submit">
            MASUK
        </button>

        <?php if ($error !== ''): ?>

            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>

        <?php endif; ?>

    </form>

</div>

</body>

</html>