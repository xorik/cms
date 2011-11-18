<?
	// Проверка пароля при входе
	if( $_POST["admin_pass"] )
		if( md5($_POST["admin_pass"] . $CONFIG["admin_salt"]) == $CONFIG["admin_hash"] )
			$_SESSION["hash"] = $CONFIG["admin_hash"];
	
	// Выход
	if( $_GET["logout"] )
	{
		session_destroy();
		header( "Location: ." );
		die;
	}
	
	// Проверка хеша
	if( $_SESSION["hash"] != $CONFIG["admin_hash"] )
	{
		// Форма входа
		die ( "<form method='post'><input type='password' name='admin_pass'><input type='submit' value='Enter'></form>" );
	}
?>
