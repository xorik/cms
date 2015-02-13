<?php

Configure::add( "mysql", "MySQL", true );

if( Configure::current() == "mysql" )
{
	Hook::add( "init", "mysql_config_init" );
	Hook::add( "content", "mysql_config_content" );
}


function mysql_config_init()
{
	if( !empty($_POST) )
	{
		Config::set( "db", array(
			"host"=>$_POST["host"],
			"user"=>$_POST["user"],
			"pass"=>$_POST["pass"] ? $_POST["pass"] : Config::get("db", "pass"),
			"db"=>$_POST["db"],
		));

		Config::save();
		Http::reload();
	}

	// Create tables
	if( isset($_GET["create"]) )
	{
		// TODO: check status
		DB::file( "modules/res/db.sql" );
		Noty::success( "Таблицы успешно созданы" );
		Http::redirect( Router::$root . Router::$path );
	}

	// Check DB connect
	$connected = false;
	try
	{
		$connected = DB::$connected;
		if( !count(DB::all("SHOW TABLES LIKE 'page'")) )
		{
			Noty::err( "Таблицы в БД не созданы!" );
			Heap::empty_db( 1 );
		}
	}
	catch( Exception $e )
	{
		$msg = $e->getMessage();
	}

	if( !$connected )
		Noty::err( "Не могу подключиться к mysql: <b>$msg</b>" );
}


function mysql_config_content()
{
	$a = array_merge( Config::get("db"), array("empty"=>Heap::empty_db()) );
	Template::show( "modules/templates/config_mysql.tpl", 0, $a );
}
