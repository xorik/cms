<?
	hook( "upload", "gallery_upload" );
	
	function img_resize( $src, $w, $h )
	{
		global $CONFIG;
		
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
		imagejpeg($idest, $src, $CONFIG["preview_quality"] );
		imagedestroy($isrc);
		imagedestroy($idest);
	}
	
	function gallery_upload( $file )
	{
		global $CONFIG;
		
		$types = array( "png", "jpg", "jpeg", "gif" );
		
		// Ресайз для картинок
		if( in_array($file["ext"], $types) )
		{
			copy( $file["path"], "files/{$file["id"]}_.jpg" );
			img_resize( "files/{$file["id"]}_.jpg", $CONFIG["preview_w"], $CONFIG["preview_h"] );
		}
	}
	
?>
