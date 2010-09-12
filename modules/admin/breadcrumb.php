<?
	hook( "menu", "breadcrumb", 0 );
	
	function breadcrumb()
	{
		// От текущего элемента к родительским
		$id = (int)$_GET["id"];
		do
		{
			
			$query = "SELECT gid, title FROM page WHERE id=$id";
			$row = mysql_fetch_array( mysql_query($query) );
			
			// Текущая страница
			if( $id == $_GET["id"] && $id )
				$crumb = " &gt; <b>{$row["title"]}</b>";
			elseif( $id )
				$crumb = " &gt; <a href='".ADMIN."id=$id'>{$row["title"]}</a>" . $crumb;
			// Корень
			if( !$row["gid"] && $id )
				$crumb = "<a href='".ADMIN."'>Разделы</a>" . $crumb;
			
			// Переходим вверх
			$id = $row["gid"];
		}
		while( $id );
		?>
			<div id='crumb'><?= $crumb ?></div>
		<?
	}
?>
