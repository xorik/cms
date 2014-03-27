<?
	run( "auth" );
	load_modules( "admin" );
	
	// Действия
	// Показать основной блок
	if( $_GET["base"] )
	{
		run( "init" );
		run( "content" );
	}

	// Добавление страницы
	if( $_GET["add"] )
	{
		// Тип корня раздела
		if( $id === 0 )
		{
			$TYPE = "root";
		}
		
		run( "init" );
		// Первый разрешенный тип, иначе просто страница
		$type = $PAGE_TYPE[$TYPE]["sub"][0] ? $PAGE_TYPE[$TYPE]["sub"][0] : "Страница";

		// Сохраняем id и тип для хуков
		$id = db_insert( "page", array("gid"=>$id, "title"=>$_POST["title"], "text"=>"", "type"=>$type) );
		$TYPE = $type;

		// Хуки после добавления
		run( "base_add", $id );

		// TODO: error message
		echo '{"id": '. $id .'}';
	}

	// Сортировка страниц
	if( $_GET["page_sort"] )
	{
		global $TYPE;
		global $PAGE_TYPE;
		
		// Обратная сортировка
		if( $PAGE_TYPE[$TYPE]["reverse"] )
			$_GET["p"] = array_reverse( $_GET["p"] );
		
		foreach( $_GET["p"] as $k=>$v )
		{
			db_update( "page", array("pos"=>(int)$k), "id=".(int)$v );
		}
	}
	
	// Сортировка файлов
	if( isset($_GET["file_sort"]) )
	{
		foreach( $_GET["p"] as $k=>$v )
		{
			db_update( "file", array("pos"=>(int)$k), "id=".(int)$v );
		}
	}
?>
