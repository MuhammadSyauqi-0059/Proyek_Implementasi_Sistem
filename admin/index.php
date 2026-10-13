<?php
require_once '../api/config/auth.php';
requireLogin();
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Admin Kopi Pekalongan</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>☕ Admin</h2>
            </div>
            <nav>
                <a href="index.php" class="active">Dashboard</a>
                <a href="producers.php">Petani & UMKM</a>
                <a href="../index.html" target="_blank">Lihat Website</a>
            </nav>
            <div class="sidebar-footer">
                <p><?= htmlspecialchars($user['name']) ?></p>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <header class="content-header">
                <h1>Dashboard</h1>
                <p>Selamat datang, <?= htmlspecialchars($user['name']) ?></p>
            </header>

            <section class="stats-grid" id="stats-grid">
                <div class="stat-card">
                    <span class="stat-number" id="stat-petani">-</span>
                    <span class="stat-label">Petani</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number" id="stat-umkm">-</span>
                    <span class="stat-label">UMKM</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number" id="stat-produk">-</span>
                    <span class="stat-label">Produk</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number" id="stat-kecamatan">-</span>
                    <span class="stat-label">Kecamatan</span>
                </div>
            </section>

            <section class="quick-actions">
                <h2>Aksi Cepat</h2>
                <a href="producer_form.php" class="btn-primary">+ Tambah Petani/UMKM</a>
                <a href="producers.php" class="btn-secondary">Kelola Data</a>
            </section>
        </main>
    </div>

    <script>
    fetch('../api/stats.php')
        .then(r => r.json())
        .then(json => {
            if (json.status === 'success') {
                document.getElementById('stat-petani').textContent    = json.data.total_petani;
                document.getElementById('stat-umkm').textContent      = json.data.total_umkm;
                document.getElementById('stat-produk').textContent    = json.data.total_produk;
                document.getElementById('stat-kecamatan').textContent = json.data.total_kecamatan;
            }
        });
    </script>
</body>
</html>