<div class="section">
    <ol class="containerList">
        {foreach from=$objects item=event}
            <li class="rpEventHeader">
                <div class="box48">
                    {user object=$event->getUserProfile() type='avatar48'}
                    
                    <div>
                        <div class="containerHeadline">
                            {anchor object=$event class='rpEventLink'}
                        </div>
                        <p>{@$event->getFormattedTimeFrame()}</p>
                        
                        <ul class="inlineList small dotSeparated">
                            <li>
                                {if $event->userID}
                                    {user object=$event->getUserProfile()}
                                {else}
                                    {$event->username}
                                {/if}
                            </li>
                            {if $event->canEdit()}
                                <li>
                                    <span>
                                        <a href="{link controller='EventEdit' application='rp' id=$event->eventID}{/link}" class="jsButtonEventInlineEditor">
                                            {lang}wcf.global.button.edit{/lang}
                                        </a>
                                    </span>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </div>
            </li>
        {/foreach}
    </ol>
</div>