(function( $ ) {

	"use strict";
	
	/* Media Uploader */
	$( document ).on( 'click', '.wprp-image-upload', function() {

		var imgfield, showfield, file_frame;
		imgfield	= jQuery(this).prev('input').attr('id');
		showfield	= jQuery(this).parents('td').find('.wprp-img-view');
		var button	= jQuery(this);

		/* If the media frame already exists, reopen it. */
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		/* Create the media frame. */
		file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'post',
			state: 'insert',
			title: button.data( 'uploader-title' ),
			button: {
				text: button.data( 'uploader-button-text' ),
			},
			multiple: false  /* Set to true to allow multiple files to be selected */
		});

		file_frame.on( 'menu:render:default', function(view) {
			/* Store our views in an object. */
			var views = {};

			/* Unset default menu items */
			view.unset('library-separator');
			view.unset('gallery');
			view.unset('featured-image');
			view.unset('embed');
			view.unset('playlist');
			view.unset('video-playlist');

			/* Initialize the views in our view object. */
			view.set(views);
		});

		/* When an image is selected, run a callback. */
		file_frame.on( 'insert', function() {

			/* Get selected size from media uploader */
			var selected_size = $('.attachment-display-settings .size').val();

			var selection = file_frame.state().get('selection');
			selection.each( function( attachment, index ) {
				attachment = attachment.toJSON();

				/* Selected attachment url from media uploader */
				var attachment_url = attachment.sizes[selected_size].url;

				if(index == 0){
					/* place first attachment in field */
					$('#'+imgfield).val(attachment_url);
					showfield.html('<img src="'+attachment_url+'" alt="" />');

				} else{
					$('#'+imgfield).val(attachment_url);
					showfield.html('<img src="'+attachment_url+'" alt="" />');
				}
			});
		});

		/* Finally, open the modal */
		file_frame.open();
	});

	/* Clear Media */
	$( document ).on( 'click', '.wprp-image-clear', function() {
		$(this).parent().find('.wprp-img-upload-input').val('');
		$(this).parent().find('.wprp-img-view').html('');
	});

})(jQuery);