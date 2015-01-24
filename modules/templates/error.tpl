<style>

</style>

<div>
	<h2>Ошибка сервера</h2>
	<p>При обработки страницы произошла ошибка</p>
	<p>ID ошибки: <b>{$id}</b></p>

	/* Admin and dev */
	{if Session::get("admin")}
		<h3>Дополнительная информация:</h3>
		<p>Сообщение: <b>{$error["msg"]}</b></p>
		<p>Файл: <b>{$error["file"]}:{$error["line"]}</b></p>
	{/if}

	{if Session::get("dev") && $error["trace"]}
		<h3>Trace:</h3>
		<table border='1'>
			{each $error["trace"] as $t}
				<tr>
					<td>{$t[0]}:{$t[1]}</td>
					<td>{$t[2]}</td>
					<td>{json($t[3])}</td>
				</tr>
			{/each}
		</table>
	{/if}
</div>
