        <footer class="footer">
            <p>&copy; <?php echo date("Y"); ?> Sistem Lelang Online.</p>
        </footer>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    <script src="assets/js/main.js"></script>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="lelang_baru.php" class="fab" title="Buat Lelang Baru">
            <i class="fas fa-plus"></i> </a>
    <?php endif; ?>
    </body>
</html>
