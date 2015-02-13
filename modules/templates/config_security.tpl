<h3>Безопасность</h3>

<p><i>Для смены логина или пароля необходимо ввести старый пароль</i></p>

<form method='post' class='long fields'>
	{Editor::input( "Логин", "login", $_POST["login"]?$_POST["login"]:$login, 0, "admin" )}
	{Editor::password( "Текущий пароль", "oldpass", 1 )}
	{Editor::password( "Новый пароль", "pass1" )}
	{Editor::password( "Ещё раз", "pass2" )}
	<div class='padding'></div>
	{Editor::select("Таймаут сессии администратора", "timeout", $timeout_list, $timeout, 10, " <small>(Максимальное время бездействия)</small>")}

	<button class='btn save_right'>Сохранить</button>
</form>

