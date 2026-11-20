<?php
require_once 'koneksi.php';
$rows = [];
$sql = "SELECT r.*,
        (SELECT ROUND(AVG(u.rating),1) FROM ulasan u WHERE u.rumah_makan_id=r.id) AS rating,
        (SELECT COUNT(*) FROM ulasan u WHERE u.rumah_makan_id=r.id) AS jumlah_ulasan
        FROM rumah_makan r ORDER BY r.nama";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) $rows[] = $row;
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Jelajah Kuliner Tataaran | WebGIS</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="user-page">
<header class="topbar">
  <a class="brand" href="index.php"><span class="brand-icon">🍽</span><span>Jelajah<span class="brand-light">Kuliner</span><small>TATAARAN • WEBGIS</small></span></a>
  <nav><a class="nav-active" href="index.php">Peta Kuliner</a><a href="admin/login.php">Admin</a></nav>
</header>
<main class="user-layout">
  <aside class="side-panel">
    <div class="eyebrow">TEMUKAN RASA FAVORITMU</div>
    <h1>Jelajahi rumah makan <span>di Tataaran.</span></h1>
    <p class="muted">Temukan tempat makan, lihat ulasan pengunjung, dan kunjungi lokasi pilihanmu.</p>
    <label class="search-box"><span>⌕</span><input id="searchInput" type="search" placeholder="Cari nama rumah makan..."></label>
    <div class="filter-row"><strong id="placeCount"><?= count($rows) ?> tempat ditemukan</strong><span class="map-tag">OpenStreetMap</span></div>
    <div id="restaurantList" class="restaurant-list">
    <?php foreach ($rows as $r): ?>
      <button class="place-item" type="button" data-id="<?= (int)$r['id'] ?>" data-name="<?= e($r['nama']) ?>">
        <div class="place-thumb"><?php if ($r['foto'] && file_exists(__DIR__.'/assets/uploads/'.$r['foto'])): ?>
          <img src="assets/uploads/<?= e($r['foto']) ?>" alt="<?= e($r['nama']) ?>">
        <?php else: ?><span>🍜</span><?php endif; ?></div>
        <div class="place-info"><strong><?= e($r['nama']) ?></strong><span><?= e($r['kategori']) ?></span>
          <small>★ <?= $r['rating'] ? e($r['rating']) : 'Belum ada rating' ?> · <?= (int)$r['jumlah_ulasan'] ?> ulasan</small></div>
        <span class="chevron">›</span>
      </button>
    <?php endforeach; ?>
    <?php if (!$rows): ?><div class="empty-state">Belum ada data rumah makan. Admin dapat menambahkannya melalui dashboard.</div><?php endif; ?>
    </div>
    <div class="panel-foot"><span class="dot"></span> Peta interaktif • Data dapat diperbarui admin</div>
  </aside>
  <section class="map-wrap">
    <div id="map"></div>
    <div class="map-note"><span class="map-note-icon">📍</span><div><strong>Lokasi rumah makan</strong><small>Klik marker untuk melihat detail</small></div></div>
  </section>
</main>
<div id="detailModal" class="modal hidden">
  <div class="modal-backdrop" data-close></div>
  <article class="detail-card">
    <button class="modal-close" data-close aria-label="Tutup">×</button>
    <div id="detailContent"><div class="loading">Memuat detail...</div></div>
  </article>
</div>
<script>window.RESTAURANTS = <?= json_encode($rows, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/js/map.js"></script>
</body></html>
