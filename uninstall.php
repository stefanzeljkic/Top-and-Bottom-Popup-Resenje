<?php
/**
 * Removes the plugin settings when the plugin is deleted.
 *
 * @package TopAndBottomPopupResenje
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'popup_resenje_settings' );

// Options stored by version 1.0.
foreach ( array( 'top', 'bottom' ) as $popup_resenje_pos ) {
	foreach ( array( 'enable', 'content', 'position', 'interval', 'width', 'height', 'bg', 'color', 'padding', 'radius' ) as $popup_resenje_field ) {
		delete_option( "popup_resenje_{$popup_resenje_field}_{$popup_resenje_pos}" );
	}
}
