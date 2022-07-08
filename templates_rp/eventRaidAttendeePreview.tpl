{if $unknownAttendee|isset}
    <p>{lang}rp.event.raid.attendee.unknownAttendee{/lang}</p>
{else}
    {assign var='character' value=$attendee->getCharacter()}
    
    <div class="box128 eventRaidAttendeePreview">
        <a href="{$character->getLink()}" title="{$character->characterName}" class="eventRaidAttendeePreviewAvatar">
            {@$character->getAvatar()->getImageTag(128)}
        </a>

        <div class="characterInformation">
            {include file='characterInformation' application='rp'}
        </div>

        <dl class="plain inlineDataList characterFields">
            <dt>{lang}rp.event.raid.attendee.registration{/lang}</dt>
            <dd>{$attendee->created|plainTime}</dd>

            {if !$character->isPrimary}
                <dt>{lang}rp.character.primary{/lang}</dt>
                <dd>{$character->getPrimaryCharacter()->getTitle()}</dd>
            {/if}

            {event name='characterFields'}
        </dl>
    </div>
{/if}