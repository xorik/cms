<?
	// Стили, скрипты
	$CSS[] = "modules/res/admin.css";
	$JS[] = "modules/res/jquery.js";
	$JS[] = "modules/res/jquery-ui.js";
	$JS[] = "modules/res/admin.js";
?>
<!doctype html>
<head>
	<? head() ?>
</head>
<body>
	<div id='top'>
		<a href='<?= $ADMIN_URL ?>' class='logo'></a> <span>сайта <?= $CONFIG["title"] ?></span>
		<div>
			<a href='<?= $CONFIG_URL ?>'>Настройки</a> |
			<a href='<?= $ADMIN_URL ?>logout=1'>Выход</a>
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
		</div>
	</div>
	<div id='bottom'>
		<span><? @include( "modules/res/version" ) ?></span>
		<div></div>
	</div>
</body>
