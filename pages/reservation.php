<?php
// FILE: reservation.php
include '../config/koneksi.php';
include '../includes/suggestion.php';

$guest_count = isset($_POST['people']) ? intval($_POST['people']) : null;
$auto_tables = [];
$show_tables = isset($_GET['show_tables']);

if ($guest_count && $guest_count > 6 && $show_tables) {
    $auto_tables = suggest_table_combination($conn, $guest_count);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Table Reservation</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="reservation-form">
  <h2>Reserve a Table</h2>
  <form action="<?= $show_tables ? 'submit_reservation.php' : 'reservation.php?show_tables=1' ?>" method="POST">
    <label for="name">Your Name</label>
    <input type="text" name="name" id="name" value="<?= $_POST['name'] ?? '' ?>" required>

     <label for="email">Email</label>
    <input type="varchar" name="email" id="email" value="<?= $_POST['email'] ?? '' ?>" required>

    <label for="No_Telp">No Telp</label>
    <input type="int" name="No_Telp" id="No_Telp" value="<?= $_POST['No_Telp'] ?? '' ?>" required>

    <label for="people">Number of People</label>
    <select name="people" id="people" required>
      <option value="">Select</option>
      <?php for ($i = 1; $i <= 10; $i++): ?>
        <option value="<?= $i ?>" <?= $guest_count == $i ? 'selected' : '' ?>><?= $i ?></option>
      <?php endfor; ?>
    </select>

    <label for="date">Date</label>
    <input type="date" name="date" id="date" value="<?= $_POST['date'] ?? '' ?>" required>

    <label for="checkin">Check-in Time <span style="font-style: italic; font-size: 0.9em;">( pastikan jam yang dipilih )</span></label>
    <input type="time" name="checkin" id="checkin" value="<?= $_POST['checkin'] ?? '' ?>" required>

    <label for="checkout">Check-out Time <span style="font-style: italic; font-size: 0.9em;">( pastikan jam yang dipilih )</span></label>
    <input type="time" name="checkout" id="checkout" value="<?= $_POST['checkout'] ?? '' ?>" required>

    <label for="dp">Down Payment (DP)</label>
    <input type="number" name="dp" id="dp" value="<?= $_POST['dp'] ?? '' ?>" step="0.01" required>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $guest_count && $show_tables): ?>
      <?php if ($guest_count > 6 && !empty($auto_tables)): ?>
        <div class="auto-suggestion">
          <h4>Auto-Suggested Tables:</h4>
          <ul>
            <?php foreach ($auto_tables as $table): ?>
              <li>Table #<?= $table['id'] ?> (<?= $table['seats'] ?> seats, Zone: <?= $table['zone'] ?>)</li>
              <input type="hidden" name="table_ids[]" value="<?= $table['id'] ?>">
            <?php endforeach; ?>
          </ul>
        </div>
      <?php elseif ($guest_count <= 6): ?>
        <label>Choose Table</label>
        <div class="floor-plan">
          <?php
          $result = mysqli_query($conn, "SELECT * FROM tables WHERE status = 'available' AND seats >= $guest_count ORDER BY seats");
          while ($table = mysqli_fetch_assoc($result)):
          ?>
            <label class="table-btn available">
              <input type="radio" name="table_ids[]" value="<?= $table['id'] ?>" required>
              Table <?= $table['id'] ?> (<?= $table['seats'] ?> seats)
            </label>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p style="color:red;">No available table combinations found.</p>
      <?php endif; ?>
    <?php endif; ?>

    <button type="submit" class="<?= $show_tables ? 'btn-primary' : 'btn-secondary' ?>">
      <?= $show_tables ? 'Continue to Menu' : 'Check Available Tables' ?>
    </button>
  </form>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>
