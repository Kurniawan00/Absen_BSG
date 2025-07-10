<?php
include "db.php";
header("Content-Type: application/json");

$kode = $_GET['kode'] ?? '';
$stmt = $conn->prepare("SELECT nama, divisi FROM magang WHERE kode_magang=?");
$stmt->bind_param("s", $kode);
$stmt->execute();
$res = $stmt->get_result();

if ($data = $res->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "nama"    => $data['nama'],
        "divisi"  => $data['divisi']
    ]);
} else {
    echo json_encode(["success" => false]);
}
