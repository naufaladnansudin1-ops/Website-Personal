<?php
require_once 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
$id = filter_input(INPUT_POST, 'rumah_makan_id', FILTER_VALIDATE_INT);
$name = trim($_POST['nama_pengunjung'] ?? '');
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$comment = trim($_POST['komentar'] ?? '');
if (!$id || $name === '' || !$rating || $rating < 1 || $rating > 5 || $comment === '' || mb_strlen($comment) > 1000) {
    die('Data ulasan tidak valid. <a href="index.php">Kembali</a>');
}
$check = $conn->prepare("SELECT id FROM rumah_makan WHERE id=?");
$check->bind_param("i", $id); $check->execute();
if (!$check->get_result()->num_rows) die('Rumah makan tidak ditemukan.');
$stmt = $conn->prepare("INSERT INTO ulasan (rumah_makan_id,nama_pengunjung,rating,komentar) VALUES (?,?,?,?)");
$stmt->bind_param("isis", $id, $name, $rating, $comment);
if ($stmt->execute()) { header('Location: index.php?review=success'); exit; }
die('Gagal menyimpan ulasan.');
?>
