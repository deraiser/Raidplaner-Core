{include file='header' pageTitle='rp.acp.raid.group.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.acp.raid.group.{$action}{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='RaidGroupList' application='rp'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}rp.acp.raid.group.list{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}