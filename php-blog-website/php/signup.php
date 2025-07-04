<?php 
if(isset($_POST['fname']) && 
   isset($_POST['uname']) && 
   isset($_POST['pass']) &&
   isset($_POST['type'])){

    include "../db_conn.php";

    $fname = $_POST['fname'];
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $type = $_POST['type'];

    $data = "fname=$fname&uname=$uname&type=$type";
    
    if (empty($fname)) {
    	$em = "Full name is required";
    	header("Location: ../signup.php?error=$em&$data");
	    exit;
    } else if(empty($uname)){
    	$em = "User name is required";
    	header("Location: ../signup.php?error=$em&$data");
	    exit;
    } else if(empty($pass)){
    	$em = "Password is required";
    	header("Location: ../signup.php?error=$em&$data");
	    exit;
    } else if(!in_array($type, ['normal', 'author'])) {
        $em = "Invalid user type";
        header("Location: ../signup.php?error=$em&$data");
        exit;
    } else {
    	// Hash the password
    	$pass = password_hash($pass, PASSWORD_DEFAULT);

    	// Insert into DB including user type
    	$sql = "INSERT INTO users(fname, username, password, type) 
    	        VALUES(?, ?, ?, ?)";
    	$stmt = $conn->prepare($sql);
    	$stmt->execute([$fname, $uname, $pass, $type]);

    	header("Location: ../signup.php?success=Your account has been created successfully");
	    exit;
    }

} else {
	header("Location: ../signup.php?error=Invalid Request");
	exit;
}
