<div id='crumb'><a href='{$root}admin'>Разделы</a> &gt; {Page::crumb()}</div>
<form method='post'>
	<input type='submit' value='Сохранить' class='save'>
		/* Не виртуальная страница */
		{if !Types::get()->virt}
			<a href='{Page::path($id)}' class='goto'>Посмотреть страницу</a>
		{/if}
	<h2>{Page::title()}</h2>
	<div class='fields'>
		{Hook::run( "show", $id )}
	</div>
</form>
