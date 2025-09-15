<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Ambil data dari form dan bersihkan dari spasi kosong
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; //
    $phone = trim($_POST['phone']);

    //Validasi dasar, pastikan tidak ada yang kosong
    if (empty($username) || empty($email) || empty($password)) {
        die("Error: Semua kolom wajib diisi.");
    }

    //variabel path ke file users.json
    $file_path = 'data/users.json';

    //Baca data pengguna yang ada. Jika file belum ada, mulai dengan array kosong.
    $users = [];
    if (file_exists($file_path)) {
        $json_data = file_get_contents($file_path);
        $users = json_decode($json_data, true);
    }

    //Cek apakah username atau email sudah terdaftar
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            die("Error: Username sudah digunakan. Silakan pilih yang lain.");
        }
        if ($user['email'] === $email) {
            die("Error: Email sudah terdaftar. Silakan gunakan email lain.");
        }
    }

    // Hash password untuk keamanan
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Langkah 7: Buat data pengguna baru dalam format array
    $newUser = [
        'id' => 'user_' . time(), // Buat ID unik berdasarkan waktu
        'username' => $username,
        'email' => $email,
        'phone' => $phone,
        'password_hash' => $password_hash,
        'role' => 'user',
    ];

    // Langkah 8: Tambahkan pengguna baru ke dalam array $users
    $users[] = $newUser;

    // Langkah 9: Tulis kembali semua data ke file users.json
    // JSON_PRETTY_PRINT agar format JSON di file rapi dan mudah dibaca
    file_put_contents($file_path, json_encode($users, JSON_PRETTY_PRINT));

    // Langkah 10: Arahkan pengguna ke halaman login setelah berhasil
    // Nanti kita akan buat halaman login.php
    header('Location: login.php?status=sukses_daftar');
    exit();

} else {
    // Jika file diakses langsung tanpa metode POST, pindahkan ke halaman utama
    header('Location: index.php');
    exit();
}
?>