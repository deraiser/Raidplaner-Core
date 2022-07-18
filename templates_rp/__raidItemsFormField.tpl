<ol class="nativeList rpRaidItemList" id="{@$field->getPrefixedId()}_itemList"></ol>

{include file='__formFieldErrors'}

<div class="row rowColGap formGrid">
    <dl class="col-xs-12 col-md-4">
        <dt><label name="{@$field->getPrefixedId()}_itemName">{lang}rp.raid.item.itemName{/lang}</label></dt>
        <dd>
            <input type="text" id="{@$field->getPrefixedId()}_itemName" class="long">
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-2">
        <dt><label name="{@$field->getPrefixedId()}_pointAccount">{lang}rp.raid.item.pointAccount{/lang}</label></dt>
        <dd>
            <select id="{@$field->getPrefixedId()}_pointAccount">
                {foreach from=$field->getPointAccounts() item=__pointAccount}
                    <option {*
                        *}value="{$__pointAccount->pointAccountID}" {*
                        *}{if $field->getValue() == $__pointAccount->pointAccountID} selected{/if}{*
                    *}>{$__pointAccount->getTitle()}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-2">
        <dt><label name="{@$field->getPrefixedId()}_character">{lang}rp.raid.item.character{/lang}</label></dt>
        <dd>
            <select id="{@$field->getPrefixedId()}_character">
                {foreach from=$field->getCharacters() item=__character}
                    <option {*
                        *}value="{$__character->characterID}" {*
                        *}{if $field->getValue() == $__character->characterID} selected{/if}{*
                    *}>{$__character->getTitle()}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-2">
        <dt><label name="{@$field->getPrefixedId()}_points">{lang}rp.raid.item.points{/lang}</label></dt>
        <dd>
            <input type="text" id="{@$field->getPrefixedId()}_points" class="long">
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-1">
        <dt></dt>
        <dd>
            <a href="#" class="button small" id="{@$field->getPrefixedId()}_addButton">{lang}wcf.global.button.add{/lang}</a>
        </dd>
    </dl>
</div>

<script data-relocate="true">
    require(['Language', 'Daries/RP/Form/Builder/Field/Raid/Items'], function(Language, RaidItemsFormField) {
        Language.addObject({
            'rp.raid.item.form.field': '{jslang __literal=true}rp.raid.item.form.field{/jslang}',
            'rp.raid.item.points.error.forma': '{jslang}rp.raid.item.points.error.forma{/jslang}'
        });

         new RaidItemsFormField('{@$field->getPrefixedId()}', [
            {implode from=$field->getValue() item=item}
                {
                    characterID: '{$item[characterID]}',
                    characterName: '{@$item[characterName]|encodeJS}',
                    itemID: '{$item[itemID]}',
                    itemName: '{@$item[itemName]|encodeJS}',
                    pointAccountID: '{$item[pointAccountID]}',
                    pointAccountName: '{@$item[pointAccountName]|encodeJS}',
                    points: '{$item[points]}'
                }
            {/implode}
        ]);
    });
</script>