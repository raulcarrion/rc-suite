(function( $ ) {
	'use strict';

    function swapObject(elemento)
    {
        // Oculta todos los seleccionables
        if (elemento.hasClass("seleccionador-flip"))
            $(".destino-seleccionable[data-grupo=" + elemento.attr("data-grupo")+"]").slideUp();
        else if (elemento.hasClass("seleccionador-fundido"))
            $(".destino-seleccionable[data-grupo=" + elemento.attr("data-grupo")+"]").fadeOut(700);
        else if (elemento.hasClass("seleccionador-entrando"))
            $(".destino-seleccionable[data-grupo=" + elemento.attr("data-grupo")+"]").hide(500);
        else
            $(".destino-seleccionable[data-grupo=" + elemento.attr("data-grupo")+"]").hide();

        // Quita la clase seleccionado a todos los selectores
        $(".seleccionador.seleccionado[data-grupo=" + elemento.attr("data-grupo")+"]").removeClass("seleccionado");

        //Selecciona el selector en el que hemos clickeado
        elemento.addClass("seleccionado");
            
        // Segun el efecto, muestra el destino
        if (elemento.hasClass("seleccionador-flip"))
            $("#" + elemento.data("destino")).slideDown();
        else if(elemento.hasClass("seleccionador-fundido"))
            $("#" + elemento.data("destino")).fadeIn(700);
        else if(elemento.hasClass("seleccionador-entrando"))
            $("#" + elemento.data("destino")).show(500);
        else
            $("#" + elemento.data("destino")).show();
    }
	
	$( document ).ready(function() 
    {
        $(".destino-seleccionable").hide();

        $(".seleccionador.seleccionado").each(function()
        {
            $("#" + $(this).attr("data-destino")).show();
        });
        
        //
        //  Estas funciones se encargan de gestionar la visualizacion de los destinos con respecto
        //  al origen, por medio del click o el hover
        //

		$(".seleccionador").on("click", function(e){
            e.preventDefault();

            if (!$(this).hasClass("seleccionado"))
                swapObject($(this));
            });
          
          $(".seleccionador.seleccionador-hovering").on("mouseover", function(e){
            e.preventDefault();
            if (!$(this).hasClass("seleccionado"))
                swapObject($(this));
            });
	});	

})( jQuery );

