<?
	require( "modules/auth.php" );
	hook( "init", "files_init" );
	hook( "files_show", "default_files_show" );
	hook( "files_action", "del_files_action", 90 );
	
	load_modules( "files" );
	
	// Показать элемент галереи
	function default_files_show( $f )
	{
		if( $f["type"]=="png" || $f["type"]=="jpg" || $f["type"]=="jpeg" || $f["type"]=="gif" )
			echo "<img src='files/{$f["id"]}_.jpg' class='pic'><br>";
		else
			echo "<img src='modules/img/file.png'> {$f["filename"]}";
	}
	
	
	// Кнопка "удалить выбранные" файлы
	function del_files_action()
	{
		echo "<input type='submit' name='del' value='Удалить выбранные' onclick='if(confirm(\"Удалить выбранные файлы?\")) return true; return false;'>\n";
	}
	
	
	function files_init()
	{
		// Удаление выбранных
		if( !$_POST["del"] )
			return;
		
		foreach( $_POST as $id => $v )
			if( $v == "on" )
			{
				delete_file( $id );
				unlink( "files/{$id}_.jpg" );
			}
	}
	
	run( "init" );
	
	?>
		<form action='?do=ajax&file=files&id=<?= $id ?>' method='post' target='upload'>
	<?
	
	$query = "SELECT id, type, filename FROM file WHERE gid=$id ORDER BY pos, id";
	$res = mysql_query( $query );
	while( $row = mysql_fetch_array($res) )
	{
		echo "<div id='{$row["id"]}' class='block'>";
			echo "<input type='checkbox' name='{$row["id"]}'>";
			run( "files_show", array("id"=>$row["id"], "type"=>$row["type"], "filename"=>$row["filename"]) );
		echo "</div>\n";
	}
	
	// Кнопка удаления, если есть хотя бы 1 файл
	if( mysql_num_rows($res) > 0 )
	{
		echo "<div style='clear:both'></div>\n";
		run( "files_action" );
	}
	?>
		</form>
	<?
?>
