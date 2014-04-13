<?php


if( $_GET["edit"] != "mysql" )
	return;

hook( "init", "mysql_init" );
hook( "content", "mysql_content" );


function mysql_init()
{
	global $CONFIG, $CONFIG_URL;
	
	// Создать таблицы в базе
	if( $_GET["create"] )
	{
		db_query( "modules/res/db.sql" );
		header( "Location: {$CONFIG_URL}edit=mysql" );
		die;
	}
	
	if( $_SERVER["REQUEST_METHOD"] != "POST" )
		return;
	
	foreach( array("db_host", "db_user", "db_pass", "db_db") AS $v )
	{
		if( $_POST[$v] )
			$CONFIG[$v] = $_POST[$v];
	}
	
	config_write();
	clear_post();
}

function mysql_content()
{
	global $DB_ERROR, $DB_EMPTY;
	
	if( !$DB_ERROR )
	{
		if( !count(db_select("SHOW TABLES LIKE 'page'")) )
			$DB_EMPTY = 1;
	}
	
	template("modules/templates/config_mysql.tpl");
}
