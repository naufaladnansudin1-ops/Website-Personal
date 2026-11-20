<?php
require_once 'koneksi.php';
header('Content-Type: application/json; charset=utf-8');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID tidak valid']); exit; }
$stmt = $conn->prepare("SELECT r.*, (SELECT ROUND(AVG(rating),1) FROM ulasan WHERE rumah_makan_id=r.id) rating,
                       (SELECT COUNT(*) FROM ulasan WHERE rumah_makan_id=r.id) jumlah_ulasan
                       FROM rumah_makan r WHERE r.id=?");
$stmt->bind_param("i", $id); $stmt->execute();
$restaurant = $stmt->get_result()->fetch_assoc();
if (!$restaurant) { http_response_code(404); echo json_encode(['error'=>'Rumah makan tidak ditemukan']); exit; }
$stmt2 = $conn->prepare("SELECT nama_pengunjung,rating,komentar,created_at FROM ulasan WHERE rumah_makan_id=? ORDER BY created_at DESC");
$stmt2->bind_param("i", $id); $stmt2->execute();
$reviews = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
echo json_encode(['restaurant'=>$restaurant,'reviews'=>$reviews], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>
