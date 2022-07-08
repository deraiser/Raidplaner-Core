{if $unknownCharacter|isset}
    <p>{lang}rp.character.unknownCharacter{/lang}</p>
{else}
    <div class="box48 characterProfilePreview">
        <a href="{$character->getLink()}" title="{$character->getTitle()}" class="characterProfilePreviewAvatar">
            {@$character->getAvatar()->getImageTag(48)}
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

    {hascontent}
        <div class="tabMenuContainer characterPreviewTab">
            <nav class="tabMenu">
                <ul>
                    {content}
                        {if $character->getOtherCharacters()|count}
                            <li><a href="#otherCharacters">{lang}rp.character.otherCharacters{/lang}</a></li>
                        {/if}
                    
                        {event name='previewTabMenu'}
                    {/content}
                </ul>
            </nav>

            {if $character->getOtherCharacters()|count}
                <div id="otherCharacters" class="tabMenuContent otherCharacters">
                    <ul class="inlineList commaSeparated">
                        {foreach from=$character->getOtherCharacters() item=character}
                            <li class="box24">
                                {character object=$character type='avatar24' ariaHidden='true' tabindex='-1'}
                                <h3>{character object=$character class='characterName'}</h3>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
            
            {event name='previewTabContent'}
        <div>
    {/hascontent}
{/if}