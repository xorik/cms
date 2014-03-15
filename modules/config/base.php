<?
	if( $_GET["edit"] )
		return;
	
	hook( "init", "config_init" );
	hook( "content", "config_content" );
	hook( "content", "chpass_content" );
	
	
	// Сохранение конфига
	function config_init()
	{
		global $CONFIG;
		global $CONFIG_URL;
		
		// Смена пароля
		if( $_POST["oldpass"] )
		{
			// Правильный пароль и пароли совпадают
			if( md5($_POST["oldpass"].$CONFIG["admin_salt"])==$CONFIG["admin_hash"] && $_POST["pass1"]==$_POST["pass2"] )
			{
				// Генерация соли и хеша пароля
				$CONFIG["admin_salt"] = base64_encode( crc32(time()) );
				$CONFIG["admin_hash"] = md5( $_POST["pass1"] . $CONFIG["admin_salt"] );
				
				// Запись конфига и разлогин
				config_write();
				die( "Пароль успешно изменен!<br><a href='$CONFIG_URL'>Назад</a><meta http-equiv='refresh' content='3;url=$CONFIG_URL'>" );
			}
		}
		
		// Сохранение конфига
		if( !$_POST["title"] )
			return;
		
		if( $CONFIG["adv"] )
		{
			$CONFIG["rewrite"] = $_POST["rewrite"] == "on";
			$CONFIG["template"] = $_POST["template"];
			$CONFIG["main"] = (int)$_POST["main"];
			$CONFIG["root"] = $_POST["root"];
			$CONFIG["404_page"] = (int)$_POST["404_page"];
			$CONFIG["load_url"] = $_POST["load_url"] == "on";
		}
		
		// Сохраняем переменные в $CONFIG, а потом в config.php
		$CONFIG["title"] = $_POST["title"];
		$CONFIG["adv"] = $_POST["adv"] == "on";
		
		config_write();
		clear_post();
	}
	
	
	// Контент настроек
	function config_content()
	{
		template( "modules/templates/config_base.tpl" );
	}
	
	function chpass_content()
	{
		template( "modules/templates/config_chpass.tpl" );
	}
?>
