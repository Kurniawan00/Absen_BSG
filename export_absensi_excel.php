<?php
include "db.php";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_absensi.xls");

echo "<table border='1'>
<tr>
  <th>No</th><th>Nama</th><th>Divisi</th>
  <th>Tanggal</th><th>Waktu</th><th>Status</th>
  <th>Latitude</th><th>Longitude</th>
</tr>";

$no=1;
$r=$conn->query("SELECT * FROM absensi ORDER BY id");
while($row=$r->fetch_assoc()){
  echo "<tr>
          <td>{$no}</td>
          <td>{$row['nama']}</td>
          <td>{$row['divisi']}</td>
          <td>{$row['tanggal']}</td>
          <td>{$row['waktu']}</td>
          <td>{$row['status']}</td>
          <td>{$row['latitude']}</td>
          <td>{$row['longitude']}</td>
        </tr>";
  $no++;
}
echo "</table>";
?>
