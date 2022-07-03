{if $unknownEvent|isset}
    <p>{lang}rp.event.unknownEvent{/lang}</p>
{else}
    <div class="box128">
        {@$event->getIcon(128)}
        
        <div>
            <div class="containerHeadline">
                <h3>{$event->getTitle()}</h3>
                <p>{@$event->getFormattedTimeFrame()}</p>
                
                {event name='containerHeadline'}
            </div>
            
            <div class="containerContent">{@$event->getExcerpt()}</div>
            
            {event name='previewData'}
        </div>
    </div>
{/if}