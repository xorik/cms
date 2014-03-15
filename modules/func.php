<?
	// Загрузить модули из каталога
	function load_modules( $mask )
	{
		foreach( glob("modules/$mask/*.php") as $file )
			@include( $file );
		foreach( glob("extra/*/$mask/*.php") as $file )
			@include( $file );
		foreach( glob("extra/*/$mask.php") as $file )
			@include( $file );
	}
	
	// Создать хук
	function hook( $hookname, $func, $pos=50 )
	{
		global $HOOK;
		
		// Пытаемся встать в позицию pos
		while( @array_key_exists($pos, $HOOK[$hookname]) )
			$pos++;
		$HOOK[$hookname][$pos] = $func;
	}
	
	// Удалить хук
	function unhook( $hookname, $func )
	{
		global $HOOK;
		
		// all удаляет все хуки
		if( $func == "all" )
			unset( $HOOK[$hookname] );
		// Иначе выбранную функцию
		else
			unset( $HOOK[$hookname][array_search($func, $HOOK[$hookname])] );
	}
	
	// Выполнить все функции из хука
	function run( $hookname, $arg = 0 )
	{
		global $HOOK;
		
		if( is_array($HOOK[$hookname])  )
		{
			// Отсортировать по приотирету
			ksort( $HOOK[$hookname] );
			foreach( $HOOK[$hookname] AS &$v )
				$v( $arg );
		}
	}
	
	// Возвращает объект для конфига
	function config_item( $v )
	{
		if( is_string($v) )
			return "'". str_replace("'", "\\'", $v) ."'";
		elseif( is_numeric($v) )
			return $v;
		elseif( is_bool($v) )
			if( $v )
				return "true";
			else
				return "false";
		elseif( is_array($v) )
		{
			$a = array();
			foreach( $v AS $k=>$v )
				$a[] = config_item( $k ) . "=>" . config_item( $v );
			return "array( ". implode(", ", $a) ." )";
		}
	}
	
	// Сохранить массив $CONFIG в файле config.php
	function config_write()
	{
		global $CONFIG;
		
		$f = fopen( "config.php", "w" );
		fwrite( $f, "<?\n" );
		foreach( $CONFIG AS $k => $v )
		{
			fwrite( $f, "\t\$CONFIG[" . config_item( $k ) . "] = " . config_item( $v ) . ";\n" );
		}
		fwrite( $f, "?>\n" );
		fclose( $f );
	}
	
	
	// Всё, что в <head>
	function head()
	{
		global $CONFIG;
		global $HEAD;
		global $CSS;
		global $JS;
		global $SCRIPT;
		
		echo "<meta charset='utf-8'>\n";
		
		if( $HEAD )
			foreach( $HEAD as $v )
				echo "\t$v\n";
		
		if( $CSS )
		{
			$CSS = array_unique( $CSS );
			foreach( $CSS as $v )
			{
				if( strpos($v, "http://")!==0 && strpos($v, "https://")!==0 && strpos($v, "//")!==0 )
					$v = $CONFIG["root"] . $v;
				echo "\t<link rel='stylesheet' href='$v'>\n";
			}
		}
		
		if( $JS )
		{
			$JS = array_unique( $JS );
			foreach( $JS as $v )
			{
				if( strpos($v, "http://")!==0 && strpos($v, "https://")!==0 && strpos($v, "//")!==0 )
					$v = $CONFIG["root"] . $v;
				echo "\t<script src='$v'></script>\n";
			}
		}
		
		if( $SCRIPT )
			foreach( $SCRIPT as $v )
				echo "\t<script>\n$v\n</script>\n";
	}
	
	
	// Перезагрузить страницу с очисткой post-данных
	function clear_post()
	{
		header( "Location: {$_SERVER["REQUEST_URI"]}" );
		die;
	}
	
	
	// Прочитать свойство
	function get_prop( $id, $field )
	{
		$query = "SELECT value FROM prop WHERE id=$id AND field='$field'";
		$row = mysql_fetch_array( mysql_query($query) );
		return $row["value"];
	}
	
	// Установить свойство
	function set_prop( $id, $field, $value )
	{
		$query = "UPDATE prop SET value='$value' WHERE id=$id AND field='$field'";
		mysql_query( $query );
		
		// Такого свойства еще нет
		if( mysql_affected_rows() < 1 )
		{
			// Добавить его
			$query = "INSERT INTO prop (id, field, value) VALUES ($id, '$field', '$value')";
			mysql_query( $query );
		}
	}
	
	
	// Удалить загруженный файл
	function delete_file( $id )
	{
		// Ищем тип
		$query = "SELECT type FROM file WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		
		// Удаляем из ФС
		unlink( "files/$id.{$row["type"]}" );
		
		// Удаляем вспомогательные файлы
		foreach( glob("files/{$id}_*", GLOB_NOSORT) as $file )
			unlink( $file );
		
		// И из БД
		$query = "DELETE FROM file WHERE id=$id";
		mysql_query( $query );
	}
	
	
	function path( $id )
	{
		global $CONFIG;
		
		if( $id == $CONFIG["main"] )
			return ".";
		$p = get_prop( $id, "path" );
		if( strlen($p) )
			return $p;
		else
			return "./?id=$id";
	}
	
	
	// Вывести текст раздела
	function get_text( $id )
	{
		$query = "SELECT text FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		return $row["text"];
	}
?>
