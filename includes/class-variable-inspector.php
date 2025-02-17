<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://bowo.io
 * @since      1.0.0
 *
 * @package    Variable_Inspector
 * @subpackage Variable_Inspector/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Variable_Inspector
 * @subpackage Variable_Inspector/includes
 * @author     Bowo <hello@bowo.io>
 */
class Variable_Inspector {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Variable_Inspector_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'VARIABLE_INSPECTOR_VERSION' ) ) {
			$this->version = VARIABLE_INSPECTOR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'variable-inspector';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Variable_Inspector_Loader. Orchestrates the hooks of the plugin.
	 * - Variable_Inspector_i18n. Defines internationalization functionality.
	 * - Variable_Inspector_Admin. Defines all hooks for the admin area.
	 * - Variable_Inspector_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-variable-inspector-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-variable-inspector-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-variable-inspector-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-variable-inspector-public.php';

		$this->loader = new Variable_Inspector_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Variable_Inspector_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Variable_Inspector_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Check if current screen is this plugin's main page
	 *
	 * @since 1.0.0
	 */
	public function is_vi() {

		// e.g. https://www.domain.com/wp-admin/tools.php?page=simple-file-manager
		$request_uri = sanitize_text_field( $_SERVER['REQUEST_URI'] );

		if ( strpos( $request_uri, 'tools.php?page=' . $this->plugin_name ) !== false ) {

			return true; // Yes, this is the plugin's main page

		} else {

			return false; // No, this is not the plugin's main page

		}

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Variable_Inspector_Admin( $this->get_plugin_name(), $this->get_version() );

		if ( is_admin() && $this->is_vi() ) {

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

			// Update footer text
			$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'vi_footer_text' );

		}

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'vi_register_admin_menu');

		$this->loader->add_filter( 'plugin_action_links_'.$this->plugin_name.'/'.$this->plugin_name.'.php', $plugin_admin, 'vi_plugin_action_links' );

		$this->loader->add_action( 'inspect', $plugin_admin, 'vi_inspect_variable' );

		$this->loader->add_action( 'wp_ajax_vi_clear_results', $plugin_admin, 'vi_clear_results' );

		$this->loader->add_action( 'wp_ajax_vi_set_viewer', $plugin_admin, 'vi_set_viewer' );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'vi_suppress_admin_notices', 0 ); // Load early with priority 5 (default is 10)
		$this->loader->add_action( 'all_admin_notices', $plugin_admin, 'vi_suppress_all_admin_notices', 0 ); // Load early with priority 5 (default is 10)

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Variable_Inspector_Public( $this->get_plugin_name(), $this->get_version() );

		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Variable_Inspector_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
