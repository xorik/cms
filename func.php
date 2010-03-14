<?
	// Загрузить модули с префиксом
	function load_modules( $mask )
	{
		$dh = opendir( "modules" );
		
		while ( ($file = readdir($dh)) !== false )
		{
			if( strpos( $file, $mask ) === 0 )
				require( "modules/" . $file );
		}
		closedir($dh);
	}
	
	// Создать хук
	function hook_add( $hookname, $func, $pos=50 )
	{
		global $hook;
		
		// Пытаемся встать в позицию pos
		while( @array_key_exists($pos, $hook[$hookname]) )
			$pos++;
		$hook[$hookname][$pos] = $func;
	}
	
	// Выполнить все функции из хука
	function hook_run( $hookname )
	{
		global $hook;
		@ksort( $hook[$hookname] );
		
		if( is_array($hook[$hookname])  )
			foreach( $hook[$hookname] AS $v )
				$v();
	}
	
	// Возвращает объект для конфига
	function config_item( $v )
	{
		if( is_string($v) )
			return "'$v'";
		elseif( is_numeric($v) )
			return $v;
		elseif( is_bool($v) )
			if( $v )
				return "true";
			else
				return "false";
		elseif( is_array($v) )
		{
			$r = "array(";
			foreach( $v AS $k=>$v )
				$r .= " " . config_item( $k ) . "=>" . config_item( $v ) . ",";
			$r .= " )";
			return $r;
		}
	}
	
	// Сохранить массив $config в файле config.php
	function config_write()
	{
		global $config;
		
		$f = fopen( "config.php", "w" );
		fwrite( $f, "<?\n" );
		foreach( $config AS $k => $v )
		{
			fwrite( $f, "\t\$config[" . config_item( $k ) . "] = " . config_item( $v ) . ";\n" );
		}
		fwrite( $f, "?>\n" );
		fclose( $f );
	}
	
	
	// Всё, что в <head>
	function head()
	{
		global $HEAD;
		global $CSS;
		global $JS;
		global $SCRIPT;
		
		if( $HEAD )
			foreach( $HEAD as $v )
				echo "\t$v\n";
		
		if( $CSS )
			foreach( $CSS as $v )
				echo "\t<link rel='stylesheet' type='text/css' href='$v'>\n";
		
		if( $JS )
			foreach( $JS as $v )
				echo "\t<script type='text/javascript' src='$v'></script>\n";
		
		if( $SCRIPT )
			foreach( $SCRIPT as $v )
				echo "\t<script type='text/javascript'>\n$v\n</script>\n";
	}
	
	
	// Перезагрузить страницу с очисткой post-данных
	function clear_post()
	{
		header( "Location: {$_SERVER["REQUEST_URI"]}" );
		die;
	}
?>
