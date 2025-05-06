<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Menu - Kopi Pesan</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header>
    <div class="logo">☕ Kopi Pesan</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="reservation.php">Reservation</a>
      <a href="menu.php">Menu</a>
      <a href="#">About</a>
      <button class="order-now">Order Now</button>
    </nav>
  </header>

  <section class="menu-section">
    <h2>Our Menu</h2>
    <div class="menu-filters">
      <button class="filter-btn" data-filter="all">All</button>
      <button class="filter-btn" data-filter="coffee">Coffee</button>
      <button class="filter-btn" data-filter="non-coffee">Non-Coffee</button>
      <button class="filter-btn" data-filter="pastry">Pastry</button>
      <button class="filter-btn" data-filter="food">Food</button>
      <button class="filter-btn" data-filter="dessert">Dessert</button>
    </div>

    <div class="menu-items">
      <!-- Chocolate Croissant -->
      <div class="menu-card pastry">
        <img src="images/chocolate-croissant.jpg" alt="Chocolate Croissant" />
        <div class="menu-info">
          <h3>Chocolate Croissant</h3>
          <p>Buttery, flaky pastry filled with rich chocolate.</p>
          <p class="price">Rp 22.000,00</p>
          <div class="quantity-control">
            <button>-</button>
            <span>1</span>
            <button>+</button>
          </div>
          <button class="add-to-cart">Add to Cart</button>
        </div>
      </div>

      <!-- Almond Croissant -->
      <div class="menu-card pastry">
        <img src="images/almond-croissant.jpg" alt="Almond Croissant" />
        <div class="menu-info">
          <h3>Almond Croissant</h3>
          <p>Flaky croissant filled with almond cream and topped with sliced almonds.</p>
          <p class="price">Rp 25.000,00</p>
          <div class="quantity-control">
            <button>-</button>
            <span>1</span>
            <button>+</button>
          </div>
          <button class="add-to-cart">Add to Cart</button>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <div class="footer-content">
      <div class="logo">☕ Kopi Pesan</div>
      <p>Serving quality coffee and great experiences since 2022.</p>
    </div>
    <div class="footer-links">
      <h4>Links</h4>
      <a href="index.php">Home</a>
      <a href="reservation.php">Reservation</a>
      <a href="menu.php">Menu</a>
      <a href="#">About</a>
    </div>
    <div class="footer-hours">
      <h4>Opening Hours</h4>
      <p>Monday - Friday: 7am - 9pm</p>
      <p>Saturday - Sunday: 8am - 10pm</p>
    </div>
    <div class="footer-contact">
      <h4>Contact</h4>
      <p>Email: hello@kopipesan.com</p>
      <p>Phone: +62 123 456 7890</p>
      <p>Address: Jl. Coffee No. 123, Jakarta</p>
    </div>
  </footer>

  <script>
    const filterButtons = document.querySelectorAll(".filter-btn");
    const menuCards = document.querySelectorAll(".menu-card");

    filterButtons.forEach(btn => {
      btn.addEventListener("click", () => {
        const filter = btn.getAttribute("data-filter");
        menuCards.forEach(card => {
          if (filter === "all" || card.classList.contains(filter)) {
            card.style.display = "flex";
          } else {
            card.style.display = "none";
          }
        });
      });
    });
  </script>
</body>
</html>
