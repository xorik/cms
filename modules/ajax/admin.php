<?
	run( "auth" );
	load_modules( "admin" );
	
	// Действия
	// Сортировка страниц
	if( $_GET["page_sort"] )
	{
		global $TYPE;
		global $PAGE_TYPE;
		
		// Обратная сортировка
		if( $PAGE_TYPE[$TYPE]["reverse"] )
			$_GET["p"] = array_reverse( $_GET["p"] );
		
		foreach( $_GET["p"] as $k => $v )
		{
			$k = (int)$k;
			$v = (int)$v;
			$query = "UPDATE page SET pos=$k WHERE id=$v";
			mysql_query( $query );
		}
	}
	
	// Сортировка файлов
	if( isset($_GET["file_sort"]) )
	{
		foreach( $_GET["p"] as $k => $v )
		{
			$k = (int)$k;
			$v = (int)$v;
			$query = "UPDATE file SET pos=$k WHERE id=$v";
			mysql_query( $query );
		}
	}
?>
