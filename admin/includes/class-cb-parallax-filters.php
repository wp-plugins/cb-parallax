<?php

/**
 * This class is based on Justin Tadlocks functionality for handling the front end display of the custom backgrounds.
 * This class will check if a post has a custom background assigned to it
 * and filter the custom background theme mods if so on singular post views.
 * It also rolls its own handling of the 'wp_head' callback but only if the current theme isn't
 * handling this with its own callback.
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
class cb_parallax_filters {

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
	 * The background color property.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $color = '';

	/**
	 * The background image property.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $image = '';

	/**
	 * The background repeat property.  Allowed: 'no-repeat', 'repeat', 'repeat-x', 'repeat-y'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $repeat = 'repeat';

	/**
	 * The vertical value of the background position property.  Allowed: 'top', 'bottom', 'center'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $position_y = 'top';

	/**
	 * The horizontal value of the background position property.  Allowed: 'left', 'right', 'center'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $position_x = 'left';

	/**
	 * The background attachment property.  Allowed: 'scroll', 'fixed'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $attachment = 'scroll';

	/**
	 * Set up this plugins main functionality.
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
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	private function init() {

		// Run a check for 'custom-background' support late. Themes should've already registered support.
		add_action( 'after_setup_theme', array( &$this, 'add_theme_support' ), 95 );
	}

	/**
	 * Checks if the current theme supports the 'custom-background' feature. If not, we won't do anything.
	 * If the theme does support it, we'll add a custom background callback on 'wp_head' if the theme
	 * hasn't defined a custom callback.  This will allow us to add a few extra options for users.
	 *
	 * @since  0.1.0
	 * @access publc
	 * @return void
	 */
	public function add_theme_support() {

		// If the current theme doesn't support custom backgrounds, bail.
		if ( ! current_theme_supports( 'custom-background' ) ) {
			return;
		}

		// Run on 'template_redirect' to make sure conditional tags are set.
		add_action( 'template_redirect', array( &$this, 'setup_background' ) );

		// Get the callback for printing styles on 'wp_head'.
		$wp_head_callback = get_theme_support( 'custom-background', 'wp-head-callback' );

		// If the theme hasn't set up a custom callback, let's roll our own with a few extra options.
		if ( empty( $wp_head_callback ) || '_cb_parallax_cb' === $wp_head_callback ) {

			add_theme_support( 'custom-background', array( 'wp-head-callback' => array( &$this, 'custom_background_callback' ) ) );
		}
	}

	/**
	 * Sets up the custom background stuff once so that we're not running through the functionality
	 * multiple  times on a page load.  If not viewing a single post or if the post type doesn't support
	 * 'custom-background', we won't do anything.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function setup_background() {

		// If this isn't a singular view, bail.
		if ( ! is_singular() ) {
			return;
		}

		// Get the post variables.
		$post    = get_queried_object();
		$post_id = get_queried_object_id();

		// If the post type doesn't support 'custom-background', bail.
		if ( ! post_type_supports( $post->post_type, 'custom-background' ) ) {
			return;
		}

		// Get the background color.
		$this->color = get_post_meta( $post_id, '_cb_parallax_color', true );

		// Get the background image attachment ID.
		$attachment_id = get_post_meta( $post_id, '_cb_parallax_image_id', true );

		// If an attachment ID was found, get the image source.
		if ( ! empty( $attachment_id ) ) {

			$image = wp_get_attachment_image_src( $attachment_id, 'full' );

			$this->image = ! empty( $image ) && isset( $image[0] ) ? esc_url( $image[0] ) : '';
		}

		// Filter the background color and image theme mods.
		add_filter( 'theme_mod_background_color', array( &$this, 'background_color' ), 25 );
		add_filter( 'theme_mod_background_image', array( &$this, 'background_image' ), 25 );

		// If an image was found, filter image-related theme mods.
		if ( ! empty( $this->image ) ) {

			$this->repeat     = get_post_meta( $post_id, '_cb_parallax_repeat', true );
			$this->position_x = get_post_meta( $post_id, '_cb_parallax_position_x', true );
			$this->position_y = get_post_meta( $post_id, '_cb_parallax_position_y', true );
			$this->attachment = get_post_meta( $post_id, '_cb_parallax_attachment', true );

			add_filter( 'theme_mod_background_repeat', array( &$this, 'background_repeat' ), 25 );
			add_filter( 'theme_mod_background_position_x', array( &$this, 'background_position_x' ), 25 );
			add_filter( 'theme_mod_background_position_y', array( &$this, 'background_position_y' ), 25 );
			add_filter( 'theme_mod_background_attachment', array( &$this, 'background_attachment' ), 25 );
		}
	}

	/**
	 * Sets the background color.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 *
	 * @param  string $color The background color property.
	 */
	public function background_color( $color ) {

		return ! empty( $this->color ) ? preg_replace( '/[^0-9a-fA-F]/', '', $this->color ) : $color;
	}

	/**
	 * Sets the background image.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 *
	 * @param  string $image The background image property.
	 */
	public function background_image( $image ) {

		// Return the image if it has been set.
		if ( ! empty( $this->image ) ) {
			$image = $this->image;
			// If no image is set but a color is, disable the WP image.
		} elseif ( ! empty( $this->color ) ) {
			$image = '';
		}

		return $image;
	}

	/**
	 * Sets the background repeat property.  Only exectued if using a background image.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 *
	 * @param  string $repeat The background repeat property.
	 */
	public function background_repeat( $repeat ) {

		return ! empty( $this->repeat ) ? $this->repeat : $repeat;
	}

	/**
	 * Sets the background horizontal position.  Only exectued if using a background image.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 *
	 * @param  string $position_x The background horizontal position.
	 */
	public function background_position_x( $position_x ) {

		return ! empty( $this->position_x ) ? $this->position_x : $position_x;
	}

	/**
	 * Sets the background vertical position.  This isn't technically supported in WordPress (as
	 * of 3.6).  This method is only executed if using a background image and the
	 * custom_background_callback() method is executed.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 *
	 * @param  string $position_y The background vertical position.
	 */
	public function background_position_y( $position_y ) {

		return ! empty( $this->position_y ) ? $this->position_y : $position_y;
	}

	/**
	 * Sets the background attachment property.  Only exectued if using a background image.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 *
	 * @param  string $attachment
	 */
	public function background_attachment( $attachment ) {

		return ! empty( $this->attachment ) ? $this->attachment : $attachment;
	}

	/**
	 * Outputs the custom background style in the header.  This function is only executed if the value
	 * of the 'wp-head-callback' for the 'custom-background' feature is set to '__return_false'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function custom_background_callback() {

		// Get the background image.
		$image = set_url_scheme( get_background_image() );

		// Get the background color.
		$color = get_background_color();

		// If there is no image or color, bail.
		if ( empty( $image ) && empty( $color ) ) {
			return;
		}

		// Set the background color.
		$style = $color ? "background-color: #{$color};" : '';

		// If there's a background image, add it.
		if ( $image ) {

			// Background image.
			$style .= " background-image: url('{$image}');";

			// Background repeat.
			$repeat = get_theme_mod( 'background_repeat', 'repeat' );
			$repeat = in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) ? $repeat : 'repeat';

			$style .= " background-repeat: {$repeat};";

			// Background position.
			$position_y = get_theme_mod( 'background_position_y', 'top' );
			$position_y = in_array( $position_y, array( 'top', 'center', 'bottom' ) ) ? $position_y : 'top';

			$position_x = get_theme_mod( 'background_position_x', 'left' );
			$position_x = in_array( $position_x, array( 'center', 'right', 'left' ) ) ? $position_x : 'left';

			$style .= " background-position: {$position_y} {$position_x};";

			// Background attachment.
			$attachment = get_theme_mod( 'background_attachment', 'scroll' );
			$attachment = in_array( $attachment, array( 'fixed', 'scroll' ) ) ? $attachment : 'scroll';

			$style .= " background-attachment: {$attachment};";
		}

		// Output the custom background style.
		echo "\n" . '<style type="text/css" id="custom-background-css">body.custom-background{ ' . trim( $style ) . ' }</style>' . "\n";
	}
}
