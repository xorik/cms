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
			self::$modules = $tmp["modules"];
			self::$ajax = $tmp["ajax"];
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

		// Scan modules
		foreach( array("extra/*/*/*.php", "modules/hooks/*/*.php") as $glob )
		{
			foreach( glob($glob) as $file )
			{
				if( preg_match("/\/([\w_-]+)\/[\w_-]+\.php$/", $file, $m) && $m[1]!="class" && $m[1]!="ajax" )
				{
					$modules[$m[1]][] = $file;
				}
			}
		}

		// Scan ajax files
		foreach( array("extra/*/ajax/*.php", "modules/ajax/*.php") as $glob )
		{
			foreach( glob($glob) as $file )
			{
				if( preg_match("/\/([\w_-]+)\.php$/", $file, $m) )
				{
					$ajax[$m[1]] = $file;
				}
			}
		}

		self::$class = $class;
		self::$modules = $modules;
		self::$ajax = $ajax;
		file_put_contents( self::CACHE_FILE, json(array("class"=>$class, "modules"=>$modules, "ajax"=>$ajax), 1) );
		self::$scan_complete = true;
	}

	static public function autoload( $class )
	{
		$class = strtolower($class);

		if( isset(self::$class[$class]) )
		{
			if( is_file((self::$class[$class])) )
			{
				require_once( self::$class[$class] );
				if( class_exists($class) )
					return true;
			}
		}

		if( !self::$scan_complete )
		{
			self::scan();
			return self::autoload( $class );
		}

		throw new Exception( "Class $class not found" );
	}
}


Class Module
{
	static public function load( $module )
	{
		if( empty(LocalCache::$modules[$module]) )
			return false;

		foreach( LocalCache::$modules[$module] as $module )
		{
			require_once( $module );
		}

		return true;
	}
}


Class Hook
{
	static protected $hooks = array();

	static public function add( $hook, $func, $pos=500, $data=null )
	{
		if( !function_exists($func) )
			throw new Exception( "Func $func not found for hook $hook" );

		// Find first empty position for hook
		if( !empty(self::$hooks[$hook]) )
		{
			while( array_key_exists($pos, self::$hooks[$hook]) )
			{
				$pos++;
			}
		}
		self::$hooks[$hook][$pos] = array( "func"=>$func, "data"=>$data );
	}

	static public function remove( $hook, $func=true, $data=null )
	{
		if( !isset(self::$hooks[$hook]) )
			return;
		
		if( $func === true )
			self::$hooks[$hook] = array();
		else
		{
			foreach( self::$hooks[$hook] as $k=>$v )
			{
				if( $v["func"]==$func && ($data===null || $v["data"]===$data) )
					unset( self::$hooks[$hook][$k] );
			}
		}
	}

	static public function run( $hook, $arg=null )
	{
		if( empty(self::$hooks[$hook]) )
			return;

		// Sort by position
		ksort( self::$hooks[$hook] );

		foreach( self::$hooks[$hook] as $hook )
			$hook["func"]( $arg, $hook["data"] );
	}
}
