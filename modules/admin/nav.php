<?
	hook( "nav", "nav_content" );
	
	// Отобразить текущий уровень
	function nav_level( $nid, $level )
	{
		global $id;
		global $GID;
		global $LEVEL;
		global $PAGE_TYPE;
		
		$query = "SELECT id, title, type, hide FROM page WHERE gid=$nid";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			// Выделение блока
			if( $row["id"] == $id )
				$class = "sel";
			elseif( $row["id"] == $GID[$level] )
				$class = "open";
			elseif( $level-1 == $LEVEL )
				$class = "last";
			else
				$class = "";
			// Скрытый ли блок
			$show = $row["hide"] ? "hide" : "show";
			echo "<li class='$class'>";
				echo "<div class='sort'></div>";
				// Отступы
				for( $i=0; $i<$level-1; $i++ )
					echo "<div class='indent'></div>";
				echo "<a href='#' class='round del'></a>";
				echo "<div class='round $show'></div>";
				echo "<a href='".ADMIN."id={$row["id"]}' class='block'>{$row["title"]}";
					// Стрелочка
					if( !$PAGE_TYPE[$row["type"]]["nosub"] )
						echo "<div class='arrow'></div>";
				echo "</a>";
			echo "</li>\n";
			
			// Подразделы
			if( $row["id"]==$GID[$level] && !$PAGE_TYPE[$row["type"]]["nosub"] )
			{
				if( $level == $LEVEL )
					echo "<div class='add'>Добавить подраздел <a href='#'></a></div>";
				nav_level( $row["id"], $level+1 );
				if( $level == $LEVEL )
					echo "<hr>";
			}
		}
	}
	
	// Отображение навигации
	function nav_content()
	{
		global $LEVEL;
		
		echo "<div id='nav_title'>Разделы";
		if( $LEVEL == 0 )
			echo "<a href='#'></a>";
		echo "</div>\n";
		
		// Список первого уровня
		nav_level( 0, 1 );
	}
?>
