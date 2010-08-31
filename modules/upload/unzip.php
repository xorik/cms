<?
	hook_add( "upload", "unzip_upload", 10 );
	
	function unzip_upload( $file )
	{
		// Не zip
		if( $file["ext"] != "zip" )
			return;
		
		$id = (int)$_GET["id"];
		
		$zip = zip_open( $file["path"] );
		if( $zip )
		{
			while ( $zip_entry = zip_read($zip) )
			{
				// Тип файла
				$ext = strtolower( substr(zip_entry_name($zip_entry), 1+strrpos(zip_entry_name($zip_entry), ".")) );
				
				// Добавление файла в БД
				$query = "INSERT INTO file (gid, filename, type) VALUES ($id, '".zip_entry_name($zip_entry)."', '$ext')";
				mysql_query( $query );
				$new_id = mysql_insert_id();
				
				$target = "files/$new_id.$ext";
				
				// Источник: http://www.timlinden.com/blog/website-development/unzip-files-with-php/
				$fp = fopen( $target, "w" );
				if (zip_entry_open($zip, $zip_entry, "r"))
				{
					$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					fwrite($fp,"$buf");
					zip_entry_close($zip_entry);
					fclose($fp);
					chmod( $target, 0644 );
					
					// Хуки
					hook_run( "upload", array("path"=>$target, "id"=>$new_id, "ext"=>$ext, "inputname"=>$file["inputname"], "filename"=>$file["filename"]) );
				}
			}
			zip_close($zip);
		}
		
		// Удаляем файл
		delete_file( $file["id"] );
		
		// Удаляем остальные хуки для этого файла
		global $HOOK;
		$HOOK["upload"] = array();
	}
	
?>
