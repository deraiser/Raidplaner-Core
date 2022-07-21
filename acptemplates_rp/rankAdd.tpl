{include file='header' pageTitle='rp.acp.rank.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.acp.rank.{$action}{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='RankList' application='rp'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}rp.acp.rank.list{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}