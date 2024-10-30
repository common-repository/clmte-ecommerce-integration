(function( $ ) {
	'use strict';

	// ON DOCUMENT LOAD
	$(window).load(() => {
		///////////////////////////////////
		// EVENT LISTENERS
		///////////////////////////////////

		// Update offset price
		$("body").on("click", "#update-offset-price", (e) => {
			e.preventDefault();

			// Update offset price
			jQuery.ajax({
				method: "post",
				url: clmte.ajax_url,
				data: {
					action: "clmte_update_offset_price",
				},
				complete: () => {
					location.reload();
				},
			});
		});

		// Sync purchases
		$("body").on("click", "#clmte-sync-offsets", (e) => {
			e.preventDefault();

			// Update offset price
			jQuery.ajax({
				method: "post",
				url: clmte.ajax_url,
				data: {
					action: "clmte_trigger_sync_offsets",
				},
				complete: () => {
					location.reload();
				},
			});
		});
	});
	
})( jQuery );
