<?
	// Удаление дефолтной картинки
	unhook( "files_show", "default_files_show" );
	
	hook( "files_show", "tinymce_files_show" );
	
	// При нажатии на картинку, она вставляется в редактор
	function tinymce_files_show( $f )
	{
		if( $f["type"]=="png" || $f["type"]=="jpg" || $f["type"]=="jpeg" || $f["type"]=="gif" ) :
		?>
			<a href='#' data-text='<img src="files/<?= $f["id"] ?>.<?= $f["type"] ?>">'>
				<img src='files/<?= $f["id"] ?>_.jpg' class='pic'>
			</a>
		<?
		else :
		?>
			<a href='#' data-text='<a href="files/<?= $f["id"] ?>.<?= $f["type"] ?>"><?= $f["filename"] ?></a>'>
				<img src='modules/img/file.png'> <?= $f["filename"] ?>
			</a>
		<?
		endif;
	}
	
?>
