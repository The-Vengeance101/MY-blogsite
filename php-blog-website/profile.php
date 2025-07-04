<?php 
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include_once("db_conn.php");

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Total posts
$postStmt = $conn->prepare("SELECT COUNT(*) FROM post WHERE author_id = ?");
$postStmt->execute([$user_id]);
$total_posts = $postStmt->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Author Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<?php include 'inc/NavBar.php'; ?>
<div class="container mt-5">
    <div class="card shadow p-4 profile-card">
        <div class="text-center">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['fname']) ?>&background=0088FF&color=fff" alt="Profile Picture">
            <h3 class="mt-2"><?= htmlspecialchars($user['fname']) ?></h3>
            <p class="text-muted">@<?= htmlspecialchars($user['username']) ?></p>
            <span class="badge bg-<?= ($user['type'] == 'author') ? 'success' : 'secondary' ?>">
                <?= ucfirst($user['type']) ?>
            </span>
        </div>
        <hr>
        <div class="row text-center mt-4">
            <div class="col-md-4">
                <h5><?= $total_posts ?></h5>
                <p>Total Posts</p>
            </div>
            <div class="col-md-4">
                <h5>N/A</h5>
                <p>Total Likes</p>
            </div>
            <div class="col-md-4">
                <h5>N/A</h5>
                <p>Total Comments</p>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <a href="author-dashboard.php" class="btn btn-primary m-2">Go to Dashboard</a>
            <a href="my-posts.php" class="btn btn-secondary m-2">View My Posts</a>
            <a href="create-user-post.php" class="btn btn-success m-2">Create New Post</a>
        </div>
    </div>
</div>
</body>
</html>
