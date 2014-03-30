<?
	hook( "auth", "base_auth" );
	
	// Простая авторизация по паролю
	function base_auth()
	{
		global $CONFIG;
		
		// Проверка пароля при входе
		if( $_POST["admin_pass"] )
			if( md5($_POST["admin_pass"] . $CONFIG["admin_salt"]) == $CONFIG["admin_hash"] )
			{
				$_SESSION["hash"] = $CONFIG["admin_hash"];
				$_SESSION["admin"] = 1;
				clear_post();
			}
		
		// Выход
		if( $_GET["logout"] )
		{
			unset( $_SESSION["hash"] );
			unset( $_SESSION["admin"] );
			header( "Location: ." );
			die;
		}
		
		// Проверка хеша
		if( $_SESSION["hash"] != $CONFIG["admin_hash"] )
		{
			unset( $_SESSION["admin"] );
			header( "HTTP/1.0 403 Forbidden" );
			
			// Форма входа
			die ( "<form method='post'><input type='password' name='admin_pass'><input type='submit' value='Enter'></form>" );
		}
	}
?>
