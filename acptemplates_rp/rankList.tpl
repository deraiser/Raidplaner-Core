{include file='header' pageTitle='rp.acp.rank.list'}

{if $objects|count}
    <script data-relocate="true">
        require(['Ajax', 'WoltLabSuite/Core/Ui/Sortable/List'], function (Ajax, UiSortableList) {
            new UiSortableList({
                containerId: 'rankList',
                className: 'rp\\data\\rank\\RankAction',
                offset: {@$startIndex},
                additionalParameters: {
                    gameID: {@RP_DEFAULT_GAME_ID}
                }
            });
        });
    </script>
{/if}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.acp.rank.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='RankAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.acp.rank.add{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{hascontent}
    <div class="paginationTop">
        {content}{pages print=true assign=pagesLinks controller="RankList" application='rp' link="pageNo=%d"}{/content}
    </div>
{/hascontent}

{if $objects|count}
    <div id="rankList" class="sortableListContainer section">
        <ol class="sortableList jsReloadPageWhenEmpty jsObjectActionContainer" data-object-action-class-name="rp\data\rank\RankAction" data-object-id="0" start="{@($pageNo - 1) * $itemsPerPage + 1}">
            {foreach from=$objects item=rank}
                <li class="sortableNode sortableNoNesting rankRow jsObjectActionObject" data-object-id="{@$rank->rankID}">
                    <span class="sortableNodeLabel">
                        <a href="{link controller='RankEdit' application='rp' id=$rank->rankID}{/link}">{$rank->getTitle()}</a> {if $rank->isDefault} ({lang}wcf.global.defaultValue{/lang}){/if}

                        <span class="statusDisplay sortableButtonContainer">
                            <span class="icon icon16 fa-arrows sortableNodeHandle"></span>
                            {if !$rank->isDefault}
                                <span 
                                    class="icon icon16 fa-square-o jsObjectAction pointer" 
                                    data-object-action="setAsDefault" 
                                    data-object-action-success="reload">
                                </span>
                            {else}
                                <span class="icon icon16 fa-check-square-o disabled"></span>
                            {/if}
                            <a href="{link controller='RankEdit' application='rp' id=$rank->rankID}{/link}"><span title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip icon icon16 fa-pencil"></span></a>
                            {if !$rank->isDefault}
                                {objectAction action="delete" objectTitle=$rank->getTitle()}
                            {else}
                                <span class="icon icon16 fa-times disabled" title="{lang}wcf.global.button.delete{/lang}"></span>
                            {/if}

                            {event name='itemButtons'}
                        </span>
                    </span>
                    <ol class="sortableList" data-object-id="{@$rank->rankID}"></ol>
                </li>
            {/foreach}
        </ol>
    </div>

    <div class="formSubmit">
        <button class="button buttonPrimary" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
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