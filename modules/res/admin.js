$(function()
{
	// Сортировка разделов
	$("#nav").sortable(
	{
		items: "li.last",
		opacity: 0.6,
		containment: "#nav",
		tolerance: "pointer",
		handle: "i.i-sort",
		placeholder: "placeholder",
		stop: function()
		{
			// Новый порядок
			var list = "";
			$("#nav li.last").each(function()
			{
				list += "&p[]="+$(this).prop("id");
			});
			
			// id раздела
			var id = 0;
			if( $("#nav li.open:last").length )
				id = $("#nav li.open:last").prop("id");
			
			
			// Сохранение сортировки
			$.ajax("?do=ajax&file=admin&page_sort=1&id="+id+list);
		}
	});

	
	// Сортировка файлов
	$("div.files").sortable(
	{
		items: "div.block",
		opacity: 0.6,
		tolerance: "pointer",
		placeholder: "placeholder",
		stop: function(event, ui)
		{
			// Новый порядок
			var list = "";
			$(ui.item).parent().find("div[id]").each(function()
			{
				list += "&p[]="+$(this).prop("id");
			});
			
			// Сохранение сортировки
			$.ajax("?do=ajax&file=admin&file_sort=1"+list);
		},
		start: function(event, ui)
		{
			ui.placeholder.css("width", ui.helper.width()+"px");
		}
	});
	
	// Файлы
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
	
	// confirm диалог
	$("body").on("click", "a.confirm, input.confirm", function()
	{
		if( confirm($(this).data("title")) )
			return true;
		else
			return false;
	});
	
	// Прокрутка к текущему пункту в навигации
	$("#nav").scrollTop( $("#nav li.sel").offset().top-72 );
});
