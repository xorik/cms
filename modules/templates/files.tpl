<h3>Изображения и файлы</h3>
<div>
	<form action='?do=ajax&file=upload&id={$id}' method='post' enctype='multipart/form-data' target='upload-gallery'>
		Загрузить: <input type='file' name='gallery[]' multiple='true'>
		{if $CONFIG["load_url"]}
			или по ссылке: <input type='text' name='url'>
		{/if}
		<input type='submit' class='btn' value='Загрузить'>
		<input type='hidden' name='gallery' value='gallery'>
		<small>(Максимум: {ini_get("upload_max_filesize")}b)</small>
	</form>
	<div class='files' data-id='{$id}' data-gallery='gallery'></div>
	<iframe name='upload-gallery' src='#' style='display:none'></iframe>
</div>
