<?php
include "db.php";                         // sudah mem‑start session
date_default_timezone_set("Asia/Makassar");

/*=========================================================================
= 1. SIMPAN DATA MAGANG  (khusus admin)                                    =
=========================================================================*/
if (isset($_POST['simpan_magang'])) {

    if (!isset($_SESSION['user_id'])) {           // keamanan
        header("Location: login.php"); exit;
    }

    /* sanitasi & simpan */
    $kode       = $conn->real_escape_string($_POST['kode_magang'] ?? '');
    $nama       = $conn->real_escape_string($_POST['nama'] ?? '');
    $divisi     = $conn->real_escape_string($_POST['divisi'] ?? '');
    $tglMulai   = $_POST['tanggal_mulai']   ?? '';
    $tglSelesai = $_POST['tanggal_selesai'] ?? '';

    $stmt = $conn->prepare(
        "INSERT INTO magang
         (kode_magang, nama, divisi, tanggal_mulai, tanggal_selesai)
         VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss",
        $kode, $nama, $divisi, $tglMulai, $tglSelesai);
    $stmt->execute(); $stmt->close();

    header("Location: Admin.php?msg=tambah_magang_ok"); exit;
}

/*=========================================================================
= 2. SIMPAN ABSENSI  (di‑submit user)                                      =
=========================================================================*/
/*
 *  dikirim dari user.php:
 *    - kode_magang
 *    - status  (Masuk | Pulang | Izin)
 *    - image   (base64; hanya Masuk/Pulang)
 *    - surat_dokter (file; hanya Izin)
 *    - latitude, longitude (opsional)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['kode_magang'], $_POST['status'])) {

    $kode   = $_POST['kode_magang'];
    $status = $_POST['status'];

    /* ambil nama & divisi */
    $stmt = $conn->prepare(
        "SELECT nama, divisi FROM magang WHERE kode_magang=?");
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $magang = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$magang) die("Kode magang tidak ditemukan / periode berakhir.");

    $nama   = $magang['nama'];
    $divisi = $magang['divisi'];

    /* ---- proses berkas (foto / surat) ---- */
    $fileName  = '';
    $upload_ok = false;

    if ($status === 'Izin') {                       /*  surat dokter  */
        if (!empty($_FILES['surat_dokter']['name'])) {
            $ext  = strtolower(pathinfo($_FILES['surat_dokter']['name'], PATHINFO_EXTENSION));
            $allow= ['jpg','jpeg','png','pdf'];
            if (in_array($ext, $allow)) {
                if (!is_dir('uploads')) mkdir('uploads', 0777, true);
                $fileName = 'surat_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['surat_dokter']['tmp_name'], "uploads/".$fileName);
                $upload_ok = true;
            }
        }
    } else {                                       /*  foto webcam  */
        $imgB64 = $_POST['image'] ?? '';
        if ($imgB64 && preg_match('/^data:image\/(\w+);base64,/', $imgB64, $m)) {
            $ext = strtolower($m[1]);                              // jpg | png | webp
            $imgData = base64_decode(substr($imgB64, strpos($imgB64, ',')+1));
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            $fileName = 'abs_' . uniqid() . '.' . $ext;
            file_put_contents("uploads/".$fileName, $imgData);
            $upload_ok = true;
        }
    }

    if (!$upload_ok) die("File tidak valid atau gagal di‑upload.");

    /* lokasi */
    $lat = isset($_POST['latitude'])  ? floatval($_POST['latitude'])  : null;
    $lon = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;

    /* simpan ke tabel absensi */
    $tanggal = date('Y-m-d');
    $waktu   = date('H:i:s');

    $stmt = $conn->prepare(
        "INSERT INTO absensi
         (nama, divisi, tanggal, waktu, status, foto, latitude, longitude)
         VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssdd",
        $nama, $divisi, $tanggal, $waktu,
        $status, $fileName, $lat, $lon);
    $stmt->execute(); $stmt->close();

    header("Location: user.php?success=1"); exit;
}

/*=========================================================================
= 3. HAPUS DATA MAGANG  (admin)                                            =
=========================================================================*/
if (isset($_GET['hapus_magang'])) {

    if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

    $id = intval($_GET['hapus_magang']);
    $stmt = $conn->prepare("DELETE FROM magang WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute(); $stmt->close();

    header("Location: Admin.php?msg=hapus_magang_ok"); exit;
}

/*=========================================================================
= 4. HAPUS ABSENSI + file (admin)                                          =
=========================================================================*/
if (isset($_GET['hapus_absen'])) {

    if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

    $id = intval($_GET['hapus_absen']);

    /* hapus file fisik */
    $row = $conn->query("SELECT foto FROM absensi WHERE id=$id")->fetch_assoc();
    if ($row && $row['foto']) {
        $path = "uploads/".$row['foto'];
        if (file_exists($path)) unlink($path);
    }

    $stmt = $conn->prepare("DELETE FROM absensi WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute(); $stmt->close();

    header("Location: Admin.php?msg=hapus_absen_ok"); exit;
}

/*=========================================================================
= Jika request tidak cocok satupun, alihkan ke beranda                    =
=========================================================================*/
header("Location: index.php");
exit;
?>
