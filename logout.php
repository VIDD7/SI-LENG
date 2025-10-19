<?php
// seperti biasa function session harus di panggil dulu karena menggunakan $_session
session_start();
// kosongin semua data session
session_destroy();
// hapus/hancurkan sessionnya
session_unset();
//timpa variabel super global session jadi array kosong
$_SESSION = [];
header("Location: index.php");
exit();
?>