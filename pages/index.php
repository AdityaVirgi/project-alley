<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
<?php include '../includes/header.php'; ?>

      <section class="hero">
      <img src="../assets/images/alley2.png" class="hero-logo">
      <form action="Reservation.php" method="post">
      <form action="reservation.php" method="post">
            <button class="add-to-cart">Reservasi</button>
            <button class="add-to-cart">Scan qr code</button>
           </form>
            </form>
    </section>

        <section id="menu" class="menu">
      <div class="menu-row">
        <div class="menu-card">
          <img src="../assets/images/gambar1.png" alt="Menu 1">
        </div>
        <div class="menu-card">
          <img src="../assets/images/gambar2.png" alt="Menu 2">
        </div>
        <div class="menu-card">
          <img src="../assets/images/gambar3.png" alt="Menu 3">
        </div>
        <div class="menu-card">
          <img src="../assets/images/gambar4.png" alt="Menu 4">
      </div>
    </section>



  <section class="services">
    <h2>Our Services</h2>
    <div class="service-cards">
      <div class="card">
        <h3>📅 Table Reservation</h3>
        <p>Reserve your favorite spot in our coffee shop and order in advance.</p>
        <a href="reservation.php" class="btn-primary">Get Started</a>
      </div>
      <div class="card">
        <h3>🔳 QR Code Ordering</h3>
        <p>Scan the QR code on your table to order directly from your seat.</p>
        <a href="reservation.php" class="btn-primary">Scan QR Code</a>
      </div>
      <div class="card">
        <h3>☕ View Our Menu</h3>
        <p>Explore our handcrafted coffee drinks and delicious food items.</p>
        <a href="menu.php" class="btn-primary">Get Started</a>
      </div>
    </div>
  </section>

  <?php include '../includes/footer.php'; ?>

</body>
</html>