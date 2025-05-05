<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Table Reservation</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo">☕ Kopi Pesan</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="#">Reservation</a>
      <a href="#">Menu</a>
      <a href="#">About</a>
      <button class="order-now">Order Now</button>
    </nav>
  </header>

  <section class="reservation-form">
  <h2>Reserve a Table</h2>
  <form action="submit_reservation.php" method="POST">
    <label for="people">Number of People</label>
    <select name="people" id="people" required>
      <?php for ($i = 1; $i <= 10; $i++): ?>
        <option value="<?= $i ?>"><?= $i ?></option>
      <?php endfor; ?>
    </select>

    <label for="date">Date</label>
    <input type="date" name="date" id="date" required>

    <label for="checkin">Check-in Time</label>
    <input type="time" name="checkin" id="checkin" required>

    <label for="checkout">Check-out Time</label>
    <input type="time" name="checkout" id="checkout" required>

    <div class="table-status">
      <p><strong>Tables Available for Exactly <span id="people-count">3</span> People</strong></p>
      <div>
        <span style="color: green;">●</span> Available
        <span style="color: red; margin-left: 20px;">●</span> Reserved
      </div>
    </div>

    <h3>Floor Plan</h3>
    <div class="floor-plan">
      <div class="table available">8<br><small>3 seats</small></div>
      <div class="table available">11<br><small>3 seats</small></div>
      <div class="table reserved">5<br><small>3 seats</small></div>
      <div class="table reserved">2<br><small>2 seats</small></div>
    </div>

    <button type="submit" class="btn-primary">Submit Reservation</button>
  </form>
</section>


</body>
</html>
