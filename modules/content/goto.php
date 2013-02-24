<?
	hook( "content", "admin_goto", 99 );
	
	// Ссылка на страницу
	function admin_goto( $id )
	{
		global $id;
		global $CONFIG;
		global $ADMIN_URL;
		
		if( $id && $_SESSION["hash"]==$CONFIG["admin_hash"] )
			echo "<br><br><a href='{$ADMIN_URL}id=$id'>Перейти к редактированию</a><br><br>\n";
	}
?>
