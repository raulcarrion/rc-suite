(function( $ ) {
    function setup_collapsible_submenus() {
        // mobile menu
        $('#mobile_menu .menu-item-has-children > a').after('<span class="menu-closed"></span>');
        $('#mobile_menu .menu-item-has-children > a').each(function() {
            $(this).next().next('.sub-menu').toggleClass('hide',1000);
        });
        $('#mobile_menu .menu-item-has-children > a + span').on('click', function(event) {
            event.preventDefault();
            $(this).toggleClass('menu-open');
            $(this).next('.sub-menu').toggleClass('hide',1000);
        });
    }
      
    $(window).load(function() {
        setTimeout(function() {
            setup_collapsible_submenus();
        }, 700);
    });
})( jQuery );