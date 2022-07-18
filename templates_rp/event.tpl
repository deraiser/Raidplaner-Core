{capture assign='pageTitle'}{$event->getTitle()}{/capture}

{if $event->isRaidEvent()}
    {capture append='sidebarRight'}
        {hascontent}
            <section class="box" data-static-box-identifier="info.daries.rp.event.raid.required">
                <h2 class="boxTitle">{lang}rp.event.raid.required{/lang}</h2>

                <div class="boxContent">
                    <dl class="plain dataList">
                        {content}
                            {foreach from=$event->getController()->getRequireds() key=__key item=__value}
                                <dt>{lang}{$__key}{/lang}</dt>
                                <dd>{@$__value}</dd>
                            {/foreach}
                        {/content}
                    </dl>
                </div>
            </section>
        {/hascontent}

        {if $event->leaders}
            <section class="box" data-static-box-identifier="info.daries.rp.event.raid.leaders">
                <h2 class="boxTitle">{lang}rp.event.raid.leader{if $event->getController()->getLeaders()|count > 1}s{/if}{/lang}</h2>

                <div class="boxContent">
                    <ul class="sidebarItemList">
                        {foreach from=$event->getController()->getLeaders() item=leader}
                            <li class="box24">
                                {character object=$leader type='avatar24' ariaHidden='true' tabindex='-1'}

                                <div class="sidebarItemTitle">
                                    <h3>{character object=$leader}</h3>
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </section>
        {/if}
    {/capture}
{/if}

{if $event->getController()->showEventNodes('right')}
    {hascontent}
        {capture append='sidebarRight'}
            <section class="box" data-static-box-identifier="info.daries.rp.notes">
                <h2 class="boxTitle">{lang}rp.event.notes{/lang}</h2>

                <div class="boxContent htmlContent">
                    {content}
                        {@$event->getSimplifiedFormattedNotes()}
                    {/content}
                </div>
            </section>
        {/capture}
    {/hascontent}
{/if}

{capture assign='contentHeader'}
    <header class="contentHeader messageGroupContentHeader rpEventHeader{if $event->isCanceled} messageCanceled{/if}{if $event->isDeleted} messageDeleted{/if}{if $event->isDisabled} messageDisabled{/if}" 
            data-object-id="{@$event->eventID}"
            data-is-canceled="{@$event->isCanceled}"
            data-is-deleted="{@$event->isDeleted}"
            data-is-disabled="{@$event->isDisabled}"
            data-can-view-deleted-event="{if $__wcf->session->getPermission('mod.rp.canViewDeletedEvent')}true{else}false{/if}"
            data-can-trash-event="{if $event->canTrash()}true{else}false{/if}"
            data-can-restore-event="{if $event->canRestore()}true{else}false{/if}"
            data-can-delete-event="{if $__wcf->getSession()->getPermission('mod.rp.canDeleteEvent')}true{else}false{/if}"
            data-can-edit-event="{if $event->canEdit()}true{else}false{/if}"
            data-can-moderate-event="{if $__wcf->getSession()->getPermission('mod.rp.canModerateEvent')}true{else}false{/if}"
            {if $event->isRaidEvent()}
                data-can-cancel-event="{if $event->canEdit() || $event->getController()->isLeader()}true{else}false{/if}"
                data-can-transform="{if !$event->raidID && $event->getController()->isLeader()}true{else}false{/if}"
            {/if}
            >
        <div class="contentHeaderIcon">
			{@$event->getIcon(64)}
		</div>
        
        <div class="contentHeaderTitle">
            <h1 class="contentTitle">
                {$event->getTitle()}
            </h1>
            
            <ul class="inlineList commaSeparated contentHeaderMetaData">
                {event name='beforeMetaData'}

                <li>
                    <span class="icon icon16 fa-clock-o"></span>
                    {@$event->getFormattedTimeFrame()}
                </li>

                <li>
                    <span class="icon icon16 fa-user"></span>
                    {user object=$event->getUserProfile()}
				</li>
                
				{if $event->getDiscussionProvider()->getDiscussionCountPhrase()}
					<li>
						<span class="icon icon16 fa-comments"></span>
						{if $event->getDiscussionProvider()->getDiscussionLink()}<a href="{$event->getDiscussionProvider()->getDiscussionLink()}">{else}<span>{/if}
						{$event->getDiscussionProvider()->getDiscussionCountPhrase()}
						{if $event->getDiscussionProvider()->getDiscussionLink()}</a>{else}</span>{/if}
					</li>
				{/if}

                <li>
                    <span class="icon icon16 fa-eye"></span>
                    {lang}rp.event.eventViews{/lang}
                </li>
                
                {if $event->isRaidEvent() && $event->raidID}
                    <li>
                        <span class="icon icon16 fa-exchange"></span>
                        <a href="{link controller='Raid' application='rp' id=$event->raidID}{/link}">{lang}rp.event.raidLink{/lang}</a>
                    </li>
                {/if}
                
                {if $event->isNew()}
                    <li><span class="badge label green newMessageBadge">{lang}wcf.message.new{/lang}</span></li>
                {/if}
                
                {if $event->isCanceled}
                    <li><span class="badge label red jsIconDisabled">{lang}rp.event.raid.message.status.canceled{/lang}</span></li>
                {/if}
                
                {if $event->isDisabled}
                    <li><span class="badge label green jsIconDisabled">{lang}wcf.message.status.disabled{/lang}</span></li>
                {/if}
                
                {if $event->isDeleted}
                    <li><span class="badge label red jsIconDeleted">{lang}wcf.message.status.deleted{/lang}</span></li>
                {/if}

                {event name='afterMetaData'}
            </ul>
        </div>

        {hascontent}
            <nav class="contentHeaderNavigation">
                <ul>
                    {content}
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

                        {if $event->isRaidEvent()}
                            {if !$event->isCanceled && !$event->getController()->isExpired()}
                                <li class="jsButtonAttendee" style="display: none;"></li>
                                <script data-relocate="true">
                                    require(['Language', 'Daries/RP/Ui/Event/Raid/Participate'], function(Language, EventRaidParticipate) {
                                        Language.addObject({
                                            'rp.event.raid.attendee.add': '{jslang}rp.event.raid.attendee.add{/jslang}',
                                            'rp.event.raid.attendee.remove.confirmMessage': '{jslang}rp.event.raid.attendee.remove.confirmMessage{/jslang}',
                                            'rp.event.raid.participate': '{jslang}rp.event.raid.participate{/jslang}',
                                            'rp.event.raid.participate.remove': '{jslang}rp.event.raid.participate.remove{/jslang}',
                                        });

                                        EventRaidParticipate.setup({@$event->eventID}, {
                                            attendeeId: {@$event->getController()->getContentData('hasAttendee')},
                                            canParticipate:  {if $__wcf->session->getPermission('user.rp.canParticipate') && $event->getController()->getContentData('characters')|count}true{else}false{/if},
                                            hasAttendee: {if $event->getController()->getContentData('hasAttendee')}true{else}false{/if},
                                            isExpired: {if $event->getController()->isExpired()}true{else}false{/if},
                                        });
                                    });
                                </script>
                            {/if}
                        {/if}
                    {/content}
                </ul>
            </nav>
        {/hascontent}
    </header>
{/capture}

{capture assign='contentInteractionButtons'}
    {if $event->isRaidEvent() && $event->getController()->isLeader()}
        <li><a class="button small jsButtonAttendeeAdd"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.event.raid.participate.add{/lang}</span></a></li>
        
        <script data-relocate="true">
            require(['Language', 'Daries/RP/Ui/Event/Raid/Leader/Participate'], function(Language, EventRaidLeaderParticipate) {
                Language.addObject({
                    'rp.event.raid.participate.add': '{jslang}rp.event.raid.participate.add{/jslang}',
                });

                new EventRaidLeaderParticipate({@$event->eventID});
            });
        </script>
    {/if}
    
    <div class="contentInteractionButton dropdown jsOnly jsEventDropdown" style="display: none;">
        <a href="#" class="button small dropdownToggle"><span class="icon icon16 fa-sliders"></span> <span>{lang}rp.event.settings{/lang}</span></a>
        <ul class="dropdownMenu jsEventDropdownItems">
            <li data-option-name="delete"><span>{lang}rp.event.delete{/lang}</span></li>
            <li data-option-name="restore"><span>{lang}rp.event.restore{/lang}</span></li>
            <li data-option-name="trash"><span>{lang}rp.event.trash{/lang}</span></li>
            <li data-option-name="enable"><span>{lang}rp.event.enable{/lang}</span></li>
            <li data-option-name="disable"><span>{lang}rp.event.disable{/lang}</span></li>
            {if $event->isRaidEvent()}
                <li data-option-name="cancel"><span>{lang}rp.event.raid.cancel{/lang}</span></li>
                {if !$event->raidID && $event->getController()->isLeader()}
                    <li data-option-name="transform" data-link="{link controller='RaidAdd' application='rp'}eventID={@$event->eventID}{/link}"><span>{lang}rp.event.raid.transform{/lang}</span></li>
                {/if}
            {/if}
            <li class="dropdownDivider" />
            <li data-option-name="editLink" data-link="{link controller='EventEdit' application='rp' id=$event->eventID}{/link}"><span>{lang}rp.event.edit{/lang}</span></li>
        </ul>
    </div>
{/capture}

{event name='beforeHeader'}

{include file='header'}

{event name='afterHeader'}

{if $event->getController()->showEventNodes('center')}
    {hascontent}
        <section class="section">
            <h2 class="sectionTitle">{lang}rp.event.notes{/lang}</h2>

            <dl>
                <dt></dt>
                <dd>
                    <div class="htmlContent">
                        {content}
                            {@$event->getFormattedNotes()}
                        {/content}
                    </div>
                </dd>
            </dl>
        </section>
    {/hascontent}
{/if}

{event name='afterHeader'}

{if !$event->isDeleted && $event->getController()->isExpired()}
    <p class="error">{lang}rp.event.expired{/lang}</p>
{/if}

{if $event->getDeleteNote()}
    <div class="section">
        <p class="rpEventDeleteNote">{@$event->getDeleteNote()}</p>
    </div>
{/if}

{@$event->getController()->getContent()}

<div class="eventLikeContent" {@$__wcf->getReactionHandler()->getDataAttributes('info.daries.rp.likeableEvent', $event->eventID)}>
    <div class="row eventLikeSection">
        {if MODULE_LIKE && RP_EVENT_ENABLE_LIKE && $__wcf->session->getPermission('user.like.canViewLike')}
            <div class="col-xs-12 col-md-6">
                <div class="eventLikesSummery">
                    {include file="reactionSummaryList" reactionData=$eventLikeData objectType="info.daries.rp.likeableEvent" objectID=$event->eventID}
                </div>
            </div>
        {/if}

        <div class="col-xs-12 col-md-6 col-md{if !(MODULE_LIKE && RP_EVENT_ENABLE_LIKE && $__wcf->session->getPermission('user.like.canViewLike'))} col-md-offset-6{/if}">
            <ul class="eventLikeButtons buttonGroup buttonList smallButtons">
                <li>
                    <a href="{$event->getLink()}" class="button wsShareButton jsOnly" data-link-title="{$event->getTitle()}">
                        <span class="icon icon16 fa-share-alt"></span> <span>{lang}wcf.message.share{/lang}</span>
                    </a>
                </li>
                {if $__wcf->session->getPermission('user.profile.canReportContent')}
                    <li class="jsReportEvent jsOnly" data-object-id="{@$event->eventID}"><a href="#" title="{lang}wcf.moderation.report.reportContent{/lang}" class="button jsTooltip"><span class="icon icon16 fa-exclamation-triangle"></span> <span class="invisible">{lang}wcf.moderation.report.reportContent{/lang}</span></a></li>
                {/if}
                {if MODULE_LIKE && RP_EVENT_ENABLE_LIKE && $__wcf->session->getPermission('user.like.canLike') && $event->userID != $__wcf->user->userID}
                    <li class="jsOnly"><span class="button reactButton{if $eventLikeData[$event->eventID]|isset && $eventLikeData[$event->eventID]->reactionTypeID} active{/if}" title="{lang}wcf.reactions.react{/lang}" data-reaction-type-id="{if $eventLikeData[$event->eventID]|isset && $eventLikeData[$event->eventID]->reactionTypeID}{$eventLikeData[$event->eventID]->reactionTypeID}{else}0{/if}"><span class="icon icon16 fa-smile-o"></span> <span class="invisible">{lang}wcf.reactions.react{/lang}</span></span></li>
                {/if}
            </ul>
        </div>
    </div>
</div>

{if ENABLE_SHARE_BUTTONS}
    {capture assign='footerBoxes'}
        <section class="box boxFullWidth jsOnly">
            <h2 class="boxTitle">{lang}wcf.message.share{/lang}</h2>

            <div class="boxContent">
                {include file='shareButtons'}
            </div>
        </section>
    {/capture}
{/if}

<footer class="contentFooter">
    {hascontent}
        <nav class="contentFooterNavigation">
            <ul>
                {content}{event name='contentFooterNavigation'}{/content}
            </ul>
        </nav>
    {/hascontent}
</footer>

{event name='afterFooter'}

{if $previousEvent || $nextEvent}
    <div class="section eventNavigation">
        <nav>
            <ul>
                {if $previousEvent}
                    <li class="previousEventButton">
                        <a href="{$previousEvent->getLink()}" rel="prev">
                            {if $previousEvent->getIcon()}
                                <div class="box96">
                                    <span class="eventNavigationEventImage">{@$previousEvent->getIcon(48)}</span>

                                    <div>
                                        <span class="eventNavigationEntityName">{lang}rp.event.previousEvent{/lang}</span>
                                        <span class="eventNavigationEventTitle">{$previousEvent->getTitle()}</span>
                                    </div>
                                </div>
                            {else}
                                <div>
                                    <span class="eventNavigationEntityName">{lang}rp.event.previousEvent{/lang}</span>
                                    <span class="eventNavigationEventTitle">{$previousEvent->getTitle()}</span>
                                </div>
                            {/if}
                        </a>
                    </li>
                {/if}

                {if $nextEvent}
                    <li class="nextEventButton">
                        <a href="{$nextEvent->getLink()}" rel="next">
                            {if $nextEvent->getIcon()}
                                <div class="box96">
                                    <span class="eventNavigationEventImage">{@$nextEvent->getIcon(48)}</span>

                                    <div>
                                        <span class="eventNavigationEntityName">{lang}rp.event.nextEvent{/lang}</span>
                                        <span class="eventNavigationEventTitle">{$nextEvent->getTitle()}</span>
                                    </div>
                                </div>
                            {else}
                                <div>
                                    <span class="eventNavigationEntityName">{lang}rp.event.nextEvent{/lang}</span>
                                    <span class="eventNavigationEventTitle">{$nextEvent->getTitle()}</span>
                                </div>
                            {/if}
                        </a>
                    </li>
                {/if}
            </ul>
        </nav>
    </div>
{/if}

{event name='beforeComments'}

{@$event->getDiscussionProvider()->renderDiscussions()}

{if MODULE_LIKE && RP_EVENT_ENABLE_LIKE}
	<script data-relocate="true">
		require(['WoltLabSuite/Core/Ui/Reaction/Handler'], function(UiReactionHandler) {
			new UiReactionHandler('info.daries.rp.likeableEvent', {
				// permissions
				canReact: {if $__wcf->getUser()->userID}true{else}false{/if},
				canReactToOwnContent: false,
				canViewReactions: {if LIKE_SHOW_SUMMARY}true{else}false{/if},
				
				// selectors
				containerSelector: '.eventLikeContent',
				summarySelector: '.eventLikesSummery'
			});
		});
	</script>
{/if}

<script data-relocate="true">
    require(['WoltLabSuite/Core/Language', 'Daries/RP/Ui/Event/InlineEditor'], function(Language, UiEventInlineEditor) {
        Language.addObject({
            'rp.event.delete.confirmMessage': '{jslang}rp.event.delete.confirmMessage{/jslang}',
            'rp.event.raid.cancel.confirmMessage': '{jslang}rp.event.raid.cancel.confirmMessage{/jslang}',
            'rp.event.trash.confirmMessage': '{jslang}rp.event.trash.confirmMessage{/jslang}',
            'rp.event.trash.reason': '{jslang}rp.event.trash.reason{/jslang}',
        });
        
        new UiEventInlineEditor({@$event->eventID});
    });
</script>

{include file='footer'}