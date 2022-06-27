{hascontent}
    {content}
        {event name='about'}

        {if !$notes|empty}
            <section class="section">
                <h2 class="sectionTitle">{lang}rp.character.notes{/lang}</h2>
                <dl>
                    <dt></dt>
                    <dd>{@$notes}</dd>
                </dl>
            </section>
        {/if}
    {/content}
{hascontentelse}
    <div class="section">
		<p class="info" role="status">{lang}rp.character.profile.content.about.noData{/lang}</p>
	</div>
{/hascontent}