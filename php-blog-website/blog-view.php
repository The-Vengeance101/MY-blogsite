<?php 
session_start();
$logged = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
	$logged = true;
	$user_id = $_SESSION['user_id'];
}

if (isset($_GET['post_id'])) {
	include_once("admin/data/Post.php");
	include_once("admin/data/Comment.php");
	include_once("db_conn.php");
	$id = $_GET['post_id'];
	$post = getById($conn, $id);
	$comments = getCommentsByPostID($conn, $id);
	$categories = get5Categoies($conn); 

	if ($post == 0) {
		header("Location: blog.php");
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Blog - <?=$post['post_title']?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<style>
		.category-aside {
			position: sticky;
			top: 100px;
			max-height: 400px;
			overflow-y: auto;
		}
		.main-blog {
			flex: 1;
			min-width: 0;
		}
		.aside-main {
			width: 250px;
			margin-left: 25px;
		}
		.card.main-blog-card {
			transition: box-shadow 0.3s;
		}
		.card.main-blog-card:hover {
			box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
		}
		.comment img {
			border-radius: 50%;
			margin-right: 10px;
		}
		.author-box {
			display: flex;
			align-items: center;
			margin-top: 10px;
			margin-bottom: 15px;
			font-size: 1.1rem;
			color: #333;
			font-weight: 500;
		}
		.author-box i {
			color: #007bff;
			font-size: 20px;
			margin-right: 10px;
		}
		.author-box span {
			color: #222;
			font-weight: 600;
			font-size: 1.2rem;
			letter-spacing: 0.5px;
		}


	</style>
</head>
<body>
<?php include 'inc/NavBar.php'; ?>
<div class="container mt-5">
	<section class="d-flex flex-wrap gap-4">
		<main class="main-blog">
			<div class="card main-blog-card mb-5">
				<img src="upload/blog/<?=$post['cover_url']?>" class="card-img-top" alt="...">
				<div class="card-body">
					<h5 class="card-title"><?=$post['post_title']?></h5>
					<div class="author-box">
						<i class="fa fa-user"></i>
						<span>@<?= $post['author_name'] ?? 'Guest Author'; ?></span>
					</div>

					<p class="card-text"><?=$post['post_text']?></p>
					<hr>
					<div class="d-flex justify-content-between">
						<div class="react-btns">
							<?php 
							$post_id = $post['post_id'];
							if ($logged) {
								$liked = isLikedByUserID($conn, $post_id, $user_id);
								if ($liked) {
							?>
							<i class="fa fa-thumbs-up liked like-btn" post-id="<?=$post_id?>" liked="1"></i>
							<?php } else { ?>
							<i class="fa fa-thumbs-up like like-btn" post-id="<?=$post_id?>" liked="0"></i>
							<?php } } else { ?>
							<i class="fa fa-thumbs-up" aria-hidden="true"></i>
							<?php } ?>
							Likes (<span><?= likeCountByPostID($conn, $post['post_id']); ?></span>)
							<i class="fa fa-comment" aria-hidden="true"></i> Comments (
							<?= CountByPostID($conn, $post['post_id']); ?> )
						</div>
						<small class="text-muted"><?=$post['crated_at']?></small>
					</div>

					<!-- Add Comment -->
					<form action="php/comment.php" method="post" id="comments" class="mt-4">
						<h5 class="text-secondary">Add comment</h5>
						<?php if(isset($_GET['error'])){ ?>
						<div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
						<?php } ?>

						<?php if(isset($_GET['success'])){ ?>
						<div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
						<?php } ?>

						<div class="mb-3">
							<input type="text" class="form-control" name="comment" required>
							<input type="hidden" name="post_id" value="<?=$id?>">
						</div>
						<button type="submit" class="btn btn-primary">Comment</button>
					</form>
					<hr>

					<!-- Comments -->
					<div class="comments">
						<?php if($comments != 0){ 
							foreach ($comments as $comment) {
								$u = getUserByID($conn, $comment['user_id']);
								if ($u && is_array($u)) {
						?>
						<div class="comment d-flex mb-3">
							<img src="img/user-default.png" width="40" height="40" alt="User">
							<div class="p-2">
								<span>@<?= htmlspecialchars($u['username']) ?></span>
								<p class="mb-1"><?= htmlspecialchars($comment['comment']) ?></p>
								<small class="text-body-secondary"><?= $comment['crated_at'] ?></small>
							</div>
						</div>
						<?php } else { ?>
						<div class="comment d-flex mb-3">
							<img src="img/user-default.png" width="40" height="40" alt="User">
							<div class="p-2">
								<span class="text-muted">Unknown user</span>
								<p class="mb-1"><?= htmlspecialchars($comment['comment']) ?></p>
								<small class="text-body-secondary"><?= $comment['crated_at'] ?></small>
							</div>
						</div>
						<?php } } } ?>
					</div>
				</div>
			</div>
		</main>

		<aside class="aside-main">
			<div class="list-group category-aside">
				<a href="#" class="list-group-item list-group-item-action active">Popular Category</a>
				<?php foreach ($categories as $category) { ?>
				<a href="category.php?category_id=<?= $category['id'] ?>" 
				   class="list-group-item list-group-item-action">
					<?= $category['category']; ?>
				</a>
				<?php } ?>
			</div>
		</aside>
	</section>
</div>

<!-- Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
	$(".like-btn").click(function(){
		var post_id = $(this).attr('post-id');
		var liked = $(this).attr('liked');

		if (liked == 1) {
			$(this).attr('liked', '0');
			$(this).removeClass('liked');
		} else {
			$(this).attr('liked', '1');
			$(this).addClass('liked');
		}
		$(this).next().load("ajax/like-unlike.php", {
			post_id: post_id
		});
	});
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php } else {
	header("Location: login.php");
	exit;
} ?>
