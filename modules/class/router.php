<?php


class Router
{
	static public $root;
	static public $path;


	static public function init()
	{
		// Parse URL
		$tmp = self::$root = str_replace( "index.php", "", $_SERVER["PHP_SELF"] );
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
				header( "HTTP/1.0 404 Not Found" );
				echo "Ajax handler for request '{$m[1]}' is not found";

				return;
			}

			Hook::run( "init" );
			require( $file );

			return;
		}
		// Get file
		elseif( preg_match("|file/(.+)|", $path, $m) )
		{
			Hook::run( "init" );
			File::download( $m[1] );

			return;
		}

		Head::$head[] = "<meta charset='utf-8'>";

		// Admin or config site
		if( $path == "admin" || $path == "config" )
		{
			Hook::add( "init", "Session::init", 100 );
			Hook::add( "init", "Auth::init", 200 );
			Module::load( $path );
			Hook::run( "init" );

			$tpl = "modules/templates/admin.tpl";
		}
		// Content
		else
		{
			if( !isset($_GET["id"]) )
				Hook::add( "init", "Page::init", 60, 1 );

			Module::load( "content" );
			Hook::run( "init" );

			$tpl = Config::get("template");
			if( !$tpl )
				$tpl = "templates/main.tpl";
		}

		// Content template
		$content = Template::get( $tpl );
		// TODO: save content for hook

		echo Head::get();
		echo $content;

		if( Head::$new_js )
			echo Head::scripts();
	}
}
