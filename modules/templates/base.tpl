<div id='crumb'><a href='{ROOT}admin'>Разделы</a> &gt; {Page::crumb()}</div>
<form method='post'>
	<button class='save'><i class='fa fa-check'></i> Сохранить</button>
		/* Не виртуальная страница */
		{if !Types::get()->virt}
			<a href='{Page::path($id)}' class='goto'><i class='fa fa-share'></i> Посмотреть страницу</a>
		{/if}
	<h2>{Page::title()}</h2>
	<div class='fields'>
		{Hook::run( "show", $id )}
	</div>
</form>
