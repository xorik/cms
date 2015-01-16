<?php


class Template
{
	static protected $dir;

	static public function show( $file, $recursive_check=0, $heap=false )
	{
		if( !file_exists($file) )
			throw new Exception( "Template file is not found: $file" );

		// Cache filename
		$cache_file = cur_dir( $file, 1 );
		$cache_file = "cache/". str_replace( array(".tpl", "/"), array(".php", "."), $cache_file );

		// Cache level
		$cache = Config::get("cache");

		// Update cache if needed
		if( $cache == CACHE_LEVEL_FORCE && file_exists($cache_file) );
		elseif( $cache == CACHE_LEVEL_CHECK )
		{
			$cache_time = is_file( $cache_file ) ? filemtime( $cache_file ) : 0;

			//One file
			if( !$recursive_check )
			{
				$file_time = filemtime( $file );

				// Cache file not found or different mtime
				if( $file_time!=$cache_time )
					self::cache_update( $file, $cache_file, $file_time );
			}
			// Directory check
			else
			{
				// Max mtime
				$mtime = 0;
				foreach( glob(dirname($file) ."/*.tpl") as $f )
				{
					$mtime = max( $mtime, filemtime($f) );
				}

				if( $cache_time != $mtime )
					self::cache_update( $file, $cache_file, $mtime );
			}
		}
		// Not exists or cache is off
		else
			self::cache_update( $file, $cache_file, time() );

		extract( $heap ? $heap : Heap::$heap, EXTR_REFS );
		$error_level = error_reporting();
		// Disable notices
		error_reporting( $error_level & ~E_NOTICE );
		require( $cache_file );
		error_reporting( $error_level );
	}

	static public function get( $file, $recursive_check=0, $heap=false )
	{
		ob_start();
		self::show( $file, $recursive_check, $heap );
		return ob_get_clean();
	}

	static public function cache_update( $file, $cache_file, $mtime )
	{
		self::$dir = dirname( $file );

		$res = file_put_contents( $cache_file, self::template_parse(file_get_contents($file)) );
		if( !$res )
			throw new Exception( "Can't create cache file: $cache_file" );

		touch( $cache_file, $mtime );
	}

	static public function template_parse( $text )
	{
		// Include
		$text = preg_replace_callback( "/{include ([\w_-]+)}/i", __CLASS__ ."::include_callback", $text );

		$regex = array(
			"/{IF ([^}]+)}/i", // IF, ELSE, ELSEIF
			"/{ELSEIF ([^}]+)}/i",
			"/{ELSE}/i",
			"/{\/IF}/i",
			"/{EACH ([^}]+)}/i", // FOREACH
			"/{\/EACH}/i",
			"/{(\S.+?)}/", // Echo variable, func, method etc
			"/{{/", // PHP code
			"/}}/",
			"/\/\*.*?\*\//s" // Comment
		);
		$replace = array(
			"<?php if(\\1): ?>",
			"<?php elseif(\\1): ?>",
			"<?php else: ?>",
			"<?php endif ?>",
			"<?php foreach(\\1): ?>",
			"<?php endforeach ?>",
			"<?php echo \\1 ?>",
			"<?php ",
			"?>",
			""
		);

		return preg_replace( $regex, $replace, $text );
	}

	static public function include_callback( $m )
	{
		$file = self::$dir ."/{$m[1]}.tpl";
		if( !file_exists($file) )
			throw new Exception( "Template file is not found for include: $file" );

		static $count = array();
		if( !isset($count[$m[1]]) )
			$count[$m[1]] = 1;
		elseif( $count[$m[1]] < 30 )
			$count[$m[1]]++;
		else
			throw new Exception( "Recursion detected in $file" );

		return self::template_parse( file_get_contents($file) );
	}
}
