<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Kopi Pekalongan</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <h1>☕ Admin Kopi Pekalongan</h1>
            <p class="login-subtitle">Masuk untuk mengelola data</p>

            <form id="login-form">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email" value="admin@kopipekalongan.id" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" value="admin123" required>
                </div>
                <button type="submit" class="btn-primary">Masuk</button>
                <div id="login-message" class="message"></div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = document.getElementById('login-message');
        msg.textContent = '';
        msg.className = 'message';

        const email    = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const res = await fetch('../api/login.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({email, password})
            });
            const json = await res.json();

            if (json.status === 'success') {
                msg.textContent = 'Login berhasil, mengalihkan...';
                msg.className = 'message success';
                setTimeout(() => window.location.href = 'index.php', 800);
            } else {
                msg.textContent = json.message;
                msg.className = 'message error';
            }
        } catch (err) {
            msg.textContent = 'Terjadi kesalahan';
            msg.className = 'message error';
        }
    });
    </script>
</body>
</html>