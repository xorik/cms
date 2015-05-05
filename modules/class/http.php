<?php


define( "HTTP_STATUS_OK", 200 );
define( "HTTP_MOVED_PERM", 301 );
define( "HTTP_MOVED_TEMP", 302 );
define( "HTTP_ERROR_REQUEST", 400 );
define( "HTTP_ERROR_FORBIDDEN", 403 );
define( "HTTP_ERROR_NOT_FOUND", 404 );
define( "HTTP_ERROR_INTERNAL", 500 );
define( "HTTP_ERROR_UNAVAILABLE", 503 );



class Http
{
	static public $status = HTTP_STATUS_OK;

	static public function json()
	{
		header( "Content-Type: application/json; charset=utf-8" );
	}

	static public function reload()
	{
		self::redirect( $_SERVER["REQUEST_URI"] );
	}

	static public function redirect( $url, $code=HTTP_MOVED_TEMP )
	{
		Hook::run( "shutdown" );

		self::header( $code );
		header( "Location: $url" );
		die;
	}

	static public function end( $msg=null, $code=null )
	{
		Hook::run( "shutdown" );

		if( $code )
			self::header( $code );

		if( $msg )
			echo $msg;

		die;
	}

	static public function header( $code )
	{
		$text = array(
			200=>"OK",
			301=>"Moved Permanently",
			302=>"Moved Temporarily",
			400=>"Bad Request",
			403=>"Forbidden",
			404=>"Not Found",
			500=>"Internal Server Error",
			503=>"Service Unavailable"
		);

		if( !isset($text[$code]) )
			throw new Exception( "Unknown http code: $code" );

		self::$status = $code;

		header( $_SERVER["SERVER_PROTOCOL"] ." ". $code ." ". $text[$code] );
	}

	static public function ip()
	{
		return $_SERVER["REMOTE_ADDR"];
	}

	static public function ua()
	{
		return $_SERVER["HTTP_USER_AGENT"];
	}

	static public function host()
	{
		return $_SERVER["HTTP_HOST"];
	}

	static public function ipua()
	{
		static $ipua = false;
		if( $ipua )
			return $ipua;

		$ipua = md5( $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"] );
		return $ipua;
	}

	static public function url()
	{
		return "http". (empty($_SERVER["HTTPS"])?"":"s") ."://". $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	}
}
