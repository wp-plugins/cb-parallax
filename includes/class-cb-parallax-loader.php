<?php

/**
 * Register all actions and filters for the plugin.
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
class cb_parallax_loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array $actions
	 */
	private $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array $filters
	 */
	private $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    0.1.0
	 * @access   public
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 * @param      string $hook The name of the WordPress action that is being registered.
	 * @param      object $component A reference to the instance of the object on which the action is defined.
	 * @param      string $callback The name of the function definition on the $component.
	 * @param      int    $priority optional       The priority at which the function should be fired.
	 * @param      int    $accepted_args optional       The number of arguments that should be passed to the $callback.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {

		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 * @param      string $hook The name of the WordPress filter that is being registered.
	 * @param      object $component A reference to the instance of the object on which the filter is defined.
	 * @param      string $callback The name of the function definition on the $component.
	 * @param      int    $priority optional       The priority at which the function should be fired.
	 * @param      int    $accepted_args optional       The number of arguments that should be passed to the $callback.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {

		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   type     The collection of actions and filters registered with WordPress.
	 *
	 * @param      array  $hooks The collection of hooks that is being registered (that is, actions or filters).
	 * @param      string $hook The name of the WordPress filter that is being registered.
	 * @param      object $component A reference to the instance of the object on which the filter is defined.
	 * @param      string $callback The name of the function definition on the $component.
	 * @param      int    $priority optional       The priority at which the function should be fired.
	 * @param      int    $accepted_args optional       The number of arguments that should be passed to the $callback.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function run() {

		foreach( $this->filters as $hook ) {
			add_filter(
				$hook['hook'], array(
				$hook['component'],
				$hook['callback'],
			), $hook['priority'], $hook['accepted_args']
			);
		}

		foreach( $this->actions as $hook ) {
			add_action(
				$hook['hook'], array(
				$hook['component'],
				$hook['callback'],
			), $hook['priority'], $hook['accepted_args']
			);
		}
	}
}