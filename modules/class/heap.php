<?php

class Heap
{
	static public $heap = array();

	static public function __callStatic( $name, $args )
	{
		// Set
		if( count($args) == 1 )
			self::$heap[$name] = $args[0];
		// Get
		elseif( count($args)==0 && isset(self::$heap[$name]) )
			return self::$heap[$name];
		else
			return null;
	}
}
