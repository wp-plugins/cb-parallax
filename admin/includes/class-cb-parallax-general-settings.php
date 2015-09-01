<?php

/**
 * Enables the custom background support for the given post types.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_paraallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_general_settings {

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.2.1
     * @access   private
     * @var      string $plugin_domain
     */
    private $plugin_domain;

    /**
     * The name of the meta key for accessing post meta data.
     *
     * @since    0.2.1
     * @access   private
     * @var      string $meta_key
     */
    private $meta_key;

    /**
     * Adds the option to the general settings page.
     *
     * @since    0.2.1
     * @access   public
     *
     * @param    string $plugin_domain
     * @param    string $meta_key
     */
    public function __construct( $plugin_domain, $meta_key ) {

        $this->plugin_domain = $plugin_domain;
        $this->meta_key      = $meta_key;
    }

    /**
    *  Hooks the filter to "add" the option to the general settings page.
    *
    * @hooked_action
    *
    * @since  0.2.1
    * @access public
    * @return void
    */
    public function add_general_option() {

        add_filter('admin_init', array(&$this, 'register_field'));
    }

    /**
    *  Registers the option with WordPress.
    *
    * @callback
    *
    * @since  0.2.1
    * @access public
    * @return void
    */
    public function register_field() {

        register_setting( 'general', $this->meta_key );
        add_settings_field($this->meta_key, '<label class="cbp_general_setting_label" for="' . $this->meta_key . '">' . __('cbParallax', $this->plugin_domain) . '</label>', array(&$this, 'display_input_field'), 'general');
    }

    /**
    *  Displays the input field.
    *
    * @callback
    *
    * @since  0.2.1
    * @access public
    * @return void
    */
    public function display_input_field() {
        $value = get_option( $this->meta_key );

    ?>
        <!-- checkbox to preserve the scrolling behaviour -->
        <p class="cbp_general_setting_container cbp-parallax-enabled-container">
        <p class="label_for_<?php echo $this->meta_key ?>"><?php echo __('Preserve scrolling behaviour', $this->plugin_domain); ?></p>

        <label class="cbp-switch">
            <input type="checkbox" id="<?php echo $this->meta_key ?>" class="cbp-switch-input" name="<?php echo $this->meta_key ?>" value="1" <?php checked(1, isset($value) ? $value : false, true); ?>>
            <span class="cbp-switch-label" data-on="On" data-off="Off"></span>
            <span class="cbp-switch-handle"></span>
        </label>
        <!--</p>-->
        <!-- # checkbox to preserve the scrolling behaviour -->
    <?php
    }

}
