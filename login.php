<?php
include "db.php";              // mem‑start session di dalamnya

/* Sudah login? langsung ke Admin */
if (isset($_SESSION['user_id'])) {
    header("Location: Admin.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* tangkap input */
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    /* cari user */
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    /* verifikasi MD5 */
    if ($user && md5($password) === $user['password']) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $username;
        header("Location: Admin.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login Admin</title>
  <link rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <style>
    body{background:#f5f7fa}
    .login-box{max-width:350px;margin:120px auto}
  </style>
</head>
<body>
<div class="login-box card shadow">
  <div class="card-body">
    <h4 class="text-center mb-4">Login Admin</h4>

    <?php if($error): ?>
      <div class="alert alert-danger py-1"><?=$error?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="form-group">
        <label>Username</label>
        <input name="username" class="form-control" required autofocus>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input name="password" type="password" class="form-control" required>
      </div>
      <button class="btn btn-primary btn-block mt-3">Login</button>
    </form>
  </div>
</div>
</body>
</html>
