{if $raidEvents|count}
    <section class="section sectionContainerList">
        <ol class="contentItemList">
            {foreach from=$raidEvents item=raidEvent}
                <li class="contentItem contentItemMultiColumn">
                    <div class="box48">
                        {@$raidEvent->getIcon(48)}
                        
                        <div class="containerHeadline">
                            <h3><a>{$raidEvent->getTitle()}</a></h3>
                        </div>
                        
                        <div class="progressBarContainer">
                            <span>{@$raidStats[$raidEvent->eventID]['percent']}% ({@$raidStats[$raidEvent->eventID]['is']}/{@$raidStats[$raidEvent->eventID]['max']})</span>
                            <div class="progressBar" style="width: {@$raidStats[$raidEvent->eventID]['percent']}%"></div>
                        </div>
                    </div>
                </li>
            {/foreach}
        </ol>
    </section>
{else}
    <p class="info" role="status">{lang}wcf.global.noItems{/lang}</p>
{/if}