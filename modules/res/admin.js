$(function()
{
	// Админка
	if( $("#nav >").size() == 0 )
	{
		// Навигация
		$("#nav").addClass("load").load("?do=ajax&file=nav", function()
		{
			$(this).removeClass("load");
			// Прокрутка к текущему пункту
			//$("#nav").scrollTop( $("#nav li.sel").offset().top-72 );
		});
	}
	
	
	// Файлы
	$("#content").on("ready", function()
	{
		$("div.files").each( function()
		{
			$(this).next().load( function()
			{
				var div = $(this).prev();
				div.load("?do=ajax&file=files&id="+div.data("id")+"&gallery="+div.data("gallery"), function()
				{
					// Выделить все файлы
					div.find("input.files_sel").click( function()
					{
						div.find("div :checkbox").prop("checked", $(this).is(":checked"));
					});
				});
			}).load();
		});
	});
	
	// confirm диалог
	$("body").on("click", "a.confirm, input.confirm", function()
	{
		if( confirm($(this).data("title")) )
			return true;
		else
			return false;
	});
});
