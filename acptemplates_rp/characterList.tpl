{if $searchID}
	{assign var='pageTitle' value='rp.acp.character.search'}
{else}
	{assign var='pageTitle' value='rp.acp.character.list'}
{/if}

{include file='header'}

{event name='javascriptInclude'}
<script data-relocate="true">
    require(['Daries/RP/Acp/Ui/Character/Editor', 'WoltLabSuite/Core/Controller/Clipboard'], (AcpUiCharacterEditor, ControllerClipboard) => {
		ControllerClipboard.setup({
            hasMarkedItems: {if $hasMarkedItems}true{else}false{/if},
            pageClassName: 'rp\\acp\\page\\CharacterListPage'
		});
    
        new AcpUiCharacterEditor();
	});
    
    {event name='javascriptInit'}
</script>

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}{@$pageTitle}{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
    </div>

    {hascontent}
        <nav class="contentHeaderNavigation">
            <ul>
                {content}
                    {if $__wcf->session->getPermission('admin.rp.canSearchCharacter')}
                        <li class="dropdown">
                            <a class="button dropdownToggle"><span class="icon icon16 fa-search"></span> <span>{lang}rp.acp.character.quickSearch{/lang}</span></a>
                            <ul class="dropdownMenu">
								<li><a href="{link controller='CharacterQuickSearch' application='rp'}mode=disabled{/link}">{lang}rp.acp.character.quickSearch.disabled{/lang}</a></li>
                            </ul>
                        </li>
                    {/if}
                
                    {if $__wcf->session->getPermission('admin.rp.canAddCharacter')}
                        <li><a href="{link controller='CharacterAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.character.add{/lang}</span></a></li>
                    {/if}

                    {event name='contentHeaderNavigation'}
                {/content}
            </ul>
        </nav>
    {/hascontent}
</header>

{hascontent}
    <div class="paginationTop">
        {content}
            {assign var='linkParameters' value=''}

            {pages print=true assign=pagesLinks controller="CharacterList" application="rp" id=$searchID link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
        {/content}
    </div>
{/hascontent}

{if $objects|count}
    <div class="section tabularBox">
        <table data-type="info.daries.rp.character" class="table jsClipboardContainer jsObjectActionContainer" data-object-action-class-name="rp\data\character\CharacterAction">
            <thead>
                <tr>
                    <th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll"></label></th>
                    <th class="columnID columnCharacterID{if $sortField == 'characterID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='CharacterList' application='rp' id=$searchID}pageNo={@$pageNo}&sortField=characterID&sortOrder={if $sortField == 'characterID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                    <th class="columnText columnCharacterName{if $sortField == 'characterName'} active {@$sortOrder}{/if}"><a href="{link controller='CharacterList' application='rp' id=$searchID}pageNo={@$pageNo}&sortField=characterName&sortOrder={if $sortField == 'characterName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}rp.character.characterName{/lang}</a></th>
                    <th class="columnText columnUsername{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link controller='CharacterList' application='rp' id=$searchID}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}rp.acp.character.owner{/lang}</a></th>
                    <th class="columnDigits columnCreated{if $sortField == 'created'} active {@$sortOrder}{/if}"><a href="{link controller='CharacterList' application='rp' id=$searchID}pageNo={@$pageNo}&sortField=created&sortOrder={if $sortField == 'created' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}rp.character.created{/lang}</a></th>

                    {event name='columnHeads'}
                </tr>
            </thead>
            <tbody class="jsReloadPageWhenEmpty">
                {foreach from=$objects item=character}
                    <tr class="jsCharacterRow jsClipboardObject jsObjectActionObject" data-object-id="{@$character->getObjectID()}" data-enabled="{if !$character->isDisabled}true{else}false{/if}">
                        <td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$character->characterID}"></td>
                        <td class="columnIcon">
                            <div class="dropdown" id="characterListDropdown{@$character->characterID}">
                                <a href="#" class="dropdownToggle button small"><span class="icon icon16 fa-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a>
                                
                                <ul class="dropdownMenu">
                                    {event name='dropdownItems'}
                                    
                                    {if $character->canDelete()}
                                        <li class="dropdownDivider"></li>
                                        <li><a href="#" class="jsDelete" data-confirm-message="{lang __encode=true objectTitle=$character->characterName}wcf.button.delete.confirmMessage{/lang}">{lang}wcf.global.button.delete{/lang}</a></li>
                                    {/if}
                                    
                                    {if $character->canEdit()}
                                        <li class="dropdownDivider"></li>
                                        <li><a href="{link controller='CharacterEdit' application='rp' id=$character->characterID}{/link}" class="jsEditLink">{lang}wcf.global.button.edit{/lang}</a></li>
                                    {/if}
                                </ul>
                            </div>
                        </td>
                        <td class="columnID columnCharacterID">{@$character->characterID}</td>
                        <td class="columnText columnCharacterName">
                            <span class="characterName">
                                <a href="{link controller='CharacterEdit' application='rp' id=$character->characterID}{/link}">{$character->getTitle()}</a>
                            </span>
                            
                            {if !$character->isPrimary}
                                <span class="primaryCharacter">
                                    ({lang}rp.character.primary{/lang}: {$character->getPrimaryCharacter()->getTitle()})
                                </span>
                            {/if}
                            
                            <span class="characterStatusIcons">
                                {if $character->isDisabled}
                                    <span class="icon icon16 fa-power-off jsTooltip jsCharacterDisabled" title="{lang}rp.acp.character.isDisabled{/lang}"></span>
                                {/if}
                            </span>
                            
							{if RP_ENABLE_RANK}
								<span class="badge rankTitleBadge">{$character->rankName}</span>
							{/if}
                        </td>
                        <td class="columnText columnUsername">{$character->username}</td>
                        <td class="columnDate columnCreated">{@$character->created|time}</td>

                        {event name='columns'}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

    <footer class="contentFooter">
        {hascontent}
            <div class="paginationBottom">
                {content}{@$pagesLinks}{/content}
            </div>
        {/hascontent}

        {hascontent}
            <nav class="contentFooterNavigation">
                <ul>
                    {content}
                        {if $__wcf->session->getPermission('admin.rp.canAddCharacter')}
                            <li><a href="{link controller='CharacterAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.character.add{/lang}</span></a></li>
                        {/if}
                    
                        {event name='contentFooterNavigation'}
                    {/content}
                </ul>
            </nav>
        {/hascontent}
    </footer>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}