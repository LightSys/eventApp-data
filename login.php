<?php 

include('config/config.php');

session_start();

$_SESSION['timestamp']=time();
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

function my_hash_equals($knownString, $userInput) {
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

function spit_out_password($pass) {
    $randsalt = md5('' . openssl_random_pseudo_bytes(12) . ':' . time());
    $salt =  pack("H*", $randsalt);
    $res = pbkdf2('sha256', $pass, $salt, 64000, 512);
    echo '<!--' . htmlstr($res . '$' . $randsalt) . '--><br>\n';
}

include("connection.php");
include("helper.php");

if(isset($_SESSION["username"])) {
    header("Location: " . "events.php");
	die();
}

if(isset($_POST["username"])) {
/*	if($_POST["create"] == "true") {
		$salt = mcrypt_create_iv(64, MCRYPT_DEV_URANDOM);
		$res = pbkdf2('sha256', $_POST["password"], $salt, 64000, 512);

		$get_info_page_stmt = $db->prepare("INSERT into users(username, password) values (:username,:password)");
		$get_info_page_stmt->bindValue(":username",$_POST["username"]);
		$get_info_page_stmt->bindValue(":password",$res."$".bin2hex($salt));
		$get_info_page_stmt->execute();

		header("Location: ".full_url($_SERVER));
		die();
	}
*/
	$get_info_page_stmt = $db->prepare("SELECT password FROM users where username=:username");
	$get_info_page_stmt->bindValue(":username",$_POST["username"]);
	$get_info_page_stmt->execute();

	if(!$get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
		echo "Error: Username and/or password is incorrect";
		die();
	}

	$split = explode('$', $get_info_page_res["password"]);
	$salt =  pack("H*", $split[1]);
	$password = $split[0];

	$res = pbkdf2('sha256', $_POST["password"], $salt, 64000, 512);

	if(my_hash_equals($password, $res)) {
		$_SESSION["username"] = $_POST["username"];
		header("Location: ". stripFileName() . "/events.php");
		die();
	}
	else {
		echo "Error: Username and/or password is incorrect<br>\n";
		die();
	}
}
?>

<html>
<head>
    <title>LightSys Event App Data Generator</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="scripts/scripts.js"></script>

    <style>
        body {
            text-align: center;
        }

        form {
            padding: 60px;
            background-color: #3E52A9;
            display: inline-block;
            color: white;
            -webkit-box-shadow: 5px 5px 5px 0px rgba(0,0,0,0.75);
            -moz-box-shadow: 5px 5px 5px 0px rgba(0,0,0,0.75);
            box-shadow: 5px 5px 5px 0px rgba(0,0,0,0.75);
            margin-top: 200px;
        }


    </style>
</head>
<body>
    <form id="form" method="post">
        <input type="hidden" name="create" value="false">
        Username:<input name="username" type="text"><br>
        Password:&nbsp;<input name="password" type="password"><br>
        <input type="submit" value="Login"/>
       <!-- <a onclick="createUser()">Create</a> -->
    </form>
</body>
<!-- <script>
	function createUser() {
		$("#form > input[name='create']").val("true");
		$("#form").submit();
	}
</script> -->
</html>
