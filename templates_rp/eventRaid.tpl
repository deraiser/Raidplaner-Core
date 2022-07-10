{if $event->isCanceled}
    <p class="error">{lang}rp.event.raid.canceled{/lang}</p>
{else if $__wcf->user->userID && !$characters|count}
    <p class="error">{lang}rp.event.raid.attendee.noCharacters{/lang}</p>
{/if}

<div class="jsClipboardContainer eventRaidContainer" data-type="info.daries.rp.raid.attendee">
    {foreach from=$raidStatus key=__status item=__statusName}
        <section class="section">
            <h2 class="sectionTitle">{$__statusName}</h2>

            <div class="contentItemList">
                {if $event->distributionMode === 'none'}
                    {include file='eventRaidItems' application='rp' __availableDistributionID='0' __title='rp.event.raid.participants'|language}
                {else}
                    {foreach from=$availableDistributions item=availableDistribution}
                        {include file='eventRaidItems' application='rp' __availableDistributionID=$availableDistribution->getObjectID() __title=$availableDistribution->getTitle()}
                    {/foreach}
                {/if}
            </div>
        </section>
    {/foreach}
</div>

{if !$event->isCanceled}
    {if $event->canEdit()}
        <script data-relocate="true">
            require(['WoltLabSuite/Core/Controller/Clipboard', 'Daries/RP/Ui/Event/Raid/DragAndDrop'], 
            function(ControllerClipboard, UiEventRaidDragAndDrop) {
                ControllerClipboard.setup({
                    hasMarkedItems: {if $hasMarkedItems}true{else}false{/if},
                    pageClassName: 'rp\\page\\EventPage'
                });

                UiEventRaidDragAndDrop.init();
            });
        </script>
    {/if}

    {if $__wcf->user->userID}
        <script data-relocate="true">
            require(['WoltLabSuite/Core/Language', 'Daries/RP/Ui/Event/Raid/InlineEditor'], 
            function(Language, EventRaidInlineEditor) {
                Language.addObject({
                    'rp.event.raid.updateStatus': '{jslang}rp.event.raid.updateStatus{/jslang}',
                });

                new EventRaidInlineEditor({
                    canEdit: {if $event->canEdit()}true{else}false{/if},
                });
            });
        </script>
    {/if}
{/if}