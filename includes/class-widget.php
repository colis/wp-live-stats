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
 * This class is used to build the WP Live Stats component via a widget.
 *
 * @subpackage Shortcode
 */
class Widget extends \WP_Widget {

	/**
	 * Initialize the widget
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$plugin            = Plugin::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->version     = $plugin->get_plugin_version();

		$widget_ops = array(
			'description' => esc_html__( 'WP Live Stats widget.', 'wp-live-stats' ),
		);

		parent::__construct( 'wpls-widget', esc_html__( 'WP Live Stats', 'wp-live-stats' ), $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		wp_enqueue_script( $this->plugin_slug . '-widget-script', plugins_url( 'assets/js/widget.js', dirname( __FILE__ ) ), array( 'jquery' ), $this->version );
		wp_enqueue_style( $this->plugin_slug . '-widget-style', plugins_url( 'assets/css/widget.css', dirname( __FILE__ ) ), $this->version );

		$object_name = 'wpls_object_' . uniqid();

		$object = array(
			'title'     => $instance['title'],
			'api_nonce' => wp_create_nonce( 'wp_rest' ),
			'api_url'   => rest_url( $this->plugin_slug . '/v1/' ),
		);

		wp_localize_script( $this->plugin_slug . '-widget-script', $object_name, $object );

		echo $args['before_widget'];

		?><div class="wp-live-stats-widget" data-object-id="<?php echo esc_html( $object_name ); ?>"></div>
		<?php

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'wp-live-stats' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}
}