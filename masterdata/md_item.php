<?php
include '../config/koneksi.php';

// Handle tambah/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'] ?? '';
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $price = (int)$_POST['price'];
  $image = mysqli_real_escape_string($conn, $_POST['image']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);

  if ($id) {
    $query = "UPDATE menu SET name='$name', category='$category', price=$price, image='$image', description='$description' WHERE id=$id";
  } else {
    $query = "INSERT INTO menu (name, category, price, image, description) VALUES ('$name', '$category', $price, '$image', '$description')";
  }
  mysqli_query($conn, $query);
  header("Location: md_item.php");
  exit;
}

// Handle delete
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  mysqli_query($conn, "DELETE FROM menu WHERE id=$id");
  header("Location: md_item.php");
  exit;
}

// Get edit data if any
$edit = null;
if (isset($_GET['edit'])) {
  $id = (int)$_GET['edit'];
  $res = mysqli_query($conn, "SELECT * FROM menu WHERE id=$id");
  $edit = mysqli_fetch_assoc($res);
}

$result = mysqli_query($conn, "SELECT * FROM menu ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Master Data Management</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
  <h2 class="page-title">📦 Master Data Management</h2>
  <div class="tab-menu">
    <button class="active">Menu Items</button>
    <button onclick="window.location.href='md_table.php'">Tables</button>
    <button onclick="window.location.href='md_rspv.php'">Reservations</button>
    <button onclick="location.href='md_order.php'">Orders</button>
    <button disabled>Access Rights</button>
  </div>

  <section class="data-section">
    <div class="section-header">
        <h3 class="section-title">Menu Items Management</h3>
        <button class="add-btn" onclick="window.location.href='md_item.php?add=1'">+ Add Menu Item</button>
    </div>

    <table class="menu-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><img src="../assets/img/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="menu-img"></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td class="price-cell">Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['description']) ?></td>
          <td class="actions">
            <a href="?edit=<?= $row['id'] ?>" class="edit-btn">&#9998;</a>
            <a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Yakin ingin menghapus item ini?')">&#128465;</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>
</div>

<?php if ($edit || isset($_GET['add'])): ?>
<div id="editModal" class="modal-edit-table" style="display:block">
  <div class="modal-edit-content">
    <span class="modal-edit-close" onclick="window.location.href='md_item.php'">&times;</span>
    <h2 class="modal-edit-title"><?= $edit ? 'Edit Menu Item' : 'Add Menu Item' ?></h2>
    <form action="md_item.php" method="post">
      <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

      <label for="name">Name *</label>
      <input type="text" name="name" id="name" value="<?= $edit['name'] ?? '' ?>" required>

      <label for="category">Category *</label>
      <input type="text" name="category" id="category" value="<?= $edit['category'] ?? '' ?>" required>

      <label for="price">Price (IDR) *</label>
      <input type="number" name="price" id="price" value="<?= $edit['price'] ?? '' ?>" required>

      <label for="image">Image Filename</label>
      <input type="text" name="image" id="image" value="<?= $edit['image'] ?? '' ?>">

      <label for="description">Description</label>
      <textarea name="description" id="description" rows="3"><?= $edit['description'] ?? '' ?></textarea>

      <div class="modal-edit-actions">
        <button type="submit" class="modal-edit-save-btn">💾 Save</button>
        <a href="md_item.php" class="modal-edit-cancel-link">❌ Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
</body>
</html>
