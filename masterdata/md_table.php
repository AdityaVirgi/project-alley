<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Master Data Management</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<?php
include '../config/koneksi.php';

// Tambah/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id     = $_POST['id'] ?? '';
  $seats  = (int)$_POST['seats'];
  $zone   = $_POST['zone'];
  $status = $_POST['status'] ?? 'available';

  if ($id) {
    $query = "UPDATE tables SET seats='$seats', zone='$zone', status='$status' WHERE id=$id";
  } else {
    $query = "INSERT INTO tables (seats, zone, status) VALUES ('$seats', '$zone', '$status')";
  }
  mysqli_query($conn, $query);
  header('Location: md_table.php');
  exit;
}

// Toggle Status
if (isset($_GET['toggle'])) {
  $id = $_GET['toggle'];
  $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM tables WHERE id=$id"));
  $new_status = $row['status'] === 'available' ? 'reserved' : 'available';
  mysqli_query($conn, "UPDATE tables SET status='$new_status' WHERE id=$id");
  header('Location: md_table.php');
  exit;
}

// Delete
if (isset($_GET['delete'])) {
  mysqli_query($conn, "DELETE FROM tables WHERE id=" . (int)$_GET['delete']);
  header('Location: md_table.php');
  exit;
}

// Edit
$edit = null;
if (isset($_GET['edit'])) {
  $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tables WHERE id=" . (int)$_GET['edit']));
} elseif (isset($_GET['add'])) {
  // Tambahkan ini untuk memastikan form kosong saat add
  $edit = ['id' => '', 'seats' => '', 'zone' => '', 'status' => 'available'];
}

$tables = mysqli_query($conn, "SELECT * FROM tables ORDER BY id ASC");
?>

<div class="container">
  <h2 class="page-title">📦 Master Data Management</h2>
  <div class="tab-menu">
    <button onclick="location.href='md_item.php'">Menu Items</button>
    <button class="active">Tables</button>
    <button onclick="window.location.href='md_rspv.php'">Reservations</button>
    <button onclick="location.href='md_order.php'">Orders</button>
    <button disabled>Access Rights</button>
  </div>

  <section class="data-section">
    <div class="section-header">
      <h3 class="section-title">Tables Management</h3>
      <button class="add-btn" onclick="location.href='md_table.php?add=1'">+ Add Table</button>

    </div>

    <table class="menu-table">
      <thead>
        <tr>
          <th>Table Number</th>
          <th>Capacity</th>
          <th>Zone</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tables as $table): ?>
        <tr>
          <td>Table <?= $table['id'] ?></td>
          <td><?= $table['seats'] ?> persons</td>
          <td><?= $table['zone'] ?></td>
          <td>
            <input type="checkbox" onclick="location.href='?toggle=<?= $table['id'] ?>'" <?= $table['status'] === 'available' ? 'checked' : '' ?>>
            <?= ucfirst($table['status']) ?>
          </td>
          <td class="actions">
            <a href="?edit=<?= $table['id'] ?>" class="edit-btn">&#9998;</a>
            <a href="?delete=<?= $table['id'] ?>" class="delete-btn" onclick="return confirm('Delete this table?')">&#128465;</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</div>

<div id="editModal" class="modal-edit-table">
  <div class="modal-edit-content">
    <span class="modal-edit-close" onclick="closeEditModal()">&times;</span>
    <h2 class="modal-edit-title">Edit Table</h2>
    <form action="md_table.php" method="post">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <label for="seats">Capacity (seats)</label>
        <input type="number" name="seats" id="seats" value="<?= $edit['seats'] ?? '' ?>" required>

        <label for="zone">Zone</label>
        <input type="text" name="zone" id="zone" value="<?= $edit['zone'] ?? '' ?>" required>

        <label for="status">Status</label>
        <select name="status" id="status">
        <option value="available" <?= ($edit['status'] ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
        <option value="reserved" <?= ($edit['status'] ?? '') === 'reserved' ? 'selected' : '' ?>>Reserved</option>
        </select>

      <div class="modal-edit-actions">
        <button type="submit" class="modal-edit-save-btn">💾 Save</button>
        <a href="#" onclick="closeEditModal()" class="modal-edit-cancel-link">❌ Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
  function openEditModal() {
    document.getElementById('editModal').style.display = 'block';
  }

  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  // Auto open modal if in edit or add mode
  <?php if (isset($_GET['edit']) || isset($_GET['add'])): ?>
    window.onload = function() {
      openEditModal();
    };
  <?php endif; ?>
</script>


<?php include '../includes/footer.php'; ?>
</body>
</html>
