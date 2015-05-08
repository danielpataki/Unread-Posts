(function( $ ) {

    function up_set_dropdowns() {
        $('.widget-liquid-right .unread-posts .post-type-select, form.unread-posts .post-type-select').SumoSelect({
            placeholder: up.post_type_select_placeholder,
            selectAll: true
        });
    }

    up_set_dropdowns();

    $( document ).ajaxComplete(function( event, xhr, settings ) {
        if( settings.data.indexOf( 'unread-posts' ) > -1 && settings.data.indexOf( 'action=widgets-order' ) === -1 ) {
            up_set_dropdowns();
        }
    })

    $( document ).on('change', 'input[name="up_below_posts_settings[post_type_type]"]', function() {
        if( $(this).val() == 'selected' ) {
            $(this).parents('td:first').find( '.post-type-type-selector' ).show();
        }
        else {
            $(this).parents('td:first').find( '.post-type-type-selector' ).hide();
        }
    })



})( jQuery );
