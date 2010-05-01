<?
	hook_add( "init", "gallery_init" );
	
	// Загрузка галереи аяксом
	function gallery_init()
	{
		global $gid;
		
		// Если страница существует
		if( isset($gid) )
			hook_add( "content", "gallery_content", 30 );
		else
			return;
		
		global $id;
		global $SCRIPT;
		
		$SCRIPT[] = '
			function update_gallery()
			{
				$.ajax({url: "admin.php?do=gallery&id='.$id.'", cache: false, success: function(html)
				{
					$("#gallery").html(html);
				}});
			}
			$(document).ready(function()
			{
				update_gallery();
			});';
	}
	
	// Показать галерею
	function gallery_content()
	{
		global $id;
		?>
			<h3 id='gallery_toggle'>Галерея</h3>
			<div>
				<form action='admin.php?do=upload&id=<?= $id ?>' method='post' enctype='multipart/form-data' target='upload'>
					Загрузить: <input type='file' name='gallery'>
					<input type='submit' value='Загрузить'>
				</form>
				<div id='gallery'></div>
				<iframe name='upload' src='#' style='display:none'></iframe>
			</div>
		<?
	}
	
?>
