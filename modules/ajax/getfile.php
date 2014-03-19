<?
	$id = (int)$_GET["fid"];
	
	// Имя файла и тип
	$row = db_select_one( "SELECT filename, type FROM file WHERE id=$id" );
	// Не найден
	if( !$row )
	{
		header( "HTTP/1.0 404 Not Found" );
		die( "404 File not found" );
	}
	
	// Путь и mime
	$file = "files/$id.{$row["type"]}";
	header( "Content-type: application/octet-stream" );
	header( "Content-Disposition: attachment; filename=\"{$row["filename"]}\"" );
	header( "Content-Length: ". filesize($file) );
	readfile( $file );
?>
