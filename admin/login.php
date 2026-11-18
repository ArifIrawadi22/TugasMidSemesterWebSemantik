<?php $base = '../'; include '../includes/header.php'; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (hash_equals(ADMIN_PASSWORD, $_POST['password'])) { $_SESSION['admin'] = true; header('Location: index.php'); exit; }
  $err = 'Password salah.';
} ?>
<h3>Login Admin (Penjual)</h3><?php if ($err) echo '<div class="alert alert-danger">' . e($err) . '</div>'; ?>
<form method="post" style="max-width:360px"><input type="password" name="password" class="form-control mb-2" placeholder="Password admin" required><button class="btn btn-primary">Masuk</button></form>
<?php include '../includes/footer.php'; ?>
