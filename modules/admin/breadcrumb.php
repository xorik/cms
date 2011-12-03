<?
	hook( "menu", "menu_crumb", 0 );
	
	function menu_crumb()
	{
		global $id;
		
		// $id не найден или 0
		if( !$id )
			return;
		
		// От текущего элемента к родительским
		$bid = $id;
		do
		{
			$query = "SELECT gid, title FROM page WHERE id=$bid";
			$row = mysql_fetch_array( mysql_query($query) );
			
			// Текущая страница
			if( $bid==$id && $bid )
				$crumb = " &gt; <b>{$row["title"]}</b>";
			elseif( $bid )
				$crumb = " &gt; <a href='".ADMIN."id=$bid'>{$row["title"]}</a>" . $crumb;
			// Корень
			if( !$row["gid"] && $bid )
				$crumb = "<a href='".ADMIN."'>Разделы</a>" . $crumb;
			
			// Переходим вверх
			$bid = $row["gid"];
		}
		while( $bid );
		?>
			<div id='crumb'><?= $crumb ?></div>
		<?
	}
?>
