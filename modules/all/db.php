<?
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
?>
