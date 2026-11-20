<?php
require_once 'auth.php';
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: rumah_makan.php');
    exit;
}

function val($key) { return trim($_POST[$key] ?? ''); }
function fail_upload($message) {
    http_response_code(400);
    echo '<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Gagal menyimpan</title><body style="font-family:Arial,sans-serif;background:#f4f0e7;color:#172b4d;padding:40px">';
    echo '<div style="max-width:650px;margin:auto;background:white;padding:28px;border-radius:16px;border:1px solid #e5d7bd">';
    echo '<h2>Data belum berhasil disimpan</h2><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p>Periksa ukuran/format gambar atau coba lagi dengan gambar JPG/PNG/WEBP yang lebih kecil dari 3 MB.</p>';
    echo '<a style="display:inline-block;background:#172b4d;color:white;padding:11px 16px;border-radius:8px;text-decoration:none" href="javascript:history.back()">Kembali ke formulir</a></div></body></html>';
    exit;
}

$action = $_POST['action'] ?? '';
$nama = val('nama');
$alamat = val('alamat');
$kategori = val('kategori');
$deskripsi = val('deskripsi');
$jam_buka = val('jam_buka') ?: '08:00';
$jam_tutup = val('jam_tutup') ?: '21:00';
$lat = filter_var(val('latitude'), FILTER_VALIDATE_FLOAT);
$lng = filter_var(val('longitude'), FILTER_VALIDATE_FLOAT);

if (!$nama || !$alamat || $lat === false || $lng === false || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    fail_upload('Data wajib belum lengkap atau koordinat tidak valid.');
}

$oldFoto = basename(val('old_foto'));
$foto = $oldFoto !== '' ? $oldFoto : null;
$newFotoPath = null;
$uploadDir = dirname(__DIR__) . '/assets/uploads';

// Buat folder upload otomatis jika belum ada.
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
    fail_upload('Folder penyimpanan foto tidak dapat dibuat. Pastikan folder proyek memiliki izin tulis.');
}
if (!is_writable($uploadDir)) {
    fail_upload('Folder assets/uploads tidak dapat ditulisi. Periksa izin folder pada XAMPP.');
}

if (isset($_FILES['foto']) && is_array($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadError = (int)$_FILES['foto']['error'];
    if ($uploadError !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Ukuran gambar melebihi batas upload_max_filesize pada PHP.',
            UPLOAD_ERR_FORM_SIZE => 'Ukuran gambar melebihi batas formulir.',
            UPLOAD_ERR_PARTIAL => 'Gambar hanya terunggah sebagian. Coba unggah kembali.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara upload PHP tidak tersedia.',
            UPLOAD_ERR_CANT_WRITE => 'PHP gagal menulis file upload ke disk.',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh konfigurasi ekstensi PHP.'
        ];
        fail_upload($messages[$uploadError] ?? 'Terjadi kesalahan saat mengunggah gambar (kode ' . $uploadError . ').');
    }
    if (!is_uploaded_file($_FILES['foto']['tmp_name'])) {
        fail_upload('File gambar tidak dikenali sebagai file upload yang valid.');
    }
    if ((int)$_FILES['foto']['size'] > 3 * 1024 * 1024) {
        fail_upload('Ukuran gambar lebih dari 3 MB. Kompres gambar lalu coba lagi.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['foto']['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        fail_upload('Format gambar tidak didukung. Gunakan JPG, PNG, atau WEBP.');
    }
    $foto = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $newFotoPath = $uploadDir . '/' . $foto;
    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $newFotoPath)) {
        fail_upload('Gambar gagal dipindahkan ke assets/uploads. Pastikan folder tersebut bisa ditulisi.');
    }
}

if ($action === 'create') {
    $stmt = $conn->prepare('INSERT INTO rumah_makan (nama, alamat, kategori, deskripsi, jam_buka, jam_tutup, latitude, longitude, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if (!$stmt) { if ($newFotoPath) @unlink($newFotoPath); fail_upload('Query database gagal disiapkan. Periksa struktur tabel rumah_makan.'); }
    $stmt->bind_param('ssssssdds', $nama, $alamat, $kategori, $deskripsi, $jam_buka, $jam_tutup, $lat, $lng, $foto);
} elseif ($action === 'update') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) { if ($newFotoPath) @unlink($newFotoPath); fail_upload('ID rumah makan tidak valid.'); }
    $stmt = $conn->prepare('UPDATE rumah_makan SET nama=?, alamat=?, kategori=?, deskripsi=?, jam_buka=?, jam_tutup=?, latitude=?, longitude=?, foto=? WHERE id=?');
    if (!$stmt) { if ($newFotoPath) @unlink($newFotoPath); fail_upload('Query database gagal disiapkan. Periksa struktur tabel rumah_makan.'); }
    $stmt->bind_param('ssssssddsi', $nama, $alamat, $kategori, $deskripsi, $jam_buka, $jam_tutup, $lat, $lng, $foto, $id);
} else {
    if ($newFotoPath) @unlink($newFotoPath);
    fail_upload('Aksi penyimpanan tidak dikenal.');
}

if ($stmt->execute()) {
    // Hapus foto lama hanya setelah data baru berhasil disimpan.
    if ($newFotoPath && $oldFoto !== '' && $oldFoto !== $foto) {
        $oldPath = $uploadDir . '/' . basename($oldFoto);
        if (is_file($oldPath)) @unlink($oldPath);
    }
    header('Location: rumah_makan.php?status=success');
    exit;
}

if ($newFotoPath && is_file($newFotoPath)) @unlink($newFotoPath);
fail_upload('Database menolak penyimpanan: ' . $stmt->error);
?>
