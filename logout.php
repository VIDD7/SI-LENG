<?php
// seperti biasa function session harus di panggil dulu karena menggunakan $_session
session_start();

// Kosongkan semua data session
session_unset();

// Hancurkan/hapus sessionnya
session_destroy();

// TIMPA DENGAN ARRAY KOSONG UNTUK MASTIIN AJA BIAR BERSIH SESSIONNYA
$_SESSION = [];

// Arahkan user kembali ke halaman utama
header('Location: index.php');
exit();
?>