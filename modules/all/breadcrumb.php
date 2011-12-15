<?
	hook( "init", "crumb_init" );
	hook( "crumb", "breadcrumb" );
	
	
	function crumb_init()
	{
		global $id;
		// Список id разделов
		global $GID;
		// Заголовки разделов
		global $GID_TITLE;
		// Уровень
		global $LEVEL;
		
		$LEVEL = 0;
		
		// $id не найден или 0
		if( !$id )
			return;
		
		$bid = $id;
		
		// От текущего элемента к родительским
		while( $bid )
		{
			$query = "SELECT gid, title FROM page WHERE id=$bid";
			$row = mysql_fetch_array( mysql_query($query) );
			
			$GID[] = $bid;
			$GID_TITLE[] = $row["title"];
			
			$bid = $row["gid"];
			$LEVEL++;
		}
		
		// Главная
		if( $_GET["do"]=="admin" )
		{
			$GID[] = 0;
			$GID_TITLE[] = "Разделы";
		}
		else
		{
			global $CONFIG;
			$GID[] = $CONFIG["main"];
			$GID_TITLE[] = "Главная";
		}
		
		// Переворачиваем массивы
		$GID = array_reverse( $GID );
		$GID_TITLE = array_reverse( $GID_TITLE );
	}
	
	
	function breadcrumb( $sep )
	{
		global $id;
		global $GID;
		global $GID_TITLE;
		global $LEVEL;
		
		$sep = $sep ? $sep : "&gt;";
		
		for( $i=0; $i<=$LEVEL; $i++ )
		{
			// Текущая
			if( $GID[$i] == $id )
				echo $GID_TITLE[$i];
			// Раздел админки
			elseif( $_GET["do"] == "admin" )
				echo "<a href='".ADMIN."id={$GID[$i]}'>{$GID_TITLE[$i]}</a> $sep ";
			// Раздел внешней части
			else
				echo "<a href='". path($GID[$i]) ."'>{$GID_TITLE[$i]}</a> $sep ";
		}
	}
?>
