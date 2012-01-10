<?
	hook( "init", "config_init" );
	hook( "content", "config_content" );
	
	
	// Сохранение конфига
	function config_init()
	{
		if( !$_POST["title"] )
			return;
		
		global $CONFIG;
		
		// Сохраняем переменные в $CONFIG, а потом в config.php
		$CONFIG["title"] = $_POST["title"];
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
			<input type='submit' value='Сохранить'>
			</form>
		<?
	}
?>
