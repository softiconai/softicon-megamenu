// Appearance → Mega Menus page
( function () {
	// a panel was just created: open it in the Site Editor, as the notice's link would
	var open = document.querySelector( '.almm-open' );
	if ( open ) {
		window.location.replace( open.href );
	}

	// video guides: play in a popup from youtube-nocookie.com, loaded only after a click; without JavaScript the card opens YouTube
	var dialog = document.querySelector( '.almm-video-dialog' );
	if ( ! dialog || ! dialog.showModal ) {
		return;
	}
	var frame = dialog.querySelector( '.almm-video-frame' );
	document.querySelectorAll( '.almm-video[data-video]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			var iframe = document.createElement( 'iframe' );
			iframe.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent( link.dataset.video ) + '?autoplay=1&rel=0';
			iframe.title = link.dataset.title;
			iframe.allow = 'autoplay; encrypted-media; fullscreen; picture-in-picture';
			iframe.allowFullscreen = true;
			frame.replaceChildren( iframe );
			dialog.showModal();
		} );
	} );
	dialog.querySelector( '.almm-video-close' ).addEventListener( 'click', function () {
		dialog.close();
	} );
	dialog.addEventListener( 'click', function ( event ) {
		if ( event.target === dialog ) {
			dialog.close();
		}
	} );
	dialog.addEventListener( 'close', function () {
		frame.replaceChildren();
	} );
}() );
