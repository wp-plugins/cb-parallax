<?php

/**
 * The class responsible for determining the image size and deciding if the parallax effect can be enabled or not.
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
class cb_parallax_check_image_dimensions {

	private $image_meta;

	/**
	 * @param $plugin_name
	 * @param $plugin_domain
	 * @param $plugin_version
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_domain  = $plugin_domain;
		$this->plugin_version = $plugin_version;

	}

	public function init() {

		add_action( 'init', array( &$this, 'get_image_meta'), 9 );
	}

	/**
	 * Retrieves the source, width and and height of the custom background image.
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

			$this->image_meta['_cb_parallax_image_src']    = $image_attributes[0];
			$this->image_meta['_cb_parallax_image_width']  = $image_attributes[1];
			$this->image_meta['_cb_parallax_image_height'] = $image_attributes[2];

			if ( $this->image_meta['_cb_parallax_image_width'] >= 1920 && $this->image_meta['_cb_parallax_image_height'] >= 1200 ) {

				$this->image_meta['parallax_enabled'] = true;
			} else {

				$this->image_meta['parallax_enabled'] = false;
			}
		}
	}

	public function localize_script() {

		// Passes true or false to the script.
		wp_localize_script( $this->plugin_name . '-meta-box-js', 'Image', array( 'parallax_enabled' => $this->image_meta['parallax_enabled'] ), 50 );

	}

}
