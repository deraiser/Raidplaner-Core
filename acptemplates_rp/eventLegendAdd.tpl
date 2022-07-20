{include file='header' pageTitle='rp.acp.event.legend.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.acp.event.legend.{$action}{/lang}</h1>
        {if $action == 'edit'}<p class="contentHeaderDescription">{$formObject->name}</p>{/if}
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='EventLegendList' application='rp'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}rp.acp.event.legend.list{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}