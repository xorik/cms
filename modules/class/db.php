<?php


DB::init();


class DB
{
	static protected $db = null;
	static public $connected = false;


	/**
	 * Connect to MySQL
	 *
	 * @throws Exception on error
	 */
	static public function init()
	{
		$db = Config::get( "db" );
		self::$db = new mysqli( $db["host"], $db["user"], $db["pass"], $db["db"] );
		if( self::$db->connect_errno )
			throw new Exception( "MySQL connect error" );

		self::query( "SET NAMES UTF8" );
		self::$connected = true;
	}

	/**
	 * Escape value for query
	 *
	 * @param mixed $var
	 * @return string escaped string
	 */
	static public function escape( $var )
	{
		if( is_string($var) )
		{
			return "'". self::$db->escape_string($var) ."'";
		}
		elseif( is_numeric($var) )
		{
			return $var;
		}
		elseif( is_null($var) )
			return "NULL";

		Error::warning( "Unknown type for escaping: ". print_r($var, true) );
		return "''";
	}

	/**
	 * Run MySQL query
	 *
	 * @param string $query
	 * @return mysqli_result|false
	 */
	static public function query( $query )
	{
		$res = self::$db->query( $query );

		if( $res === false )
			Error::warning( "Error running query '$query'<br>". self::$db->error );

		return $res;
	}

	/**
	 * Get all rows for query result
	 *
	 * @param string $query
	 * @return array|false
	 */
	static public function all( $query )
	{
		$res = self::query( $query );
		if( !$res ) return false;

		$rows = array();
		while( $row = $res->fetch_assoc() )
			$rows[] = $row;

		$res->free();
		return $rows;
	}

	/**
	 * Get one row from query result
	 *
	 * @param string $query
	 * @return array|false
	 */
	static public function row( $query )
	{
		$res = self::query( $query );
		if( !$res ) return false;

		$tmp = $res->fetch_assoc();
		$res->free();
		return $tmp;
	}

	/**
	 * Get column as array
	 *
	 * @param $query
	 * @return array|false
	 */
	static public function column( $query )
	{
		$res = self::query( $query );
		if( !$res ) return false;

		$rows = array();
		while( $row = $res->fetch_row() )
		{
			list($tmp) = $row;
			$rows[] = $tmp;
		}

		$res->free();
		return $rows;
	}

	/**
	 * Get one field from query result
	 *
	 * @param string $query
	 * @return mixed|false
	 */
	static public function one( $query )
	{
		$res = self::query( $query );
		if( !$res ) return false;

		list($tmp) = $res->fetch_row();
		$res->free();
		return $tmp;
	}

	/**
	 * Run SQL queries from file
	 *
	 * @param string $file SQL file path
	 * @return mixed mysql result
	 * @throws Exception if file not exists
	 */
	static public function file( $file )
	{
		if( !file_exists($file) )
			throw new Exception( "File not exists: $file" );

		return self::$db->multi_query( file_get_contents($file) );
	}

	/**
	 * Insert into DB
	 *
	 * @param string $table
	 * @param array $fields
	 * @param int $replace replace or inserts
	 * @param int $unesc unescape field values
	 * @return bool|int insert_id or false on error
	 */
	static public function insert( $table, $fields, $replace=0, $unesc=0 )
	{
		$a = $b = array();
		foreach( $fields as $k=>$v )
		{
			$a[] = "`$k`";
			$b[] = $unesc ? $v : self::escape( $v );
		}

		$cmd = $replace ? "REPLACE" : "INSERT";
		$query = "$cmd INTO `$table` (". implode(", ", $a) .") VALUES(". implode(", ", $b) .")";

		$res = self::query( $query );
		if( !$res ) return false;

		return self::$db->insert_id;
	}

	/**
	 * Update table
	 *
	 * @param string $table
	 * @param array $fields
	 * @param string $where
	 * @param int $unesc unescape field values
	 * @return bool|integer affected_rows or false on error
	 */
	static public function update( $table, $fields, $where, $unesc=0 )
	{
		$a = array();
		foreach( $fields as $k=>$v )
		{
			$a[] = "`$k`=". ($unesc ? $v : self::escape($v));
		}

		$query = "UPDATE `$table` SET ". implode(", ", $a) ." WHERE $where";

		$res = self::query( $query );
		if( !$res ) return false;

		return self::$db->affected_rows;
	}

	/**
	 * Delete row from table
	 *
	 * @param $table
	 * @param $where
	 * @return bool|integer affected_rows or false on error
	 */
	static public function delete( $table, $where )
	{
		$query = "DELETE FROM `$table` WHERE $where";

		$res = self::query( $query );
		if( !$res ) return false;

		return self::$db->affected_rows;
	}
}
