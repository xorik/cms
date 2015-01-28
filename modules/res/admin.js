$(function()
{
	// Индикатор загрузки
	$.fn.addLoad = function()
	{
		$(this).append("<div class='load'></div>").find("> div.load").css({top: $(this).scrollTop()});
		return $(this);
	}
	$.fn.removeLoad = function()
	{
		$(this).find("> div.load").remove();
		return $(this);
	}


	// Загрузить контент
	function loadContent()
	{
		$("#content").addLoad().load("ajax/admin?base&id="+id, function()
		{
			$(this).trigger("ready").removeLoad();
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
		
		$("#nav").addLoad().load("ajax/nav", function()
		{
			gotoPage( true );
			
			if( goto_sel!==false && $("#nav li.sel").size() )
			{
				scroll = $("#nav li.sel").offset().top - 72;
			}
			
			// Прокрутка
			if( $("#nav li.sel").size() )
			{
				$("#nav").scrollTop( 0 );
				$("#nav").scrollTop( scroll );
			}
			
			// Сортировка разделов
			$("#nav").sortable(
			{
				items: "li.last",
				opacity: 0.6,
				containment: "#nav",
				tolerance: "pointer",
				handle: "i.i-sort",
				distance: 5,
				placeholder: "placeholder",
				stop: function(event, ui)
				{
					// Новый порядок
					var list = "";
					$("#nav li.last").each(function()
					{
						list += "&p[]="+$(this).find("a.block").data("id");
					});
					
					// id раздела
					var id = 0;
					if( $("#nav li.last:first").parent().prev().is("li") )
					{
						id = $("#nav li.last:first").parent().prev().find("a.block").data("id");
					}
					
					// Если встал между разделам и его подразделами
					if( ui.item.next().is("div.sub") )
						ui.item.insertAfter( ui.item.next() );
					
					// Поиск подразделов
					var sub = $("#nav div.sub[data-gid="+ui.item.find("a.block").data("id")+"]");
					if( sub.size() )
					{
						sub.insertAfter( ui.item );
					}
					
					// Сохранение сортировки
					$.ajax("ajax/admin?page_sort&id="+id+list);
				}
			});

			// id для сортировки
			$("#nav div.sub").each( function()
			{
				$(this).attr("data-gid", $(this).prev().find("a.block").data("id"));
			});
			
			$(this).removeLoad();
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
				"json/admin?save&id="+id,
				$(this).closest("form").serialize(),
				function(data)
				{
					ajaxNotify( data );
					if( !data.error )
					{
						loadNav( false );
					}
				},
				"json"
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
				$.post("json/admin?add&id="+$(this).data("gid"), {title: title}, function(data)
				{
					ajaxNotify( data );
					if( typeof data.id !== 'undefined' )
					{
						id = data.id;
						history.pushState({id: id}, "", admin_url+"id="+id);
						loadNav( false );
					}
				},
				"json");
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
					"json/admin?del&id="+id,
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
					div.load("ajax/files?id="+div.data("id")+"&gallery="+div.data("gallery"), function()
					{
						// Выделить все файлы
						div.find("input.files_sel").click( function()
						{
							div.find("div :checkbox").prop("checked", $(this).is(":checked"));
						});
					});
				}).load();
			});
			
			// Сортировка файлов
			$("div.files").sortable(
			{
				items: "div.block",
				opacity: 0.6,
				tolerance: "pointer",
				placeholder: "placeholder",
				distance: 10,
				stop: function(event, ui)
				{
					// Новый порядок
					var list = "";
					$(ui.item).parent().find("div[id]").each(function()
					{
						list += "&p[]="+$(this).prop("id");
					});
					
					// Сохранение сортировки
					$.ajax("ajax/admin?file_sort"+list);
				},
				start: function(event, ui)
				{
					ui.placeholder.css("width", ui.helper.width()+"px");
				}
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
	
	
	// Уведомления
	$.extend( $.noty.defaults, {timeout: 2000, type: "information"} );
	
	window.show_notify = function( notify )
	{
		for( var i in notify )
		{
			noty( notify[i] );
		}
	}
	
	$(document).ajaxError( function(event, jqXHR, ajaxSettings)
	{
		noty({
			type: "warning",
			text: "Произошла ошибка при обработке запроса: <b>"+
				ajaxSettings.url+"</b>: "+jqXHR.statusText,
			timeout: 5000}
		);
	});
	
	
	function ajaxNotify( data )
	{
		if( data.success )
			noty({type: "success", text: data.success, timeout: 2000});
		
		if( data.info )
			noty({type: "information", text: data.info, timeout: 2000});
		
		if( data.error )
			noty({type: "warning", text: data.error, timeout: 5000});
	}
});
