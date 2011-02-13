<?
	hook( "base_show", "base_goto", 5 );
	
	// Ссылка на страницу
	function base_goto( $id )
	{
		echo "<a href='".path($id)."'>Ссылка на страницу</a><br>\n";
	}
?>
