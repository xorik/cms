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
		<a href='<?= ADMIN ?>' class='logo'><img src='modules/img/adm.png'></a> <span>сайта <?= $CONFIG["title"] ?></span>
		<div>
			<a href='<?= CONFIG ?>'>Настройки</a> |
			<a href='?logout=1'>Выход</a>
		</div>
	</div>
	<div id='main'>
		<div id='nav'>
			<? run( "nav" ) ?>
		</div>
		<div id='content'>
			<div id='crumb'>
			<?
				// Хлебные крошки или переход к разделам
				if( $_GET["do"] == "admin" )
					run( "crumb", "•" );
				else
					echo "<a href='".ADMIN."'>Разделы</a>";
			?>
			</div>
			<? run( "content" ) ?>
			<div style='clear: left'></div>
		</div>
	</div>
	<div id='bottom'>
		<img src='modules/img/logo.png'>
		<? @include( "version" ) ?>
	</div>
</body>
