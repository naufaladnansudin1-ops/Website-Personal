<?php
require_once 'auth.php'; require_once '../koneksi.php';
$total = (int)$conn->query("SELECT COUNT(*) n FROM rumah_makan")->fetch_assoc()['n'];
$reviews = (int)$conn->query("SELECT COUNT(*) n FROM ulasan")->fetch_assoc()['n'];
$avg = $conn->query("SELECT ROUND(AVG(rating),1) n FROM ulasan")->fetch_assoc()['n'] ?? 0;
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head><body class="admin-page">
<aside class="admin-sidebar"><a class="brand" href="../index.php"><span class="brand-icon">🍽</span><span>Jelajah<span class="brand-light">Kuliner</span><small>ADMIN PANEL</small></span></a><div class="admin-nav"><a class="active" href="dashboard.php">▦ Dashboard</a><a href="rumah_makan.php">⌖ Data Rumah Makan</a><a href="ulasan.php">☆ Ulasan</a></div><a class="logout-link" href="logout.php">Keluar ↗</a></aside>
<main class="admin-main"><header class="admin-header"><div><div class="eyebrow">RINGKASAN SISTEM</div><h1>Dashboard Admin</h1><p class="muted">Selamat datang, <?= e($_SESSION['admin_username']) ?>. Berikut ringkasan WebGIS kuliner.</p></div><a class="primary-button" href="tambah.php">+ Tambah Rumah Makan</a></header>
<div class="stats-grid"><div class="stat-card"><span class="stat-icon">🍜</span><small>Total Rumah Makan</small><strong><?= $total ?></strong><span class="stat-note">Data lokasi terdaftar</span></div><div class="stat-card"><span class="stat-icon">☆</span><small>Total Ulasan</small><strong><?= $reviews ?></strong><span class="stat-note">Masukan pengunjung</span></div><div class="stat-card"><span class="stat-icon">★</span><small>Rating Rata-rata</small><strong><?= $avg ? e($avg) : '—' ?></strong><span class="stat-note">Dari seluruh ulasan</span></div></div>
<section class="admin-content-card"><div class="section-heading"><div><h2>Kelola data rumah makan</h2><p class="muted">Tambah informasi dan atur posisi marker di peta.</p></div><a href="rumah_makan.php" class="text-link">Lihat semua →</a></div>
<div class="admin-shortcuts"><a href="tambah.php"><span>＋</span><strong>Tambah lokasi</strong><small>Input data dan koordinat peta</small></a><a href="rumah_makan.php"><span>⌖</span><strong>Kelola lokasi</strong><small>Edit data dan pindahkan marker</small></a><a href="ulasan.php"><span>☆</span><strong>Moderasi ulasan</strong><small>Periksa dan hapus ulasan</small></a></div></section>
</main></body></html>
