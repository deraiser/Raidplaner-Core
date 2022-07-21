<ul class="sidebarItemList">
    {foreach from=$boxEventList item=event}
        <li class="box24">
            <a href="{link controller='Event' application='rp' object=$event}{/link}" aria-hidden="true">{@$event->getIcon(24)}</a>

            <div class="sidebarItemTitle">
                <h3>
                    {anchor object=$event class='rpEventLink'}
                </h3>
                <p>{@$event->getFormattedTimeFrame()}</p>
            </div>
        </li>
    {/foreach}
</ul>