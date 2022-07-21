{capture assign='pageTitle'}{$raid->getTitle()} - {lang}rp.raid.raids{/lang}{/capture}

{capture assign='contentHeader'}
    <header class="contentHeader rpRaid">
        <div class="contentHeaderIcon">
            <a href="{link controller='RaidEdit' application='rp' id=$raidID}{/link}" class="jsTooltip" title="{lang}rp.raid.edit{/lang}">{@$raid->getIcon(64)}</a>
        </div>
        <div class="contentHeaderTitle">
            <h1 class="contentTitle">
                <span class="rpRaidTitle">{$raid->getTitle()}</span>
            </h1>

            <div class="contentHeaderDescription">
                <ul class="inlineList commaSeparated">
                    <li>{lang}rp.raid.created{/lang} {$raid->addedBy}</li>
                    <li>{lang}rp.raid.points{/lang}: {@$raid->points}</li>
                    {if $raid->notes}<li>{lang}rp.raid.notes{/lang}: {$raid->notes}</li>{/if}
                </ul>
            </div>
        </div>
                
        {hascontent}
            <nav class="contentHeaderNavigation">
                <ul>
                    {content}
                        {if $__wcf->getSession()->getPermission('mod.rp.canEditRaid')}
                            <li><a href="{link controller='RaidEdit' application='rp' id=$raidID}{/link}" class="button"><span class="icon icon16 fa-pencil"></span> <span>{lang}rp.raid.edit{/lang}</span></a></li>
                        {/if}

                        {event name='contentHeaderNavigation'}
                    {/content}
                </ul>
            </nav>
        {/hascontent}
    </header>
{/capture}

{include file='header'}

<section class="section sectionContainerList rpRaidClassDistribution">
    <ol class="contentItemList">
        <li class="contentItem contentItemMultiColumn"><div id="raidClassDistribution" style="height: 400px"></div></li>
        <li class="contentItem contentItemMultiColumn">
            <ol class="contentItemList">
                {foreach from=$classDistributions item=classDistribution}
                    <li class="contentItem contentItemMultiColumn rpRaidClassAttendees">
                        <div class="contentItemContent">
                            <div id="class{@$classDistribution[object]->classificationID}" class="box48">
                                {@$classDistribution[object]->getIcon(48)}
                                    
                                <div class="details">
                                    <div class="containerHeadline">
                                        <h3>
                                            {implode from=$classDistribution[attendees] item=attendee}
                                                {$attendee->characterName}
                                            {/implode}
                                        </h3>
                                    </div>

                                    <div class="progressBarContainer">
                                        <span>{@$classDistribution[percent]}%</span>
                                        <div class="progressBar" style="width: {@$classDistribution[percent]}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                {/foreach}
            </ol>
        </li>
    </ol>
</section>

{if !$items|empty}
    <section class="section sectionContainerList rpRaidItems">
        <h2 class="sectionTitle">{lang}rp.raid.items{/lang}</h2>

        <ol class="containerList itemList tripleColumned">
            {foreach from=$items item=__item}
                <li>
                    <div class="box64">
                        {item object=$__item[item] type='icon64'}

                        <div class="details">
                            <div class="containerHeadline">
                                <h3>{item object=$__item[item]}</h3>
                            </div>
                            <ul class="inlineList commaSeparated">
                                <li>{lang}rp.raid.item.buyer{/lang}: {$__item[character]->getTitle()}</li>
                                <li>{lang}rp.raid.item.pointAccount{/lang}: {$__item[pointAccount]->getTitle()}</li>
                                <li>{lang}rp.raid.item.points{/lang}: {$__item[points]}</li>
                            </ul>
                        </div>
                    </div>
                </li>
            {/foreach}
        </ol>
    </section>
{/if}

{include file='footer'}

<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/flot/jquery.flot.js"></script>
<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/flot/jquery.flot.pie.js"></script>
<script data-relocate="true">
    $(function() {
        var data = [
            {implode from=$classDistributions item=classDistribution}
                {
                    label: '{@$classDistribution[object]->getTitle()|encodeJS} ({@$classDistribution[count]} - {@$classDistribution[percent]}%)',
                    data: {@$classDistribution[count]}
                }
            {/implode}
        ];

        var raidClassDistribution = $("#raidClassDistribution");
        $.plot(raidClassDistribution, data, {
            series: {
                pie: {
                    innerRadius: 0.5,
                    show: true
                }
            }
        });
    });
</script>