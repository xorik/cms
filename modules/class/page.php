<?php


class Page
{
	static protected $id = null;
	static protected $cache = null;


	static public function init( $tmp, $reload )
	{
		// Get page id from $_GET[id] or main page
		if( isset($_GET["id"]) || !Router::$path )
		{
			$id = isset($_GET["id"]) ? (int)$_GET["id"] : (int)Config::get("main");
			$row = self::get( $id );
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
				$row = self::get( $id );
			else
				$id = null;
		}

		self::$id = $id;
		Heap::set( "id", $id );
		if( !$id )
		{
			Http::header( HTTP_ERROR_NOT_FOUND );
			// TODO: 404 page template
			return;
		}

		Heap::set( "title", $row["title"] );
		Heap::set( "type", $row["type"] );
	}

	static public function get( $id )
	{
		if( isset(self::$cache[$id]["gid"]) )
			return self::$cache[$id];

		$res = DB::row( "SELECT gid, type, title FROM page WHERE id=". DB::escape($id) );

		if( $res )
			self::$cache[$id] = $res;

		return $res;
	}

	static public function text( $id )
	{
		return DB::one( "SELECT text FROM page WHERE id=". DB::escape($id) );
	}

	static public function path( $id )
	{
		if( !$id )
			return Router::$root;

		// Check in cache
		if( isset(self::$cache[$id]["path"]) )
			return self::$cache[$id]["path"];

		if( $id == Config::get("main") )
			$out = "";
		if( $path = self::prop($id, "path") )
			$out = $path;
		else
			$out = "?id=". $id;

		$out = Router::$root . $out;
		self::$cache[$id]["path"] = $out;

		return $out;
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

	static public function gid( $level=null, $id=null )
	{
		$id = $id ? (int)$id : self::$id;
		if( !$level )
		{
			$row = self::get( $id );
			return $row ? $row["gid"] : false;
		}

		$level = $level - 1;
		if( !isset(self::$cache[$id]["gids"][$level]) && !self::level($id) )
			return false;

		return self::$cache[$id]["gids"][$level];
	}

	static public function title( $level, $id=null )
	{
		$id = $id ? (int)$id : self::$id;
		if( !$level )
		{
			$row = self::get( $id );
			return $row ? $row["title"] : false;
		}

		$level = $level - 1;
		if( !isset(self::$cache[$id]["titles"][$level]) && !self::level($id) )
			return false;

		return self::$cache[$id]["titles"][$level];
	}

	static public function type( $level, $id=null )
	{
		$id = $id ? (int)$id : self::$id;
		if( !$level )
		{
			$row = self::get( $id );
			return $row ? $row["type"] : false;
		}

		$level = $level - 1;
		if( !isset(self::$cache[$id]["types"][$level]) && !self::level($id) )
			return false;

		return self::$cache[$id]["types"][$level];
	}


	static public function level( $id=null )
	{
		$id = $id ? (int)$id : self::$id;

		// Check cache
		if( isset(self::$cache[$id]["level"]) )
			return self::$cache[$id]["level"];

		// Calculate level + fill gid, title and type cache
		$row = self::get($id);
		if( !$row )
			return false;

		$gid = (int)$row["gid"];

		// Parent is root
		if( $gid == 0 )
		{
			self::$cache[$id]["level"] = 1;
			self::$cache[$id]["gids"] = array( $gid );
			self::$cache[$id]["titles"] = array( $row["title"] );
			self::$cache[$id]["types"] = array( $row["type"] );

			return 1;
		}
		// Get parent from cache, add current level
		elseif( isset(self::$cache[$gid]["level"]) || self::level($gid) )
		{
			self::$cache[$id]["level"] = self::$cache[$gid]["level"] + 1;
			self::$cache[$id]["gids"] = self::$cache[$gid]["gids"];
			self::$cache[$id]["gids"][] = $gid;
			self::$cache[$id]["titles"] = self::$cache[$gid]["titles"];
			self::$cache[$id]["titles"][] = $row["title"];
			self::$cache[$id]["types"] = self::$cache[$gid]["types"];
			self::$cache[$id]["types"][] = $row["title"];

			return self::$cache[$id]["level"];
		}
		else
			return false;
	}

	static public function crumb( $sep="&gt", $id=null )
	{
		$id = $id ? (int)$id : self::$id;

		if( !$id || !($level = self::level( $id )) )
			return "";

		$out = array();

		// Add main page to root
		if( Router::$type==PAGE_TYPE_CONTENT )
		{
			// Not main page or its sub-page
			if( !isset(self::$cache[$id]["gids"][1]) || self::$cache[$id]["gids"][1]!=Config::get("main") )
			{
				$row = self::get( Config::get("main") );
				$out[] = "<a href='". Router::$root ."'>{$row["title"]}</a>";
			}
		}

		if( $id == Config::get("main") )
			return $out[0];

		foreach( self::$cache[$id]["titles"] as $k=>$v )
		{
			$i = $k >= $level-1 ? $id : self::$cache[$id]["gids"][$k+1];
			$path = self::path( $i );
			$out[] = "<a href='$path'>$v</a>";
		}

		return implode( " $sep ", $out );
	}

	static public function menu( $gid, $tpl="[]" )
	{
		if( !self::$id || !self::level( self::$id ) )
			$gids = array();

		// Current page and its parents IDs
		else
		{
			$gids = self::$cache[self::$id]["gids"];
			$gids[] = self::$id;
		}

		$out = "";
		$rows = DB::all( "SELECT id, title FROM page WHERE hide=0 AND gid=". DB::escape($gid) );
		foreach( $rows as $row )
		{
			$sel = in_array($row["id"], $gids) ? " class='sel'" : "";
			$tmp = "<a href='". self::path($row["id"]) ."'$sel>{$row["title"]}</a> ";
			$out .= str_replace( "[]", $tmp, $tpl );
		}

		return $out;
	}

	static public function delete( $id )
	{
		// Delete sub-pages
		$list = DB::column( "SELECT id FROM page WHERE gid=$id" );
		foreach( $list as $v )
		{
			self::delete( $v );
		}

		// Page and prop
		DB::delete( "page", "id=$id" );
		DB::delete( "prop", "id=$id" );

		// Files
		$list = DB::column( "SELECT id FROM file WHERE gid=$id" );
		foreach( $list as $v )
			File::delete( $v );

		Hook::run( "del", $id );
	}
}
