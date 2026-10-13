// =========================================================
// MAP.JS - Inisialisasi & Render Peta Leaflet
// =========================================================

let map;
let markersCluster;
const markerRefs = {}; // simpan referensi marker per producer id

// =========================================================
// INIT MAP
// =========================================================
function initMap() {
    // Pusat peta: Kabupaten Pekalongan
    map = L.map('map').setView([-7.05, 109.70], 11);

    // Tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker cluster
    markersCluster = L.markerClusterGroup();
    map.addLayer(markersCluster);
}

// =========================================================
// CUSTOM ICON
// =========================================================
function createIcon(type) {
    const color = type === 'petani' ? '#2e7d32' : '#c62828';

    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="
            background:${color};
            width:28px;
            height:28px;
            border-radius:50% 50% 50% 0;
            transform:rotate(-45deg);
            border:3px solid white;
            box-shadow:0 2px 6px rgba(0,0,0,0.3);
        "></div>`,
        iconSize: [28, 28],
        iconAnchor: [14, 28],
        popupAnchor: [0, -28]
    });
}

// =========================================================
// RENDER MARKERS DI PETA
// =========================================================
function renderMapMarkers(producers) {
    markersCluster.clearLayers();
    Object.keys(markerRefs).forEach(k => delete markerRefs[k]);

    producers.forEach(p => {
        if (!p.latitude || !p.longitude) return;

        const lat = parseFloat(p.latitude);
        const lng = parseFloat(p.longitude);

        const marker = L.marker([lat, lng], {
            icon: createIcon(p.type)
        });

        const waLink = p.whatsapp
            ? `https://wa.me/62${p.whatsapp.replace(/^0/, '')}`
            : null;

        const popupContent = `
            <div style="min-width:200px;">
                <h3 style="margin:0 0 6px;color:#3e2723;font-size:1rem;">${p.name}</h3>
                <p style="margin:0 0 4px;font-size:0.85rem;color:#6d4c41;">
                    <strong>Tipe:</strong> ${p.type.toUpperCase()}
                </p>
                <p style="margin:0 0 4px;font-size:0.85rem;color:#6d4c41;">
                    <strong>Pemilik:</strong> ${p.owner_name || '-'}
                </p>
                <p style="margin:0 0 8px;font-size:0.85rem;color:#6d4c41;">
                    📍 ${p.district_name}, ${p.regency_name}
                </p>
                ${waLink ? `<a href="${waLink}" target="_blank" style="
                    display:inline-block;
                    padding:6px 12px;
                    background:#25d366;
                    color:white;
                    text-decoration:none;
                    border-radius:6px;
                    font-size:0.8rem;
                ">Hubungi via WhatsApp</a>` : ''}
            </div>
        `;

        marker.bindPopup(popupContent);
        markersCluster.addLayer(marker);
        markerRefs[p.id] = marker;
    });
}

// =========================================================
// FOCUS KE MARKER TERTENTU (dari card)
// =========================================================
function focusMarker(producerId) {
    const marker = markerRefs[producerId];
    if (!marker) return;

    const latlng = marker.getLatLng();
    map.setView(latlng, 15);
    marker.openPopup();

    // Scroll ke peta
    document.getElementById('map').scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}

// =========================================================
// INIT SAAT PAGE READY
// =========================================================
document.addEventListener('DOMContentLoaded', initMap);