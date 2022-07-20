{capture assign='pageTitle'}{lang}wcf.date.month.{@$month->getName()}{/lang} {@$month->getYear()}{/capture}
{capture assign='contentTitle'}{lang}wcf.date.month.{@$month->getName()}{/lang} {@$month->getYear()}{/capture}

{capture assign='contentHeaderNavigation'}
	<li>
		<ul class="buttonGroup">
			{if $month->getPreviousMonth()->getYear() > 1969}<li><a href="{link controller='Calendar' application='rp' year=$month->getPreviousMonth()->getYear() month=$month->getPreviousMonth()->getMonth()}{/link}" class="button jsTooltip" title="{lang}wcf.date.month.{@$month->getPreviousMonth()->getName()}{/lang} {@$month->getPreviousMonth()->getYear()}"><span class="icon icon16 fa-chevron-left"></span> <span class="invisible">{lang}wcf.date.month.{@$month->getPreviousMonth()->getName()}{/lang} {@$month->getPreviousMonth()->getYear()}</span></a></li>{/if}
			<li><a href="{link controller='Calendar' application='rp'}{/link}" class="button jsTooltip" title="{lang}wcf.date.month.{@$currentMonth->getName()}{/lang} {@$currentMonth->getYear()}">{lang}rp.calendar.today{/lang}</a></li>
			{if $month->getNextMonth()->getYear() < 2038}<li><a href="{link controller='Calendar' application='rp' year=$month->getNextMonth()->getYear() month=$month->getNextMonth()->getMonth()}{/link}" class="button jsTooltip" title="{lang}wcf.date.month.{@$month->getNextMonth()->getName()}{/lang} {@$month->getNextMonth()->getYear()}"><span class="icon icon16 fa-chevron-right"></span> <span class="invisible">{lang}wcf.date.month.{@$month->getNextMonth()->getName()}{/lang} {@$month->getNextMonth()->getYear()}</span></a></li>{/if}
		</ul>
	</li>
{/capture}

{capture assign='headContent'}
	{if $__wcf->getUser()->userID}
		<link rel="alternate" type="application/rss+xml" title="{lang}wcf.global.button.rss{/lang}" href="{link controller='CalendarFeed' application='rp'}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}">
	{else}
		<link rel="alternate" type="application/rss+xml" title="{lang}wcf.global.button.rss{/lang}" href="{link controller='CalendarFeed' application='rp'}{/link}">
	{/if}
{/capture}

{capture assign='contentInteractionButtons'}
    <a href="#" class="markAllAsReadButton contentInteractionButton button small jsOnly"><span class="icon icon16 fa-check"></span> <span>{lang}wcf.global.button.markAllAsRead{/lang}</span></a>
{/capture}

{include file='header'}

<div class="section">
    <div class="rpCalendar">
        <div class="rpDays">
            {foreach from=$weekDays item=week}
                <div class="rpDayName">
                    {lang}wcf.date.day.{$week}{/lang}
                </div>
            {/foreach}
            
            {foreach from=$calendar->getDays() item=day}
                <div class="rpDay{if $day->getMonth() !== $calendar->getMonth()} ignore{/if}{if $day->isCurrentDay($currentDay)} selected{/if}"
                    first-day-of-week="{if $day->isFirstDayOfWeek()}1{else}0{/if}"
                    last-day-of-week="{if $day->isLastDayOfWeek()}1{else}0{/if}">
                    <span>{@$day->getDay()}</span>
                    
                    {if $calendar->getEvents($day)|count}
                        {foreach from=$calendar->getEvents($day) item=event}
                            {if $event == ':empty'}
                                <div class="rpEvent rpEmptyEvent"></div>
                            {else}
                                <div class="rpEvent
                                        {if $event->isNew()} isNew{/if}
                                        {if $event->cssMultipleEvent} {$event->cssMultipleEvent}{/if}
                                        {if $event->isDisabled} rpEventDisabled{/if}
                                        {if $event->isClosed} rpEventClosed{/if}
                                        {if $event->isDeleted} rpEventDeleted{/if}"
                                    {@$event->getCustomCSS()}>
                                    {if !$event->cssMultipleEvent || ($event->cssMultipleEvent != 'rpEventStart' && $day->isFirstDayOfWeek()) || $event->cssMultipleEvent == 'rpEventStart'}
                                        {if $event->getController()->isExpired()}
                                            <span class="icon icon16 fa-lock rpEventExpired jsTooltip" title="{lang}rp.event.expired{/lang}"></span>
                                        {elseif $event->getController()->hasLogin()}
                                            <span class="icon icon16 fa-clock-o rpEventLogin jsTooltip" title="{lang}rp.event.login{/lang}"></span>
                                        {elseif $event->getController()->getIcon(16)}
                                            {@$event->getController()->getIcon(16)}
                                        {/if}
                                    {/if}
                                    
                                    <a href="{$event->getLink()}" class="rpEventLink" data-object-id="{@$event->eventID}">
                                        {if $event->cssMultipleEvent}
                                            {if ($event->cssMultipleEvent != 'rpEventStart' && $day->isFirstDayOfWeek()) || $event->cssMultipleEvent == 'rpEventStart'}
                                                {$event->getTitle()}
                                            {else}
                                                &nbsp;
                                            {/if}
                                        {else}
                                            {$event->getTitle()}
                                        {/if}
                                    </a>
                                </div>
                            {/if}
                        {/foreach}
                    {/if}
                </div>
            {/foreach}
        </div>
    </div>
</div>

<footer class="contentFooter">
    {hascontent}
        <nav class="contentFooterNavigation">
            <ul>
                {content}
                    {if $__wcf->user->userID && $__wcf->session->getPermission('user.rp.canCreateEvent')}
                        <li><a href="#" class="button buttonPrimary jsButtonEventAdd"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.event.add{/lang}</span></a></li>
                    {/if}
                    
					{event name='contentFooterNavigation'}
                {/content}
            </ul>
        </nav>
    {/hascontent}
</footer>

<script data-relocate="true">
	$(function() {
        {if $__wcf->user->userID && $__wcf->session->getPermission('user.rp.canCreateEvent')}
            $('.jsCalendarDblClickEvent').dblclick(function(event) {
                var $target = $(event.currentTarget);
                window.location = '{link controller='EventAdd' application='rp' encode=false}date=%date%{/link}'.replace(/\%date\%/, $target.data('date'));
            });
		{/if}
		
		{event name='javascriptInit'}
	});
</script>

<script data-relocate="true">
    require(['Daries/RP/Ui/Event/MarkAllAsRead'], function(UiEventMarkAllAsRead) {
        UiEventMarkAllAsRead.init();
    });
</script>

{if $__wcf->getSession()->getPermission('user.rp.canCreateEvent')}
	{include file='eventAddDialog' application='rp'}
{/if}
{include file='footer'}