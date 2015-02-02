<?php


Configure::add( "base", "Основные настройки" );


class Configure
{
	static public $list = array();


	static public function add( $key, $title, $dev_only=false )
	{
		if( !$dev_only || Session::dev() )
			self::$list[$key] = $title;
	}

	static public function current()
	{
		static $cache = null;

		if( $cache !== null )
			return $cache;

		if( Router::$path == "config" )
			$cache = "base";
		elseif( preg_match("|^config/([\w_-]+)$|", Router::$path, $m) )
			$cache = $m[1];
		else
			$cache = false;

		return $cache;
	}
}
