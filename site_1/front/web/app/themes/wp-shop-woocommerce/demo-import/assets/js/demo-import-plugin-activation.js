jQuery(document).ready(function($) {
    'use strict';
    var this_obj = wp_shop_woocommerce_activate_plugin;

    $(document).on('click', '.theme-plugin-install', function(event) {
        
        event.preventDefault();
        var button = $(this);
        var slug = button.data('slug');
        button.text(this_obj.installing + '...').addClass('updating-message');
        wp.updates.installPlugin({
            slug: slug,
            success: function(data) {
                button.attr('href', data.activateUrl);
                button.text(this_obj.activating + '...');
                button.removeClass('button-secondary updating-message theme-plugin-install');
                button.addClass('button-primary theme-plugin-activate');
                button.trigger('click');
            },
            error: function(data) {
                jQuery('.theme-recommended-plugins .theme-activation-note').css('display','block');
                //console.log('error', data);
                button.removeClass('updating-message');
                button.text(this_obj.error);
            },
        });

    });

    

});
