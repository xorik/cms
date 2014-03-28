$(function()
{
	// Загрузить контент
	function loadContent()
	{
		$("#content").addClass("load").load("?do=ajax&file=admin&base=1&id="+id, function()
		{
			$(this).trigger("ready").removeClass("load");
		});
	}
	
	
	// Загрузить навигациюs
	function loadNav( goto_sel )
	{
		var scroll;
		if( goto_sel === false )
		{
			scroll = $("#nav").scrollTop();
		}
		
		$("#nav").addClass("load").load("?do=ajax&file=nav", function()
		{
			gotoPage( true );
			
			if( goto_sel!==false && $("#nav li.sel").size() )
			{
				scroll = $("#nav li.sel").offset().top - 72;
			}
			
			// Прокрутка к текущему пункту
			if( $("#nav li.sel").size() )
			{
				$("#nav").scrollTop( 0 );
				$("#nav").scrollTop( scroll );
			}
			$(this).removeClass("load");
		});
	}
	
	
	// Открыть подраздел
	function gotoSub( li, fast )
	{
		var time = fast ? 0 : 500;
		// Он и открыт, выходим
		if( li.hasClass("open") && li.next().find("li:first").hasClass("last") )
		{
			return;
		}

		if( li.is("li") )
		{
			li.addClass("open");
			li.next().slideDown(time);
		}

		$("#nav li.last").removeClass("last");
		$("#nav hr:visible, #nav div.add:visible").slideUp(time);

		var div = li.size()==0 ? $("#nav") : li.next();

		// Закрытие открытых разделов
		div.find("li.open").removeClass("open");
		div.find("div.sub:visible").slideUp(time);
		div.find("> li").addClass("last");
		div.find("> hr, > div.add").slideDown(time);
	}


	function gotoPage( fast )
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
				gotoSub(li, fast);
			}
			// Подразделы открыты
			else
			{
				if(li.hasClass("sel"))
				{
					gotoSub(li.parent().prev(), fast);
				}
				else
				{
					gotoSub(li, fast);
				}
			}
		}
		else
			gotoSub(li.parent().prev(), fast);

		// sel и контент
		if( !li.hasClass("sel") )
		{
			$("#nav li.sel").removeClass("sel");
			li.addClass("sel");
			loadContent();
		}
	}
	
	
	// Можно ли перейти на другую страницу или надо сохранить
	function tryUnload()
	{
		var unload = $("#content").triggerHandler("unload");
		if( typeof unload === "undefined" )
			return false;
		
		if( confirm(unload) )
			return false;
		else
			return true;
	}
	
	
	// Навигация и контент в админке
	if( $("#nav >").size() == 0 )
	{
		history.replaceState({id: id}, "", admin_url+"id="+id);
		
		// Левый блок
		loadNav();
		
		// Навигация
		$("#nav, #content").on("click", "a[data-id]", function()
		{
			if( tryUnload() )
				return false;
			
			id = $(this).data("id");
			history.pushState({id: id}, "", admin_url+"id="+id);
			gotoPage();
			return false;
		});
		
		// Назад в браузере
		$(window).bind("popstate", function(event)
		{
			if( tryUnload() )
				return;
			
			id = event.originalEvent.state.id;
			gotoPage();
			// Прокрутка к текущему пункту
			if( $("#nav li.sel").size() )
			{
				$("#nav").scrollTop( 0 );
				$("#nav").scrollTop( $("#nav li.sel").offset().top-72 );
			}
		});
		
		
		// Сохранение
		$("#content").on( "click", "input.save", function( e )
		{
			e.preventDefault();
			$(this).trigger("submit");
			$.post(
				"?do=ajax&file=admin&save=1&id="+id,
				$(this).closest("form").serialize(),
				function(data)
				{
					loadNav( false );
				}
			);
		});
		
		// Добавление раздела
		$("#nav").on("click", "a.add, div.add a", function()
		{
			if( tryUnload() )
				return false;
			
			var title = prompt( "Название новой страницы:", $(this).data("type") );
			if( title != null)
			{
				$.post("?do=ajax&file=admin&add=1&id="+$(this).data("gid"), {title: title}, function(data)
				{
					id = data.id;
					history.pushState({id: id}, "", admin_url+"id="+id);
					loadNav( false );
				}, "json");
			}
			
			return false;
		});
		
		
		function removeAnimation( e )
		{
			e.animate({left: "-330px"}, 300).slideUp(500, function()
			{
				$(this).remove();
			})
		}
		// Удаление раздела
		$("#nav").on("click", "a.del", function()
		{
			if( confirm($(this).data("title")) )
			{
				// TODO: error detect
				$.post(
					"?do=ajax&file=admin&del=1&id="+id,
					{del: $(this).closest("li").find("a.block").data("id")},
					function(data)
					{
						if( typeof data.id !== 'undefined' )
						{
							id = data.id;
							history.pushState({id: id}, "", admin_url+"id="+id);
							gotoPage();
						}
					},
					"json"
				);
				removeAnimation( $(this).closest("li") );
				removeAnimation( $(this).closest("li").next().filter("div.sub") );
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
	}
	
	
	// confirm диалог
	$("body").on("click", "a.confirm, input.confirm", function()
	{
		if( confirm($(this).data("title")) )
			return true;
		else
			return false;
	});
});
