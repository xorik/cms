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
?>
