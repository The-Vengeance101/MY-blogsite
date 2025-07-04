<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'author') {
    header("Location: index.php");
    exit;
}
include_once("db_conn.php");
include_once("admin/data/Post.php");
include_once("admin/data/Comment.php");
$user_id = $_SESSION['user_id'];

$myPosts = getPostsByUser($conn, $user_id);
$recentComments = getRecentCommentsByUserPosts($conn, $user_id);
$postStats = getUserPostStats($conn, $user_id);
$activityLogs = getActivityLogs($conn, $user_id);
$postChartData = getPostCountByMonth($conn, $user_id);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            flex-grow: 1;
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-center">@<?php echo htmlspecialchars($_SESSION['username']); ?></h4>
        <hr>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="create-user-post.php"><i class="fas fa-pen"></i> Create Post</a>
        <a href="my-posts.php"><i class="fas fa-eye"></i> View Posts</a>
        <a href="my-posts.php?mode=edit"><i class="fas fa-edit"></i> Edit Post</a>
        <a href="my-posts.php?mode=delete"><i class="fas fa-trash"></i> Delete Post</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main-content">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5>Total Posts</h5>
                        <h2><?= $postStats['total_posts'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5>Total Likes</h5>
                        <h2><?= $postStats['total_likes'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5>Total Comments</h5>
                        <h2><?= $postStats['total_comments'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5>Most Liked Post</h5>
                        <h6><?= htmlspecialchars($postStats['top_post']) ?></h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <h4 class="mt-5">Recent Posts</h4>
        <ul class="list-group">
            <?php foreach(array_slice($myPosts, 0, 5) as $post): ?>
            <li class="list-group-item d-flex justify-content-between">
                <span><?= htmlspecialchars($post['post_title']) ?></span>
                <span>
                    <a href="blog-view.php?post_id=<?= $post['post_id'] ?>">View</a> |
                    <a href="edit-post.php?id=<?= $post['post_id'] ?>">Edit</a> |
                    <a href="delete-post.php?id=<?= $post['post_id'] ?>">Delete</a>
                </span>
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- Recent Comments -->
        <h4 class="mt-5">Recent Comments</h4>
        <ul class="list-group">
            <?php foreach(array_slice($recentComments, 0, 5) as $c): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($c['commenter']) ?>:</strong>
                <?= htmlspecialchars(substr($c['comment_text'], 0, 100)) ?>...
                <br><small><a href="blog-view.php?post_id=<?= $c['post_id'] ?>">View Post</a></small>
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- Activity Log -->
        <h4 class="mt-5">Activity Log</h4>
        <ul class="list-group">
            <?php foreach(array_slice($activityLogs, 0, 5) as $log): ?>
            <li class="list-group-item">
                <<?= strip_tags($log['message'], '<b><strong><i><em><u>') ?>
 <br>
                <small class="text-muted"><?= $log['timestamp'] ?></small>
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- Chart -->
        <h4 class="mt-5">Monthly Posts</h4>
        <canvas id="postChart" width="400" height="150"></canvas>
        <script>
            const ctx = document.getElementById('postChart').getContext('2d');
            const postChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_keys($postChartData)) ?>,
                    datasets: [{
                        label: 'Posts',
                        data: <?= json_encode(array_values($postChartData)) ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>

    </div>
</body>
</html>
