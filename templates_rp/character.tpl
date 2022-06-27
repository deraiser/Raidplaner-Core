{capture assign='pageTitle'}{$character->characterName} - {lang}rp.character.characters{/lang}{/capture}

{capture assign='headContent'}
    {event name='javascriptInclude'}
    
    <script data-relocate="true">
        require(['Daries/RP/Ui/Character/Profile/TabMenu'], function(CharacterProfileTabMenu) {
            new CharacterProfileTabMenu(document.getElementById('profileContent'), {@$characterID});
        });
    </script>
{/capture}

{capture assign='contentHeader'}
    <header class="contentHeader characterProfileCharacter" data-object-id="{@$character->characterID}">
		<div class="contentHeaderIcon">
			{if $character->userID == $__wcf->user->userID}
				<a href="{link controller='CharacterEdit' application='rp' id=$character->characterID}{/link}" class="jsTooltip" title="{lang}rp.character.edit{/lang}">{@$character->getAvatar()->getImageTag(128)}</a>
			{else}
				<span>{@$character->getAvatar()->getImageTag(128)}</span>
			{/if}
		</div>
        <div class="contentHeaderTitle">
            <h1 class="contentTitle">
                <span class="characterProfileUsername">{$character->characterName}</span>
                {if RP_ENABLE_RANK}
                    <span class="badge rankTitleBadge">{$character->rankName}</span>
                {/if}
				
				{event name='afterContentTitle'}
            </h1>
            
            <div class="contentHeaderDescription">
                <ul class="inlineList commaSeparated">
                    {if $__wcf->getSession()->getPermission('user.rp.canViewCharacterProfile')}
                        {if !$character->guildName|empty}<li>{$character->guildName}</li>{/if}
                    {/if}
                    <li>{lang}rp.character.charactersList.created{/lang}</li>

                    {event name='characterHeaderDescription'}
                </ul>
                
                <dl class="plain inlineDataList">
					{include file='characterInformationStatistics' application='rp'}
					
					{if $character->profileHits}
						<dt>{lang}rp.character.profileHits{/lang}</dt>
						<dd>{#$character->profileHits}</dd>
					{/if}
				</dl>
            </div>
        </div>

        {hascontent}
			<nav class="contentHeaderNavigation">
				<ul class="userProfileButtonContainer">
					{content}
						{if $character->userID == $__wcf->user->userID}
                            <li><a href="{link controller='CharacterEdit' application='rp' object=$character}{/link}" class="button"><span class="icon icon16 fa-pencil"></span> <span>{lang}rp.character.edit{/lang}</span></a></li>
                        {/if}
						
						{event name='contentHeaderNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}
    </header>
{/capture}

{include file='header'}

<div id="profileContent" class="section tabMenuContainer characterProfileContent" data-active="{$__rp->getCharacterProfileMenu()->getActiveMenuItem($characterID)->getIdentifier()}">
    <nav class="tabMenu">
        <ul>
            {foreach from=$__rp->getCharacterProfileMenu()->getMenuItems() item=menuItem}
                {if $menuItem->getContentManager()->isVisible($characterID)}
                    <li><a href="#{$menuItem->getIdentifier()|rawurlencode}">{$menuItem}</a></li>
                {/if}
            {/foreach}
        </ul>
    </nav>

    {foreach from=$__rp->getCharacterProfileMenu()->getMenuItems() item=menuItem}
        {if $menuItem->getContentManager()->isVisible($characterID)}
            <div id="{$menuItem->getIdentifier()}" class="tabMenuContent" data-menu-item="{$menuItem->menuItem}">
                {if $menuItem === $__rp->getCharacterProfileMenu()->getActiveMenuItem($characterID)}
                    {@$profileContent}
                {/if}
            </div>
        {/if}
    {/foreach}
</div>

{include file='footer'}
