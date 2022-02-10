<?php

/**
 * @link       https://www.raulcarrion.com/
 * @since      1.0.0
 *
 * @package    Rc_Suite
 * @subpackage Rc_Suite/includes
 */

/**
 * @since      1.0.0
 * @package    Rc_Suite
 * @subpackage Rc_Suite/includes
 * @author     Raúl Carrión <hola@raulcarrion.com>
 */
class Rc_Suite {

	/**
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rc_Suite_Loader    $loader    
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    
	 */
	protected $version;

	/**
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'RC_SUITE_VERSION' ) ) {
			$this->version = RC_SUITE_VERSION;
		} else {
			$this->version = '1.1.8';
		}
		$this->plugin_name = 'rc-suite';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rc-suite-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rc-suite-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rc-suite-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rc-suite-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rc-reemplazador.php';

		$this->loader = new Rc_Suite_Loader();

	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rc_Suite_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Rc_Suite_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'rc_menu_plugin' );

		// Para el control de errores
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'rc_suite_admin_notices' );

		/*
		 *	AJAX
		 */
		$this->loader->add_action( 'wp_ajax_rcru-get-replacer-file-det', $plugin_admin, 'rcsu_ajx_replacer_get_file_details');
		$this->loader->add_action( 'wp_ajax_rcru-replacer-test-file', $plugin_admin, 'rcsu_ajx_test_file');
		$this->loader->add_action( 'wp_ajax_rcru-replacer-process-file', $plugin_admin, 'rcsu_ajx_process_file');

		/*
			OPCIONES DEL GENERAL
		*/
		if(get_option('rc_anti_publi_plugins_enabled')==1)
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'rc_anti_publi_plugins' );

		if(get_option('rc_divi_projects_disabled_enabled')==1)
			$this->loader->add_action( 'et_project_posttype_args', $plugin_admin, 'rc_divi_projects_disabled',10,1 );

		/*
			CSS ADMIN
		*/

		if (get_option('rc_parent_css_admin_enabled'))
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'rc_admin_style' );

	}

	/**
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Rc_Suite_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		if(get_option('rc_parent_css_enabled')==1)
		{
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'rc_parent_css' );
		}

		if(get_option('rc_anti_cache_css_enabled')==1)
		{
			$this->loader->add_action( 'style_loader_src', $plugin_public, 'rc_anti_cache_css' );
		}

		if(get_option('rc_login_customer_enabled')==1)
		{
			add_shortcode( 'rc_suite_login_customer', array( $plugin_public, 'rc_login_customer_sc' ));
			$this->loader->add_action('wp_nav_menu_items', $plugin_public, 'rc_login_customer',10,2 );
		}
		else
		{
			remove_shortcode( 'rc_suite_login_customer' );
		}

		// 	Ocultar el price range de woocommerce
		if(get_option('rcsu_woo_hide_price_range_enabled'))
		{
			$this->loader->add_filter( 'woocommerce_variable_sale_price_html', $plugin_public, 'rcsu_hide_variable_product_price', 10, 2 );
			$this->loader->add_filter( 'woocommerce_variable_price_html', $plugin_public ,'rcsu_hide_variable_product_price', 10, 2 );		
		}

		// SEO - BLOG
		if(get_option('rcsu_seo_blog_enabled'))
		{
			$this->loader->add_action( 'generate_rewrite_rules', $plugin_public, 'rcsu_seo_blog_posts_add_rewrite_rules',2 );
			$this->loader->add_filter( 'post_link', $plugin_public ,'rcsu_seo_blog_posts_change_blog_links', 10, 2 );		
			$this->loader->add_filter( 'category_link', $plugin_public ,'rcsu_seo_blog_posts_change_category_links', 10, 2 );	
			$this->loader->add_filter( 'template_redirect', $plugin_public , 'redirect_old_urls', 10, 2 );	
			
			if (in_array('wordpress-seo/wp-seo.php', apply_filters('active_plugins', get_option('active_plugins')))) {
				$this->loader->add_filter( 'wpseo_canonical', $plugin_public , 'yoast_remove_canonical_items', 10, 2 );	
			}
		
		}

		// REMOVE WEB FROM COMMENTS
		if(get_option('rcsu_remove_web_comments_enabled'))
		{
			
			$this->loader->add_filter('comment_moderation_text', $plugin_public,'rc_filter_comment_text', 10, 2 );
			$this->loader->add_filter('comment_notification_text', $plugin_public,'rc_filter_comment_text', 10, 2 );
			$this->loader->add_filter('comment_form_default_fields',$plugin_public,'rc_disable_url_comment');
			$this->loader->add_filter('get_comment_author_link', $plugin_public,'rc_disable_comment_author_links' );

			/* Desactiva los enlaces en el texto de los comentarios */
			remove_filter('comment_text', 'make_clickable', 9);

			$this->loader->add_filter('preprocess_comment', $plugin_public,'rc_comment_post', '', 1);
			$this->loader->add_filter('comment_text', $plugin_public,'rc_comment_display', '', 1);
			$this->loader->add_filter('comment_text_rss', $plugin_public,'rc_comment_display', '', 1);
			$this->loader->add_filter('comment_excerpt', $plugin_public,'rc_comment_display', '', 1);

			if (in_array('wordpress-seo/wp-seo.php', apply_filters('active_plugins', get_option('active_plugins')))) {
				$this->loader->add_filter( 'wpseo_canonical', $plugin_public , 'yoast_remove_canonical_items', 10, 2 );	
			}
		}

		if(get_option('rcsu_gtag_enabled'))
		{
			$this->loader->add_action('wp_body_open', $plugin_public, 'rc_add_gtag_head',10,2 );
			$this->loader->add_action('wp_head', $plugin_public, 'rc_add_gtag_head',10,2 );
		}
	}

	/**
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * @since     1.0.0
	 * @return    string   
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * @since     1.0.0
	 * @return    Rc_Suite_Loader  
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * @since     1.0.0
	 * @return    string  
	 */
	public function get_version() {
		return $this->version;
	}	
}