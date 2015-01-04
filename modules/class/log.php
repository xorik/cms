<?php


class Log
{
	static public function add( $type, $data, $hash=null, $inc=0 )
	{
		if( is_array($data) )
			$data = json( $data );

		if( !is_string($hash) )
			$hash = str_pad( "", 32, "0" );

		// Increment, if exists with updating data
		if( $inc )
		{
			$id = DB::one( "SELECT id FROM log WHERE type=". DB::escape($type) ." AND hash=". DB::escape($hash) );
			if( $id )
			{
				self::inc( $type, $hash );
				DB::update( "log", array("data"=>$data), "id=$id" );

				return $id;
			}
		}

		return DB::insert( "log", array("type"=>$type, "hash"=>$hash, "data"=>$data) );
	}

	static public function inc( $type, $hash )
	{
		return DB::update( "log", array("count"=>"`count`+1"), "type=". DB::escape($type) ." AND hash=". DB::escape($hash), 1 );
	}

	static public function delete( $id )
	{
		return DB::delete( "log", "id=$id" );
	}

	static public function get( $type, $lim=100, $asc=false )
	{
		$order = $asc ? "ASC" : "DESC";
		$rows = DB::all( "SELECT id, UNIX_TIMESTAMP(date) as date, `count`, data FROM log WHERE type=". DB::escape($type) ." ORDER BY id $order LIMIT $lim" );

		// Auto decode json
		foreach( $rows as &$row )
		{
			if( $row["data"][0] == "{" )
				$row["data"] = json( $row["data"] );
		}

		return $rows;
	}

	static public function count( $type )
	{
		return DB::one( "SELECT COUNT(*) FROM log WHERE type=". DB::escape($type) );
	}
}
