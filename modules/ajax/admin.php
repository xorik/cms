<?
	run( "auth" );
	load_modules( "admin" );
	
	// Действия
	// Показать основной блок
	if( $_GET["base"] )
	{
		run( "content" );
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
