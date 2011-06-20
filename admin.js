// Поменять местами элементы
jQuery.fn.swapWith = function(to) {
	return this.each(function() {
		var copy_to = $(to).clone(true);
		var copy_from = $(this).clone(true);
		$(to).replaceWith(copy_from);
		$(this).replaceWith(copy_to);
	});
};



$(document).ready(function()
{
	// Полосы в таблицах
	$("tr:odd").addClass("odd");
	
	// Кнопка сортировки
	function sort_reset()
	{
		// Удаление стрелки в верхнем элементе
		$("td.sort:first").find("a").remove();
		// Создание стрелки в остальных елементах
		$("td.sort:not(:first):not(:has(a))").each(function()
		{
			$(this).append("<a href='#'><img src='modules/img/up.png'></a>");
			$(this).find("a").click(function()
			{
				var tab = $(this).parent().parent().parent();
				
				// Перемена местами
				$(this).parent().parent().swapWith( $(this).parent().parent().prev() );
				sort_reset();
				
				var list = "";
				$("td.sort").each(function()
				{
					list += "&p[]="+$(this).parent().attr("id");
				});
				
				var loc = document.location.href;
				if( loc.indexOf("?") == -1 )
					loc += "?id=0"
				
				// Ссылка на сохранение
				tab.find("tr:last td").html( "<a href='"+loc+"&page_sort=1"+list+"'>Сохранить изменения</a>" );
				
				return false;
			});
		});
	}
	
	sort_reset();
});
