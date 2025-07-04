<?php
header("Location: index.php");
exit;
?>

<?php 
session_start();
$logged = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
	$logged = true;
	$user_id = $_SESSION['user_id'];
}
$notFound = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>
		<?php 
		if(isset($_GET['search'])){ 
			echo "search '".htmlspecialchars($_GET['search'])."'"; 
		}else{
			echo "Blog Page";
		} ?>
	</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">

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
		.form-control.dark-mode, .btn.dark-mode {
			background-color: #2a2a2a;
			color: #ffffff;
			border-color: #444;
		}
		.blog-grid-view {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
			gap: 25px;
		}
		.grid-toggle {
			margin-bottom: 20px;
			display: flex;
			justify-content: flex-end;
		}
		.grid-toggle button {
			border: none;
			background-color: #f1f1f1;
			padding: 8px 14px;
			font-size: 14px;
			cursor: pointer;
			margin-left: 10px;
			border-radius: 6px;
			transition: background-color 0.3s ease;
		}
		.grid-toggle button.active {
			background-color: #007bff;
			color: #fff;
		}
		.category-aside {
			position: sticky;
			top: 100px;
			max-height: 400px;
		}
		.main-blog-card {
			transition: box-shadow 0.3s;
		}
		.main-blog-card:hover {
			box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
		}
	</style>
</head>
<body>
<?php 
	include 'inc/NavBar.php';
	include_once("admin/data/Post.php");
	include_once("admin/data/Comment.php");
	include_once("db_conn.php");

	if(isset($_GET['search'])){
		$key = $_GET['search'];
		$posts = serach($conn, $key);
		if ($posts == 0) $notFound = 1;
	} else {
		$posts = getAll($conn);
	}

	$categories = get5Categoies($conn); 
?>

<div class="container mt-5">
	<section class="d-flex flex-wrap gap-4">
	<?php if ($posts != 0) { ?>
		<main class="main-blog flex-fill">
			<h1 class="display-4 mb-3 fs-3">
				<?php 
					if(isset($_GET['search'])){ 
						echo "Search <b>'".htmlspecialchars($_GET['search'])."'</b>"; 
					}
				?>
			</h1>

			<!-- Toggle View Buttons -->
			<div class="grid-toggle">
				<button id="listViewBtn">List View</button>
				<button id="gridViewBtn" class="active">Grid View</button>

			</div>

			<!-- Post Container -->
			<div id="postContainer">
			<?php foreach ($posts as $post) { ?>
				<div class="card main-blog-card mb-4">
					<img src="upload/blog/<?=$post['cover_url']?>" class="card-img-top" alt="Post Image">
					<div class="card-body">
						<h5 class="card-title"><?=$post['post_title']?></h5>
						<?php 
							$p = strip_tags($post['post_text']); 
							$p = substr($p, 0, 200);               
						?>
						<p class="card-text"><?=$p?>...</p>
						<a href="blog-view.php?post_id=<?=$post['post_id']?>" class="btn btn-primary">Read more</a>
						<hr>
						<div class="d-flex justify-content-between align-items-center">
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
								<i class="fa fa-thumbs-up"></i>
								<?php } ?>
								Likes (<span><?= likeCountByPostID($conn, $post_id); ?></span>)
								<a href="blog-view.php?post_id=<?=$post_id?>#comments">
									<i class="fa fa-comment"></i> Comments (
									<?= CountByPostID($conn, $post_id); ?>)
								</a>	
							</div>	
							<small class="text-muted"><?= $post['crated_at'] ?></small>
						</div>	
					</div>
				</div>
			<?php } ?>
			</div> <!-- End postContainer -->
		</main>
	<?php } else { ?>
		<main class="main-blog p-2">
			<?php if($notFound){ ?>
				<div class="alert alert-warning"> 
					No search results found - <b>key = '<?=htmlspecialchars($_GET['search'])?>'</b>
				</div>
			<?php } else { ?>
				<div class="alert alert-warning">No posts yet.</div>
			<?php } ?>
		</main>
	<?php } ?>

	<!-- Sidebar -->
	<aside class="aside-main" style="width: 250px;">
		<div class="list-group category-aside">
			<a href="#" class="list-group-item list-group-item-action active">Category</a>
			<?php foreach ($categories as $category) { ?>
			<a href="category.php?category_id=<?=$category['id']?>" 
			   class="list-group-item list-group-item-action">
				<?= $category['category']; ?>
			</a>
			<?php } ?>
		</div>
	</aside>
	</section>
</div>

<!-- JS -->
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

	// Grid/List Toggle
	const listBtn = document.getElementById("listViewBtn");
	const gridBtn = document.getElementById("gridViewBtn");
	const postContainer = document.getElementById("postContainer");
	postContainer.classList.add("blog-grid-view"); // 👈 Add this line to make Grid view default

	listBtn.addEventListener("click", () => {
		postContainer.classList.remove("blog-grid-view");
		listBtn.classList.add("active");
		gridBtn.classList.remove("active");
	});

	gridBtn.addEventListener("click", () => {
		postContainer.classList.add("blog-grid-view");
		gridBtn.classList.add("active");
		listBtn.classList.remove("active");
	});
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
