<li id="attendee{@$attendee->attendeeID}"
    class="attendee jsClipboardObject{if $event->canEdit()} draggable{/if}" 
    data-object-id="{@$attendee->attendeeID}"  
    data-user-id="{@$attendee->userID}" 
    data-distribution-id="{$__availableDistributionID}"
    {if $event->canEdit()}draggable="true"{/if}
    data-droppable-to="{implode from=$attendee->possibleDistribution() item=distributionID}distribution{@$distributionID}{/implode}">
    <div class="box24">
        {if !$event->isCanceled && $event->canEdit()}
            <div class="columnMark">
                <input type="checkbox" class="jsClipboardItem" data-object-id="{@$attendee->attendeeID}">
            </div>
        {/if}
        <div class="attendeeName">
            {if $attendee->getCharacter()}
                {@$attendee->getCharacter()->getAvatar()->getImageTag(24)}
                <span>
                    <a href="{$attendee->getLink()}" 
                       class="rpEventRaidAttendeeLink" 
                       data-object-id="{@$attendee->attendeeID}">{$attendee->getCharacter()->characterName}
                    </a>
                </span>
            {else}
                <span>{$attendee->characterName}<span>
            {/if}
        </div>
        
        {if !$event->isCanceled && 
            !$event->isClosed && 
            !$event->getController()->isExpired() &&
            $attendee->getCharacter() && 
            $attendee->getCharacter()->userID == $__wcf->user->userID}
            <span class="statusDisplay">
                <div id="attendreeDropdown{@$attendee->attendeeID}" class="dropdown">
                    <a class="dropdownToggle"><span class="icon icon16 fa-cog"></span></a>
                    <ul class="dropdownMenu">
                        <li><a class="jsAttendeeUpdateStatus">{lang}rp.event.raid.updateStatus{/lang}</a></li>
                        <li><a class="jsAttendeeRemove" data-confirm-message-html="{lang __encode=true}rp.event.raid.attendee.remove.confirmMessage{/lang}">{lang}rp.event.raid.attendee.remove{/lang}</a></li>
                    </ul>
                </div>
            </span>
        {/if}
    </div>
</li>