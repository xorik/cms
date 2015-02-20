<h2>Настройки сайта</h2>

<form method='post' enctype='multipart/form-data' class='long fields'>
	{Editor::input( "Заголовок сайта", "title", Config::get("title") )}
	<div>
		<div class='descr'>Логотип формы входа:</div>
		<div class='padding'>
			{if $logo}
				<img src='{ROOT}{$logo}' style='vertical-align: bottom'>
				<label>
					<input type='checkbox' name='rm_logo' value='1'>
					Удалить
				</label>
			{else}
				<i>Не выбран</i>
			{/if}
			<br>
			<input type='file' name='logo'>
		</div>
	</div>
	<button class='btn save_right'>Сохранить</button>
</form>
