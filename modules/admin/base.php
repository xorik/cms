<?
	hook( "init", "type_init", 10 );
	hook( "init", "base_init", 90 );
	
	
	// Простая страница
	function type_init()
	{
		global $PAGE_TYPE;
		$PAGE_TYPE["Страница"] = array();
	}
	
	
	function base_init()
	{
		global $id;
		global $TYPE;
		global $PAGE_TYPE;
		global $CONFIG;
		
		// Нет страницы или главная -> выход
		if( !$id )
		{
			// Нет страницы
			if( !isset($id) )
				hook( "content", "not_found_content" );
			return;
		}
		
		hook( "content", "base_content", 10 );
		hook( "base_show", "base_title", 10 );
		hook( "base_show", "base_type", 15 );
		// Не виртуальная страница
		if( !$PAGE_TYPE[$TYPE]["virt"] )
			hook( "base_show", "base_hide", 80 );
		// Нужен текст
		if( !$PAGE_TYPE[$TYPE]["notext"] )
			hook( "base_show", "base_text", 90 );
		// Путь, если включен rewrite, не главная и не виртуальная
		if( $CONFIG["rewrite"] && $id!=$CONFIG["main"] && !$PAGE_TYPE[$TYPE]["virt"] )
			hook( "base_show", "base_path", 20 );
		
		// Обновление данных (в последнюю очередь, после всех init'ов)
		if( $_POST["title"] )
			hook( "init", "post_base_init", 99 );
	}
	
	// Обновление данных
	function post_base_init()
	{
		global $id;
		$hide = (int)$_POST["hide"];
		
		$query = "UPDATE page set title='{$_POST["title"]}', text='{$_POST["text"]}', type='{$_POST["type"]}', hide=$hide WHERE id=$id";
		mysql_query( $query );
		
		// Путь для rewrite
		$p = $_POST["path"];
		$p = str_replace( " ", "_", $p );
		set_prop( $id, "path", $p );
		
		// Обновление
		run( "base_submit", $id );
		
		clear_post();
	}
	
	// Редактирование
	function base_content()
	{
		global $id;
		
		$query = "SELECT title, hide FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		
		?>
			<form method='post'>
				<input type='submit' value='Сохранить' class='save'>
				<?
					// Не виртуальная страница
					if( !$PAGE_TYPE[$TYPE]["virt"] )
						echo "<a href='".path($id)."' class='goto'>Посмотреть страницу</a>";
				?>
				<h2><?= $img ." ". $row["title"] ?></h2>
				<? run( "base_show", $id ) ?>
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
			<textarea name='text' cols='80' rows='20' class='mce editor'><?= $row["text"] ?></textarea><br>
		<?
	}
	
	// Выбор типа в редактировании
	function base_type( $id )
	{
		global $TYPE;
		global $PAGE_TYPE;
		
		if( $PAGE_TYPE[$TYPE]["notype"] )
		{
			echo "<input type='hidden' name='type' value='$TYPE'>\n";
			return;
		}
		
		echo "Тип: <select name='type'>\n";
		foreach( $PAGE_TYPE as $k=>$v )
			if( $k == $TYPE )
				echo "<option selected>$k</option>\n";
			else
				echo "<option>$k</option>\n";
		
		echo "</select><br>\n";
	}
	
	function base_hide( $id )
	{
		$query = "SELECT hide FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		?>
			Опубликовать:
			<label><input type='radio' name='hide' value='0' <? if(!$row["hide"]) echo "checked" ?>> Да</label>
			<label><input type='radio' name='hide' value='1' <? if($row["hide"]) echo "checked" ?>> Нет</label>
			<br>
		<?
	}
	
	
	function base_path( $id )
	{
		echo "Путь: <input type='text' name='path' value='". get_prop( $id, "path" ) ."'><br>\n";
	}
	
	
	function not_found_content()
	{
		echo "<h3>Страница была удалена или еще не создана!</h3><a href='".ADMIN."'>Назад</a>\n";
	}
?>
