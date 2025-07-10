<?php
include "db.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=backup_magang.xls");

echo "<table border='1'>
        <tr>
          <th>No</th><th>Kode</th><th>Nama</th><th>Divisi</th>
          <th>Tgl Mulai</th><th>Tgl Selesai</th>
        </tr>";

$no=1;
$res=$conn->query("SELECT * FROM magang_backup ORDER BY id");
while($row=$res->fetch_assoc()){
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
echo "</table>";
?>
