{{
	Head::css( "modules/res/admin.css" );

	Noty::js();
	Head::js( "modules/res/jquery-ui.js" );
	if( Router::$path == "admin" )
		Head::script( "var id=". (int)$_GET["id"] .", admin_url = '". Router::$root ."admin?';" );
	Head::js( "modules/res/admin.js" );
}}
<body>
	<div id='top'>
		<a href='{$root}admin' class='logo'></a>
		<span>сайта <a href='{$root}'>{Config::get("title")}</a></span>
		<div>
			<a href='{$root}config'>Настройки</a> |
			<a href='{$root}admin?logout'>Выход</a>
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
					<a href='{$root}admin'>Разделы</a>
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
