(function( $ ) {
	'use strict';
	
	$( document ).ready(function() {
		
		$("#banner-cerrar").on("click", function () {

			// Ocultamos el banner
			var data = {
				'action'      : 'ocultar-banner',
				'nonce'       : rc_suite_vars.ajax_nonce
			};
	
			jQuery.post(rc_suite_vars.ajax_url, data, function(response) {
				$(this).hide(500);
				console.log(response);
			});
		});

	});

	

})( jQuery );
