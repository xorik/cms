<?
	hook( "content", "admin_goto", 99 );
	
	// Ссылка на страницу
	function admin_goto( $id )
	{
		global $id;
		global $ADMIN_URL;
		
		if( $id && $_SESSION["admin"] )
			echo "<br><br><a href='{$ADMIN_URL}id=$id'>Перейти к редактированию</a><br><br>\n";
	}
?>
