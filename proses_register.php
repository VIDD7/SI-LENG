<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $phone = trim($_POST['phone']);

  //validasi awal dulu biar ga kirim data kosong
  if(empty($username) || empty($password) || empty($email)) {
    die("Err, semua kolom wajib diisi ya!");
  }
  $file_path = 'data/users.json';

  $users = [];
  if (file_exists($file_path)) {
    $json_data = file_get_contents($file_path);
    $users = json_decode($json_data, true);
  }
  //untuk cek user nya udh ada atau belum
  foreach ($users as $user) {
    if ($user['username'] === $username) {
      die("Err, Username udah digunakan.");
    }
    if ($user['email'] === $email) {
      die("Err, Email sudah digunakan.");
    }
  }
  $password_hash = password_hash($password, PASSWORD_DEFAULT);
  $newUser = [
    'id' => 'user_' . time(), //artinya tuh buat id unik berdasarkan waktu/time
    'username' => $username,
    'email' => $email,
    'phone'=> $phone,
    'password_hash' => $password_hash,
    'role' => 'user',
  ];
  // tambahin user baru tadi ke dalam variabel users
  $users[] = $newUser;
  file_put_contents($file_path, json_encode($users, JSON_PRETTY_PRINT)); // JSON_PRETTY_PRINT itu biar data yg di simpan di array rapi

  header('Location: login.php?status=sukses_daftar');
  exit();
} else {
  header('Location: index.php');
  exit();
}
?>