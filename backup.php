<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Backup Magang</title>
  <link rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body class="container mt-4">

  <h3>Backup Anak Magang (Periode Selesai)</h3>

  <a href="Admin.php" class="btn btn-secondary btn-sm mb-2">
      ← Kembali ke Admin
  </a>
  <a href="export_backup_excel.php" class="btn btn-dark btn-sm mb-2">
      ⬇️ Download Backup (Excel)
  </a>

  <table class="table table-bordered">
    <tr class="text-center">
      <th>No</th><th>Kode</th><th>Nama</th><th>Divisi</th>
      <th>Tgl Mulai</th><th>Tgl Selesai</th>
    </tr>
<?php
$no = 1;
$r  = $conn->query("SELECT * FROM magang_backup ORDER BY tanggal_selesai DESC");
while ($row = $r->fetch_assoc()) {
   echo "<tr>
           <td>{$no}</td>
           <td>{$row['kode_magang']}</td>
           <td>{$row['nama']}</td>
           <td>{$row['divisi']}</td>
           <td>{$row['tanggal_mulai']}</td>
           <td>{$row['tanggal_selesai']}</td>
         </tr>";
   $no++;
}
?>
  </table>
</body>
</html>
