{include file='header'}

{if $errorField == 'search'}
	<p class="error">{lang}rp.acp.character.search.error.noMatches{/lang}</p>
{/if}

{@$form->getHtml()}

{include file='footer'}