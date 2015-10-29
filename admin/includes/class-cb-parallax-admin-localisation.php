<?php

/**
 * This class is responsible for localizing the admin part of the plugin.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_admin_localisation {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $plugin_version
	 */
	private $plugin_version;

	/**
	 * The name of the meta key for accessing post meta data.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $meta_key
	 */
	private $meta_key;

	/**
	 * The array holding the "meta data" of the image.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array $image_options
	 */
	private $image_options;

	/**
	 * Kicks off localisation of the admin part.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @param    string $plugin_name
	 * @param    string $plugin_domain
	 * @param    string $plugin_version
	 * @param    string $meta_key
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version, $meta_key ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_domain = $plugin_domain;
		$this->plugin_version = $plugin_version;
		$this->meta_key = $meta_key;

		$this->init();
	}

	/**
	 * Register all necessary hooks for this part of the plugin to work with WordPress, if the user has admin rights.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	private function init() {

		if( is_admin() ) {

			add_action( 'admin_enqueue_scripts', array( &$this, 'get_background_image_options' ), 1 );
			add_action( 'admin_enqueue_scripts', array( &$this, 'localize_meta_box' ), 1000 );
			add_action( 'admin_enqueue_scripts', array( &$this, 'localize_media_frame' ), 1000 );
		}
	}

	/**
	 * Retrieves the source, width and and height of the custom background image,
	 * as well as the possible directions and the actually selected direction ( for "mode").
	 * This is used to control display of the elements inside the meta box.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function get_background_image_options() {

		$this->image_options = [ ];
		$image_options_attributes = NULL;

		// Get the background image attachment ID.
		$post_meta = get_post_meta( get_the_ID(), $this->meta_key, true );

		if( !empty($post_meta['attachment_id']) ) {
			$image_options_attributes = wp_get_attachment_image_src( absint( $post_meta['attachment_id'] ), false );

			$this->image_options['attachmentUrl'] = $image_options_attributes[0];
			$this->image_options['imageWidth'] = $image_options_attributes[1];
			$this->image_options['imageHeight'] = $image_options_attributes[2];

			if( $this->image_options['imageWidth'] >= 1920 && $this->image_options['imageHeight'] >= 1200 ) {

				$this->image_options['parallaxPossible'] = true;
			} else {

				$this->image_options['parallaxPossible'] = false;
			}

			$this->image_options['parallaxEnabled'] = !empty($post_meta['parallax_enabled']) ? $post_meta['parallax_enabled'] : false;

			// Below we retrieve localized strings - the actual value and the allowed options for that value to check against inside the script.
			$direction = !empty($post_meta['direction']) ? $post_meta['direction'] : false;
			if( false !== $direction && $direction === __( 'vertical', $this->plugin_domain ) ) {

				$this->image_options['actualDirection'] = __( 'vertical', $this->plugin_domain );
			} else {

				$this->image_options['actualDirection'] = __( 'horizontal', $this->plugin_domain );
			}

			$this->image_options['verticalDirection'] = __( 'vertical', $this->plugin_domain );
			$this->image_options['horizontalDirection'] = __( 'horizontal', $this->plugin_domain );
		}
	}

	/**
	 * Retrieves - so far German - strings to localize some css-pseudo-selectors. They are not implemented as localizeable strings,
	 * since there may be issues with translated words for "on" and "off" regarding the limited space on the switch.
	 * I'll leave it like that for now.
	 *
	 * @since  0.1.0
	 * @see    admin/css/switch.css
	 * @see    admin/js/admin.js
	 * @access private
	 * @return array $labels
	 */
	private function switches_texts() {

		$locale = $this->get_locale();

		switch( $locale ) {

			case($locale == 'de_DE');

				$labels = array(
					'locale'       => $locale,
					'switchesText' => array( 'On' => 'Ein', 'Off' => 'Aus' ),
				);
				break;

			default:

				$labels = array(
					'locale'       => 'default',
					'switchesText' => array( 'On' => 'On', 'Off' => 'Off' ),
				);
		}

		return $labels;
	}

	/**
	 * Localizes the text on the color picker.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return array
	 */
	private function background_color_text() {

		return array(
			'backgroundColorText' => __( 'Background Color', $this->plugin_domain ),
			'overlayColorText'    => __( 'Overlay Color', $this->plugin_domain ),
			'noneString'          => __( 'none', $this->plugin_domain ),
		);
	}

	/**
	 * Retrieves the locale of the WordPress installation.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return string
	 */
	private function get_locale() {

		return get_locale();
	}

	/**
	 * Localizes the meta box.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function localize_meta_box() {

		wp_localize_script(
			$this->plugin_name . '-admin-js',
			'cbParallax',
			array_merge(
				$this->switches_texts(),
				$this->background_color_text(),
				$this->image_options
			)
		);
	}

	/**
	 * Localizes the "media frame".
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function localize_media_frame() {

		wp_localize_script(
			$this->plugin_name . '-admin-js',
			'cbParallaxMediaFrame',
			array(
				'title'  => __( 'Set Background Image', $this->plugin_domain ),
				'button' => __( 'Set background image', $this->plugin_domain ),
			)
		);
	}
}
