<?
	hook( "init", "files_init", 95 );
	
	// Загрузка галереи аяксом
	function files_init()
	{
		global $gid;
		
		// Если страница существует
		if( isset($gid) )
			hook( "content", "files_content", 30 );
		else
			return;
		
		global $id;
		global $SCRIPT;
		
		$SCRIPT[] = '
			function update_files()
			{
				$.ajax({url: "?do=ajax&file=files&id='.$id.'", cache: false, success: function(html)
				{
					$("#gallery").html(html);
				}});
			}
			$(document).ready(function()
			{
				update_files();
			});';
	}
	
	// Показать галерею
	function files_content()
	{
		global $id;
		?>
			<h3 id='gallery_toggle'>Изображения и файлы</h3>
			<div>
				<form action='?do=ajax&file=upload&id=<?= $id ?>' method='post' enctype='multipart/form-data' target='upload'>
					Загрузить: <input type='file' name='gallery'>
					<input type='submit' value='Загрузить'>
				</form>
				<div id='gallery'></div>
				<iframe name='upload' src='#' style='display:none'></iframe>
			</div>
		<?
	}
	
?>
