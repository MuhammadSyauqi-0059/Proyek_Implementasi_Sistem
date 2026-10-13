<?php
require_once '../api/config/auth.php';
requireLogin();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$isEdit = $id > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Edit' : 'Tambah' ?> Data - Admin Kopi Pekalongan</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header"><h2>☕ Admin</h2></div>
            <nav>
                <a href="index.php">Dashboard</a>
                <a href="producers.php" class="active">Petani & UMKM</a>
                <a href="../index.html" target="_blank">Lihat Website</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <h1><?= $isEdit ? 'Edit' : 'Tambah' ?> Petani/UMKM</h1>
                <a href="producers.php" class="btn-secondary">← Kembali</a>
            </header>

            <form id="producer-form" class="form-card">
                <input type="hidden" id="id" value="<?= $id ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Usaha/Petani *</label>
                        <input type="text" id="name" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe *</label>
                        <select id="type" required>
                            <option value="petani">Petani</option>
                            <option value="umkm">UMKM</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Pemilik</label>
                        <input type="text" id="owner_name">
                    </div>
                    <div class="form-group">
                        <label>Kecamatan *</label>
                        <select id="district_id" required>
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea id="description" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea id="address" rows="2"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Latitude *</label>
                        <input type="text" id="latitude" placeholder="-7.12345678" required>
                    </div>
                    <div class="form-group">
                        <label>Longitude *</label>
                        <input type="text" id="longitude" placeholder="109.76543210" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" id="phone">
                    </div>
                    <div class="form-group">
                        <label>WhatsApp</label>
                        <input type="text" id="whatsapp" placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Instagram</label>
                        <input type="text" id="instagram" placeholder="@username">
                    </div>
                    <div class="form-group">
                        <label>Foto</label>
                        <input type="file" id="photo_file" accept="image/*">
                        <small id="photo-info"></small>
                        <input type="hidden" id="photo">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select id="status">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="producers.php" class="btn-secondary">Batal</a>
                </div>

                <div id="form-message" class="message"></div>
            </form>
        </main>
    </div>

    <script>
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    const producerId = <?= $id ?>;

    // Load districts
    async function loadDistricts() {
        const res = await fetch('../api/districts.php');
        const json = await res.json();
        const sel = document.getElementById('district_id');
        json.data.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id;
            opt.textContent = d.district_name;
            sel.appendChild(opt);
        });
    }

    // Load producer jika edit
    async function loadProducer() {
        if (!isEdit) return;
        const res = await fetch(`../api/producers.php`);
        const json = await res.json();
        const p = json.data.find(x => x.id == producerId);
        if (!p) return;

        document.getElementById('name').value        = p.name;
        document.getElementById('type').value        = p.type;
        document.getElementById('owner_name').value  = p.owner_name || '';
        document.getElementById('description').value = p.description || '';
        document.getElementById('address').value     = p.address || '';
        document.getElementById('latitude').value    = p.latitude;
        document.getElementById('longitude').value   = p.longitude;
        document.getElementById('phone').value       = p.phone || '';
        document.getElementById('whatsapp').value    = p.whatsapp || '';
        document.getElementById('instagram').value   = p.instagram || '';
        document.getElementById('photo').value       = p.photo || '';
        document.getElementById('status').value      = p.status;

        if (p.photo) {
            document.getElementById('photo-info').textContent = 'Foto: ' + p.photo;
        }

        // Set district
        setTimeout(() => {
            document.getElementById('district_id').value = p.district_id || '';
        }, 300);
    }

    // Upload foto
    document.getElementById('photo_file').addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const fd = new FormData();
        fd.append('photo', file);

        const res = await fetch('../api/upload.php', { method: 'POST', body: fd });
        const json = await res.json();

        if (json.status === 'success') {
            document.getElementById('photo').value = json.filename;
            document.getElementById('photo-info').textContent = 'Berhasil upload: ' + json.filename;
        } else {
            alert('Upload gagal: ' + json.message);
        }
    });

    // Submit form
    document.getElementById('producer-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = document.getElementById('form-message');
        msg.textContent = '';

        const data = {
            id:           document.getElementById('id').value,
            name:         document.getElementById('name').value,
            type:         document.getElementById('type').value,
            owner_name:   document.getElementById('owner_name').value,
            district_id:  document.getElementById('district_id').value,
            description:  document.getElementById('description').value,
            address:      document.getElementById('address').value,
            latitude:     document.getElementById('latitude').value,
            longitude:    document.getElementById('longitude').value,
            phone:        document.getElementById('phone').value,
            whatsapp:     document.getElementById('whatsapp').value,
            instagram:    document.getElementById('instagram').value,
            photo:        document.getElementById('photo').value,
            status:       document.getElementById('status').value
        };

        const url = isEdit ? '../api/producer_update.php' : '../api/producer_create.php';

        const res = await fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        const json = await res.json();

        if (json.status === 'success') {
            msg.textContent = json.message;
            msg.className = 'message success';
            setTimeout(() => window.location.href = 'producers.php', 1000);
        } else {
            msg.textContent = json.message;
            msg.className = 'message error';
        }
    });

    loadDistricts().then(loadProducer);
    </script>
</body>
</html>