/**
 * Top and Bottom Popup - Resenje: shows each popup unless the visitor closed it within the configured interval.
 *
 * @package TopAndBottomPopupResenje
 */

( function () {
	'use strict';

	function storageGet( key ) {
		try {
			return window.localStorage.getItem( key );
		} catch ( e ) {
			return null;
		}
	}

	function storageSet( key, value ) {
		try {
			window.localStorage.setItem( key, value );
		} catch ( e ) {
			// Storage unavailable (private mode, blocked storage): the popup simply shows again next time.
		}
	}

	function setup( item ) {
		var popup = document.getElementById( item.id );
		if ( ! popup ) {
			return;
		}
		var key        = item.id + 'Closed';
		var lastClosed = parseInt( storageGet( key ) || '0', 10 ) || 0;
		var interval   = parseInt( item.interval, 10 ) || 0;

		if ( ( Date.now() - lastClosed ) / 1000 >= interval ) {
			popup.hidden = false;
		}

		var close = popup.querySelector( '.popup-resenje__close' );
		if ( close ) {
			close.addEventListener(
				'click',
				function () {
					popup.hidden = true;
					storageSet( key, String( Date.now() ) );
				}
			);
		}
	}

	function init() {
		var config = window.popupResenjeConfig || [];
		var count  = config.length;
		for ( var i = 0; i < count; i++ ) {
			setup( config[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
