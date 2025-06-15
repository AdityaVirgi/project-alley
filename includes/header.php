<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<header>
  <nav class="navbar">
    <a href="index.php" class="navbar-logo">Alley<span>.jkt</span></a>

    <div class="navbar-nav">
      <?php if (isset($_SESSION['user'])): ?>
        <a href="../pages/dashboard.php">Dashboard</a>
        <a href="../masterdata/md_item.php">Master Data</a>
      <?php else: ?>
        <a href="../pages/index.php">Home</a>
        <a href="../pages/reservation.php">Reservation</a>
        <a href="../pages/menu.php">Menu</a>
        <a href="../pages/meja.php">Table</a>
        <a href="../pages/about.php">About</a>
      <?php endif; ?>
    </div>

    <div class="navbar-extra">
      <a href="add_to_cart.php" id="shopping-cart-button">
        <i data-feather="shopping-cart"></i>
      </a>

      <?php if (isset($_SESSION['user'])): ?>
        <span class="username">👤 <?= htmlspecialchars($_SESSION['user']) ?></span>
        <a href="../logout.php" class="btn-logout" style="margin-left: 10px; color: white;">Logout</a>
      <?php endif; ?>

      <a href="#" id="hamburger-menu"><i data-feather="menu"></i></a>
    </div>
  </nav>
</header>
