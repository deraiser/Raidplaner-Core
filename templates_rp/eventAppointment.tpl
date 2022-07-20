<section class="section sectionContainerList jsEventAccepted">
    <header class="sectionHeader">
        <h2 class="sectionTitle">{lang}rp.event.accepted{/lang}</h2>
    </header>

    {hascontent}
        <ol class="contentItemList eventAppointment">
            {content}
                {foreach from=$accepted item=user}
                    {include file='userListItem' application='rp'}
                {/foreach}
            {/content}
        </ol>
    {hascontentelse}
        <p class="info">{lang}wcf.global.noItems{/lang}</p>
    {/hascontent}
</section>

<section class="section sectionContainerList jsEventMaybe">
    <header class="sectionHeader">
        <h2 class="sectionTitle">{lang}rp.event.maybe{/lang}</h2>
    </header>

    {hascontent}
        <ol class="contentItemList eventAppointment">
            {content}
                {foreach from=$maybe item=user}
                    {include file='userListItem' application='rp'}
                {/foreach}
            {/content}
        </ol>
    {hascontentelse}
        <p class="info">{lang}wcf.global.noItems{/lang}</p>
    {/hascontent}
</section>

<section class="section sectionContainerList jsEventCanceled">
    <header class="sectionHeader">
        <h2 class="sectionTitle">{lang}rp.event.canceled{/lang}</h2>
    </header>

    {hascontent}
        <ol class="contentItemList eventAppointment">
            {content}
                {foreach from=$canceled item=user}
                    {include file='userListItem' application='rp'}
                {/foreach}
            {/content}
        </ol>
    {hascontentelse}
        <p class="info">{lang}wcf.global.noItems{/lang}</p>
    {/hascontent}
</section>