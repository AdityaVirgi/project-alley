<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validasi input wajib
    if (
        empty($_POST['name']) || empty($_POST['people']) || empty($_POST['date']) ||
        empty($_POST['checkin']) || empty($_POST['checkout']) || empty($_POST['dp']) ||
        !isset($_POST['table_ids']) || count($_POST['table_ids']) == 0
    ) {
        echo "Harap lengkapi semua data dan pilih minimal 1 meja.";
        exit;
    }

    // Ambil data dari form
    $name     = $_POST['name'];
    $people   = intval($_POST['people']);
    $date     = $_POST['date'];
    $checkin  = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $dp       = floatval($_POST['dp']);
    $table_ids = $_POST['table_ids'];

    // Validasi jam operasional (08:00 - 22:00)
    $open_time = strtotime("08:00");
    $close_time = strtotime("22:00");

    $checkin_time = strtotime($checkin);
    $checkout_time = strtotime($checkout);

    if ($checkin_time < $open_time || $checkout_time > $close_time) {
        echo "Reservasi hanya diperbolehkan antara pukul 08:00 hingga 22:00.";
        exit;
    }

    if ($checkin_time >= $checkout_time) {
        echo "Jam check-out harus lebih besar dari jam check-in.";
        exit;
    }

    // Simpan data ke tabel 'reservations'
    $stmt = $conn->prepare("INSERT INTO reservations (guest_name, people, reservation_date, checkin, checkout, dp_amount, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sisssd", $name, $people, $date, $checkin, $checkout, $dp);

    if (!$stmt->execute()) {
        echo "Gagal menyimpan reservasi utama: " . $stmt->error;
        exit;
    }

    $reservation_id = $stmt->insert_id;
    $stmt->close();

    // Simpan ke tabel relasi reservation_tables dan update status meja
    $stmt_table = $conn->prepare("INSERT INTO reservation_tables (reservation_id, table_id) VALUES (?, ?)");
    $update_status = $conn->prepare("UPDATE tables SET status = 'reserved' WHERE id = ?");

    foreach ($table_ids as $table_id) {
        $table_id = intval($table_id);

        $stmt_table->bind_param("ii", $reservation_id, $table_id);
        $stmt_table->execute();

        $update_status->bind_param("i", $table_id);
        $update_status->execute();
    }

    $stmt_table->close();
    $update_status->close();

    // Redirect ke menu.php
    header("Location: ../pages/menu.php?reservation_id=" . $reservation_id);
    exit;

} else {
    echo "Invalid request.";
}
?>
