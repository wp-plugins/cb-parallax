<?php

/**
 * The core class of the plugin.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax {

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
	 * The name of the meta key for accessing post meta data.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $meta_key
	 */
	public $meta_key;

	/**
	 * The loader that's responsible for maintaining
	 * and registering all hooks that power the plugin.
	 *
     * @since    0.1.0
	 * @access   private
	 * @var      cb_parallax_loader $loader
	 */
	private $loader;

	/**
	 * 1. Defines the plugin's name, domain and version, and loads its basic dependencies.
	 * 2. Instanciates and assigns the loader.
	 * 3. Loads the language files.
	 * 4. Loads the admin part of the plugin.
	 * 5. Loads the public part of the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 */
	public function __construct() {

		$this->plugin_name    = 'cb-parallax';
		$this->plugin_domain  = $this->get_plugin_domain();
		$this->plugin_version = '0.1.0';
		$this->meta_key       = 'cb_parallax';

		$this->load_dependencies();
		$this->set_i18n();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Loads the initial files needed by the plugin and assigns the loader object.
	 *
	 * The class responsible for orchestrating the hooks of the plugin.
	 * The class responsible for defining the internationalization functionality of the plugin.
	 * The class that defines the admin part of the plugin.
	 * The class that defines the public part of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return void
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cb-parallax-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cb-parallax-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/class-cb-parallax-admin.php";

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "public/class-cb-parallax-public.php";

		$this->loader = new cb_parallax_loader();
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return void
	 */
	private function set_i18n() {

		$i18n = new cb_parallax_i18n();

		$i18n->set_domain( $this->get_plugin_domain() );

		$this->loader->add_action( 'plugins_loaded', $i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Instanciates the admin object and registers the hooks that shall be executed on it.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   void
	 */
	private function define_admin_hooks() {

		$admin = new cb_parallax_admin( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version(), $this->get_loader(), $this->get_meta_key() );

		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $admin, 'check_cap' );
		$this->loader->add_action( 'plugin_row_meta', $admin, 'plugin_row_meta', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$public = new cb_parallax_public( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version(), $this->get_loader(), $this->get_meta_key() );

		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_scripts' );
	}

	/**
	 * Runs the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function run() {

		$this->loader->run();
	}

	/**
	 * Retrieves the name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;
	}

	/**
	 * Retrieves the domain of the plugin used to uniquely identify it within the context of
	 * WordPress and to abstract internationalization functionality.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string    The domain of the plugin.
	 */
	public function get_plugin_domain() {

		return $this->get_plugin_name();
	}

	/**
	 * Retrieves the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string  $plugin_version
	 */
	public function get_plugin_version() {

		return $this->plugin_version;
	}

	/**
	 * Retrieves the loader.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string  $loader
	 */
	public function get_loader() {

		return $this->loader;
	}

	/**
	 * Retrieves the meta key for accessing the post meta data.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string  $meta_key
	 */
	public function get_meta_key() {

		return $this->meta_key;
	}
}
