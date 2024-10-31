jQuery(document).ready(function () {
	jQuery('#mho_wpsubpages_redirect').click(function() { jQuery('#r2s_container').toggle(); });
	jQuery('input[name="r2s_type"]').click(function() { 
		jQuery('.r2s_subcontainer').hide();
		jQuery( '#' + jQuery('input[name=r2s_type]:checked').val() ).show();
	});
	if ( jQuery('input[name=r2s_type]:checked').val() )  jQuery( '#' + jQuery('input[name=r2s_type]:checked').val() ).show();
	if ( jQuery('#mho_wpsubpages_redirect:checked').val() )  jQuery('#r2s_container').show();
});