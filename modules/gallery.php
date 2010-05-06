<?
	hook_add( "gallery_show", "default_gallery_show" );
	
	load_modules( "gallery" );
	
	// Показать элемент галереи
	function default_gallery_show( $f )
	{
		if( $f["type"]=="png" || $f["type"]=="jpg" || $f["type"]=="jpeg" || $f["type"]=="gif" )
			echo "<img src='files/{$f["id"]}_.jpg' class='pic'><br>";
		else
			echo "<img src='modules/img/file.png'> {$f["filename"]}";
	}
	
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
	
	$query = "SELECT id, type, filename FROM file WHERE gid=$id ORDER BY id DESC";
	$res = mysql_query( $query );
	while( $row = mysql_fetch_array($res) )
	{
		echo "<div class='block'>";
			echo "<input type='checkbox' name='{$row["id"]}'>";
			hook_run( "gallery_show", array("id"=>$row["id"], "type"=>$row["type"], "filename"=>$row["filename"]) );
		echo "</div>\n";
	}
	
	?>
		<div style='clear:both'></div>
		<input type='submit' name='del' value='Удалить выбранные' onclick='if(confirm("Удалить выбранные файлы?")) return true; return false;'>
		</form>
	<?
?>
