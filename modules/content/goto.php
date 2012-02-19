<?
	hook( "content", "admin_goto", 99 );
	
	// Ссылка на страницу
	function admin_goto( $id )
	{
		global $id;
		global $CONFIG;
		
		if( $CONFIG["rewrite"] )
			define( "ADMIN", "admin?" );
		else
			define( "ADMIN", "?do=admin&" );
		
		if( $id && $_SESSION["hash"]==$CONFIG["admin_hash"] )
			echo "<br><br><a href='". ADMIN ."id=$id'>Перейти к редактированию</a><br><br>\n";
	}
?>
