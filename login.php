<?php 
session_start();

include("connection.php");
include("helper.php");

if(isset($_SESSION["username"])) {
	header("Location: ". stripFileName() . "/events.php");
	die();
}

if(isset($_POST["username"])) {
	$get_info_page_stmt = $db->prepare("SELECT password FROM users where username=:username");
	$get_info_page_stmt->bindValue(":username",$_POST["username"]);
	$get_info_page_stmt->execute();

	if(!$get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
		echo "Error: Username not found.";
		die();
	}

	if(password_verify($_POST["password"], $get_info_page_res["password"])) {
		$_SESSION["username"] = $_POST["username"];
		header("Location: ". stripFileName() . "/events.php");
		die();
	}
	else {
		echo "Error: Incorrect password.";
		die();
	}
}
?>

<html>
<body>
<form id="form" method="post">
Username:<input name="username" type="text">
Password:<input name="password" type="password">
<input type="submit">
</form>
</body>
</html>