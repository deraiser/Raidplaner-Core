{if $event->getController()->getObjectTypeName() == 'info.daries.rp.event.raid'}
    {capture append='sidebarRight'}
        {hascontent}
            <section class="box" data-static-box-identifier="info.daries.rp.event.raid.required">
                <h2 class="boxTitle">{lang}rp.event.raid.required{/lang}</h2>

                <div class="boxContent">
                    <dl class="plain dataList">
                        {content}
                            {foreach from=$event->getController()->getRequireds() key=__key item=__value}
                                <dt>{lang}{$__key}{/lang}</dt>
                                <dd>{@$__value}</dd>
                            {/foreach}
                        {/content}
                    </dl>
                </div>
            </section>
        {/hascontent}

        {if $event->leaders}
            <section class="box" data-static-box-identifier="info.daries.rp.event.raid.leaders">
                <h2 class="boxTitle">{lang}rp.event.raid.leader{if $event->getController()->getLeaders()|count > 1}s{/if}{/lang}</h2>

                <div class="boxContent">
                    <ul class="sidebarItemList">
                        {foreach from=$event->getController()->getLeaders() item=leader}
                            <li class="box24">
                                {character object=$leader type='avatar24' ariaHidden='true' tabindex='-1'}

                                <div class="sidebarItemTitle">
                                    <h3>{character object=$leader}</h3>
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </section>
        {/if}
    {/capture}
{/if}
