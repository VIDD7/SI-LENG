<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="icon" type="image/png" href="assets/img/titlelogo.png"/>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="wrapper" >
    <h1>Register</h1>
    <form action="proses_register.php" method="POST" >
      <div class="form-group" >
        <input type="text" placeholder="username" id="username" name="username" required >
      </div>
      <div class="form-group" >
        <input type="password" placeholder="password" id="password" name="password" required >
      </div>
      <div class="form-group" >
        <input type="email" placeholder="email" id="email" name="email" required >
      </div>
      <div class="form-group" >
        <input type="text" placeholder="No hp (62xx)" id="phone" name="phone" required >
      </div>
      <div class="link" >
        <button type="submit" class="btnl">Register</button>
        <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
      </div>
    </form>
  </div>
</body>
</html>