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
		
		$query = "SELECT id, title, type, hide FROM page WHERE gid=$nid ORDER BY $order";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			// Скрытый ли блок
			$show = $row["hide"] ? "hide" : "show";
			echo "<li id='li{$row["id"]}'>";
				echo "<i class='i-sort'></i>";
				// Отступы
				for( $i=0; $i<$level-1; $i++ )
					echo "<div class='indent'></div>";
				// Не неудаляемая
				if( !$PAGE_TYPE[$row["type"]]["lock"] )
					echo "<a href='#' class='round del confirm' data-title='Удалить \"{$row["title"]}\" вместе с подразделами?'><i class='i-del'></i></a>";
				// Неудаляемая
				else
					echo "<div class='round lock'></div>";
				echo "<div class='round $show'><i class='i-'></i></div>";
				echo "<a href='#' class='block' title='{$row["title"]}'>{$row["title"]}";
					// Стрелочка
					if( !empty($PAGE_TYPE[$row["type"]]["sub"]) )
						echo "<i class='i-arrow'></i>";
				echo "</a>";
			echo "</li>\n";
			
			// Подразделы
			if( !empty($PAGE_TYPE[$row["type"]]["sub"]) )
			{
				echo "<div class='sub'>";
				// Кнопка "добавить подраздел"
				echo "<div class='add'>Добавить подраздел <a href#'><i class='i-plus'></i></a></div>";
				nav_level( $row["id"], $level+1, $row["type"] );
				echo "<hr></div>";
			}
		}
	}
	
	echo "<div id='nav_title'>";
	echo "<a href='#' class='add'><i class='i-plus'></i></a>";
	echo "<a href='{$ADMIN_URL}'>Разделы</a></div>\n";
	
	// Список первого уровня
	nav_level( 0, 1, "" );
?>
