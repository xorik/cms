<?
	global $CONFIG;
	
	mysql_connect( $CONFIG["db_host"], $CONFIG["db_user"], $CONFIG["db_pass"] ) or die( "DB connect error" );
	mysql_select_db( $CONFIG["db_db"] ) or die( "DB error" );
	mysql_query ( "SET NAMES UTF8" );
?>
