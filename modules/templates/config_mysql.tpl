<h3>Настройки MySQL</h3>
<form method='post'>
	<table>
		<col width='100'>
		<col>
		
		<tr>
			<td>host:</td>
			<td><input type='text' name='db_host' value='{$CONFIG[db_host]}'></td>
		</tr>
		<tr>
			<td>user:</td>
			<td><input type='text' name='db_user' value='{$CONFIG[db_user]}'></td>
		</tr>
		<tr>
			<td>pass:</td>
			<td><input type='password' name='db_pass'></td>
		</tr>
		<tr>
			<td>db:</td>
			<td><input type='text' name='db_db' value='{$CONFIG[db_db]}'></td>
		</tr>
		<tr>
			<td colspan='2'><input type='submit' class='btn save_right' value='Сохранить'></td>
		</tr>
	</table>
</form>
