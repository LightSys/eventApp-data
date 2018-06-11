<?php 
// Retrieved from https://stackoverflow.com/questions/6768793/get-the-full-url-in-php
function url_origin( $s, $use_forwarded_host = false ) {
	$ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
	$sp       = strtolower( $s['SERVER_PROTOCOL'] );
	$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
	$port     = $s['SERVER_PORT'];
	$port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
	$host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
	$host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
	return $protocol . '://' . $host;
}

function full_url( $s, $use_forwarded_host = false ) {
	return url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
}
function whitelistChars($allowedChars,$testString){
	/* made by judson hayes
	
	returns 0 on any string that contains charecters not in the allowedChars array/regular expression
	returns 1 on a string that only contains charecters from the allowedChars array
	allowedChars must be a pattern able to be interpreted by preg_replace or the integer 1, in the special case of
	the integer 1, it allows  alphabetical characters without special annotations, numbers, and the '-' symbol. 
		

	
	*/
	
	if($allowedChars==1){
		$testString=strtolower($testString);
		$allowedChars=array('/q/','/w/','/e/','/r/','/t/','/y/','/u/','/i/','/o/','/p/','/a/','/s/','/d/','/f/','/g/','/h/','/j/','/k/','/l/','/z/','/x/','/c/','/v/','/b/','/n/','/m/','/1/','/2/','/3/','/4/','/5/','/6/','/7/','/8/','/9/','/0/','/-/');
	        $result=preg_replace($allowedChars,'',$testString);

       		 if(!$result){
               		 return 1;
       		 }
       		 else return 0;		
	}

        $result=preg_replace($allowedChars,'',$testString);

	if(!$result){
		return 1;
	}
	else return 0;
}


function json_encode_noescape($a=false)
{
	if (is_null($a)) return 'null';
	if ($a === false) return 'false';
	if ($a === true) return 'true';
	if (is_scalar($a))
	{
		if (is_float($a))
		{
			// Always use "." for floats.
			return floatval(str_replace(",", ".", strval($a)));
		}

		if (is_string($a))
		{

			static $jsonReplaces = array(array("\\", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
			return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
		}
		else {
			return $a;
		}
	}
	$isList = true;
	for ($i = 0, reset($a); $i < count($a); $i++, next($a))
	{
		if (key($a) !== $i)
		{
			$isList = false;
			break;
		}
	}
	$result = array();
	if ($isList)
	{
		foreach ($a as $v) $result[] = json_encode_noescape($v);
		return '[' . join(',', $result) . ']';
	}
	else
	{
		foreach ($a as $k => $v) $result[] = (is_string($k) ? json_encode_noescape($k) : '"'.json_encode_noescape($k). '"') .':'.json_encode_noescape($v);
			return '{' . join(',', $result) . '}';
	}
}
function secure(){
	
	$timelimit=12;
	$tempTime=time();
	//include("templates/check-event-exists.php");
	include("connection.php");

	//decide realitive path to logout
	
	if(getParentDir()=="edit"){
		$logoutpath=getbasedir()."/templates/logout.php";
	}
	else{
		$logoutpath=getbasedir()."/templates/logout.php";
	}

	$get_event_stmt = $db->prepare("SELECT admin FROM event WHERE ID =:id");

	$tempid= $_GET["id"];	
	if(!whitelistChars(1,$tempid)){
		
		header("Location: http:// ".$logoutpath);
	}
		
        $get_event_stmt->bindValue(":id",$tempid);

        $get_event_stmt->execute();



        $get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);




        if(count($get_event_res) != 1) {

                die();

        }

	if($tempTime- $_SESSION['timestamp']>$timelimit){
		
		header("Location: http://".$logoutpath);
		
		die("session time expired");
	}

        $get_event_res = $get_event_res[0];
        if(!is_null($get_event_res['admin']) && (!isset($_SESSION["username"])||$get_event_res['admin']!=$_SESSION['username'])){
                
		header("Location: http://".$logoutpath);
                die();
        }

	$_SESSION['timestamp']=$tempTime;
}

function eventSecure(){
	$timelimit=12;
        $tempTime=time();
         //decide realitive path to logout

        
          if(getParentDir()=="edit"){
                $logoutpath=getbasedir()."/templates/logout.php";
        }
        else{
                $logoutpath=getbasedir()."/templates/logout.php";
        }
        

	
        include("connection.php");
	 if(!isset($_SESSION['username'])){
                 header_remove();
		 header("Location: http://".$logoutpath);
                 die();

          }
	if($tempTime- $_SESSION['timestamp']>$timelimit){
                header("Location: ".$logoutpath);

                die('session time expired');
        }
	       
	$_SESSION['timestamp']=$tempTime;

}

function getEventId() {
	global $db;
	if (!($get_event_stmt = $db->prepare("SELECT internal_ID FROM event where ID=:id"))) {
		die("error in get event stmt");
	}

	if(!$get_event_stmt->bindParam(":id",$_REQUEST['id'])) {
		die("error in bind param");
	}

	if(!$get_event_stmt->execute()) {
		die("error in execute");
	}

	$get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);

	if(count($get_event_res) != 1) {
		echo(count($get_event_res));
		die("error count != 1");
	}

	$get_event_res = $get_event_res[0];

	return $get_event_res["internal_ID"];
}

// Preserves and returns the full url with the file name at the end removed
function stripFileName() {
	$url = url_origin($_SERVER);
	$url .= dirname($_SERVER['REQUEST_URI']) . "/";

	return $url;
}

function getParentDir() {
	$url = url_origin($_SERVER);
	$url .= dirname(dirname($_SERVER['REQUEST_URI'])) . "/";

	return $url;
}
function getbasedir(){//this MUST be modified for deployment
	return "10.5.11.121/judson/eventApp-data"; 
}


?>
