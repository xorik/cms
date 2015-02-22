<div id='nav_title'><a href='{ROOT}config'><i class='fa fa-wrench fa-lg'></i> Настройки</a></div>
{each $list as $url=>$title}
<li {if $url==$current}class='sel'{/if}><div class='indent'></div><div class='indent'></div><div class='indent'></div><a class='block' href='{ROOT}config/{$url}'>{$title}</a></li>
{/each}
