<div class="section">
    <dl>
        <dt>{lang}rp.event.raid.updateStatus{/lang}</dt>
        <dd>
            <select name="status" id="status">
                {foreach from=$statusData key=key item=value}
                    <option value="{@$key}">{$value}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
</div>

<div class="formSubmit">
    <button data-type="submit">{lang}wcf.global.button.submit{/lang}</button>
</div>