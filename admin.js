$(function()
{
	// Сортировка разделов
	$("#sub tbody").sortable(
	{
		items: "tr[id]",
		stop: function()
		{
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
			$("#sub tr:last td").html( "<a href='"+loc+"&page_sort=1"+list+"'>Сохранить изменения</a>" );
		}
	});
	
	// Сортировка файлов
	$("#gallery").sortable(
	{
		items: "div.block",
		opacity: 0.8,
		placeholder: "placeholder",
		stop: function()
		{
			// Вставить кнопку, если нужно
			if( $("#gallery a.save").size() == 0 )
				$("#gallery").append("<a href='#' class='save'>Сохранить изменения</a>");
			
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
});
