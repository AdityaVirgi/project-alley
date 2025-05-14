<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Menu - Kopi Pesan</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <?php include '../includes/header.php'; ?>
  
   <!-- KATEGORI MAKANAN -->
  <section class="menu-section">
  <div class="menu-header">
  <h2>Our Menu</h2>
  <a href="javascript:void(0);" onclick="openCart()" class="cart-icon">
    <img src="../assets/images/cart.png" alt="Cart" />
    <span class="cart-badge">2</span>
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
      <!-- Chocolate Croissant -->
      <div class="menu-card pastry">
          <img src="../assets/images/Chocolate-Croissant.jpg" alt="Chocolate Croissant" />
          <div class="menu-info">
          <h3>Chocolate Croissant</h3>
          <p>Buttery, flaky pastry filled with rich chocolate.</p>
          <p class="price">Rp 22.000,00</p>

          <div class="quantity-control">
            <button type="button" onclick="decreaseQuantity()">-</button>
            <span id="quantityDisplay">0</span>
            <button type="button" onclick="increaseQuantity()">+</button>
          </div>

          <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_name" value="Chocolate Croissant">
          <input type="hidden" name="price" value="22000">
          <input type="hidden" name="quantity" id="quantityInput" value="0">
          <button class="add-to-cart">Add to Cart</button>
        </form>
          <script>
            let quantity = 0;

            function updateQuantityDisplay() {
              document.getElementById('quantityDisplay').textContent = quantity;
              document.getElementById('quantityInput').value = quantity;
            }

            function increaseQuantity() {
              quantity++;
              updateQuantityDisplay();
            }

            function decreaseQuantity() {
              if (quantity > 0) {
                quantity--;
                updateQuantityDisplay();
              }
            }
          </script>
        </div>
      </div>

      <!-- Almond Croissant -->
      <div class="menu-card pastry">
        <img src="../assets/images/almond-croissant.jpg" alt="Almond Croissant" />
        <div class="menu-info">
          <h3>Almond Croissant</h3>
          <p>Flaky croissant filled with almond cream and topped with sliced almonds.</p>
          <p class="price">Rp 25.000,00</p>

          <div class="quantity-control">
            <button type="button" onclick="changeQuantity(this, -1)">-</button>
            <span class="quantity-display">0</span>
            <button type="button" onclick="changeQuantity(this, 1)">+</button>
          </div>

          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_name" value="Almond Croissant">
            <input type="hidden" name="price" value="25000">
            <input type="hidden" name="quantity" class="quantity-input" value="0">
            <button class="add-to-cart">Add to Cart</button>
          </form>
        </div>
      </div>

      <script>
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
    </div>
  </section>


  <?php include '../includes/footer.php'; ?>

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

<script>
  // Ambil jumlah dari localStorage, default 0
  let cartCount = localStorage.getItem('cartCount') ? parseInt(localStorage.getItem('cartCount')) : 0;
  updateCartBadge();

  // Tambahkan event listener ke semua tombol "Add to Cart"
  document.querySelectorAll(".add-to-cart").forEach(button => {
    button.addEventListener("click", function (e) {
      e.preventDefault();

      const form = this.closest("form");
      const quantityInput = form.querySelector("input[name='quantity']");
      const quantity = parseInt(quantityInput.value);

      if (quantity > 0) {
        cartCount += quantity;
        localStorage.setItem('cartCount', cartCount);
        updateCartBadge();
        alert("Item added to cart!");
        quantityInput.value = 0;

        const display = form.parentElement.querySelector(".quantity-display");
        if (display) display.textContent = 0;

        const displaySingle = form.parentElement.querySelector("#quantityDisplay");
        if (displaySingle) displaySingle.textContent = 0;
      } else {
        alert("Please select at least 1 item.");
      }
    });
  });

  function updateCartBadge() {
    const badge = document.querySelector(".cart-badge");
    if (badge) {
      badge.textContent = cartCount;
    }
  }
</script>

<!-- Cart Popup HTML -->
<div class="cart-overlay" id="cartOverlay" style="display: none;">
  <!-- Sidebar Cart -->
  <div class="cart-popup">
    <div class="cart-header">
      <h3>Your Order</h3>
      <button onclick="closeCart()" class="cart-close-btn">&times;</button>
    </div>

    <div class="cart-items">
      <div class="cart-item">
        <img src="../assets/images/cappuccino.jpg" class="item-img" alt="Cappuccino">
        <div class="item-info">
          <p class="item-name">Cappuccino</p>
          <p class="item-price">Rp 35.000,00 × 4</p>
          <div class="item-qty">
            <button class="btn-qty">-</button>
            <span>4</span>
            <button class="btn-qty">+</button>
          </div>
        </div>
        <button class="item-delete">🗑</button>
      </div>
    </div>

    <div class="cart-footer">
      <div class="total">
        <span>Total</span>
        <strong>Rp 140.000,00</strong>
      </div>
      <button class="btn-checkout">Checkout</button>
      <button class="btn-clear">Clear Cart</button>
    </div>
  </div>
</div>

<!-- Script Buka/Tutup Cart -->
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
