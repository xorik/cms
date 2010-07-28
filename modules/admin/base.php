<?
	hook_add( "init", "base_init", 10 );
	
	function base_init()
	{
		global $id;
		global $gid;
		global $TYPE;
		global $PAGE_TYPE;
		
		$id = (int)$_GET["id"];
		
		// Список типов страниц
		$PAGE_TYPE[] = "Страница";
		
		// Проверка есть ли страница
		$query = "SELECT gid, type FROM page WHERE id=$id";
		$res = mysql_query( $query );
		// Есть страница
		if( mysql_num_rows($res) )
		{
			$row = mysql_fetch_array( $res );
			$gid = $row["gid"];
			$TYPE = $row["type"];
			hook_add( "content", "base_content", 10 );
			hook_add( "base_show", "base_title", 10 );
			hook_add( "base_show", "base_type", 15 );
			hook_add( "base_show", "base_hide", 80 );
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
		$hide = (int)$_POST["hide"];
		
		$query = "UPDATE page set title='{$_POST["title"]}', text='{$_POST["text"]}', type='{$_POST["type"]}', hide=$hide WHERE id=$id";
		mysql_query( $query );
		
		// Обновление
		hook_run( "base_submit", $id );
		
		clear_post();
	}
	
	// Редактирование
	function base_content()
	{
		global $id;
		
		$query = "SELECT title, hide FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		
		if( $row["hide"] )
				$img = "<img src='modules/img/hide.png'>";
			else
				$img = "<img src='modules/img/edit.png'>";
		?>
			<h3 id='base_toggle'><?= $img ." ". $row["title"] ?></h3>
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
			<textarea name='text' cols='80' rows='20' class='mce'><?= $row["text"] ?></textarea><br>
		<?
	}
	
	// Выбор типа в редактировании
	function base_type( $id )
	{
		global $TYPE;
		global $PAGE_TYPE;
		
		echo "Тип: <select name='type'>\n";
		foreach( $PAGE_TYPE as $v )
			if( $v == $TYPE )
				echo "<option selected>$v</option>\n";
			else
				echo "<option>$v</option>\n";
		
		echo "</select><br>\n";
	}
	
	function base_hide( $id )
	{
		$query = "SELECT hide FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		?>
			Скрывать в меню:
			<input type='radio' name='hide' value='0' <? if(!$row["hide"]) echo "checked" ?>> Нет
			<input type='radio' name='hide' value='1' <? if($row["hide"]) echo "checked" ?>> Да
			<br>
		<?
	}
?>
