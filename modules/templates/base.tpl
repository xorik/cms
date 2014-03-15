<form method='post'>
	<input type='submit' value='Сохранить' class='save'>
		/* Не виртуальная страница */
		{if !$PAGE_TYPE[$TYPE]["virt"]}
			<a href='{path($id)}' class='goto'>Посмотреть страницу</a>
		{/if}
	<h2>{$ORIG_TITLE}</h2>
	<table class='base'>
		<col width='150'>
		<col>
		{run( "base_show", $id )}
	</table>
</form>
