<?php


define( "HTTP_STATUS_OK", 200 );
define( "HTTP_MOVED_PERM", 301 );
define( "HTTP_MOVED_TEMP", 302 );
define( "HTTP_ERROR_FORBIDDEN", 403 );
define( "HTTP_ERROR_NOT_FOUND", 404 );
define( "HTTP_ERROR_INTERNAL", 500 );
define( "HTTP_ERROR_UNAVAILABLE", 503 );



class Http
{
	static public $status = HTTP_STATUS_OK;


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

	static public function header( $code )
	{
		$text = array(
			200=>"OK",
			301=>"Moved Permanently",
			302=>"Moved Temporarily",
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
}
