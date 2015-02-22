<form method='post' class='long fields'>

	<button class='save'>Сохранить</button>
	<h3>Редактирование страниц</h3>

	<div>
		<div class='descr'>Видимость новых страниц:</div>
		<div class='padding'>
			<label><input type='radio' name='hide_new' value='0' {if !Config::get("hide_new")}checked{/if}> Новые страницы доступны сразу</label><br>
			<label><input type='radio' name='hide_new' value='1' {if Config::get("hide_new")}checked{/if}> Новые страницы скрытые <small>(Необходимо вручную делать их видимыми)</small></label>

		</div>
	</div>


	<h3>Список файлов</h3>

	<div class='descr'>Макс. размер изображений:</div>
	<div>
		<input type='text' name='max_w' value='{$max_img[0]}' class='small'> X
		<input type='text' name='max_h' value='{$max_img[1]}' class='small'> px
	</div>

	<div>
		<div class='descr'>Порядок сортировки:</div>
		<div class='padding'>
			<label><input type='radio' name='order' value='asc' {if $order=="asc"}checked{/if}> Новые файлы в конец списка</label><br>
			<label><input type='radio' name='order' value='desc' {if $order=="desc"}checked{/if}> Новые файлы в начало списка</label>
		</div>
	</div>

	<div class='padding'>
		<div class='descr'>Прокрутка:</div>
		<div class='padding'>
			<label><input type='radio' name='scroll' value='1' {if $scroll}checked{/if}> Горизонтальная прокрутка</label><br>
			<label><input type='radio' name='scroll' value='0' {if !$scroll}checked{/if}> Показывать все файлы</label>
		</div>
	</div>

	<div class='padding'></div>

	{Editor::checkbox( "", "url", "1", "Загрузка изображений и файлов по ссылке", $url)}

</form>
