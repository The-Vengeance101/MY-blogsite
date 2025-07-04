<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once("db_conn.php");

    $title = trim($_POST['title']);
    $text = trim($_POST['text']);
    $category_id = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($text) || empty($category_id)) {
        header("Location: create-user-post.php?error=All fields are required");
        exit;
    }

    // Image upload
    if (isset($_FILES['cover']['name']) && $_FILES['cover']['error'] == 0) {
        $coverName = $_FILES['cover']['name'];
        $tmpName = $_FILES['cover']['tmp_name'];
        $coverPath = "upload/blog/" . uniqid() . "-" . $coverName;

        move_uploaded_file($tmpName, $coverPath);
    } else {
        header("Location: create-user-post.php?error=Failed to upload image");
        exit;
    }

    // Insert post
    $sql = "INSERT INTO post (post_title, post_text, category, cover_url, author_id) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $text, $category_id, basename($coverPath), $user_id]);

    header("Location: create-user-post.php?success=Post published successfully");
    exit;
} else {
    header("Location: create-user-post.php");
    exit;
}
