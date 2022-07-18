{foreach from=$items item=item}
    <li class="contentItem contentItemMultiColumn">
        <div class="contentItemContent">
            <div class="box64">
                {item object=$item[item] type='icon64'}

                <div class="details itemInformation">
                    <div class="containerHeadline">
                        <h3><a>{item object=$item[item]}</a></h3>
                    </div>

                    <dl class="plain dataList containerContent small">
                        {if $item[raid]}
                            <dt>{lang}rp.raid.date{/lang}</dt>
                            <dd>{$item[raid]->date|date}</dd>
                            <dt>{lang}rp.raid.raid{/lang}</dt>
                            <dd>{$item[raid]->getTitle()}</dd>
                        {/if}
                        {if $item[pointAccount]}
                            <dt>{lang}rp.character.point.account{/lang}</dt>
                            <dd>{$item[pointAccount]->getTitle()}</dd>
                        {/if}
                        <dt>{lang}rp.character.points{/lang}</dt>
                        <dd class="red">{$item[points]}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </li>
{/foreach}