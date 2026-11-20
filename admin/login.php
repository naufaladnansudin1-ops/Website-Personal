<?php
session_start(); require_once '../koneksi.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? ''); $password = $_POST['password'] ?? '';
  $stmt = $conn->prepare("SELECT id,username,password_hash FROM admins WHERE username=?");
  $stmt->bind_param("s",$username); $stmt->execute(); $admin = $stmt->get_result()->fetch_assoc();
  if ($admin && password_verify($password,$admin['password_hash'])) {
    session_regenerate_id(true); $_SESSION['admin_id']=$admin['id']; $_SESSION['admin_username']=$admin['username'];
    header('Location: dashboard.php'); exit;
  } else $error = 'Username atau password salah.';
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login Admin</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body class="admin-login"><div class="login-card"><a class="brand" href="../index.php"><span class="brand-icon">🍽</span><span>Jelajah<span class="brand-light">Kuliner</span><small>ADMIN AREA</small></span></a><h1>Masuk ke dashboard</h1><p class="muted">Kelola data rumah makan Tataaran.</p>
<?php if($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post"><label>Username<input name="username" required autocomplete="username"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button class="primary-button full" type="submit">Login Admin</button></form><a class="back-link" href="../index.php">← Kembali ke peta user</a></div></body></html>
