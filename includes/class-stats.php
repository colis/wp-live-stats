<?php
/**
 * WP-Live-Stats
 *
 * @package   WP-Live-Stats
 * @author    Carmine Colicino
 * @license   GPL-3.0
 * @link      https://github.com/colis
 */

namespace Colicino\WPLS;

/**
 * This class defines the REST Controller for the "/stats" endpoint
 *
 * @subpackage REST_Controller
 */
class Stats {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		$plugin            = Plugin::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
	}

	/**
	 * Set up WordPress hooks and filters
	 *
	 * @return void
	 */
	public function do_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->do_hooks();
		}

		return self::$instance;
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = $this->plugin_slug . '/v' . $version;
		$endpoint  = '/stats/';

		register_rest_route(
			$namespace, $endpoint, array(
				array(
					'methods'  => \WP_REST_Server::READABLE,
					'callback' => array( $this, 'all_stats' ),
				),
			)
		);

	}

	/**
	 * The all_stats endpoint returns all the available statistics.
	 * The response includes the total number of posts, pages, users, categories, tags, comments, images.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function all_stats( $request ) {

		$data = array();

		if ( is_multisite() ) {
			$blogs = get_sites();

			foreach ( $blogs as $blog ) {
				$current_blog_id = $blog->blog_id;

				switch_to_blog( $current_blog_id );
				$data[ $current_blog_id ] = $this->build_response( $current_blog_id );

				restore_current_blog();
			}
		} else {
			$current_blog_id          = get_current_blog_id();
			$data[ $current_blog_id ] = $this->build_response( get_current_blog_id() );
		}

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	 * Build the response object.
	 *
	 * @param int|string $blog_id The current blog id.
	 */
	private function build_response( $blog_id ) {
		return array(
			'site_name'        => get_bloginfo( 'name' ),
			'total_posts'      => $this->total_posts(),
			'total_pages'      => $this->total_pages(),
			'total_users'      => $this->total_users(),
			'total_categories' => $this->total_categories(),
			'total_tags'       => $this->total_tags(),
			'total_comments'   => $this->total_comments(),
			'total_images'     => $this->total_images(),
		);
	}

	/**
	 * Return the total number of posts.
	 */
	private function total_posts() {
		$posts_count = wp_count_posts();

		return intval( $posts_count->publish );
	}

	/**
	 * Return the total number of pages.
	 */
	private function total_pages() {
		$pages_count = wp_count_posts( 'page' );

		return intval( $pages_count->publish );
	}

	/**
	 * Return the total number of users.
	 */
	private function total_users() {
		$users_count = count_users();

		return $users_count['total_users'];
	}

	/**
	 * Return the total number of categories.
	 */
	private function total_categories() {
		$categories = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => false,
			)
		);

		return count( $categories );
	}

	/**
	 * Return the total number of tags.
	 */
	private function total_tags() {
		$tags = get_terms(
			array(
				'taxonomy'   => 'post_tag',
				'hide_empty' => false,
			)
		);

		return count( $tags );
	}

	/**
	 * Return the total number of comments.
	 */
	private function total_comments() {
		$comments_count = wp_count_comments();

		return intval( $comments_count->approved );
	}

	/**
	 * Return the total number of images.
	 */
	private function total_images() {
		$total_attachments_per_type = (array) wp_count_attachments( 'image' );

		return array_sum( $total_attachments_per_type );
	}
}
