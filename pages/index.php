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
        <img src="../assets/images/alley2.png" class="hero-logo" alt="Alley Logo">
        <div class="buttons">
          <button onclick="openModal()">Already Reserved</button>
          <button onclick="window.location.href='reservation.php'">Not Yet Reserved</button>
        </div>
      </section>


      <!-- Modal Popup -->
      <div id="reservationModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeModal()">&times;</span>
          <h3>Enter Your Reservation Number</h3>
          <form action="order_confirmation.php" method="get">
            <input type="text" name="reservation_id" id="reservation_id" required placeholder="e.g. 21">
            <button type="submit" class="btn-success">View Details</button>
          </form>
        </div>
      </div>




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
      <?php if (!isset($_SESSION['user'])): ?>
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
    <?php endif; ?>

      <?php if (isset($_SESSION['user'])): ?>
      <div class="card">
        <h3>📊 Dashboard</h3>
        <p>Management dashboard with analytics and operational insights.</p>
        <a href="dashboard.php" class="btn-primary">Get Started</a>
      </div>
      <div class="card">
        <h3>📦 Master Data</h3>
        <p>Manage menu items, tables, and other data for the coffee shop.</p>
        <a href="../masterdata/md_item.php" class="btn-primary">Get Started</a>
      </div>
      <?php endif; ?>


  </section>

  <section class="about" id="about" style="text-align: center; padding: 40px 20px;">
    <h2>About alley.jkt</h2>
    <p style="max-width: 800px; margin: 0 auto;">
      alley.jkt is a modern coffee shop dedicated to providing a seamless experience for our customers.
      Our reservation and ordering system allows you to reserve tables, pre-order menu items, and enjoy your coffee
      without waiting in line.
    </p>
    <a href="about.php" class="btn-primary" style="margin-top: 20px; display: inline-block;">Learn More About Us</a>
  </section>

  <script>
  function openModal() {
    document.getElementById("reservationModal").style.display = "block";
  }
  function closeModal() {
    document.getElementById("reservationModal").style.display = "none";
  }
  window.onclick = function(event) {
    const modal = document.getElementById("reservationModal");
    if (event.target === modal) {
      modal.style.display = "none";
    }
  }
  </script>


  <?php include '../includes/footer.php'; ?>

</body>
</html>