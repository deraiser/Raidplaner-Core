{if $event->getController()->getObjectTypeName() == 'info.daries.rp.event.appointment'}
    {if !$event->getController()->isExpired()}
        <li class="dropdown">
            <a class="button dropdownToggle"><span class="icon icon16 fa-cog"></span> <span>{lang}rp.event.participation{/lang}</span></a>
            <div class="dropdownMenu" id="eventDropdown">
                <ul class="scrollableDropdownMenu">
                    <li><a href="#" class="button jsButtonEventAccepted" data-status="accepted" title="{lang}rp.event.accepted{/lang}"><span class="icon icon16 fa-check-circle"></span> <span>{lang}rp.event.accepted{/lang}</span></a></li>
                    <li><a href="#" class="button jsButtonEventMaybe" data-status="maybe" title="{lang}rp.event.maybe{/lang}"><span class="icon icon16 fa-circle"></span> <span>{lang}rp.event.maybe{/lang}</span></a></li>
                    <li><a href="#" class="button jsButtonEventCanceled" data-status="canceled" title="{lang}rp.event.canceled{/lang}"><span class="icon icon16 fa-times-circle"></span> <span>{lang}rp.event.canceled{/lang}</span></a></li>
                </ul>
            </div>
        </li>

        <script data-relocate="true">
            require(['Language', 'Daries/RP/Ui/Event/Appointment'], function(Language, EventAppointment) {
                new EventAppointment({@$eventID}, {@$__wcf->user->userID});
            });
        </script>
    {/if}
{/if}

{if $event->getController()->getObjectTypeName() == 'info.daries.rp.event.raid'}
    <li class="jsButtonAttendee" style="display: none;"></li>
    
    <script data-relocate="true">
        require(['Language', 'Daries/RP/Ui/Event/Raid/Participate'], function(Language, EventRaidParticipate) {
            Language.addObject({
                'rp.event.raid.attendee.add': '{jslang}rp.event.raid.attendee.add{/jslang}',
                'rp.event.raid.participate': '{jslang}rp.event.raid.participate{/jslang}',
                'rp.event.raid.participate.remove': '{jslang}rp.event.raid.participate.remove{/jslang}',
            });
        
            EventRaidParticipate.setup({@$event->eventID}, {
                attendeeId: {@$event->getController()->getContentData('hasAttendee')},
                canParticipate:  {if $__wcf->session->getPermission('user.rp.canParticipate') && $event->getController()->getContentData('characters')|count}true{else}false{/if},
                hasAttendee: {if $event->getController()->getContentData('hasAttendee')}true{else}false{/if},
            });
        });
    </script>
{/if}