<?php
session_start();
include "db_conn.php";
include_once("admin/data/Post.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'author') {
    header("Location: login.php");
    exit;
}

$post_id = $_GET['post_id'] ?? null;
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM post WHERE post_id = ? AND author_id = ?");
$stmt->execute([$post_id, $user_id]);
$post = $stmt->fetch();

if (!$post) {
    echo "Post not found or access denied.";
    exit;
}

include_once("admin/data/Category.php");
$categories = getAll($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/richtext.min.css">
</head>
<body>
<?php include 'inc/NavBar.php'; ?>
<div class="container mt-5">
  <h3>Edit Post</h3>
  <form action="php/update-post.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="post_id" value="<?= $post_id ?>">
    <div class="mb-3">
      <label>Title</label>
      <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['post_title']) ?>">
    </div>
    <div class="mb-3">
      <label>Current Cover:</label><br>
      <img src="upload/blog/<?= $post['cover_url'] ?>" height="100">
    </div>
    <div class="mb-3">
      <label>New Cover Image (optional)</label>
      <input type="file" name="cover" class="form-control">
    </div>
    <div class="mb-3">
      <label>Text</label>
      <textarea name="text" class="form-control rich-text"><?= htmlspecialchars($post['post_text']) ?></textarea>
    </div>
    <div class="mb-3">
      <label>Category</label>
      <select name="category" class="form-control">
        <?php foreach($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $post['category'] ? 'selected' : '' ?>>
            <?= $cat['category'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary">Update Post</button>
  </form>
</div>

<script src="js/jquery.richtext.min.js"></script>
<script>
  $(".rich-text").richText();
</script>
</body>
</html>
