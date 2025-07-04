<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    body {
      background-color: #f8f9fa; /* Light background */
    }

    .form-container {
      max-width: 600px;
      background-color: #ffffff;
      padding: 3rem 2rem;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .btn-custom {
      transition: transform 0.2s;
    }

    .btn-custom:hover {
      transform: translateY(-2px);
    }

    .icon-button i {
      margin-right: 8px;
    }
  </style>
</head>
<body>

  <div class="container d-flex align-items-center justify-content-center min-vh-100">
    <form class="form-container w-100" action="php/login.php" method="post">

      <h2 class="text-center mb-4">🔐 Login</h2>

      <?php if(isset($_GET['error'])){ ?>
        <div class="alert alert-danger" role="alert">
          <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" class="form-control" name="uname"
          value="<?php echo (isset($_GET['uname'])) ? htmlspecialchars($_GET['uname']) : "" ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="pass">
      </div>

      <button type="submit" class="btn btn-primary w-100 btn-custom mb-4 icon-button">
        <i class="bi bi-box-arrow-in-right"></i> Login
      </button>

      <div class="d-grid gap-2">
        <a href="admin-login.php" class="btn btn-outline-secondary btn-custom icon-button">
          <i class="bi bi-shield-lock"></i> Admin Login
        </a>
        <a href="blog.php" class="btn btn-outline-info btn-custom icon-button">
          <i class="bi bi-journal-text"></i> Blog
        </a>
        <a href="signup.php" class="btn btn-outline-success btn-custom icon-button">
          <i class="bi bi-person-plus"></i> Sign Up
        </a>
      </div>

    </form>
  </div>

</body>
</html>
