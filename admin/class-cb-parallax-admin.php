<?php

/**
 * The admin part of the plugin.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_admin {

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
     * The loader that's responsible for maintaining and registering all hooks that power the plugin.
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
     * Kicks off the admin part of the plugin.
     *
     * 1. Loads the dependencies the admin part relies on.
     * 2. Defines post type support.
     * 3. Defines the functionality for the custom backgrounds.
     * 4. Checks if the theme supports custom backgrounds and the user's permission to interact with this plugin.
     * 5. Defines the meta box.
     *
     * @since    0.1.0
     * @access   public
     *
     * @param    string $plugin_name
     * @param    string $plugin_domain
     * @param    string $plugin_version
     * @param    object $loader
     * @param    string $meta_key
     */
    public function __construct( $plugin_name, $plugin_domain, $plugin_version, $loader, $meta_key ) {

        $this->plugin_name = $plugin_name;
        $this->plugin_domain = $plugin_domain;
        $this->plugin_version = $plugin_version;
        $this->loader = $loader;
        $this->meta_key = $meta_key;

        $this->load_dependencies();
        $this->define_post_type_support();
        $this->define_admin_localisation();
        $this->define_custom_background();
        $this->define_help_tab();
        $this->define_general_setting();
    }

    /**
     * Loads the initial files needed by the admin part of the plugin and assigns the loader object.
     *
     * The class responsible for orchestrating the actions and filters of the core plugin.
     * The class responsible for the post type support.
     * The class responsible for the localisation of the admin part.
     * The class responsible for setting up the custom background.
     * The class responsible for the help tab.
     * The classes responsible for the meta box.
     *
     * @since    0.1.0
     * @access   public
     * @return   void
     */
    public function load_dependencies() {

        require_once plugin_dir_path(dirname(__FILE__)) . "admin/includes/class-cb-parallax-post-type-support.php";

        require_once plugin_dir_path(dirname(__FILE__)) . "admin/includes/class-cb-parallax-admin-localisation.php";

        require_once plugin_dir_path(dirname(__FILE__)) . "admin/includes/class-cb-parallax-custom-background.php";

        require_once plugin_dir_path(dirname(__FILE__)) . "admin/includes/class-cb-parallax-help-tab.php";

        require_once plugin_dir_path(dirname(__FILE__)) . "admin/includes/class-cb-parallax-meta-box.php";

        require_once plugin_dir_path(dirname(__FILE__)) . "admin/includes/class-cb-parallax-general-settings.php";
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @hooked_action
     *
     * @since    0.1.0
     * @param    string $hook_suffix
     * @return   void
     */
    public function enqueue_styles( $hook_suffix ) {

        if( ! in_array($hook_suffix, array( 'post-new.php', 'post.php', 'options-general.php' )) || ! current_user_can('cb_parallax_edit') ) {
            return;
        }

        // Color picker.
        wp_enqueue_style('wp-color-picker');

        wp_enqueue_style(
            $this->plugin_name . '-admin-css',
            plugin_dir_url(__FILE__) . 'css/admin.css',
            array(),
            $this->plugin_version,
            'all'
        );
    }

    /**
     * Registers and enqueues the script for the admin part of the plugin.
     *
     * @hooked_action
     *
     * But first we check if we're in the right spot and
     * if the current user owns the capability needed to interact with this plugin.
     * If so, the script for the admin part gets registered and enqueued, and that script then gets localized.
     * Finally, we enqueue the color picker.
     *
     * @since    0.1.0
     * @access   public
     * @return   void
     *
     * @param    $hook_suffix
     */
    public function enqueue_scripts( $hook_suffix ) {

        if( ! in_array($hook_suffix, array( 'post-new.php', 'post.php' )) || ! current_user_can('cb_parallax_edit') ) {
            return;
        }

        // Color picker.
        wp_enqueue_script('wp-color-picker');

        // Media Frame.
        wp_enqueue_script('media-views');

        // Fancy Select.
        wp_enqueue_script(
            $this->plugin_name . '-inc-fancy-select-js',
            plugin_dir_url(__FILE__) . '../vendor/fancy-select/fancySelect.js',
            array( 'jquery' ),
            $this->plugin_version,
            true
        );

        // Admin part.
        wp_enqueue_script(
            $this->plugin_name . '-admin-js',
            plugin_dir_url(__FILE__) . 'js/admin.js',
            array(
                'jquery',
                'wp-color-picker',
                'media-views',
                $this->plugin_name . '-inc-fancy-select-js'
            ),
            $this->plugin_version,
            true
        );
    }

    /**
     * Defines the functionality for editing the custom background -
     * if the current user has the capability to do so.
     *
     * @hooked_action
     *
     * @since    0.1.0
     * @access   public
     * @return   void
     */
    public function check_cap() {

        if( ! current_user_can('cb_parallax_edit') ) {
            return;
        }

        $this->define_meta_box();
    }

    /**
     * Registers the action to execute on the object regarding the post type support.
     *
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function define_post_type_support() {

        $wp_support = new cb_parallax_post_type_support();

        $this->loader->add_action('init', $wp_support, 'add_post_type_support');
    }

    /**
     * Instanciates the class responsible for setting up the custom background.
     *
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function define_custom_background() {

        $custom_background = new cb_parallax_custom_background($this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version(), $this->get_meta_key());

        $this->loader->add_action('after_setup_theme', $custom_background, 'add_theme_support', 95);
    }

    /**
     * Instanciates the class responsible for displaying the meta box.
     *
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function define_meta_box() {

        $meta_box = new cb_parallax_meta_box($this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version(), $this->get_meta_key());

        $this->loader->add_action('add_meta_boxes', $meta_box, 'add_meta_box');
    }

    /**
     * Instanciates the class responsible localizing the admin part of the plugin.
     *
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function define_admin_localisation() {

        $admin_localisation = new cb_parallax_admin_localisation($this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version(), $this->get_meta_key());

        $this->loader->add_action('admin_enqueue_scripts', $admin_localisation, 'get_background_options', 1);
        $this->loader->add_action('admin_enqueue_scripts', $admin_localisation, 'localize_meta_box', 1000);
        $this->loader->add_action('admin_enqueue_scripts', $admin_localisation, 'localize_media_frame', 1000);
    }

    /**
     * Instanciates the class responsible for registering and displaying the "general option".
     *
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function define_general_setting() {

        $general_settings = new cb_parallax_general_settings($this->get_plugin_domain(), $this->get_meta_key());

        $this->loader->add_action('admin_init', $general_settings, 'add_general_option', 1);
    }

    /**
     * Instanciates the class responsible for displaing the help tab.
     *
     * @since  0.1.0
     * @see    admin/includes/class-nsr-help-tab.php
     * @access private
     * @return void
     */
    private function define_help_tab() {

        // Show up on all following post type's edit screens:
        if( ( isset( $_REQUEST['page'] ) || isset( $_REQUEST['post'] ) || isset( $_REQUEST['product'] ) ) && isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'edit' ) {

            $Help_Tab = new cb_parallax_help_tab($this->get_plugin_domain());

            $this->loader->add_action('in_admin_header', $Help_Tab, 'add_cbp_help_tab', 20);

            $this->loader->add_action('load-post.php', $Help_Tab, 'add_cbp_help_tab', 10);
            $this->loader->add_action('load-post-new.php', $Help_Tab, 'add_cbp_help_tab', 11);
            $this->loader->add_action("load-{$GLOBALS['pagenow']}", $Help_Tab, 'add_cbp_help_tab', 12);
        }
    }

    /**
     * Adds support, rating, and donation links to the plugin row meta on the plugins admin screen.
     *
     * @hooked_action
     *
     * @since    0.1.0
     * @access   public
     * @return   array
     *
     * @param    array $meta
     * @param    string $file
     */
    public function plugin_row_meta( $meta, $file ) {

        $plugin = plugin_basename('cb-parallax/cb-parallax.php');

        if( $file == $plugin ) {
            $meta[] = '<a href="https://github.com/demispatti/cb-parallax" target="_blank">' . __('Plugin support', $this->plugin_domain) . '</a>';
            $meta[] = '<a href="http://wordpress.org/plugins/cb-parallax" target="_blank">' . __('Rate plugin', $this->plugin_domain) . '</a>';
            $meta[] = '<a href="http://demispatti.ch/plugins" target="_blank">' . __('Donate', $this->plugin_domain) . '</a>';
        }

        return $meta;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.1.0
     * @access   public
     * @return   void
     */
    public function run() {

        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.1.0
     * @access    public
     * @return    string $plugin_name
     */
    public function get_plugin_name() {

        return $this->plugin_name;
    }

    /**
     * The domain of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.1.0
     * @access    public
     * @return    string $plugin_domain
     */
    public function get_plugin_domain() {

        return $this->plugin_domain;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.1.0
     * @access    public
     * @return    string $plugin_version
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
