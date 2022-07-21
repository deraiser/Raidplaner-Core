<section class="section sectionContainerList rpEventList">
    <ol class="contentItemList">
        {foreach from=$objects item=event}
            <li class="contentItem contentItemMultiColumn rpEventItem{if $event->isNew()} new{/if}">
                <div class="contentItemContent">
                    <div class="box48">
                        {@$event->getIcon(48)}

                        <div class="details">
                            <div class="containerHeadline">
                                <h3>
                                    {anchor object=$event class='rpEventLink'}
                                </h3>
                                <p>{@$event->getFormattedTimeFrame()}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        {/foreach}
    </ol>
</section>