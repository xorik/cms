<?php


class Noty
{
	static protected $list = array();

	static protected function add( $type, $text, $timeout )
	{
		self::$list[] = array( "type"=>$type, "text"=>$text, "timeout"=>$timeout );
	}

	static public function get( $subkey=true )
	{
		$list = self::$list;
		self::$list = array();

		return $subkey ? array("noty"=>$list) : $list;
	}

	static public function success( $text, $timeout=2000 )
	{
		self::add( "success", $text, $timeout );
	}

	static public function info( $text, $timeout=2000 )
	{
		self::add( "information", $text, $timeout );
	}

	static public function err( $text, $timeout=5000 )
	{
		self::add( "warning", $text, $timeout );
	}
}
