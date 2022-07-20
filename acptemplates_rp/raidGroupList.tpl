{include file='header' pageTitle='rp.acp.raid.group.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}rp.acp.raid.group.list{/lang} <span class="badge badgeInverse">{#$items}</span></h1>
	</div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='RaidGroupAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.acp.raid.group.add{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{hascontent}
	<div class="paginationTop">
		{content}{pages print=true assign=pagesLinks controller="RaidGroupList" application="rp" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}{/content}
	</div>
{/hascontent}

{if $objects|count}
    <div class="section tabularBox">
        <table class="table jsObjectActionContainer" data-object-action-class-name="rp\data\raid\group\RaidGroupAction">
            <thead>
                <tr>
                    <th class="columnID columnGroupID{if $sortField == 'groupID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='RaidGroupList' application='rp'}pageNo={@$pageNo}&sortField=groupID&sortOrder={if $sortField == 'groupID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                    <th class="columnTitle columnGroupName{if $sortField == 'groupNameI18n'} active {@$sortOrder}{/if}"><a href="{link controller='RaidGroupList' application='rp'}pageNo={@$pageNo}&sortField=groupNameI18n&sortOrder={if $sortField == 'groupNameI18n' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.name{/lang}</a></th>
                    <th class="columnDigits columnMembers{if $sortField == 'members'} active {@$sortOrder}{/if}"><a href="{link controller='RaidGroupList' application='rp'}pageNo={@$pageNo}&sortField=members&sortOrder={if $sortField == 'members' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}rp.acp.raid.group.members{/lang}</a></th>

                    {event name='columnHeads'}
                </tr>
            </thead>
            <tbody class="jsReloadPageWhenEmpty">
                {foreach from=$objects item=group}
                    <tr id="groupContainer{@$group->groupID}" class="jsRaidGroupRow jsObjectActionObject" data-object-id="{@$group->getObjectID()}">
                        <td class="columnIcon">
                            <a href="{link controller='RaidGroupEdit' application='rp' id=$group->groupID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
                            {objectAction action="delete" objectTitle=$group->getTitle()}

                            {event name='rowButtons'}
                        </td>
                        <td class="columnID columnGroupID">{@$group->groupID}</td>
                        <td class="columnTitle columnGroupName">
                            <a title="{lang}rp.acp.raid.group.edit{/lang}" href="{link controller='RaidGroupEdit' application='rp' id=$group->groupID}{/link}">{$group->getTitle()}</a>
                        </td>
                        <td class="columnDigits columnMembers">
                            <a class="jsTooltip" title="{lang}rp.acp.raid.group.showMembers{/lang}" href="{link controller='CharacterSearch' application='rp'}raidGroupID={@$group->groupID}{/link}">{#$group->members}</a>
                        </td>

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

        <nav class="contentFooterNavigation">
            <ul>
                <li><a href="{link controller='RaidGroupAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.acp.raid.group.add{/lang}</span></a></li>

                {event name='contentFooterNavigation'}
            </ul>
        </nav>
    </footer>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}