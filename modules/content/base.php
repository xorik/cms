<?php
	hook( "content", "base_content" );
	
	function base_content()
	{
		global $id, $CONFIG;
		
		if( !$id )
		{
			if( $CONFIG["404_page"] )
				echo get_text( $CONFIG["404_page"] );
			else
				echo "<h3>Ошибка 404: Страница \"". urldecode($_SERVER["REQUEST_URI"]) ."\" не найдена!</h3>";
			
			return;
		}
		
		echo get_text( $id );
	}


	// Ссылка на страницу
	function admin_goto( $id, $short )
	{
		global $ADMIN_URL;
		
		if( $id && $_SESSION["admin"] )
		{
			if( $short )
				return "<a href='{$ADMIN_URL}id=$id'>Редактировать</a>";
			else
				return "<br><br><a href='{$ADMIN_URL}id=$id'>Перейти к редактированию</a>";
		}
	}
