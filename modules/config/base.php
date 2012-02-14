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
			if( $_POST["db_pass"] )
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
		
		if( $CONFIG["adv"] )
		{
			$CONFIG["rewrite"] = $_POST["rewrite"] == "on";
			$CONFIG["main"] = (int)$_POST["main"];
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
		global $CONFIG;
		
		?>
			<h2>Настройки сайта</h2>
			<form method='post'>
			<table class='base'>
				<col width='150'>
				<col>
			<tr><td>Заголовок сайта:</td> <td><input type='text' name='title' value='<?= $CONFIG["title"] ?>'></td></tr>
			<tr><td>Расширенные настройки (mysql, SEO):</td> <td><input type='checkbox' name='adv' <? if( $CONFIG["adv"] ) echo "checked" ?>></td></tr>
		<?
			// Расширенные настройки
			if( $CONFIG["adv"] ):
			?>
				<tr><td>Использовать mod_rewrite:</td> <td><input type='checkbox' name='rewrite' <? if( $CONFIG["rewrite"] ) echo "checked" ?>></td></tr>
				<tr><td>id главной страницы:</td> <td><input type='text' name='main' value='<?= $CONFIG["main"] ?>' class='small'></td></tr>
			<?
			endif;
		?>
			<tr><td colspan='2'><input type='submit' value='Сохранить'></td></tr>
			</table>
			</form>
			
			<h3>Смена пароля</h3>
			<form method='post'>
			<table class='base'>
				<col width='150'>
				<col>
				<tr><td>Старый пароль:</td> <td><input type='password' name='oldpass'></td></tr>
				<tr><td>Новый пароль:</td> <td><input type='password' name='pass1'></td></tr>
				<tr><td>Еще раз:</td> <td><input type='password' name='pass2'></td></tr>
				<tr><td colspan='2'><input type='submit' value='Сохранить'></td></tr>
			</table>
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
			<table class='base'>
				<col width='150'>
				<col>
				
				<tr><td>host:</td> <td><input type='text' name='db_host' value='<?= $CONFIG["db_host"] ?>'></td></tr>
				<tr><td>user:</td> <td><input type='text' name='db_user' value='<?= $CONFIG["db_user"] ?>'></td></tr>
				<tr><td>pass:</td> <td><input type='password' name='db_pass'></td></tr>
				<tr><td>db:</td> <td><input type='text' name='db_db' value='<?= $CONFIG["db_db"] ?>'></td></tr>
				<tr><td colspan='2'><input type='submit' value='Сохранить'></td></tr>
			</table>
			</form>
		<?
	}
?>
