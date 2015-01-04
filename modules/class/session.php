<?php


// TODO: custom timeout


class Session
{
	const SESSION_COOKIE = "sess";
	const SESSION_COOKIE_PREG = "/\w{40}/";
	const SESSION_DIR = "cache/sess";
	const CLEANUP_TIME = 86400;
	const CLEANUP_CHANCE = 1000;

	static protected $init = false;
	static protected $sess = array();
	static protected $changed = false;


	static public function init()
	{
		if( self::$init )
			return;

		// Incorrect cookie should be reset
		if( isset($_COOKIE[self::SESSION_COOKIE]) && !preg_match(self::SESSION_COOKIE_PREG, $_COOKIE[self::SESSION_COOKIE]) )
			unset( $_COOKIE[self::SESSION_COOKIE] );

		// Load session
		if( isset($_COOKIE[self::SESSION_COOKIE]) && is_file($file=self::SESSION_DIR ."/sess-".$_COOKIE[self::SESSION_COOKIE]) )
		{
			self::$sess = json( file_get_contents($file) );
		}
		// Create new session
		else
		{
			$key =  sha1( microtime(true) . rand() );
			setcookie( self::SESSION_COOKIE, $key, null, Router::$root );
			$_COOKIE[self::SESSION_COOKIE] = $key;
			self::$changed = true;
		}

		self::$init = true;
	}

	static public function get( $key )
	{
		if( isset(self::$sess[$key]) )
			return self::$sess[$key];
		else
			return false;
	}

	static public function set( $key, $value )
	{
		self::$sess[$key] = $value;
		self::$changed = true;
	}

	static public function save()
	{
		if( self::$changed )
		{
			// Create directory
			if( !is_dir(self::SESSION_DIR) )
			{
				$res = mkdir( self::SESSION_DIR, 0700 );
				if( !$res )
					throw new Exception( "Can't create directory ". self::SESSION_DIR );
			}

			$res = file_put_contents( self::SESSION_DIR ."/sess-".$_COOKIE[self::SESSION_COOKIE], json(self::$sess) );
			if( !$res )
				throw new Exception( "Can't save session" );
		}

		// Cleanup
		if( rand(1, self::CLEANUP_CHANCE)==1 )
		{
			$mintime = time() - self::CLEANUP_TIME;
			foreach( glob(self::SESSION_DIR ."/sess-*") as $file )
			{
				if( filemtime($file) < $mintime )
					unlink( $file );
			}
		}
	}
}
