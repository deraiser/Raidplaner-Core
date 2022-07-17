<li id="attendee{@$attendee->attendeeID}"
    class="attendee jsClipboardObject{if $event->canEdit()} draggable{/if}" 
    data-object-id="{@$attendee->attendeeID}"  
    data-character-id="{@$attendee->characterID}" 
    data-user-id="{@$attendee->getCharacter()->userID}" 
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
            {@$attendee->getCharacter()->getAvatar()->getImageTag(24)}
            <span>
                <a href="{$attendee->getLink()}" 
                   class="rpEventRaidAttendeeLink" 
                   data-object-id="{@$attendee->attendeeID}">{$attendee->getCharacter()->characterName}
                </a>
            </span>
        </div>
        
        <span class="statusDisplay">
            {if !$attendee->notes|empty}<span class="icon icon16 fa-comment tooltip" title="{$attendee->notes}"></span>{/if}
            {if !$attendee->characterID}<span class="icon icon16 fa-user tooltip" title="{lang}rp.event.raid.attendee.guest{/lang}"></span>{/if}
            {if $attendee->addByLeader}<span class="icon icon16 fa-plus-circle tooltip" title="{lang}rp.event.raid.attendee.addByLeader{/lang}"></span>{/if}
            {if !$event->isCanceled && 
                !$event->isClosed && 
                !$event->getController()->isExpired() &&
                $attendee->getCharacter()->userID == $__wcf->user->userID}
                <div id="attendreeDropdown{@$attendee->attendeeID}" class="dropdown">
                    <a class="dropdownToggle"><span class="icon icon16 fa-cog"></span></a>
                    <ul class="dropdownMenu">
                        <li><a class="jsAttendeeUpdateStatus">{lang}rp.event.raid.updateStatus{/lang}</a></li>
                        <li><a class="jsAttendeeRemove" data-confirm-message-html="{lang __encode=true}rp.event.raid.attendee.remove.confirmMessage{/lang}">{lang}rp.event.raid.attendee.remove{/lang}</a></li>
                    </ul>
                </div>
            {/if}
        </span>
    </div>
</li>