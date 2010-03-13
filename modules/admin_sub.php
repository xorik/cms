<?
	// Не тот модуль
	if( $_GET["do"] && $_GET["do"] != "edit" )
		return;
	
	hook_add( "content", "sub_content", 60 );
	
	// Добавление страницы
	if( isset($_GET["page_add"]) )
	{
		$gid = (int)$_GET["page_add"];
		$query = "INSERT INTO page (gid, title, text) VALUES ($gid, 'Новая страница', '')";
		mysql_query( $query );
		
		// Переход на созданную страницу
		$id = mysql_insert_id();
		header( "Location: ?id=$id" );
		die;
	}
	
	function sub_content()
	{
		global $id;
		
		if( $id )
			echo "<h3>Подразделы</h3>\n";
		else
			echo "<h3>Разделы</h3>\n";
		
		// Список поразделов
		$query = "SELECT id, title FROM page WHERE gid=$id";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			echo "<a href='?id={$row["id"]}'>{$row["title"]}</a><br>\n";
		}
		echo "<a href='?page_add=$id'>Добавить страницу</a>\n";
		
	}
?>
