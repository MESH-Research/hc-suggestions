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
		global $post; // need to access the permalink property to get correct links for buddypress fake post types

		$params = $data->get_query_params();

		/**
		 * $_REQUEST param names are hardcoded to be parsed by elasticpress-buddypress,
		 * (and possibly elsewhere) so the names must match here
		 */
		$hcs_query = new WP_Query( [
			'ep_integrate' => true,
			'post_type' => $params['post_type'],
			's' => $params['s'],
		] );

		$response_data = [];

		if ( $hcs_query->have_posts() ) {
			while ( $hcs_query->have_posts() ) {
				$hcs_query->the_post();

				ob_start();

				printf(
					'<a href="%s" title="%s">%s</a>',
					$post->permalink, // the_permalink() is wrong for fake post types, access directly instead
					the_title_attribute( 'echo=0' ),
					get_the_title()
				);

				$response_data[] = ob_get_clean();
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
}
