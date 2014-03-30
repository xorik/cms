<?
	run( "auth" );
	
	load_modules( "admin" );
	run( "init" );
	
	// Отобразить текущий уровень
	function nav_level( $nid, $level, $type )
	{
		global $PAGE_TYPE;
		
		// Прямая или обратная сортировка
		if( $PAGE_TYPE[$type]["reverse"] )
			$order = "pos DESC, id DESC";
		else
			$order = "pos, id";

		$rows = db_select( "SELECT id, title, type, hide FROM page WHERE gid=$nid ORDER BY $order" );
		foreach( $rows as $row )
		{
			// Скрытый ли блок
			$show = $row["hide"] ? "hide" : "show";
			echo "<li>";
				echo "<i class='i-sort'></i>";
				// Отступы
				for( $i=0; $i<$level-1; $i++ )
				{
					echo "<div class='indent'></div>";
				}
				// Не неудаляемая
				if( !$PAGE_TYPE[$row["type"]]["lock"] )
				{
					echo "<a href='#' class='round del' data-title='Удалить \"{$row["title"]}\" вместе с подразделами?'><i class='i-del'></i></a>";
				}
				// Неудаляемая
				else
				{
					echo "<div class='round lock'></div>";
				}
				echo "<div class='round $show'><i class='i-'></i></div>";
				echo "<a href='#' class='block' data-id='{$row["id"]}' title='{$row["title"]}'>{$row["title"]}";
					// Стрелочка
					if( !empty($PAGE_TYPE[$row["type"]]["sub"]) )
					{
						echo "<i class='i-arrow'></i>";
					}
				echo "</a>";
			echo "</li>\n";
			
			// Подразделы
			if( !empty($PAGE_TYPE[$row["type"]]["sub"]) )
			{
				echo "<div class='sub'>";
				// Кнопка "добавить подраздел"
				$type = $PAGE_TYPE[$row["type"]]["sub"][0] ? $PAGE_TYPE[$row["type"]]["sub"][0] : "Страница";
				echo "<div class='add'>Добавить подраздел <a href='#' data-gid='{$row["id"]}' data-type='$type'><i class='i-plus'></i></a></div>";
				nav_level( $row["id"], $level+1, $row["type"] );
				echo "<hr></div>";
			}
		}
	}
	
	echo "<div id='nav_title'>";
	$type = $PAGE_TYPE["root"]["sub"][0] ? $PAGE_TYPE["root"]["sub"][0] : "Страница";
	echo "<a href='#' class='add' data-gid='0' data-type='$type'><i class='i-plus'></i></a>";
	echo "<a href='{$ADMIN_URL}' data-id='0'>Разделы</a></div>\n";
	
	// Список первого уровня
	nav_level( 0, 1, "" );
?>
