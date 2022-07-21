{capture assign='pageTitle'}{$__wcf->getActivePage()->getTitle()}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
{capture assign='contentTitle'}{$__wcf->getActivePage()->getTitle()} <span class="badge">{#$items}</span>{/capture}

{capture assign='canonicalURLParameters'}{if $letter}&letter={@$letter|rawurlencode}{/if}{/capture}

{capture assign='headContent'}
    {if $pageNo < $pages}
        <link rel="next" href="{link controller='PointList' application='rp'}pageNo={@$pageNo+1}{/link}">
    {/if}
    {if $pageNo > 1}
        <link rel="prev" href="{link controller='PointList' application='rp'}{if $pageNo > 2}pageNo={@$pageNo-1}{/if}{/link}">
    {/if}
    <link rel="canonical" href="{link controller='PointList' application='rp'}{if $pageNo > 1}pageNo={@$pageNo}{/if}{/link}">
{/capture}

{capture assign='sidebarRight'}
	{assign var=encodedLetter value=$letter|rawurlencode}
	<section class="jsOnly box">
        <h2 class="boxTitle">{lang}rp.character.characters.sort.letters{/lang}</h2>

        <div class="boxContent">
            <ul class="buttonList smallButtons letters">
                {foreach from=$letters item=__letter}
                    <li><a href="{link controller='PointList' application='rp'}letter={$__letter|rawurlencode}{/link}" class="button small{if $letter == $__letter} active{/if}">{$__letter}</a></li>
                {/foreach}
                {if !$letter|empty}<li class="lettersReset"><a href="{link controller='PointList' application='rp'}{/link}" class="button small">{lang}rp.character.characters.sort.letters.all{/lang}</a></li>{/if}
            </ul>
        </div>
	</section>
{/capture}

{capture assign='contentInteractionPagination'}
	{pages print=true assign='pagesLinks' controller='PointList' application='rp' link="pageNo=%d$canonicalURLParameters"}
{/capture}

{include file='header'}

{if $items}
    <section class="section sectionContainerList">
        <ol class="contentItemList">
            {foreach from=$objects item=character}
                <li class="contentItem contentItemMultiColumn" data-object-id="{@$character->characterID}">
                    <div class="contentItemContent">
                        <div class="box48">
                            {character object=$character type='avatar48' ariaHidden='true' tabindex='-1'}
                            
                            <div class="details characterInformation">
                                {include file='characterInformationHeadline' application='rp'}
                                
                                <dl class="plain dataList containerContent characterPointAccountList">
                                    {assign var='__characterPoints' value=$__rp->getCharacterPointHandler()->getPoints($character)}
                                    {foreach from=$pointAccounts item=pointAccount}
                                        {if $__characterPoints[$pointAccount->pointAccountID]|isset}
                                            <dt>{$pointAccount->getTitle()}</dt>
                                            <dd{if $__characterPoints[$pointAccount->pointAccountID]['current']['color']} class="{$__characterPoints[$pointAccount->pointAccountID]['current'][color]}"{/if}>
                                                {$__characterPoints[$pointAccount->pointAccountID]['current'][points]|characterPoint}
                                            </dd>
                                        {/if}
                                    {/foreach}
                                </dl>
                            </div>
                        </div>
                    </div>
                </li>
            {/foreach}
        </ol>
    </section>
{else}
    <p class="info" role="status">{lang}rp.point.account.noDatas{/lang}</p>
{/if}

<footer class="contentFooter">
	{hascontent}
		<div class="paginationBottom">
			{content}{@$pagesLinks}{/content}
		</div>
	{/hascontent}
	
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>
    
{include file='footer'}