// =========================================================
// APP.JS - Logika Utama
// =========================================================

const API_BASE = 'api';

// State global
let allProducers = [];
let allDistricts = [];

// =========================================================
// INIT
// =========================================================
document.addEventListener('DOMContentLoaded', async () => {
    await loadStats();
    await loadDistricts();
    await loadProducers();

    setupEventListeners();
});

// =========================================================
// LOAD STATISTIK
// =========================================================
async function loadStats() {
    try {
        const res = await fetch(`${API_BASE}/stats.php`);
        const json = await res.json();

        if (json.status === 'success') {
            document.getElementById('stat-petani').textContent    = json.data.total_petani;
            document.getElementById('stat-umkm').textContent      = json.data.total_umkm;
            document.getElementById('stat-produk').textContent    = json.data.total_produk;
            document.getElementById('stat-kecamatan').textContent = json.data.total_kecamatan;
        }
    } catch (err) {
        console.error('Gagal load stats:', err);
    }
}

// =========================================================
// LOAD DISTRICTS (untuk dropdown filter)
// =========================================================
async function loadDistricts() {
    try {
        const res = await fetch(`${API_BASE}/districts.php`);
        const json = await res.json();

        if (json.status === 'success') {
            allDistricts = json.data;

            const select = document.getElementById('filter-district');
            json.data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id;
                opt.textContent = `${d.district_name} (${d.total_producers})`;
                select.appendChild(opt);
            });
        }
    } catch (err) {
        console.error('Gagal load districts:', err);
    }
}

// =========================================================
// LOAD PRODUCERS
// =========================================================
async function loadProducers(params = {}) {
    try {
        const query = new URLSearchParams(params).toString();
        const url   = query ? `${API_BASE}/producers.php?${query}` : `${API_BASE}/producers.php`;

        const res = await fetch(url);
        const json = await res.json();

        if (json.status === 'success') {
            allProducers = json.data;
            renderProducers(json.data);
            renderMapMarkers(json.data);
        }
    } catch (err) {
        console.error('Gagal load producers:', err);
    }
}

// =========================================================
// RENDER CARD PRODUCERS
// =========================================================
function renderProducers(data) {
    const container = document.getElementById('producers-container');
    container.innerHTML = '';

    if (data.length === 0) {
        container.innerHTML = '<p style="text-align:center;color:#8d6e63;">Tidak ada data ditemukan.</p>';
        return;
    }

    data.forEach(p => {
        const waLink = p.whatsapp ? `https://wa.me/62${p.whatsapp.replace(/^0/, '')}` : '#';
        const igLink = p.instagram ? `https://instagram.com/${p.instagram.replace('@', '')}` : null;

        const card = document.createElement('div');
        card.className = 'producer-card';
        card.innerHTML = `
            <div class="card-header">
                <h3>${p.name}</h3>
                <span class="badge">${p.type}</span>
            </div>
            <div class="card-body">
                <p><strong>${p.owner_name || '-'}</strong></p>
                <p class="address">📍 ${p.address || p.district_name}</p>
                ${p.description ? `<p>${p.description}</p>` : ''}
            </div>
            <div class="card-footer">
                ${p.whatsapp ? `<a href="${waLink}" target="_blank" class="wa">WhatsApp</a>` : ''}
                ${igLink ? `<a href="${igLink}" target="_blank">Instagram</a>` : ''}
                <a href="#" onclick="focusMarker(${p.id});return false;">Lihat di Peta</a>
            </div>
        `;
        container.appendChild(card);
    });
}

// =========================================================
// EVENT LISTENERS
// =========================================================
function setupEventListeners() {
    const searchInput = document.getElementById('search-input');
    const filterDistrict = document.getElementById('filter-district');
    const filterType = document.getElementById('filter-type');
    const btnReset = document.getElementById('btn-reset');

    // Debounce untuk search
    let timer;
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => applyFilters(), 400);
    });

    filterDistrict.addEventListener('change', applyFilters);
    filterType.addEventListener('change', applyFilters);

    btnReset.addEventListener('click', () => {
        searchInput.value = '';
        filterDistrict.value = '';
        filterType.value = '';
        applyFilters();
    });
}

// =========================================================
// APPLY FILTERS
// =========================================================
function applyFilters() {
    const params = {};

    const search   = document.getElementById('search-input').value.trim();
    const district = document.getElementById('filter-district').value;
    const type     = document.getElementById('filter-type').value;

    if (search)   params.search      = search;
    if (district) params.district_id = district;
    if (type)     params.type        = type;

    loadProducers(params);
}