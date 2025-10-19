<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = trim($_POST['title']);
  $description = trim($_POST['description']);
  $open_price = trim($_POST['open_price']); 
  $end_at = $_POST['end_at'];
  $owner_id = $_SESSION['user_id'];

  if (empty($title) || empty($description) || empty($end_at)) {
    die("Err, Semua kolom harus diisi yaa!.");
  }
  //proses upload gambar
  //variabel $_POST itu buat nampung data teks, angka, dll.
  //variabel $_FILES itu khusus buat nampung data file yang di-upload.
  if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
    $target_dir = "uploads/";
    //ambil ekstensi file asli dan ubah namanya jadi huruf kecil
    $imageFileType = strtolower(pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION));
    //buat nama file unik gabungan antara waktu sama nama ekstensi file
    $unique_filename = uniqid() . '-' . time() . '-' . $imageFileType;
    $target_file = $target_dir . $unique_filename;

    //Pindahkan file dari lokasi sementara yg di simpen sama php ke lokasi folder permanen (folder uploads/)
    if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
      //simpen target_file ke variabel $image_path
      $image_path = $target_file;
    } else {
      die('Maaf, terjadi error saat mengakses file gambar anda.');
    }
  } else {
    die('Maaf, file yg diunggah tidak boleh ada error.');
  }
  //buat struktur data sama nama file
  $itemId = 'item_' . time(); //buat id unik untuk barang
  $itemDir = 'data/items/' . $itemId;

  //buat direktori baru untuk item
  if (!mkdir($itemDir, 0777, true)) {
    die('Gagal membuat direktori untuk item baru.');
  }
  //buat file deskripsi
  file_put_contents($itemDir . '/description.md', $description);
  //siapin data apa aja untuk detail.json
  $details = [
    'id'=> $itemId,
    'owner_id'=> $owner_id,
    'title' => $title,
    'open_price' => (int)$open_price,
    'current_price' => (int)$open_price,
    'highest_bidder_id' => null,
    'image_path' => $image_path,
    'created_at' => date('c'),
    'end_at' => date('c', strtotime($end_at)),
    'status' => 'active' //status lelang
  ];
  //buat/tulis file untuk detail.json
  file_put_contents($itemDir . '/detail.json', json_encode($details, JSON_PRETTY_PRINT));
  //buat file bids.json kosong untuk item
  file_put_contents($itemDir . '/bids.json', '[]');
  //arahin ke detail barang klo udah sukses
  header('Location: item.php?id=' . $itemId);
  exit();

} else {
  //klo file di akses langsung
  header('Location: index.php');
  exit();
}
?>