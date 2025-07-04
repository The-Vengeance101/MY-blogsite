<style>
  body.dark-mode {
    background-color: #121212;
    color: #ffffff;
  }

  .navbar.dark-mode {
    background-color: #1f1f1f !important;
  }

  .card.dark-mode {
    background-color: #2c2c2c;
    color: #ffffff;
  }

  .form-control.dark-mode,
  .btn.dark-mode {
    background-color: #2a2a2a;
    color: #ffffff;
    border-color: #444;
  }

  /* Font for blog title */
  @import url('https://fonts.googleapis.com/css2?family=Rajdhani:wght@600&display=swap');

  .blog-title {
    font-family: 'Rajdhani', sans-serif;
    font-size: 48px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #222;
    display: inline-block;
    position: relative;
    animation: fadeSlideUp 1s ease-out;
  }

  .blog-title-prefix {
    color: #444;
    margin-right: 4px;
    opacity: 0;
    animation: slideInLeft 0.8s ease-out forwards;
  }

  .blog-title-main {
    color: #0077cc;
    opacity: 0;
    animation: slideInRight 0.8s ease-out forwards;
    animation-delay: 0.2s;
  }

  @keyframes slideInLeft {
    from {
      opacity: 0;
      transform: translateX(-40px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(40px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes fadeSlideUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* ---------- New Styles Below ---------- */

  /* Space between menu items */
  .navbar-nav .nav-item {
    margin-right: 25px;
  }

  /* Style for Login | Signup */
  .navbar-nav .nav-item:last-child .nav-link {
    font-weight: 500;
    border: 1px solid #0077cc;
    padding: 6px 12px;
    border-radius: 5px;
    color: #0077cc;
    transition: all 0.3s ease;
  }

  .navbar-nav .nav-item:last-child .nav-link:hover {
    background-color: #0077cc;
    color: white;
  }

  /* Dark Mode variation of Login button */
  body.dark-mode .navbar-nav .nav-item:last-child .nav-link {
    border-color: #90caf9;
    color: #90caf9;
  }

  body.dark-mode .navbar-nav .nav-item:last-child .nav-link:hover {
    background-color: #90caf9;
    color: #000;
  }
</style>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <div class="blog-title">
        <span class="blog-title-prefix">My</span><span class="blog-title-main">BlogSite</span>
      </div>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <!-- Home -->
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        </li>

        <!-- Category -->
        <li class="nav-item">
          <a class="nav-link" href="category.php">Category</a>
        </li>

        <!-- Author Dashboard (only for author) -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['type'] === 'author'): ?>
        <li class="nav-item">
          <a class="nav-link" href="author-dashboard.php">Dashboard</a>
        </li>
        <?php endif; ?>

        <!-- User dropdown or Login -->
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="profile.php" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-user" aria-hidden="true"></i>
            @<?= htmlspecialchars($_SESSION['username']) ?>
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
          </ul>
        </li>
        <?php else: ?>
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login | Signup</a>
        </li>
        <?php endif; ?>
      </ul>

      <!-- Search Form -->
      <form class="d-flex" role="search" method="GET" action="index.php">
        <input class="form-control me-2" type="search" name="search" placeholder="Search"
          aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>

  <!-- Dark Mode Script -->
  <script src="dark-mode.js"></script>
</nav>
