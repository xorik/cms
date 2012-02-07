<?
	hook( "nav", "nav_content" );
	
	
	// Отображение навигации
	function nav_content()
	{
		?>
			<div id='nav_title'>
				Разделы
				<a href='#'></a>
			</div>
		<?
		
		global $id;
		global $TYPE;
		global $PAGE_TYPE;
		
		// Список первого уровня
		$query = "SELECT id, title, type, hide FROM page WHERE gid=0";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			// Выделение блока
			$class =  $row["id"]==$id ? "class='sel'" : "";
			// Скрытый ли блок
			$show = $row["hide"] ? "hide" : "show";
			echo "<li $class>";
				echo "<div class='sort'></div>";
				echo "<a href='#' class='round del'></a>";
				echo "<div class='round $show'></div>";
				echo "<a href='".ADMIN."id={$row["id"]}' class='block'>{$row["title"]}";
					// Стрелочка
					if( !$PAGE_TYPE[$row["type"]]["nosub"] )
						echo "<div class='arrow'></div>";
				echo "</a>";
			echo "</li>\n";
		}
	}
?>
