<?php
// admin/login.php
require_once __DIR__ . '/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '') {
        $rows = db_query($db, "SELECT * FROM admins WHERE username = ? LIMIT 1", [$username]);
        if (!empty($rows)) {
            $user = $rows[0];
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['pw_admin_id'] = $user['id'];
                $_SESSION['pw_admin_user'] = $user['username'];
                $_SESSION['pw_admin_name'] = $user['full_name'];
                header("Location: index.php");
                exit;
            }
        }
        $error = 'Invalid username or password.';
    } else {
        $error = 'Please fill in both fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Portal | Planet Wanders Tours</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      background: #111712;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card-login {
      background: #ffffff;
      border-radius: 20px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }
    .btn-gold {
      background: #d99a26;
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 12px;
      padding: 0.75rem;
    }
    .btn-gold:hover {
      background: #b87e1a;
      color: #fff;
    }
  </style>
</head>
<body>
  <div class="card card-login p-4 p-md-5">
    <div class="text-center mb-4">
      <div class="d-inline-flex p-3 rounded-circle bg-warning bg-opacity-10 text-warning mb-2">
        <i class="fa-solid fa-compass fa-2x"></i>
      </div>
      <h4 class="fw-bold text-dark mb-0">Planet Wanders</h4>
      <small class="text-muted text-uppercase tracking-wider">Staff Management Panel</small>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small"><?= esc($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label small fw-bold text-muted">Username</label>
        <input type="text" name="username" class="form-control" placeholder="admin" required autofocus>
      </div>
      <div class="mb-4">
        <label class="form-label small fw-bold text-muted">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-gold w-100 mb-2">Sign In</button>
      <div class="text-center text-muted small mt-3">
        Default: <code>admin</code> / <code>admin123</code>
      </div>
    </form>
  </div>
</body>
</html>