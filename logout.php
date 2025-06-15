<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php"); // Atur sesuai struktur folder
exit;
