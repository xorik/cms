<?
	hook( "content", "base_content" );
	
	function base_content()
	{
		global $id;
		
		$query = "SELECT text FROM page WHERE id=$id";
		$row = mysql_fetch_array( mysql_query($query) );
		echo $row["text"];
	}
?>
