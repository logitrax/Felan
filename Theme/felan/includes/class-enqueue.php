<?php

if (!defined("ABSPATH")) {
	exit();
}

if (!class_exists("Felan_Enqueue")) {
	/**
	 *  Class Felan_Enqueue
	 */
	class Felan_Enqueue
	{
		/**
		 * The constructor.
		 */
		function __construct()
		{
			add_action("wp_enqueue_scripts", [$this, "enqueue_styles"]);
			add_action("wp_enqueue_scripts", [$this, "enqueue_scripts"]);

			add_action("wp_enqueue_scripts", [$this, "el_register_styles"]);
		}

		/**
		 * Register the stylesheets for the public-facing side of the site.
		 */
		public function enqueue_styles()
		{
			/*
			 * Enqueue Third Party Styles
			 */

			if (!class_exists('Felan_Framework')) {
				wp_enqueue_style(
					'font-awesome-all',
					FELAN_THEME_URI . '/assets/fonts/font-awesome/css/fontawesome-all.min.css',
					array(),
					'5.10.0',
					'all'
				);
			}

			wp_enqueue_style(
				"slick",
				FELAN_THEME_URI . "/assets/libs/slick/slick.css",
				[],
				"1.8.1",
				"all"
			);

			wp_enqueue_style(
				"slick-theme",
				FELAN_THEME_URI . "/assets/libs/slick/slick-theme.css",
				[],
				"1.8.1",
				"all"
			);

			wp_register_style(
				"felan-swiper",
				FELAN_THEME_URI . "/assets/libs/swiper/css/swiper.min.css",
				[],
				"5.3.8",
				"all"

			);


			wp_enqueue_style('growl', FELAN_THEME_URI . '/assets/libs/growl/css/jquery.growl.min.css', array(), '1.3.3', 'all');

			/*
			 * Enqueue Theme Styles
			 */
			wp_enqueue_style(
				"felan-font-cabin",
				FELAN_THEME_URI . "/assets/fonts/cabin/cabin.css"
			);

			$enable_rtl_mode = Felan_Helper::felan_get_option(
				"enable_rtl_mode",
				0
			);

            $id = get_the_ID();
            $show_page_rtl   = get_post_meta($id, 'felan-show_page_rtl', true);
            if (is_rtl() || $enable_rtl_mode || $show_page_rtl == '1') {
				wp_enqueue_style(
					"felan_rtl-style",
					FELAN_THEME_URI . "/assets/scss/rtl/rtl.min.css",
					[]
				);
				wp_enqueue_style(
					"felan_custom-rtl-style",
					FELAN_THEME_URI . "/assets/scss/rtl/custom-rtl.css",
					[]
				);
			} else {
				wp_enqueue_style(
					"felan_minify-style",
					FELAN_THEME_URI . "/assets/scss/style.min.css",
					[]
				);
			}
		}


		public function el_register_styles()
		{
			$style = [
				'accordion',
				'accordion-image',
				'attribute-list',
				'banner',
				'blogs',
				'circle-progress-chart',
				'client-logo',
				'client-logo-animation',
				'contact-form-7',
				'fancy-heading',
				'flip-box',
				'google-map',
				'gradation',
				'heading',
				'icon',
				'icon-box',
				'number-box',
				'user-form',
				'job-search',
				'image-animation',
				'image-vertical-animation',
				'image-box',
				'image-carousel',
				'image-gallery',
				'image-layers',
				'image-rotate',
				'instagram',
				'list',
				'mailchimp-form',
				'modern-carousel',
				'modern-menu',
				'modern-slider',
				'freelancer-carousel',
				'modern-tabs',
				'popup-video',
				'pricing',
				'separator',
				'shapes',
				'social-networks',
				'table',
				'team-member',
				'team-member-carousel',
				'testimonial-carousel',
				'testimonial-grid',
				'timeline',
				'twitter',
				'morphing',
				'view-demo',
				'toggle',
				'user-form',
			];

			foreach ($style as $key => $value) {
				wp_register_style('felan-el-widget-' . $value, FELAN_ELEMENTOR_URI  . '/assets/scss/' . $value . '.min.css');
			}
		}

		/**
		 * Register the JavaScript for the admin area.
		 */
		public function enqueue_scripts()
		{
			/*
			 * Enqueue Third Party Scripts
			 */

			wp_enqueue_script(
				"waypoints",
				FELAN_THEME_URI . "/assets/libs/waypoints/jquery.waypoints.js",
				["jquery"],
				"4.0.1",
				true
			);

			wp_enqueue_script(
				"matchheight",
				FELAN_THEME_URI .
					"/assets/libs/matchHeight/jquery.matchHeight-min.js",
				["jquery"],
				"0.7.0",
				true
			);

			wp_enqueue_script(
				"imagesloaded",
				FELAN_THEME_URI .
					"/assets/libs/imagesloaded/imagesloaded.min.js",
				["jquery"],
				null,
				true
			);

			wp_enqueue_script('growl', FELAN_THEME_URI . '/assets/libs/growl/js/jquery.growl.min.js', array('jquery'), '1.3.3', true);

			wp_register_script(
				"isotope-masonry",
				FELAN_THEME_URI . "/assets/libs/isotope/js/isotope.pkgd.min.js",
				["jquery"],
				"3.0.6",
				true
			);

			wp_register_script(
				"packery-mode",
				FELAN_THEME_URI .
					"/assets/libs/packery-mode/packery-mode.pkgd.min.js",
				["jquery"],
				"3.0.6",
				true
			);

			wp_enqueue_script(
				"validate",
				FELAN_THEME_URI . "/assets/libs/validate/jquery.validate.min.js",
				["jquery"],
				"1.17.0",
				true
			);

			wp_register_script(
				"felan-grid-layout",
				FELAN_THEME_URI . "/assets/js/grid-layout.min.js",
				[
					"jquery",
					"imagesloaded",
					"matchheight",
					"isotope-masonry",
					"packery-mode",
				],
				FELAN_THEME_VER,
				true
			);

			wp_register_script(
				"felan-layout-masonry",
				FELAN_THEME_URI . "/assets/js/layout-masonry.min.js",
				["jquery"],
				"1.17.0",
				true
			);

			/*
			 * Enqueue Theme Scripts
			 */
			wp_enqueue_script(
				"felan-swiper-wrapper",
				FELAN_THEME_URI . "/assets/js/swiper-wrapper.min.js",
				["jquery"],
				FELAN_THEME_VER,
				true
			);

			$felan_swiper_js = [
				"prevText" => esc_html__("Prev", "felan"),
				"nextText" => esc_html__("Next", "felan"),
			];
			wp_localize_script(
				"felan-swiper-wrapper",
				'$felanSwiper',
				$felan_swiper_js
			);

			wp_enqueue_script(
				"felan-main-js",
				FELAN_THEME_URI . "/assets/js/main.js",
				["jquery"],
				null,
				true
			);

			wp_register_script(
				"felan-swiper",
				FELAN_THEME_URI . "/assets/libs/swiper/js/swiper.min.js",
				["jquery"],
				"5.3.8",
				true
			);

			wp_enqueue_script('felan-widget-grid-post', FELAN_ELEMENTOR_URI . '/assets/js/widgets/widget-grid-post.js', array('felan-grid-layout'), null, true);
			wp_register_script('felan-group-widget-carousel', FELAN_ELEMENTOR_URI . '/assets/js/widgets/group-widget-carousel.js', array(
				'jquery',
				'felan-swiper',
				'felan-swiper-wrapper',
			), null, true);

			if (!class_exists('Felan_Framework')) {
				wp_enqueue_script(
					"slick",
					FELAN_THEME_URI . "/assets/libs/slick/slick.min.js",
					["jquery"],
					"1.8.1",
					true
				);
			}


			$ajax_url = admin_url("admin-ajax.php");
			$current_lang = apply_filters("wpml_current_language", null);

			if ($current_lang) {
				$ajax_url = add_query_arg("lang", $current_lang, $ajax_url);
			}

			$google_id = Felan_Helper::felan_get_option(
				"google_login_api",
				"406259942299-s0m5o0ecdf8khdiittl1r6cd3pdjqsum.apps.googleusercontent.com"
			);
			$sticky_header = Felan_Helper::get_setting("sticky_header");
			$float_header = Felan_Helper::get_setting("float_header");


			wp_localize_script("felan-main-js", "theme_vars", [
				"ajax_url" => esc_url($ajax_url),
				"google_id" => $google_id,
				"send_user_info" => esc_html__(
					"Sending user info,please wait...",
					"felan"
				),
				"forget_password" => esc_html__(
					"Checking your email,please wait...",
					"felan"
				),
				"change_password" => esc_html__(
					"Checking your password,please wait...",
					"felan"
				),
				"notice_cookie_enable" => Felan_Helper::felan_get_option('enable_cookie'),
				"enable_search_box_dropdown" => Felan_Helper::felan_get_option('enable_search_box_dropdown'),
				"limit_search_box" => Felan_Helper::felan_get_option('limit_search_box') ? intval(Felan_Helper::felan_get_option('limit_search_box')) : 0,
				"notice_cookie_confirm" => isset($_COOKIE["notice_cookie_confirm"]) ? "yes" : "no",
				"notice_cookie_messages" => Felan_Cookie::instance()->get_notice_cookie_messages(),
				"sticky_header" => $sticky_header,
				"float_header" => $float_header,

				//Form Login/Register
				'required' => esc_html__('This field is required', 'felan'),
				'remote' => esc_html__('Please fix this field', 'felan'),
				'email' => esc_html__('A valid email address is required', 'felan'),
				'date' => esc_html__('Please enter a valid date', 'felan'),
				'dateISO' => esc_html__('Please enter a valid date (ISO)', 'felan'),
				'number' => esc_html__('Please enter a valid number.', 'felan'),
				'digits' => esc_html__('Please enter only digits', 'felan'),
				'creditcard' => esc_html__('Please enter a valid credit card number', 'felan'),
				'equalTo' => esc_html__('Please enter the same value again', 'felan'),
				'accept' => esc_html__('Please enter a value with a valid extension', 'felan'),
				'maxlength' => esc_html__('Please enter no more than {0} characters', 'felan'),
				'minlength' => esc_html__('Please enter at least {0} characters', 'felan'),
				'rangelength' => esc_html__('Please enter a value between {0} and {1} characters long', 'felan'),
				'range' => esc_html__('Please enter a value between {0} and {1}', 'felan'),
				'max' => esc_html__('Please enter a value less than or equal to {0}', 'felan'),
				'min' => esc_html__('Please enter a value greater than or equal to {0}', 'felan'),
			]);

			/*
			 * The comment-reply script.
			 */
			if (
				is_singular() &&
				comments_open() &&
				get_option("thread_comments")
			) {
				wp_enqueue_script("comment-reply");
			}
		}
	}
}
