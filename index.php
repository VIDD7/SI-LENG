<?php 
require 'includes/header.php'; 
?>

<main>
    <div class="search-container">
        <form action="index.php" method="GET">
            <input type="text" name="search_query" placeholder="Cari barang lelang..." value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
            <button type="submit" class="btn"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <h2>Barang Lelang Terbaru</h2>
    <div class="gallery">
        <?php
        // Cek apakah ada query pencarian di URL
        $searchQuery = '';
        $searchAktif = false;
        if (isset($_GET['search_query']) && !empty(trim($_GET['search_query']))) {
            $searchQuery = trim($_GET['search_query']);
            $searchAktif = true;
        }

        $itemsDir = 'data/items/';
        if (is_dir($itemsDir)) {
            $itemFolders = scandir($itemsDir);
            $itemFound = false;
            
            foreach ($itemFolders as $folder) {
                if ($folder === '.' || $folder === '..') {
                    continue;
                }
                $detailPath = $itemsDir . $folder . '/detail.json';
                if (file_exists($detailPath)) {
                    $detailsJson = file_get_contents($detailPath);
                    $details = json_decode($detailsJson, true);
                    
                    if ($details) {
                        // LOGIKA FILTER: Tampilkan barang hanya jika cocok dengan pencarian
                        $tampilkan = true; // Asumsi awal: tampilkan barang
                        if ($searchAktif) {
                            // stripos mencari teks tanpa peduli huruf besar/kecil
                            // Jika TIDAK KETEMU, maka jangan tampilkan
                            if (stripos($details['title'], $searchQuery) === false) {
                                $tampilkan = false;
                            }
                        }

                        if ($tampilkan) {
                            $itemFound = true;
                            echo '<a href="item.php?id=' . htmlspecialchars($details['id']) . '" class="item-card">';
                            echo '  <img src="' . htmlspecialchars($details['image_path']) . '" alt="' . htmlspecialchars($details['title']) . '">';
                            echo '  <h3>' . htmlspecialchars($details['title']) . '</h3>';
                            echo '  <p class="harga">Rp ' . number_format($details['current_price'], 0, ',', '.') . '</p>';
                            echo '</a>';
                        }
                    }
                }
            }
            if (!$itemFound) {
                if ($searchAktif) {
                    echo "<p>Tidak ada barang lelang yang cocok dengan pencarian '<strong>" . htmlspecialchars($searchQuery) . "</strong>'.</p>";
                } else {
                    echo "<p>Belum ada barang yang dilelang saat ini.</p>";
                }
            }
        } else {
            echo "<p>Belum ada barang yang dilelang saat ini.</p>";
        }
        ?>
    </div>
</main>

<?php 
require 'includes/footer.php'; 
?>