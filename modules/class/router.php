<?php


class Router
{
	static public $root;
	static public $path;


	static public function init()
	{
		// Parse URL
		$tmp = self::$root = str_replace( "index.php", "", $_SERVER["PHP_SELF"] );
		$tmp = str_replace( $tmp, "", $_SERVER["REQUEST_URI"] );
		$tmp = explode( "?", $tmp );
		self::$path = $tmp[0];

		// Route hooks
		Hook::add( "route", "Router::default_route", 999 );
		Module::load( "route" );
		Hook::run( "route", urldecode(self::$path) );
	}
	
	static public function default_route( $path )
	{
		
	}
}