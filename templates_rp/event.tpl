{capture assign='pageTitle'}{$event->getTitle()}{/capture}

{capture assign='contentHeaderMetaData'}
    <li>
        <span class="icon icon16 fa-clock-o"></span>
        <a href="{link controller='Event' application='rp' object=$event}{/link}">{@$event->getFormattedTimeFrame()}</a>
    </li>
{/capture}

{event name='beforeHeader'}

{capture assign='contentHeader'}
    <header class="contentHeader messageGroupContentHeader rpEventHeader{if $event->isDeleted} messageDeleted{/if}{if $event->isDisabled} messageDisabled{/if}" 
            data-object-id="{@$event->eventID}"
            data-is-deleted="{@$event->isDeleted}"
            data-is-disabled="{@$event->isDisabled}"
            data-can-view-deleted-event="{if $__wcf->session->getPermission('mod.rp.canViewDeletedEvent')}true{else}false{/if}"
            data-can-trash-event="{if $event->canTrash()}true{else}false{/if}"
            data-can-restore-event="{if $event->canRestore()}true{else}false{/if}"
            data-can-delete-event="{if $__wcf->getSession()->getPermission('mod.rp.canDeleteEvent')}true{else}false{/if}"
            data-can-edit-event="{if $event->canEdit()}true{else}false{/if}"
            data-can-moderate-event="{if $__wcf->getSession()->getPermission('mod.rp.canModerateEvent')}true{else}false{/if}">
        <div class="contentHeaderIcon">
			{@$event->getIcon(64)}
		</div>
        
        <div class="contentHeaderTitle">
            <h1 class="contentTitle">
                {$event->getTitle()}
                {if $event->isNew()}<span class="badge green">{lang}wcf.message.new{/lang}</span>{/if}
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

                <li>
                    <span class="icon icon16 fa-eye"></span>
                    {lang}rp.event.eventViews{/lang}
                </li>

                {event name='afterMetaData'}
            </ul>
        </div>

        {hascontent}
            <nav class="contentHeaderNavigation">
                <ul>
                    {content}
                        {event name='contentHeaderNavigation'}
                    {/content}
                </ul>
            </nav>
        {/hascontent}
    </header>
{/capture}

{capture assign='contentInteractionButtons'}
    <div class="contentInteractionButton dropdown jsOnly jsEventDropdown">
        <a href="#" class="button small dropdownToggle"><span class="icon icon16 fa-sliders"></span> <span>{lang}rp.event.settings{/lang}</span></a>
        <ul class="dropdownMenu jsEventDropdownItems">
            <li data-option-name="delete" data-confirm-message="{lang __encode=true}rp.event.delete.confirmMessage{/lang}"><span>{lang}rp.event.delete{/lang}</span></li>
            <li data-option-name="restore"><span>{lang}rp.event.restore{/lang}</span></li>
            <li data-option-name="trash" data-confirm-message="{lang __encode=true}rp.event.trash.confirmMessage{/lang}"><span>{lang}rp.event.trash{/lang}</span></li>
            <li data-option-name="enable"><span>{lang}rp.event.enable{/lang}</span></li>
            <li data-option-name="disable"><span>{lang}rp.event.disable{/lang}</span></li>
            <li class="dropdownDivider" />
            <li data-option-name="editLink" data-link="{link controller='EventEdit' application='rp' id=$event->eventID}{/link}"><span>{lang}rp.event.edit{/lang}</span></li>
        </ul>
    </div>
{/capture}

{include file='header'}

{event name='afterHeader'}

{if !$event->notes|empty}
    <section class="section">
        <h2 class="sectionTitle">{lang}rp.event.notes{/lang}</h2>

        <dl>
            <dt></dt>
            <dd>
                <div class="htmlContent">
                    {@$event->getFormattedNotes()}
                </div>
            </dd>
        </dl>
    </section>
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

<script data-relocate="true">
    require(['WoltLabSuite/Core/Language', 'Daries/RP/Ui/Event/InlineEditor'], function(Language, UiEventInlineEditor) {
        Language.addObject({
            'rp.event.delete.confirmMessage': '{jslang}rp.event.delete.confirmMessage{/jslang}',
            'rp.event.trash.confirmMessage': '{jslang}rp.event.trash.confirmMessage{/jslang}',
            'rp.event.trash.reason': '{jslang}rp.event.trash.reason{/jslang}',
        });
        
        new UiEventInlineEditor({@$event->eventID});
    });
</script>

{include file='footer'}