<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru</title>
    <link rel="icon" type="image/png" href="assets/css/titlelogo.png">
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body>

    <div class="wrapper">
        <h1>Buat Akun Baru</h1>

        <form action="proses_register.php" method="POST">
            <div class="form-group">
                <input type="text" placeholder="Username" id="username" name="username" required>
            </div>

            <div class="form-group">
                <input type="email" placeholder="Email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <input type="text" placeholder="Nomor HP (62xx)" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <input type="password" placeholder="Password" id="password" name="password" required>
            </div>
            <div class="link">
            <button type="submit" class="btnl">Daftar</button>
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </form>
    </div>

</body>
</html>