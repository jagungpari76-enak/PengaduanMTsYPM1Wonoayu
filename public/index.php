<?php
require_once '../config/database.php';

$stmtKategori = $conn->prepare("SELECT id, nama_kategori FROM kategori ORDER BY id");
$stmtKategori->execute();
$kategoriList = $stmtKategori->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Pengaduan Sekolah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f0f2f1;
            margin: 0;
            padding: 24px;
            color: #1f2937;
            position: relative;
            overflow-x: hidden;
        }
        body::before, body::after {
            content: ""; position: fixed; border-radius: 50%; background: #1f4d3c;
            box-shadow: -14px 14px 0 rgba(0,0,0,0.08); z-index: 0; pointer-events: none;
        }
        body::before { width: 240px; height: 240px; top: -100px; right: -80px; }
        body::after {
            width: 200px; height: 200px; bottom: -100px; left: -70px;
            box-shadow: 14px -14px 0 rgba(0,0,0,0.08);
        }
        .container { max-width: 520px; margin: 0 auto; position: relative; z-index: 1; }
        .top { text-align: center; margin-bottom: 24px; }
        .top .logo { width: 56px; height: 56px; object-fit: contain; margin-bottom: 8px; }
        .top .nama-sekolah {
            font-size: 15px; font-weight: 700; color: #1f4d3c; letter-spacing: 1px;
            text-transform: uppercase; margin-bottom: 2px;
        }
        .top h1 { font-size: 20px; font-weight: 700; color: #14342a; margin: 0 0 4px; }
        .top p { font-size: 13.5px; color: #6b7280; margin: 0; }
        .cek-link { text-align: center; margin-bottom: 18px; }
        .cek-link a {
            font-size: 13px; font-weight: 600; color: #fff; background: #1f4d3c;
            text-decoration: none; padding: 8px 16px; border: none; border-radius: 999px;
            display: inline-block;
        }
        .cek-link a:hover { background: #17392c; }
        .card {
            background: #fff; border-radius: 10px; padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        }
        label { display: block; font-size: 13.5px; font-weight: 600; color: #374151; margin-top: 16px; margin-bottom: 6px; }
        label:first-child { margin-top: 0; }
        input[type=text], input[type=tel], select, textarea {
            width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;
            font-family: inherit; font-size: 14px; background: #fafafa;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #1f4d3c; background: #fff; }
        textarea { resize: vertical; min-height: 90px; }
        .hint { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        button {
            width: 100%; margin-top: 22px; padding: 12px; background: #1f4d3c; color: #fff;
            border: none; border-radius: 999px; cursor: pointer; font-size: 15px; font-weight: 600;
            letter-spacing: 0.5px; font-family: inherit;
        }
        button:hover { background: #17392c; }
        .error { background: #fef2f2; color: #b91c1c; padding: 10px 14px; border-radius: 6px; font-size: 13.5px; margin-bottom: 16px; }
        .foto-opsi { display: flex; align-items: stretch; gap: 10px; width: 100%; box-sizing: border-box; }
        .foto-btn {
            flex: 1 1 0; min-width: 0; display: flex; align-items: center; justify-content: center; gap: 6px;
            text-align: center; padding: 0 6px; margin: 0; border: 1px solid #d1d5db; border-radius: 6px;
            background: #fafafa; font-size: 13px; font-weight: 600; color: #374151; cursor: pointer;
            line-height: 1.2; vertical-align: middle;
            transition: background 0.15s, border-color 0.15s;
            height: 44px; white-space: nowrap; overflow: hidden; box-sizing: border-box;
        }
        .foto-btn:hover { background: #f0f2f1; border-color: #1f4d3c; }
        #nama_file_terpilih { margin-top: 8px; margin-bottom: 4px; }
        #nama_file_terpilih.terisi { color: #14342a; font-size: 14px; font-weight: 600; }

        /* Area kamera & pratinjau foto — dirapikan, tidak lagi selebar layar */
        #area_kamera {
            margin-top: 14px; padding: 14px; background: #f7f8f7;
            border: 1px solid #e3e6e4; border-radius: 10px;
        }
        #area_kamera .kamera-box {
            width: 220px; max-width: 100%; margin: 0 auto;
            border-radius: 8px; overflow: hidden; background: #000;
        }
        #preview_kamera, #hasil_foto {
            display: block; width: 100%; height: auto; max-height: 220px;
            object-fit: cover; border-radius: 8px;
        }
        #area_kamera .tombol-kamera {
            display: flex; gap: 8px; margin-top: 10px;
        }
        #area_kamera .tombol-kamera button {
            margin-top: 0; width: auto; flex: 1; padding: 9px 10px; font-size: 13px;
            border-radius: 8px; letter-spacing: 0;
        }
        #area_kamera .tombol-kamera button.batal {
            background: #fff; color: #374151; border: 1px solid #d1d5db;
        }
        #area_kamera .tombol-kamera button.batal:hover { background: #f0f2f1; }
    </style>

<script>
    let streamKamera = null;

    function tampilkanNamaFoto(input) {
        const info = document.getElementById('nama_file_terpilih');

        if (input.files && input.files[0]) {
            info.textContent = 'Foto dipilih: ' + input.files[0].name;
            info.classList.add('terisi');
        } else {
            info.textContent = 'Belum ada foto dipilih.';
            info.classList.remove('terisi');
        }
    }

    async function bukaKamera() {
        const video = document.getElementById('preview_kamera');
        const area = document.getElementById('area_kamera');

        try {
            streamKamera =
                await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                });

            video.srcObject = streamKamera;
            area.style.display = 'block';
            video.style.display = 'block';
            document.getElementById('hasil_foto').style.display = 'none';
            await video.play();
        } catch (error) {
            alert('Kamera gagal dibuka. Periksa izin kamera browser.');
            console.error(error);
        }
    }

    function ambilFoto() {
        const video = document.getElementById('preview_kamera');
        const canvas = document.getElementById('hasil_foto');
        const input = document.getElementById('foto_galeri');

        if (!streamKamera || !video.videoWidth) {
            alert('Tunggu sampai kamera siap.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        canvas.getContext('2d').drawImage(
            video, 0, 0, canvas.width, canvas.height
        );

        canvas.toBlob(function(blob) {
            if (!blob) {
                alert('Gagal mengambil foto.');
                return;
            }

            const file = new File(
                [blob],
                'foto_pengaduan_' + Date.now() + '.jpg',
                { type: 'image/jpeg' }
            );

            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;

            document.getElementById('nama_file_terpilih').textContent =
                'Foto dipilih: ' + file.name;

            document.getElementById('nama_file_terpilih')
                .classList.add('terisi');

            video.style.display = 'none';
            canvas.style.display = 'block';

            streamKamera.getTracks().forEach(track => track.stop());
            streamKamera = null;
        }, 'image/jpeg', 0.9);
    }

    function tutupKamera() {
        if (streamKamera) {
            streamKamera.getTracks().forEach(track => track.stop());
            streamKamera = null;
        }

        document.getElementById('area_kamera').style.display = 'none';

        const video = document.getElementById('preview_kamera');
        video.style.display = 'block';
        video.srcObject = null;

        const canvas = document.getElementById('hasil_foto');
        canvas.style.display = 'none';
    }
</script>

</head>
<body>
<div class="container">
    <div class="top">
        <img class="logo" src="../assets/logo-sekolah.png" alt="Logo MTs YPM 1 Wonoayu" onerror="this.style.display='none'">
        <div class="nama-sekolah">MTs YPM 1 Wonoayu</div>
        <h1>Sistem Pengaduan Sekolah</h1>
        <p>Sampaikan pengaduan atau keluhan Anda kepada pihak sekolah</p>
    </div>

    <div class="cek-link">
        <a href="cek_status.php">Sudah pernah lapor? Cek status di sini</a>
    </div>

    <div class="card">
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

       <form action="submit.php" method="POST" enctype="multipart/form-data">

    <label for="nama_pelapor">Nama Lengkap</label>
    <input type="text" id="nama_pelapor" name="nama_pelapor" required
           placeholder="Masukkan nama lengkap">

    <label for="kelas">Kelas</label>
    <select id="kelas" name="kelas" required>
        <option value="">-- Pilih Kelas --</option>
        <option value="7">Kelas 7 / VII</option>
        <option value="8">Kelas 8 / VIII</option>
        <option value="9">Kelas 9 / IX</option>
    </select>

    <label for="lokasi_kejadian">Lokasi Kejadian</label>
    <input type="text" id="lokasi_kejadian" name="lokasi_kejadian" required
           placeholder="Contoh: Ruang kelas 8 / Lapangan sekolah">

    <label for="kategori_id">Kategori Pengaduan</label>
    <select id="kategori_id" name="kategori_id" required>
        <option value="">-- Pilih Kategori --</option>

        <?php while ($kat = $kategoriList->fetch_assoc()): ?>
            <option value="<?= $kat['id'] ?>">
                <?= htmlspecialchars($kat['nama_kategori']) ?>
            </option>
        <?php endwhile; ?>
    </select>

            <label for="deskripsi">Deskripsi Pengaduan</label>
            <textarea id="deskripsi" name="deskripsi" required placeholder="Jelaskan keluhan Anda secara detail..."></textarea>

            <label for="foto">Foto Bukti (opsional)</label>

            <div class="foto-opsi">
                <label class="foto-btn" for="foto_galeri">
                    📁 Pilih dari Galeri
                </label>

                <button
                    type="button"
                    class="foto-btn"
                    onclick="bukaKamera()"
                >
                    📷 Ambil Foto
                </button>
            </div>

            <input
                type="file"
                id="foto_galeri"
                name="foto"
                accept="image/jpeg,image/png"
                style="display:none;"
                onchange="tampilkanNamaFoto(this)"
            >

            <div id="area_kamera" style="display:none;">
                <div class="kamera-box">
                    <video id="preview_kamera" autoplay playsinline></video>
                    <canvas id="hasil_foto" style="display:none;"></canvas>
                </div>

                <div class="tombol-kamera">
                    <button type="button" onclick="ambilFoto()">
                        📸 Jepret Foto
                    </button>
                    <button type="button" class="batal" onclick="tutupKamera()">
                        Batal
                    </button>
                </div>
            </div>

            <div class="hint" id="nama_file_terpilih">
                Belum ada foto dipilih.
            </div>
            <div class="hint">Format JPG/PNG, maksimal 50MB.</div>

            <button type="submit">Kirim Pengaduan</button>
        </form>
    </div>
</div>
</body>
</html>
<?php $stmtKategori->close(); ?>