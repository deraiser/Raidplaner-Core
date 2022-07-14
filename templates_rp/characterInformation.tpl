{include file='characterInformationHeadline' application='rp'}

{if !$disableCharacterInformationButtons|isset || $disableCharacterInformationButtons != true}{include file='characterInformationButtons' application='rp'}{/if}

{capture assign='__contentInformation'}
    {event name='beforeInformations'}
    {if $contentInformation|isset}{@$contentInformation}{/if}
    {event name='afterInformations'}
{/capture}
{assign var='__contentInformation' value=$__contentInformation|trim}


{if $__contentInformation}
    <dl class="plain inlineDataList characterContentInformation">
        {@$__contentInformation}
    </dl>
{/if}

<dl class="plain inlineDataList small">
	{include file='characterInformationStatistics' application='rp'}
</dl>