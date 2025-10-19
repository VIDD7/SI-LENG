<?php
session_start();

// 'Penjaga' halaman. Jika pengguna belum login, arahkan ke halaman login.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<?php 
require 'includes/header.php'; 
?>

<main>
    <div class="form-page-header">
        <a href="index.php" class="back-link">Home</a>
        <h1>Mulai Lelang Barang Baru</h1>
        <p>Isi detail barang yang akan Anda lelang di bawah ini.</p>
    </div>

    <form action="proses_lelang_baru.php" method="POST" enctype="multipart/form-data" class="new-auction-form">
        <div class="form-column">
            <div class="form-group">
                <label for="title">Judul Barang:</label>
                <input type="text" id="title" name="title" required placeholder="Contoh: Vespa Sprint 150">
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Barang:</label>
                <textarea id="description" name="description" rows="8" required placeholder="Jelaskan kondisi barang, kelengkapan, dll. Anda bisa menggunakan format Markdown."></textarea>
            </div>
        </div>

        <div class="form-column">
            <div class="form-group">
                <label for="open_price">Harga Pembukaan (Rp):</label>
                <input type="number" id="open_price" name="open_price" required placeholder="Contoh: 50000000">
            </div>

            <div class="form-group">
                <label for="end_at">Waktu Selesai Lelang:</label>
                <input type="datetime-local" id="end_at" name="end_at" required>
            </div>

            <div class="form-group">
                <label for="item_image">Foto Barang Utama:</label>
                <input type="file" id="item_image" name="item_image" accept="image/*" required>
            </div>

            <button type="submit" class="btn">Publikasikan Lelang</button>
        </div>
    </form>
</main>

<?php 
require 'includes/footer.php'; 
?>