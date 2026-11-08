<?php
/**
 * Plugin Name:       Top and Bottom Popup - Resenje
 * Plugin URI:        https://github.com/stefanzeljkic/Top-and-Bottom-Popup-Resenje
 * Description:       Shows two independent popups (top and bottom of the page) with your own HTML content and separate style settings.
 * Version:           1.1.0
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            Stefan Zeljkic
 * Author URI:        https://github.com/stefanzeljkic
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       top-and-bottom-popup-resenje
 *
 * @package TopAndBottomPopupResenje
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'POPUP_RESENJE_VERSION', '1.1.0' );
define( 'POPUP_RESENJE_OPTION', 'popup_resenje_settings' );

/**
 * Popup positions handled by the plugin.
 *
 * @return string[]
 */
function popup_resenje_positions() {
	return array( 'top', 'bottom' );
}

/**
 * Default settings for one popup.
 *
 * @return array
 */
function popup_resenje_popup_defaults() {
	return array(
		'enable'   => false,
		'content'  => '',
		'position' => 'center',
		'interval' => 30,
		'width'    => '300px',
		'height'   => 'auto',
		'bg'       => '#ffffff',
		'color'    => '#000000',
		'padding'  => '20px',
		'radius'   => '8px',
	);
}

/**
 * Default settings for all popups.
 *
 * @return array
 */
function popup_resenje_defaults() {
	$defaults = array();
	foreach ( popup_resenje_positions() as $pos ) {
		$defaults[ $pos ] = popup_resenje_popup_defaults();
	}
	return $defaults;
}

/**
 * Validates a CSS size made of 1 to $max_parts values (e.g. "300px", "80%", "10px 20px").
 *
 * Anything that does not match is replaced by the default, so nothing else can reach the CSS.
 *
 * @param mixed  $value      Raw value.
 * @param string $fallback   Value used when $value is invalid.
 * @param bool   $allow_auto Whether "auto" is accepted.
 * @param int    $max_parts  Maximum number of space separated values.
 * @return string
 */
function popup_resenje_sanitize_css_size( $value, $fallback, $allow_auto = false, $max_parts = 1 ) {
	$value = strtolower( trim( sanitize_text_field( (string) $value ) ) );
	if ( $allow_auto && 'auto' === $value ) {
		return 'auto';
	}
	$parts = preg_split( '/\s+/', $value );
	if ( '' === $value || count( $parts ) > $max_parts ) {
		return $fallback;
	}
	foreach ( $parts as $part ) {
		if ( '0' !== $part && ! preg_match( '/^\d{1,4}(\.\d{1,2})?(px|%|em|rem|vh|vw)$/', $part ) ) {
			return $fallback;
		}
	}
	return implode( ' ', $parts );
}

/**
 * Sanitizes the settings of one popup.
 *
 * @param mixed $input Raw settings of one popup.
 * @return array
 */
function popup_resenje_sanitize_popup( $input ) {
	$defaults = popup_resenje_popup_defaults();
	$input    = is_array( $input ) ? $input : array();

	$position = isset( $input['position'] ) ? sanitize_key( $input['position'] ) : $defaults['position'];
	if ( ! in_array( $position, array( 'left', 'center', 'right' ), true ) ) {
		$position = $defaults['position'];
	}

	$bg    = isset( $input['bg'] ) ? sanitize_hex_color( $input['bg'] ) : '';
	$color = isset( $input['color'] ) ? sanitize_hex_color( $input['color'] ) : '';

	return array(
		'enable'   => ! empty( $input['enable'] ),
		'content'  => isset( $input['content'] ) ? wp_kses_post( $input['content'] ) : '',
		'position' => $position,
		'interval' => isset( $input['interval'] ) ? min( absint( $input['interval'] ), 31536000 ) : $defaults['interval'],
		'width'    => popup_resenje_sanitize_css_size( isset( $input['width'] ) ? $input['width'] : '', $defaults['width'] ),
		'height'   => popup_resenje_sanitize_css_size( isset( $input['height'] ) ? $input['height'] : '', $defaults['height'], true ),
		'bg'       => $bg ? $bg : $defaults['bg'],
		'color'    => $color ? $color : $defaults['color'],
		'padding'  => popup_resenje_sanitize_css_size( isset( $input['padding'] ) ? $input['padding'] : '', $defaults['padding'], false, 4 ),
		'radius'   => popup_resenje_sanitize_css_size( isset( $input['radius'] ) ? $input['radius'] : '', $defaults['radius'], false, 4 ),
	);
}

/**
 * Sanitize callback for the whole settings option (used by register_setting()).
 *
 * @param mixed $input Raw option value.
 * @return array
 */
function popup_resenje_sanitize_settings( $input ) {
	$input  = is_array( $input ) ? $input : array();
	$output = array();
	foreach ( popup_resenje_positions() as $pos ) {
		$output[ $pos ] = popup_resenje_sanitize_popup( isset( $input[ $pos ] ) ? $input[ $pos ] : array() );
	}
	return $output;
}

/**
 * Returns the stored settings, always sanitized and complete.
 *
 * @return array
 */
function popup_resenje_get_settings() {
	$stored = get_option( POPUP_RESENJE_OPTION, null );
	if ( null === $stored ) {
		$stored = popup_resenje_legacy_settings();
	}
	return popup_resenje_sanitize_settings( $stored );
}

/**
 * Reads settings saved by version 1.0 (one option per field), if present.
 *
 * @return array
 */
function popup_resenje_legacy_settings() {
	$legacy = array();
	foreach ( popup_resenje_positions() as $pos ) {
		if ( false === get_option( "popup_resenje_enable_{$pos}" ) ) {
			continue;
		}
		$legacy[ $pos ] = array();
		foreach ( array_keys( popup_resenje_popup_defaults() ) as $field ) {
			$value = get_option( "popup_resenje_{$field}_{$pos}" );
			if ( false !== $value ) {
				$legacy[ $pos ][ $field ] = 'content' === $field ? wp_unslash( $value ) : $value;
			}
		}
	}
	return $legacy;
}

/**
 * Registers the settings with a sanitize callback.
 */
function popup_resenje_register_settings() {
	register_setting(
		'popup_resenje_group',
		POPUP_RESENJE_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'popup_resenje_sanitize_settings',
			'default'           => popup_resenje_defaults(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'popup_resenje_register_settings' );

/**
 * Adds the settings page to the admin menu.
 */
function popup_resenje_admin_menu() {
	add_menu_page(
		__( 'Popup Resenje', 'top-and-bottom-popup-resenje' ),
		__( 'Popup Resenje', 'top-and-bottom-popup-resenje' ),
		'manage_options',
		'popup-resenje',
		'popup_resenje_admin_page',
		'dashicons-format-status'
	);
}
add_action( 'admin_menu', 'popup_resenje_admin_menu' );

/**
 * Adds a "Settings" link on the Plugins screen.
 *
 * @param array $links Existing action links.
 * @return array
 */
function popup_resenje_action_links( $links ) {
	$url = admin_url( 'admin.php?page=popup-resenje' );
	array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'top-and-bottom-popup-resenje' ) . '</a>' );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'popup_resenje_action_links' );

/**
 * Renders the settings page.
 */
function popup_resenje_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = popup_resenje_get_settings();
	$labels   = array(
		'top'    => __( 'Top Popup', 'top-and-bottom-popup-resenje' ),
		'bottom' => __( 'Bottom Popup', 'top-and-bottom-popup-resenje' ),
	);
	$aligns   = array(
		'left'   => __( 'Left', 'top-and-bottom-popup-resenje' ),
		'center' => __( 'Center', 'top-and-bottom-popup-resenje' ),
		'right'  => __( 'Right', 'top-and-bottom-popup-resenje' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Popup Settings', 'top-and-bottom-popup-resenje' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'popup_resenje_group' ); ?>
			<?php
			foreach ( $labels as $pos => $label ) :
				$s    = $settings[ $pos ];
				$name = POPUP_RESENJE_OPTION . '[' . $pos . ']';
				$id   = 'popup-resenje-' . $pos;
				?>
				<h2><?php echo esc_html( $label ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable', 'top-and-bottom-popup-resenje' ); ?></th>
						<td><label><input type="checkbox" name="<?php echo esc_attr( $name . '[enable]' ); ?>" value="1" <?php checked( $s['enable'] ); ?>> <?php esc_html_e( 'Show this popup', 'top-and-bottom-popup-resenje' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id . '-content' ); ?>"><?php esc_html_e( 'Content', 'top-and-bottom-popup-resenje' ); ?></label></th>
						<td>
							<textarea id="<?php echo esc_attr( $id . '-content' ); ?>" name="<?php echo esc_attr( $name . '[content]' ); ?>" rows="6" class="large-text code"><?php echo esc_textarea( $s['content'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'HTML allowed in posts. Style elements with inline style attributes, e.g. <p style="font-size:18px">.', 'top-and-bottom-popup-resenje' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id . '-position' ); ?>"><?php esc_html_e( 'Alignment', 'top-and-bottom-popup-resenje' ); ?></label></th>
						<td>
							<select id="<?php echo esc_attr( $id . '-position' ); ?>" name="<?php echo esc_attr( $name . '[position]' ); ?>">
								<?php foreach ( $aligns as $value => $text ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $s['position'], $value ); ?>><?php echo esc_html( $text ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id . '-interval' ); ?>"><?php esc_html_e( 'Show again after (seconds)', 'top-and-bottom-popup-resenje' ); ?></label></th>
						<td>
							<input type="number" id="<?php echo esc_attr( $id . '-interval' ); ?>" name="<?php echo esc_attr( $name . '[interval]' ); ?>" value="<?php echo esc_attr( $s['interval'] ); ?>" min="0" max="31536000">
							<p class="description"><?php esc_html_e( 'How long the popup stays hidden after a visitor closes it. 0 = show on every page load.', 'top-and-bottom-popup-resenje' ); ?></p>
						</td>
					</tr>
					<?php
					$text_fields = array(
						'width'   => __( 'Width (e.g. 300px or 80%)', 'top-and-bottom-popup-resenje' ),
						'height'  => __( 'Height (e.g. auto or 200px)', 'top-and-bottom-popup-resenje' ),
						'padding' => __( 'Padding (e.g. 20px or 10px 20px)', 'top-and-bottom-popup-resenje' ),
						'radius'  => __( 'Border radius (e.g. 8px)', 'top-and-bottom-popup-resenje' ),
					);
					foreach ( $text_fields as $field => $field_label ) :
						?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $id . '-' . $field ); ?>"><?php echo esc_html( $field_label ); ?></label></th>
							<td><input type="text" id="<?php echo esc_attr( $id . '-' . $field ); ?>" name="<?php echo esc_attr( $name . '[' . $field . ']' ); ?>" value="<?php echo esc_attr( $s[ $field ] ); ?>" class="regular-text"></td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id . '-bg' ); ?>"><?php esc_html_e( 'Background color', 'top-and-bottom-popup-resenje' ); ?></label></th>
						<td><input type="color" id="<?php echo esc_attr( $id . '-bg' ); ?>" name="<?php echo esc_attr( $name . '[bg]' ); ?>" value="<?php echo esc_attr( $s['bg'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id . '-color' ); ?>"><?php esc_html_e( 'Text and close button color', 'top-and-bottom-popup-resenje' ); ?></label></th>
						<td><input type="color" id="<?php echo esc_attr( $id . '-color' ); ?>" name="<?php echo esc_attr( $name . '[color]' ); ?>" value="<?php echo esc_attr( $s['color'] ); ?>"></td>
					</tr>
				</table>
				<hr>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>

		<h2><?php esc_html_e( 'Support Development', 'top-and-bottom-popup-resenje' ); ?></h2>
		<p><?php esc_html_e( 'If you find this plugin useful, please consider supporting future updates and development:', 'top-and-bottom-popup-resenje' ); ?></p>
		<p>
			<a href="<?php echo esc_url( 'https://www.patreon.com/user?u=76138587' ); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Donate via Patreon', 'top-and-bottom-popup-resenje' ); ?></a>
			<a href="<?php echo esc_url( 'https://buymeacoffee.com/stefanzeljkic' ); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Buy Me a Coffee', 'top-and-bottom-popup-resenje' ); ?></a>
			<a href="<?php echo esc_url( 'https://ko-fi.com/stefanzeljkic' ); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Support on Ko-fi', 'top-and-bottom-popup-resenje' ); ?></a>
		</p>
	</div>
	<?php
}

/**
 * Returns the enabled popups.
 *
 * @return array
 */
function popup_resenje_enabled_popups() {
	$enabled = array();
	foreach ( popup_resenje_get_settings() as $pos => $popup ) {
		if ( $popup['enable'] ) {
			$enabled[ $pos ] = $popup;
		}
	}
	return $enabled;
}

/**
 * Enqueues the front-end CSS and JS (only when at least one popup is enabled).
 */
function popup_resenje_enqueue_assets() {
	$popups = popup_resenje_enabled_popups();
	if ( empty( $popups ) ) {
		return;
	}

	wp_enqueue_style(
		'popup-resenje',
		plugins_url( 'assets/css/popup.css', __FILE__ ),
		array(),
		POPUP_RESENJE_VERSION
	);

	$css    = '';
	$config = array();
	foreach ( $popups as $pos => $popup ) {
		// Values are validated again right before they are written into CSS.
		$popup = popup_resenje_sanitize_popup( $popup );
		$css  .= sprintf(
			'#popup-resenje-%1$s{width:%2$s;height:%3$s;background:%4$s;color:%5$s;padding:%6$s;border-radius:%7$s;}',
			sanitize_key( $pos ),
			$popup['width'],
			$popup['height'],
			$popup['bg'],
			$popup['color'],
			$popup['padding'],
			$popup['radius']
		);

		$config[] = array(
			'id'       => 'popup-resenje-' . sanitize_key( $pos ),
			'interval' => (int) $popup['interval'],
		);
	}
	wp_add_inline_style( 'popup-resenje', $css );

	wp_enqueue_script(
		'popup-resenje',
		plugins_url( 'assets/js/popup.js', __FILE__ ),
		array(),
		POPUP_RESENJE_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_add_inline_script( 'popup-resenje', 'window.popupResenjeConfig = ' . wp_json_encode( $config ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'popup_resenje_enqueue_assets' );

/**
 * Prints the popup markup in the footer (hidden until the script shows it).
 */
function popup_resenje_render_popups() {
	foreach ( popup_resenje_enabled_popups() as $pos => $popup ) {
		$classes = 'popup-resenje popup-resenje--' . $pos . ' popup-resenje--' . $popup['position'];
		?>
		<div id="<?php echo esc_attr( 'popup-resenje-' . $pos ); ?>" class="<?php echo esc_attr( $classes ); ?>" role="dialog" aria-label="<?php echo esc_attr( 'top' === $pos ? __( 'Top popup', 'top-and-bottom-popup-resenje' ) : __( 'Bottom popup', 'top-and-bottom-popup-resenje' ) ); ?>" hidden>
			<button type="button" class="popup-resenje__close" aria-label="<?php esc_attr_e( 'Close', 'top-and-bottom-popup-resenje' ); ?>">&times;</button>
			<div class="popup-resenje__content"><?php echo wp_kses_post( $popup['content'] ); ?></div>
		</div>
		<?php
	}
}
add_action( 'wp_footer', 'popup_resenje_render_popups' );
