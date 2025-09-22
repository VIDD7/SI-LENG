<?php
session_start();

// Pastikan hanya user yg udah login yang bisa akses
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// memastikan diakses via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemId = $_POST['item_id'];
    $userId = $_SESSION['user_id'];

    $itemDir = 'data/items/' . $itemId;
    $detailPath = $itemDir . '/detail.json';
    $bidsPath = $itemDir . '/bids.json';
    $descPath = $itemDir . '/description.md';

    // Verifikasi ulang di sisi server
    if (file_exists($detailPath)) {
        $details = json_decode(file_get_contents($detailPath), true);
        $bids = json_decode(file_get_contents($bidsPath), true);

        // Cek apakah user ini adalah pemilik DAN belum ada tawaran
        // variabel role admin
        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

        // Cek Kondisi Penghapusan
        $canDelete = false;
        // Kondisi 1: Dihapus oleh pemilik, dan belum ada tawaran atau bid
        if ($details['owner_id'] === $userId && empty($bids)) {
            $canDelete = true;
        }
        // Kondisi 2: Dihapus oleh admin, dan lelang sudah berakhir
        if ($isAdmin && $details['status'] === 'ended') {
            $canDelete = true;
        }

        if ($canDelete) {
            // --- PROSES PENGHAPUSAN ---

            // 1. Hapus file gambar di folder uploads/
            if (file_exists($details['image_path'])) {
                unlink($details['image_path']);
            }

            // 2. Hapus file-file di dalam folder item
            unlink($detailPath);
            unlink($bidsPath);
            unlink($descPath);

            // 3. Hapus folder item itu sendiri
            rmdir($itemDir);

            // 4. Arahkan kembali ke halaman utama dengan pesan sukses
            header('Location: index.php?status=hapus_sukses');
            exit();

        } else {
            die("Error: Anda tidak punya hak untuk menghapus lelang ini atau lelang sudah memiliki tawaran.");
        }
    } else {
        die("Error: Barang lelang tidak ditemukan.");
    }
} else {
    header('Location: index.php');
    exit();
}
?>