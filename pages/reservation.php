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

<!-- AJAX Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const peopleSelect = document.getElementById("people");

  peopleSelect.addEventListener("change", function () {
    const guestCount = this.value;
    if (!guestCount) return;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "get_tables.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && xhr.status === 200) {
        document.getElementById("table-suggestions").innerHTML = xhr.responseText;
      }
    };
    xhr.send("people=" + guestCount);
  });

  // Trigger langsung saat halaman load ulang
  if (peopleSelect.value) {
    peopleSelect.dispatchEvent(new Event('change'));
  }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const peopleSelect = document.getElementById("people");
  const suggestionArea = document.getElementById("table-suggestions");

  peopleSelect.addEventListener("change", function () {
    const guestCount = parseInt(this.value);
    if (!guestCount) return;

    fetch("get_tables.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "people=" + guestCount
    })
    .then(res => res.text())
    .then(html => {
      suggestionArea.innerHTML = html;
    });
  });
});
</script>


</body>
</html>
