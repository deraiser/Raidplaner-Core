{capture assign='contentInteractionPagination'}
	{pages print=true assign=pagesLinks controller='EventParticipationList' application='rp' link="pageNo=%d"}
{/capture}

{include file='header'}

{if $items}
    <section class="section sectionContainerList">
        <ol class="contentItemList">
            {foreach from=$events item=event}
                <li class="contentItem contentItemMultiColumn" data-object-id="{@$event->eventID}">
                    <div class="contentItemContent">
                        <div class="box48">
                            {@$event->getIcon(48)}

                            <div class="details raidEventInformation">
                                <div class="containerHeadline">
                                    <h3><a href="{$event->getLink()}">{$event->getTitle()}</a></h3>
                                    <p>{@$event->getFormattedTimeFrame()}</p>
                                </div>
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