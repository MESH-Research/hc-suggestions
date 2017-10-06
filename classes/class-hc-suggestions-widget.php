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
		$user_terms = wpmn_get_object_terms(
			get_current_user_id(),
			self::TAXONOMY,
			[
				'fields' => 'names',
			]
		);

		$tax_query = new WP_Tax_Query( [
			[
				'taxonomy' => self::TAXONOMY,
				'field'    => 'name',
				'operator' => 'OR',
				'terms'    => $user_terms,
			],
		] );

		$hcs_query = new WP_Query( [
			'ep_integrate' => true,
			'tax_query' => $tax_query,
		] );

		$results = $hcs_query->get_posts();

		echo '<pre>';
		var_dump( $hcs_query );
		var_dump( $results );
		echo '</pre>';
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

	/**
	 * Get terms by user id
	 *
	 * @uses Mla_Academic_Interests::
	 * @global $mla_academic_interests
	 *
	 * @param int $user_id Optional. User for whom to get terms. Default current user.
	 * @return array
	 */
	public function get_terms_for_user( int $user_id ) {
		global $mla_academic_interests;

		$terms = [];

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		return $terms;
	}

}
