<?php


Config::init();


class JSONConfig
{
	const FILE = "";
	const REQUIRED = false;
	const PRETTY = false;
	const NOTY = true;

	static private $config = array();

	static public function init()
	{
		$class = get_called_class();

		if( !file_exists(static::FILE) )
		{
			if( static::REQUIRED )
				throw new Exception( __METHOD__.": file ". self::FILE ." is not found" );

			self::$config[$class] = array();
			return;
		}

		self::$config[$class] = json( file_get_contents(static::FILE) );
		if( self::$config[$class] === null )
			throw new Exception( __METHOD__.": config file parse error" );
	}

	static public function save()
	{
		$res = file_put_contents( static::FILE, json(self::$config[get_called_class()], static::PRETTY) );
		if( !$res )
			throw new Exception( __METHOD__.": can't save config to ". static::FILE );

		if( static::NOTY )
			Noty::success( "Настройки сохранены", 2 );
	}

	static public function get( $key, $subkey=false )
	{
		$class = get_called_class();

		if( !$subkey && isset(self::$config[$class][$key]) )
			return self::$config[$class][$key];
		elseif( $subkey && isset(self::$config[$class][$key][$subkey]) )
			return self::$config[$class][$key][$subkey];
		else
			return false;
	}

	static public function get_all()
	{
		return self::$config[get_called_class()];
	}

	static public function set( $key, $value )
	{
		self::$config[get_called_class()][$key] = $value;
	}

	static public function set_all( $value )
	{
		self::$config[get_called_class()] = $value;
	}
}


class Config extends JSONConfig
{
	const FILE = "config/config.json";
	const REQUIRED = true;
	const PRETTY = true;
}
