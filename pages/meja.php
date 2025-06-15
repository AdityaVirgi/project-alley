<?php
include '../config/koneksi.php';

// Set timezone agar sinkron dengan waktu lokal
date_default_timezone_set('Asia/Jakarta');
$now = date('Y-m-d H:i:s');

// DEBUG (opsional)
echo "<!-- Server now: $now -->";

// 1. CEK APAKAH ADA RESERVASI YANG MASIH AKTIF
$reservasiAktif = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM reservations r
    JOIN reservation_tables rt ON r.id = rt.reservation_id
    WHERE r.checkin <= '$now' AND r.checkout > '$now'
");
$cek = mysqli_fetch_assoc($reservasiAktif);

// 2. RESET ATAU UPDATE STATUS MEJA BERDASARKAN KONDISI
if ($cek['total'] == 0) {
    // Tidak ada reservasi aktif → semua meja Kosong
    mysqli_query($conn, "UPDATE tables SET status = 'available'");
} else {
    // Ada reservasi aktif → update sesuai status
    mysqli_query($conn, "
        UPDATE tables 
        SET status = 'available'
        WHERE id NOT IN (
            SELECT rt.table_id
            FROM reservations r
            JOIN reservation_tables rt ON r.id = rt.reservation_id
            WHERE r.checkin <= '$now' AND r.checkout > '$now'
        )
    ");

    mysqli_query($conn, "
        UPDATE tables 
        SET status = 'reserved'
        WHERE id IN (
            SELECT rt.table_id
            FROM reservations r
            JOIN reservation_tables rt ON r.id = rt.reservation_id
            WHERE r.checkin <= '$now' AND r.checkout > '$now'
        )
    ");
}

// 3. AMBIL SEMUA DATA MEJA
$query = "SELECT * FROM `tables` ORDER BY id ASC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}

// 4. AMBIL RESERVASI YANG MASIH AKTIF
$reservations = [];
$resQuery = "
    SELECT rt.table_id, r.guest_name AS customer_name, r.checkin, r.checkout
    FROM reservations r
    JOIN reservation_tables rt ON r.id = rt.reservation_id
    WHERE r.checkin <= '$now' AND r.checkout > '$now'
";
$resResult = mysqli_query($conn, $resQuery);
if (!$resResult) {
    die("Query reservasi gagal: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($resResult)) {
    $reservations[$row['table_id']] = $row;
}

// 5. Fungsi status
function getStatusLabel($status) {
    switch ($status) {
        case 'available': return '<span class="status green">Kosong</span>';
        case 'reserved': return '<span class="status blue">Reservasi</span>';
        case 'occupied': return '<span class="status red">Terisi</span>';
        case 'cleaning': return '<span class="status yellow">Cleaning</span>';
        default: return '<span class="status gray">-</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Meja | Kopi Pesan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Status Meja</h2>
    <div class="table-container">
        <?php while ($row = mysqli_fetch_assoc($result)) :
            $id = $row['id'];
            $status = $row['status'];
            $customer_full = $reservations[$id]['customer_name'] ?? null;
            $customer = $customer_full ? ucfirst(strtolower(explode(' ', $customer_full)[0])) : null;
            $checkin = $reservations[$id]['checkin'] ?? null;
            $checkout = $reservations[$id]['checkout'] ?? null;

            $durasi = '';
            $cardClass = '';

            if ($checkout && $status === 'reserved') {
                $checkout_time = new DateTime($checkout);
                $now_dt = new DateTime($now);
                if ($checkout_time < $now_dt) {
                    $durasi = 'Waktu habis';
                    $cardClass = 'expired';
                } else {
                    $interval = $now_dt->diff($checkout_time);
                    $minutes_left = ($interval->h * 60) + $interval->i;
                    $durasi = $interval->format('%h jam %i min');
                    if ($interval->h == 0) $durasi = $interval->format('%i min');
                    if ($minutes_left < 30) $cardClass = 'warning';
                }
            }
        ?>
        <div class="table-box <?= $cardClass ?>">
            <h3>Meja <?= $id ?></h3>
            <?= getStatusLabel($status) ?>
            <?php if ($customer && $status === 'reserved'): ?>
                <p>Customer: <?= $customer ?></p>
                <p>Sisa waktu: <?= $durasi ?></p>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
