<?
	require( "config.php" );
	require( "func.php" );
	
	session_start();
	require( "modules/auth.php" );
	load_modules( "all_" );
	load_modules( "admin_" );
	
	// Кодировка и заголовок страницы
	$HEAD[] = "<meta http-equiv='content-type' content='text/html; charset=utf-8'>";
	$HEAD[] = "<title>{$config["title"]}</title>";
	$CSS[] = "admin.css";
	$JS[] = "jquery.js";
	$JS[] = "admin.js";
	
	hook_run( "init" );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
	<? head() ?>
</head>
<body>
	<div id='top'>
		<? hook_run( "menu" ) ?>
		<a href='?logout=1'>Выйти</a>
	</div>
	<div id='content'>
		<? hook_run( "content" ) ?>
	</div>
</body>
