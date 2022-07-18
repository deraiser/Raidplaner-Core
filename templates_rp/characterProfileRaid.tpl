<script data-relocate="true">
	require(['Daries/RP/Ui/Raid/Profile/Loader', 'Language'], function(UiRaidProfileLoader, Language) {
		Language.addObject({
			'rp.character.raid.noMoreEntries': '{jslang}rp.character.raid.noMoreEntries{/jslang}',
			'rp.character.raid.more': '{jslang}rp.character.raid.more{/jslang}'
		});
		
		new UiRaidProfileLoader({@$characterID});
	});
</script>

<ul id="raidList" class="containerList tripleColumned raidList" data-last-raid-time="{@$lastRaidTime}">
    {include file='characterProfileRaidItem' application='rp'}
</ul>