<h2>Настройки сайта</h2>
<form method='post'>
	<table>
		<col width='250'>
		<col>
		<tr>
			<td>Заголовок сайта:</td>
			<td><input type='text' name='title' value='{$CONFIG[title]}'></td>
		</tr>
		<tr>
			<td>Расширенные настройки:</td>
			<td><input type='checkbox' name='adv' {if $CONFIG[adv]}checked{/if}></td>
		</tr>
		<tr>
			<td>Включить загрузку файлов по ссылке:</td>
			<td><input type='checkbox' name='load_url' {if $CONFIG[load_url]}checked{/if}></td>
		</tr>
		/* Расширенные настройки */
		{if $CONFIG["adv"]}
			<tr>
				<td>Использовать mod_rewrite:</td>
				<td><input type='checkbox' name='rewrite' {if $CONFIG[rewrite]}checked{/if}></td>
			</tr>
			<tr>
				<td>Основной шаблон:</td>
				<td><input type='text' name='template' value='{$CONFIG[template]}'></td>
			</tr>
			<tr>
				<td>Корень сайта:</td>
				<td><input type='text' name='root' value='{$CONFIG[root]}'></td>
			</tr>
			<tr>
				<td>id главной страницы:</td>
				<td><input type='text' name='main' value='{$CONFIG[main]}' class='small'></td>
			</tr>
			<tr>
				<td>id страницы 404:</td>
				<td><input type='text' name='404_page' value='{$CONFIG["404_page"]}' class='small'> <small>(Оставьте 0, чтобы использовать стандартную)</small></td>
			</tr>
		{/if}
		<tr><td colspan='2'><input type='submit' class='btn save_right' value='Сохранить'></td></tr>
	</table>
</form>
