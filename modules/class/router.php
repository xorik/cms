<?php


class Router
{
	static public function init()
	{
		// Route hooks
		$tmp = str_replace( "index.php", "", $_SERVER["PHP_SELF"] );
		$tmp = str_replace( $tmp, "", $_SERVER["REQUEST_URI"] );
		$tmp = explode( "?", $tmp );
		Hook::add( "route", "Router::default_route", 999 );
		Module::load( "route" );
		Hook::run( "route", $tmp[0] );
	}
	
	static public function default_route( $path )
	{
		
	}
}