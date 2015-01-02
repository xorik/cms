<?php


class Router
{
	static public $root;

	static public function init()
	{
		$tmp = self::$root = str_replace( "index.php", "", $_SERVER["PHP_SELF"] );
		$tmp = str_replace( $tmp, "", $_SERVER["REQUEST_URI"] );
		$tmp = explode( "?", $tmp );

		// Route hooks
		Hook::add( "route", "Router::default_route", 999 );
		Module::load( "route" );
		Hook::run( "route", $tmp[0] );
	}
	
	static public function default_route( $path )
	{
		
	}
}