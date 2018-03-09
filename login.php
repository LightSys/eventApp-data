<?php 
session_start();

function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
{
    $algorithm = strtolower($algorithm);
    if(!in_array($algorithm, hash_algos(), true))
        trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
    if($count <= 0 || $key_length <= 0)
        trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);

    if (function_exists("hash_pbkdf2")) {
        // The output length is in NIBBLES (4-bits) if $raw_output is false!
        if (!$raw_output) {
            $key_length = $key_length * 2;
        }
        return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
    }

    $hash_length = strlen(hash($algorithm, "", true));
    $block_count = ceil($key_length / $hash_length);

    $output = "";
    for($i = 1; $i <= $block_count; $i++) {
        // $i encoded as 4 bytes, big endian.
        $last = $salt . pack("N", $i);
        // first iteration
        $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
        // perform the other $count - 1 iterations
        for ($j = 1; $j < $count; $j++) {
            $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
        }
        $output .= $xorsum;
    }

    if($raw_output)
        return substr($output, 0, $key_length);
    else
        return bin2hex(substr($output, 0, $key_length));
}

function hash_equals($knownString, $userInput) {
    if (!is_string($knownString)) {
        trigger_error('Expected known_string to be a string, '.gettype($knownString).' given', E_USER_WARNING);
        return false;
    }
    if (!is_string($userInput)) {
        trigger_error('Expected user_input to be a string, '.gettype($userInput).' given', E_USER_WARNING);
        return false;
    }
    $knownLen = strlen($knownString);
    $userLen = strlen($userInput);
    if ($knownLen !== $userLen) {
        return false;
    }
    $result = 0;
    for ($i = 0; $i < $knownLen; ++$i) {
        $result |= ord($knownString[$i]) ^ ord($userInput[$i]);
    }
    return 0 === $result;
}

include("connection.php");
include("helper.php");

if(isset($_SESSION["username"])) {
	header("Location: ". stripFileName() . "events.php");
	die();
}

if(isset($_POST["username"])) {
	if($_POST["create"] == "true") {
		$salt = mcrypt_create_iv(64, MCRYPT_DEV_URANDOM);
		$res = pbkdf2('sha256', $_POST["password"], $salt, 64000, 512);

		$get_info_page_stmt = $db->prepare("INSERT into users(username, password) values (:username,:password)");
		$get_info_page_stmt->bindValue(":username",$_POST["username"]);
		$get_info_page_stmt->bindValue(":password",$res."$".bin2hex($salt));
		$get_info_page_stmt->execute();

		header("Location: ".full_url($_SERVER));
		die();
	}

	$get_info_page_stmt = $db->prepare("SELECT password FROM users where username=:username");
	$get_info_page_stmt->bindValue(":username",$_POST["username"]);
	$get_info_page_stmt->execute();

	if(!$get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
		echo "Error: Username not found.";
		die();
	}

	$split = explode('$', $get_info_page_res["password"]);
	$salt =  pack("H*", $split[1]);
	$password = $split[0];

	$res = pbkdf2('sha256', $_POST["password"], $salt, 64000, 512);

	if(hash_equals($password, $res)) {
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
<head>
    <title>LightSys Event App Data Generator</title>
    <!--<link rel="stylesheet" href="styles/styles.css" /> FIXME: breaks username field--> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="scripts/scripts.js"></script>
</head>
<body>
    <form id="form" method="post">
        <input type="hidden" name="create" value="false">
        Username:<input name="username" type="text">
        Password:<input name="password" type="password">
        <input type="submit">
        <a onclick="createUser()">Create</a>
    </form>
</body>
<script>
	function createUser() {
		$("#form > input[name='create']").val("true");
		$("#form").submit();
	}
</script>
</html>