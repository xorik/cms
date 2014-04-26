<?php
	hook( "content", "base_content" );
	
	function base_content()
	{
		global $id;
		
		if( !$id )
		{
			global $CONFIG;
			
			if( $CONFIG["404_page"] )
				echo get_text( $CONFIG["404_page"] );
			else
				echo "<h3>Ошибка 404: Страница \"". urldecode($_SERVER["REQUEST_URI"]) ."\" не найдена!</h3>";
			
			return;
		}
		
		echo get_text( $id );
	}
