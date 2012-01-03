$(function()
{
	// Сортировка разделов
	$("#sub tbody").sortable(
	{
		items: "tr[id]",
		opacity: 0.6,
		containment: "#sub",
		tolerance: "pointer",
		stop: function()
		{
			// Вставить кнопку, если нужно
			if( $("#sub a.save").size() == 0 )
				$("#sub tr:last td").append("<a href='#' class='save'>Сохранить порядок</a>");
			
			// Новый порядок
			var list = "";
			$("#sub tr:[id]").each(function()
			{
				list += "&p[]="+$(this).attr("id");
			});
			
			// Ссылка на страницу
			var loc = document.location.href;
			if( loc.indexOf("?") == -1 )
				loc += "?id=0"
			
			// Сохранение сортировки
			$("#sub a.save").attr("href", loc+"&page_sort=1"+list);
		}
	});
	
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
});
