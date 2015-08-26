<?php

/**
 * This class is responsible for localizing the public part of the plugin.
 *
 * @link              https://github.com/demispatti/cb-parallax
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/public/includes
 *                    Author:            Demis Patti <demis@demispatti.ch>
 *                    Author URI:        http://demispatti.ch
 *                    License:           GPL-2.0+
 *                    License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_public_localisation {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_version
	 */
	private $plugin_version;

	/**
	 * The array holding the meta data.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array $post_meta
	 */
	private $post_meta;

	/**
	 * The array holding the meta data.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array $image_meta
	 */
	private $image_meta;

	/**
	 * The string that holds the meta key to access post meta data.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $meta_key
	 */
	private $meta_key;

	/**
	 * The array holding a set of default values for the background image display.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array $default
	 */
	private $default;

	/**
	 * Maintains the allowed option values.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array $allowed
	 */
	public $allowed;

	/**
	 * Sets the default values for a custom background.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   void
	 */
	private function set_default_values() {

		// Set up an array with default values.
		$this->default = array(
			'background_repeat' => 'no-repeat',
			'position_x'        => 'left',
			'position_y'        => 'top',
			'attachment'        => 'fixed'
		);
	}

	/**
	 * A whitelist of allowed options.
	 *
	 * @todo     : refactor all "allowed options" from all files -> one class.
	 * @since    0.1.0
	 * @access   private
	 * @return   void
	 */
	private function set_allowed_options() {

		// Image options for a static background image.
		$this->allowed['position_x'] = array(
			'left'   => __( 'left', $this->plugin_domain ),
			'center' => __( 'center', $this->plugin_domain ),
			'right'  => __( 'right', $this->plugin_domain ),
		);

		$this->allowed['position_y'] = array(
			'top'    => __( 'top', $this->plugin_domain ),
			'center' => __( 'center', $this->plugin_domain ),
			'bottom' => __( 'bottom', $this->plugin_domain ),
		);

		$this->allowed['attachment'] = array(
			'fixed'  => __( 'fixed', $this->plugin_domain ),
			'scroll' => __( 'scroll', $this->plugin_domain ),
		);

		$this->allowed['repeat'] = array(
			'no-repeat' => __( 'no-repeat', $this->plugin_domain ),
			'repeat'    => __( 'repeat', $this->plugin_domain ),
			'repeat-x'  => __( 'repeat horizontally', $this->plugin_domain ),
			'repeat-y'  => __( 'repeat vertically', $this->plugin_domain ),
		);

		// Image options for a dynamic background image.
		$this->allowed['parallax'] = array(
			'off' => false,
			'on'  => true
		);

		$this->allowed['direction'] = array(
			'vertical'   => __( 'vertical', $this->plugin_domain ),
			'horizontal' => __( 'horizontal', $this->plugin_domain )
		);

		$this->allowed['vertical_scroll_direction'] = array(
			'top'    => __( 'to top', $this->plugin_domain ),
			'bottom' => __( 'to bottom', $this->plugin_domain )
		);

		$this->allowed['horizontal_scroll_direction'] = array(
			'left'  => __( 'to the left', $this->plugin_domain ),
			'right' => __( 'to the right', $this->plugin_domain )
		);

		$this->allowed['horizontal_alignment'] = array(
			'left'   => __( 'left', $this->plugin_domain ),
			'center' => __( 'center', $this->plugin_domain ),
			'right'  => __( 'right', $this->plugin_domain ),
		);

		$this->allowed['vertical_alignment'] = array(
			'top'    => __( 'top', $this->plugin_domain ),
			'center' => __( 'center', $this->plugin_domain ),
			'bottom' => __( 'bottom', $this->plugin_domain ),
		);

		$this->allowed['overlay_image'] = array(
			'none' => __( 'none', $this->plugin_domain ),
			'01'   => '01.png',
			'02'   => '02.png',
			'03'   => '03.png',
			'04'   => '04.png',
			'05'   => '05.png',
			'06'   => '06.png',
			'07'   => '07.png',
			'08'   => '08.png',
			'09'   => '09.png'
		);

		$this->allowed['overlay_opacity'] = array(
			'default' => __( 'default', $this->plugin_domain ),
			'0.1'     => '0.1',
			'0.2'     => '0.2',
			'0.3'     => '0.3',
			'0.4'     => '0.4',
			'0.5'     => '0.5',
			'0.6'     => '0.6',
			'0.7'     => '0.7',
			'0.8'     => '0.8',
			'0.9'     => '0.9'
		);
	}

	/**
	 * Kicks off localisation of the public part of the plugin.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param    string $plugin_name
	 * @param    string $plugin_domain
	 * @param    string $plugin_version
	 * @param    string $meta_key
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version, $meta_key ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_domain  = $plugin_domain;
		$this->plugin_version = $plugin_version;
		$this->meta_key       = $meta_key;
		$this->post_meta      = [ ];

		$this->set_default_values();
		$this->set_allowed_options();
		$this->init();
	}

	/**
	 * Register all necessary hooks for this part of the plugin to work with WordPress.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function init() {

		add_action( 'wp_enqueue_scripts', array( &$this, 'get_image_meta' ), 1 );
		add_action( 'template_redirect', array( &$this, 'get_post_meta' ), 100 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'localize_public_area' ), 1000 );
	}

	/**
	 * Retrieves the image meta data.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function get_image_meta() {

		global $post;

		$this->image_meta = [ ];

		if ( '' === get_post_meta( $post->ID, $this->meta_key, true ) ) {
			return;
		}

		// Get the post meta data.
		$post_meta = get_post_meta( $post->ID, $this->meta_key, true );

		$image_attributes = null;
		$attachment_id    = $post_meta['attachment_id'];

		// If an attachment ID was found, get the image height.
		if ( ! empty( $attachment_id ) ) {
			$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' );

			$this->image_meta['imageSrc']    = isset( $image_attributes[0] ) ? $image_attributes[0] : '';
			$this->image_meta['imageWidth']  = $image_attributes[1];
			$this->image_meta['imageHeight'] = $image_attributes[2];
		} else {

			$this->image_meta['imageSrc']    = '';
			$this->image_meta['imageWidth']  = 0;
			$this->image_meta['imageHeight'] = 0;
		}
		// Determines weather parallax is possible or not.
		if ( $this->image_meta['imageWidth'] >= 1920 && $this->image_meta['imageHeight'] >= 1200 ) {

			$this->image_meta['parallaxPossible'] = true;
		} else {

			$this->image_meta['parallaxPossible'] = false;
		}
	}

	/**
	 * Retrieves the post meta data.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function get_post_meta() {

		global $post;

		$this->post_meta = [ ];

		if ( '' === get_post_meta( $post->ID, $this->meta_key, true ) ) {
			return;
		}

		// Get the post meta data.
		$post_meta = get_post_meta( $post->ID, $this->meta_key, true );

		// Translates parameters into the default locale to propperly serve the script.
		$post_meta = $this->translate_to_default_locale( $post_meta );

		// Below we sort of cache the values and make sure they have been white-listed. Otherwise, we set the default value.

		// Image options.
		$this->post_meta['backgroundRepeat'] = in_array( $post_meta['background_repeat'], $this->allowed['repeat'] ) ? $post_meta['background_repeat'] : array_values( $this->allowed['repeat'] )[0];

		$this->post_meta['positionX'] = in_array( $post_meta['position_x'], $this->allowed['position_x'] ) ? $post_meta['position_x'] : array_values( $this->allowed['position_x'] )[0];

		$this->post_meta['positionY'] = in_array( $post_meta['position_y'], $this->allowed['position_y'] ) ? $post_meta['position_y'] : array_values( $this->allowed['position_y'] )[0];

		$this->post_meta['backgroundAttachment'] = in_array( $post_meta['background_attachment'], $this->allowed['attachment'] ) ? $post_meta['background_attachment'] : array_values( $this->allowed['attachment'] )[0];
		// Parallax options.
		$this->post_meta['parallaxEnabled'] = $post_meta['parallax_enabled'] == true ? $post_meta['parallax_enabled'] : array_values( $this->allowed['parallax'] )[0];

		$this->post_meta['direction'] = in_array( $post_meta['direction'], $this->allowed['direction'] ) ? $post_meta['direction'] : array_values( $this->allowed['direction'] )[0];

		$this->post_meta['verticalScrollDirection']   = in_array( $post_meta['vertical_scroll_direction'], $this->allowed['vertical_scroll_direction'] ) ? $post_meta['vertical_scroll_direction'] : array_values( $this->allowed['vertical_scroll_direction'] )[0];
		$this->post_meta['horizontalScrollDirection'] = in_array( $post_meta['horizontal_scroll_direction'], $this->allowed['horizontal_scroll_direction'] ) ? $post_meta['horizontal_scroll_direction'] : array_values( $this->allowed['horizontal_scroll_direction'] )[0];

		$this->post_meta['horizontalAlignment'] = in_array( $post_meta['horizontal_alignment'], $this->allowed['horizontal_alignment'] ) ? $post_meta['horizontal_alignment'] : array_values( $this->allowed['horizontal_alignment'] )[0];

		$this->post_meta['verticalAlignment'] = in_array( $post_meta['vertical_alignment'], $this->allowed['vertical_alignment'] ) ? $post_meta['vertical_alignment'] : array_values( $this->allowed['vertical_alignment'] )[0];

		$this->post_meta['overlayImage'] = in_array( $post_meta['overlay_image'], $this->allowed['overlay_image'] ) ? $post_meta['overlay_image'] : array_values( $this->allowed['overlay_image'] )[0];

		$this->post_meta['overlayOpacity'] = in_array( $post_meta['overlay_opacity'], $this->allowed['overlay_opacity'] ) ? $post_meta['overlay_opacity'] : array_values( $this->allowed['overlay_opacity'] )[3];
	}

	/**
	 * Helper function, that translates "non-default-locale strings" into strings of the default locale,
	 * to propperly serve the script.
	 *
	 * @since  0.1.0
	 * @access private
	 * @param  $post_meta
	 * @return mixed|void
	 */
	private function translate_to_default_locale( $post_meta ) {

		$output = [ ];

		foreach ( $post_meta as $option => $value ) {

			switch ( $option ) {

				// Custom background options.
				case( $option === 'background_repeat' );

					if ( isset( $value ) && $value == __( 'no-repeat', $this->plugin_domain ) ) {

						$output[ $option ] = 'no-repeat';
					} else if ( isset( $value ) && $value == __( 'repeat', $this->plugin_domain ) ) {

						$output[ $option ] = 'repeat';
					} else if ( isset( $value ) && $value == __( 'horizontal', $this->plugin_domain ) ) {

						$output[ $option ] = 'horizontal';
					} else if ( isset( $value ) && $value == __( 'vertical', $this->plugin_domain ) ) {

						$output[ $option ] = 'vertical';
					} else {
						$output[ $option ] = $value;
					}

					break;

				case( $option === 'vertical_alignment' || $option === 'position_y' );

					if ( isset( $value ) && $value == __( 'top', $this->plugin_domain ) ) {

						$output[ $option ] = 'top';
					} else if ( isset( $value ) && $value == __( 'center', $this->plugin_domain ) ) {

						$output[ $option ] = 'center';
					} else if ( isset( $value ) && $value == __( 'bottom', $this->plugin_domain ) ) {

						$output[ $option ] = 'bottom';
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'horizontal_alignment' || $option === 'position_x' );

					if ( isset( $value ) && $value == __( 'left', $this->plugin_domain ) ) {

						$output[ $option ] = 'left';
					} else if ( isset( $value ) && $value == __( 'center', $this->plugin_domain ) ) {

						$output[ $option ] = 'center';
					} else if ( isset( $value ) && $value == __( 'right', $this->plugin_domain ) ) {

						$output[ $option ] = 'right';
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'background_attachment' );

					if ( isset( $value ) && $value == __( 'fixed', $this->plugin_domain ) ) {

						$output[ $option ] = 'fixed';
					} else if ( isset( $value ) && $value == __( 'scroll', $this->plugin_domain ) ) {

						$output[ $option ] = 'scroll';
					} else {

						$output[ $option ] = $value;
					}
					break;

				// Parallax background options.
				case( $option === 'direction' );

					if ( isset( $value ) && $value == __( 'vertical', $this->plugin_domain ) ) {

						$output[ $option ] = 'vertical';
					} else if ( isset( $value ) && $value == __( 'horizontal', $this->plugin_domain ) ) {

						$output[ $option ] = 'horizontal';
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'vertical_scroll_direction' );

					if ( isset( $value ) && $value == __( 'to top', $this->plugin_domain ) ) {

						$output[ $option ] = 'to top';
					} else if ( isset( $value ) && $value == __( 'to bottom', $this->plugin_domain ) ) {

						$output[ $option ] = 'to bottom';
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'horizontal_scroll_direction' );

					if ( isset( $value ) && $value == __( 'to the left', $this->plugin_domain ) ) {

						$output[ $option ] = 'to the left';
					} else if ( isset( $value ) && $value == __( 'to the right', $this->plugin_domain ) ) {

						$output[ $option ] = 'to the right';
					} else {

						$output[ $option ] = $value;
					}
					break;

				case( $option === 'overlay' );

					if ( isset( $value ) && $value == __( 'none', $this->plugin_domain ) ) {

						$output[ $option ] = 'none';
					} else {
						$output[ $option ] = $value;
					}
					break;

				case( $option === 'overlay_opacity' );

					if ( isset( $value ) && $value == __( 'default', $this->plugin_domain ) ) {

						$output[ $option ] = 'default';
					} else {
						$output[ $option ] = $value;
					}
					break;

				default:
					$output[ $option ] = $value;
			}
		}

		return apply_filters( 'translate_to_default_locale', $output, $post_meta );
	}

	/**
	 * Retrieves the path to the folder containing the overlay images.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   array
	 */
	private function get_path_to_overlay_images() {

		$path = site_url() . '/wp-content/plugins/cb-parallax/public/images/overlays/';

		return array( 'overlayPath' => $path );
	}

	/**
	 * Localizes the public part of the plugin.
	 *
	 * @hooked_action
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function localize_public_area() {

		// Passes the parameters to the script.
		wp_localize_script(
			$this->plugin_name . '-public-js',
			'cbParallax',
			array_merge(
				$this->image_meta,
				$this->post_meta,
				$this->get_path_to_overlay_images()
			)
		);
	}
}
