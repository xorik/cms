<?php
	hook( "init", "crumb_init" );
	hook( "crumb", "breadcrumb" );
	
	
	function crumb_init()
	{
		global $id,
		// Список id разделов
		$GID,
		// Заголовки разделов
		$GID_TITLE,
		// Типы разделов
		$GID_TYPE,
		// Уровень
		$LEVEL;
		
		$LEVEL = 0;
		
		// $id не найден или 0
		if( !$id )
			return;
		
		$bid = $id;
		
		// От текущего элемента к родительским
		while( $bid )
		{
			$row = db_select_one( "SELECT gid, title, type FROM page WHERE id=$bid" );
			
			$GID[] = $bid;
			$GID_TITLE[] = $row["title"];
			$GID_TYPE[] = $row["type"];
			
			$bid = $row["gid"];
			$LEVEL++;
		}
		
		// Главная
		$GID_TYPE[] = "root";
		if( $_GET["do"]=="ajax" && $_GET["file"]=="admin" )
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
		$GID_TYPE = array_reverse( $GID_TYPE );
	}
	
	
	function breadcrumb( $sep )
	{
		global $id, $GID, $GID_TITLE, $LEVEL;
		
		$sep = $sep ? $sep : "&gt;";
		
		for( $i=0; $i<=$LEVEL; $i++ )
		{
			// Текущая
			if( $GID[$i] == $id )
				echo $GID_TITLE[$i];
			// Раздел админки
			elseif( $_GET["do"]=="ajax" && $_GET["file"]=="admin" )
				echo "<a href='#' data-id='{$GID[$i]}'>{$GID_TITLE[$i]}</a> $sep ";
			// Раздел внешней части
			else
				echo "<a href='". path($GID[$i]) ."'>{$GID_TITLE[$i]}</a> $sep ";
		}
	}
