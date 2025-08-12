<?php

// Exit if accessed directly.
if (!defined("ABSPATH")) {
	exit();
}

/**
 * Initial setup for this theme
 *
 */
class Felan_Init
{
	private $linkedin_access_token;
	private $linkedin_user_datas;
	private $linkedin_email_datas;
	/**
	 * The constructor.
	 */
	function __construct()
	{
		// class Felan_Helper
		new Felan_Helper();

		// class Felan_Enqueue
		new Felan_Enqueue();

		// class Felan_Kirki
		new Felan_Kirki();

		// class Felan_Customizer
		new Felan_Customizer();

		$app_id = Felan_Helper::felan_get_option(
			"linkedin_client_id",
		);
		$app_secret = Felan_Helper::felan_get_option(
			"linkedin_client_secret",
		);
		$callback = home_url('/');
		$scopes = "openid profile email";
		$ssl = false; //TRUE FOR PRODUCTION ENV.

		// Load the theme's textdomain.
		add_action("after_setup_theme", [$this, "load_theme_textdomain"]);

		// Register navigation menus.
		add_action("after_setup_theme", [$this, "register_nav_menus"]);

		// Add theme supports.
		add_action("after_setup_theme", [$this, "add_theme_supports"]);

		// Add theme background setup.
		add_action("after_setup_theme", [$this, "theme_custom_background_setup"]);

		// Add theme background setup.
		add_action("after_setup_theme", [$this, "theme_custom_header_setup"]);

		// Register nav menu.
		add_action("after_setup_theme", [$this, "register_menus"]);

		add_action('customize_register', [$this, "theme_remove_customizer_options"]);

		// Register widget areas.
		add_action("widgets_init", [$this, "widgets_init"]);

		// Register head template.
		add_action("wp_body_open", [$this, "loading_effect"], 9999);

		// Register footer template.
		add_action("wp_footer", [$this, "global_template"]);

		// Support editor style.
		add_editor_style(["/assets/css/editor-style.css"]);

		// Process User
		if (!is_admin() && $app_id != '' && $app_secret != '') {
			new Felan_LinkedIn($app_id, $app_secret, $callback, $scopes, $ssl);
			add_action("init", [$this, "process_user_linkedin"]);
		}
	}

	public function theme_custom_background_setup()
	{
		$args = array(
			'default-color' => 'ffffff',
			'default-image' => '',
			'wp-head-callback' => '_custom_background_cb',
		);
		add_theme_support('custom-background', $args);
	}

	public function theme_custom_header_setup()
	{
		$args = array(
			'width'              => 1600,
			'height'             => 400,
			'flex-height'        => true,
			'flex-width'         => true,
			'header-text'        => false,
		);
		add_theme_support('custom-header', $args);
	}

	public function theme_remove_customizer_options($wp_customize)
	{
		$wp_customize->remove_section('header_image');
		$wp_customize->remove_section('background_image');
	}

	/**
	 * Registers the Menus.
	 *
	 * @access public
	 */
	public function register_nav_menus()
	{
		// This theme uses wp_nav_menu() in one location.
		register_nav_menus([
			"primary" => esc_html__("Primary", "felan"),
		]);
	}

	/**
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 *
	 * @access public
	 */
	public function load_theme_textdomain()
	{
		load_theme_textdomain("felan", FELAN_THEME_DIR . "/languages");
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @access public
	 */
	function add_theme_supports()
	{
		/*
		 * Add default posts and comments RSS feed links to head.
		 */
		add_theme_support("automatic-feed-links");

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support("title-tag");

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support("post-thumbnails");

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support("html5", [
			"search-form",
			"comment-form",
			"comment-list",
			"gallery",
			"caption",
		]);

		/**
		 * Add post-formats support.
		 */
		add_theme_support(
			'post-formats',
			array(
				'link',
				'aside',
				'image',
				'quote',
				'video',
			)
		);

		/*
		 * Support selective refresh for widget
		 */
		add_theme_support("customize-selective-refresh-widgets");

		/*
		 * wp-block-styles
		 */
		add_theme_support("wp-block-styles");

		/*
		 * responsive-embeds
		 */
		add_theme_support("responsive-embeds");

		// Add support for full and wide align images.
		add_theme_support('align-wide');

		/*
		 * custom-logo
		 */
		$logo_width  = 300;
		$logo_height = 100;
		add_theme_support(
			'custom-logo',
			array(
				'height'               => $logo_height,
				'width'                => $logo_width,
				'flex-width'           => true,
				'flex-height'          => true,
				'unlink-homepage-logo' => true,
			)
		);

		/*
		 * Optimize speed for homepage
		 */
		add_theme_support("felan");
	}

	/**
	 * Register nav menu.
	 */
	function register_menus()
	{
		register_nav_menus([
			"primary" => esc_html__("Primary Menu", "felan"),
		]);

		register_nav_menus([
			"main_menu" => esc_html__("Main Menu", "felan"),
		]);

		register_nav_menus([
			"mobile_menu" => esc_html__("Mobile Menu", "felan"),
		]);
	}

	/**
	 * Register widget area.
	 *
	 * @access public
	 * @link   https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 */
	function widgets_init()
	{
		register_sidebar([
			"id" => "sidebar",
			"name" => esc_html__("Sidebar", "felan"),
			"description" => esc_html__("Add widgets here.", "felan"),
			"before_widget" => '<section id="%1$s" class="widget %2$s">',
			"after_widget" => "</section>",
			"before_title" => '<h3 class="widget-title">',
			"after_title" => "</h3>",
		]);
		register_sidebar([
			"id" => "jobs_sidebar",
			"name" => esc_html__("Jobs Sidebar", "felan"),
			"description" => esc_html__("Add widgets here.", "felan"),
			"before_widget" => '<section id="%1$s" class="widget %2$s">',
			"after_widget" => "</section>",
			"before_title" => '<h3 class="widget-title">',
			"after_title" => "</h3>",
		]);
		register_sidebar([
			"id" => "company_sidebar",
			"name" => esc_html__("Company Sidebar", "felan"),
			"description" => esc_html__("Add widgets here.", "felan"),
			"before_widget" => '<section id="%1$s" class="widget %2$s">',
			"after_widget" => "</section>",
			"before_title" => '<h3 class="widget-title">',
			"after_title" => "</h3>",
		]);
		register_sidebar(array(
			'id'            => 'freelancer_sidebar',
			'name'          => esc_html__('Freelancer Sidebar', 'felan'),
			'description'   => esc_html__('Add widgets here.', 'felan'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
		register_sidebar(array(
			'id'            => 'service_sidebar',
			'name'          => esc_html__('Service Sidebar', 'felan'),
			'description'   => esc_html__('Add widgets here.', 'felan'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
		register_sidebar(array(
			'id'            => 'project_sidebar',
			'name'          => esc_html__('Project Sidebar', 'felan'),
			'description'   => esc_html__('Add widgets here.', 'felan'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
	}

	/**
	 * Register global template
	 */
	function loading_effect()
	{
		get_template_part("templates/global/site-loading");
	}

	/**
	 * Register global template
	 */
	function global_template()
	{
		get_template_part("templates/global/account");

		get_template_part("templates/global/canvas-search");
	}

	/**
	 * Process User Linkedin.
	 *
	 * @access public
	 */
	public function process_user_linkedin()
	{
		// If this is a user sign-in request, but the user denied granting access, redirect to login URL
		if (isset($_REQUEST['error']) && $_REQUEST['error'] == 'access_denied') {
			wp_redirect(home_url('/'));
		}

		if (isset($_GET['code']) && $_GET['code'] != '' && !isset($_GET['oauth2callback'])) {
			$this->linkedin_access_token = Felan_LinkedIn::getAccessToken(Felan_Helper::felan_clean(wp_unslash($_GET['code'])));
			$this->linkedin_user_datas = Felan_LinkedIn::getPerson($this->linkedin_access_token);

			$this->login_user_linkedin();
			$this->create_user_linkedin();

			$redirect_url  = get_page_link(Felan_Helper::felan_get_option('felan_freelancer_dashboard_page_id'));
			if (!$redirect_url) {
				$redirect_url = home_url('/');
			}
			wp_redirect($redirect_url);
		}
	}

	/**
	 * Login User Linkedin.
	 *
	 * @access public
	 */
	public function login_user_linkedin()
	{
		$user_id = isset($this->linkedin_user_datas['sub']) ? $this->linkedin_user_datas['sub'] : '';

		// We look for the `eo_linkedin_id` to see if there is any match
		$wp_users = get_users(array(
			'number' => 1,
			'count_total' => false,
			'fields' => 'ids',
			'meta_query' => array(
				array(
					'key' => 'linkedin_user_id',
					'value' => $user_id,
					'compare' => "=",
				)
			)
		));

		if (empty($wp_users[0])) {
			return false;
		}

		wp_set_auth_cookie($wp_users[0]);

		do_action('after_linkedin_login', $wp_users[0]);

		$redirect_url  = get_page_link(Felan_Helper::felan_get_option('felan_freelancer_dashboard_page_id'));
		if (!$redirect_url) {
			$redirect_url = home_url('/');
		}
		wp_redirect($redirect_url);
	}

	/**
	 * Create User Linkedin.
	 *
	 * @access public
	 */
	public function create_user_linkedin()
	{
		$linkedin_user_id = isset($this->linkedin_user_datas['sub']) ? $this->linkedin_user_datas['sub'] : '';

		$first_name = $last_name = '';

		if (!empty($this->linkedin_user_datas['given_name'])) {
			$first_name = $this->linkedin_user_datas['given_name'];
		}

		if (!empty($this->linkedin_user_datas['family_name'])) {
			$last_name = $this->linkedin_user_datas['family_name'];
		}

		$email = isset($this->linkedin_user_datas['email']) ? $this->linkedin_user_datas['email'] : '';

		$wp_user = get_user_by('email', $email);

		if (!empty($wp_user->ID)) {
			update_user_meta($wp_user->ID, 'linkedin_user_id', $linkedin_user_id);
			$this->login_user_linkedin();
		}

		if (!empty($first_name) && !empty($last_name)) {
			$name = $first_name . '_' . $last_name;
			$name = str_replace(array(' '), array('_'), $name);
			$username = sanitize_user(str_replace(' ', '_', strtolower($name)));
		} else {
			$username = $email;
		}

		if (username_exists($username)) {
			$username .= '_' . rand(100000, 999999);
		}

		$userdata = array(
			'user_login' => sanitize_user($username),
			'user_email' => sanitize_email($email),
			'user_pass' => wp_generate_password(),
			'account_type' => 'felan_user_freelancer',
		);

		$user_id = wp_insert_user($userdata);

		if (!is_wp_error($user_id)) {
			update_user_meta($user_id, 'first_name', $first_name);
			update_user_meta($user_id, 'last_name', $last_name);
			update_user_meta($user_id, 'linkedin_user_id', $linkedin_user_id);
			update_user_meta($user_id, 'linkedin_access_token', $this->linkedin_access_token, true);
			do_action('after_linkedin_login', $user_id);
			wp_set_auth_cookie($user_id);
		} else {
			set_transient('linkedin_message', $user_id->get_error_message(), 60 * 60 * 24 * 30);
			echo esc_html($user_id->get_error_message());
			die;
		}
	}
}
