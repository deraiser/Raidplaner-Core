<div id="eventAddDialog" style="display: none">
    <div class="section">
        <dl>
            <dt>{lang}rp.event.type{/lang}</dt>
            <dd>
                {foreach from=$availableEventControllers item=availableEventController}
                    <label><input type="radio" name="objectTypeID" value="{@$availableEventController->objectTypeID}"{if RP_DEFAULT_EVENT_TYPE_SELECTED == $availableEventController->objectType} checked{/if}> {lang}rp.event.controller.{@$availableEventController->objectType}{/lang}</label>
                {/foreach}
            </dd>
        </dl>
        <div class="formSubmit">
            <button class="buttonPrimary">{lang}wcf.global.button.next{/lang}</button>
        </div>
    </div>
</div>
<script data-relocate="true">
    require(['Language', 'Daries/RP/Ui/Event/Add'], function(Language, EventAdd) {
        Language.addObject({
            'rp.event.add': '{jslang}rp.event.add{/jslang}'
        });

        EventAdd.init('{link controller='EventAdd' application='rp' encode=false}{literal}objectTypeID={$objectTypeID}{/literal}{/link}');

        {if $showEventAddDialog}
            window.setTimeout(function() {
                EventAdd.openDialog();
            }, 10);
        {/if}
    });
</script>