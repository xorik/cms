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
	if( !$_GET["t"] && $_GET["do"]!="config" )
		$id = isset($_GET["id"]) ? (int)$_GET["id"] : $CONFIG["main"];
	// в админке если не указан id, показать главное меню
	if( $_GET["do"]=="admin" && !$_GET["id"] )
		$id = 0;
	
	// Страница выбрана
	if( $id )
	{
		// Заголовок и тип
		$query = "SELECT title, type FROM page WHERE id=$id";
		$res = mysql_query( $query );
		// Страница существует
		if( mysql_num_rows($res) )
		{
			$row = mysql_fetch_array( $res );
			$ORIG_TITLE = $row["title"];
			$TITLE = "{$CONFIG["title"]} - $ORIG_TITLE";
			$TYPE = $row["type"];
		}
		else
			unset($id);
	}
	
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
		if( $CONFIG["rewrite"] )
			define( "ADMIN", "admin?" );
		else
			define( "ADMIN", "?do=admin&" );
		load_modules( $DO );
		run( "init" );
		
		// Заголовок
		$HEAD[] = "<title>Страница администратора - {$CONFIG["title"]}</title>";
		
		require( "admin.php" );
	}
	// Аяксовая функция
	elseif( $DO=="ajax" )
	{
		header( "Content-type: text/html; charset=utf-8" );
		// TODO: проверка на ".." и "/"
		@include( "modules/ajax/{$_GET["file"]}.php" );
		
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
		$HEAD[] = "<title>$TITLE</title>";
		// Шаблон контента
		require( "main.php" );
	}
?>
