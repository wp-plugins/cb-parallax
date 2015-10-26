<?php

/**
 * Define the internationalization functionality
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
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
class cb_parallax_i18n {

	/**
	 * The domain specified for this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @return   void
	 * @access   public
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_domain, FALSE, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Set the domain equal to that of the specified domain.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @param    string $plugin_domain
	 * @return   void
	 */
	public function set_domain( $plugin_domain ) {

		$this->plugin_domain = $plugin_domain;
	}
}
