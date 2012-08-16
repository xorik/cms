<?
	$id = (int)$_GET["fid"];
	
	// Имя файла и тип
	$query = "SELECT filename, type FROM file WHERE id=$id";
	$row = mysql_fetch_array( $res = mysql_query($query) );
	// Не найден
	if( mysql_num_rows($res) == 0 )
	{
		header( "HTTP/1.0 404 Not Found" );
		die( "404 File not found" );
	}
	
	// Путь и mime
	$file = "files/$id.{$row["type"]}";
	$mime = mime_content_type( $file );
	header( "Content-type: $mime" );
	header( "Content-Disposition: attachment; filename=\"{$row["filename"]}\"" );
	readfile( $file );
?>
