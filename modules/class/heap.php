<?php

class Heap
{
	static public $heap = array();

	static public function get( $key )
	{
		return self::$heap[$key];
	}

		static public function set( $key, $value )
	{
		self::$heap[$key] = $value;
	}
}
