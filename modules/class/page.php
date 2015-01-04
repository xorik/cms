<?php


class Page
{
	static public function init( $tmp, $reload )
	{
		// Get page id from $_GET[id] or main page
		if( isset($_GET["id"]) || !Router::$path )
		{
			$id = isset($_GET["id"]) ? (int)$_GET["id"] : (int)Config::get("main");
			$row = DB::row( "SELECT type, title FROM page WHERE id=$id" );
			if( $row )
			{
				// Reload to path
				if( $reload && $id!=Config::get("main") && $path=self::prop($id, "path") )
				{
					header( "Location: ". Router::$root . $path );
					die;
				}
			}
			else
				$id = null;
		}
		// Get page id from path
		else
		{
			$id = DB::one( "SELECT id FROM prop WHERE field='path' AND value=". DB::escape(Router::$path) );
			if( $id )
				$row = DB::row( "SELECT type, title FROM page WHERE id=$id" );
			else
				$id = null;
		}

		Heap::set( "id", $id );
		if( !$id )
			return;

		Heap::set( "title", $row["title"] );
		Heap::set( "type", $row["type"] );
	}

	static public function text( $id )
	{
		return DB::one( "SELECT text FROM page WHERE id=". DB::escape($id) );
	}

	static public function path( $id )
	{
		if( $id == Config::get("main") )
			return Router::$root;
		if( $path = self::prop($id, "path") )
			return Router::$root . $path;
		else
			return Router::$root ."?id=". $id;
	}

	static public function prop( $id, $key, $value=null )
	{
		$where = "id=". DB::escape($id) ." AND field=". DB::escape($key);
		// Get prop
		if( $value === null )
			return DB::one( "SELECT value FROM prop WHERE $where" );
		// Set prop
		elseif( $value )
			DB::insert( "prop", array("id"=>$id, "field"=>$key, "value"=>$value), 1 );
		else
			DB::delete( "prop", $where );
	}
}
