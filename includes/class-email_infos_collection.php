<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://grdtest.com:81
 * @since      1.0.0
 *
 * @package    Email_infos_collection
 * @subpackage Email_infos_collection/includes
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
 * @package    Email_infos_collection
 * @subpackage Email_infos_collection/includes
 * @author     grdtest.com:81 <447494332@qq.com>
 */
class Email_infos_collection
{
	protected $loader;
	protected $plugin_name;
	protected $version;
	public function __construct()
	{
		if (defined('EMAIL_INFOS_COLLECTION_VERSION')) {
			$this->version = EMAIL_INFOS_COLLECTION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'email_infos_collection';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies()
	{

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-email_infos_collection-loader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-email_infos_collection-i18n.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-email_infos_collection-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-email_infos_collection-public.php';

		$this->loader = new Email_infos_collection_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Email_infos_collection_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Email_infos_collection_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Email_infos_collection_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('rest_api_init', $plugin_admin, 'ajax_rest_api_init'); // 注册ajax 请求
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu'); // 注册菜单
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_submenu'); // 注册子菜单
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Email_infos_collection_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 20);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Email_infos_collection_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
