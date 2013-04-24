$(function()
{
	// Сохранять высоту textarea
	CKEDITOR.on('instanceReady', function( e )
	{
		var id = e.editor.id;
		$("#"+id+"_contents").css( "height", $("div."+id).prev().height()+"px");
	});
	
	// Подключить редактор
	CKEDITOR.replaceAll("rich");
	
	// Вставка картинки из галереи
	$("#gallery").on( "click", "a", function()
	{
		CKEDITOR.currentInstance.insertHtml($(this).data("text"));
		return false;
	});
	
	// Кнопка "сохранить"
	var save = false;
	$("input.save").click( function()
	{
		save = true;
	});
	
	// Сохранить перед выходом
	$(window).bind('beforeunload', function()
	{
		if( save )
			return;
		
		for (var i in CKEDITOR.instances)
			if( CKEDITOR.instances[i].checkDirty() )
				return "Уйти без сохранения?";
	});
})
