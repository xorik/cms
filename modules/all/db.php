<?php


global $CONFIG;

$error = "";

mysql_connect( $CONFIG["db_host"], $CONFIG["db_user"], $CONFIG["db_pass"] ) or $error = "mysql_connect failed! ";
mysql_select_db( $CONFIG["db_db"] ) or $error .= " mysql_select_db failed!";
mysql_query ( "SET NAMES UTF8" );

// Ошибка подключение и не страница настроек
if( $error && $_GET["do"]!="config" )
	die( $error );
elseif( empty($_POST) )
	echo( $error );


function db_escape( $var )
{
	if( is_string($var) )
	{
		return "'". mysql_real_escape_string($var) ."'";
	}
	elseif( is_numeric($var) )
	{
		return $var;
	}
	elseif( is_null($var) )
		return "NULL";
	
	trigger_error( "Unknown type for escaping: ". print_r($var, true) );
	return "''";
}


if( !$CONFIG["db_debug"] )
{
	function db_select_one( $query )
	{
		$res = mysql_query( $query ." LIMIT 0,1" );
		if( !$res )
		{
			trigger_error("Error running query $query<br>". mysql_error());
			return false;
		}
		
		return mysql_fetch_assoc( $res );
	}
	
	
	function db_select( $query )
	{
		$res = mysql_query( $query );
		if( !$res )
		{
			trigger_error("Error running query $query<br>". mysql_error());
			return false;
		}
		
		$rows = array();
		while( $row = mysql_fetch_assoc($res) )
		{
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	
	function db_insert( $table, $fields, $replace=0 )
	{
		$a = $b = array();
		foreach( $fields as $k=>$v )
		{
			$a[] = "`$k`";
			$b[] = db_escape( $v );
		}
		$cmd = $replace ? "REPLACE" : "INSERT";
		$query = "$cmd INTO `$table` (". implode(", ", $a) .") VALUES(". implode(", ", $b) .")";
		
		$res = mysql_query( $query );
		if( !$res )
		{
			trigger_error("Error running query $query<br>". mysql_error());
			return false;
		}
		
		return mysql_insert_id();
	}
	
	
	function db_update( $table, $fields, $where )
	{
		$a = array();
		foreach( $fields as $k=>$v )
		{
			$a[] = "`$k`=". db_escape( $v );
		}
		$query = "UPDATE `$table` SET ". implode(", ", $a) ." WHERE $where";
		
		$res = mysql_query( $query );
		if( !$res )
		{
			trigger_error("Error running query $query<br>". mysql_error());
			return false;
		}
		
		return mysql_affected_rows();
	}
	
	function db_delete( $table, $where )
	{
		$query = "DELETE FROM `$table` WHERE $where";
		
		$res = mysql_query( $query );
		if( !$res )
		{
			trigger_error("Error running query $query<br>". mysql_error());
			return false;
		}
		
		return mysql_affected_rows();
	}
}
