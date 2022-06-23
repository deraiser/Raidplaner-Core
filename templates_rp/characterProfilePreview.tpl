{if $unknownCharacter|isset}
    <p>{lang}rp.character.unknownCharacter{/lang}</p>
{else}
    <div class="box128 characterProfilePreview">
        <a href="{$character->getLink()}" title="{$character->getTitle()}" class="characterProfilePreviewAvatar">
            {@$character->getAvatar()->getImageTag(128)}
        </a>

        <div class="characterInformation">
            {include file='characterInformation' application='rp'}
        </div>

        {hascontent}
            <dl class="plain inlineDataList characterFields">
                {content}
                    {event name='characterFields'}
                {/content}
            </dl>
        {/hascontent}
    </div>
{/if}