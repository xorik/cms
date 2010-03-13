<?
	// Проверка пароля при входе
	if( $_POST["admin_pass"] )
		if( md5($_POST["admin_pass"] . $config["admin_salt"]) == $config["admin_hash"] )
			$_SESSION["hash"] = $config["admin_hash"];
	
	// Выход
	if( $_GET["logout"] )
	{
		session_destroy();
		header( "Location: ." );
		die;
	}
	
	// Проверка хеша
	if( $_SESSION["hash"] != $config["admin_hash"] )
	{
		// Форма входа
		die ( "<form method='post'><input type='password' name='admin_pass'><input type='submit' value='Enter'></form>" );
	}
?>
