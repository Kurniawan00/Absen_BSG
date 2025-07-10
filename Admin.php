<?php
/* ---------- INISIALISASI ---------- */
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

/* ---------- BACKUP OTOMATIS ---------- */
date_default_timezone_set("Asia/Makassar");
$today = date('Y-m-d');
$conn->query("INSERT IGNORE INTO magang_backup
              SELECT * FROM magang
              WHERE tanggal_selesai < '$today'");
$conn->query("DELETE FROM magang
              WHERE tanggal_selesai < '$today'");

/* ---------- PROSES CRUD ---------- */
/* Tambah data anak magang */
if (isset($_POST['simpan_magang'])) {
    $stmt = $conn->prepare("INSERT INTO magang
           (kode_magang, nama, divisi, tanggal_mulai, tanggal_selesai)
           VALUES (?,?,?,?,?)");
    $stmt->bind_param(
        "sssss",
        $_POST['kode_magang'],
        $_POST['nama'],
        $_POST['divisi'],
        $_POST['tanggal_mulai'],
        $_POST['tanggal_selesai']
    );
    $stmt->execute();
    $stmt->close();
}
/* Edit data anak magang – diproses di halaman ini juga */
if (isset($_POST['update_magang'])) {
    $stmt = $conn->prepare("UPDATE magang
           SET kode_magang=?, nama=?, divisi=?,
               tanggal_mulai=?, tanggal_selesai=?
           WHERE id=?");
    $stmt->bind_param(
        "sssssi",
        $_POST['kode_magang'],
        $_POST['nama'],
        $_POST['divisi'],
        $_POST['tanggal_mulai'],
        $_POST['tanggal_selesai'],
        $_POST['id']
    );
    $stmt->execute();
    $stmt->close();
}
/* Hapus data magang */
if (isset($_GET['hapus_magang'])) {
    $id = intval($_GET['hapus_magang']);
    $conn->query("DELETE FROM magang WHERE id=$id");
}
/* Hapus absensi */
if (isset($_GET['hapus_absen'])) {
    $id = intval($_GET['hapus_absen']);
    $conn->query("DELETE FROM absensi WHERE id=$id");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Absen BSG – Panel Admin</title>

<!-- Bootstrap 4 & jQuery -->
<link rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="admin.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <span class="navbar-brand">Absen BSG (Bank SulutGO)</span>
  <div class="text-right">
      <span class="text-white mr-3">Halo, Admin</span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>

<div class="container mt-4">

<!-- ===== FORM TAMBAH MAGANG ===== -->
<h3 class="mb-3 font-weight-bold">Tambah Data Anak Magang</h3>
<form method="POST">
  <div class="card p-3 mb-4" style="max-width:550px">
      <label>Kode Magang</label>
      <input name="kode_magang" class="form-control" required>

      <label class="mt-2">Nama</label>
      <input name="nama" class="form-control" required>

      <label class="mt-2">Divisi</label>
      <select name="divisi" class="form-control" required>
          <option>Div HC</option>
          <option>Div PBJ</option>
          <option>Div GH. Pemasaran</option>
          <!-- tambah opsi lain sesuai kebutuhan -->
      </select>

      <label class="mt-2">Tanggal Mulai</label>
      <input type="date" name="tanggal_mulai" class="form-control" required>

      <label class="mt-2">Tanggal Selesai</label>
      <input type="date" name="tanggal_selesai" class="form-control" required>

      <button type="submit" name="simpan_magang"
              class="btn btn-primary mt-3">Simpan</button>
  </div>
</form>

<!-- ===== TOMBOL TOOLS ===== -->
<div class="mb-4">
    <a href="export_absensi_excel.php" class="btn btn-dark btn-sm">
        ⬇️ Download Absensi (Excel)
    </a>
    <a href="export_magang_excel.php" class="btn btn-dark btn-sm">
        ⬇️ Download Magang Aktif (Excel)
    </a>
    <a href="backup.php" class="btn btn-secondary btn-sm">
        📁 Backup Magang (Selesai)
    </a>
</div>

<!-- ===== TABEL MAGANG ===== -->
<h4 class="font-weight-bold">Daftar Anak Magang</h4>
<table class="table table-bordered">
 <tr class="text-center bg-light">
  <th>No</th><th>Kode</th><th>Nama</th><th>Divisi</th>
  <th>Tgl Mulai</th><th>Tgl Selesai</th><th>Aksi</th>
 </tr>
<?php
$no = 1;
$magang = $conn->query("SELECT * FROM magang ORDER BY id");
while ($m = $magang->fetch_assoc()) {
    echo "<tr>
           <td>{$no}</td>
           <td>{$m['kode_magang']}</td>
           <td>{$m['nama']}</td>
           <td>{$m['divisi']}</td>
           <td>{$m['tanggal_mulai']}</td>
           <td>{$m['tanggal_selesai']}</td>
           <td class='text-center'>
             <button class='btn btn-warning btn-sm edit-btn'
                     data-id='{$m['id']}'
                     data-kode='{$m['kode_magang']}'
                     data-nama='".htmlspecialchars($m['nama'],ENT_QUOTES)."'
                     data-divisi='".htmlspecialchars($m['divisi'],ENT_QUOTES)."'
                     data-start='{$m['tanggal_mulai']}'
                     data-end='{$m['tanggal_selesai']}'
                     data-toggle='modal' data-target='#editModal'>
                 Edit
             </button>
             <a href='?hapus_magang={$m['id']}'
                class='btn btn-danger btn-sm'
                onclick=\"return confirm('Hapus data ini?')\">Hapus</a>
           </td>
         </tr>";
    $no++;
}
?>
</table>

<!-- ===== TABEL ABSENSI ===== -->
<h4 class="font-weight-bold mt-5">Rekap Absensi</h4>
<table class="table table-bordered">
 <tr class="text-center bg-light">
  <th>No</th><th>Nama</th><th>Divisi</th>
  <th>Tanggal</th><th>Waktu</th><th>Status</th>
  <th>Foto/Surat</th><th>Lokasi</th><th>Aksi</th>
 </tr>
<?php
$no = 1;
$absen = $conn->query("SELECT * FROM absensi ORDER BY id DESC");
while ($a = $absen->fetch_assoc()) {

  /* Foto atau surat */
  if ($a['foto'] && file_exists("uploads/".$a['foto'])) {
      $ext = strtolower(pathinfo($a['foto'], PATHINFO_EXTENSION));
      if ($ext === 'pdf') {
          $fotoCell = "<a href='uploads/{$a['foto']}' target='_blank'>📄 PDF</a>";
      } else {
          $fotoCell = "<a href='#' class='img-popup' data-img='uploads/{$a['foto']}'>
                         <img src='uploads/{$a['foto']}' width='100' height='80'>
                       </a>";
      }
  } else { $fotoCell = "-"; }

  /* Lokasi */
  if ($a['latitude'] && $a['longitude']) {
      $lat = $a['latitude']; $lng = $a['longitude'];
      $lokasi = "<a href='#' class='map-popup' data-lat='{$lat}' data-lng='{$lng}'>
                   {$lat}, {$lng}
                 </a>";
  } else { $lokasi = "-"; }

  echo "<tr>
          <td>{$no}</td>
          <td>{$a['nama']}</td>
          <td>{$a['divisi']}</td>
          <td>{$a['tanggal']}</td>
          <td>{$a['waktu']}</td>
          <td>{$a['status']}</td>
          <td>{$fotoCell}</td>
          <td>{$lokasi}</td>
          <td class='text-center'>
             <a href='?hapus_absen={$a['id']}'
                class='btn btn-danger btn-sm'
                onclick=\"return confirm('Hapus data ini?')\">Hapus</a>
          </td>
        </tr>";
  $no++;
}
?>
</table>
</div><!-- /.container -->

<!-- ===== MODAL EDIT MAGANG ===== -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog"
     aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form class="modal-content" method="POST">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Magang</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <label>Kode Magang</label>
        <input name="kode_magang" id="edit-kode" class="form-control" required>
        <label class="mt-2">Nama</label>
        <input name="nama" id="edit-nama" class="form-control" required>
        <label class="mt-2">Divisi</label>
        <input name="divisi" id="edit-divisi" class="form-control" required>
        <label class="mt-2">Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" id="edit-start" class="form-control" required>
        <label class="mt-2">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" id="edit-end" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="submit" name="update_magang" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- ===== MODAL FOTO ===== -->
<div class="modal fade" id="imgModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <img id="modal-img" class="w-100 rounded shadow">
  </div>
</div>

<!-- ===== MODAL MAP ===== -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
     <div class="embed-responsive embed-responsive-4by3 w-100">
        <iframe id="modal-map" class="embed-responsive-item" frameborder="0" allowfullscreen></iframe>
     </div>
  </div>
</div>

<!-- ===== JAVASCRIPT KHUSUS ===== -->
<script>
/* Isi form modal edit */
$('.edit-btn').on('click', function () {
  $('#edit-id').val($(this).data('id'));
  $('#edit-kode').val($(this).data('kode'));
  $('#edit-nama').val($(this).data('nama'));
  $('#edit-divisi').val($(this).data('divisi'));
  $('#edit-start').val($(this).data('start'));
  $('#edit-end').val($(this).data('end'));
});

/* Popup foto */
$(document).on('click','.img-popup',function(e){
  e.preventDefault();
  $('#modal-img').attr('src',$(this).data('img'));
  $('#imgModal').modal('show');
});

/* Popup Google Map */
$(document).on('click','.map-popup',function(e){
  e.preventDefault();
  const lat=$(this).data('lat'), lng=$(this).data('lng');
  $('#modal-map').attr('src',
    `https://www.google.com/maps?q=${lat},${lng}&output=embed`);
  $('#mapModal').modal('show');
});
</script>
</body>
</html>
