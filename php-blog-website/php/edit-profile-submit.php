<?php 
session_start();
include "../db_conn.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php"); exit;
}

$user_id = $_SESSION['user_id'];
$fname = $_POST['fname'];
$password = $_POST['password'];

if (!empty($password)) {
  $hashed = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("UPDATE users SET fname=?, password=? WHERE id=?");
  $stmt->execute([$fname, $hashed, $user_id]);
} else {
  $stmt = $conn->prepare("UPDATE users SET fname=? WHERE id=?");
  $stmt->execute([$fname, $user_id]);
}

header("Location: ../profile.php");
