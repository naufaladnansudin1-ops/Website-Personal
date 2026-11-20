<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<div class="coordinate-help">Klik peta untuk menentukan lokasi. Marker dapat digeser; latitude dan longitude akan terisi otomatis.</div>
<div id="adminMap" class="admin-map"></div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
 const latInput=document.getElementById('latitude'), lngInput=document.getElementById('longitude');
 let lat=parseFloat(latInput.value)||1.2875, lng=parseFloat(lngInput.value)||124.85;
 const map=L.map('adminMap').setView([lat,lng],14);
 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap'}).addTo(map);
 const marker=L.marker([lat,lng],{draggable:true}).addTo(map);
 function update(p){latInput.value=p.lat.toFixed(7);lngInput.value=p.lng.toFixed(7);}
 map.on('click',e=>{marker.setLatLng(e.latlng);update(e.latlng);});
 marker.on('dragend',e=>update(e.target.getLatLng()));
 latInput.addEventListener('change',()=>{const p=[parseFloat(latInput.value)||lat,parseFloat(lngInput.value)||lng];marker.setLatLng(p);map.panTo(p);});
 lngInput.addEventListener('change',()=>{const p=[parseFloat(latInput.value)||lat,parseFloat(lngInput.value)||lng];marker.setLatLng(p);map.panTo(p);});
 update(marker.getLatLng());
})();
</script>
