<?
	require( "modules/auth.php" );
	load_modules( "upload" );
	
	// Инициализация модулей
	run( "init" );
	
	// Для каждого файла
	foreach( $_FILES as $name => $file )
		// Файл загружен
		if( is_uploaded_file($file["tmp_name"]) )
		{
			// Тип файла
			$ext = strtolower( substr($file["name"], 1+strrpos($file["name"], ".")) );
			
			// Сохранение в БД
			$query = "INSERT INTO file (gid, filename, type) VALUES ($id, '{$file["name"]}', '$ext')";
			mysql_query( $query );
			$id = mysql_insert_id();
			
			// Пемещение файла
			$target = "files/$id.$ext";
			move_uploaded_file( $file["tmp_name"], $target );
			chmod( $target, 0644 );
			
			// Хуки
			run( "upload", array("path"=>$target, "id"=>$id, "ext"=>$ext, "inputname"=>$name, "filename"=>$file["name"]) );
		}
?>
