<?php
include '../config/koneksi.php';

$guest_count = isset($_POST['people']) ? intval($_POST['people']) : 0;
$date = $_POST['date'] ?? '';
$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';

if (!$guest_count || !$date || !$checkin || !$checkout) {
    echo '<p style="color:red;">Mohon isi jumlah orang, tanggal, dan waktu checkin-checkout.</p>';
    exit;
}

$found = false;
$max_seats = 12;

for ($seats = $guest_count; $seats <= $max_seats; $seats++) {
    $query = "SELECT * FROM tables WHERE seats = $seats AND id NOT IN (
        SELECT rt.table_id 
        FROM reservation_tables rt
        JOIN reservations r ON r.id = rt.reservation_id
        WHERE r.reservation_date = '$date' AND (
            ('$checkin' BETWEEN r.checkin AND r.checkout) OR
            ('$checkout' BETWEEN r.checkin AND r.checkout) OR
            (r.checkin BETWEEN '$checkin' AND '$checkout') OR
            (r.checkout BETWEEN '$checkin' AND '$checkout')
        )
    ) ORDER BY zone, id";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<label>Pilih Meja (Seats: ' . $seats . ')</label>';
        echo '<div class="floor-plan">';
        while ($table = mysqli_fetch_assoc($result)) {
            echo '<label class="table-btn available">';
            echo '<input type="radio" name="table_ids[]" value="' . $table['id'] . '" required>';
            echo 'Table ' . $table['id'] . ' (' . $table['seats'] . ' seats, Zone: ' . $table['zone'] . ')';
            echo '</label>';
        }
        echo '</div>';
        $found = true;
        break;
    }
}

if (!$found && $guest_count > 6) {
    include '../includes/suggestion.php';
    $auto_tables = suggest_table_combination($conn, $guest_count, $date, $checkin, $checkout);

    if (!empty($auto_tables)) {
        echo '<div class="auto-suggestion">';
        echo '<h4>Auto-Suggested Tables:</h4>';
        echo '<ul>';
        foreach ($auto_tables as $table) {
            echo '<li>Table #' . $table['id'] . ' (' . $table['seats'] . ' seats, Zone: ' . $table['zone'] . ')</li>';
            echo '<input type="hidden" name="table_ids[]" value="' . $table['id'] . '">';
        }
        echo '</ul>';
        echo '</div>';
        $found = true;
    }
}

if (!$found) {
    echo '<p style="color:red;">Tidak ada meja tersedia untuk jumlah orang dan waktu tersebut.</p>';
}
?>
