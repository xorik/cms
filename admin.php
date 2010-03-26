<?
	require( "config.php" );
	require( "func.php" );
	
	session_start();
	require( "modules/auth.php" );
	load_modules( "all_" );
	
	$DO = isset( $_GET["do"] ) ? $_GET["do"] : "edit";
	
	// Редактирование
	if( $DO == "edit" )
		load_modules( "admin_" );
	elseif( $DO == "config" )
		load_modules( "config_" );
	
	// Редактирование или настройки
	if( $DO == "edit" || $DO == "config" )
	{
		// Кодировка и заголовок страницы
		$HEAD[] = "<meta http-equiv='content-type' content='text/html; charset=utf-8'>";
		$HEAD[] = "<title>{$config["title"]}</title>";
		$CSS[] = "admin.css";
		$JS[] = "jquery.js";
		$JS[] = "admin.js";
	}
	// Загрузка модуля через аякс и выход
	else
	{
		header( "Content-type: text/html; charset=utf-8" );
		if( is_file("modules/$DO.php") )
			require( "modules/$DO.php" );
		
		hook_run( "init" );
		die();
	}
	
	hook_run( "init" );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
	<? head() ?>
</head>
<body>
	<div id='top'>
		<? hook_run( "menu" ) ?>
		<a href='?logout=1'><img src='modules/img/logout.png'> Выйти</a>
	</div>
	<div id='content'>
		<? hook_run( "content" ) ?>
	</div>
	<div id='bottom'>
		<? require( "version" ) ?>
	</div>
</body>
