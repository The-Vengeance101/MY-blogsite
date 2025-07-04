<?php
session_start();
include "db_conn.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'author') {
    header("Location: login.php");
    exit;
}

$post_id = $_GET['post_id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM post WHERE post_id = ? AND author_id = ?");
$stmt->execute([$post_id, $user_id]);

header("Location: my-posts.php?success=Post deleted");
