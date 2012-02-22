<?
	hook( "init", "tinymce_init", 80 );
	
	
	function tinymce_init()
	{
		global $CONFIG;
		
		// Сохранение
		if( $_POST["formats"] )
		{
			$CONFIG["formats"] = $_POST["formats"];
			config_write();
			clear_post();
		}
		
		// Отображать только при расширенных настройках
		if( $CONFIG["adv"] )
			hook( "content", "tinymce_content" );
	}
	
	
	function tinymce_content()
	{
		global $CONFIG;
		
		?>
			<h3>Стили редактора</h3>
			
			<form method='post'>
				<textarea name='formats' cols='80' rows='7'><?= $CONFIG["formats"] ?></textarea>
				<br>
				<input type='submit' value='Сохранить'>
			</form>
		<?
	}
?>