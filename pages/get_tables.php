<?php
include '../config/koneksi.php';

$guest_count = isset($_POST['people']) ? intval($_POST['people']) : 0;

if (!$guest_count) {
    echo '<p style="color:red;">Jumlah orang tidak valid.</p>';
    exit;
}

// Coba cari meja dengan jumlah kursi >= guest_count, urut dari yang paling pas
$max_seats = 12; // Atur sesuai kapasitas maksimal meja di tempatmu
$found = false;

for ($seats = $guest_count; $seats <= $max_seats; $seats++) {
    $query = "SELECT * FROM tables WHERE status = 'available' AND seats = $seats ORDER BY zone, id";
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

// Jika tetap tidak ditemukan, coba gunakan auto-suggestion untuk kombinasi meja (jika guest > 6)
if (!$found && $guest_count > 6) {
    include '../includes/suggestion.php';
    $auto_tables = suggest_table_combination($conn, $guest_count);

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
    echo '<p style="color:red;">Tidak ada meja tersedia untuk jumlah orang tersebut.</p>';
}
?>
