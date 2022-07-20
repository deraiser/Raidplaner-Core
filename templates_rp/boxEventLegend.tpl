<dl class="floated">
    <dt></dt>
    <dd style="text-align: center;">
        {foreach from=$boxLegendList item=legend}
            <label><div style="background: {@$legend->bgColor}; padding: 10px;"><code>{$legend->getTitle()}</code></div></label>
        {/foreach}
    </dd>
</dl>