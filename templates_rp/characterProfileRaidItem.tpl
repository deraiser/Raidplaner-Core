{foreach from=$raidList item=raid}
    <li class="contentItem contentItemMultiColumn">
        <div class="box48">
            {@$raid->getIcon(64)}
            
            <div>
                <div class="containerHeadline">
                    <h3><a href="{$raid->getLink()}">{$raid->getTitle()}</a></h3>
                </div>
                
                <ul class="inlineList commaSeparated">
                    <li>{lang}rp.raid.date{/lang}: {$raid->date|date}</li>
                    <li>{lang}rp.raid.points{/lang}: {$raid->points}</li>
                </ul>
            </div>
        </div>
    </li>
{/foreach}