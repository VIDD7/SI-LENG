<?php
session_start();

//oke temen2 jadi ini file otak dari login.php
//jadi file ini yang bakal ngecek username dan passwordnya bener atau salah

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi dasar
    if (empty($username) || empty($password)) {
        // Jika ada yang kosong, arahkan ke login.php dengan pesan error
        header('Location: login.php?error=1');
        exit();
    }

    // Ini untuk menentukan lokasi penyimpanan user yaitu di file users.json
    $file_path = 'data/users.json';

    // Baca data pengguna
    $users = [];
    if (file_exists($file_path)) {
        $json_data = file_get_contents($file_path);
        $users = json_decode($json_data, true);
    }

    // Cari pengguna berdasarkan username
    $user_ditemukan = false;
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            // Jika username ditemukan, sekarang verifikasi passwordnya
            // Ini adalah 'jodoh'-nya password_hash()
            if (password_verify($password, $user['password_hash'])) {
                
                // --- LOGIN BERHASIL! ---
                $user_ditemukan = true;

                // Simpan 'tanda pengenal' pengguna ke dalam session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Arahkan ke halaman utama (index.php)
                header('Location: index.php');
                exit();
            }
        }
    }

    // Jika setelah dicek semua pengguna dan tidak ada yang cocok
    // (baik username atau passwordnya)
    if (!$user_ditemukan) {
        // arahkan ke halaman login dengan pesan error
        header('Location: login.php?error=1');
        exit();
    }

} else {
    // Jika file diakses langsung, arahkan ke halaman utama
    header('Location: index.php');
    exit();
}
?>