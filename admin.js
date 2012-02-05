$(function()
{
	// Сортировка файлов
	$("#gallery").sortable(
	{
		items: "div.block",
		opacity: 0.6,
		containment: "#gallery",
		tolerance: "pointer",
		placeholder: "placeholder",
		stop: function()
		{
			// Вставить кнопку, если нужно
			if( $("#gallery a.save").size() == 0 )
				$("#gallery").append("<a href='#' class='save'>Сохранить порядок</a>");
			
			// Новый порядок
			var list = "";
			$("#gallery div[id]").each(function()
			{
				list += "&p[]="+$(this).attr("id");
			});
			
			// Ссылка на страницу
			var loc = document.location.href;
			if( loc.indexOf("?") == -1 )
				loc += "?id=0"
			
			// Сохранение сортировки
			$("#gallery a.save").attr("href", loc+"&file_sort=1"+list);
		}
	});
	
	// Список файлов
	if( $("#gallery").length > 0 )
	{
		$("#gallery").load("?do=ajax&file=files&id="+$("#gallery").data("id"));
		
		$("iframe[name=upload]").load( function()
		{
			$("#gallery").load("?do=ajax&file=files&id="+$("#gallery").data("id"));
		});
	}
	
	// Вставка картинки из галереи
	$("#gallery a").live( "click", function()
	{
		$("textarea.editor").tinymce().execCommand("mceInsertContent", false, $(this).data("text"));
		return false;
	});
	
	// confirm диалог
	$("a.confirm, input.confirm").live("click", function()
	{
		if( confirm($(this).data("title")) )
			return true;
		else
			return false;
	});
});
