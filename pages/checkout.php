<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gunakan isset() untuk memastikan key ada
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone     = isset($_POST['phone']) ? trim($_POST['phone']) : '';

    if (!empty($full_name) && !empty($email) && !empty($phone)) {
        $stmt = $conn->prepare("INSERT INTO customers (full_name, email, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $full_name, $email, $phone);
        $stmt->execute();

        $_SESSION['checkout_success'] = "Customer information saved!";
        header("Location: checkout.php");
        exit;
    } else {
        $error = "All fields are required!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
</head>
<body>

<h2>Checkout</h2>

<form method="post" action="">
  <h3>Contact Information</h3>

  <label for="full_name">Full Name:</label><br>
  <input type="text" id="full_name" name="full_name" required><br><br>

  <label for="email">Email:</label><br>
  <input type="email" id="email" name="email" required><br><br>

  <label for="phone">Phone Number:</label><br>
  <input type="text" id="phone" name="phone" required><br><br>

  <button type="submit">Continue</button>
</form>

<?php if (isset($error)): ?>
  <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['checkout_success'])): ?>
  <p style="color: green;"><?= $_SESSION['checkout_success'] ?></p>
  <?php unset($_SESSION['checkout_success']); ?>
<?php endif; ?>

</body>
</html>
