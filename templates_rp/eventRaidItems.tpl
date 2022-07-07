<div class="contentItem contentItemMultiColumn attendeeBox" 
     data-status="{$__status}" 
     data-object-id="{$__availableDistributionID}" 
     data-droppable="distribution{$__availableDistributionID}"
     data-event-id="{@$event->eventID}">
    <div class="contentItemLink">
        {if $availableDistribution|isset}
            <div class="contentItemImage">
                {@$availableDistribution->getIcon(16)}
            </div>
        {/if}
        
        <div class="contentItemContent">
            <h2 class="contentItemTitle">{$__title}</h2>
        </div>
    </div>
    <ol class="attendeeList">
        {if $attendees[$__status][$__availableDistributionID]|isset}
            {foreach from=$attendees[$__status][$__availableDistributionID] item=attendee}
                {include file='eventRaidAttendeeItems' application='rp'}
            {/foreach}
        {/if}
    </ol>
</div>