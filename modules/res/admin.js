$(function()
{
	function gotoSub( li, upd )
	{
		// Он и открыт, выходим
		if( li.hasClass("open") && li.next().find("li:first").hasClass("last") )
			return;

		if( li.is("li") )
		{
			li.addClass("open");
			li.next().slideDown( upd ? 0 : 500 );
		}

		$("#nav li.last").removeClass("last");
		$("#nav hr:visible, #nav div.add:visible").slideUp( upd ? 0 : 500 );

		var div = li.size()==0 ? $("#nav") : li.next();

		// Закрытие открытых разделов
		div.find("li.open").removeClass("open");
		div.find("div.sub:visible").slideUp( upd ? 0 : 500 );
		div.find("> li").addClass("last");
		div.find("> hr, > div.add").slideDown( upd ? 0 : 500 );
	}


	function gotoPage( id, upd )
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
				gotoSub(li, upd);
			}
			// Подразделы открыты
			else
			{
				if(li.hasClass("sel"))
					gotoSub(li.parent().prev(), upd);
				else
					gotoSub(li, upd);
			}
		}
		else
			gotoSub(li.parent().prev(), upd);

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
	
	
	// Навигация и контент в админке
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


	// Добавление раздела
	$("#nav").on("click", "a.add, div.add a", function()
	{
		var title = prompt( "Название новой страницы:", $(this).data("type") );
		if( title != null)
		{
			var scroll = $("#nav").scrollTop();
			$.post("?do=ajax&file=admin&add=1&id="+$(this).data("gid"), {title: title}, function(data)
			{
				// Обновить левый блок
				$("#nav").addClass("load").load("?do=ajax&file=nav", function()
				{
					gotoPage( data.id, true );
					$("#nav").scrollTop(scroll);
					$(this).removeClass("load");
				});
			}, "json");
		}

		return false;
	});
	
	
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
