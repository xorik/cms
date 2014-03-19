<form action='?do=ajax&file=files&id={$id}&gallery={$_GET[gallery]}' method='post' target='upload-{$_GET[gallery]}'>
	{if count($files) > 1}
		<small><br>Файлы сортируются мышкой: захватите и перетащите</small>
	{/if}
	
	<div>
	{each $files as $file}
		<div id='{$file[id]}' class='block'>
			<input type='checkbox' name='{$file[id]}'>
			{{run( "files_show", array("id"=>$file["id"], "type"=>$file["type"], "filename"=>$file["filename"], "gallery"=>$file["gallery"]) )}}
		</div>
	{/each}
	</div>
	
	{if count($files) > 1}
		{run( "files_action" )}
	{/if}
</form>
