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
			echo "<h3 id='sub_toggle'>Подразделы</h3>\n";
		else
			echo "<h3 id='sub_toggle'>Разделы</h3>\n";
		
		// Список поразделов
		?>
			<table class='noth'>
		<?
		$query = "SELECT id, title FROM page WHERE gid=$id";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			echo "<tr><td><a href='?id={$row["id"]}'><img src='modules/img/edit.png'> {$row["title"]}</a></td>";
			echo "<td><a href='?id=$id&page_del={$row["id"]}'><img src='modules/img/del.png'> Удалить</a></td></tr>\n";
		}
		?>
			<tr><td colspan='2'><a href='?page_add=<?= $id ?>'><img src='modules/img/add.png'> Добавить страницу</a></td></tr>
			</table>
		<?
	}
?>
