<?php
require_once '../api/config/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data - Admin Kopi Pekalongan</title>
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
                <h1>Petani & UMKM</h1>
                <a href="producer_form.php" class="btn-primary">+ Tambah Data</a>
            </header>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Kecamatan</th>
                        <th>Kontak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr><td colspan="6" style="text-align:center;">Memuat data...</td></tr>
                </tbody>
            </table>
        </main>
    </div>

    <script>
    async function loadData() {
        const res = await fetch('../api/producers.php');
        const json = await res.json();
        const tbody = document.getElementById('table-body');
        tbody.innerHTML = '';

        if (json.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Belum ada data</td></tr>';
            return;
        }

        json.data.forEach((p, i) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${i + 1}</td>
                <td>${p.name}</td>
                <td><span class="badge badge-${p.type}">${p.type}</span></td>
                <td>${p.district_name}</td>
                <td>${p.whatsapp || '-'}</td>
                <td>
                    <a href="producer_form.php?id=${p.id}" class="btn-sm btn-edit">Edit</a>
                    <button onclick="hapusData(${p.id})" class="btn-sm btn-delete">Hapus</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    async function hapusData(id) {
        if (!confirm('Yakin ingin menghapus data ini?')) return;
        const res = await fetch('../api/producer_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        const json = await res.json();
        alert(json.message);
        loadData();
    }

    loadData();
    </script>
</body>
</html>