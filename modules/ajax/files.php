<?
	run( "auth" );
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
			echo "<img src='res/img/admin/file.png'> {$f["filename"]}";
	}
	
	
	// Кнопка "удалить выбранные" файлы
	function del_files_action()
	{
		echo "<label><input type='checkbox' class='files_sel'> <small>Выделить все</small> </label>";
		echo "<input type='submit' name='del' value='Удалить выбранные' class='confirm' data-title='Удалить выбранные файлы?'>\n";
	}
	
	
	function files_init()
	{
		// Удаление выбранных
		if( !$_POST["del"] )
			return;
		
		foreach( $_POST as $id => $v )
			if( $v == "on" )
				delete_file( $id );
	}
	
	run( "init" );
	
	?>
		<form action='?do=ajax&file=files&id=<?= $id ?>' method='post' target='upload'>
	<?
	
	$query = "SELECT id, type, filename FROM file WHERE gid=$id ORDER BY pos, id";
	$res = mysql_query( $query );
	// Подсказка, если 2 или больше файла
	if( mysql_num_rows($res) > 1 )
		echo "<small><br>Файлы сортируются мышкой: захватите и перетащите</small>";
	
	echo "<div>";
	
	while( $row = mysql_fetch_array($res) )
	{
		echo "<div id='{$row["id"]}' class='block'>";
			echo "<input type='checkbox' name='{$row["id"]}'>";
			run( "files_show", array("id"=>$row["id"], "type"=>$row["type"], "filename"=>$row["filename"]) );
		echo "</div>\n";
	}
	
	echo "</div>";
	
	// Кнопка удаления, если есть хотя бы 1 файл
	if( mysql_num_rows($res) > 0 )
		run( "files_action" );
	?>
		</form>
	<?
?>
