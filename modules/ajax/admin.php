<?
	require( "modules/auth.php" );
	load_modules( "admin" );
	
	// Инициализация
	run( "init" );
	
	// Действия
	// Сортировка страниц
	if( $_GET["page_sort"] )
	{
		global $TYPE;
		global $PAGE_TYPE;
		
		// Обратная сортировка
		if( $PAGE_TYPE[$TYPE]["reverse"] )
			$_GET["p"] = array_reverse( $_GET["p"] );
		
		$i = 0;
		
		foreach( $_GET["p"] as $k => $v )
		{
			$k = (int)$k;
			$v = (int)$v;
			$query = "UPDATE page SET pos=$k WHERE id=$v";
			mysql_query( $query );
		}
	}
?>
