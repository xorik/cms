<?php


// Autoloader, hooks, class and ajax files cache
class LocalCache
{
	const CACHE_FILE = "cache/localcache.json";

	static public $class;
	static public $modules;
	static public $ajax;
	static public $route;
	static private $init = false;


	static public function init()
	{
		if( is_file(self::CACHE_FILE) )
		{
			$tmp = json( file_get_contents(self::CACHE_FILE) );
			self::$class = $tmp["class"];
			self::$modules = $tmp["modules"];
			self::$ajax = $tmp["ajax"];
			self::$route = $tmp["route"];
		}
		else
			self::scan();

		spl_autoload_register( __CLASS__. "::autoload" );
	}


	static public function scan()
	{
		if( self::$init )
			return;

		$class = $modules = $ajax = $route = array();

		// Scan class
		$dirs = array_merge( glob("extra/*/class"), array("modules/class") );
		foreach( mask_search($dirs, "*.php") as $file )
		{
			$data = file_get_contents( $file );
			$ns = preg_match('/^namespace\s+([\w\\\\]+)/m', $data, $m) ? $m[1]."\\" : "";
			if( preg_match_all('/^(abstract\s+|)(class|interface|trait)\s+([\w_]+)\s/m', $data, $m) )
			{
				foreach( $m[3] as $c )
				{
					$c = strtolower( $ns.$c );
					if( !isset($class[$c]) )
						$class[$c] = $file;
				}
			}
			unset($data);
		}

		// Scan modules
		foreach( array("extra/*/*/*.php", "modules/hooks/*/*.php") as $glob )
		{
			foreach( glob($glob) as $file )
			{
				if( preg_match("/\/([\w_-]+)\/[\w_-]+\.php$/", $file, $m) && $m[1]!="class" && $m[1]!="ajax" && $m[1]!="route" )
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

		// Scan router rules
		foreach( glob("extra/*/route/route") as $file )
		{
			foreach( explode("\n", file_get_contents($file)) as $line )
			{
				if( !trim($line) )
					continue;

				$a = explode(" ", $line);
				if( count($a) < 2 )
					throw new Exception( "Incorrect route line: '$line' in file '$file'" );

				$route[] = $a[0];
				$route[] = cur_dir($file) ."/". $a[1];
			}
		}

		self::$class = $class;
		self::$modules = $modules;
		self::$ajax = $ajax;
		self::$route = $route;
		$res = file_put_contents( self::CACHE_FILE, json(array("class"=>$class, "modules"=>$modules, "ajax"=>$ajax, "route"=>$route), 1) );
		if( $res === false )
			throw new Exception( "Error saving cache file" );

		self::$init = true;
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

		if( !self::$init )
		{
			self::scan();
			return self::autoload( $class );
		}

		return false;
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
			if( is_file( $module ) )
				require_once( $module );
			else
				// TODO: fix me
				LocalCache::scan();
		}

		return true;
	}
}


Class Hook
{
	static protected $hooks = array();

	static public function add( $hook, $func, $pos=500 )
	{
		if( !is_callable($func) )
			throw new Exception( "$func is not callable" );

		// Find first empty position for hook
		if( !empty(self::$hooks[$hook]) )
		{
			while( array_key_exists($pos, self::$hooks[$hook]) )
			{
				$pos++;
			}
		}

		$data = array_slice( func_get_args(), 3 );

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
			return null;

		// Sort by position
		ksort( self::$hooks[$hook] );

		foreach( self::$hooks[$hook] as $hook )
		{
			$a = array_merge( array($arg), $hook["data"] );
			$res = call_user_func_array( $hook["func"], $a );
			
			if( $res !== null )
				return $res;
		}

		return null;
	}

	static public function dump()
	{
		$tmp = array();
		foreach( self::$hooks as $name=>$list )
		{
			$a = "<b>$name</b><br>";
			foreach( $list as $pos=>$l )
			{
				if( is_string($l["func"]) )
					$v = $l["func"];
				else
				{
					$r = new ReflectionFunction( $l["func"] );
					$v = "Closure:". cur_dir($r->getFileName(), 1) ." (". $r->getStartLine() ."-". $r->getEndLine() .")";
				}
				$a .= "$pos => $v<br>";
			}

			$tmp[] = $a;
		}

		echo "<pre>";
		echo implode( "<br><br>", $tmp );
		echo "</pre>";
	}
}
