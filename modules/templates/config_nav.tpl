{each $groups as $g=>$v}
	{if $list[$g]}
		<h3>{if $v[1]}<i class='fa fa-{$v[1]} fa-fw fa-lg'></i> {/if}{$v[0]}</h3>

	{each $list[$g] as $url=>$l}
		<li {if $url==$current}class='sel'{/if}>
		<i class='config {if $l[1]}fa fa-{$l[1]} fa-fw fa-lg{else}hidden{/if}'></i>
		<a class='block' href='{ROOT}config/{$url}'>{$l[0]}</a></li>
	{/each}
	{/if}
{/each}
