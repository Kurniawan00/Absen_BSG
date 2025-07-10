<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = intval($_POST['id']    ?? 0);
    $field =        $_POST['field'] ?? '';
    $value =        $_POST['value'] ?? '';

    $allowed = ['kode_magang','nama','divisi','tanggal_mulai','tanggal_selesai'];
    if (!in_array($field, $allowed)) exit;      // keamanan

    $sql  = "UPDATE magang SET $field=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $value, $id);
    $stmt->execute();
}
