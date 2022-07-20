{capture assign='pageTitle'}{$__wcf->getActivePage()->getTitle()}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
{capture assign='contentTitle'}{$__wcf->getActivePage()->getTitle()} <span class="badge">{#$items}</span>{/capture}

{capture assign='headContent'}
    {if $pageNo < $pages}
        <link rel="next" href="{link controller='RaidEvents' application='rp'}pageNo={@$pageNo+1}{/link}">
    {/if}
    {if $pageNo > 1}
        <link rel="prev" href="{link controller='RaidEvents' application='rp'}{if $pageNo > 2}pageNo={@$pageNo-1}{/if}{/link}">
    {/if}
    <link rel="canonical" href="{link controller='RaidEvents' application='rp'}{if $pageNo > 1}pageNo={@$pageNo}{/if}{/link}">
{/capture}

{capture assign='contentInteractionPagination'}
	{pages print=true assign=pagesLinks controller='RaidEventList' application='rp' link="pageNo=%d"}
{/capture}

{include file='header'}

{if $items}
     <section class="section sectionContainerList">
        <ol class="contentItemList">
            {foreach from=$objects item=event}
                <li class="contentItem contentItemMultiColumn" data-object-id="{@$event->eventID}">
                    <div class="contentItemContent">
                        <div class="box64">
                            {@$event->getIcon(64)}

                            <div class="details raidEventInformation">
                                <div class="containerHeadline">
                                    <h3><a href="{$event->getLink()}">{$event->getTitle()}</a></h3>
                                </div>

                                <dl class="plain dataList containerContent small">
                                    <dt>{lang}rp.raid.event.pointAccount{/lang}</dt>
                                    <dd>{if $event->getPointAccount()}{$event->getPointAccount()->getTitle()}{else}-{/if}</dd>
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