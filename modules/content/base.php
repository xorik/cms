<?
	hook( "content", "base_content" );
	
	function base_content()
	{
		global $id;
		
		if( !$id )
		{
			echo "<h3>Ошибка 404: Страница \"{$_SERVER["REQUEST_URI"]}\" не найдена!</h3>";
			return;
		}
		$query = "SELECT text FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		echo $row["text"];
	}
?>
