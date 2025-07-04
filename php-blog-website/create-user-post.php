<?php 
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include_once("db_conn.php");
include_once("admin/data/Category.php");

$categories = getAll($conn);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Create New Post</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/richtext.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="js/jquery.richtext.min.js"></script>
</head>
<body>
	<?php include 'inc/NavBar.php'; ?>

	<div class="container mt-5">
		<h3>Create a New Blog Post</h3>

		<?php if (isset($_GET['error'])) { ?>
			<div class="alert alert-danger"><?=htmlspecialchars($_GET['error'])?></div>
		<?php } ?>

		<?php if (isset($_GET['success'])) { ?>
			<div class="alert alert-success"><?=htmlspecialchars($_GET['success'])?></div>
		<?php } ?>

		<form class="shadow p-4" 
		      action="user-post-create.php" 
		      method="post" 
		      enctype="multipart/form-data">
			
			<div class="mb-3">
				<label class="form-label">Post Title</label>
				<input type="text" class="form-control" name="title" required>
			</div>

			<div class="mb-3">
				<label class="form-label">Cover Image</label>
				<input type="file" class="form-control" name="cover" required>
			</div>

			<div class="mb-3">
				<label class="form-label">Post Content</label>
				<textarea name="text" class="form-control text" required></textarea>
			</div>

			<div class="mb-3">
				<label class="form-label">Category</label>
				<select name="category" class="form-control" required>
					<?php foreach ($categories as $category) { ?>
					<option value="<?=$category['id']?>"><?=$category['category']?></option>
					<?php } ?>
				</select>
			</div>

			<button type="submit" class="btn btn-primary">Publish Post</button>
		</form>
	</div>

	<script>
		$(document).ready(function() {
			$('.text').richText();
		});
	</script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
