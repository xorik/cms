<?
	hook( "init", "nav_init", 90 );
	hook( "nav", "nav_content" );
	
	
	// Добавление/удаление страниц
	function nav_init()
	{
		global $id;
		global $TYPE;
		global $LEVEL;
		global $PAGE_TYPE;
		global $ADMIN_URL;
		
		// Добавление страницы
		if( isset($_GET["page_add"]) && ($LEVEL==0 || !empty($PAGE_TYPE[$TYPE]["sub"])) )
		{
			// Первый разрешенный тип, иначе просто страница
			$type = $PAGE_TYPE[$TYPE]["sub"][0] ? $PAGE_TYPE[$TYPE]["sub"][0] : "Страница";
			$query = "INSERT INTO page (gid, title, text, type) VALUES ($id, '{$_POST["title"]}', '', '$type')";
			mysql_query( $query );
			
			// Сохраняем id и тип для хуков
			$id = mysql_insert_id();
			$TYPE = $type;
			
			// Хуки после добавления
			run( "base_add", $id );
			
			header( "Location: {$ADMIN_URL}id=$id" );
			die;
		}
		
		// Рекурсивное удаление страницы
		function page_del( $id )
		{
			// Потомки
			$query = "SELECT id FROM page WHERE gid=$id";
			$res = mysql_query( $query );
			while( $row = mysql_fetch_array($res) )
				page_del( $row["id"] );
			
			// Сама страница
			$query = "DELETE FROM page WHERE id=$id";
			mysql_query( $query );
			
			// Доп. поля
			$query = "DELETE FROM prop WHERE id=$id";
			mysql_query( $query );
			
			// Файлы
			$query = "SELECT id FROM file WHERE gid=$id";
			$res = mysql_query( $query );
			while( $row = mysql_fetch_array($res) )
				delete_file( $row["id"] );
			
			
			// Другие действия при удалении
			run( "base_del", $id );
		}
		
		// Удаление страниц
		if( isset($_GET["page_del"]) )
		{
			// Удаление
			$del = (int)$_GET["page_del"];
			page_del( $del );
			
			// Переход обратно
			header( "Location: {$ADMIN_URL}id=$id" );
			die;
		}
	}
	
	
	// Отобразить текущий уровень
	function nav_level( $nid, $level, $type )
	{
		global $id;
		global $GID;
		global $LEVEL;
		global $TYPE;
		global $PAGE_TYPE;
		global $ADMIN_URL;
		
		// Прямая или обратная сортировка
		if( $PAGE_TYPE[$type]["reverse"] )
			$order = "pos DESC, id DESC";
		else
			$order = "pos, id";
		
		$query = "SELECT id, title, type, hide FROM page WHERE gid=$nid ORDER BY $order";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			// Выделение блока
			$class = "";
			if( $row["id"] == $id )
				$class = "sel";
			if( $row["id"]==$GID[$level] && ($level!=$LEVEL || ($level==$LEVEL && !empty($PAGE_TYPE[$TYPE]["sub"]))) )
				$class .= " open";
			if( $level-1==$LEVEL || ($level==$LEVEL && empty($PAGE_TYPE[$TYPE]["sub"])) )
				$class .= " last";
			
			// Скрытый ли блок
			$show = $row["hide"] ? "hide" : "show";
			echo "<li id='{$row["id"]}' class='$class'>";
				echo "<i class='i-sort'></i>";
				// Отступы
				for( $i=0; $i<$level-1; $i++ )
					echo "<div class='indent'></div>";
				// Не неудаляемая
				if( !$PAGE_TYPE[$row["type"]]["lock"] )
					echo "<a href='{$ADMIN_URL}id=$id&page_del={$row["id"]}' class='round del confirm' data-title='Удалить \"{$row["title"]}\" вместе с подразделами?'><i class='i-del'></i></a>";
				// Неудаляемая
				else
					echo "<div class='round lock'></div>";
				echo "<div class='round $show'><i class='i-'></i></div>";
				echo "<a href='{$ADMIN_URL}id={$row["id"]}' class='block' title='{$row["title"]}'>{$row["title"]}";
					// Стрелочка
					if( !empty($PAGE_TYPE[$row["type"]]["sub"]) )
						echo "<i class='i-arrow'></i>";
				echo "</a>";
			echo "</li>\n";
			
			// Подразделы
			if( $row["id"]==$GID[$level] && !empty($PAGE_TYPE[$row["type"]]["sub"]) )
			{
				// Кнопка "добавить подраздел" если последний уровень вложенности
				if( $level==$LEVEL || ($level==$LEVEL-1 && empty($PAGE_TYPE[$TYPE]["sub"])) )
				{
					$type = $PAGE_TYPE[$row["type"]]["sub"][0] ? $PAGE_TYPE[$row["type"]]["sub"][0] : "Страница";
					echo "<div class='add'>Добавить подраздел <a href='#' data-id='{$row["id"]}' data-type='$type'><i class='i-plus'></i></a></div>";
				}
				nav_level( $row["id"], $level+1, $row["type"] );
				if( $level == $LEVEL )
					echo "<hr>";
			}
		}
		// Последний уровень и нет подразделов
		if( $level==$LEVEL && empty($PAGE_TYPE[$TYPE]["sub"]) )
			echo "<hr>";
	}
	
	// Отображение навигации
	function nav_content()
	{
		global $id;
		global $LEVEL;
		global $PAGE_TYPE;
		global $ADMIN_URL;
		
		echo "<div id='nav_title'>";
		if( $LEVEL == 0 )
		{
			$type = $PAGE_TYPE["root"]["sub"][0] ? $PAGE_TYPE["root"]["sub"][0] : "Страница";
			echo "<a href='#' class='add' data-id='1' data-type='$type'><i class='i-plus'></i></a>";
		}
		echo "<a href='{$ADMIN_URL}'>Разделы</a></div>\n";
		
		// Список первого уровня
		nav_level( 0, 1, "" );
	}
?>
