<?php

/**
 * Defines and displays the meta box.
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
class cb_parallax_meta_box {

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
     * Whether the theme has a custom backround callback for 'wp_head' output.
     *
     * @since  0.1.0
     * @access public
     * @var    bool
     */
    public $theme_has_callback = false;

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
    public $allowed;

    /**
     * A whitelist of allowed options.
     *
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function set_allowed_options() {

        // Image options for a static background image.
        $this->allowed['position_x'] = array(
            'left' => __('left', $this->plugin_domain),
            'center' => __('center', $this->plugin_domain),
            'right' => __('right', $this->plugin_domain)
        );

        $this->allowed['position_y'] = array(
            'top' => __('top', $this->plugin_domain),
            'center' => __('center', $this->plugin_domain),
            'bottom' => __('bottom', $this->plugin_domain)
        );

        $this->allowed['attachment'] = array(
            'fixed' => __('fixed', $this->plugin_domain),
            'scroll' => __('scroll', $this->plugin_domain)
        );

        $this->allowed['repeat'] = array(
            'no-repeat' => __('no-repeat', $this->plugin_domain),
            'repeat' => __('repeat', $this->plugin_domain),
            'repeat-x' => __('repeat horizontally', $this->plugin_domain),
            'repeat-y' => __('repeat vertically', $this->plugin_domain)
        );

        // Image options for a dynamic background image.
        $this->allowed['parallax'] = array(
            'off' => false,
            'on' => true
        );

        $this->allowed['direction'] = array(
            'vertical' => __('vertical', $this->plugin_domain),
            'horizontal' => __('horizontal', $this->plugin_domain)
        );

        $this->allowed['vertical_scroll_direction'] = array(
            'top' => __('to top', $this->plugin_domain),
            'bottom' => __('to bottom', $this->plugin_domain)
        );

        $this->allowed['horizontal_scroll_direction'] = array(
            'left' => __('to the left', $this->plugin_domain),
            'right' => __('to the right', $this->plugin_domain)
        );

        $this->allowed['horizontal_alignment'] = array(
            'left' => __('left', $this->plugin_domain),
            'center' => __('center', $this->plugin_domain),
            'right' => __('right', $this->plugin_domain)
        );

        $this->allowed['vertical_alignment'] = array(
            'top' => __('top', $this->plugin_domain),
            'center' => __('center', $this->plugin_domain),
            'bottom' => __('bottom', $this->plugin_domain)
        );

        $this->allowed['overlay_image'] = array(
            'none' => __('none', $this->plugin_domain),
            '01' => '01.png',
            '02' => '02.png',
            '03' => '03.png',
            '04' => '04.png',
            '05' => '05.png',
            '06' => '06.png',
            '07' => '07.png',
            '08' => '08.png',
            '09' => '09.png'
        );

        $this->allowed['overlay_opacity'] = array(
            'default' => __('default', $this->plugin_domain),
            '0.1' => '0.1',
            '0.2' => '0.2',
            '0.3' => '0.3',
            '0.4' => '0.4',
            '0.5' => '0.5',
            '0.6' => '0.6',
            '0.7' => '0.7',
            '0.8' => '0.8',
            '0.9' => '0.9'
        );
    }

    /**
     * Kicks off the meta box.
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

        /* If the current user can't edit custom backgrounds, bail early. */
        if( ! current_user_can('cb_parallax_edit') && ! current_user_can('edit_theme_options') ) {
            return;
        }

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

        /* Only load on the edit post screen. */
        add_action('load-post.php', array( $this, 'load_post' ));
        add_action('load-post-new.php', array( $this, 'load_post' ));
    }

    /**
     * Add actions for the edit post screen.
     *
     * @since  0.1.0
     * @access public
     * @return void
     */
    public function load_post() {

        $screen = get_current_screen();

        /* If the current theme doesn't support custom backgrounds, bail. */
        if( ! current_theme_supports('custom-background') || ! post_type_supports($screen->post_type, 'custom-background') ) {
            return;
        }

        /* Get the 'wp_head' callback. */
        $wp_head_callback = get_theme_support('custom-background', 'wp-head-callback');

        /* Checks if the theme has set up a custom callback. */
        $this->theme_has_callback = empty( $wp_head_callback ) || '_custom_background_cb' === $wp_head_callback ? false : true;

        // Add the meta box
        add_action('add_meta_boxes', array( &$this, 'add_meta_box' ), 5);

        // Save meta data.
        add_action('save_post', array( &$this, 'save_post' ), 10, 2);
    }

    /**
     * Adds the meta box.
     *
     * @since  0.1.0
     * @access public
     * @param  string $post_type
     * @return void
     */
    public function add_meta_box( $post_type ) {

        add_meta_box($this->plugin_name . '-meta-box', __('cb Parallax', $this->plugin_domain), array(
            &$this,
            'display_meta_box'
        ), $post_type, 'side', 'core');
    }

    /**
     * Displays the meta box.
     *
     * @since  0.1.0
     * @access public
     * @param  object $post
     * @return void
     */
    public function display_meta_box( $post ) {

        // Get the post meta.
        $post_meta = get_post_meta($post->ID, $this->meta_key, true);

        // Get the background color.
        $background_color = ! empty( $post_meta['background_color'] ) ? $post_meta['background_color'] : '';

        // Get the background image attachment ID.
        $attachment_id = isset( $post_meta['attachment_id'] ) ? $post_meta['attachment_id'] : false;

        // If an attachment ID was found, get the image source.
        if( false !== $attachment_id ) {
            $image = wp_get_attachment_image_src(absint($attachment_id), 'full');
        }

        // Get the image URL.
        $url = ! empty( $image ) && isset( $image[0] ) ? $image[0] : '';

        /**
         * Make sure values are set for the "static" image options. This should always be set so that we can
         * be sure that the user's background image overwrites the default/WP custom background settings.
         * With one theme, this doesn't matter, but we need to make sure that the background stays
         * consistent between different themes and different WP custom background settings.  The data
         * will only be stored if the user selects a background image.
         */
        // Default image options.
        $background_repeat = ! empty( $post_meta['background_repeat'] ) ? $post_meta['background_repeat'] : array_values($this->allowed['repeat'])[0];

        $position_x = ! empty( $post_meta['position_x'] ) ? $post_meta['position_x'] : array_values($this->allowed['position_x'])[1];

        $position_y = ! empty( $post_meta['position_y'] ) ? $post_meta['position_y'] : array_values($this->allowed['position_y'])[1];

        $background_attachment = ! empty( $post_meta['background_attachment'] ) ? $post_meta['background_attachment'] : array_values($this->allowed['attachment'])[0];


        // Parallax options.
        $parallax_enabled = ! empty( $post_meta['parallax_enabled'] ) ? $post_meta['parallax_enabled'] : array_values($this->allowed['parallax'])[0];

        $direction = ! empty( $post_meta['direction'] ) ? $post_meta['direction'] : array_values($this->allowed['direction'])[0];

        $vertical_scroll_direction = ! empty( $post_meta['vertical_scroll_direction'] ) ? $post_meta['vertical_scroll_direction'] : array_values($this->allowed['vertical_scroll_direction'])[0];

        $horizontal_scroll_direction = ! empty( $post_meta['horizontal_scroll_direction'] ) ? $post_meta['horizontal_scroll_direction'] : array_values($this->allowed['horizontal_scroll_direction'])[0];

        $vertical_alignment = ! empty( $post_meta['vertical_alignment'] ) ? $post_meta['vertical_alignment'] : array_values($this->allowed['vertical_alignment'])[1];

        $horizontal_alignment = ! empty( $post_meta['horizontal_alignment'] ) ? $post_meta['horizontal_alignment'] : array_values($this->allowed['horizontal_alignment'])[1];

        $overlay_image = ! empty( $post_meta['overlay_image'] ) ? $post_meta['overlay_image'] : array_values($this->allowed['overlay_image'])[0];

        $overlay_opacity = ! empty( $post_meta['overlay_opacity'] ) ? $post_meta['overlay_opacity'] : array_values($this->allowed['overlay_opacity'])[3];

        $overlay_color = ! empty( $post_meta['overlay_color'] ) ? $post_meta['overlay_color'] : '';
        ?>

        <!-- hidden fields. -->
        <?php wp_nonce_field('cb_parallax_nonce_field', 'cb_parallax_nonce'); ?>
        <input type="hidden" name="cbp_background_image" id="cbp_background_image"
               value="<?php echo ! empty( $attachment_id ) ? esc_attr($attachment_id) : '' ?>"/>
        <input type="hidden" name="cbp_background_image_location" id="cbp_background_image_location"
               value="<?php echo ! empty( $attachment_id ) ? esc_url($url) : '' ?>"/>
        <!-- # hidden fields. -->

        <!-- background color. -->
        <p>
            <label for="cbp_background_color"><?php _e('Background Color', $this->plugin_domain); ?></label>
            <input type="text" name="cbp_background_color" id="cbp_background_color"
                   class="wp-color-picker cbp-color-picker" value="#<?php echo esc_attr($background_color); ?>"/>
        </p>
        <!-- # background color. -->

        <!-- background image. -->
        <p>
            <a href="#" class="cbp-media-url"><img id="cbp_background_image_url" class="cbp_background_image_url"
                                                   src="<?php echo esc_url($url); ?>"
                                                   style="max-width: 100%; max-height: 200px; display: block;"/></a>
        </p>
        <!-- # background image. -->

        <!-- media buttons. -->
        <p>
            <a href="#"
               class="button button-secondary cbp-add-media-button"><?php _e('Set background image', $this->plugin_domain); ?></a>
            <a href="#"
               class="button button-secondary cbp-remove-media-button"><?php _e('Remove background image', $this->plugin_domain); ?></a>
        </p>
        <!-- # media buttons. -->

        <!-- parallax checkbox -->
        <p class="cbp-single-option-container cbp-parallax-enabled-container">
            <label for="cbp_parallax_enabled"
                   class="label-for-cbp-switch"><?php echo __('Parallax', $this->plugin_domain); ?></label>

            <label class="cbp-switch">
                <input type="checkbox" id="cbp_parallax_enabled" class="cbp-switch-input cbp_parallax_enabled"
                       name="cbp_parallax_enabled" value="1"
                    <?php checked(1, isset( $parallax_enabled ) ? $parallax_enabled : 0, true); ?>>
                <span class="cbp-switch-label cbp_parallax_enabled" data-on="On" data-off="Off"></span>
                <span class="cbp-switch-handle"></span>
            </label>
        </p>
        <!-- # parallax checkbox -->

        <!-- background image options -->
        <div class="cbp-background-image-options-container">
            <p class="cbp-single-option-container">
                <label for="cbp_background_repeat"><?php _e('Repeat', $this->plugin_domain); ?></label>
                <select name="cbp_background_repeat" id="cbp_background_repeat"
                        class="widefat cbp_background_repeat fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['repeat'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $background_repeat); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container">
                <label for="cbp_position_y"><?php _e('Vertical Position', $this->plugin_domain); ?></label>
                <select name="cbp_position_y" id="cbp_position_y"
                        class="widefat cbp_position_y fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['position_y'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $position_y); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container">
                <label for="cbp_position_x"><?php _e('Horizontal Position', $this->plugin_domain); ?></label>
                <select name="cbp_position_x" id="cbp_position_x"
                        class="widefat cbp_position_x fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['position_x'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $position_x); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container">
                <label for="cbp_background_attachment"><?php _e('Attachment', $this->plugin_domain); ?></label>
                <select name="cbp_background_attachment" id="cbp_background_attachment"
                        class="widefat cbp_background_attachment fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['attachment'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $background_attachment); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
        </div>
        <!-- # background image options. -->

        <!-- parallax options -->
        <div class="cbp-parallax-options-container">
            <p class="cbp-single-option-container">
                <label for="cbp_direction"><?php _e('Mode', $this->plugin_domain); ?></label>
                <select name="cbp_direction" id="cbp_direction"
                        class="widefat cbp_direction fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['direction'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $direction); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container" id="cpb_vertical_scroll_direction_container">
                <label
                    for="cbp_vertical_scroll_direction"><?php _e('Scroll Direction', $this->plugin_domain); ?></label>
                <select name="cbp_vertical_scroll_direction" id="cbp_vertical_scroll_direction"
                        class="widefat cbp_vertical_scroll_direction fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['vertical_scroll_direction'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $vertical_scroll_direction); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container" id="cpb_horizontal_scroll_direction_container">
                <label
                    for="cbp_horizontal_scroll_direction"><?php _e('Scroll Direction', $this->plugin_domain); ?></label>
                <select name="cbp_horizontal_scroll_direction" id="cbp_horizontal_scroll_direction"
                        class="widefat cbp_horizontal_scroll_direction fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['horizontal_scroll_direction'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $horizontal_scroll_direction); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container" id="cbp_horizontal_alignment_container">
                <label for="cbp_horizontal_alignment"><?php _e('Alignment', $this->plugin_domain); ?></label>
                <select name="cbp_horizontal_alignment" id="cbp_horizontal_alignment"
                        class="widefat cbp_horizontal_alignment fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['horizontal_alignment'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $horizontal_alignment); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container" id="cbp_vertical_alignment_container">
                <label for="cbp_vertical_alignment"><?php _e('Alignment', $this->plugin_domain); ?></label>
                <select name="cbp_vertical_alignment" id="cbp_vertical_alignment"
                        class="widefat cbp_vertical_alignment fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['vertical_alignment'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $vertical_alignment); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container" id="cbp_overlay_image_container">
                <label for="cbp_overlay_image"><?php _e('Overlay', $this->plugin_domain); ?></label>
                <select name="cbp_overlay_image" id="cbp_overlay_image"
                        class="widefat cbp_overlay_image fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['overlay_image'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $overlay_image); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container" id="cbp_overlay_opacity_container">
                <label for="cbp_overlay_opacity"><?php _e('Overlay Opacity', $this->plugin_domain); ?></label>
                <select name="cbp_overlay_opacity" id="cbp_overlay_opacity"
                        class="widefat cbp_overlay_opacity fancy-select cbp-fancy-select">
                    <?php foreach( $this->allowed['overlay_opacity'] as $key => $value ) { ?>
                        <option
                            value="<?php echo esc_attr($value); ?>" <?php selected($value, $overlay_opacity); ?> ><?php echo esc_html($value); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p class="cbp-single-option-container" id="cbp_overlay_color_container">
                <label for="cbp_overlay_color"><?php _e( 'Overlay Color', $this->plugin_domain ); ?></label>
                <input type="text" name="cbp_overlay_color" id="cbp_overlay_color"
                       class="wp-color-picker cbp-color-picker" value="#<?php echo esc_attr( $overlay_color ); ?>"/>
            </p>
        </div>
        <!-- # parallax options. -->

        <?php
    }

    /**
     * Saves the data from the meta box.
     *
     * @since  0.1.0
     * @access public
     * @return void / mixed
     *
     * @param  int $post_id
     * @param  object $post
     */
    public function save_post( $post_id, $post ) {

        // Verify the nonce.
        if( ! isset( $_POST['cb_parallax_nonce'] ) || ! wp_verify_nonce($_POST['cb_parallax_nonce'], 'cb_parallax_nonce_field') ) {
            return;
        }

        // Get the post type object.
        $post_type = get_post_type_object($post->post_type);

        // Check if the current user has permission to edit the post.
        if( ! current_user_can($post_type->cap->edit_post, $post_id) ) {
            return $post_id;
        }

        // Don't save if the post is only a revision.
        if( 'revision' == $post->post_type ) {
            return;
        }

        // Parallax
        $meta['parallax_enabled'] = isset( $_POST['cbp_parallax_enabled'] ) ? $_POST['cbp_parallax_enabled'] : false;

        $meta['direction'] = in_array($_POST['cbp_direction'], $this->allowed['direction']) ? $_POST['cbp_direction'] : array_values($this->allowed['direction'])[0];
        // Sanitize the value for the color.
        $meta['background_color'] = ! empty( $_POST['cbp_background_color'] ) ? preg_replace('/[^0-9a-fA-F]/', '', $_POST['cbp_background_color']) : '';

        // Get the background image.
        $meta['background_image'] = isset( $_POST['cbp_background_image_location'] ) ? $_POST['cbp_background_image_location'] : '';

        // Make sure the background image attachment ID is an absolute integer.
        $meta['attachment_id'] = $_POST['cbp_background_image'] != '' ? absint($_POST['cbp_background_image']) : '';

        // Make sure the values have been white-listed. Otherwise, set the default value.
        $meta['background_repeat'] = in_array($_POST['cbp_background_repeat'], $this->allowed['repeat']) ? $_POST['cbp_background_repeat'] : array_values($this->allowed['repeat'])[0];

        $meta['position_x'] = in_array($_POST['cbp_position_x'], $this->allowed['position_x']) ? $_POST['cbp_position_x'] : array_values($this->allowed['position_x'])[1];

        $meta['position_y'] = in_array($_POST['cbp_position_y'], $this->allowed['position_y']) ? $_POST['cbp_position_y'] : array_values($this->allowed['position_y'])[1];

        $meta['background_attachment'] = in_array($_POST['cbp_background_attachment'], $this->allowed['attachment']) ? $_POST['cbp_background_attachment'] : array_values($this->allowed['attachment'])[0];

        $meta['vertical_scroll_direction'] = in_array($_POST['cbp_vertical_scroll_direction'], $this->allowed['vertical_scroll_direction']) ? $_POST['cbp_vertical_scroll_direction'] : array_values($this->allowed['vertical_scroll_direction'])[0];

        $meta['horizontal_scroll_direction'] = in_array($_POST['cbp_horizontal_scroll_direction'], $this->allowed['horizontal_scroll_direction']) ? $_POST['cbp_horizontal_scroll_direction'] : array_values($this->allowed['horizontal_scroll_direction'])[0];

        $meta['horizontal_alignment'] = in_array($_POST['cbp_horizontal_alignment'], $this->allowed['horizontal_alignment']) ? $_POST['cbp_horizontal_alignment'] : array_values($this->allowed['horizontal_alignment'])[0];

        $meta['vertical_alignment'] = in_array($_POST['cbp_vertical_alignment'], $this->allowed['vertical_alignment']) ? $_POST['cbp_vertical_alignment'] : array_values($this->allowed['vertical_alignment'])[0];

        $meta['overlay_image'] = in_array($_POST['cbp_overlay_image'], $this->allowed['overlay_image']) ? $_POST['cbp_overlay_image'] : array_values($this->allowed['overlay_image'])[0];

        $meta['overlay_opacity'] = in_array($_POST['cbp_overlay_opacity'], $this->allowed['overlay_opacity']) ? $_POST['cbp_overlay_opacity'] : array_values($this->allowed['overlay_opacity'])[3];

        $meta['overlay_color'] = ! empty( $_POST['cbp_overlay_color'] ) ? preg_replace( '/[^0-9a-fA-F]/', '', $_POST['cbp_overlay_color'] ) : '';

        // If an attachment is set...
        if( $meta['attachment_id'] != '' ) {

            $is_custom_header = get_post_meta($post->ID, '_wp_attachment_is_custom_background', true);

            // ...add the image to the pool of uploaded background images for this theme.
            if( $is_custom_header !== get_stylesheet() ) {
                update_post_meta($post_id, '_wp_attachment_is_custom_background', get_stylesheet());
            }
        }

        // Save data.
        update_post_meta($post_id, $this->meta_key, $meta);
    }

}
