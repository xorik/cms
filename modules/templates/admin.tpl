{{
	$CSS[] = "modules/res/admin.css";
	$JS[] = "modules/res/jquery.js";
	$JS[] = "modules/res/jquery-ui.js";
	$JS[] = "modules/res/admin.js";
}}
<!DOCTYPE html>
<head>
	{head()}
</head>
<body>
	<div id='top'>
		<a href='{$ADMIN_URL}' class='logo'></a>
		<span>сайта <a href='.'>{$CONFIG[title]}</a></span>
		<div>
			<a href='{$CONFIG_URL}'>Настройки</a> |
			<a href='{$ADMIN_URL}logout=1'>Выход</a>
		</div>
	</div>
	<div id='main'>
		<div id='nav'>
			{run( "nav" )}
		</div>
		<div id='content'>
			<div id='crumb'>
				/* Хлебные крошки или переход к разделам */
				{if $_GET["do"] == "admin"}
					{run( "crumb", "•" )}
				{else}
					<a href='{$ADMIN_URL}'>Разделы</a>
				{/if}
			</div>
			{run( "content" )}
		</div>
	</div>
	<div id='bottom'>
		<span>{file_get_contents( "modules/res/version" )}</span>
		<div></div>
	</div>
</body>
