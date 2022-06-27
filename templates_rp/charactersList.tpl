{capture assign='pageTitle'}{if $searchID}{lang}rp.character.search.results{/lang}{else}{$__wcf->getActivePage()->getTitle()}{/if}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
{capture assign='contentTitle'}{if $searchID}{lang}rp.character.search.results{/lang}{else}{$__wcf->getActivePage()->getTitle()}{/if} <span class="badge">{#$items}</span>{/capture}
{capture assign='canonicalURLParameters'}sortField={@$sortField}&sortOrder={@$sortOrder}{if $letter}&letter={@$letter|rawurlencode}{/if}{if $ownCharacters}&ownCharacters=1{/if}{/capture}

{capture assign='headContent'}
	{if $pageNo < $pages}
		<link rel="next" href="{link controller='CharactersList' application='rp'}pageNo={@$pageNo+1}&{@$canonicalURLParameters}{/link}">
	{/if}
	{if $pageNo > 1}
		<link rel="prev" href="{link controller='CharactersList' application='rp'}{if $pageNo > 2}pageNo={@$pageNo-1}&{/if}{@$canonicalURLParameters}{/link}">
	{/if}
	<link rel="canonical" href="{link controller='CharactersList' application='rp'}{if $pageNo > 1}pageNo={@$pageNo}&{/if}{@$canonicalURLParameters}{/link}">
{/capture}

{capture assign='contentHeaderNavigation'}
    {if $__wcf->getSession()->getPermission('user.rp.canAddCharacter')}
        <li><a href="{link controller='CharacterAdd' application='rp'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}rp.character.add{/lang}</span></a></li>
    {/if}
{/capture}

{capture assign='sidebarRight'}
	{assign var=encodedLetter value=$letter|rawurlencode}
	<section class="jsOnly box">
		<form method="post" action="{link controller='CharacterSearch' application='rp'}{/link}">
			<h2 class="boxTitle"><a href="{link controller='CharacterSearch' application='rp'}{/link}">{lang}rp.character.search{/lang}</a></h2>
			
			<div class="boxContent">
				<dl>
					<dt></dt>
					<dd>
						<input type="text" id="searchCharacterName" name="characterName" class="long" placeholder="{lang}rp.character.characterName{/lang}">
						{csrfToken}
					</dd>
				</dl>
			</div>
		</form>
	</section>
{/capture}

{capture assign='contentInteractionPagination'}
	{if $searchID}
			{pages print=true assign=pagesLinks controller='CharactersList' application='rp' id=$searchID link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&letter=$encodedLetter"}
		{else}
			{pages print=true assign=pagesLinks controller='CharactersList' application='rp' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&letter=$encodedLetter"}
		{/if}
{/capture}

{include file='header'}

{if $items}
    <div class="section sectionContainerList">
        <div class="containerListDisplayOptions">
            <div class="containerListSortOptions">
                <a rel="nofollow" href="{link controller='CharactersList' application='rp' id=$searchID}pageNo={@$pageNo}&sortField={$sortField}&sortOrder={if $sortOrder == 'ASC'}DESC{else}ASC{/if}{if $letter}&letter={$letter}{/if}{if $ownCharacters}&ownCharacters=1{/if}{/link}">
					<span class="icon icon16 fa-sort-amount-{$sortOrder|strtolower} jsTooltip" title="{lang}wcf.global.sorting{/lang} ({lang}wcf.global.sortOrder.{if $sortOrder === 'ASC'}ascending{else}descending{/if}{/lang})"></span>
				</a>
                <span class="dropdown">
                    <span class="dropdownToggle">{lang}rp.character.sortField.{$sortField}{/lang}</span>
                    
                    <ul class="dropdownMenu">
                        {foreach from=$validSortFields item=_sortField}
							<li{if $_sortField === $sortField} class="active"{/if}><a rel="nofollow" href="{link controller='CharactersList' application='rp' id=$searchID}pageNo={@$pageNo}&sortField={$_sortField}&sortOrder={if $sortField === $_sortField}{if $sortOrder === 'DESC'}ASC{else}DESC{/if}{else}{$sortOrder}{/if}{if $letter}&letter={$letter}{/if}{if $ownCharacters}&ownCharacters=1{/if}{/link}">{lang}rp.character.sortField.{$_sortField}{/lang}</a></li>
						{/foreach}
                    </ul>
                </span>
            </div>

            {hascontent}
                <div class="containerListActiveFilters">
                    <ul class="inlineList">
                        {content}
                            {if $letter}<li><span class="icon icon16 fa-bold jsTooltip" title="{lang}rp.character.characters.sort.letters{/lang}"></span> {$letter}</li>{/if}
                            {if $ownCharacters}<li><span class="icon icon16 fa-user jsTooltip" title="{lang}rp.character.characters.sort.ownCharacters{/lang}"></span></li>{/if}
                        {/content}
                    </ul>
                </div>
            {/hascontent}
            
            <div class="containerListFilterOptions jsOnly">
                <button class="small jsStaticDialog" data-dialog-id="charactersListSortFilter"><span class="icon icon16 fa-filter"></span> {lang}wcf.global.filter{/lang}</button>
            </div>
        </div>

        <ol class="containerList characterList">
            {foreach from=$objects item=character}
				{include file='characterListItem' application='rp'}
			{/foreach}
        </ol>
    </div>
	
	<div id="charactersListSortFilter" class="jsStaticDialogContent" data-title="{lang}rp.character.characters.filter{/lang}">
		<form method="post" action="{link controller='CharactersList' application='rp' id=$searchID}{/link}">
			<div class="section">
				<dl>
					<dt><label for="letter">{lang}rp.character.characters.sort.letters{/lang}</label></dt>
					<dd>
						<select name="letter" id="letter">
							<option value="">{lang}rp.character.characters.sort.letters.all{/lang}</option>
							{foreach from=$letters item=__letter}
								<option value="{$__letter}"{if $__letter == $letter} selected{/if}>{$__letter}</option>
							{/foreach}
						</select>
					</dd>
				</dl>
                <dl>
                    <dt></dt>
                    <dd>
                        <label><input name="ownCharacters" type="checkbox" value="1"{if $ownCharacters} checked{/if}> {lang}rp.character.characters.sort.ownCharacters{/lang}</label>
                    </dd>
                </dl>
			</div>
			
			<div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
				<a href="{link controller='CharactersList' application='rp'}{/link}" class="button">{lang}wcf.global.button.reset{/lang}</a>
				<input type="hidden" name="sortField" value="{$sortField}">
				<input type="hidden" name="sortOrder" value="{$sortOrder}">
			</div>
		</form>
	</div>
{else}
	<p class="info" role="status">{lang}rp.character.characters.noCharacters{/lang}</p>
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

<script data-relocate="true">
    require(['Daries/RP/Ui/Character/Search/Input'], (UiCharacterSearchInput) => {
		new UiCharacterSearchInput(document.getElementById('searchCharacterName'), {
			callbackSelect(item) {
				const link = '{link controller='Character' application='rp' id=2147483646 title='wcftitleplaceholder' encode=false}{/link}';
				window.location = link.replace('2147483646', item.dataset.objectId).replace('wcftitleplaceholder', item.dataset.label);
			}
		});
	});
</script>

{include file='footer'}