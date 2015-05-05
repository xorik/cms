<?php


define( "PAGE_TYPE_UNKNOWN", 0 );
define( "PAGE_TYPE_CONTENT", 1 );
define( "PAGE_TYPE_ADMIN", 2 );
define( "PAGE_TYPE_AJAX", 3 );
define( "PAGE_TYPE_JSON", 4 );
define( "PAGE_TYPE_FILE", 5 );



class Router
{
	static public $path;
	static public $type = PAGE_TYPE_UNKNOWN;


	static public function init()
	{
		// Parse URL
		$tmp = preg_replace( "/". preg_quote(ROOT, "/") ."/", "", $_SERVER["REQUEST_URI"], 1 );
		$tmp = explode( "?", $tmp );
		self::$path = urldecode( $tmp[0] );

		// Route hooks
		Hook::add( "route", "Router::default_route", 999 );
		$route = LocalCache::$route;
		for( $i=0; $i<count($route); $i+=2 )
		{
			if( $route[$i][0] == "/" )
			{
				if( preg_match($route[$i], self::$path) )
					require_once( $route[$i+1] );
			}
			elseif( $route[$i] == self::$path )
				require_once( $route[$i+1] );
		}

		Hook::run( "route", self::$path );
	}
	
	static public function default_route( $path )
	{
		if( isset($_GET["id"]) && $_GET["id"] )
			Hook::add( "init", "Page::init", 60 );

		// Ajax handler
		if( preg_match("/^(ajax|json)\/(.+)$/", $path, $m) )
		{
			$json = $m[1] == "json";
			if( $json )
				Http::json();

			if( !isset(LocalCache::$ajax[$m[2]]) || !is_file($file=LocalCache::$ajax[$m[2]]) )
			{
				Http::header( HTTP_ERROR_NOT_FOUND );
				$msg = "Ajax handler for request '{$m[2]}' is not found";
				echo $json ? json(array("error"=>$msg)) : $msg;

				return;
			}

			self::$type = $json ? PAGE_TYPE_JSON : PAGE_TYPE_AJAX;
			require( $file );

			return;
		}
		// Get file
		elseif( preg_match("|^file/(\d+)$|", $path, $m) )
		{
			self::$type = PAGE_TYPE_FILE;
			Hook::run( "init" );
			File::download( $m[1] );

			return;
		}

		// Admin or config site
		if( $path == "admin" || strpos($path, "config")===0 )
		{
			self::$type = PAGE_TYPE_ADMIN;
			Hook::add( "init", "Auth::init", 200 );

			$content = $path=="admin" ? "admin" : "config";
			Module::load( "secure" );
			Module::load( $content );
			Heap::content( $content );
			$res = Hook::run( "init" );
			$tpl = $res ? $res : "modules/templates/admin.tpl";
		}
		// Content
		else
		{
			self::$type = PAGE_TYPE_CONTENT;
			if( !isset($_GET["id"]) || !$_GET["id"] )
				Hook::add( "init", "Page::init", 60 );

			Module::load( "content" );
			$res = Hook::run( "init" );
			$tpl = $res ? $res : "templates/main.tpl";
		}

		// Content template
		Template::html( $tpl, self::$type==PAGE_TYPE_CONTENT );
		// TODO: save content for hook
	}
}
