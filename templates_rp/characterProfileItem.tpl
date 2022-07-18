<script data-relocate="true">
	require(['Daries/RP/Ui/Item/Profile/Loader', 'Language'], function(UiItemProfileLoader, Language) {
		Language.addObject({
			'rp.character.item.noMoreEntries': '{jslang}rp.character.item.noMoreEntries{/jslang}',
			'rp.character.item.more': '{jslang}rp.character.item.more{/jslang}'
		});
		
		new UiItemProfileLoader({@$characterID});
	});
</script>

<section class="section sectionContainerList">
    <ol id="itemList" class="contentItemList itemList" data-last-item-offset="{@$lastItemOffset}">
        {include file='characterProfileItemItem' application='rp'}
    </ol>
</section>