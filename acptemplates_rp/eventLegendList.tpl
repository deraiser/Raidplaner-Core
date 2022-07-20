{include file='header' pageTitle='rp.acp.event.legend.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}rp.acp.event.legend.list{/lang} <span class="badge badgeInverse">{#$items}</span></h1>
	</div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='EventLegendAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.acp.event.legend.add{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{hascontent}
	<div class="paginationTop">
		{content}{pages print=true assign=pagesLinks controller="EventLegendList" application="rp" link="pageNo=%d"}{/content}
	</div>
{/hascontent}

{if $objects|count}
    <div class="section tabularBox">
        <table class="table jsObjectActionContainer" data-object-action-class-name="rp\data\event\legend\EventLegendAction">
            <thead>
                <tr>
                    <th class="columnID columnLegendID" colspan="2">{lang}wcf.global.objectID{/lang}</th>
                    <th class="columnTitle columnLegendName">{lang}wcf.global.name{/lang}</th>

                    {event name='columnHeads'}
                </tr>
            </thead>
            <tbody class="jsReloadPageWhenEmpty">
                {foreach from=$objects item=legend}
                    <tr id="legendContainer{@$legend->legendID}" class="jsEventLegendRow jsObjectActionObject" data-object-id="{@$legend->getObjectID()}">
                        <td class="columnIcon">
                            <a href="{link controller='EventLegendEdit' application='rp' id=$legend->legendID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
                            {objectAction action="delete" objectTitle=$legend->name}

                            {event name='rowButtons'}
                        </td>
                        <td class="columnID columnLegendID">{@$legend->legendID}</td>
                        <td class="columnTitle columnLegendName">
                            <a title="{lang}rp.acp.event.legend.edit{/lang}" href="{link controller='EventLegendEdit' application='rp' id=$legend->legendID}{/link}">{$legend->name}</a>
                        </td>

                        {event name='columns'}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

    <footer class="contentFooter">
        {hascontent}
            <div class="paginationBottom">
                {content}{@$pagesLinks}{/content}
            </div>
        {/hascontent}

        <nav class="contentFooterNavigation">
            <ul>
                <li><a href="{link controller='EventLegendAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.acp.event.legend.add{/lang}</span></a></li>

                {event name='contentFooterNavigation'}
            </ul>
        </nav>
    </footer>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}