<?php

/**
 * The public facing part of the plugin.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/public
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_public {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_domain The string used to uniquely identify this plugin.
	 */
	private $plugin_domain;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_version The current version of the plugin.
	 */
	private $plugin_version;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      object $loader Maintains and registers all hooks for the plugin.
	 */
	private $loader;

	/**
	 * Initialize the class and set its properties.
	 *
	 * 1. Gets the stylesheet enqueued
	 * 2. Gets the scripts enqueued
	 * 3. Loads its dependencies
	 * 4. Defines the post meta manager file which retrieves the data related to the background image display
	 *
	 * @since      0.2.0
	 *
     * @param      string $plugin_name    The name of the plugin.
	 * @param      string $plugin_domain  The name of the plugin.
	 * @param      string $plugin_version The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_domain  = $plugin_domain;
		$this->plugin_version = $plugin_version;

		$this->load_dependencies();
		$this->define_post_meta_manager();
	}

	/**
	 * Loads the initial files needed by the admin part of the plugin and assigns the loader object.
	 * The class responsible for orchestrating the actions and filters of the core plugin.
	 * The class responsible for loading and handling post meta data.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cb-parallax-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "public/includes/class-cb-parallax-meta-manager-public.php";

		$this->loader = new cb_parallax_loader();
	}

	/**
	 * Register the stylesheet for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name . '-public-css', plugin_dir_url( __FILE__ ) . 'css/cb-parallax-public.css', array(), $this->plugin_version, 'all' );
	}

	/**
	 * Register the scripts for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		// Parallax Script
		wp_enqueue_script( $this->plugin_name . '-main-js', plugin_dir_url( __FILE__ ) . 'js/cb-parallax-main.js', array( 'jquery' ), $this->plugin_version, false, 10 );
	}

	/**
	 * Retrieves the data related to the background image to display.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   void
	 */
	private function define_post_meta_manager() {

		$post_meta_manager_public = new cb_parallax_meta_manager_public( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $post_meta_manager_public, 'get_image_meta_data', 1 );
		$this->loader->add_action( 'template_redirect', $post_meta_manager_public, 'get_post_meta_data', 1 );
		$this->loader->add_action( 'wp_enqueue_scripts', $post_meta_manager_public, 'localize_script', 100 );
	}

	/**
	 * Retrieves the name of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;
	}

	/**
	 * Retrieves the domain of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string    The domain of the plugin.
	 */
	public function get_plugin_domain() {

		return $this->plugin_domain;
	}

	/**
	 * Retrieves the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string    The version number of the plugin.
	 */
	public function get_plugin_version() {

		return $this->plugin_version;
	}
}
