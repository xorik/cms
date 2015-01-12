<?php


define( "PAGE_TYPE_UNKNOWN", 0 );
define( "PAGE_TYPE_CONTENT", 1 );
define( "PAGE_TYPE_ADMIN", 2 );
define( "PAGE_TYPE_AJAX", 3 );
define( "PAGE_TYPE_FILE", 4 );



class Router
{
	static public $root;
	static public $path;
	static public $type = PAGE_TYPE_UNKNOWN;


	static public function init()
	{
		// Parse URL
		$tmp = self::$root = str_replace( "index.php", "", $_SERVER["PHP_SELF"] );
		Heap::set( "root", self::$root );
		$tmp = str_replace( $tmp, "", $_SERVER["REQUEST_URI"] );
		$tmp = explode( "?", $tmp );
		self::$path = $tmp[0];

		// Route hooks
		Hook::add( "route", "Router::default_route", 999 );
		Module::load( "route" );
		Hook::run( "route", urldecode(self::$path) );
	}
	
	static public function default_route( $path )
	{
		if( isset($_GET["id"]) )
			Hook::add( "init", "Page::init", 60, 0 );

		// Ajax handler
		if( preg_match("|ajax/(.+)|", $path, $m) )
		{
			if( !isset(LocalCache::$ajax[$m[1]]) || !is_file($file=LocalCache::$ajax[$m[1]]) )
			{
				Http::header( HTTP_ERROR_NOT_FOUND );
				echo "Ajax handler for request '{$m[1]}' is not found";

				return;
			}

			self::$type = PAGE_TYPE_AJAX;
			require( $file );

			return;
		}
		// Get file
		elseif( preg_match("|file/(.+)|", $path, $m) )
		{
			self::$type = PAGE_TYPE_FILE;
			Hook::run( "init" );
			File::download( $m[1] );

			return;
		}

		Head::$head[] = "<meta charset='utf-8'>";

		// Admin or config site
		if( $path == "admin" || $path == "config" )
		{
			self::$type = PAGE_TYPE_ADMIN;
			Hook::add( "init", "Session::init", 100 );
			Hook::add( "init", "Auth::init", 200 );
			Module::load( $path );
			$res = Hook::run( "init" );
			$tpl = $res ? $res : "modules/templates/admin.tpl";
		}
		// Content
		else
		{
			self::$type = PAGE_TYPE_CONTENT;
			if( !isset($_GET["id"]) )
				Hook::add( "init", "Page::init", 60, 1 );

			Module::load( "content" );
			$res = Hook::run( "init" );
			$tpl = $res ? $res : "templates/main.tpl";
		}

		// Content template
		$content = Template::get( $tpl, self::$type==PAGE_TYPE_CONTENT );
		// TODO: save content for hook

		echo Head::get();
		echo $content;

		if( Head::$new_js )
			echo Head::scripts();
	}
}
