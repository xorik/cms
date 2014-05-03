<?php
	hook( "nav", "config_nav", 10 );
	
	global $MENU;
	$MENU = array( ""=>"Базовые настройки" );
	
	if( $CONFIG["adv"] )
		$MENU["mysql"] = "MySQL";
	
	function config_nav()
	{
		global $MENU, $CONFIG_URL;
		
		echo "<div id='nav_title'><a href='{$CONFIG_URL}'>Настройки</a></div>\n";
		
		foreach( $MENU as $k=>$v )
		{
			$sel = $_GET["edit"]==$k ? "class='sel'" : "";
			$link = $k ? "{$CONFIG_URL}edit=$k" : $CONFIG_URL;
			echo "<li $sel><div class='indent'></div><div class='indent'></div><div class='indent'></div><a class='block' href='$link'>$v</a></li>";
		}
	}
