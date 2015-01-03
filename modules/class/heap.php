<?php

class Heap
{
	static public $heap = array();

	static public function get( $key )
	{
		if( isset(self::$heap[$key]) )
			return self::$heap[$key];

		return null;
	}

		static public function set( $key, $value )
	{
		self::$heap[$key] = $value;
	}
}
