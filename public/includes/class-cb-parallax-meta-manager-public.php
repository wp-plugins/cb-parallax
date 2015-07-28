<?php

/**
 * This class is responsible for retrieving the post meta data
 * and passing it on to the script.
 *
 * @link              https://github.com/demispatti/cb-parallax
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/public/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_meta_manager_public {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $plugin_domain The string used to uniquely identify this plugin.
	 */
	private $plugin_domain;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string $plugin_version The current version of the plugin.
	 */
	private $plugin_version;

	/**
	 * The array holding the meta data.

	 * @since    0.2.0
	 * @access   private
	 * @var      array $post_meta The current version of the plugin.
	 */
	private $post_meta;

	/**
	 * The array holding a set of default values for the background image display.
	 *
	 * @since    0.2.0
	 * @access   private
	 * @var      array $default The default settings for the background image.
	 */
	private $default;

	/**
	 * Plugin setup.
	 *
	 * 1. Assigns the plugin's id.
	 * 2. Loads options on the edit screens.
	 * 3. Saves teh post.
	 *
	 * @since    0.1.0
	 * @access   public
	 *
	 * @param    string $plugin_name
	 * @param    string $plugin_domain
	 * @param    string $plugin_version
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_domain  = $plugin_domain;
		$this->plugin_version = $plugin_version;

		// Set up the array which will hold the meta data.
		$this->post_meta = array();

		// Set up an array of meta keys and values.
		$this->meta_key = array(
			0 => '_cb_parallax_color',
			1 => '_cb_parallax_image_id',
			2 => '_cb_parallax_repeat',
			3 => '_cb_parallax_position_x',
			4 => '_cb_parallax_position_y',
			5 => '_cb_parallax_attachment',
			6 => '_cb_parallax_enabled',
			7 => '_cb_parallax_image_src',
			8 => '_cb_parallax_image_width',
			9 => '_cb_parallax_image_height'
		);

		// Set up an array with default values.
		$this->default = array(
			'repeat'     => 'no-repeat',
			'position_x' => 'left',
			'position_y' => 'top',
			'attachment' => 'fixed'
		);

		$this->init();
	}

	/**
	 * Register all necessary hooks for this part of the plugin to work with WordPress.
	 *
	 * @since    0.2.0
	 * @access   public
	 * @return   void
	 */
	private function init() {

		add_action( 'wp_enqueue_scripts', array( &$this, 'get_image_meta_data' ), 1 );
		add_action( 'template_redirect', array( &$this, 'get_post_meta_data' ), 1 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'localize_script' ), 100 );
	}

	/**
	 * Retrieves the source, width and and height of the custom background image.
	 *
	 * @since    0.2.0
	 */
	public function get_image_meta_data() {

		global $post;

		$image_attributes = null;
		$attachment_id    = null;

		// Get the background image attachment ID.
		$attachment_id = get_post_meta( $post->ID, '_cb_parallax_image_id', true );

		// If an attachment ID was found, get the image height.
		if ( ! empty( $attachment_id ) ) {
			$image_attributes = wp_get_attachment_image_src( absint( $attachment_id ), false );

			$this->post_meta['_cb_parallax_image_src']    = $image_attributes[0];
			$this->post_meta['_cb_parallax_image_width']  = $image_attributes[1];
			$this->post_meta['_cb_parallax_image_height'] = $image_attributes[2];

			if( $this->post_meta['_cb_parallax_image_width'] >= 1920 && $this->post_meta['_cb_parallax_image_height'] >= 1200 ) {

				$this->post_meta['parallax_enabled'] = true;
			} else {

				$this->post_meta['parallax_enabled'] = false;
			}

		}
	}

	/**
	 * Compares the meta_key values with the post meta data and stores matched items in an array.
	 *
	 * @since    0.1.0
	 * @access   public
	 */
	public function get_post_meta_data() {

		$post_meta = array();

		$meta_keys[] = $this->meta_key;
		$stored_data = get_post_meta( get_the_ID() );

		// Loop through the meta array to find...
		foreach ( $stored_data as $key => $value ) {

			// ...identical keys and then...
			if ( in_array( $key, $meta_keys[0] ) ) {

				// ...store its value assigned to it's key in an array.
				$post_meta[ $key ] = $value[0];
			}
		}

		// White-listed values.
		$allowed_repeat     = array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' );
		$allowed_position_x = array( 'left', 'right', 'center' );
		$allowed_position_y = array( 'top', 'bottom', 'center' );
		$allowed_attachment = array( 'fixed', 'scroll' );

		// Make sure the values have been white-listed. Otherwise, set the default value.
		$this->post_meta['_cb_parallax_repeat']           = in_array( $post_meta['_cb_parallax_repeat'], $allowed_repeat ) ? $post_meta['_cb_parallax_repeat'] : $this->default['repeat'];
		$this->post_meta['_cb_parallax_position_x']       = in_array( $post_meta['_cb_parallax_position_x'], $allowed_position_x ) ? $post_meta['_cb_parallax_position_x'] : $this->default['pos_x'];
		$this->post_meta['_cb_parallax_position_y']       = in_array( $post_meta['_cb_parallax_position_y'], $allowed_position_y ) ? $post_meta['_cb_parallax_position_y'] : $this->default['pos_y'];
		$this->post_meta['_cb_parallax_attachment']       = in_array( $post_meta['_cb_parallax_attachment'], $allowed_attachment ) ? $post_meta['_cb_parallax_attachment'] : $this->default['attachment'];
		$this->post_meta['_cb_parallax_enabled'] = ( $post_meta['_cb_parallax_enabled'] == 'on' ) ? true : false;

		$this->post_meta = $post_meta;
	}

	public function localize_script() {

		if ( $this->post_meta['_cb_parallax_enabled'] ) {

			// Passes the parameters to the script.
			wp_localize_script( $this->plugin_name . '-main-js', 'PostMetaData', array(
				'repeat'           => $this->post_meta['_cb_parallax_repeat'],
				'position_x'       => $this->post_meta['_cb_parallax_position_x'],
				'position_y'       => $this->post_meta['_cb_parallax_position_y'],
				'bg_attachment'    => $this->post_meta['_cb_parallax_attachment'],
				'parallax_enabled' => $this->post_meta['_cb_parallax_enabled'],
				'image_src'        => $this->post_meta['_cb_parallax_image_src'],
				'image_width'      => $this->post_meta['_cb_parallax_image_width'],
				'image_height'     => $this->post_meta['_cb_parallax_image_height'],
			), 50 );
		}
	}
}
