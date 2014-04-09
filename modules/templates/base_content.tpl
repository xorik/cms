{if $DATA == "title"}
	<tr>
		<td>Заголовок:</td>
		<td><input type='text' name='title' value='{$BASE[title]}'></td>
	</tr>
{elseif $DATA == "text"}
	<tr>
		<td colspan='2'>
			Текст:<br>
			<textarea name='text' cols='80' rows='20' class='rich editor'>{htmlspecialchars($BASE[text])}</textarea>
		</td>
	</tr>
{elseif $DATA == "descr"}
	<tr>
		<td></td>
		<td class='descr'>{$BASE[descr]}</td>
	</tr>
{elseif $DATA == "hide"}
	<tr>
		<td colspan='2'>
			<label>
				<input type='radio' name='hide' value='0' {if !$BASE[hide]}checked{/if}> Страница видна всем
				(<div class='round show'><i class='i-'></i></div>)
			</label>
			<label>
				<input type='radio' name='hide' value='1' {if $BASE[hide]}checked{/if}> Скрывать страницу в меню
				(<div class='round hide'><i class='i-'></i></div>)
			</label>
		</td>
	</tr>
{elseif $DATA == "path"}
	<tr>
		<td>Путь:</td> <td><input type='text' name='path' value='{$BASE[path]}'></td>
	</tr>
{elseif $DATA == "type"}
	{if $PAGE_TYPE[$TYPE]["notype"] || count($BASE['types'])==1}
		<input type='hidden' name='type' value='{$TYPE}'>
	{else}
		<tr>
			<td>Тип:</td>
			<td><select name='type'>
				{each $BASE[types] as $v}
					<option {if $v==$TYPE}selected{/if}>{$v}</option>
				{/each}
			</select></td>
		</tr>
	{/if}
{/if}
