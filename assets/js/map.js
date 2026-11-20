const defaultCenter = [1.2875, 124.85]; // pusat peta awal Tataaran/Tondano; sesuaikan bila diperlukan
const map = L.map('map', {zoomControl: true}).setView(defaultCenter, 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19, attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

const markers = new Map();
let userLocation = null;
let userLocationMarker = null;
let userAccuracyCircle = null;
const markerIcon = L.divIcon({className:'food-marker-wrap', html:'<div class="food-marker">🍴</div>', iconSize:[38,42], iconAnchor:[19,40]});

function directionsUrl(restaurant) {
  const destination = `${Number(restaurant.latitude)},${Number(restaurant.longitude)}`;
  // OSM route expects origin;destination (lat,lon pairs). If GPS is not available,
  // the link still opens the destination for the user to choose their starting point.
  const route = userLocation
    ? `${userLocation.lat},${userLocation.lng};${destination}`
    : destination;
  return `https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=${encodeURIComponent(route)}`;
}

function showDetail(id) {
  const modal = document.getElementById('detailModal');
  const content = document.getElementById('detailContent');
  modal.classList.remove('hidden');
  content.innerHTML = '<div class="loading">Memuat detail rumah makan...</div>';
  fetch('detail.php?id=' + encodeURIComponent(id))
    .then(r => r.json()).then(data => {
      if (data.error) throw new Error(data.error);
      const r = data.restaurant, reviews = data.reviews || [];
      const photo = r.foto ? `<img class="detail-photo" src="assets/uploads/${escapeHtml(r.foto)}" alt="${escapeHtml(r.nama)}">`
                           : `<div class="detail-photo placeholder-photo">🍜<span>Foto belum tersedia</span></div>`;
      const rating = r.rating ? `${r.rating} / 5` : 'Belum ada rating';
      const reviewHtml = reviews.length ? reviews.map(v => `<div class="review"><div class="review-head"><strong>${escapeHtml(v.nama_pengunjung)}</strong><span>${'★'.repeat(Number(v.rating))}${'☆'.repeat(5-Number(v.rating))}</span></div><p>${escapeHtml(v.komentar)}</p><small>${escapeHtml(v.created_at)}</small></div>`).join('') : '<p class="muted">Belum ada ulasan untuk tempat ini.</p>';
      content.innerHTML = `${photo}<div class="detail-body"><div class="eyebrow">${escapeHtml(r.kategori || 'Rumah Makan')}</div><h2>${escapeHtml(r.nama)}</h2>
        <div class="rating-line"><span class="stars">★</span><strong>${rating}</strong><span class="muted">(${r.jumlah_ulasan} ulasan)</span></div>
        <p class="detail-address">📍 ${escapeHtml(r.alamat)}</p><p>${escapeHtml(r.deskripsi || 'Belum ada deskripsi.')}</p>
        <div class="hours"><span>Jam operasional</span><strong>${escapeHtml((r.jam_buka || '').slice(0,5))} – ${escapeHtml((r.jam_tutup || '').slice(0,5))}</strong></div>
        <a class="direction-button" data-restaurant-id="${Number(r.id)}" target="_blank" rel="noopener" href="${directionsUrl(r)}">Petunjuk arah dari lokasi saya ↗</a>
        <section class="reviews-section"><h3>Ulasan pengunjung</h3>${reviewHtml}</section>
        <section class="review-form"><h3>Bagikan pengalamanmu</h3><form method="post" action="ulasan_simpan.php">
          <input type="hidden" name="rumah_makan_id" value="${Number(r.id)}">
          <label>Nama<input name="nama_pengunjung" maxlength="100" required placeholder="Nama kamu"></label>
          <label>Rating<select name="rating" required><option value="">Pilih rating</option><option value="5">5 - Sangat baik</option><option value="4">4 - Baik</option><option value="3">3 - Cukup</option><option value="2">2 - Kurang</option><option value="1">1 - Buruk</option></select></label>
          <label>Ulasan<textarea name="komentar" required maxlength="1000" rows="3" placeholder="Ceritakan pengalamanmu..."></textarea></label>
          <button class="primary-button" type="submit">Kirim ulasan</button></form></section></div>`;
    }).catch(err => content.innerHTML = `<div class="error-state">${escapeHtml(err.message)}</div>`);
}
function escapeHtml(v) { return String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

(window.RESTAURANTS || []).forEach(r => {
  const lat = Number(r.latitude), lng = Number(r.longitude);
  if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
  const marker = L.marker([lat,lng], {icon:markerIcon}).addTo(map);
  marker.bindTooltip(escapeHtml(r.nama), {direction:'top'});
  marker.on('click', () => showDetail(r.id));
  markers.set(String(r.id), marker);
});

// Kontrol untuk menampilkan atau menonaktifkan seluruh titik rumah makan.
let markersEnabled = true;
const markerToggleControl = L.control({position: 'topright'});
markerToggleControl.onAdd = function () {
  const container = L.DomUtil.create('div', 'leaflet-bar marker-toggle-control');
  const button = L.DomUtil.create('button', '', container);
  button.type = 'button';
  button.setAttribute('aria-pressed', 'true');
  button.innerHTML = '<span class="toggle-indicator"></span><span class="toggle-label">Nonaktifkan titik</span>';
  L.DomEvent.disableClickPropagation(container);
  L.DomEvent.on(button, 'click', () => {
    markersEnabled = !markersEnabled;
    markers.forEach(marker => {
      if (markersEnabled) {
        if (!map.hasLayer(marker)) marker.addTo(map);
      } else if (map.hasLayer(marker)) {
        map.removeLayer(marker);
      }
    });
    button.setAttribute('aria-pressed', String(markersEnabled));
    button.querySelector('.toggle-label').textContent = markersEnabled ? 'Nonaktifkan titik' : 'Aktifkan titik';
    button.title = markersEnabled ? 'Sembunyikan semua titik rumah makan' : 'Tampilkan semua titik rumah makan';
  });
  return container;
};
markerToggleControl.addTo(map);
document.querySelectorAll('.place-item').forEach(button => button.addEventListener('click', () => {
  const marker = markers.get(button.dataset.id);
  if (marker) { map.flyTo(marker.getLatLng(), 17); marker.openTooltip(); }
  showDetail(button.dataset.id);
}));
document.getElementById('searchInput').addEventListener('input', e => {
  const q = e.target.value.toLowerCase().trim(); let visible = 0;
  document.querySelectorAll('.place-item').forEach(item => {
    const show = item.dataset.name.toLowerCase().includes(q);
    item.style.display = show ? '' : 'none'; if (show) visible++;
  });
  document.getElementById('placeCount').textContent = `${visible} tempat ditemukan`;
});
document.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', () => document.getElementById('detailModal').classList.add('hidden')));
document.addEventListener('keydown', e => { if (e.key === 'Escape') document.getElementById('detailModal').classList.add('hidden'); });


// GPS pengguna: browser akan meminta izin lokasi. Hanya koordinat yang dipakai
// pada halaman ini; lokasi tidak dikirim atau disimpan ke server.
const locateControl = L.control({position: 'topright'});
locateControl.onAdd = function () {
  const div = L.DomUtil.create('div', 'leaflet-bar user-locate-control');
  const button = L.DomUtil.create('button', '', div);
  button.type = 'button';
  button.title = 'Tampilkan lokasi saya';
  button.setAttribute('aria-label', 'Tampilkan lokasi saya');
  button.innerHTML = '◎';
  L.DomEvent.disableClickPropagation(div);
  L.DomEvent.on(button, 'click', locateUser);
  return div;
};
locateControl.addTo(map);

function locateUser() {
  if (!('geolocation' in navigator)) {
    alert('Browser ini tidak mendukung fitur lokasi.');
    return;
  }
  map.getContainer().classList.add('locating');
  navigator.geolocation.getCurrentPosition(position => {
    map.getContainer().classList.remove('locating');
    userLocation = {
      lat: position.coords.latitude,
      lng: position.coords.longitude
    };
    const point = [userLocation.lat, userLocation.lng];
    if (userLocationMarker) map.removeLayer(userLocationMarker);
    if (userAccuracyCircle) map.removeLayer(userAccuracyCircle);
    userLocationMarker = L.circleMarker(point, {
      radius: 9, color: '#fff', weight: 3, fillColor: '#2678e8', fillOpacity: 1
    }).addTo(map).bindTooltip('Lokasi saya', {direction: 'top'});
    userAccuracyCircle = L.circle(point, {
      radius: Math.max( position.coords.accuracy || 30, 25),
      color: '#2678e8', weight: 1, fillColor: '#2678e8', fillOpacity: 0.10
    }).addTo(map);
    map.flyTo(point, Math.max(map.getZoom(), 16));
    // Update an already-open restaurant detail so its directions use the GPS origin.
    const currentLink = document.querySelector('#detailContent .direction-button');
    if (currentLink) {
      const id = currentLink.dataset.restaurantId;
      const restaurant = (window.RESTAURANTS || []).find(item => String(item.id) === String(id));
      if (restaurant) currentLink.href = directionsUrl(restaurant);
    }
  }, error => {
    map.getContainer().classList.remove('locating');
    const message = error.code === 1
      ? 'Izin lokasi ditolak. Izinkan akses lokasi pada pengaturan browser untuk menggunakan fitur ini.'
      : error.code === 2
        ? 'Lokasi belum dapat ditentukan. Pastikan GPS/lokasi perangkat aktif.'
        : 'Permintaan lokasi melewati batas waktu. Coba lagi.';
    alert(message);
  }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 30000 });
}

// Minta izin dan tampilkan titik pengguna saat halaman dibuka. Jika izin ditolak,
// peta dan pencarian rumah makan tetap dapat digunakan seperti biasa.
if ('geolocation' in navigator) locateUser();
