{include file='header' pageTitle='rp.acp.character.search'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.acp.character.search{/lang}</h1>
    </div>
</header>

{if $errorField == 'search'}
	<p class="error">{lang}rp.acp.character.search.error.noMatches{/lang}</p>
{/if}

{@$form->getHtml()}

{include file='footer'}