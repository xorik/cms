<?php


// TODO: custom timeout


Session::init();


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
	static public $mtime = 0;


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
			self::$mtime = filemtime( $file );
		}
		// Create new session
		else
		{
			$key =  sha1( microtime(true) . rand() );
			setcookie( self::SESSION_COOKIE, $key, null, Router::$root );
			$_COOKIE[self::SESSION_COOKIE] = $key;
			self::$changed = true;
			self::$mtime = time();
		}

		Hook::add( "shutdown", "Session::save", 900 );
		self::$init = true;
	}

	static public function touch()
	{
		touch( self::SESSION_DIR ."/sess-".$_COOKIE[self::SESSION_COOKIE] );
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

	static public function __callStatic( $name, $args )
	{
		// Set
		if( count($args) == 1 )
		{
			self::$sess[$name] = $args[0];
			self::$changed = true;
		}
		// Get
		elseif( count($args)==0 && isset(self::$sess[$name]) )
			return self::$sess[$name];
		else
			return false;
	}
}
