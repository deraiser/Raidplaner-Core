{if $unknownAttendee|isset}
    <p>{lang}rp.event.raid.attendee.unknownAttendee{/lang}</p>
{else}
    {assign var='character' value=$attendee->getCharacter()}
    
    {capture assign='contentInformation'}
        <dt>{lang}rp.event.raid.attendee.registration{/lang}</dt>
        <dd>{$attendee->created|plainTime}</dd>

        {if !$character->isPrimary}
            <dt>{lang}rp.character.primary{/lang}</dt>
            <dd>{$character->getPrimaryCharacter()->getTitle()}</dd>
        {/if}
    {/capture}
    
    {include file='characterProfilePreview' application='rp' disableCharacterInformationButtons='true'}
{/if}