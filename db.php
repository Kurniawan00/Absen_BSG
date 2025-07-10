<?php
/* -------- Koneksi DB -------- */
$host = "localhost";
$user = "root";
$pass = "";
$db   = "absensi_db";   // <== Ganti sesuai setup Anda

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: ".$conn->connect_error);

/* -------- Session global -------- */
if (session_status() === PHP_SESSION_NONE) session_start();
?>
