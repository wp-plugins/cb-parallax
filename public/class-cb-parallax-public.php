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
	 * The loader that's responsible for maintaining
	 * and registering all hooks that power the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      object $loader
	 */
	private $loader;

	/**
	 * The name of the meta key for accessing post meta data.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $meta_key
	 */
	private $meta_key;

	/**
	 * Kicks off the public part of the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name
	 * @param string $plugin_domain
	 * @param string $plugin_version
	 * @param object $loader
	 * @param string $meta_key
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version, $loader, $meta_key ) {

		$this->plugin_name = $plugin_name;
		$this->plugin_domain = $plugin_domain;
		$this->plugin_version = $plugin_version;
		$this->loader = $loader;
		$this->meta_key = $meta_key;

		$this->load_dependencies();
	}

	/**
	 * Checks if it should "superseed" a possibly installed "Nicescrollr" plugin on the frontend.
	 *
	 * @hooked_action
	 *
	 * @since    0.2.4
	 * @access   public
	 * @return   void
	 */
	public function check_for_nicescrollr_plugin() {

		// Retrieves the "parallax enabled" option set within the meta box.
		$post_meta = get_post_meta( get_the_ID(), $this->meta_key, true );
		// Checks for the "scrolling preserved" option.
		$scrolling_preserved = ('1' == get_option( $this->meta_key, true ) ? get_option( $this->meta_key, true ) : false);

		$parallax_enabled = (isset($post_meta['parallax_enabled']) && '1' == $post_meta['parallax_enabled'] ? $post_meta['parallax_enabled'] : false);

		if( $parallax_enabled || $scrolling_preserved ) {

			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			if( is_plugin_active( 'nicescrollr/nsr.php' ) ) {

				set_transient( 'cb_parallax_superseeds_nicescrollr_plugin_on_frontend', true, 60 );
			}
		}
	}

	/**
	 * Loads the class responsible for localizing the script that manages the parallax related stuff on the frontend.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "public/includes/class-cb-parallax-public-localisation.php";
	}

	/**
	 * Registers the stylesheet for the public-facing side of the site.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function enqueue_styles() {
		
		wp_enqueue_style(
			$this->plugin_name . '-public-css',
			plugin_dir_url( __FILE__ ) . 'css/public.css',
			array(),
			$this->plugin_version,
			'all'
		);
	}

	/**
	 * Registers the scripts for the public-facing side of the site.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function enqueue_scripts() {

		global $post;

		$post_meta = get_post_meta( $post->ID, $this->meta_key, true );

		//* Checks if parallax is enabled and if we need to load these scripts
		if( isset($post_meta) && $post_meta == '' || false == $post_meta['parallax_enabled'] ) {
			return;
		}

		// Nicescroll, modified version.
		wp_enqueue_script(
			$this->plugin_name . '-cbp-nicescroll-min-js',
			plugin_dir_url( __FILE__ ) . '../vendor/nicescroll/jquery.cbp.nicescroll.min.js',
			array( 'jquery' ),
			$this->plugin_version,
			true
		);

		// Public part.
		wp_enqueue_script(
			$this->plugin_name . '-public-js',
			plugin_dir_url( __FILE__ ) . 'js/public.js',
			array(
				'jquery',
				$this->plugin_name . '-cbp-nicescroll-min-js',
			),
			$this->plugin_version,
			true
		);
	}

	/**
	 * Initiates the localisation for the public part of the plugin.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   void
	 */
	public function define_public_localisation() {

		$post_meta = get_post_meta( get_the_ID(), $this->meta_key, true );

		//* Checks if parallax is enabled and if we need to load the class that localizes the script
		if( isset($post_meta ) && $post_meta == '' || false == $post_meta['parallax_enabled'] ) {
			return;
		}

		$public_localisation = new cb_parallax_public_localisation( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version(), $this->get_meta_key() );
		$this->loader->add_action( 'wp_enqueue_scripts', $public_localisation, 'get_image_meta', 12 );
		$this->loader->add_action( 'template_redirect', $public_localisation, 'get_post_meta' );
		$this->loader->add_action( 'wp_enqueue_scripts', $public_localisation, 'localize_public_area', 1000 );
	}

	/**
	 * Retrieves the name of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;
	}

	/**
	 * Retrieves the domain of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string The domain of the plugin.
	 */
	public function get_plugin_domain() {

		return $this->plugin_domain;
	}

	/**
	 * Retrieves the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string The version number of the plugin.
	 */
	public function get_plugin_version() {

		return $this->plugin_version;
	}

	/**
	 * Retrieves the meta key.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   string $meta_key
	 */
	public function get_meta_key() {

		return $this->meta_key;
	}

}
