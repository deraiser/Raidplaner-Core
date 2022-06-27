{capture assign='contentHeader'}
    <header class="contentHeader">
        <div class="contentHeaderTitle">
            <h1 class="contentTitle">{lang}rp.character.{$action}{/lang}</h1>
            {if $action == 'edit'}
                <p class="contentHeaderDescription">{$formObject->getTitle()}</p>
            {/if}
        </div>

        <nav class="contentHeaderNavigation">
            <ul>
                <li><a href="{link controller='CharactersList' application='rp'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}rp.character.list{/lang}</span></a></li>

                {event name='contentHeaderNavigation'}
            </ul>
        </nav>
    </header>
{/capture}

{include file='header'}

{@$form->getHtml()}

{include file='footer'}