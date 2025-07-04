<?php 
session_start();
include "db_conn.php";
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php"); exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'inc/NavBar.php'; ?>
<div class="container mt-5">
  <h3>Edit Your Profile</h3>
  <form method="post" action="php/edit-profile-submit.php">
    <div class="mb-3">
      <label>Full Name</label>
      <input type="text" class="form-control" name="fname" value="<?= htmlspecialchars($user['fname']) ?>">
    </div>
    <div class="mb-3">
      <label>New Password (leave blank to keep same)</label>
      <input type="password" class="form-control" name="password">
    </div>
    <button class="btn btn-primary">Update Profile</button>
  </form>
</div>
</body>
</html>
