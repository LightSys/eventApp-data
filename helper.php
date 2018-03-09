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

function stripFileName() {
	$url = full_url($_SERVER);
    $url = substr($url, 7);
    $token = strtok($url, "/");

    $val = substr_count($url, "/");

    $url = "";
    while ($val > 0 && $token !== false) {
        $url .= $token."/";
        $token = strtok("/");
        $val--;
	}
	
	return $url;
}
?>