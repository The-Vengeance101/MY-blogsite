<?php 
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include_once("db_conn.php");
include_once("admin/data/Post.php");

$user_id = $_SESSION['user_id'];
$posts = getPostsByUser($conn, $user_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'inc/NavBar.php'; ?>
<div class="container mt-5">
    <h3 class="mb-4">My Blog Posts</h3>
    <?php if ($posts && count($posts) > 0): ?>
        <div class="row">
        <?php foreach ($posts as $post): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <img src="upload/blog/<?= htmlspecialchars($post['cover_url']) ?>" class="card-img-top" style="height: 250px; object-fit: cover;" alt="Cover">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($post['post_title']) ?></h5>
                        <p class="card-text"><?= substr(strip_tags($post['post_text']), 0, 100) ?>...</p>
                        <a href="blog-view.php?post_id=<?= $post['post_id'] ?>" class="btn btn-primary btn-sm">View</a>
                        <a href="edit-post.php?post_id=<?= $post['post_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete-post.php?post_id=<?= $post['post_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">You haven't posted anything yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
