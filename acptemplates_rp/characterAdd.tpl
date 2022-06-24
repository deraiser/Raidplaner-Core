{include file='header' pageTitle='rp.character.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.character.{$action}{/lang}</h1>
        {if $action == 'edit'}
            <p class="contentHeaderDescription">{$formObject->getTitle()}</p>
        {/if}
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='CharacterList' application='rp'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}rp.character.list{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}