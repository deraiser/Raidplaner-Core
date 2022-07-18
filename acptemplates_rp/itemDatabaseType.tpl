{if $option->issortable}
	<script data-relocate="true">
		document.addEventListener('DOMContentLoaded', function() {
			require(['Dom/Traverse', 'Dom/Util', 'Ui/TabMenu', 'WoltLabSuite/Core/Ui/Sortable/List'], function (DomTraverse, DomUtil, UiTabMenu, UiSortableList) {
				var sortableList = elById('{$option->optionName}SortableList');
				var tabMenu = UiTabMenu.getTabMenu(DomUtil.identify(DomTraverse.parentByClass(sortableList, 'tabMenuContainer')));
				var activeTab = tabMenu.getActiveTab();
				
				// select the tab the sortable list is in as jQuery's sortable requires
				// the sortable list to be visible
				tabMenu.select(elData(DomTraverse.parentByClass(sortableList, 'tabMenuContent'), 'name'));
				
				new UiSortableList({
					containerId: '{$option->optionName}SortableList',
					options: {
						toleranceElement: ''
					}
				});
				
				// re-select the previously selected tab
				tabMenu.select(null, activeTab);
			});
		});
	</script>
	
	<div id="{$option->optionName}SortableList" class="sortableListContainer">
		<ol class="sortableList">
			{foreach from=$availableDatabases item=availableDatabase}
				<li class="sortableNode">
					<span class="sortableNodeLabel">
						<span class="icon icon16 fa-arrows sortableNodeHandle"></span> <label><input type="checkbox" name="values[{$option->optionName}][]" value="{$availableDatabase}"{if $availableDatabase|in_array:$value} checked{/if}> {lang}rp.acp.item.database.{$availableDatabase}{/lang}</label>
					</span>
				</li>
			{/foreach}
		</ol>
	</div>
{else}
	{foreach from=$availableDatabases item=availableDatabase}
		<label><input type="checkbox" name="values[{$option->optionName}][]" value="{$availableDatabase}"{if $availableDatabase|in_array:$value} checked{/if}> {lang}rp.acp.item.database.{$availableDatabase}{/lang}</label>
	{/foreach}
{/if}
