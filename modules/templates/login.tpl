{{
	Head::css( "modules/res/login.css" );
}}
<body>
	<a href='{$root}' id='admin_logo'></a>
	<form method='post' id='admin_login'>
		<b>Вход в админку сайта</b><br>
		<a href='{$root}'>{Config::get("title")}</a><br>
		
		<hr>
		
		<input type='text' name='admin_login' value='{$_POST[admin_login]}' placeholder='Логин' autofocus>
		<input type='password' name='admin_pass' placeholder='Пароль'>
		<button>Вход</button>
		{if !empty($_POST)}
			<p>Неверный логин или пароль!</p>
		{/if}
	</form>
</body>
