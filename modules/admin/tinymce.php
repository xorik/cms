<?
	hook( "init", "tinymce_init", 96 );
	
	function tinymce_init()
	{
		global $CONFIG;
		global $id;
		// Нужен ли редактор
		if( !isset( $id ) )
			return;
		
		// Подключение и настройка tinymce
		global $JS;
		global $SCRIPT;
		
		$JS[] = "modules/tiny_mce/tiny_mce.js";
		$SCRIPT[] = "tinyMCE.init({
			mode : 'textareas',
			editor_selector: 'mce',
			theme: 'advanced',
			plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',
			theme_advanced_buttons1 : 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,|,blockquote',
			theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor',
			theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,|,sub,sup,|,media,|,ltr,rtl,|,fullscreen',
			theme_advanced_toolbar_location : 'top',
			theme_advanced_toolbar_align : 'left',
			theme_advanced_statusbar_location : 'bottom',
			theme_advanced_resizing : true,
			convert_urls : false,
			language: 'ru',
			style_formats : [
				{$CONFIG["formats"]}
			],
			content_css: '/content.css',
		});";
	}
?>
