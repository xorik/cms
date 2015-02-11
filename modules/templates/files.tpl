<form action='{Router::$root}ajax/files?id={$id}&gallery={$_GET["gallery"]}' method='post' target='upload-{$_GET["gallery"]}'>
	{if count($files) > 1}
		<small><br>Файлы сортируются мышкой: захватите и перетащите</small>
	{/if}
	
	<div {if Config::get("files", "scroll")}class='scroll'{/if}>
	{each $files as $f}
		<div id='{$f[id]}' class='block'>
			<input type='checkbox' name='{$f[id]}'>
			{Hook::run( "file_show", $f )}
		</div>
	{/each}
	</div>
	
	{if count($files) > 0}
		{Hook::run( "files_action" )}
	{/if}
</form>
