{include file='header' pageTitle='rp.acp.raid.event.list'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.acp.raid.event.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='RaidEventAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.acp.raid.event.add{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{hascontent}
    <div class="paginationTop">
        {content}
            {assign var='linkParameters' value=''}

            {pages print=true assign=pagesLinks controller="RaidEventList" application="rp" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
        {/content}
    </div>
{/hascontent}

{if $objects|count}
    <div class="section tabularBox">
        <table class="table jsObjectActionContainer" data-object-action-class-name="rp\data\raid\event\RaidEventAction">
            <thead>
                <tr>
                    <th class="columnID columnEventID{if $sortField == 'eventID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='RaidEventList' application='rp'}pageNo={@$pageNo}&sortField=eventID&sortOrder={if $sortField == 'eventID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                    <th class="columnText columnEventName{if $sortField == 'eventNameI18n'} active {@$sortOrder}{/if}"><a href="{link controller='RaidEventList' application='rp'}pageNo={@$pageNo}&sortField=eventNameI18n&sortOrder={if $sortField == 'eventNameI18n' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.name{/lang}</a></th>
                    <th class="columnText columnPointAccountName{if $sortField == 'pointAccountName'} active {@$sortOrder}{/if}"><a href="{link controller='RaidEventList' application='rp'}pageNo={@$pageNo}&sortField=pointAccountName&sortOrder={if $sortField == 'pointAccountName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}rp.acp.raid.event.point.account{/lang}</a></th>
                    <th class="columnDate columnDefaultPoints{if $sortField == 'defaultPoints'} active {@$sortOrder}{/if}"><a href="{link controller='RaidEventList' application='rp'}pageNo={@$pageNo}&sortField=defaultPoints&sortOrder={if $sortField == 'defaultPoints' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}rp.acp.raid.event.defaultPoints{/lang}</a></th>

                    {event name='columnHeads'}
                </tr>
            </thead>
            <tbody class="jsReloadPageWhenEmpty">
                {foreach from=$objects item=event}
                    <tr class="jsEventRow jsObjectActionObject" data-object-id="{@$event->eventID}">
                        <td class="columnIcon">
                            <a href="{link controller='RaidEventEdit' application='rp' id=$event->eventID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
                            {objectAction action="delete" objectTitle=$event->getTitle()}
                        </td>
                        <td class="columnID columnEventID">{@$event->eventID}</td>
                        <td class="columnText columnEventName"><a href="{link controller='RaidEventEdit' application='rp' id=$event->eventID}{/link}">{$event->getTitle()}</a></td>
                        <td class="columnText columnPointAccountName">{$event->pointAccountName}</td>
                        <td class="columnDigits columnPoints">{#$event->defaultPoints}</td>

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
                <li><a href="{link controller='RaidEventAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.acp.raid.event.add{/lang}</span></a></li>

                {event name='contentFooterNavigation'}
            </ul>
        </nav>
    </footer>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}