<section class="section sectionContainerList">
    <ol class="contentItemList">
        {foreach from=$pointAccounts key=pointAccountID item=pointAccount}
            {assign var='__pointAccountID' value=$pointAccount->pointAccountID}
            <li class="contentItem contentItemMultiColumn">
                <dl class="plain dataList contentItemContent characterPointAccountList">
                    <dt>{lang}rp.character.point.account.title{/lang}</dt>
                    <dd>{$pointAccount->getTitle()}</dd>
                    <dt>{lang}rp.character.point.account.received{/lang}</dt>
                    <dd{if $characterPoints[$__pointAccountID][received][color]} class="{$characterPoints[$__pointAccountID][received][color]}"{/if}>
                        {$characterPoints[$__pointAccountID][received][points]}
                    </dd>
                    <dt>{lang}rp.character.point.account.issued{/lang}</dt>
                    <dd{if $characterPoints[$__pointAccountID][issued][color]} class="{$characterPoints[$__pointAccountID][issued][color]}"{/if}>
                        {$characterPoints[$__pointAccountID][issued][points]}
                    </dd>
                    <dt>{lang}rp.character.point.account.adjustments{/lang}</dt>
                    <dd{if $characterPoints[$__pointAccountID][adjustments][color]} class="{$characterPoints[$__pointAccountID][adjustments][color]}"{/if}>
                        {$characterPoints[$__pointAccountID][adjustments][points]}
                    </dd>
                    <dt>{lang}rp.character.point.account.current{/lang}</dt>
                    <dd{if $characterPoints[$__pointAccountID][current][color]} class="{$characterPoints[$__pointAccountID][current][color]}"{/if}>
                        {$characterPoints[$__pointAccountID][current][points]}
                    </dd>
                    <dt>{lang}rp.character.point.account.raid30{/lang}</dt>
                    <dd class="{$characterStats[$__pointAccountID][raid30][color]}">
                        {#$characterStats[$__pointAccountID][raid30][percent]}% ({#$characterStats[$__pointAccountID][raid30][is]}/{#$characterStats[$__pointAccountID][raid30][max]})
                    </dd>
                    <dt>{lang}rp.character.point.account.raid60{/lang}</dt>
                    <dd class="{$characterStats[$__pointAccountID][raid60][color]}">
                        {#$characterStats[$__pointAccountID][raid60][percent]}% ({#$characterStats[$__pointAccountID][raid60][is]}/{#$characterStats[$__pointAccountID][raid60][max]})
                    </dd>
                    <dt>{lang}rp.character.point.account.raid90{/lang}</dt>
                    <dd class="{$characterStats[$__pointAccountID][raid90][color]}">
                        {#$characterStats[$__pointAccountID][raid90][percent]}% ({#$characterStats[$__pointAccountID][raid90][is]}/{#$characterStats[$__pointAccountID][raid90][max]})
                    </dd>
                    <dt>{lang}rp.character.point.account.raidAll{/lang}</dt>
                    <dd class="{$characterStats[$__pointAccountID][raidAll][color]}">
                        {#$characterStats[$__pointAccountID][raidAll][percent]}% ({#$characterStats[$__pointAccountID][raidAll][is]}/{#$characterStats[$__pointAccountID][raidAll][max]})
                    </dd>
                </dl>
            </li>
        {/foreach}
    </ol>
</section>