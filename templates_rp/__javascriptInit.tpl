require(['Daries/RP/BootstrapFrontend'], function(BootstrapFrontend) {
    BootstrapFrontend.setup({
        enableCharacterPopover: {if $__wcf->getSession()->getPermission('user.rp.canViewCharacterProfile')}true{else}false{/if},
        enableEventPopover: {if $__wcf->getSession()->getPermission('user.rp.canReadEvent')}true{else}false{/if},
    });
});
