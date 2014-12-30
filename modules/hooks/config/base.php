<?php
	if( $_GET["edit"] )
		return;
	
	hook( "init", "config_init" );
	hook( "content", "config_content" );
	hook( "content", "chpass_content" );
	
	
	// Сохранение конфига
	function config_init()
	{
		global $CONFIG, $CONFIG_URL;
		
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
				$_SESSION["notify"] = array( array("text"=>"Пароль успешно изменен! Необходимо заново войти в систему", "type"=>"success", "timeout"=>10000) );
				$_SESSION["chpass"] = 1;
			}
			else
			{
				$_SESSION["notify"][] = array( "text"=>"Неправильный пароль или новые пароли не совпадают!" );
				clear_post();
			}
		}
		
		if( $_SESSION["chpass"] )
		{
			hook( "content", "cphass_refresh" );
			unset( $_SESSION["chpass"] );
		}
		
		if( $CONFIG["adv"] && !$_SESSION["adv_info"] )
		{
			$_SESSION["notify"][] = array( "text"=>"Расширенные настройки включены, поэтому изменяйте только то, что понимаете или отключите расширенные настройки!", "type"=>"warning", "timeout"=>20000 );
			$_SESSION["adv_info"] = 1;
		}
		
		// Сохранение конфига
		if( !$_POST["title"] )
			return;
		
		if( $CONFIG["adv"] )
		{
			$CONFIG["rewrite"] = $_POST["rewrite"] == "on";
			$CONFIG["template"] = $_POST["template"];
			$CONFIG["main"] = (int)$_POST["main"];
			$CONFIG["404_page"] = (int)$_POST["404_page"];
		}
		
		// Сохраняем переменные в $CONFIG, а потом в config.php
		$CONFIG["title"] = $_POST["title"];
		$CONFIG["load_url"] = $_POST["load_url"] == "on";
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
	
	
	function cphass_refresh()
	{
		echo "<meta http-equiv='refresh' content='5;url=$CONFIG_URL'>";
	}
