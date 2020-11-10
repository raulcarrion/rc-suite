(function( $ ) {
	'use strict';

	// Cuando el documento se ha cargado completamente
	$(document).ready(function($) {

		// Actualizamos la info del fihcero 
		if ($('#rcsu-choose-file').val() != "")
		{
			var data = {
				'action' : 'rcru-get-replacer-file-det',
				'rcsu-file'   : $("#rcsu-choose-file").val(),
				'nonce'  : rc_suite_vars.ajax_nonce
			};

			jQuery.post(ajaxurl, data, function(response) {

				$('#file-msg').html(response);
				// Scroll arriba
				//window.scrollTo(0, 0);
			});
		}

		/* 
			Cambio en el desplegable de fichero 
		*/
		$('#rcsu-choose-file').on("change", function() {

			// Pedimos la info del fichero
			var data = {
				'action' : 'rcru-get-replacer-file-det',
				'rcsu-file'   : $("#rcsu-choose-file").val(),
				'nonce'  : rc_suite_vars.ajax_nonce
			};

			jQuery.post(ajaxurl, data, function(response) {

				$('#file-msg').html(response);
				$('#file-sub-msg').html("");
				$('#rcsu-test-file').prop("disabled",false);
				$('#rcsu-process-file').prop("disabled",false);
			});

		});

		/* 
			Boton TEST
		*/
		$('#rcsu-test-file').on("click", function() {

			// Pedimos la info del fichero
			var data = {
				'action' 	: 'rcru-replacer-test-file',
				'rcsu-file' : $("#rcsu-choose-file").val(),
				'nonce'  	: rc_suite_vars.ajax_nonce
			};

			jQuery.post(ajaxurl, data, function(response) {

				$('#file-sub-msg').html(response);
				$('#rcsu-test-file').prop("disabled",true);
			});

		});

		/* 
			Boton PROCESAR FICHERO REPLACER
		*/
		$('#rcsu-process-file').on("click", function() {

			// Pedimos la info del fichero
			var data = {
				'action' 	: 'rcru-replacer-process-file',
				'rcsu-file' : $("#rcsu-choose-file").val(),
				'nonce'  	: rc_suite_vars.ajax_nonce
			};

			jQuery.post(ajaxurl, data, function(response) {

				$('#file-sub-msg').html(response);
				$('#rcsu-process-file').prop("disabled",true);
			});

		});
	});


})( jQuery );
