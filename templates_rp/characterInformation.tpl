{include file='characterInformationHeadline' application='rp'}

{if !$disableCharacterInformationButtons|isset || $disableCharacterInformationButtons != true}{include file='characterInformationButtons' application='rp'}{/if}

<dl class="plain inlineDataList small">
	{include file='characterInformationStatistics' application='rp'}
</dl>
