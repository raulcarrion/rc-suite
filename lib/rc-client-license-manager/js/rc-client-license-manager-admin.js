(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(document).ready(function($) {
		// Cuando tocamo el license key, activamos botones
        $("#"+rc_client_license_manager_translate.parent_plugin_slug+"_rcclm_license_key").on("change", function(){
			if ($("#"+rc_client_license_manager_translate.parent_plugin_slug+"_rcclm_license_key").val()) 
				$("#"+rc_client_license_manager_translate.parent_plugin_slug+"_client_license_manager_bt_activate").prop("disabled",false);
			else
				$("#"+rc_client_license_manager_translate.parent_plugin_slug+"_client_license_manager_bt_activate").prop("disabled",true);
			$("#"+rc_client_license_manager_translate.parent_plugin_slug+"_client_license_manager_bt_deactivate").prop("disabled",true);
		});

		
	});
})( jQuery );
