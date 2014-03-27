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
	
	// Сохранение
	elseif( $_GET["save"] )
	{
		run( "init" );
		db_update( "page", array("title"=>$_POST["title"], "text"=>$_POST["text"], "type"=>$_POST["type"], "hide"=>(int)$_POST["hide"]), "id=$id" );
		
		// Путь для rewrite
		if( $CONFIG["rewrite"] )
			set_prop( $id, "path", str_replace(" ", "_", $_POST["path"]) );
		
		// Обновление
		run( "base_submit", $id );
	}
	
	// Добавление страницы
	elseif( $_GET["add"] )
	{
		run( "init" );
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
	
	elseif( $_GET["del"] )
	{
		run( "init" );
		// Рекурсивное удаление страницы
		function page_del( $id )
		{
			// Потомки
			$rows = db_select( "SELECT id FROM page WHERE gid=$id" );
			foreach( $rows as $row )
				page_del( $row["id"] );
			
			// Сама страница
			db_delete( "page", "id=$id" );
			
			// Доп. поля
			db_delete( "prop", "id=$id" );
			
			// Файлы
			$rows = db_select( "SELECT id FROM file WHERE gid=$id" );
			foreach( $rows as $row )
				delete_file( $row["id"] );
			
			// Другие действия при удалении
			run( "base_del", $id );
		}
		
		page_del( (int)$_POST["del"] );
	}

	// Сортировка страниц
	elseif( $_GET["page_sort"] )
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
	elseif( isset($_GET["file_sort"]) )
	{
		foreach( $_GET["p"] as $k=>$v )
		{
			db_update( "file", array("pos"=>(int)$k), "id=".(int)$v );
		}
	}
?>
