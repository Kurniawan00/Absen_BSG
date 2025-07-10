<?php
include "db.php";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_magang_aktif.xls");

echo "<table border='1'>
<tr>
  <th>No</th><th>Kode Magang</th><th>Nama</th><th>Divisi</th>
  <th>Tgl Mulai</th><th>Tgl Selesai</th>
</tr>";

$no=1;
$r=$conn->query("SELECT * FROM magang ORDER BY id");
while($row=$r->fetch_assoc()){
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
