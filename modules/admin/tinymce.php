<?
	hook( "init", "tinymce_init", 96 );
	
	function tinymce_init()
	{
		global $CONFIG;
		global $id;
		global $TYPE;
		global $PAGE_TYPE;
		// Нужен ли редактор
		if( !$id || $PAGE_TYPE[$TYPE]["notext"] )
			return;
		
		// Подключение и настройка tinymce
		global $JS;
		global $SCRIPT;
		
		$JS[] = "jquery.js";
		$JS[] = "modules/tiny_mce/jquery.tinymce.js";
		$SCRIPT[] = "$(function()
		{
			// workaround для ширины
			$('textarea.editor.mce').css('width', $('#content').width());
			$('textarea.mce').tinymce(
			{
			script_url : 'modules/tiny_mce/tiny_mce.js',
			theme: 'advanced',
			plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',
			theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,|,blockquote',
			theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor',
			theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,|,sub,sup,|,media,|,ltr,rtl,|,fullscreen',
			theme_advanced_toolbar_location : 'top',
			theme_advanced_toolbar_align : 'left',
			theme_advanced_statusbar_location : 'bottom',
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false,
			convert_urls : false,
			language: 'ru',
			style_formats : [
				{$CONFIG["formats"]}
			],
			content_css: 'content.css',
			extended_valid_elements : 'iframe[name|src|framespacing|border|frameborder|scrolling|title|height|width]',
			});
		});";
	}
?>
