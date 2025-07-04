<?php 
session_start();

if (isset($_POST['uname']) && isset($_POST['pass'])) {

    include "../db_conn.php";

    $uname = $_POST['uname'];
    $pass = $_POST['pass'];

    $data = "uname=" . urlencode($uname);

    if (empty($uname)) {
        $em = "User name is required";
        header("Location: ../login.php?error=$em&$data");
        exit;
    } elseif (empty($pass)) {
        $em = "Password is required";
        header("Location: ../login.php?error=$em&$data");
        exit;
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$uname]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();

            if (password_verify($pass, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['type'] = $user['type'];

                // ✅ Redirect based on type
                if ($user['type'] === 'author') {
                    header("Location: ../author-dashboard.php");
                } else {
                    header("Location: ../blog.php");
                }
                exit;
            } else {
                $em = "Incorrect username or password";
                header("Location: ../login.php?error=$em&$data");
                exit;
            }

        } else {
            $em = "Incorrect username or password";
            header("Location: ../login.php?error=$em&$data");
            exit;
        }
    }

} else {
    header("Location: ../login.php?error=Invalid request");
    exit;
}
