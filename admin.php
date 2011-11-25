<?
	// Заголовок, стили, скрипты
	$HEAD[] = "<title>{$CONFIG["title"]}</title>";
	$CSS[] = "admin.css";
	$JS[] = "jquery.js";
	$JS[] = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js";
	$JS[] = "admin.js";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
	<? head() ?>
</head>
<body>
	<div id='top'>
		<? run( "menu" ) ?>
		<a href='?logout=1'><img src='modules/img/logout.png'> Выйти</a>
	</div>
	<div id='content'>
		<? run( "content" ) ?>
	</div>
	<div id='bottom'>
		<? @include( "version" ) ?>
	</div>
</body>
