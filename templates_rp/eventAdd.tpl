{capture assign='contentTitle'}{lang}rp.event.{$action}{/lang}{/capture}
{if $action == 'edit'}
    {capture assign='contentDescription'}{$formObject->getTitle()}{/capture}
{/if}

{if $action == 'edit'}
    {capture assign='contentHeaderNavigation'}
        <li>
            <a href="{link controller='Event' application='rp' object=$formObject}{/link}" class="button">
                <span class="icon icon16 fa-arrow-circle-left"></span> 
                <span>{lang}rp.event.backEvent{/lang}</span>
            </a>
        </li>
            <li>
                <a href="{link controller='EventAdd' application='rp' presetEventID=$formObject->eventID}{/link}" class="button">
                    <span class="icon icon16 fa-files-o"></span> 
                    <span>{lang}rp.event.useAsPreset{/lang}</span>
                </a>
            </li>
    {/capture}
{/if}

{include file='header'}

{if $action == 'add'}
	{if !$__wcf->session->getPermission('user.rp.canCreateEventWithoutModeration')}
		<p class="info" role="status">{lang}rp.event.moderation.info{/lang}</p>
	{/if}
	
	{if $presetEventID}
		<p class="info" role="status">{lang}rp.event.preset{/lang}</p>
	{/if}
{/if}

{@$form->getHtml()}

{include file='footer'}