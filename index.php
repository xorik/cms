<?
	require( "config.php" );
	require( "func.php" );
	
	load_modules( "all" );
	
	// id страницы
	$id = isset($_GET["id"]) ? (int)$_GET["id"] : 1;
	
	// Заголовок и тип
	$query = "SELECT title, type FROM page WHERE id=$id";
	$row = mysql_fetch_array( mysql_query($query) );
	$TITLE = $row["title"];
	$TYPE = $row["type"];
	
	// Действие
	$DO = $_GET["do"];
	// Админка
	if( $DO=="admin" || $DO=="config" )
	{
		// Проверка прав
		session_start();
		require( "modules/auth.php" );
		// Инициализация
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
