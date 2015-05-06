<?php


// TODO: custom timeout


$class = Config::get("session");
call_user_func( array($class?$class:"Session", "init") );


class Session
{
	const COOKIE = "sess";
	const COOKIE_PREG = '/\w{40}/';
	const COOKIE_TTL = 0;
	const SESSION_PREFIX = "cache/sess/sess-";
	const CLEANUP_TIME = 86400;
	const CLEANUP_CHANCE = 1000;

	static protected $sess = array();
	static protected $changed = false;
	static public $mtime = 0;


	static public function init()
	{
		// Incorrect cookie should be reset
		if( isset($_COOKIE[static::COOKIE]) && !preg_match(self::COOKIE_PREG, $_COOKIE[static::COOKIE]) )
			unset( $_COOKIE[static::COOKIE] );

		// Load session
		if( isset($_COOKIE[static::COOKIE]) && is_file($file=static::SESSION_PREFIX.$_COOKIE[static::COOKIE]) )
		{
			self::$sess = json( file_get_contents($file) );
			self::$mtime = filemtime( $file );
		}
		else
			self::$mtime = time();

		Hook::add( "shutdown", get_called_class()."::save", 900 );
	}

	static public function touch()
	{
		touch( static::SESSION_PREFIX.$_COOKIE[static::COOKIE] );
	}

	static public function delete( $key )
	{
		if( !isset(self::$sess[$key]) )
			return;

		unset( self::$sess[$key] );
		self::$changed = true;
	}


	static public function save()
	{
		if( self::$changed )
		{
			// Create new session
			if( self::$sess )
			{
				if( !isset($_COOKIE[static::COOKIE]) )
				{
					$key =  sha1( microtime(true) . rand() );
					$ttl = static::COOKIE_TTL ? time()+static::COOKIE_TTL : 0;
					setcookie( static::COOKIE, $key, $ttl, ROOT );
					$_COOKIE[static::COOKIE] = $key;
				}

				// Create directory
				if( !is_dir($dir=dirname(static::SESSION_PREFIX)) )
				{
					$res = mkdir( $dir, 0700, true );
					if( !$res )
						throw new Exception( "Can't create directory $dir" );
				}

				$res = file_put_contents( static::SESSION_PREFIX.$_COOKIE[static::COOKIE], json(self::$sess) );
				if( !$res )
					throw new Exception( "Can't save session" );
			}
			// Clean cookie
			elseif( isset($_COOKIE[static::COOKIE]) )
			{
				setcookie( static::COOKIE, "" );
				if( is_file($file=static::SESSION_PREFIX.$_COOKIE[static::COOKIE]) )
					unlink( $file );
			}
		}

		// Cleanup
		if( static::CLEANUP_CHANCE && rand(1, static::CLEANUP_CHANCE)==1 )
		{
			$mintime = time() - static::CLEANUP_TIME;
			foreach( glob(static::SESSION_PREFIX."*") as $file )
			{
				if( filemtime($file) < $mintime )
					unlink( $file );
			}
		}
	}

	static public function __callStatic( $name, $args )
	{
		// Set
		if( count($args) )
		{
			self::$sess[$name] = $args[0];
			self::$changed = true;

			return null;
		}
		// Get
		elseif( isset(self::$sess[$name]) )
			return self::$sess[$name];
		else
			return false;
	}
}
