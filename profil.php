<?php 

require 'includes/header.php'; 

// 'PENJAGA' HALAMAN
// Jika pengguna belum login, arahkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

//CARI DATA PENGGUNA YANG SEDANG LOGIN 
$currentUser = null; // variabel kosong untuk menampung data user
$userId = $_SESSION['user_id'];

$usersJson = file_get_contents('data/users.json');
$users = json_decode($usersJson, true);

// obrak-abrik file users.json untuk mencari data yang cocok
foreach ($users as $user) {
    if ($user['id'] === $userId) {
        $currentUser = $user;
        break; // Hentikan loop karena data sudah ditemukan
    }
}

// Jaga-jaga jika karena suatu hal data user tidak ditemukan di file
if (!$currentUser) {
    die("Error: Gagal memuat data pengguna.");
}
// KUMPULKAN RIWAYAT LELANG PENGGUNA 
$myAuctions = []; // Array untuk menampung lelang yang dia buat
$myWinnings = []; // Array untuk menampung lelang yang dia menangkan

$itemsDir = 'data/items/';
if (is_dir($itemsDir)) {
    $itemFolders = scandir($itemsDir);
    foreach ($itemFolders as $folder) {
        if ($folder === '.' || $folder === '..') {
            continue;
        }
        $detailPath = $itemsDir . $folder . '/detail.json';
        if (file_exists($detailPath)) {
            $details = json_decode(file_get_contents($detailPath), true);
            
            // Cek apakah pengguna ini adalah pemilik barang
            if ($details && $details['owner_id'] === $userId) {
                $myAuctions[] = $details;
            }

            // Cek apakah pengguna ini adalah pemenang lelang
            if ($details && $details['highest_bidder_id'] === $userId && $details['status'] === 'ended') {
                $myWinnings[] = $details;
            }
        }
    }
}
?>

<main>
    <div class="profile-header">
        <a href="index.php" class="back-link">&larr; Kembali ke Daftar Lelang</a>
        <h1>Profil Saya</h1>
        <p>Ini adalah informasi akun Anda yang terdaftar di sistem.</p>
    </div>

    <div class="profile-grid">
        <div class="profile-card">
            <h3>Detail Akun</h3>
            <table class="profile-details-table">
                <tr>
                    <td>Username</td>
                    <td>: <strong><?php echo htmlspecialchars($currentUser['username']); ?></strong></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>: <?php echo htmlspecialchars($currentUser['email']); ?></td>
                </tr>
                <tr>
                    <td>Nomor HP</td>
                    <td>: <?php echo htmlspecialchars($currentUser['phone']); ?></td>
                </tr>
                <tr>
                    <td>Peran</td>
                    <td>: <?php echo ucfirst(htmlspecialchars($currentUser['role'])); ?></td>
                </tr>
            </table>
        </div>

        <div class="profile-card">
            <div class="profile-actions">
                <h3>Lelang yang Saya Buat</h3>
                <?php if (empty($myAuctions)): ?>
                    <p>Anda belum membuat lelang apapun.</p>
                <?php else: ?>
                    <ul class="history-list">
                        <?php foreach ($myAuctions as $item): ?>
                            <li>
                                <a href="item.php?id=<?php echo $item['id']; ?>">
                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                    <span>Status: <?php echo htmlspecialchars($item['status']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <hr>

            <div class="profile-winnings">
                <h3>Lelang yang Saya Menangkan</h3>
                <?php if (empty($myWinnings)): ?>
                    <p>Anda belum pernah memenangkan lelang.</p>
                <?php else: ?>
                    <ul class="history-list">
                        <?php foreach ($myWinnings as $item): ?>
                            <li>
                                <a href="item.php?id=<?php echo $item['id']; ?>">
                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                    <span>Dimenangkan: Rp <?php echo number_format($item['current_price']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

</main>

<?php require 'includes/footer.php'; ?>