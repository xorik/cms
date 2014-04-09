<?php
	if( $_GET["edit"] != "mysql" )
		return;
	
	hook( "init", "mysql_init" );
	hook( "content", "mysql_content" );
	
	
	function mysql_init()
	{
		if( count($_POST) )
		{
			global $CONFIG;
			
			foreach( array("db_host", "db_user", "db_pass", "db_db") AS $v )
			{
				if( $_POST[$v] )
					$CONFIG[$v] = $_POST[$v];
			}
			config_write();
			clear_post();
		}
	}
	
	function mysql_content()
	{
		template("modules/templates/config_mysql.tpl");
	}
