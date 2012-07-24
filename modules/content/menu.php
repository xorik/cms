<?
	// Отобразить меню
	function menu( $id, $level=1, $templ="{}" )
	{
		global $GID;
		
		$query = "SELECT id, title FROM page WHERE gid=$id AND hide=0 ORDER BY pos, id";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			if( $row["id"] == $GID[$level] )
				$s = "<a href='".path($row["id"])."' class='sel'>{$row["title"]}</a>";
			else
				$s = "<a href='".path($row["id"])."'>{$row["title"]}</a>";
			
			echo str_replace( "{}", $s, $templ );
		}
	}

?>
