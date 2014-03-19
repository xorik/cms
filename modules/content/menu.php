<?
	// Отобразить меню
	function menu( $gid, $templ="{}" )
	{
		global $id;
		global $GID;
		global $CONFIG;
		
		if( !$GID )
			$GID = array();
		
		$rows = db_select( "SELECT id, title FROM page WHERE gid=$gid AND hide=0 ORDER BY pos, id" );
		foreach( $rows as $row )
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
