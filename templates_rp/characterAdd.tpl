{capture assign='contentTitle'}{lang}rp.character.{$action}{/lang}{/capture}
{if $action == 'edit'}
    {capture assign='contentDescription'}{$formObject->getTitle()}{/capture}
{/if}

{capture assign='contentHeaderNavigation'}
    <li>
        <a href="{link controller='CharactersList' application='rp'}{/link}" class="button">
            <span class="icon icon16 fa-list"></span> 
            <span>{lang}rp.character.list{/lang}</span>
        </a>
    </li>
{/capture}

{include file='header'}

{@$form->getHtml()}

{include file='footer'}