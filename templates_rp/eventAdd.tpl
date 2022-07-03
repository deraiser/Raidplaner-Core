{if $action == 'edit'}
	{capture assign='contentHeaderNavigation'}
		<li><a href="{link controller='EventAdd' application='rp' presetEventID=$formObject->eventID}{/link}" class="button"><span class="icon icon16 fa-files-o"></span> <span>{lang}rp.event.useAsPreset{/lang}</span></a></li>
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