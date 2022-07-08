{if $event->getController()->getObjectTypeName() == 'info.daries.rp.event.raid' && $event->leaders}
    {hascontent}
        {capture append='sidebarRight'}
            <section class="box" data-static-box-identifier="info.daries.rp.event.raid.leaders">
                <h2 class="boxTitle">{lang}rp.event.raid.leader{if $event->getController()->getLeaders()|count > 1}s{/if}{/lang}</h2>

                <div class="boxContent">
                    <ul class="sidebarItemList">
                        {content}
                            {foreach from=$event->getController()->getLeaders() item=leader}
                                <li class="box24">
                                    {character object=$leader type='avatar24' ariaHidden='true' tabindex='-1'}

                                    <div class="sidebarItemTitle">
                                        <h3>{character object=$leader}</h3>
                                    </div>
                                </li>
                            {/foreach}
                        {/content}
                    </ul>
                </div>
            </section>
        {/capture}
    {/hascontent}
{/if}
