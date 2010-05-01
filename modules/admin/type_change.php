<?
	hook_add( "base_show", "type_base_show", 15 );
	hook_add( "base_submit", "type_base_submit" );
	
	// Выбор типа в редактировании
	function type_base_show( $id )
	{
		global $TYPE;
		global $PAGE_TYPE;
		
		echo "Тип: <select name='type'>\n";
		foreach( $PAGE_TYPE as $v )
			if( $v == $TYPE )
				echo "<option selected>$v</option>\n";
			else
				echo "<option>$v</option>\n";
		
		echo "</select><br>\n";
	}
	
	// Смена типа страницы
	function type_base_submit( $id )
	{
		set_prop( $id, "type", $_POST["type"] );
	}
?>
