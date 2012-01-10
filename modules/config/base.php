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
		
		// Сохранение конфига
		if( !$_POST["title"] )
			return;
		
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
			<h3>Настройки сайта</h3>
			<form method='post'>
			
			Заголовок сайта: <input type='text' name='title' value='<?= $CONFIG["title"] ?>'><br>
			Расширенные настройки (mysql, SEO): <input type='checkbox' name='adv' <? if( $CONFIG["adv"] ) echo "checked" ?>><br>
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
