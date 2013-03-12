<?
	// Отобразить меню
	function menu( $gid, $templ="{}" )
	{
		global $id;
		global $GID;
		global $CONFIG;
		
		$query = "SELECT id, title FROM page WHERE gid=$gid AND hide=0 ORDER BY pos, id";
		$res = mysql_query( $query );
		while( $row = mysql_fetch_array($res) )
		{
			// Один из подразделов
			$sel = "";
			if( in_array($row["id"], $GID) )
			{
				if( ($row["id"]==$CONFIG["main"] && $id==$row["id"]) || $row["id"]!=$CONFIG["main"] )
					$sel = "class='sel'";
			}
			
			$s = "<a href='".path($row["id"])."' $sel>{$row["title"]}</a>";
			
			echo str_replace( "{}", $s, $templ );
		}
	}
?>
