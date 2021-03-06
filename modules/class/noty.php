<?php


class Noty
{
	static protected $init = false;
	static protected $list = array();

	static protected function add( $type, $text, $timeout )
	{
		$timeout = $timeout >= 1000 ? $timeout : $timeout*1000;

		self::$list[] = array( "type"=>$type, "text"=>$text, "timeout"=>$timeout );

		if( self::$init )
			return;

		Hook::add( "shutdown", __CLASS__ ."::store", 800 );
		self::$init = true;
	}

	static public function get( $subkey=true )
	{
		$list = self::$list;
		self::$list = array();

		return $subkey ? array("noty"=>$list) : $list;
	}

	static public function js()
	{
		// Get noty from session
		if( isset($_COOKIE["sess"]) && $sess=Session::noty() )
		{
			self::$list = array_merge( self::$list, $sess );
			Session::delete( "noty" );
		}

		if( empty(self::$list) )
			return;

		Head::noty();
		foreach( self::$list as $l )
		{
			Head::script( "noty(". json($l) .");" );
		}

		self::$list = array();
	}

	static public function store()
	{
		if( empty(self::$list) )
			return;

		Session::noty( self::$list );
		$class = Config::get("session");
		call_user_func( array($class?$class:"Session", "save") );
		self::$list = array();
	}

	static public function success( $text, $timeout=5 )
	{
		self::add( "success", $text, $timeout );
	}

	static public function info( $text, $timeout=5 )
	{
		self::add( "information", $text, $timeout );
	}

	static public function err( $text, $timeout=10 )
	{
		self::add( "warning", $text, $timeout );
	}
}
