( function( api ) {

	// Extends our custom "wp-shop-woocommerce" section.
	api.sectionConstructor['wp-shop-woocommerce'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );