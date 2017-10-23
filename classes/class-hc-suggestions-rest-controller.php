<?php
/**
 * REST controller to query ElasticPress dynamically
 *
 * @package HC_Suggestions
 */

/**
 * Controller
 */
class HC_Suggestions_REST_Controller extends WP_REST_Controller {


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->namespace = 'hc-suggestions/v1';
		$this->rest_base = '/query';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, $this->rest_base, [
				'methods' => 'GET',
				'callback' => [ $this, 'query' ],
			]
		);
	}

	/**
	 * Query ElasticPress for relevant content
	 *
	 * @param WP_REST_Request $data request data. Expected to contain "s" & "post_type" params.
	 * @return WP_REST_Response
	 */
	public function query( WP_REST_Request $data ) {
		/**
		 * The global $current_user isn't populated here, have to do it ourselves.
		 * This won't work without shibd setting this header.
		 */
		wp_set_current_user( get_user_by( 'login', $data->get_header( 'employeenumber' ) ) );

		$params = $data->get_query_params();

		/**
		 * $_REQUEST param names are hardcoded to be parsed by elasticpress-buddypress,
		 * (and possibly elsewhere) so the names must match here
		 */
		$hcs_query_args = [
			'ep_integrate' => true,
			'post_type' => $params['post_type'],
			's' => $params['s'],
			'paged' => $params['paged'],
		];

		switch ( $params['post_type'] ) {
			case EP_BP_API::MEMBER_TYPE_NAME:
				// Exclude users already being followed by the current user.
				$hcs_query_args['post__not_in'] = bp_follow_get_following( [ 'user_id' => get_current_user_id() ] );
				break;
			case EP_BP_API::GROUP_TYPE_NAME:
				// Exclude groups already joined by the current user.
				$exclude_group_ids = array_keys( bp_get_user_groups(
					get_current_user_id(),
					[
						'is_admin' => null,
						'is_mod' => null,
					]
				) );

				// Exclude groups on society networks the current user does not belong to.
				$current_user_memberships = Humanities_Commons::hcommons_get_user_memberships();
				$non_member_society_groups = groups_get_groups( [
					'group_type__not_in' => $current_user_memberships['societies'],
					'per_page' => 999, // TODO This won't scale well.
				] );
				foreach ( $non_member_society_groups['groups'] as $group ) {
					$exclude_group_ids[] = $group->id;
				}

				// Exclude private groups.
				// TODO should do this here, but there's no 'status' param to groups_get_groups until bp 2.9.
				// For now, check in the loop below and just exclude there.

				$hcs_query_args['post__not_in'] = array_unique( $exclude_group_ids );
				break;
			case 'humcore_deposit':
				// Exclude deposits authored by the current user.
				$hcs_query_args['author__not_in'] = [ get_current_user_id() ];
				break;
			default:
				break;
		}

		$response_data = [];

		$hcs_query = new WP_Query( $hcs_query_args );

		if ( $hcs_query->have_posts() ) {
			while ( $hcs_query->have_posts() ) {
				$hcs_query->the_post();

				// TODO once BP is upgraded to 2.9, move this to the switch above.
				if ( EP_BP_API::GROUP_TYPE_NAME === $params['post_type'] ) {
					$group = groups_get_group( get_the_ID() );
					if ( 'public' !== $group->status ) {
						continue;
					}
				}

				$response_data[] = $this->_get_formatted_post();
			}

		}

		$response = new WP_REST_Response;

		$response->set_data(
			[
				'results' => $response_data,
			]
		);

		return $response;
	}

	/**
	 * Format a search result for output
	 *
	 * @global $post current post in the search results loop
	 * @return string formatted post markup
	 */
	public function _get_formatted_post() {
		global $post;

		ob_start();

		bp_get_template_part( 'suggestions/' . $post->post_type );

		return ob_get_clean();
	}
}
