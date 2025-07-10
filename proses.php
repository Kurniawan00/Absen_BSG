<?php
include 'db.php';
header('Content-Type: application/json');

if (isset($_GET['kode'])) {
    $kode = $_GET['kode'];
    $stmt = $conn->prepare("SELECT * FROM magang WHERE kode_magang = ? LIMIT 1");
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(null);
    }
}
?>
