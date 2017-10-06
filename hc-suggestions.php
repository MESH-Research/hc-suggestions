<?php
/**
 * Plugin Name:     HC Suggestions
 * Plugin URI:      https://github.com/mlaa/hc-suggestions
 * Description:     Widget to suggest content to members based on selected terms.
 * Author:          MLA
 * Author URI:      https://github.com/mlaa
 * Text Domain:     hc-suggestions
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         HC_Suggestions
 */

/**
 * Main file.
 */

require_once trailingslashit( __DIR__ ) . 'classes/class-hc-suggestions-widget.php';

/**
 * Register widget.
 */
add_action( 'widgets_init', function() {
	register_widget( 'HC_Suggestions_Widget' );
} );
