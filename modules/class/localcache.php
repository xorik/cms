<?php


// TODO: cache level: on, off, smart


// Autoloader, hooks, class and ajax files cache
class LocalCache
{
	const CACHE_FILE = "cache/localcache.php";

	static public $class;
	static public $modules;
	static public $ajax;
	static private $scan_complete = false;


	static public function init()
	{
		if( is_file(self::CACHE_FILE) )
		{
			$tmp = json( file_get_contents(self::CACHE_FILE) );
			self::$class = $tmp["class"];
		}
		else
			self::scan();

		spl_autoload_register( __CLASS__. "::autoload" );
	}


	static public function scan()
	{
		$class = $modules = $ajax = array();

		// Scan class
		foreach( array("extra/*/class/*.php", "modules/class/*.php") as $glob )
		{
			foreach( glob($glob) as $file )
			{
				if( preg_match_all("/^class ([\w_]+)\s/m", file_get_contents($file), $m) )
				{
					foreach( $m[1] as $c )
					{
						$c = strtolower( $c );
						if( !isset($class[$c]) )
							$class[$c] = $file;
					}
				}
			}
		}

		self::$class = $class;
		file_put_contents( self::CACHE_FILE, json(array("class"=>$class), 1) );
		self::$scan_complete = true;
	}

	static public function autoload( $class )
	{
		$class = strtolower($class);

		if( isset(self::$class[$class]) )
		{
			require( self::$class[$class] );
			if( class_exists($class) )
				return true;
		}

		if( !self::$scan_complete )
		{
			self::scan();
			return self::autoload( $class );
		}

		throw new Exception( "Class $class not found" );
	}
}