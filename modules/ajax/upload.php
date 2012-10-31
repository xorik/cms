<?
	run( "auth" );
	load_modules( "upload" );
	
	
	// Добавить файл в базу и запустить хуки
	function add_file( $file, $name )
	{
		global $id;
		
		// Файл загружен
		if( is_uploaded_file($file["tmp_name"]) )
		{
			// Тип файла
			$ext = strtolower( substr($file["name"], 1+strrpos($file["name"], ".")) );
			
			// Сохранение в БД
			$query = "INSERT INTO file (gid, filename, type) VALUES ($id, '{$file["name"]}', '$ext')";
			mysql_query( $query );
			$fid = mysql_insert_id();
			
			// Пемещение файла
			$target = "files/$fid.$ext";
			move_uploaded_file( $file["tmp_name"], $target );
			chmod( $target, 0644 );
			
			// Хуки
			run( "upload", array("path"=>$target, "id"=>$fid, "ext"=>$ext, "inputname"=>$name, "filename"=>$file["name"]) );
		}
	}
	
	// Инициализация модулей
	run( "init" );
	
	// Для каждого файла
	foreach( $_FILES as $name=>$f )
	{
		if( is_array($f["tmp_name"]) )
		{
			foreach( $f["tmp_name"] as $k=>$v )
				add_file( array("name"=>$f["name"][$k], "tmp_name"=>$f["tmp_name"][$k]), $name );
		}
		else
			add_file( $f, $name );
	}
	
?>
