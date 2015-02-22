{{
	Head::css( "modules/res/login.css" );
	Head::fontawesome();
}}
<body>
	<a href='{ROOT}' id='admin_logo' {if is_file($file="files/logo.png")}style='background-image:url({ROOT}{$file})'{/if}></a>
	<form method='post' id='admin_login'>
		<b>Вход в админку сайта</b><br>
		<a href='{ROOT}'>{Config::get("title")}</a><br>
		
		<hr>
		
		<input type='text' name='admin_login' value='{$_POST[admin_login]}' placeholder=' Логин' class='fa' autofocus>
		<input type='password' name='admin_pass' placeholder=' Пароль' class='fa'>
		<button><i class='fa fa-sign-in fa-lg'></i> Вход</button>
		{if !empty($_POST)}
			<p>Неверный логин или пароль!</p>
		{/if}
	</form>
</body>
