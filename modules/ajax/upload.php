<?php
	run( "auth" );
	load_modules( "upload" );
	
	
	// Добавить файл в базу и запустить хуки
	function add_file( $file, $name )
	{
		global $id;
		global $status;
		
		// Файл загружен
		if( is_uploaded_file($file["tmp_name"]) || $file["url"] )
		{
			// Тип файла
			$ext = strtolower( substr($file["name"], 1+strrpos($file["name"], ".")) );
			
			// Сохранение в БД
			$fid = db_insert( "file", array("gid"=>$id, "filename"=>$file["name"], "type"=>$ext, "gallery"=>$name) );
			
			// Пемещение файла
			$target = "files/$fid.$ext";
			// Скачивание
			if( $file["url"] )
			{
				$res = file_get_contents( $file["url"] );
				$res2 = file_put_contents( $target, $res );
				if( !$res || !$res2 )
				{
					delete_file( $fid );
					$status[] = "Ошибка загрузки файла по ссылке: {$file["url"]}";
				}
			}
			else
			{
				$res = move_uploaded_file( $file["tmp_name"], $target );
				if( !$res )
				{
					delete_file( $fid );
					$status[] = "Ошибка загрузки файла: {$file["name"]}";
				}
			}
			chmod( $target, 0644 );
			
			// Хуки
			run( "upload", array("path"=>$target, "id"=>$fid, "ext"=>$ext, "inputname"=>$name, "filename"=>$file["name"]) );
			
			$status[] = "ok";
		}
		elseif( $file["name"] && !is_uploaded_file($file["tmp_name"]) && !$file["url"])
		{
			$status[] = "Ошибка загрузки файла: {$file["name"]}";
		}
	}
	
	// Инициализация модулей
	run( "init" );
	
	$status = array();
	
	// Загрузка из URL
	if( $_POST["url"] )
	{
		$url = explode( " ", $_POST["url"] );
		foreach( $url as $u )
		{
			$name = substr( $u, 1+strrpos($u, "/") );
			add_file( array("name"=>$name, "url"=>$u), $_POST["gallery"] );
		}
	}
	
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
	
	$notify = array();
	if( count($status) )
	{
		foreach( $status as $s )
		{
			if( $s != "ok" )
				$notify[] = array( "text"=>$s, "type"=>"warning" );
		}
		
		if( !count($notify) )
			$notify[] = array( "text"=>"Файл успешно загружен", "type"=>"success" );
	}
	else
		$notify[] = array( "text"=>"Файл не выбран!" );
	
	$SCRIPT[] = "parent.show_notify( ".json_encode($notify, true) .")";
	head();
