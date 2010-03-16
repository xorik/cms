<?
	load_modules( "gallery_" );
	
	$id = (int)$_GET["id"];
	
	// Удаление выбранных
	if( $_POST["del"] )
	{
		foreach( $_POST as $id => $v )
			if( $v == "on" )
			{
				delete_file( $id );
				unlink( "files/{$id}_.jpg" );
			}
		// Обновление через аякс
		global $SCRIPT;
		$SCRIPT[] = 'window.top.window.update_gallery();';
		head();
		die();
	}
	
	?>
		<form action='admin.php?do=gallery&id=<?= $id ?>' method='post' target='upload'>
	<?
	
	$query = "SELECT id FROM file WHERE gid=$id AND (type='png' OR type='jpg' OR type='jpeg' OR type='gif') ORDER BY id DESC";
	$res = mysql_query( $query );
	while( $row = mysql_fetch_array($res) )
	{
		echo "<div class='block'>";
			echo "<input type='checkbox' name='{$row["id"]}'>";
			echo "<img src='files/{$row["id"]}_.jpg' class='pic'><br>";
			hook_run( "gallery_show" );
		echo "</div>\n";
	}
	
	?>
		<div style='clear:both'></div>
		<input type='submit' name='del' value='Удалить выбранные' onclick='if(confirm("Удалить выбранные изображения?")) return true; return false;'>
		</form>
	<?
?>
