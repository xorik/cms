$(function()
{
	// Переход на страницу
	function gotoPage( id )
	{
		// Навигация
		$("#nav").addClass("load").load("?do=ajax&file=nav&id="+id, function()
		{
			$(this).removeClass("load");
			// Прокрутка к текущему пункту
			//$("#nav").scrollTop( $("#nav li.sel").offset().top-72 );
		});
	}
	
	
	function gotoSub( id )
	{
		// Он и открыт, выходим
		if( $("#"+id).hasClass("open") && $("#"+id).next().find("li:first").hasClass("last") )
			return;
		
		$("#nav li.last").removeClass("last");
		$("#nav hr:visible, #nav div.add:visible").slideUp(500);
		
		if( id !== undefined )
			var div = $("#"+id).next();
		else
			var div = $("#nav");
		
		// Закрытие открытых разделов
		div.find("li.open").removeClass("open");
		div.find("div.sub:visible").slideUp(500);
		div.find("> li").addClass("last");
		div.find("> hr, > div.add").slideDown(500);
	}
	
	
	gotoPage( 0 );
	
	// Навигация
	$("#nav").on("click", "li", function()
	{
		// Подразделы
		if( $(this).next().is("div") )
		{
			// Подразделы закрыты
			if( ! $(this).hasClass("open") )
			{
				gotoSub($(this).prop("id"));
				$(this).addClass("open");
				$(this).next().slideDown(500);
			}
			// Подразделы открыты
			else
			{
				if($(this).hasClass("sel"))
					gotoSub($(this).parent().prev().prop("id"));
				else
					gotoSub($(this).prop("id"));
			}
		}
		else
			gotoSub($(this).parent().prev().prop("id"));
		
		// Выделение
		$("#nav li.sel").removeClass("sel");
		$(this).addClass("sel");
		
		return false;
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
});
