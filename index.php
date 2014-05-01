<?php
	require( "config.php" );
	require( "modules/func.php" );
	require( "modules/template.php" );
	
	header( "Content-type: text/html; charset=utf-8" );
	
	load_modules( "all" );
	
	// id страницы
	// Админка или настройки
	if( ($_GET["t"]=="admin" || $_GET["t"]=="config") && $_GET["do"]!="ajax" )
		$_GET["do"] = $_GET["t"];
	
	// Файл
	elseif( strpos($_GET["t"], "file") === 0 )
	{
		preg_match( "|file/(.*)|", $_GET["t"], $m );
		$_GET = array( "do"=>"ajax", "file"=>"getfile", "fid"=>$m[1] );
	}
	// Страница или ajax
	else
	{
		if( $_GET["t"] && $_GET["do"]!="ajax" )
		{
			$row = db_select_one( "SELECT id FROM prop WHERE field='path' AND value=". db_escape($_GET["t"]) );
			$id = $row["id"];
		}
		// id 0 для админки
		elseif($_GET["do"]=="ajax" && $_GET["file"]=="admin" && !$_GET["id"])
			$id = 0;
		// Неправильный путь, страница по id или главная
		else
			$id = $_GET["id"] ? (int)$_GET["id"] : $CONFIG["main"];
	}
	
	// Переход с ?id= на путь страницы, если включен реврайт
	if( $_GET["id"] && $id && $id!=$CONFIG["main"] && !$_GET["do"] && $CONFIG["rewrite"] )
	{
		if( $path = get_prop($id, "path") )
		{
			header( "Location: $path" );
			die;
		}
	}
	
	// Страница выбрана
	if( $id )
	{
		// Заголовок и тип
		$row = db_select_one( "SELECT title, type FROM page WHERE id=$id" );
		// Страница существует
		if( $row )
		{
			$ORIG_TITLE = $row["title"];
			$TITLE = "{$CONFIG["title"]} - $ORIG_TITLE";
			$TYPE = $row["type"];
		}
		else
			unset( $id );
	}
	
	// Вернуть статус 404, если страницы нет
	if( !$id && !$_GET["do"] )
		header( "HTTP/1.0 404 Not Found" );
	
	// Начать PHP-сессию
	session_start();
	
	// Инициализация
	$ADMIN_URL = $CONFIG["rewrite"] ? "./admin?" : "./?t=admin&";
	$CONFIG_URL = $CONFIG["rewrite"] ? "./config?" : "./?t=config&";
	
	// Фикс для шаблонов
	$HEAD = $CSS = $JS = $SCRIPT = array();
	
	// Действие
	$DO = $_GET["do"];
	// Админка
	if( $DO=="admin" || $DO=="config" )
	{
		// Проверка прав
		run( "auth" );
		load_modules( $DO );
		run( "init" );
		
		// Заголовок
		$HEAD[] = "<title>Страница администратора - {$CONFIG["title"]}</title>";
		
		template( "modules/templates/admin.tpl" );
	}
	// Аяксовая функция
	elseif( $DO=="ajax" )
	{
		// Проверка на запрещенные символы
		if( strpos($_GET["file"], "*")!==false || strpos($_GET["file"], "..")!==false )
			$file = "";
		// Файл в modules
		elseif( is_file("modules/ajax/{$_GET["file"]}.php") )
			$file = "modules/ajax/{$_GET["file"]}.php";
		// Файл в extra
		else
		{
			$files = glob( "extra/*/ajax/{$_GET["file"]}.php" );
			$file = $files[0];
		}
		
		if( $file )
			@include( $file );
		else
			header( "HTTP/1.0 404 Not Found" );
		
		die();
	}
	// Контент
	else
	{
		// Инициализация
		load_modules( "content" );
		run( "init" );
		
		// Заголовок страницы
		$HEAD[] = "<title>$TITLE</title>";
		// Шаблон контента
		if( substr_compare($CONFIG["template"], ".tpl", -4, 4) === 0 )
			template( $CONFIG["template"], 1 );
		else
			require( $CONFIG["template"] );
	}
	
	run( "shutdown" );
