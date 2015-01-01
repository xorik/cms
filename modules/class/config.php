<?php


class Config
{
	const CONFIG_FILE = "config.json";
	static private $config;
	
	static public function init()
	{
		self::$config = json( file_get_contents(self::CONFIG_FILE) );
	}
	
	static public function save()
	{
		$res = file_put_contents( self::CONFIG_FILE, json(self::$config, 1) );
		if( !$res )
			throw new Exception( "Can't save to ". self::CONFIG_FILE );
	}
	
	static public function get( $key, $subkey=false )
	{
		if( !$subkey && isset(self::$config[$key]) )
			return self::$config[$key];
		elseif( $subkey && isset(self::$config[$key][$subkey]) )
			return self::$config[$key][$subkey];
		else
			return false;
	}
	
	static public function set( $key, $value )
	{
		self::$config[$key] = $value;
	}
}