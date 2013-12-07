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
		global $CONFIG;
		
		?>
			<h2>Настройки сайта</h2>
			<form method='post'>
			<table class='base'>
				<col width='250'>
				<col>
			<tr><td>Заголовок сайта:</td> <td><input type='text' name='title' value='<?= $CONFIG["title"] ?>'></td></tr>
			<tr><td>Расширенные настройки:</td> <td><input type='checkbox' name='adv' <? if( $CONFIG["adv"] ) echo "checked" ?>></td></tr>
		<?
			// Расширенные настройки
			if( $CONFIG["adv"] ):
			?>
				<tr><td>Использовать mod_rewrite:</td> <td><input type='checkbox' name='rewrite' <? if( $CONFIG["rewrite"] ) echo "checked" ?>></td></tr>
				<tr><td>Загрузка файлов по ссылке:</td> <td><input type='checkbox' name='load_url' <? if( $CONFIG["load_url"] ) echo "checked" ?>></td></tr>
				<tr><td>Основной шаблон:</td> <td><input type='text' name='template' value='<?= $CONFIG["template"] ?>'></td></tr>
				<tr><td>Корень сайта:</td> <td><input type='text' name='root' value='<?= $CONFIG["root"] ?>'></td></tr>
				<tr><td>id главной страницы:</td> <td><input type='text' name='main' value='<?= $CONFIG["main"] ?>' class='small'></td></tr>
				<tr><td>id страницы 404:</td> <td><input type='text' name='404_page' value='<?= $CONFIG["404_page"] ?>' class='small'> <small>(Оставьте 0, чтобы использовать стандартную)</small></td></tr>
			<?
			endif;
		?>
			<tr><td colspan='2'><input type='submit' value='Сохранить'></td></tr>
			</table>
			</form>
		<?
	}
	
	function chpass_content()
	{
		?>
			<h3>Смена пароля</h3>
			<form method='post'>
			<table class='base'>
				<col width='250'>
				<col>
				<tr><td>Старый пароль:</td> <td><input type='password' name='oldpass'></td></tr>
				<tr><td>Новый пароль:</td> <td><input type='password' name='pass1'></td></tr>
				<tr><td>Еще раз:</td> <td><input type='password' name='pass2'></td></tr>
				<tr><td colspan='2'><input type='submit' value='Сохранить'></td></tr>
			</table>
			</form>
		<?
	}
?>
