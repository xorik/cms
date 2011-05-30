<?
	require( "config.php" );
	require( "func.php" );
	
	load_modules( "all" );
	
	// id страницы из пути
	if( $_GET["t"] )
	{
		$query = "SELECT id FROM prop WHERE value='{$_GET["t"]}' AND field='path'";
		$row = mysql_fetch_array( mysql_query($query) );
		$id = $row["id"];
	}
	// Неправильный путь, страница по id или главная
	if( !$_GET["t"] || !$id )
		$id = isset($_GET["id"]) ? (int)$_GET["id"] : $config["main"];
	// в админке если не указан id, показать главное меню
	if( $_GET["do"]=="admin" && !$_GET["id"] )
		$id = 0;
	
	// Заголовок и тип
	$query = "SELECT title, type FROM page WHERE id=$id";
	$row = mysql_fetch_array( mysql_query($query) );
	$TITLE = $row["title"];
	$TYPE = $row["type"];
	
	// Начать PHP-сессию
	session_start();
	
	// Действие
	$DO = $_GET["do"];
	// Админка
	if( $DO=="admin" || $DO=="config" )
	{
		// Проверка прав
		require( "modules/auth.php" );
		// Инициализация
		if( $config["rewrite"] )
			define( "ADMIN", "admin?" );
		else
			define( "ADMIN", "?do=admin&" );
		load_modules( $DO );
		run( "init" );
		
		require( "admin.php" );
	}
	// Аяксовая функция
	elseif( $DO=="ajax" )
	{
		header( "Content-type: text/html; charset=utf-8" );
		// TODO: проверка на ".." и "/"
		@include( "modules/{$_GET["file"]}.php" );
		
		run( "init" );
		die();
	}
	// Контент
	else
	{
		// Инициализация
		load_modules( "content" );
		run( "init" );
		
		// Заголовок страницы
		$HEAD[] = "<title>{$config["title"]} - $TITLE</title>";
		// Шаблон контента
		require( "main.php" );
	}
?>
