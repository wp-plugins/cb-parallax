<?php

/**
 * This class is responsible for determining wether to display the
 * parallax enable checkbox inside the meta box or not.
 * It then passes the parameter to the meta box script trough WordPress' localisation feature.
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
class cb_parallax_meta_manager_admin {

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
	 * @var      array $image The current version of the plugin.
	 */
	private $image;

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

		add_action( 'admin_enqueue_scripts', array( &$this, 'get_image_meta' ), 1 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'localize_script' ), 100 );
	}

	/**
	 * Retrieves the source, width and and height of the custom background image.
	 *
	 * @since    0.2.0
	 */
	public function get_image_meta() {

		global $post;

		$image_attributes = null;
		$attachment_id    = null;

		// Get the background image attachment ID.
		$attachment_id = get_post_meta( $post->ID, '_cb_parallax_image_id', true );

		// If an attachment ID was found, get the image height.
		if ( ! empty( $attachment_id ) ) {
			$image_attributes = wp_get_attachment_image_src( absint( $attachment_id ), false );

			$this->image['_cb_parallax_image_width']  = $image_attributes[1];
			$this->image['_cb_parallax_image_height'] = $image_attributes[2];

			if( $this->image['_cb_parallax_image_width'] >= 1920 && $this->image['_cb_parallax_image_height'] >= 1200 ) {

				$this->image['parallax_possible'] = true;
			} else {

				$this->image['parallax_possible'] = false;
			}
		}
	}

	/**
	 * Passes the parameter that defines wether to display the parallax enable checkbox or not.
	 *
	 * @since    0.2.0
	 */
	public function localize_script() {

		wp_localize_script( $this->plugin_name . '-meta-box-js', 'AdminMeta', array( 'parallax_possible' => $this->image['parallax_possible'], ) );
	}
}
