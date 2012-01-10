<?
	// Стили, скрипты
	$CSS[] = "admin.css";
	$JS[] = "jquery.js";
	$JS[] = "jquery-ui.js";
	$JS[] = "admin.js";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
	<? head() ?>
</head>
<body>
	<div id='top'>
		<div id='crumb'>
		<?
			// Хлебные крошки или переход к разделам
			if( $_GET["do"] == "admin" )
				run( "crumb" );
			else
				echo "<a href='".ADMIN."'>Разделы</a>";
		?>
		</div>
		<a href='./?do=config'><img src='modules/img/config.png'> Настройки</a>
		<a href='?logout=1'><img src='modules/img/logout.png'> Выйти</a>
	</div>
	<div id='content'>
		<? run( "content" ) ?>
	</div>
	<div id='bottom'>
		<? @include( "version" ) ?>
	</div>
</body>
