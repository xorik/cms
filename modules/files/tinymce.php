<?
	// Удаление дефолтной картинки
	unhook( "files_show", "default_files_show" );
	
	hook( "files_show", "tinymce_files_show" );
	
	// При нажатии на картинку, она вставляется в редактор
	function tinymce_files_show( $f )
	{
		global $CONFIG;
		
		if( $f["type"]=="png" || $f["type"]=="jpg" || $f["type"]=="jpeg" || $f["type"]=="gif" )
		{
			$text = "<img src='files/{$f["id"]}.{$f["type"]}'>";
			$html = "<img src='files/{$f["id"]}_.jpg' class='pic'>";
		}
		else
		{
			if( $CONFIG["rewrite"] )
				$text = "<a href='file/{$f["id"]}'>{$f["filename"]}</a>";
			else
				$text = "<a href='?do=ajax&file=getfile&fid={$f["id"]}'>{$f["filename"]}</a>";
			
			$html = "<img src='res/img/admin/file.png'> {$f["filename"]}";
		}
		
		echo "<a href='#' data-text=\"$text\">$html</a>";
	}
?>
