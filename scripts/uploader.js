jQuery(document).ready(function(){



	// File uploader
	var file_frame;
	jQuery('#apple-touch-startup-image-upload_button').on('click', function( event ){
 
		event.preventDefault();
	 
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: 'Select an Image',
		  button: {
			text: 'Use this Image',
		  },
		  multiple: false  // only allow the one file to be selected
		});
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
		  // We set multiple to false so only get one image from the uploader
		  attachment = file_frame.state().get('selection').first().toJSON();
		  jQuery('#apple-touch-startup-image').val(attachment.id);
 		  jQuery('#apple-touch-startup-image-url').val(attachment.url);
		});
		// Finally, open the modal
		file_frame.open();
	});


	
});