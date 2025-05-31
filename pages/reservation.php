<?php
include '../config/koneksi.php';
include '../includes/suggestion.php';

$guest_count = isset($_POST['people']) ? intval($_POST['people']) : null;
$show_tables = isset($_GET['show_tables']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Reservasi Meja</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
<?php include '../includes/header.php'; ?>

<section class="reservation-form">
  <h2>Reservasi Meja</h2>
  <form action="submit_reservation.php" method="POST">
    <label for="name">Nama</label>
    <input type="text" name="name" id="name" value="<?= $_POST['name'] ?? '' ?>" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?= $_POST['email'] ?? '' ?>" required>

    <label for="No_Telp">No Telp</label>
    <input type="text" name="No_Telp" id="No_Telp" value="<?= $_POST['No_Telp'] ?? '' ?>" required>

    <label for="people">Jumlah Orang</label>
    <select name="people" id="people" required>
      <option value="">Pilih</option>
      <?php for ($i = 1; $i <= 10; $i++): ?>
        <option value="<?= $i ?>" <?= $guest_count == $i ? 'selected' : '' ?>><?= $i ?></option>
      <?php endfor; ?>
    </select>

    <label for="date">Tanggal</label>
    <input type="date" name="date" id="date" value="<?= $_POST['date'] ?? '' ?>" required>

    <label for="checkin">Jam Check-in</label>
    <input type="time" name="checkin" id="checkin" value="<?= $_POST['checkin'] ?? '' ?>" required>

    <label for="checkout">Jam Check-out</label>
    <input type="time" name="checkout" id="checkout" value="<?= $_POST['checkout'] ?? '' ?>" required>

    <p style="font-size: 0.9em; font-style: italic; color: #666;">
      * Jam operasional: 08:00 – 22:00
    </p>
    

    <label for="dp">Down Payment (DP)</label>
    <input type="number" name="dp" id="dp" value="<?= $_POST['dp'] ?? '' ?>" required>

    <!-- Tempat AJAX akan tampilkan hasil -->
    <div id="table-suggestions">
      <!-- AJAX result here -->
    </div>

    <button type="submit" class="btn-primary">Lanjut ke Menu</button>
  </form>
</section>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const peopleSelect = document.getElementById("people");
  const dateInput = document.getElementById("date");
  const checkinInput = document.getElementById("checkin");
  const checkoutInput = document.getElementById("checkout");
  const suggestionArea = document.getElementById("table-suggestions");

  function fetchTables() {
    const guestCount = peopleSelect.value;
    const date = dateInput.value;
    const checkin = checkinInput.value;
    const checkout = checkoutInput.value;

    if (!guestCount || !date || !checkin || !checkout) return;

    fetch("get_tables.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `people=${guestCount}&date=${date}&checkin=${checkin}&checkout=${checkout}`
    })
    .then(res => res.text())
    .then(html => {
      suggestionArea.innerHTML = html;
    });
  }

  [peopleSelect, dateInput, checkinInput, checkoutInput].forEach(el => {
    el.addEventListener("change", fetchTables);
  });
});
</script>

<script>
document.querySelector("form").addEventListener("submit", function(e) {
  const checkin = document.getElementById("checkin").value;
  const checkout = document.getElementById("checkout").value;

  const minTime = "08:00";
  const maxTime = "22:00";

  if (checkin < minTime || checkin > maxTime) {
    alert("Jam check-in harus antara 08:00 dan 22:00.");
    e.preventDefault();
    return;
  }

  if (checkout < minTime || checkout > maxTime) {
    alert("Jam check-out harus antara 08:00 dan 22:00.");
    e.preventDefault();
    return;
  }

  if (checkout <= checkin) {
    alert("Jam check-out harus lebih dari jam check-in.");
    e.preventDefault();
    return;
  }
});
</script>

</body>
</html>
