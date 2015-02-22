<?php


Configure::group( "default", "Настройки", "wrench" );
Configure::group( "admin", "Админка", "lock" );
Configure::group( "server", "Окружение", "server" );
Configure::group( "dev", "Разработка", "gears" );
Configure::group( "stat", "Статистика", "line-chart" );


class Configure
{
	static public $groups = array();
	static public $list = array();


	static public function group( $key, $title, $icon=null )
	{
		self::$groups[$key] = array( $title, $icon );
	}

	static public function add( $key, $title, $icon=null, $sub=null, $dev_only=false )
	{
		if( !$sub )
			$sub = "default";

		if( !$dev_only || Session::dev() )
			self::$list[$sub][$key] = array( $title, $icon );
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
