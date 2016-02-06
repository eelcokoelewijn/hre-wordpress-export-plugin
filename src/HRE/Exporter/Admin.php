<?php
namespace HRE\Exporter;

use HRE\Exporter;
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Admin {
	///Key used for storing product id and name in cache
	private static $productCacheKey = "hre_exporter_key";

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, Exporter::pluginURL('/admin/css/hre-exporter-admin.css'), array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, Exporter::pluginURL('/admin/js/hre-exporter-admin.js'), array( 'jquery' ), $this->version, false );
	}

	public function register_menu()
	{
		//woocommerce
		add_submenu_page('woocommerce', __('Registration Export',$this->plugin_name), __('Registration Export',$this->plugin_name), 'manage_options', 'hre-registration-export-page', array( $this, 'render' ) );
	}

	public function render()
	{
		include_once(Exporter::absolutePath('/admin/partials/hre-exporter-admin-display.php') );
	}

	/**
	 * Function that returns an array containing the IDs of all the products.
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function hre_get_product_ids($categoryId = nil) {
	  // Load from cache
		$product_ids = get_transient( Admin::$productCacheKey );

		// Valid cache found
		if ( false !== $product_ids )
			return $product_ids;

		$products = get_posts( array(
			'post_type'      => array( 'product', 'product_variation' ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields' => 'id,post_name'
		) );

		$product_ids = array();
		foreach ($products as $product) {
			$product_ids[$product->ID] = $product->post_name;
		}

		set_transient( Admin::$productCacheKey, $product_ids );

		return $product_ids;
	}
}
