<?php
	// Ссылка на страницу
	function admin_goto( $id )
	{
		global $id, $ADMIN_URL;
		
		if( $id && $_SESSION["admin"] )
		{
			return "<br><br><a href='{$ADMIN_URL}id=$id'>Перейти к редактированию</a><br><br>\n";
		}
	}
