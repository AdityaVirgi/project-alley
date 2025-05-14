<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Table Reservation</title>
  <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
  <?php include '../includes/header.php'; ?>

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

    <!-- HTML untuk tombol Floor Plan -->
<h3>Floor Plan</h3>
<div class="floor-plan">
  <button type="button" class="table-btn available" data-table-id="8" data-seats="3">
    8<br><small>3 seats</small>
  </button>
  <button type="button" class="table-btn available" data-table-id="11" data-seats="3">
    11<br><small>3 seats</small>
  </button>
  <button type="button" class="table-btn reserved" data-table-id="5" data-seats="3" disabled>
    5<br><small>3 seats</small>
  </button>
  <button type="button" class="table-btn reserved" data-table-id="2" data-seats="2" disabled>
    2<br><small>2 seats</small>
  </button>
</div>

    <button type="submit" class="btn-primary">Submit Reservation</button>
  </form>
</section>

<?php include '../includes/footer.php'; ?>

</body>
</html>
