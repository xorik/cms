<?php


$error = "";

mysql_connect( $CONFIG["db_host"], $CONFIG["db_user"], $CONFIG["db_pass"] )or $error = "mysql_connect failed! ";
mysql_select_db( $CONFIG["db_db"] ) or $error .= " mysql_select_db failed!";
mysql_query ( "SET NAMES UTF8" );

// Ошибка подключение и не страница настроек
if( $error && $_GET["do"]!="config" && $_GET["t"]!="config" )
	die( $error );
elseif( $error && empty($_POST) )
{
	global $DB_ERROR;
	
	session_start();
	$_SESSION["notify"][] = array( "text"=>$error, "type"=>"warning" );
	$DB_ERROR = 1;
}

// Экранировать переменную
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


// Выполнить запрос к базе, $query может быть SQL или путь к файлу
function db_query( $query )
{
	if( is_file($query) )
		$query = file_get_contents( $query );
	
	if( strpos($query, ";") )
		$q = explode( ";", $query );
	else
		$q = array( $query );
	
	$res = array();
	foreach( $q as $query )
	{
		if( strlen( trim($query)) == 0 )
			continue;
		
		$r = mysql_query( $query );
		if( $r )
			$res[] = $r;
		else
		{
			trigger_error("Error running query $query<br>". mysql_error());
			return false;
		}
	}

	return count($res)==1 ? $res[0] : $res;
}


if( !$CONFIG["db_debug"] )
{
	// Первый результат запроса
	function db_select_one( $query )
	{
		$res = is_resource($query) ? $query : mysql_query($query ." LIMIT 0,1");
		if( !$res )
		{
			trigger_error("Error running query $query<br>". mysql_error());
			return false;
		}
		
		return mysql_fetch_assoc( $res );
	}
	
	
	// Все строки запроса
	function db_select( $query )
	{
		$res = is_resource($query) ? $query : mysql_query($query);
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
	
	
	// Вставить в базу
	function db_insert( $table, $fields, $replace=0, $unesc=0 )
	{
		$a = $b = array();
		foreach( $fields as $k=>$v )
		{
			$a[] = "`$k`";
			$b[] = $unesc ? $v : db_escape( $v );
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
	
	
	// Обновить в базе
	function db_update( $table, $fields, $where, $unesc = 0 )
	{
		$a = array();
		foreach( $fields as $k=>$v )
		{
			$a[] = "`$k`=". ($unesc ? $v : db_escape($v));
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
	
	
	// Удалить в базе
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
