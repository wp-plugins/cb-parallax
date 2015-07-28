<?php

/**
 * Enables the post type support for the given post types.
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
class cb_parallax_post_type_support {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The domain of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_domain The domain of this plugin.
	 */
	private $plugin_domain;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_version The current version of this plugin.
	 */
	private $plugin_version;

	/**
	 * The available post types.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $post_types The available post types to use this plugin with.
	 */
	private $post_types;

	/**
	 * The feature.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $feature The feature we want the post types to work with.
	 */
	private $feature;

	/**
	 * Set up this plugins post type support.
	 *
	 * 1. Defines the post types to support.
	 * 2. Defines the desired feature.
	 * 3. Assigns the feature to the post types.
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

		$this->post_types = array( 'post', 'page' );
		$this->feature    = 'custom-background';
	}

	/**
	 * Add post type support for the given post types with the defined feature.
	 * //So it is possible to define more features assigned to all previously defined post types.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function enable_post_type_support() {

		foreach ( $this->post_types as $post_type ) {

			add_post_type_support( $post_type, $this->feature );
		}

	}
}
