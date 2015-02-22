{{
	Head::css( "modules/res/admin.css" );
	Head::fontawesome();

	Head::jquery();
	Noty::js();
	Head::js( "modules/res/jquery-ui.js" );
	if( Router::$path == "admin" )
		Head::script( "var id=". (int)$_GET["id"] .", admin_url = '". ROOT ."admin?';" );
	Head::js( "modules/res/admin.js" );
}}
<body>
	<div id='top'>
		<a href='{ROOT}admin' class='logo'></a>
		<span>сайта <a href='{ROOT}'>{Config::get("title")}</a></span>
		<div>
			<a href='{ROOT}config'><i class='fa fa-wrench'></i> Настройки</a> |
			<a href='{ROOT}admin?logout'><i class='fa fa-sign-out'></i> Выход</a>
		</div>
	</div>
	<div id='main'>
		<div id='nav'>
			{if $content=="config"}
				{Hook::run( "nav" )}
			{/if}
		</div>
		<div id='content'>
			{if $content=="config"}
				<div id='crumb'>
					<a href='{ROOT}admin'>Разделы</a>
				</div>
				{Hook::run( "content" )}
			{/if}
		</div>
	</div>
	<div id='bottom'>
		<span>{file_get_contents( "modules/res/version" )}</span>
		<div></div>
	</div>
{Head::scripts()}
</body>
