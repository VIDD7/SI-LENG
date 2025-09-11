<?php
session_start();

// Cek apakah pengguna sudah login, jika iya, arahkan ke halaman utama
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="image/png" href="assets/css/titlelogo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="wrapper">
        <h1>Login</h1>

        <?php
        // Tampilkan pesan sukses jika baru saja berhasil mendaftar
        if (isset($_GET['status']) && $_GET['status'] == 'sukses_daftar') {
            echo '<p class="pesan-sukses">Pendaftaran berhasil! Silakan login.</p>';
        }

        // Tampilkan pesan error jika login gagal
        if (isset($_GET['error'])) {
            echo '<p class="pesan-error">Username atau password salah.</p>';
        }
        ?>

        <form action="proses_login.php" method="POST">
            <div class="form-group">
                <input type="text" placeholder="Username"  id="username" name="username" required>
            </div>

            <div class="form-group">
                <input type="password" placeholder="Password" id="password" name="password" required>
            </div>
            <div class="link">
            <button type="submit" class="btnl">Login</button>
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </form>
    </div>

</body>
</html>