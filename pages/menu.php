<?php
session_start();
include '../config/koneksi.php';

// Ambil semua menu dari database
$menus = [];
$result = mysqli_query($conn, "SELECT * FROM menu");
while ($row = mysqli_fetch_assoc($result)) {
  $menus[] = $row;
}

// Ambil cart dari session
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Menu - Kopi Pesan</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include '../includes/header.php'; ?>
  
  <section class="menu-section">
    <div class="menu-header">
      <h2>Our Menu</h2>
      <a href="javascript:void(0);" onclick="openCart()" class="cart-icon">
        <img src="../assets/images/cart.png" alt="Cart" />
        <span class="cart-badge">
          <?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?>
        </span>
      </a>
    </div>

    <div class="menu-filters">
      <button class="filter-btn" data-filter="all">All</button>
      <button class="filter-btn" data-filter="coffee">Coffee</button>
      <button class="filter-btn" data-filter="non-coffee">Non-Coffee</button>
      <button class="filter-btn" data-filter="pastry">Pastry</button>
      <button class="filter-btn" data-filter="food">Food</button>
      <button class="filter-btn" data-filter="dessert">Dessert</button>
    </div>

    <div class="menu-items">
      <?php foreach ($menus as $menu): ?>
        <div class="menu-card <?= htmlspecialchars($menu['category']) ?>">
          <img src="../assets/images/<?= htmlspecialchars($menu['image']) ?>" alt="<?= htmlspecialchars($menu['name']) ?>" />
          <div class="menu-info">
            <h3><?= htmlspecialchars($menu['name']) ?></h3>
            <p><?= htmlspecialchars($menu['description']) ?></p>
            <p class="price">Rp <?= number_format($menu['price'], 0, ',', '.') ?></p>

            <div class="quantity-control">
              <button type="button" onclick="changeQuantity(this, -1)">-</button>
              <span class="quantity-display">0</span>
              <button type="button" onclick="changeQuantity(this, 1)">+</button>
            </div>

            <form action="add_to_cart.php" method="post">
              <input type="hidden" name="product_id" value="<?= $menu['id'] ?>">
              <input type="hidden" name="quantity" class="quantity-input" value="0">
              <?php if (isset($_GET['reservation_id'])): ?>
                <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($_GET['reservation_id']) ?>">
              <?php endif; ?>
              <button class="add-to-cart">Add to Cart</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php include '../includes/footer.php'; ?>

  <!-- Script filter -->
  <script>
    const filterButtons = document.querySelectorAll(".filter-btn");
    const menuCards = document.querySelectorAll(".menu-card");

    filterButtons.forEach(btn => {
      btn.addEventListener("click", () => {
        const filter = btn.getAttribute("data-filter");
        menuCards.forEach(card => {
          card.style.display = (filter === "all" || card.classList.contains(filter)) ? "flex" : "none";
        });
      });
    });
  </script>

  <!-- Script add to cart -->
  <script>
    document.querySelectorAll(".add-to-cart").forEach(button => {
      button.addEventListener("click", function (e) {
        const form = this.closest("form");
        const quantityInput = form.querySelector("input[name='quantity']");
        const quantity = parseInt(quantityInput.value);
        if (quantity <= 0) {
          e.preventDefault();
          alert("Please select at least 1 item.");
        }
      });
    });

    function changeQuantity(button, change) {
      const container = button.closest('.menu-info');
      const display = container.querySelector('.quantity-display');
      const input = container.querySelector('.quantity-input');

      let quantity = parseInt(display.textContent);
      quantity = Math.max(0, quantity + change);
      display.textContent = quantity;
      input.value = quantity;
    }
  </script>

  <!-- Cart Overlay -->
  <div class="cart-overlay" id="cartOverlay" style="display: none;">
    <div class="cart-popup">
      <div class="cart-header">
        <h3>Your Order</h3>
        <button onclick="closeCart()" class="cart-close-btn">&times;</button>
      </div>

      <div class="cart-items">
        <?php if (!empty($cart_items)): ?>
          <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
              <img src="../assets/images/<?= htmlspecialchars($item['image'] ?? 'default.png') ?>" class="item-img" alt="<?= htmlspecialchars($item['name']) ?>">
              <div class="item-info">
                <p class="item-name"><?= htmlspecialchars($item['name']) ?></p>
                <p class="item-price">
                  Rp <?= number_format($item['price'], 0, ',', '.') ?> × <?= $item['quantity'] ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align:center;">Cart is empty</p>
        <?php endif; ?>
      </div>

      <?php if (!empty($cart_items)): ?>
        <div class="cart-footer">
          <form action="../pages/checkout.php" method="post">
            <input type="hidden" name="reservation_id" value="<?= $_GET['reservation_id'] ?? 0 ?>">
            <button type="submit" class="btn-checkout">Checkout</button>
          </form>
          <form action="../pages/clear_cart.php" method="post">
            <button type="submit" class="btn-clear">Clear Cart</button>
          </form>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function openCart() {
      document.getElementById('cartOverlay').style.display = 'flex';
    }
    function closeCart() {
      document.getElementById('cartOverlay').style.display = 'none';
    }
  </script>
</body>
</html>
