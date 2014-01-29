<?
	hook( "init", "files_init", 95 );
	
	// Загрузка галереи аяксом
	function files_init()
	{
		global $id;
		global $TYPE;
		global $PAGE_TYPE;
		
		// Если страница существует
		if( $id && !$PAGE_TYPE[$TYPE]["nofiles"] )
			hook( "content", "files_content", 30 );
		else
			return;
	}
	
	// Показать галерею
	function files_content()
	{
		global $id;
		global $CONFIG;
		
		?>
			<h3>Изображения и файлы</h3>
			<div>
				<form action='?do=ajax&file=upload&id=<?= $id ?>' method='post' enctype='multipart/form-data' target='upload-gallery'>
					Загрузить: <input type='file' name='gallery[]' multiple='true'>
					<? if( $CONFIG["load_url"] ): ?>
						или по ссылке: <input type='text' name='url'>
					<? endif ?>
					<input type='submit' value='Загрузить'>
					<input type='hidden' name='gallery' value='gallery'>
					<small>(Максимум: <?= ini_get("upload_max_filesize") ?>b)</small>
				</form>
				<div class='files' data-id='<?= $id ?>' data-gallery='gallery'></div>
				<iframe name='upload-gallery' src='#' style='display:none'></iframe>
			</div>
		<?
	}
	
?>
