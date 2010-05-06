<?
	hook_add( "init", "base_init", 10 );
	
	function base_init()
	{
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
			hook_add( "content", "base_content", 10 );
			hook_add( "base_show", "base_title", 10 );
			hook_add( "base_show", "base_text", 90 );
			
			// Обновление данных (в последнюю очередь, после всех init'ов)
			if( $_POST["title"] )
				hook_add( "init", "post_base_init", 99 );
		}
	}
	
	// Обновление данных
	function post_base_init()
	{
		global $id;
		
		$query = "UPDATE page set title='{$_POST["title"]}', text='{$_POST["text"]}' WHERE id=$id";
		mysql_query( $query );
		
		// Обновление
		hook_run( "base_submit", $id );
		
		clear_post();
	}
	
	// Редактирование
	function base_content()
	{
		global $id;
		
		$query = "SELECT title FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		?>
			<h3 id='base_toggle'><?= $row["title"] ?></h3>
			<form method='post'>
				<? hook_run( "base_show", $id ) ?>
				<input type='submit' value='Сохранить'>
			</form>
		<?
	}
	
	// Заголовок в редактировании
	function base_title( $id )
	{
		$query = "SELECT title FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		?>
			Заголовок: <input type='text' name='title' value='<?= $row["title"] ?>'><br>
		<?
	}
	
	// Текст в редактировании
	function base_text( $id )
	{
		$query = "SELECT text FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		?>
			Текст:<br>
			<textarea name='text' cols='80' rows='20'><?= $row["text"] ?></textarea><br>
		<?
	}
	
?>
