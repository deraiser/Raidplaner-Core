{capture assign='pageTitle'}{$__wcf->getActivePage()->getTitle()}{if $raidEvent} ({$raidEvent->getTitle()}){/if}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
{capture assign='contentTitle'}{$__wcf->getActivePage()->getTitle()}{if $raidEvent} ({$raidEvent->getTitle()}){/if} <span class="badge">{#$items}</span>{/capture}
{if $raidEvent}{capture assign='contentDescription'}{lang}rp.raid.event.pointAccount{/lang}: {if $raidEvent->getPointAccount()}{$raidEvent->getPointAccount()->getTitle()}{else}-{/if}{/capture}{/if}

{capture assign='headContent'}
    {if $pageNo < $pages}
        <link rel="next" href="{link controller='Raids' application='rp'}pageNo={@$pageNo+1}{/link}">
    {/if}
    {if $pageNo > 1}
        <link rel="prev" href="{link controller='Raids' application='rp'}{if $pageNo > 2}pageNo={@$pageNo-1}{/if}{/link}">
    {/if}
    <link rel="canonical" href="{link controller='Raids' application='rp'}{if $pageNo > 1}pageNo={@$pageNo}{/if}{/link}">
{/capture}

{capture assign='contentHeaderNavigation'}
    {if $__wcf->getSession()->getPermission('mod.rp.canAddRaid')}
        <li><a href="{link controller='RaidAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.raid.add{/lang}</span></a></li>
    {/if}
{/capture}

{capture assign='contentInteractionPagination'}
	{pages print=true assign=pagesLinks controller='RaidList' application='rp' link="pageNo=%d"}
{/capture}

{include file='header'}

{if $items}
    <section class="section sectionContainerList">
        <ol class="contentItemList">
            {foreach from=$objects item=raid}
                 <li class="contentItem contentItemMultiColumn" data-object-id="{@$raid->raidID}">
                     <div class="contentItemContent">
                        <div class="box64">
                            {@$raid->getIcon(64)}

                            <div class="details raidInformation">
                                <div class="containerHeadline">
                                    <h3><a href="{$raid->getLink()}">{$raid->getTitle()}</a></h3>
                                </div>

                                <dl class="plain dataList containerContent small">
                                    <dt>{lang}rp.raid.date{/lang}</dt>
                                    <dd>{$raid->date|date}</dd>

                                    <dt>{lang}rp.raid.attendees{/lang}</dt>
                                    <dd>{$raid->getAttendees()|count}</dd>

                                    <dt>{lang}rp.raid.points{/lang}</dt>
                                    <dd>{#$raid->points}</dd>

                                    {if RP_ENABLE_ITEM}
                                        <dt>{lang}rp.raid.items{/lang}</dt>
                                        <dd>{#$raid->items}</dd>
                                    {/if}

                                    <dt>{lang}rp.raid.notes{/lang}</dt>
                                    <dd{if $raid->notes|strlen > 100} title="{$raid->notes}"{/if}>{if $raid->notes|empty}-{else}{$raid->notes|truncate:100}{/if}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                 </li>
            {/foreach}
        </ol>
    </section>
    
    <footer class="contentFooter">
        {hascontent}
            <div class="paginationBottom">
                {content}{@$pagesLinks}{/content}
            </div>
        {/hascontent}

        {hascontent}
            <nav class="contentFooterNavigation">
                <ul>
                    {content}{event name='contentFooterNavigation'}{/content}
                </ul>
            </nav>
        {/hascontent}
    </footer>
{else}
    <p class="info" role="status">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}