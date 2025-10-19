<?php
session_start();

if ($_SERVER["REQUEST_METHOD"]== "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if (empty($username) || empty($password)) {
    header ('Location: login.php?error=1');
    exit();
  }
  $file_path = 'data/users.json';
  $users = [];
  if (file_exists($file_path)) {
    $json_data = file_get_contents($file_path);
    $users = json_decode($json_data, true);
  }
  $user_ditemukan = false;
  foreach ($users as $user) {
    if ($user['username'] === $username) {
      if (password_verify($password, $user['password_hash'])) {
        $user_ditemukan = true;
        //simpen semua data user ke session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit();
      }
    }
  }
  if (!$user_ditemukan) {
    header('Location: login.php?error=1');
    exit();
  }
} else {
  //kalo di akses langsung
  header('Location: index.php');
  exit();
}
?>