<?
	// Не тот модуль
	if( $_GET["do"] && $_GET["do"] != "edit" )
		return;
	
	global $id;
	global $gid;
	
	$id = (int)$_GET["id"];
	
	// Проверка есть ли страница
	$query = "SELECT gid FROM page WHERE id=$id";
	$res = mysql_query( $query );
	// Есть страница
	if( mysql_num_rows($res) )
	{
		$row = mysql_fetch_array( $res );
		$gid = $row["gid"];
		hook_add( "content", "base_content" );
		
		// Обновление данных
		if( $_POST )
		{
			$query = "UPDATE page set title='{$_POST["title"]}', text='{$_POST["text"]}' WHERE id=$id";
			mysql_query( $query );
			clear_post();
		}
	}
	
	function base_content()
	{
		global $id;
		
		$query = "SELECT title, text FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		?>
			<h3 id='base_toggle'><?= $row["title"] ?></h3>
			<form method='post'>
				Заголовок: <input type='text' name='title' value='<?= $row["title"] ?>'><br>
				Текст:<br>
				<textarea name='text' cols='80' rows='20'><?= $row["text"] ?></textarea><br>
				<input type='submit' value='Сохранить'>
			</form>
		<?
	}
?>
