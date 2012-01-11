<?
	hook( "init", "config_init" );
	hook( "content", "config_content" );
	
	
	// Сохранение конфига
	function config_init()
	{
		global $CONFIG;
		// Настройки БД, если включены расширенные настройки
		if( $CONFIG["adv"] )
		{
			hook( "content", "db_config", 90 );
			// Сохранение настроек
			if( $_POST["db_host"] )
			{
				foreach( array("db_host", "db_user", "db_pass", "db_db") AS $v )
					$CONFIG[$v] = $_POST[$v];
				config_write();
				clear_post();
				die();
			}
		}
		
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
				die( "Пароль успешно изменен!<br><a href='". CONFIG ."'>Назад</a><meta http-equiv='refresh' content='3;url=". CONFIG ."'>" );
			}
		}
		
		// Сохранение конфига
		if( !$_POST["title"] )
			return;
		
		// Сохраняем переменные в $CONFIG, а потом в config.php
		$CONFIG["title"] = $_POST["title"];
		$CONFIG["adv"] = $_POST["adv"] == "on";
		
		if( $CONFIG["adv"] )
		{
			$CONFIG["rewrite"] = $_POST["rewrite"] == "on";
			$CONFIG["main"] = (int)$_POST["main"];
		}
		config_write();
		clear_post();
	}
	
	
	// Контент настроек
	function config_content()
	{
		global $CONFIG;
		
		?>
			<h3>Настройки сайта</h3>
			<form method='post'>
			
			Заголовок сайта: <input type='text' name='title' value='<?= $CONFIG["title"] ?>'><br>
			Расширенные настройки (mysql, SEO): <input type='checkbox' name='adv' <? if( $CONFIG["adv"] ) echo "checked" ?>><br>
		<?
			// Расширенные настройки
			if( $CONFIG["adv"] ):
			?>
				Использовать mod_rewrite: <input type='checkbox' name='rewrite' <? if( $CONFIG["rewrite"] ) echo "checked" ?>><br>
				id главной страницы: <input type='text' name='main' value='<?= $CONFIG["main"] ?>' size='2'><br>
			<?
			endif;
		?>
			<input type='submit' value='Сохранить'>
			</form>
			
			<h3>Смена пароля</h3>
			<form method='post'>
				Старый пароль: <input type='password' name='oldpass'><br>
				Новый пароль: <input type='password' name='pass1'><br>
				Еще раз: <input type='password' name='pass2'><br>
				<input type='submit' value='Сохранить'>
			</form>
		<?
	}
	
	
	// Настройка базы данных
	function db_config()
	{
		global $CONFIG;
		
		?>
			<h3>Настройки MySQL</h3>
			<form method='post'>
			
			host: <input type='text' name='db_host' value='<?= $CONFIG["db_host"] ?>'><br>
			user: <input type='text' name='db_user' value='<?= $CONFIG["db_user"] ?>'><br>
			pass: <input type='text' name='db_pass' value='<?= $CONFIG["db_pass"] ?>'><br>
			db: <input type='text' name='db_db' value='<?= $CONFIG["db_db"] ?>'><br>
			<input type='submit' value='Сохранить'>
			</form>
		<?
	}
?>
