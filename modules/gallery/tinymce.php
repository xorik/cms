<?
	// Удаление дефолтной картинки
	global $hook;
	unset( $hook[gallery_show][array_search("default_gallery_show", $hook[gallery_show])] );
	
	hook_add( "gallery_show", "tinymce_gallery_show" );
	
	// При нажатии на картинку, она вставляется в редактор
	function tinymce_gallery_show( $id )
	{
		?>
			<a href='javascript:void(0);' onclick='tinyMCE.execCommand( "mceInsertContent", false, "<img src=\"files/<?= $id ?>.jpg\">");'>
				<img src='files/<?= $id ?>_.jpg' class='pic'>
			</a>
		<?
	}
	
?>
