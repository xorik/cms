$(function()
{
	// Сортировка разделов
	$("#nav").sortable(
	{
		items: "li.last",
		opacity: 0.6,
		containment: "#nav",
		tolerance: "pointer",
		handle: "div.sort",
		placeholder: "placeholder",
		stop: function()
		{
			// Новый порядок
			var list = "";
			$("#nav li.last").each(function()
			{
				list += "&p[]="+$(this).attr("id");
			});
			
			// id раздела
			var id = 0;
			if( $("#nav li.open:last").length )
				id = $("#nav li.open:last").attr("id");
			
			
			// Сохранение сортировки
			$.ajax("?do=ajax&file=admin&page_sort=1&id="+id+list);
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
			// Новый порядок
			var list = "";
			$("#gallery div[id]").each(function()
			{
				list += "&p[]="+$(this).attr("id");
			});
			
			// Сохранение сортировки
			$.ajax("?do=ajax&file=admin&file_sort=1"+list);
		},
		start: function(event, ui)
		{
			ui.placeholder.css("width", ui.helper.width()+"px");
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
		tinyMCE.activeEditor.execCommand("mceInsertContent", false, $(this).data("text"));
		return false;
	});
	
	// Выделить все файлы
	$("input.files_sel").live( "change", function()
	{
		$("#gallery input[type=checkbox]").attr("checked", Boolean($(this).attr('checked')));
	});
	
	// confirm диалог
	$("a.confirm, input.confirm").live("click", function()
	{
		if( confirm($(this).data("title")) )
			return true;
		else
			return false;
	});
	
	// Выделить заголовок, если == названию типа
	if( $("#content select[name=type]").val() == $("#content input[name=title]").val() )
	{
		$("#content input[name=title]").focus(function()
		{
			this.select();
		}).focus();
	}
	
	$(window).bind('beforeunload', function()
	{
		if( $("textarea.mce").tinymce && $("textarea.mce").tinymce().isDirty() )
			return "Уйти без сохранения?";
	});
});
