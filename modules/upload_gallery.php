<?
	hook_add( "upload", "gallery_upload" );
	
	function img_resize( $src, $w, $h )
	{
		global $config;
		
		// Открытие источника
		$size = getimagesize( $src );
		$format = strtolower( substr($size['mime'], strpos($size['mime'], '/')+1) );
		$icfunc = "imagecreatefrom" . $format;
		$isrc = $icfunc($src);
		
		// Вычисление размеров
		$prop = (float)$size[0]/(float)$size[1];
		
		// Шире
		if( $prop > (float)$w/(float)$h )
		{
			$W = $w; $H = (int)$w/$prop;
		}
		// Уже
		else
		{
			$W = (int)($h*$prop); $H = $h;
		}
		
		// Приемник
		$idest = imagecreatetruecolor( $W, $H );
		imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $W, $H, $size[0], $size[1]);
		imagejpeg($idest, $src, $config["preview_quality"] );
		imagedestroy($isrc);
		imagedestroy($idest);
	}
	
	function gallery_upload( $file )
	{
		// Не наш файл
		if( $file["inputname"] != "gallery" )
			return;
		
		global $config;
		
		$types = array( "png", "jpg", "jpeg", "gif" );
		if( !in_array($file["ext"], $types) )
		{
			delete_file( $file["id"] );
			// TODO: писать "неправильный формат файла"
			return;
		}
		
		copy( $file["path"], "files/{$file["id"]}_.jpg" );
		img_resize( "files/{$file["id"]}_.jpg", $config["preview_w"], $config["preview_h"] );
		
		// Защита от мульти-аплоада
		if( strpos($file["filename"], ".zip") === false )
		{
			// Обновление через аякс
			global $SCRIPT;
			$SCRIPT[] = 'window.top.window.update_gallery();';
			head();
		}
	}
	
?>
