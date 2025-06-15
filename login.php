<?php
session_start();

if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Demo credentials
  if ($username === 'admin' && $password === 'admin123') {
    $_SESSION['user'] = $username;
    header("Location: pages/dashboard.php");
    exit;
  } else {
    $error = "Incorrect username or password!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Master Data Access</title>
  <link rel="stylesheet" href="assets/css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <div class="lock-icon">
        <i class="fas fa-lock"></i>
      </div>
      <h2>Master Data Access</h2>
      <p>Please login to access master data management</p>

      <form method="POST" action="">
        <div class="form-group">
          <label for="username"><i class="fas fa-user"></i></label>
          <input type="text" id="username" name="username" placeholder="Enter your username" required>
        </div>
        <div class="form-group">
          <label for="password"><i class="fas fa-lock"></i></label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" name="login" class="btn-login">Login</button>
      </form>

      <?php if (isset($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="demo-info">
        <small>Demo credentials: <strong>admin</strong> / <strong>admin123</strong></small>
      </div>
    </div>
  </div>
</body>
</html>
