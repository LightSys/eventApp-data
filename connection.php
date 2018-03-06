<?php 
// Remove for production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new PDO('mysql:host=localhost;dbname=eventdb', 'root', 'password');
date_default_timezone_set("America/Denver");

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
		else
			return $a;
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
		foreach ($a as $k => $v) $result[] = json_encode_noescape($k).':'.json_encode_noescape($v);
			return '{' . join(',', $result) . '}';
		}
	}

	?>