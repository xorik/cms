<?
	hook( "init", "ckeditor_init" );
	
	function ckeditor_init()
	{
		global $JS;
		global $SCRIPT;
		global $CONFIG;
		
		$JS[] = "res/jquery.js";
		$JS[] = "modules/etc/ckeditor/ckeditor.js";
		$JS[] = "res/ckeditor.js";
		
		$SCRIPT[] = "$(function()
			{
				CKEDITOR.stylesSet.add( 'default', [{$CONFIG["formats"]}] );
			});";
	}
?>
