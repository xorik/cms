<?php


Config::init();


class Config
{
	const CONFIG_FILE = "config/config.json";
	static private $config;
	
	static public function init()
	{
		if( !file_exists(self::CONFIG_FILE) )
			throw new Exception( "Config file ". self::CONFIG_FILE ." not found" );

		self::$config = json( file_get_contents(self::CONFIG_FILE) );
		if( self::$config === null )
			throw new Exception( "Config file parse error" );
	}
	
	static public function save( $noty = true )
	{
		$res = file_put_contents( self::CONFIG_FILE, json(self::$config, 1) );
		if( !$res )
			throw new Exception( "Can't save to ". self::CONFIG_FILE );

		if( $noty )
			Noty::success( "Настройки сохранены" );
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
