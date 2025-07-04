<?php
session_start();
include "../db_conn.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'author') {
    header("Location: ../login.php");
    exit;
}

$post_id = $_POST['post_id'];
$title = $_POST['title'];
$text = $_POST['text'];
$category = $_POST['category'];
$user_id = $_SESSION['user_id'];

$cover = $_FILES['cover']['name'] ? $_FILES['cover']['name'] : null;

if ($cover) {
    $path = "../upload/blog/";
    $tmp = $_FILES['cover']['tmp_name'];
    move_uploaded_file($tmp, $path.$cover);
    
    $stmt = $conn->prepare("UPDATE post SET post_title=?, post_text=?, category=?, cover_url=? WHERE post_id=? AND author_id=?");
    $stmt->execute([$title, $text, $category, $cover, $post_id, $user_id]);
} else {
    $stmt = $conn->prepare("UPDATE post SET post_title=?, post_text=?, category=? WHERE post_id=? AND author_id=?");
    $stmt->execute([$title, $text, $category, $post_id, $user_id]);
}

header("Location: ../my-posts.php?success=Post updated");
