<?php


class Page
{
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
