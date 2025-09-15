<?php
session_start();

// 'penerjemah' Markdown
require 'Parsedown.php';

// TANGKAP DAN VALIDASI ID BARANG
// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    die("Error: ID barang tidak ditemukan.");
}

$itemId = $_GET['id'];
$itemDir = 'data/items/' . $itemId;

// Cek apakah direktori (dan barangnya) benar-benar ada
if (!is_dir($itemDir)) {
    die("Error: Barang lelang dengan ID ini tidak ditemukan.");
}

//PEMERIKSA WAKTU OTOMATIS & UPDATE STATUS

$detailPath = $itemDir . '/detail.json';

// Baca dulu data detail untuk mendapatkan waktu selesai dan status
$preDetailsJson = file_get_contents($itemDir . '/detail.json');
$details = json_decode($preDetailsJson, true);

$now = new DateTime(); // Waktu server saat ini
$endTime = new DateTime($details['end_at']); // Waktu selesai lelang

// fungsi Cek apakah waktu sekarang sudah melewati waktu selesai DAN statusnya masih 'active'
if ($now > $endTime && $details['status'] === 'active') {
    // Jika ya, update statusnya menjadi 'ended'
    // file locking agar proses update aman
    $detailHandle = fopen($detailPath, 'r+');
    if ($detailHandle && flock($detailHandle, LOCK_EX)) {
        
        $details['status'] = 'ended'; // Ubah status di array
        
        ftruncate($detailHandle, 0);
        rewind($detailHandle);
        fwrite($detailHandle, json_encode($details, JSON_PRETTY_PRINT));
        fflush($detailHandle);
        
        flock($detailHandle, LOCK_UN);
        fclose($detailHandle);
    }
}

//BACA SEMUA DATA BARANG
$detailsJson = file_get_contents($itemDir . '/detail.json');
$details = json_decode($detailsJson, true);

$descriptionMd = file_get_contents($itemDir . '/description.md');

// Ubah deskripsi dari Markdown ke HTML
$parsedown = new Parsedown();
$descriptionHtml = $parsedown->text($descriptionMd);

//AMBIL DATA UNTUK RIWAYAT PENAWARAN

// Buat "kamus" user ID -> username
$usersJson = file_get_contents('data/users.json');
$users = json_decode($usersJson, true);
$userMap = [];
foreach ($users as $user) {
    $userMap[$user['id']] = $user['username'];
}
// Ambil data pemilik barang (penjual)
$ownerId = $details['owner_id'];
$seller = null; // variabel kosong
foreach($users as $user){
    if($user['id'] === $ownerId){
        $seller = $user;
        break; // Hentikan loop jika penjual sudah ditemukan
    }
}

// Baca data penawaran dari bids.json
$bidsJson = file_get_contents($itemDir . '/bids.json');
$bids = json_decode($bidsJson, true);

// Urutkan penawaran dari yang paling tinggi (terbaru) ke terendah
if (!empty($bids)) {
    usort($bids, function($a, $b) {
        return $b['amount'] - $a['amount'];
    });
}
?>

<?php 
require 'includes/header.php'; 
?>

<main class="item-page-container">
    <a href="index.php" class="back-link">&larr; Kembali ke Daftar Lelang</a>

    <div class="item-grid">
        <div class="item-gallery">
            <a href="<?php echo htmlspecialchars($details['image_path']); ?>" data-lightbox="lelang-gambar" data-title="<?php echo htmlspecialchars($details['title']); ?>">
                <img src="<?php echo htmlspecialchars($details['image_path']); ?>" alt="<?php echo htmlspecialchars($details['title']); ?>">
            </a>
            </div>

        <div class="item-description-panel">
            <h1><?php echo htmlspecialchars($details['title']); ?></h1>
            
            <h3>Deskripsi Barang</h3>
            <div class="description-content">
                <?php echo $descriptionHtml; ?>
            </div>
            
            <div class="bid-history">
                <h3>Riwayat Penawaran</h3>
                <?php if (empty($bids)): ?>
                    <p>Belum ada penawaran. Jadilah yang pertama!</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($bids as $bid): ?>
                            <li>
                                <strong><?php echo isset($userMap[$bid['bidder_id']]) ? htmlspecialchars($userMap[$bid['bidder_id']]) : 'Anonim'; ?></strong>
                                <span>menawar sebesar</span>
                                <strong class="harga">Rp <?php echo number_format($bid['amount'], 0, ',', '.'); ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="item-action-panel">
            <?php if ($details['status'] === 'active'): ?>
                <h3>Lelang Berakhir Dalam:</h3>
                <div id="countdown" data-end-time="<?php echo $details['end_at']; ?>"></div>
                <hr>
            <?php endif; ?>

            <h3>Harga Saat Ini:</h3>
            <p class="harga-utama">Rp <?php echo number_format($details['current_price'], 0, ',', '.'); ?></p>

            <?php 
            $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $details['owner_id'];

            if ($seller && !empty($seller['phone']) && !$isOwner): 
                $pesan_wa = urlencode("Halo, saya tertarik dengan lelang \"" . $details['title'] . "\".");
                $link_wa = "https://wa.me/" . $seller['phone'] . "?text=" . $pesan_wa;
            ?>
                <a href="<?php echo $link_wa; ?>" class="btn btn-kontak" target="_blank">Chat Penjual via WhatsApp</a>
            <?php endif; ?>
            
            <?php 
            $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $details['owner_id'];
            $isHighestBidder = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $details['highest_bidder_id'];
            $isLoggedIn = isset($_SESSION['user_id']);
            $isActive = $details['status'] === 'active';
            $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

            if ($isActive && $isLoggedIn && !$isOwner && !$isHighestBidder): ?>
                <form action="proses_bid.php" method="POST" class="bid-form">
                    <label for="bid_amount">Ajukan Tawaran Anda (Rp):</label>
                    <input type="number" name="bid_amount" id="bid_amount" min="<?php echo $details['current_price'] + 1; ?>" required>
                    <input type="hidden" name="item_id" value="<?php echo $itemId; ?>">
                    <button type="submit" class="btn">Kirim Tawaran (BID)</button>
                </form>
            <?php elseif ($isActive && $isOwner): ?>
                <p class="info-status">Ini adalah barang lelang Anda, tidak bisa menawar.</p>
            <?php elseif ($isActive && $isHighestBidder): ?>
                <p class="info-status">Anda adalah penawar tertinggi saat ini.</p>
            <?php elseif ($isActive && !$isLoggedIn): ?>
                <p class="info-status">Anda harus <a href="login.php">login</a> untuk menawar.</p>
            <?php elseif (!$isActive): // Lelang berakhir ?>
                <div class="info-pemenang">
                    <h4>Lelang Telah Berakhir!</h4>
                    <?php if (!empty($details['highest_bidder_id'])): 
                        $winnerId = $details['highest_bidder_id'];
                        $winnerName = isset($userMap[$winnerId]) ? htmlspecialchars($userMap[$winnerId]) : 'Tidak Dikenal';
                    ?>
                        <p>Dimenangkan oleh: <strong><?php echo $winnerName; ?></strong></p>
                    <?php else: ?>
                        <p>Lelang ini berakhir tanpa ada penawar.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (($isOwner && empty($bids)) || ($isAdmin && !$isActive)): ?>
            <div class="owner-actions">
                <form action="proses_hapus.php" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus lelang ini?');">
                    <input type="hidden" name="item_id" value="<?php echo $itemId; ?>">
                    <button type="submit" class="btn-hapus"><?php echo $isAdmin ? 'Hapus (Admin)' : 'Hapus Lelang'; ?></button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php 
require 'includes/footer.php'; 
?>

