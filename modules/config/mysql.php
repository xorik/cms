<?
	if( $_GET["edit"] != "mysql" )
		return;
	
	hook( "init", "mysql_init" );
	hook( "content", "mysql_content" );
	
	
	function mysql_init()
	{
		if( count($_POST) )
		{
			global $CONFIG;
			
			foreach( array("db_host", "db_user", "db_pass", "db_db") AS $v )
			{
				if( $_POST[$v] )
					$CONFIG[$v] = $_POST[$v];
			}
			config_write();
			clear_post();
		}
	}
	
	function mysql_content()
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
