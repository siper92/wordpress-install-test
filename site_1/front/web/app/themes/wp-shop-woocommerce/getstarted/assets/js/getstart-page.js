jQuery.noConflict()(function($){

	'use strict';

/* ===============================================
   Changelog
   =============================================== */

    $('.changelog_container .theme_version').last().css('border-bottom', 'none', 'border-top', 'none');
	
    $('.changelog_container .theme_version').click(function() {
        if ($(this).next('.changelog_details').css('display') === 'none') {
            $(this).addClass('inactive');
            $(this).children('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
        } else {
            $(this).removeClass('inactive');
            $(this).children('.dashicons').removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        }
        $(this).next('.changelog_details').stop(true, false).slideToggle('slow');
    });

});
