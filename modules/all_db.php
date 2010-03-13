<?
	global $config;
	
	mysql_connect( "localhost", $config["db_user"], $config["db_pass"] ) or die( "DB connect error" );
	mysql_select_db( $config["db_db"] ) or die( "DB error" );
	mysql_query ( "SET NAMES UTF8" );
?>
