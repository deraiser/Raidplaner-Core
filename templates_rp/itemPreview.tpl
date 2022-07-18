{if $unknownItem|isset}
    <p>{lang}rp.item.unknownItem{/lang}</p>
{else}
    {@$template}
{/if}