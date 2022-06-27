{include file='__textFormField'}

<script data-relocate="true">
	require(['Daries/RP/Ui/Character/Search/Input'], (UiCharacterSearchInput) => {
		new UiCharacterSearchInput(document.getElementById('{@$field->getPrefixedId()}'));
	});
</script>