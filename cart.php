<?php
session_start();
print_r($_SESSION['cart']);
?>

<h2>Your Cart</h2>
<ul>
  <?php foreach ($cart as $item): ?>
    <li>
      <?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?> — Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
    </li>
  <?php endforeach; ?>
</ul>
<p>
  <strong>Total:</strong>
  Rp <?= number_format(array_reduce($cart, fn($t, $i) => $t + $i['price'] * $i['quantity'], 0), 0, ',', '.') ?>
</p>
