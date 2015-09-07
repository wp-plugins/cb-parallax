<?php

/**
 * This class is based on Justin Tadlocks functionality for handling the front end display of custom backgrounds.
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
class cb_parallax_custom_background {

    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_name
     */
    private $plugin_name;

    /**
     * The domain of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_domain
     */
    private $plugin_domain;

    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_version
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
     * The background repeat property.  Allowed: 'no-repeat', 'background_repeat', 'repeat-x', 'repeat-y'.
     *
     * @since  0.1.0
     * @access public
     * @var    string
     */
    public $repeat = 'background_repeat';

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
     * The name of the meta key for accessing post meta data.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $meta_key
     */
    private $meta_key;

    /**
     * Maintains the allowed option values.
     *
     * @since  0.1.0
     * @access public
     * @var    array $allowed
     */
    private $allowed;

    /**
     * A whitelist of allowed options.
     *
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function set_allowed_options() {

        $this->allowed['parallax'] = array(
            'off' => 'off',
            'on' => 'on'
        );

        // Set up an array of allowed values for the repeat option.
        $this->allowed['repeat'] = array(
            'no-repeat' => __('no repeat', $this->plugin_domain),
            'background_repeat' => __('repeat', $this->plugin_domain),
            'repeat-x' => __('repeat horizontally', $this->plugin_domain),
            'repeat-y' => __('repeat vertically', $this->plugin_domain),
        );

        // Set up an array of allowed values for the position-x option.
        $this->allowed['position_x'] = array(
            'left' => __('left', $this->plugin_domain),
            'right' => __('right', $this->plugin_domain),
            'center' => __('center', $this->plugin_domain),
        );

        // Set up an array of allowed values for the position-x option.
        $this->allowed['position_y'] = array(
            'top' => __('top', $this->plugin_domain),
            'bottom' => __('bottom', $this->plugin_domain),
            'center' => __('center', $this->plugin_domain),
        );

        // Set up an array of allowed values for the attachment option.
        $this->allowed['attachment'] = array(
            'scroll' => __('scroll', $this->plugin_domain),
            'fixed' => __('fixed', $this->plugin_domain),
        );

        $this->allowed['ratio'] = array(
            'auto' => __('auto', $this->plugin_domain),
            'slow' => 'slow',
            'medium' => 'medium',
            'fast' => 'fast'
        );

        $this->allowed['type'] = array(
            'background' => __('background', $this->plugin_domain),
            'foreground' => __('foreground', $this->plugin_domain)
        );

        $this->allowed['direction'] = array(
            'vertical' => __('vertical', $this->plugin_domain),
            'horizontal' => __('horizontal', $this->plugin_domain)
        );

        $this->allowed['vertical_scroll_direction'] = array(
            'top' => __('top', $this->plugin_domain),
            'bottom' => __('bottom', $this->plugin_domain)
        );

        $this->allowed['horizontal_scroll_direction'] = array(
            'left' => __('left', $this->plugin_domain),
            'right' => __('right', $this->plugin_domain)
        );
    }

    /**
     * Sets up the default custom background.
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
    private function init() {

        // Run a check for 'custom-background' support late. Themes should've already registered support.
        add_action('init', array( &$this, 'add_theme_support' ), 95);
    }

    /**
     * Checks if the current theme supports the 'custom-background' feature. If not, we won't do anything.
     * If the theme does support it, we'll add a custom background callback on 'wp_head' if the theme
     * hasn't defined a custom callback.  This will allow us to add a few extra options for users.
     *
     * @hooked_action
     *
     * @uses   setup_background()
     * @uses   echo_custom_background()
     *
     * @since  0.1.0
     * @access publc
     * @return void
     */
    public function add_theme_support() {

        // Run on 'template_redirect' to make sure conditional tags are set.
        add_action('template_redirect', array( &$this, 'setup_background' ));

        // Get the callback for printing styles on 'wp_head'.
        $wp_head_callback = get_theme_support('custom-background', 'wp-head-callback');

        // If the theme hasn't set up a custom callback, let's roll our own with a few extra options.
        if( false === $wp_head_callback || '_custom_background_cb' === $wp_head_callback ) {

            add_theme_support('custom-background', array( 'wp-head-callback' => array( &$this, 'echo_custom_background' ) ));
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
        if( ! is_singular() ) {
            return;
        }

        // Get the post variables.
        global $post;

        // If the post type doesn't support 'custom-background', bail.
        if( ! post_type_supports($post->post_type, 'custom-background') ) {
            return;
        }

        // If the theme doesn't support 'custom-background', bail.
        if( ! current_theme_supports('custom-background') ) {
            return;
        }

        // Get the post meta.
        $post_meta = get_post_meta($post->ID, $this->meta_key, true);

        // If there is no post meta stored yet, we bail.
        if( false === $post_meta || '' === $post_meta ) {

            return;
        }

        $post_meta = $this->translate_to_default_locale($post_meta);

        // Get the background color.
        $this->color = ! empty( $post_meta['background_color'] ) ? $post_meta['background_color'] : '';

        // Get the background image attachment ID.
        $attachment_id = ! empty( $post_meta['attachment_id'] ) ? $post_meta['attachment_id'] : false;

        // If an attachment ID was found, get the image source.
        if( false !== $attachment_id ) {

            $image = wp_get_attachment_image_src($attachment_id, 'full');

            $this->image = ! empty( $image ) && isset( $image[0] ) ? esc_url($image[0]) : '';
        }

        // Filter the background color and image theme mods.
        add_filter('theme_mod_background_color', array( &$this, 'background_color' ), 25);
        add_filter('theme_mod_background_image', array( &$this, 'background_image' ), 25);

        // If an image was found, filter image-related theme mods.
        if( ! empty( $this->image ) ) {

            $this->repeat = ! empty( $post_meta['background_repeat'] ) ? $post_meta['background_repeat'] : $this->allowed['background_repeat'];
            $this->position_x = ! empty( $post_meta['position_x'] ) ? $post_meta['position_x'] : $this->allowed['position_x'];
            $this->position_y = ! empty( $post_meta['position_y'] ) ? $post_meta['position_y'] : $this->allowed['position_y'];
            $this->attachment = ! empty( $post_meta['background_attachment'] ) ? $post_meta['background_attachment'] : $this->allowed['background_attachment'];

            add_filter('theme_mod_background_repeat', array( &$this, 'background_repeat' ), 25);
            add_filter('theme_mod_position_x', array( &$this, 'position_x' ), 25);
            add_filter('theme_mod_position_y', array( &$this, 'position_y' ), 25);
            add_filter('theme_mod_background_attachment', array( &$this, 'background_attachment' ), 25);
        }
    }

    /**
     * Sets the background color. Only exectued if using a background image.
     *
     * @since  0.1.0
     * @access public
     * @return string
     * @param  string $color
     */
    public function background_color( $color ) {

        return ! empty( $this->color ) ? preg_replace('/[^0-9a-fA-F]/', '', $this->color) : $color;
    }

    /**
     * Sets the background image. Only exectued if using a background image.
     *
     * @since  0.1.0
     * @access public
     * @return string
     * @param  string $image The background image property.
     */
    public function background_image( $image ) {

        // Return the image if it has been set.
        if( ! empty( $this->image ) ) {
            $image = $this->image;
            // If no image is set but a color is, disable the WP image.
        } elseif( ! empty( $this->color ) ) {
            $image = '';
        }

        return $image;
    }

    /**
     * Sets the background repeat property. Only exectued if using a background image.
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
    public function position_x( $position_x ) {

        return ! empty( $this->position_x ) ? $this->position_x : $position_x;
    }

    /**
     * Sets the background vertical position. Only exectued if using a background image.
     *
     * @since  0.1.0
     * @access public
     * @return string
     * @param  string $position_y The background vertical position.
     */
    public function position_y( $position_y ) {

        return ! empty( $this->position_y ) ? $this->position_y : $position_y;
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
    private function translate_to_default_locale( $input ) {

        $output = [ ];

        foreach( $input as $option => $value ) {

            switch( $option ) {

                // Custom background options.
                case( $option === 'background_repeat' );

                    if( isset( $value ) && $value == __('no-repeat', $this->plugin_domain) ) {

                        $output[$option] = 'no-repeat';
                    } else if( isset( $value ) && $value == __('repeat', $this->plugin_domain) ) {

                        $output[$option] = 'repeat';
                    } else if( isset( $value ) && $value == __('horizontal', $this->plugin_domain) ) {

                        $output[$option] = 'horizontal';
                    } else if( isset( $value ) && $value == __('vertical', $this->plugin_domain) ) {

                        $output[$option] = 'vertical';
                    } else {
                        $output[$option] = $value;
                    }

                    break;

                case( $option === 'position_y' );

                    if( isset( $value ) && $value == __('top', $this->plugin_domain) ) {

                        $output[$option] = 'top';
                    } else if( isset( $value ) && $value == __('center', $this->plugin_domain) ) {

                        $output[$option] = 'center';
                    } else if( isset( $value ) && $value == __('bottom', $this->plugin_domain) ) {

                        $output[$option] = 'bottom';
                    } else {
                        $output[$option] = $value;
                    }
                    break;

                case( $option === 'position_x' );

                    if( isset( $value ) && $value == __('left', $this->plugin_domain) ) {

                        $output[$option] = 'left';
                    } else if( isset( $value ) && $value == __('center', $this->plugin_domain) ) {

                        $output[$option] = 'center';
                    } else if( isset( $value ) && $value == __('right', $this->plugin_domain) ) {

                        $output[$option] = 'right';
                    } else {
                        $output[$option] = $value;
                    }
                    break;

                case( $option === 'background_attachment' );

                    if( isset( $value ) && $value == __('fixed', $this->plugin_domain) ) {

                        $output[$option] = 'fixed';
                    } else if( isset( $value ) && $value == __('scroll', $this->plugin_domain) ) {

                        $output[$option] = 'scroll';
                    } else {

                        $output[$option] = $value;
                    }
                    break;

                default:
                    $output[$option] = $value;
            }
        }

        return apply_filters('translate_to_default_locale', $output, $input);
    }

    /**
     * Sets the background attachment property. Only exectued if using a background image.
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
     * Outputs the custom background style in the header, if an image is set.
     *
     * @since  0.1.0
     * @access public
     * @return void
     */
    public function echo_custom_background() {

        global $post;

        $post_meta = get_post_meta($post->ID, $this->meta_key, true);

        // We do only proceed if the parallax option is enabled or if there is meta data stored.
        if( isset( $post_meta['parallax_enabled'] ) && $post_meta['parallax_enabled']  == '1' || false === $post_meta ) {

            return;
        }

        // Get the background image.
        $image = set_url_scheme(get_background_image());

        // We do only proceed if an image is set.
        if( $image === '' ) {

            return;
        }

        // Get the background color.
        $color = get_background_color();

        // If there is no image or color, bail.
        if( empty( $image ) && empty( $color ) ) {

            return;
        }

        // Set the background color.
        $style = $color ? "background-color: #{$color};" : '';

        // If there's a background image, add it and set these properties.
        if( $image ) {

            // Background image.
            $style .= " background-image: url('{$image}');";

            // Background repeat.
            $mod_repeat = get_theme_mod('background_repeat', 'no-repeat');
            $repeat = in_array($post_meta['background_repeat'], array( 'no-repeat', 'repeat-x', 'repeat-y', 'background_repeat' )) ? $post_meta['background_repeat'] : $mod_repeat;

            $style .= " background-repeat: {$repeat};";

            // Background position.
            $mod_position_y = get_theme_mod('position_y', 'top');
            $position_y = in_array($post_meta['position_y'], array( 'top', 'center', 'bottom' )) ? $post_meta['position_y'] : $mod_position_y;

            $mod_position_x = get_theme_mod('position_x', 'left');
            $position_x = in_array($post_meta['position_x'], array( 'center', 'right', 'left' )) ? $post_meta['position_x'] : $mod_position_x;

            $style .= " background-position: {$position_y} {$position_x};";

            // Background attachment.
            $mod_attachment = get_theme_mod('background_attachment', 'scroll');
            $attachment = in_array($post_meta['background_attachment'], array( 'fixed', 'scroll' )) ? $post_meta['background_attachment'] : $mod_attachment;

            $style .= " background-attachment: {$attachment};";
        }

        $parallax_enabled = true == $post_meta['parallax_enabled'] ? $post_meta['parallax_enabled'] : false;

        // We bail, if the parallax option is enabled and the image is served trough the jQuery script. Else we echo the style for the custom background,
        // while the script won't be executed.
        if( $parallax_enabled ) {
            return;
        }
        // Output the custom background style.
        echo "\n" . '<style type="text/css" id="custom-background-css">' . 'body.custom-background' . '{ ' . trim($style) . ' }' . '</style>' . "\n";
    }

}
