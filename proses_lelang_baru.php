<?php
session_start();

// 'Penjaga' halaman. Pastikan hanya pengguna yang sudah login yang bisa mengakses.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Pastikan proses ini diakses via metode POST dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // TANGKAP & VALIDASI DATA TEKS
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $open_price = trim($_POST['open_price']);
    $end_at = $_POST['end_at'];
    $owner_id = $_SESSION['user_id']; // Ambil ID pemilik dari session

    if (empty($title) || empty($description) || empty($open_price) || empty($end_at)) {
        die("Error: Semua kolom teks wajib diisi.");
    }
    
    // PROSES UPLOAD GAMBAR
    // $_POST itu buat nampung data teks, angka, dll.
    // $_FILES itu khusus buat nampung data file yang di-upload.
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $target_dir = "uploads/";
        
        // Buat nama file yang unik untuk mencegah file dengan nama sama saling menimpa
        // Caranya: gabungkan ID unik berdasarkan waktu + nama file asli
        $imageFileType = strtolower(pathinfo($_FILES["item_image"]["name"], PATHINFO_EXTENSION));
        $unique_filename = uniqid() . '-' . time() . '.' . $imageFileType;
        $target_file = $target_dir . $unique_filename;

        // Pindahkan file dari lokasi sementara ke lokasi permanen (folder uploads/)
        if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
            // Jika upload gambar berhasil, lanjut proses data
            $image_path = $target_file;
        } else {
            die("Maaf, terjadi error saat mengupload file gambar Anda.");
        }
    } else {
        die("Error: File gambar wajib diunggah dan tidak boleh ada error.");
    }

    // BUAT STRUKTUR DATA & FILE
    $itemId = 'item_' . time(); // Buat ID unik untuk barang ini
    $itemDir = 'data/items/' . $itemId;

    // Buat direktori baru untuk item ini
    if (!mkdir($itemDir, 0777, true)) {
        die('Gagal membuat direktori untuk item baru.');
    }
    
    // Tulis file deskripsi (description.md)
    file_put_contents($itemDir . '/description.md', $description);

    // Siapkan data untuk detail.json
    $details = [
        'id' => $itemId,
        'owner_id' => $owner_id,
        'title' => $title,
        'open_price' => (int)$open_price,
        'current_price' => (int)$open_price,
        'highest_bidder_id' => null,
        'image_path' => $image_path,
        'created_at' => date('c'), // Format waktu ISO 8601
        'end_at' => date('c', strtotime($end_at)),
        'status' => 'active' // Status lelang: active, ended, sold
    ];

    // c. Tulis file detail (detail.json)
    file_put_contents($itemDir . '/detail.json', json_encode($details, JSON_PRETTY_PRINT));
    
    // d. Buat file bids.json kosong untuk item ini
    file_put_contents($itemDir . '/bids.json', '[]');


    // --- 4. REDIRECT SETELAH SUKSES ---
    // Arahkan pengguna ke halaman detail barang yang baru saja dibuat
    header('Location: item.php?id=' . $itemId);
    exit();

} else {
    // Jika file diakses langsung, arahkan ke halaman utama
    header('Location: index.php');
    exit();
}
?>