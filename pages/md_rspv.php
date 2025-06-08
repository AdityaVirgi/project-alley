<?php
include '../config/koneksi.php';

// Tambah / Update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id       = $_POST['id'] ?? '';
  $guest    = mysqli_real_escape_string($conn, $_POST['guest_name']);
  $email    = mysqli_real_escape_string($conn, $_POST['email']);
  $telp     = mysqli_real_escape_string($conn, $_POST['no_telp']);
  $people   = (int)$_POST['people'];
  $date     = $_POST['reservation_date'];
  $checkin  = $_POST['checkin'];
  $checkout = $_POST['checkout'];
  $dp       = (int)$_POST['dp_amount'];
  $table_id = (int)$_POST['table_id'];

  if ($id) {
    mysqli_query($conn, "UPDATE reservations SET guest_name='$guest', email='$email', no_telp='$telp', people=$people, reservation_date='$date', checkin='$checkin', checkout='$checkout', dp_amount=$dp WHERE id=$id");
    mysqli_query($conn, "UPDATE reservation_tables SET table_id=$table_id WHERE reservation_id=$id");
  } else {
    mysqli_query($conn, "INSERT INTO reservations (guest_name, email, no_telp, people, reservation_date, checkin, checkout, dp_amount, created_at) VALUES ('$guest', '$email', '$telp', $people, '$date', '$checkin', '$checkout', $dp, NOW())");
    $new_id = mysqli_insert_id($conn);
    mysqli_query($conn, "INSERT INTO reservation_tables (reservation_id, table_id) VALUES ($new_id, $table_id)");
  }
  header('Location: md_rspv.php');
  exit;
}

// Hapus
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  mysqli_query($conn, "DELETE FROM reservations WHERE id=$id");
  mysqli_query($conn, "DELETE FROM reservation_tables WHERE reservation_id=$id");
  header('Location: md_rspv.php');
  exit;
}

// Ambil semua data
$reservations = mysqli_query($conn, "SELECT r.*, rt.table_id FROM reservations r LEFT JOIN reservation_tables rt ON r.id = rt.reservation_id ORDER BY r.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Master Data Reservations</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container">
  <h2 class="page-title">📦 Master Data Management</h2>
  <div class="tab-menu">
    <button onclick="location.href='md_item.php'">Menu Items</button>
    <button onclick="location.href='md_table.php'">Tables</button>
    <button class="active">Reservations</button>
    <button onclick="location.href='md_order.php'">Orders</button>
    <button disabled>Access Rights</button>
  </div>
  <section class="data-section">
    <div class="section-header">
      <h3 class="section-title">Reservations Management</h3>
      <button class="add-btn" onclick="openReservationModal()">+ Add Reservation</button>
    </div>
    <table class="menu-table">
      <thead>
        <tr>
          <th>Customer</th>
          <th>Contact</th>
          <th>Table</th>
          <th>Date & Time</th>
          <th>Guests</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($reservations)): ?>
        <tr>
          <td><strong><?= htmlspecialchars($r['guest_name']) ?></strong><br><?= htmlspecialchars($r['email']) ?></td>
          <td><?= htmlspecialchars($r['No_Telp']) ?></td>
          <td>Table <?= $r['table_id'] ?></td>
          <td><?= $r['reservation_date'] ?><br><?= $r['checkin'] ?></td>
          <td><?= $r['people'] ?> persons</td>
          <td class="actions">
            <a href="#" class="edit-btn" onclick="editReservation(this)"
              data-id="<?= $r['id'] ?>"
              data-guest="<?= htmlspecialchars($r['guest_name']) ?>"
              data-email="<?= htmlspecialchars($r['email']) ?>"
              data-telp="<?= htmlspecialchars($r['No_Telp']) ?>"
              data-people="<?= $r['people'] ?>"
              data-date="<?= $r['reservation_date'] ?>"
              data-checkin="<?= $r['checkin'] ?>"
              data-checkout="<?= $r['checkout'] ?>"
              data-dp="<?= $r['dp_amount'] ?>"
              data-table="<?= $r['table_id'] ?>">
              &#9998;
            </a>
            <a href="?delete=<?= $r['id'] ?>" class="delete-btn" onclick="return confirm('Delete this reservation?')">&#128465;</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>
</div>

<!-- Modal -->
<div id="editReservationModal" class="modal-edit-reservation">
  <div class="modal-edit-reservation-content">
    <span class="modal-edit-reservation-close" onclick="closeReservationModal()">&times;</span>
    <h2 class="modal-edit-reservation-title">Reservation Form</h2>
    <form action="md_rspv.php" method="post">
      <input type="hidden" name="id">

      <label>Guest Name</label>
      <input type="text" name="guest_name" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>No. Telp</label>
      <input type="text" name="no_telp" required>

      <label>People</label>
      <input type="number" name="people" required>

      <label>Reservation Date</label>
      <input type="date" name="reservation_date" required>

      <label>Check-in</label>
      <input type="time" name="checkin" required>

      <label>Check-out</label>
      <input type="time" name="checkout" required>

      <label>DP Amount</label>
      <input type="number" name="dp_amount" required>

      <label>Table ID</label>
      <input type="number" name="table_id" required>

      <div class="modal-edit-reservation-actions">
        <button type="submit" class="modal-edit-reservation-save-btn">💾 Save</button>
        <a href="#" onclick="closeReservationModal()" class="modal-edit-reservation-cancel-link">❌ Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
function openReservationModal() {
  const modal = document.getElementById('editReservationModal');
  modal.style.display = 'block';
  document.body.style.overflow = 'hidden';
  modal.querySelector('form').reset();
  modal.querySelector('input[name="id"]').value = '';
}

function closeReservationModal() {
  document.getElementById('editReservationModal').style.display = 'none';
  document.body.style.overflow = '';
}

function editReservation(el) {
  const modal = document.getElementById('editReservationModal');
  modal.style.display = 'block';
  document.body.style.overflow = 'hidden';

  modal.querySelector('input[name="id"]').value = el.dataset.id;
  modal.querySelector('input[name="guest_name"]').value = el.dataset.guest;
  modal.querySelector('input[name="email"]').value = el.dataset.email;
  modal.querySelector('input[name="no_telp"]').value = el.dataset.telp;
  modal.querySelector('input[name="people"]').value = el.dataset.people;
  modal.querySelector('input[name="reservation_date"]').value = el.dataset.date;
  modal.querySelector('input[name="checkin"]').value = el.dataset.checkin;
  modal.querySelector('input[name="checkout"]').value = el.dataset.checkout;
  modal.querySelector('input[name="dp_amount"]').value = el.dataset.dp;
  modal.querySelector('input[name="table_id"]').value = el.dataset.table;
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
