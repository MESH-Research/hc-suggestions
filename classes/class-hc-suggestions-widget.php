<?php
/**
 * Widget.
 *
 * @package HC_Suggestions
 */

/**
 * Widget.
 */
class HC_Suggestions_Widget extends WP_Widget {

	/**
	 * Name of the taxonomy from which to get user's selected terms
	 */
	const TAXONOMY = 'mla_academic_interests';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			'HC_Suggestions_Widget',
			'HC Suggestions Widget',
			[
				'classname' => 'HC_Suggestions_Widget',
				'description' => 'Suggest content to members based on selected terms.',
			]
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		$tab_id_prefix = 'hc-suggestions-tab-';

		$user_terms = wpmn_get_object_terms(
			get_current_user_id(),
			self::TAXONOMY,
			[
				'fields' => 'names',
			]
		);

		/**
		 * Identifier => label.
		 *
		 * Would be nice to pull labels from an authoritative source rather than hardcode,
		 * but that doesn't exist for fake post types anyway.
		 */
		$post_types = [
			EP_BP_API::MEMBER_TYPE_NAME => 'Members',
			EP_BP_API::GROUP_TYPE_NAME => 'Groups',
			'humcore_deposit' => 'Deposits',
		];

		echo '<div class="hc-suggestions-widget">'; // main widget container.
		echo '<ul>'; // open tabs.

		// tabs.
		foreach ( $post_types as $identifier => $label ) {
			printf(
				'<li><a href="#%s">%s</a></li>',
				esc_attr( $tab_id_prefix . $identifier ),
				$label
			);
		}

		echo '</ul>'; // close tabs.

		// results containers.
		foreach ( $post_types as $identifier => $label ) {
			printf(
				'<div id="%s" data-hc-suggestions-query="%s" data-hc-suggestions-type="%s"></div>',
				esc_attr( $tab_id_prefix . $identifier ),
				implode( ' OR ', $user_terms ),
				// $search_query = 'alkjsdlkfjlskdjflksjdfkljdsf'; // @todo test no results logic
				$identifier
			);
		}

		// embed inline for performance.
		echo '<style>' . file_get_contents( trailingslashit( __DIR__ ) . '../public/css/hc-suggestions.css' ) . '</style>';
		echo '<script>' . file_get_contents( trailingslashit( __DIR__ ) . '../public/js/hc-suggestions.js' ) . '</script>';
		echo '<script>jQuery( hc_suggestions.init )</script>';
		echo '</div>'; // close class="hc-suggestions-widget".
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {
		echo __METHOD__;
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return [];
	}

}
