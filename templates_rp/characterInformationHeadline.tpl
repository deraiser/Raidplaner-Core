<div class="containerHeadline">
	<h3>
        {character object=$character class='characterName'}
        {if $character->isPrimary}<span class="badge green">{lang}rp.character.primary{/lang}</span>{/if}
        {if RP_ENABLE_RANK && $character->getRank()}<span class="badge characterRankBadge">{$character->getRank()->getTitle()}</span>{/if}
	</h3>
</div>

<ul class="inlineList commaSeparated">
    {if $__wcf->getSession()->getPermission('user.rp.canViewCharacterProfile')}
		{if !$character->guildName|empty}<li>{$character->guildName}</li>{/if}
    {/if}
    <li>{lang}rp.character.charactersList.created{/lang}</li>
    
    {event name='characterData'}
</ul>