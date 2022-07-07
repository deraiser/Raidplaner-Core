{include file='header' pageTitle='rp.acp.point.account.list'}

{if $objects|count}
    <script data-relocate="true">
        require(['WoltLabSuite/Core/Ui/Sortable/List'], function (UiSortableList) {
            new UiSortableList({
                containerId: 'pointAccountList',
                className: 'rp\\data\\point\\account\\PointAccountAction',
                offset: {@$startIndex}
            });
        });
    </script>
{/if}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.acp.point.account.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='PointAccountAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.acp.point.account.add{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{hascontent}
    <div class="paginationTop">
        {content}{pages print=true assign=pagesLinks controller="PointAccountList" application='rp' link="pageNo=%d"}{/content}
    </div>
{/hascontent}

{if $objects|count}
    <div id="pointAccountList" class="sortableListContainer section">
        <ol class="sortableList jsReloadPageWhenEmpty jsObjectActionContainer" data-object-action-class-name="rp\data\point\account\PointAccountAction" data-object-id="0" start="{@($pageNo - 1) * $itemsPerPage + 1}">
            {foreach from=$objects item=pointAccount}
                <li class="sortableNode sortableNoNesting pointAccountRow jsObjectActionObject" data-object-id="{@$pointAccount->pointAccountID}">
                    <span class="sortableNodeLabel">
                        <a href="{link controller='PointAccountEdit' application='rp' id=$pointAccount->pointAccountID}{/link}">{$pointAccount->getTitle()}</a>

                        <span class="statusDisplay sortableButtonContainer">
                            <span class="icon icon16 fa-arrows sortableNodeHandle"></span>
                            <a href="{link controller='PointAccountEdit' application='rp' id=$pointAccount->pointAccountID}{/link}"><span title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip icon icon16 fa-pencil"></span></a>
                            {objectAction action="delete" objectTitle=$pointAccount->getTitle()}

                            {event name='itemButtons'}
                        </span>
                    </span>
                    <ol class="sortableList" data-object-id="{@$pointAccount->pointAccountID}"></ol>
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