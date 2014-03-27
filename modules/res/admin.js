$(function()
{
	function gotoSub( li )
	{
		// Он и открыт, выходим
		if( li.hasClass("open") && li.next().find("li:first").hasClass("last") )
			return;

		$("#nav li.last").removeClass("last");
		$("#nav hr:visible, #nav div.add:visible").slideUp(500);

		var div = li.size()==0 ? $("#nav") : li.next();

		// Закрытие открытых разделов
		div.find("li.open").removeClass("open");
		div.find("div.sub:visible").slideUp(500);
		div.find("> li").addClass("last");
		div.find("> hr, > div.add").slideDown(500);
	}


	function gotoPage( id )
	{
		// Поиск в левом блоке
		var li = $("#nav a.block[data-id="+id+"]").parent();
		li.parents("div.sub:hidden").show();

		// Подразделы
		if( li.next().is("div") )
		{
			// Подразделы закрыты
			if( ! li.hasClass("open") )
			{
				gotoSub(li);
				li.addClass("open");
				li.next().slideDown(500);
			}
			// Подразделы открыты
			else
			{
				if(li.hasClass("sel"))
					gotoSub(li.parent().prev());
				else
					gotoSub(li);
			}
		}
		else
			gotoSub(li.parent().prev());

		// sel и контент
		if( !li.hasClass("sel") )
		{
			$("#nav li.sel").removeClass("sel");
			li.addClass("sel");

			// Загрузить контент
			$("#content").addClass("load").load("?do=ajax&file=admin&base=1&id="+id, function()
			{
				$(this).trigger("ready").removeClass("load");
			});
		}
	}
	
	
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
		
		$("#nav, #content").on("click", "a[data-id]", function()
		{
			gotoPage( $(this).data("id") );
			return false;
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
