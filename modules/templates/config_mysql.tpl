<h3>Настройки MySQL</h3>

<form method='post' class='fields'>
	{Editor::input( "Host", "host", $host)}
	{Editor::input( "User", "user", $user)}
	{Editor::password( "Password", "pass", (bool)$pass)}
	{Editor::input( "Database", "db", $db)}

	<button class='btn save_right'>Сохранить</button>
</form>


{if $empty}
	<h3>Таблицы не созданы!</h3>
	<a href='?create'>Создать таблицы</a>
{/if}

