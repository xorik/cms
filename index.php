<?
	require( "config.php" );
	require( "modules/func.php" );
	require( "modules/template.php" );
	
	load_modules( "all" );
	
	// id страницы из пути
	if( $_GET["t"] )
	{
		$row = db_select_one( "SELECT id FROM prop WHERE field='path' AND value='{$_GET["t"]}'" );
		$id = $row["id"];
	}
	// Переход с ?id= на путь страницы, если включен реврайт
	elseif( $_GET["id"] && $_GET["id"]!=$CONFIG["main"] && !$_GET["do"] && $CONFIG["rewrite"] )
	{
		if( $path = get_prop($_GET["id"], "path") )
		{
			header( "Location: $path" );
			die;
		}
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
	$ADMIN_URL = $CONFIG["rewrite"] ? "./admin?" : "./?do=admin&";
	$CONFIG_URL = $CONFIG["rewrite"] ? "./config?" : "./?do=config&";
	
	// Фикс для шаблонов
	$HEAD = $CSS = $JS = $SCRIPT = array();
	
	// Действие
	$DO = $_GET["do"];
	// Админка
	if( $DO=="admin" || $DO=="config" )
	{
		// Проверка прав
		run( "auth" );
		// Тип корня раздела
		if( $id === 0 )
			$TYPE = "root";
		load_modules( $DO );
		run( "init" );
		
		// Заголовок
		$HEAD[] = "<title>Страница администратора - {$CONFIG["title"]}</title>";
		
		template( "modules/templates/admin.tpl" );
	}
	// Аяксовая функция
	elseif( $DO=="ajax" )
	{
		header( "Content-type: text/html; charset=utf-8" );
		
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
?>
