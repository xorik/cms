<?
	hook( "init", "type_init", 10 );
	hook( "init", "base_init", 90 );
	
	
	// Простая страница
	function type_init()
	{
		global $PAGE_TYPE;
		$PAGE_TYPE["Страница"] = array("descr"=>"Обычная страница с текстами и картинками");
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
		// Описание
		if( $PAGE_TYPE[$TYPE]["descr"] )
			hook( "base_show", "base_descr", 16 );
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
		global $CONFIG;
		
		$hide = (int)$_POST["hide"];
		
		$query = "UPDATE page set title='{$_POST["title"]}', text='{$_POST["text"]}', type='{$_POST["type"]}', hide=$hide WHERE id=$id";
		mysql_query( $query );
		
		// Путь для rewrite
		if( $CONFIG["rewrite"] )
			set_prop( $id, "path", str_replace(" ", "_", $_POST["path"]) );
		
		// Обновление
		run( "base_submit", $id );
		
		clear_post();
	}
	
	// Редактирование
	function base_content()
	{
		template( "modules/templates/base.tpl" );
	}
	
	// Заголовок в редактировании
	function base_title( $id )
	{
		$query = "SELECT title FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		?>
			<tr>
				<td>Заголовок:</td>
				<td><input type='text' name='title' value='<?= $row["title"] ?>'></td>
			</tr>
		<?
	}
	
	// Текст в редактировании
	function base_text( $id )
	{
		$query = "SELECT text FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		?>
			<tr>
				<td colspan='2'>Текст:<br>
				<textarea name='text' cols='80' rows='20' class='rich editor'><?= $row["text"] ?></textarea></td>
			</tr>
		<?
	}
	
	// Выбор типа в редактировании
	function base_type( $id )
	{
		global $TYPE;
		global $PAGE_TYPE;
		
		// Все типы подразделов родителя
		global $LEVEL;
		global $GID_TYPE;
		
		$types = array( $TYPE );
		if( $PAGE_TYPE[$GID_TYPE[$LEVEL-1]]["sub"] )
			$types = array_merge( $types, $PAGE_TYPE[$GID_TYPE[$LEVEL-1]]["sub"] );
		
		// Убираем повторы
		$types = array_unique( $types );
		
		// Не показывать тип, если notype или он один
		if( $PAGE_TYPE[$TYPE]["notype"] || count($types)==1 )
		{
			echo "<input type='hidden' name='type' value='$TYPE'>\n";
			return;
		}
		
		echo "<tr>";
			echo "<td>Тип:</td> <td><select name='type'>\n";
			
			foreach( $types AS $v )
			{
				if( $v == $TYPE )
					echo "<option selected>$v</option>\n";
				else
					echo "<option>$v</option>\n";
			}
			
			echo "</select></td>";
		echo "</tr>\n";
	}
	
	
	function base_descr()
	{
		global $TYPE;
		global $PAGE_TYPE;
		
		echo "<tr><td></td><td class='descr'>{$PAGE_TYPE[$TYPE]["descr"]}</td></tr>";
	}
	
	
	function base_hide( $id )
	{
		$query = "SELECT hide FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		?>
			<tr>
				<td colspan='2'>
				<label><input type='radio' name='hide' value='0' <? if(!$row["hide"]) echo "checked" ?>> Страница видна всем (<div class='round show'><i class='i-'></i></div>)</label>
				<label><input type='radio' name='hide' value='1' <? if($row["hide"]) echo "checked" ?>> Скрывать страницу в меню (<div class='round hide'><i class='i-'></i></div>)</label>
				</td>
			</tr>
		<?
	}
	
	
	function base_path( $id )
	{
		echo "<tr>";
			echo "<td>Путь:</td> <td><input type='text' name='path' value='". get_prop( $id, "path" ) ."'></td>";
		echo "</tr>\n";
	}
	
	
	function not_found_content()
	{
		global $ADMIN_URL;
		echo "<h3>Страница была удалена или еще не создана!</h3><a href='$ADMIN_URL'>Назад</a>\n";
	}
?>
