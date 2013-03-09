<?
	// Стили, скрипты
	$CSS[] = "res/admin.css";
	$JS[] = "res/jquery.js";
	$JS[] = "res/jquery-ui.js";
	$JS[] = "res/admin.js";
?>
<!doctype html>
<head>
	<? head() ?>
</head>
<body>
	<div id='top'>
		<a href='<?= $ADMIN_URL ?>' class='logo'><img src='res/img/adm.png'></a> <span>сайта <?= $CONFIG["title"] ?></span>
		<div>
			<a href='<?= $CONFIG_URL ?>'>Настройки</a> |
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
					echo "<a href='$ADMIN_URL'>Разделы</a>";
			?>
			</div>
			<? run( "content" ) ?>
			<div style='clear: left'></div>
		</div>
	</div>
	<div id='bottom'>
		<img src='res/img/logo.png'>
		<? @include( "res/version" ) ?>
	</div>
</body>
