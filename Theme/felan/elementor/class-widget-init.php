<?php

namespace Felan_Elementor;

use Elementor\Plugin;

defined('ABSPATH') || exit;

class Widget_Init
{

	private static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function initialize()
	{
		add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
		add_action('elementor/element/after_add_attributes', [$this, 'add_elementor_attribute']);

		// Registered Widgets.
		add_action('elementor/widgets/register', [$this, 'init_widgets']);
		//add_action( 'elementor/widgets/register', [ $this, 'remove_unwanted_widgets' ], 15 );

		add_action('elementor/frontend/after_register_scripts', [$this, 'after_register_scripts']);

		add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);

		// Modify original widgets settings.
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/modify-base.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/section.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/column.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/accordion.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/animated-headline.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/counter.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/form.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/heading.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/icon-box.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/progress.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/original/countdown.php';
	}

	/**
	 * Register scripts for widgets.
	 */
	public function after_register_scripts()
	{
		// Fix Wordpress old version not registered this script.
		if (!wp_script_is('imagesloaded', 'registered')) {
			wp_register_script('imagesloaded', FELAN_THEME_URI . '/assets/libs/imagesloaded/imagesloaded.min.js', array('jquery'), null, true);
		}

		wp_register_script('circle-progress', FELAN_THEME_URI . '/assets/libs/circle-progress/circle-progress.min.js', array('jquery'), null, true);
		wp_register_script('felan-widget-circle-progress', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-circle-progress.js', array(
			'jquery',
			'circle-progress',
		), null, true);

		wp_register_script('felan-swiper-wrapper', FELAN_THEME_URI . '/assets/js/swiper-wrapper.js', array('jquery'), FELAN_THEME_VER, true);
		wp_register_script('felan-group-widget-carousel', FELAN_ELEMENTOR_URI . '/assets/js/widgets/group-widget-carousel.js', array(
			'jquery',
			'felan-swiper',
			'felan-swiper-wrapper',
		), null, true);
		$felan_swiper_js = array(
			'prevText' => esc_html__('Prev', 'felan'),
			'nextText' => esc_html__('Next', 'felan'),
		);
		wp_localize_script('felan-swiper-wrapper', '$felanSwiper', $felan_swiper_js);

		wp_register_script('scrollmonitor', FELAN_ELEMENTOR_URI . '/assets/libs/scrollmonitor/scrollmonitor.min.js', array('jquery'), null, true);

		wp_register_script('anime', FELAN_ELEMENTOR_URI . '/assets/libs/anime/anime.min.js', array('jquery'), null, true);

		wp_register_script('felan-grid-query', FELAN_ELEMENTOR_URI . '/assets/js/widgets/grid-query.min.js', array('jquery'), null, true);

		wp_register_script('felan-widget-modern-menu', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-modern-menu.js', array('jquery'), null, true);
		wp_register_script('felan-widget-modern-tabs', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-modern-tabs.js', array('jquery'), null, true);

		wp_register_script('felan-widget-grid-post', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-grid-post.js', array('felan-grid-layout'), null, true);
		wp_register_script('felan-group-widget-grid', FELAN_ELEMENTOR_URI . '/assets/js/widgets/group-widget-grid.js', array('felan-grid-layout'), null, true);

		wp_register_script('felan-widget-google-map', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-google-map.js', array('jquery'), null, true);

		wp_register_script('felan-widget-testimonial-carousel', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-testimonial.js', array(
			'jquery',
		), null, true);

		wp_register_script('felan-widget-list', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-list.js', array(
			'jquery',
		), null, true);

		wp_register_script('felan-widget-user-form', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-user-form.js', array(
			'jquery',
		), null, true);

		wp_register_script('felan-social-networks', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-social-networks.js', array(
			'jquery',
		), null, true);

		wp_register_script('felan-widget-flip-box', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-flip-box.js', array(
			'jquery',
			'imagesloaded',
		), null, true);

		wp_register_script('typed', FELAN_ELEMENTOR_URI . '/assets/libs/typed/typed.min.js', array('jquery'), null, true);
		wp_register_script('vivus', FELAN_ELEMENTOR_URI . '/assets/libs/vivus/vivus.min.js', array('jquery'), null, true);
		wp_register_script('felan-widget-fancy-heading', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-fancy-heading.js', array(
			'jquery',
			'typed',
		), null, true);

		wp_register_script('felan-widget-accordion', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-accordion.js', array(
			'jquery',
		), null, true);

		wp_register_script('felan-widget-accordion-image', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-accordion-image.js', array(
			'jquery',
		), null, true);

		wp_register_script('felan-widget-morphing', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-morphing.js', array(
			'jquery',
		), null, true);

		wp_register_script('felan-widget-client-logo-animation', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-client-logo-animation.js', array(
			'jquery',
		), null, true);

		wp_register_script('felan-widget-gallery-justified-content', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-gallery-justified-content.js', array(
			'justifiedGallery',
		), null, true);

		wp_register_script('countdown', FELAN_ELEMENTOR_URI . '/assets/libs/jquery.countdown/js/jquery.countdown.min.js', array('jquery'), FELAN_THEME_VER, true);
	}

	/**
	 * enqueue scripts in editor mode.
	 */
	public function enqueue_editor_scripts()
	{
		wp_enqueue_script('felan-widget-editor', FELAN_ELEMENTOR_URI . '/assets/js/editor.js', array('jquery'), null, true);
	}

	/**
	 * @param \Elementor\Elements_Manager $elements_manager
	 *
	 * Add category.
	 */
	function add_elementor_widget_categories($elements_manager)
	{
		$elements_manager->add_category('felan', [
			'title' => esc_html__('Felan', 'felan'),
			'icon'  => 'fa fa-plug',
		]);
	}

	/**
	 * @param \Elementor\Elements_Manager $element_base
	 *
	 * Add attribute.
	 */
	function add_elementor_attribute($element_base)
	{
		$settings = $element_base->get_settings_for_display();

		$_animation = !empty($settings['_animation']);
		$animation = !empty($settings['animation']);
		$has_animation = $_animation && 'none' !== $settings['_animation'] || $animation && 'none' !== $settings['animation'];

		if ($has_animation) {
			$is_static_render_mode = Plugin::$instance->frontend->is_static_render_mode();

			$felan_effect = array(
				'FelanSlideInDown',
				'FelanSlideInLeft',
				'FelanSlideInRight',
				'FelanSlideInUp',
				'FelanBottomToTop',
				'FelanSpin',
				'FelanMoving01',
				'FelanMoving02',
				'FelanMoving03',
				'FelanMoving04',
				'FelanMoving05',
			);

			$felan_current_effect = $felan_animation = '';
			if (!empty($settings['animation'])) {
				$felan_animation = $settings['animation'];
			} elseif (!empty($settings['_animation'])) {
				$felan_animation = $settings['_animation'];
			}

			if (!empty($felan_animation)) {
				if ($felan_animation == 'FelanSlideInDown') {
					$felan_current_effect = 'felan-slide-in-down';
				} elseif ($felan_animation == 'FelanSlideInLeft') {
					$felan_current_effect = 'felan-slide-in-left';
				} elseif ($felan_animation == 'FelanSlideInRight') {
					$felan_current_effect = 'felan-slide-in-right';
				} elseif ($felan_animation == 'FelanSlideInUp') {
					$felan_current_effect = 'felan-slide-in-up';
				} elseif ($felan_animation == 'FelanBottomToTop') {
					$felan_current_effect = 'felan-bottom-to-top';
				} elseif ($felan_animation == 'FelanSpin') {
					$felan_current_effect = 'felan-spin';
				} elseif ($felan_animation == 'FelanMoving01') {
					$felan_current_effect = 'felan-moving-01';
				} elseif ($felan_animation == 'FelanMoving02') {
					$felan_current_effect = 'felan-moving-02';
				} elseif ($felan_animation == 'FelanMoving03') {
					$felan_current_effect = 'felan-moving-03';
				} elseif ($felan_animation == 'FelanMoving04') {
					$felan_current_effect = 'felan-moving-04';
				} elseif ($felan_animation == 'FelanMoving05') {
					$felan_current_effect = 'felan-moving-05';
				}

				if (!$is_static_render_mode && in_array($felan_animation, $felan_effect)) {
					// Hide the element until the animation begins
					$element_base->add_render_attribute('_wrapper', 'class', ['felan-elementor-loading', $felan_current_effect]);
				}
			}
		}
	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since  1.0.0
	 *
	 * @access public
	 */
	public function init_widgets()
	{

		// Include Widget files.
		require_once FELAN_ELEMENTOR_DIR . '/module-query.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/base.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/form/form-base.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/posts/posts-base.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/carousel/carousel-base.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/carousel/posts-carousel-base.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/carousel/static-carousel.php';

		require_once FELAN_ELEMENTOR_DIR . '/widgets/accordion.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/accordion-image.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/button.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/circle-progress-chart.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/google-map.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/heading.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/fancy-heading.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/icon.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/icon-box.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/number-box.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/user-form.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/job-search.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/image-box.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/image-rotate.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/image-animation.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/image-vertical-animation.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/image-layers.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/image-gallery.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/banner.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/shapes.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/flip-box.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/instagram.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/attribute-list.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/gradation.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/timeline.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/list.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/pricing-table.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/twitter.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/team-member.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/social-networks.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/popup-video.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/separator.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/table.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/modern-tabs.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/morphing.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/client-logo-animation.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/toggle.php';

		require_once FELAN_ELEMENTOR_DIR . '/widgets/grid/grid-base.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/grid/static-grid.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/grid/client-logo.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/grid/view-demo.php';

		require_once FELAN_ELEMENTOR_DIR . '/widgets/posts/blog.php';

		require_once FELAN_ELEMENTOR_DIR . '/widgets/testimonial-grid.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/carousel/testimonial-carousel.php';

		require_once FELAN_ELEMENTOR_DIR . '/widgets/carousel/team-member-carousel.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/carousel/image-carousel.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/carousel/modern-carousel.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/carousel/modern-slider.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/carousel/freelancer-carousel.php';

		require_once FELAN_ELEMENTOR_DIR . '/widgets/header/account.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/header/notification.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/header/search-popup.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/header/logo.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/header/search-form.php';
		require_once FELAN_ELEMENTOR_DIR . '/widgets/header/list-categories.php';

		// Register Widgets.
		Plugin::instance()->widgets_manager->register(new Widget_Accordion());
		Plugin::instance()->widgets_manager->register(new Widget_Accordion_Image());
		Plugin::instance()->widgets_manager->register(new Widget_Button());
		Plugin::instance()->widgets_manager->register(new Widget_Client_Logo());
		Plugin::instance()->widgets_manager->register(new Widget_Client_Logo_Animation());
		Plugin::instance()->widgets_manager->register(new Widget_Circle_Progress_Chart());
		Plugin::instance()->widgets_manager->register(new Widget_Google_Map());
		Plugin::instance()->widgets_manager->register(new Widget_Heading());
		Plugin::instance()->widgets_manager->register(new Widget_Icon());
		Plugin::instance()->widgets_manager->register(new Widget_Icon_Box());
		Plugin::instance()->widgets_manager->register(new Widget_Number_Box());
		Plugin::instance()->widgets_manager->register(new Widget_User_Form());
		Plugin::instance()->widgets_manager->register(new Widget_Job_Search());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Box());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Rotate());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Animation());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Vertical_Animation());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Layers());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Gallery());
		Plugin::instance()->widgets_manager->register(new Widget_Image_Carousel());
		Plugin::instance()->widgets_manager->register(new Widget_Freelancer_Carousel());
		Plugin::instance()->widgets_manager->register(new Widget_Banner());
		Plugin::instance()->widgets_manager->register(new Widget_Shapes());
		Plugin::instance()->widgets_manager->register(new Widget_Modern_Carousel());
		Plugin::instance()->widgets_manager->register(new Widget_Modern_Slider());
		Plugin::instance()->widgets_manager->register(new Widget_Instagram());
		Plugin::instance()->widgets_manager->register(new Widget_Flip_Box());
		Plugin::instance()->widgets_manager->register(new Widget_Blog());
		Plugin::instance()->widgets_manager->register(new Widget_Attribute_List());
		Plugin::instance()->widgets_manager->register(new Widget_List());
		Plugin::instance()->widgets_manager->register(new Widget_Fancy_Heading());
		Plugin::instance()->widgets_manager->register(new Widget_Gradation());
		Plugin::instance()->widgets_manager->register(new Widget_Timeline());
		Plugin::instance()->widgets_manager->register(new Widget_Pricing_Table());
		Plugin::instance()->widgets_manager->register(new Widget_Twitter());
		Plugin::instance()->widgets_manager->register(new Widget_Team_Member());
		Plugin::instance()->widgets_manager->register(new Widget_Team_Member_Carousel());
		Plugin::instance()->widgets_manager->register(new Widget_Testimonial_Carousel());
		Plugin::instance()->widgets_manager->register(new Widget_Testimonial_Grid());
		Plugin::instance()->widgets_manager->register(new Widget_Social_Networks());
		Plugin::instance()->widgets_manager->register(new Widget_Popup_Video());
		Plugin::instance()->widgets_manager->register(new Widget_Separator());
		Plugin::instance()->widgets_manager->register(new Widget_Table());
		Plugin::instance()->widgets_manager->register(new Widget_View_Demo());
		Plugin::instance()->widgets_manager->register(new Widget_Moderm_Tabs());
		Plugin::instance()->widgets_manager->register(new Widget_Account());
		Plugin::instance()->widgets_manager->register(new Widget_Notification());
		Plugin::instance()->widgets_manager->register(new Widget_Search_Popup());
		Plugin::instance()->widgets_manager->register(new Widget_Morphing());
		Plugin::instance()->widgets_manager->register(new Widget_Content_Toggle());
		Plugin::instance()->widgets_manager->register(new Widget_Site_Logo());
		Plugin::instance()->widgets_manager->register(new Widget_Search_Form());
		Plugin::instance()->widgets_manager->register(new Widget_List_Categories());

		/**
		 * Include & Register Dependency Widgets.
		 */

		if (function_exists('mc4wp_get_forms')) {
			require_once FELAN_ELEMENTOR_DIR . '/widgets/form/mailchimp-form.php';

			Plugin::instance()->widgets_manager->register(new Widget_Mailchimp_Form());
		}

		if (defined('WPCF7_VERSION')) {
			require_once FELAN_ELEMENTOR_DIR . '/widgets/form/contact-form-7.php';

			Plugin::instance()->widgets_manager->register(new Widget_Contact_Form_7());
		}
	}

	/**
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 *
	 * Remove unwanted widgets
	 */
	function remove_unwanted_widgets($widgets_manager)
	{
		$elementor_widget_blacklist = array(
			'theme-site-logo',
		);

		foreach ($elementor_widget_blacklist as $widget_name) {
			$widgets_manager->unregister_widget_type($widget_name);
		}
	}

	public static function felan_template_elementor($atts)
	{
		if (!class_exists('Elementor\Plugin')) {
			return '';
		}
		if (!isset($atts['id']) || empty($atts['id'])) {
			return '';
		}

		$post_id = $atts['id'];
		$response = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($post_id);
		return $response;
	}
}

Widget_Init::instance()->initialize();
