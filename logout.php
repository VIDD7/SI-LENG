<?php
// panggil session dulu 
session_start();

// Kosongkan semua data session
session_unset();

// Hancurkan session-nya itu sendiri
session_destroy();

// Arahkan pengguna kembali ke halaman utama
header('Location: index.php');
exit();
?>