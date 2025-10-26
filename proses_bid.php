<?php
session_start();

// 'Penjaga' halaman. jadi fungsinya memastikan hanya pengguna yang sudah login yang bisa menawar.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // TANGKAP & VALIDASI DATA
    $itemId = $_POST['item_id'];
    $bidAmount = (int)$_POST['bid_amount'];
    $bidderId = $_SESSION['user_id'];

    if (empty($itemId) || empty($bidAmount) || !is_numeric($bidAmount)) {
        die("Error: Data tawaran tidak valid.");
    }

    $detailPath = 'data/items/' . $itemId . '/detail.json';
    $bidsPath = 'data/items/' . $itemId . '/bids.json';

    // Pastikan file detail item ada
    if (!file_exists($detailPath)) {
        die("Error: Item lelang tidak ditemukan.");
    }
    
    // KUNCI FILE UNTUK PROSES KRITIS 
    // pakai fopen dengan mode r+. Artinya kita buka file untuk 'Baca dan Tulis'.
    $detailHandle = fopen($detailPath, 'r+');

    if (!$detailHandle) {
        die("Error: Gagal membuka file data.");
    }

    // Mereka akan antrii menunggu giliran.
    if (flock($detailHandle, LOCK_EX)) {
        
        $detailsJson = stream_get_contents($detailHandle);
        $details = json_decode($detailsJson, true);

        // Cek apakah lelang masih aktif dan tawaran lebih tinggi
        if ($details['status'] === 'active' && $bidAmount > $details['current_price']) {
            
            // Update data di array $details
            $details['current_price'] = $bidAmount;
            $details['highest_bidder_id'] = $bidderId;

            // Tulis ulang file detail.json yang sudah diupdate
            // buat 'ngosongin' file sebelum ditulis ulang
            ftruncate($detailHandle, 0);
            rewind($detailHandle);
            fwrite($detailHandle, json_encode($details, JSON_PRETTY_PRINT));
            fflush($detailHandle); // Memastikan data langsung tertulis ke disk

            // Catat tawaran baru ke bids.json
            $bids = json_decode(file_get_contents($bidsPath), true);
            $newBid = [
                'bidder_id' => $bidderId,
                'amount' => $bidAmount,
                'timestamp' => date('c')
            ];
            $bids[] = $newBid;
            file_put_contents($bidsPath, json_encode($bids, JSON_PRETTY_PRINT));

        } else {
            // Jika tawaran tidak lebih tinggi atau lelang sudah berakhir
            // tidak melakukan apa-apa, cukup lepaskan kunci dan redirect.
        }

        // melepas kunci
        flock($detailHandle, LOCK_UN);

    } else {
        echo "Sistem sedang sibuk, gagal mendapatkan kunci file. Coba lagi.";
    }

    // Tutup file handle setelah selesai
    fclose($detailHandle);

    // Arahkan pengguna kembali ke halaman detail barang
    header('Location: item.php?id=' . $itemId);
    exit();

} else {
    // ini tuh pengaman jika file diakses langsung, arahkan ke halaman utama
    header('Location: index.php');
    exit();
}
?>