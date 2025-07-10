<?php
include "db.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Absen BSG – Form Absensi</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="user_style.css">
</head>
<body>
  <!-- ===== HEADER SINGLE BAR ===== -->
  <div class="header-bar">
      <img src="logo/logo.jpg" alt="BSG Logo">
      <div class="title">Absensi Magang BSG – Kantor Pusat 2025</div>
      <a href="login.php" class="login-link">Login</a>
  </div>

  <div class="wrapper">
    <div class="card">
      <div class="card-header">ABSENSI</div>
      <div class="card-body">
        <form id="absenForm" method="POST" action="penyimpan_data.php" enctype="multipart/form-data">
          <div class="form-group">
            <label>Kode Magang:</label>
            <input type="text" class="form-control" name="kode_magang" id="kode_magang" required autofocus>
          </div>

          <div id="magang_info" class="info-box mb-3" style="display:none">
            <strong>Nama:</strong> <span id="nama_magang"></span><br>
            <strong>Divisi:</strong> <span id="divisi_magang"></span>
          </div>

          <div class="form-group mt-3">
            <label>Status Absensi:</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="masuk" value="Masuk" checked>
              <label class="form-check-label" for="masuk">Masuk</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="pulang" value="Pulang">
              <label class="form-check-label" for="pulang">Pulang</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="izin" value="Izin">
              <label class="form-check-label" for="izin">Izin / Sakit</label>
            </div>
          </div>

          <!-- webcam + preview side by side -->
          <div id="webcam_section" class="form-group mt-4 webcam-flex">
             <video id="video" autoplay playsinline></video>
             <canvas id="canvas" style="display:none;"></canvas>
             <img id="preview" alt="preview" style="display:none;">
          </div>
          <div class="text-center">
             <button type="button" id="snap" class="btn btn-info btn-sm mt-2">📷 Ambil Foto</button>
          </div>

          <div id="surat_section" class="form-group mt-4" style="display:none">
            <label>Upload Surat Keterangan Dokter (JPG/PNG/PDF):</label>
            <input type="file" name="surat_dokter" id="surat_dokter" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf">
          </div>

          <div id="lokasi_box" class="info-box mt-3" style="display:none"></div>

          <input type="hidden" id="image" name="image">
          <input type="hidden" id="latitude" name="latitude">
          <input type="hidden" id="longitude" name="longitude">

          <button class="btn btn-primary btn-block mt-4">Kirim Absensi</button>
        </form>
      </div>
    </div>
  </div>

<script>
function toggleMode(){const s=document.querySelector('input[name="status"]:checked').value;document.getElementById("webcam_section").style.display="Izin"===s?"none":"flex";document.getElementById("surat_section").style.display="Izin"===s?"block":"none";document.getElementById("surat_dokter").required="Izin"===s}document.querySelectorAll('input[name="status"]').forEach(r=>r.addEventListener("change",toggleMode));toggleMode();let stream=!1;const v=document.getElementById("video"),c=document.getElementById("canvas"),p=document.getElementById("preview"),btn=document.getElementById("snap");navigator.mediaDevices.getUserMedia({video:!0}).then(s=>{v.srcObject=s,stream=!0}).catch(err=>alert("Webcam gagal: "+err));btn.addEventListener("click",()=>{if(!stream)return alert("Webcam belum siap");c.width=v.videoWidth,c.height=v.videoHeight,c.getContext("2d").drawImage(v,0,0);const d=c.toDataURL("image/jpeg",.9);document.getElementById("image").value=d;p.src=d;p.style.display="block";geo()});function geo(){navigator.geolocation&&navigator.geolocation.getCurrentPosition(pos=>{const {latitude:lat,longitude:lon,accuracy:acc}=pos.coords;document.getElementById("latitude").value=lat;document.getElementById("longitude").value=lon;document.getElementById("lokasi_box").style.display="block";document.getElementById("lokasi_box").innerHTML=`Lat: ${lat.toFixed(6)} / Lon: ${lon.toFixed(6)}<br>Akurasi: ${acc.toFixed(0)} m`;},e=>alert("Lokasi gagal: "+e.message),{enableHighAccuracy:!0,timeout:1e4,maximumAge:0})}

document.getElementById("kode_magang").addEventListener("blur",()=>{const k=document.getElementById("kode_magang").value.trim();k&&fetch("cek_magang.php?kode="+encodeURIComponent(k)).then(r=>r.json()).then(r=>{if(r.success){document.getElementById("nama_magang").textContent=r.nama;document.getElementById("divisi_magang").textContent=r.divisi;document.getElementById("magang_info").style.display="block";}else{alert("Kode tidak ditemukan.");document.getElementById("magang_info").style.display="none";}})});

document.getElementById("absenForm").addEventListener("submit",e=>{const st=document.querySelector('input[name="status"]:checked').value;if("Izin"!==st&&!document.getElementById("image").value){alert("Silakan ambil foto dulu.");return e.preventDefault()}if("Izin"===st&&!document.getElementById("surat_dokter").files.length){alert("Silakan upload surat dokter.");e.preventDefault()}});
</script>
</body>
</html>
