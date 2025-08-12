<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Felan_Template_Loader')) {
	/**
	 * Felan_Template_Loader
	 */
	class Felan_Template_Loader
	{
		/**
		 * Constructor
		 * *******************************************************
		 */
		public function __construct()
		{
			$this->template_jobs_hooks();
			$this->template_company_hooks();
			$this->template_freelancer_hooks();
			$this->template_service_hooks();
			$this->template_project_hooks();
			$this->includes();

			add_filter('script_loader_tag', array($this, 'add_defer_facebook'), 10, 2);
			add_filter('body_class', array($this, 'felan_login_to_view'));

			add_action('after_post_job_form', array($this, 'ai_form_generate'));

			add_filter('wp_mail_from_name', array($this, 'custom_wp_mail_from_name'));
		}

		/**
		 * Includes library for plugin
		 * *******************************************************
		 */
		private function includes()
		{
			require_once FELAN_PLUGIN_DIR . 'includes/felan-template-hooks.php';
		}

		/**
		 * Register the stylesheets for the public-facing side of the site.
		 */
		public function admin_enqueue()
		{
			$min_suffix = felan_get_option('enable_min_css', 0) == 1 ? '.min' : '';

			wp_enqueue_style('line-awesome', FELAN_PLUGIN_URL . 'assets/libs/line-awesome/css/line-awesome' . $min_suffix . '.css', array(), '1.1.0', 'all');

			wp_enqueue_style('hint', FELAN_PLUGIN_URL . 'assets/libs/hint/hint.min.css', array(), '2.6.0', 'all');

			wp_enqueue_script('lottie', FELAN_PLUGIN_URL . 'assets/libs/lottie/lottie.min.js', array('jquery'), false, true);

			wp_enqueue_script('magnific-popup', FELAN_PLUGIN_URL . 'assets/libs/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), false, true);

			wp_enqueue_style('magnific-popup', FELAN_PLUGIN_URL . 'assets/libs/magnific-popup/magnific-popup.css', array(), FELAN_PLUGIN_VER, 'all');

			wp_enqueue_style(FELAN_PLUGIN_PREFIX . '-admin', FELAN_PLUGIN_URL . 'assets/css/_admin' . $min_suffix . '.css', array(), FELAN_PLUGIN_VER, 'all');

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'import', FELAN_PLUGIN_URL . 'assets/js/import' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_localize_script(
				FELAN_PLUGIN_PREFIX . 'import',
				'felan_import_vars',
				array(
					'ajax_url' => FELAN_AJAX_URL,
					'animation_url' => FELAN_PLUGIN_URL . 'assets/animation/',
				)
			);

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'admin', FELAN_PLUGIN_URL . 'assets/js/admin' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_localize_script(
				FELAN_PLUGIN_PREFIX . 'admin',
				'felan_admin_vars',
				array(
					'ajax_url' => FELAN_AJAX_URL,
				)
			);
		}

		/**
		 * Register the JavaScript for the admin area.
		 */
		public function enqueue_scripts()
		{
			$min_suffix = felan_get_option('enable_min_js', 0) == 1 ? '.min' : '';

			wp_enqueue_script('waypoints', FELAN_PLUGIN_URL . 'assets/libs/waypoints/jquery.waypoints' . $min_suffix . '.js', array('jquery'), '4.0.1', true);

			wp_enqueue_script('select2', FELAN_PLUGIN_URL . 'assets/libs/select2/js/select2.min.js', array('jquery'), '4.0.13', true);

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'jobs', FELAN_PLUGIN_URL . 'assets/js/jobs/jobs' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'company', FELAN_PLUGIN_URL . 'assets/js/company/company' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer', FELAN_PLUGIN_URL . 'assets/js/freelancer/freelancer' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'apply', FELAN_PLUGIN_URL . 'assets/js/jobs/apply' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'invite', FELAN_PLUGIN_URL . 'assets/js/jobs/invite' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'dashboard', FELAN_PLUGIN_URL . 'assets/js/dashboard/dashboard' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			//register
			wp_register_script('slick', FELAN_PLUGIN_URL . 'assets/libs/slick/slick.min.js', array('jquery'), '1.8.1', false);

			wp_register_script('lightgallery', FELAN_PLUGIN_URL . 'assets/libs/lightgallery/js/lightgallery.min.js', array('jquery'), false, false);

			wp_register_script('lity', FELAN_PLUGIN_URL . 'assets/libs/lity/js/lity' . $min_suffix . '.js', array('jquery'), false, true);

			wp_register_script('chart', FELAN_PLUGIN_URL . 'assets/libs/chart/chart' . $min_suffix . '.js', array('jquery'), false, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'light-gallery', FELAN_PLUGIN_URL . 'assets/js/loop/light-gallery' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'wishlist', FELAN_PLUGIN_URL . 'assets/js/jobs/wishlist' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'messages-dashboard', FELAN_PLUGIN_URL . 'assets/js/dashboard/messages' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'notification', FELAN_PLUGIN_URL . 'assets/js/dashboard/notification' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'payout', FELAN_PLUGIN_URL . 'assets/js/dashboard/payout' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'company-follow', FELAN_PLUGIN_URL . 'assets/js/company/follow' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-follow', FELAN_PLUGIN_URL . 'assets/js/freelancer/follow' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'chart', FELAN_PLUGIN_URL . 'assets/js/dashboard/chart' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'jobs-dashboard', FELAN_PLUGIN_URL . 'assets/js/dashboard/jobs' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'applicants-dashboard', FELAN_PLUGIN_URL . 'assets/js/dashboard/applicants' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancers-dashboard', FELAN_PLUGIN_URL . 'assets/js/dashboard/freelancers' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'follow-freelancer', FELAN_PLUGIN_URL . 'assets/js/dashboard/follow-freelancer' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'invite-freelancer', FELAN_PLUGIN_URL . 'assets/js/dashboard/invite-freelancer' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'my-wishlist', FELAN_PLUGIN_URL . 'assets/js/dashboard/my-wishlist' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'employer-wishlist', FELAN_PLUGIN_URL . 'assets/js/dashboard/employer-wishlist' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'employer-service-order', FELAN_PLUGIN_URL . 'assets/js/dashboard/employer-service-order.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'employer-disputes', FELAN_PLUGIN_URL . 'assets/js/dashboard/employer-disputes.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'employer-project-disputes', FELAN_PLUGIN_URL . 'assets/js/dashboard/employer-project-disputes.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-disputes', FELAN_PLUGIN_URL . 'assets/js/dashboard/freelancer-disputes.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-project-disputes', FELAN_PLUGIN_URL . 'assets/js/dashboard/freelancer-project-disputes.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'employer-project-order', FELAN_PLUGIN_URL . 'assets/js/dashboard/employer-project-order.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'service-detail', FELAN_PLUGIN_URL . 'assets/js/dashboard/service-detail.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'project-detail', FELAN_PLUGIN_URL . 'assets/js/dashboard/project-detail.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'disputes-detail', FELAN_PLUGIN_URL . 'assets/js/dashboard/disputes-detail.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'project-disputes-detail', FELAN_PLUGIN_URL . 'assets/js/dashboard/project-disputes-detail.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-service-order', FELAN_PLUGIN_URL . 'assets/js/dashboard/freelancer-service-order' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'my-follow', FELAN_PLUGIN_URL . 'assets/js/dashboard/my-follow' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'my-apply', FELAN_PLUGIN_URL . 'assets/js/dashboard/my-apply' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'my-review', FELAN_PLUGIN_URL . 'assets/js/dashboard/my-review' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'my-invite', FELAN_PLUGIN_URL . 'assets/js/dashboard/my-invite' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'my-service', FELAN_PLUGIN_URL . 'assets/js/dashboard/my-service' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'my-wallet', FELAN_PLUGIN_URL . 'assets/js/dashboard/my-wallet' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'employer-wallet', FELAN_PLUGIN_URL . 'assets/js/dashboard/employer-wallet' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'my-wallet-topup', FELAN_PLUGIN_URL . 'assets/js/dashboard/my-wallet-topup' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'project-my-wallet', FELAN_PLUGIN_URL . 'assets/js/dashboard/project-my-wallet' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'jobs-submit', FELAN_PLUGIN_URL . 'assets/js/jobs/submit' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'jobs-archive', FELAN_PLUGIN_URL . 'assets/js/jobs/archive.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-submit', FELAN_PLUGIN_URL . 'assets/js/freelancer/submit.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-archive', FELAN_PLUGIN_URL . 'assets/js/freelancer/archive' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-review', FELAN_PLUGIN_URL . 'assets/js/freelancer/review' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'download-cv', FELAN_PLUGIN_URL . 'assets/js/freelancer/download-cv' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'company-submit', FELAN_PLUGIN_URL . 'assets/js/company/submit' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'company-archive', FELAN_PLUGIN_URL . 'assets/js/company/archive' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'company-review', FELAN_PLUGIN_URL . 'assets/js/company/review' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'company-related', FELAN_PLUGIN_URL . 'assets/js/company/related' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'company-dashboard', FELAN_PLUGIN_URL . 'assets/js/dashboard/company' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'service-archive', FELAN_PLUGIN_URL . 'assets/js/service/archive.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'service-submit', FELAN_PLUGIN_URL . 'assets/js/service/submit' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'settings', FELAN_PLUGIN_URL . 'assets/js/settings' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script('jquery-validate', FELAN_PLUGIN_URL . 'assets/libs/validate/jquery.validate.min.js', array('jquery'), '1.17.0', true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'meetings', FELAN_PLUGIN_URL . 'assets/js/dashboard/meetings' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script('stripe-checkout', 'https://checkout.stripe.com/checkout.js', array(), null, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'service-review', FELAN_PLUGIN_URL . 'assets/js/service/review' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'service-wishlist', FELAN_PLUGIN_URL . 'assets/js/service/wishlist' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-payment', FELAN_PLUGIN_URL . 'assets/js/freelancer/payment' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'service', FELAN_PLUGIN_URL . 'assets/js/service/service' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'service-payment', FELAN_PLUGIN_URL . 'assets/js/service/payment' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'payment', FELAN_PLUGIN_URL . 'assets/js/payment/payment' . $min_suffix . '.js', array('jquery', 'wp-util'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-print', FELAN_PLUGIN_URL . 'assets/js/freelancer/print' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'project-archive', FELAN_PLUGIN_URL . 'assets/js/project/archive' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'project-submit', FELAN_PLUGIN_URL . 'assets/js/project/submit' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'project-wishlist', FELAN_PLUGIN_URL . 'assets/js/project/wishlist' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'project-my-wishlist', FELAN_PLUGIN_URL . 'assets/js/dashboard/project-my-wishlist' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'proposal', FELAN_PLUGIN_URL . 'assets/js/project/proposal' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'project-payment', FELAN_PLUGIN_URL . 'assets/js/project/payment' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'project-applicants', FELAN_PLUGIN_URL . 'assets/js/project/applicants' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'my-project', FELAN_PLUGIN_URL . 'assets/js/project/my-project' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-project-proposal', FELAN_PLUGIN_URL . 'assets/js/dashboard/freelancer-project-proposal' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'employer-service-review', FELAN_PLUGIN_URL . 'assets/js/dashboard/service-review' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'freelancer-review-company', FELAN_PLUGIN_URL . 'assets/js/dashboard/freelancer-review-company' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'employer-review-freelancer', FELAN_PLUGIN_URL . 'assets/js/dashboard/employer-review-freelancer' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'wallet-payment', FELAN_PLUGIN_URL . 'assets/js/dashboard/wallet-payment.js', array('jquery'), FELAN_PLUGIN_VER, true);

			//Loop
			wp_register_script(FELAN_PLUGIN_PREFIX . 'search-autocomplete', FELAN_PLUGIN_URL . 'assets/js/loop/search-autocomplete' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'thumbnail', FELAN_PLUGIN_URL . 'assets/js/loop/thumbnail' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'avatar', FELAN_PLUGIN_URL . 'assets/js/loop/avatar' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'custom_image', FELAN_PLUGIN_URL . 'assets/js/loop/custom-image' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'gallery', FELAN_PLUGIN_URL . 'assets/js/loop/gallery' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'upload-cv', FELAN_PLUGIN_URL . 'assets/js/loop/upload-cv' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'custom-upload-cv', FELAN_PLUGIN_URL . 'assets/js/loop/custom-upload-cv' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'map-box-submit', FELAN_PLUGIN_URL . 'assets/js/loop/map/submit/map-box' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'openstreet-map-submit', FELAN_PLUGIN_URL . 'assets/js/loop/map/submit/openstreet-map' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'google-map-submit', FELAN_PLUGIN_URL . 'assets/js/loop/map/submit/google-map' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'map-box-single', FELAN_PLUGIN_URL . 'assets/js/loop/map/single/map-box' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'openstreet-map-single', FELAN_PLUGIN_URL . 'assets/js/loop/map/single/openstreet-map' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'google-map-single', FELAN_PLUGIN_URL . 'assets/js/loop/map/single/google-map' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'login-to-view', FELAN_PLUGIN_URL . 'assets/js/loop/login-to-view' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'single-popup', FELAN_PLUGIN_URL . 'assets/js/loop/single-popup' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'select-location', FELAN_PLUGIN_URL . 'assets/js/loop/select-location' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'social-network', FELAN_PLUGIN_URL . 'assets/js/loop/social-network' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'mess_image', FELAN_PLUGIN_URL . 'assets/js/loop/mess-image' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_register_script(FELAN_PLUGIN_PREFIX . 'mess_file', FELAN_PLUGIN_URL . 'assets/js/loop/mess-file' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			$payment_data = array(
				'ajax_url' => FELAN_AJAX_URL,
				'processing_text' => esc_html__('Processing, Please wait...', 'felan-framework')
			);
			wp_localize_script(FELAN_PLUGIN_PREFIX . 'payment', 'felan_payment_vars', $payment_data);
			wp_localize_script(FELAN_PLUGIN_PREFIX . 'service-payment', 'felan_payment_vars', $payment_data);
			wp_localize_script(FELAN_PLUGIN_PREFIX . 'project-payment', 'felan_payment_vars', $payment_data);
			wp_localize_script(FELAN_PLUGIN_PREFIX . 'freelancer-payment', 'felan_payment_vars', $payment_data);
			wp_localize_script(FELAN_PLUGIN_PREFIX . 'wallet-payment', 'felan_payment_vars', $payment_data);

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'template', FELAN_PLUGIN_URL . 'assets/js/template' . $min_suffix . '.js', array('jquery'), FELAN_PLUGIN_VER, true);

			wp_add_inline_script(FELAN_PLUGIN_PREFIX . 'template', 'var Felan_Inline_Style = document.getElementById( \'felan_main-style-inline-css\' );');

			$price_min = felan_get_option('felan_price_min', '0');
			$price_max = felan_get_option('felan_price_max', '1000');
			$archive_jobs_items_amount = felan_get_option('archive_jobs_items_amount', '12');
			$map_zoom_level = felan_get_option('map_zoom_level', '12');
			$map_pin_cluster = felan_get_option('map_pin_cluster', 1);
			$map_type = felan_get_option('map_type', 'google_map');
			$google_map_type = 'roadmap';
			if ($map_type == 'google_map') {
				$google_map_style = felan_get_option('googlemap_style', '');
				$google_map_type = felan_get_option('googlemap_type', 'roadmap');
			} else {
				$google_map_style = felan_get_option('mapbox_style', 'streets-v11');
			}
			if ($map_type == 'mapbox') {
				$api_key = felan_get_option('mapbox_api_key');
			} else if ($map_type == 'openstreetmap') {
				$api_key = felan_get_option('openstreetmap_api_key');
			} else {
				$api_key = felan_get_option('googlemap_api_key');
			}

			$google_map_needed = 'true';
			$map_marker_icon_url = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
			$map_cluster_icon_url = FELAN_PLUGIN_URL . 'assets/images/cluster-icon.png';
			$map_effects = felan_get_option('map_effects');
			$enable_archive_map = felan_get_option('enable_archive_map', 1);
			$currency_sign = felan_get_option('currency_sign_default');

			$item_amount = $archive_jobs_items_amount;
			$date_format = get_option('date_format');
			$taxonomy_name = get_query_var('taxonomy');

			wp_localize_script(
				FELAN_PLUGIN_PREFIX . 'template',
				'felan_template_vars',
				array(
					'ajax_url' => FELAN_AJAX_URL,
					'not_found' => esc_html__("We didn't find any results, you can retry with other keyword.", 'felan-framework'),
					'not_jobs' => esc_html__('No jobs found', 'felan-framework'),
					'not_file' => esc_html__('Please upload the appropriate file format', 'felan-framework'),
					'no_results' => esc_html__('No Results', 'felan-framework'),
					'wishlist_save' => esc_html__('Save', 'felan-framework'),
					'wishlist_saved' => esc_html__('Saved', 'felan-framework'),
					'follow_save' => esc_html__('Follow', 'felan-framework'),
					'follow_saved' => esc_html__('Following', 'felan-framework'),
					'apply_saved' => esc_html__('Applied', 'felan-framework'),
					'login_to_view' => esc_html__('Please login to view', 'felan-framework'),
					'package_expires' => esc_html__('The quantity in your package has reached its limit or your package has expired', 'felan-framework'),
					'marker_image_size' => '100x100',
					'item_amount' => $item_amount,
					'range_min' => $price_min,
					'range_max' => $price_max,
					'currency_sign' => $currency_sign,
					'date_format' => $date_format,
					'googlemap_default_zoom' => $map_zoom_level,
					'map_pin_cluster' => $map_pin_cluster,
					'map_api_key' => $api_key,
					'marker_default_icon' => $map_marker_icon_url,
					'clusterIcon' => $map_cluster_icon_url,
					'map_effects' => $map_effects,
					'map_type' => $map_type,
					'google_map_needed' => $google_map_needed,
					'google_map_style' => $google_map_style,
					'google_map_type' => $google_map_type,
					'enable_archive_map' => $enable_archive_map,
					'sending_text' => esc_html__('Sending email, Please wait...', 'felan-framework'),
				)
			);

			// Google map API
			$map_ssl = felan_get_option('map_ssl', 0);
			$map_type = felan_get_option('map_type', '');

			if ($map_type == 'google_map') {

				$googlemap_api_key = felan_get_option('googlemap_api_key', 'AIzaSyBvPDNG6pePr9iFpeRKaOlaZF_l0oT3lWk');
				if (esc_html($map_ssl) == 1 || is_ssl()) {
					wp_register_script('google-map', 'https://maps-api-ssl.google.com/maps/api/js?libraries=places&language=' . get_locale() . '&key=' . esc_html($googlemap_api_key), array('jquery'), FELAN_PLUGIN_VER, true);
				} else {
					wp_register_script('google-map', 'http://maps.googleapis.com/maps/api/js?libraries=places&language=' . get_locale() . '&key=' . esc_html($googlemap_api_key), array('jquery'), FELAN_PLUGIN_VER, true);
				}
			}

			if ($map_pin_cluster != 0) {
				wp_register_script('markerclusterer', FELAN_PLUGIN_URL . 'assets/libs/markerclusterer/markerclusterer.js', array('jquery'), false, true);
			}

			// Mapbox
			if ($map_type == 'mapbox') {
				wp_register_script(FELAN_PLUGIN_PREFIX . 'mapbox-gl', FELAN_PLUGIN_URL . 'assets/libs/mapbox/mapbox-gl.js', array('jquery'), '1.0.0', false);
				wp_register_script(FELAN_PLUGIN_PREFIX . 'mapbox-gl-geocoder', FELAN_PLUGIN_URL . 'assets/libs/mapbox/mapbox-gl-geocoder.min.js', array('jquery'), '1.0.0', false);
				wp_register_script(FELAN_PLUGIN_PREFIX . 'es6-promisel', FELAN_PLUGIN_URL . 'assets/libs/mapbox/es6-promise.min.js', array('jquery'), '1.0.0', false);
				wp_register_script(FELAN_PLUGIN_PREFIX . 'es6-promise', FELAN_PLUGIN_URL . 'assets/libs/mapbox/es6-promise.auto.min.js', array('jquery'), '1.0.0', false);
			}

			// Openstreetmap
			if ($map_type == 'openstreetmap') {
				wp_register_script(FELAN_PLUGIN_PREFIX . 'leaflet', FELAN_PLUGIN_URL . 'assets/libs/leaflet/leaflet.js', array('jquery'), '1.0.0', true);
				wp_register_script(FELAN_PLUGIN_PREFIX . 'leaflet-src', FELAN_PLUGIN_URL . 'assets/libs/leaflet/leaflet-src.js', array('jquery'), '1.0.0', true);
				wp_register_script(FELAN_PLUGIN_PREFIX . 'esri-leaflet', FELAN_PLUGIN_URL . 'assets/libs/leaflet/esri-leaflet.js', array('jquery'), '1.0.0', true);
				wp_register_script(FELAN_PLUGIN_PREFIX . 'esri-leaflet-geocoder', FELAN_PLUGIN_URL . 'assets/libs/leaflet/esri-leaflet-geocoder.js', array('jquery'), '1.0.0', true);
			}

			// Facebook API
			$enable_social_login = felan_get_option('enable_social_login', '1');
			$facebook_app_id = felan_get_option('facebook_app_id', '1270446883532471');
			if ($facebook_app_id && $enable_social_login && !is_user_logged_in()) {
				if (is_ssl()) {
					wp_register_script('facebook-api', 'https://connect.facebook.net/' . get_locale() . '/sdk.js#xfbml=1&version=v4.0&appId=' . $facebook_app_id . '&autoLogAppEvents=1', array('jquery'), FELAN_PLUGIN_VER, true);
				} else {
					wp_register_script('facebook-api', 'http://connect.facebook.net/' . get_locale() . '/sdk.js#xfbml=1&version=v4.0&appId=' . $facebook_app_id . '&autoLogAppEvents=1', array('jquery'), FELAN_PLUGIN_VER, true);
				}
			}

			//Google API
			if ($enable_social_login && !is_user_logged_in()) {
				// wp_register_script("google-api", "https://apis.google.com/js/platform.js", ["jquery"], FELAN_PLUGIN_VER, true);
				wp_register_script("google-api", "https://accounts.google.com/gsi/client", ["jquery"], FELAN_PLUGIN_VER, true);
			}

			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'notification');
		}

		/**
		 * Register the stylesheets for the public-facing side of the site.
		 */
		public function enqueue_styles()
		{
			$min_suffix = felan_get_option('enable_min_css', 0) == 1 ? '.min' : '';

			wp_enqueue_style('line-awesome', FELAN_PLUGIN_URL . 'assets/libs/line-awesome/css/line-awesome' . $min_suffix . '.css', array(), '1.1.0', 'all');

			wp_enqueue_style('font-awesome-all', FELAN_PLUGIN_URL . '/assets/libs/font-awesome/css/fontawesome-all.min.css', array(), '5.2.0', 'all');

			wp_enqueue_style('hint', FELAN_PLUGIN_URL . 'assets/libs/hint/hint.min.css', array(), '2.6.0', 'all');

			wp_enqueue_style('slick', FELAN_PLUGIN_URL . 'assets/libs/slick/slick.min.css', array(), FELAN_PLUGIN_VER, 'all');

			wp_enqueue_style('slick-theme', FELAN_PLUGIN_URL . 'assets/libs/slick/slick-theme.css', array(), FELAN_PLUGIN_VER, 'all');

			wp_enqueue_style('select2', FELAN_PLUGIN_URL . 'assets/libs/select2/css/select2.min.css', array(), '4.0.13', 'all');

			//RTL
			$enable_rtl_mode = felan_get_option("enable_rtl_mode");
			$id = get_the_ID();
			$show_page_rtl   = get_post_meta($id, 'felan-show_page_rtl', true);
			if (is_rtl() || $enable_rtl_mode || $show_page_rtl == '1') {
				wp_enqueue_style(FELAN_PLUGIN_PREFIX . '-rtl', FELAN_PLUGIN_URL . 'assets/css/rtl/_rtl' . $min_suffix . '.css', array(), FELAN_PLUGIN_VER, 'all');
				wp_enqueue_style(FELAN_PLUGIN_PREFIX . '-custom-rtl', FELAN_PLUGIN_URL . 'assets/css/rtl/_custom-rtl' . $min_suffix . '.css', array(), FELAN_PLUGIN_VER, 'all');
			} else {
				wp_enqueue_style(FELAN_PLUGIN_PREFIX . '-style', FELAN_PLUGIN_URL . 'assets/scss/style.min.css', array(), FELAN_PLUGIN_VER, 'all');
			}

			//Post
			wp_dequeue_style('wp-block-library');
			wp_dequeue_style('wp-block-library-theme');
			if (is_single() || is_archive()) {
				wp_enqueue_style('wp-block-library');
				wp_enqueue_style('wp-block-library-theme');
			}

			//WooCommerce
			if (class_exists('WooCommerce')) {
				wp_enqueue_style('checkout-woocomerce', FELAN_PLUGIN_URL . 'assets/scss/package/woocomerce.min.css', array(), FELAN_PLUGIN_VER, 'all');
			}

			//Register
			wp_register_style('lightgallery', FELAN_PLUGIN_URL . 'assets/libs/lightgallery/css/lightgallery.min.css', array(), false, 'all');

			wp_register_style('lity', FELAN_PLUGIN_URL . 'assets/libs/lity/css/lity.min.css', array(), FELAN_PLUGIN_VER, 'all');

			wp_register_style(FELAN_PLUGIN_PREFIX . 'dashboard', FELAN_PLUGIN_URL . 'assets/scss/dashboard/dashboard.min.css', array(), FELAN_PLUGIN_VER, 'all');

			//Map
			$map_type = felan_get_option('map_type', 'mapbox');
			if ($map_type == 'mapbox') {
				wp_register_style(FELAN_PLUGIN_PREFIX . 'mapbox-gl', FELAN_PLUGIN_URL . 'assets/libs/mapbox/mapbox-gl.css', array());
				wp_register_style(FELAN_PLUGIN_PREFIX . 'mapbox-gl-geocoder', FELAN_PLUGIN_URL . 'assets/libs/mapbox/mapbox-gl-geocoder.css', array());
			}
			if ($map_type == 'openstreetmap') {
				wp_register_style(FELAN_PLUGIN_PREFIX . 'leaflet', FELAN_PLUGIN_URL . 'assets/libs/leaflet/leaflet.css', array());
				wp_register_style(FELAN_PLUGIN_PREFIX . 'esri-leaflet', FELAN_PLUGIN_URL . 'assets/libs/leaflet/esri-leaflet-geocoder.css', array());
			}

			//Elementor

			wp_enqueue_style('widget-style', FELAN_PLUGIN_URL . 'modules/elementor/assets/css/widget.min.css', array(), FELAN_THEME_VERSION);

			wp_enqueue_style(FELAN_PLUGIN_PREFIX . 'search-horizontal', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/search-horizontal.min.css', array());

			wp_enqueue_style(FELAN_PLUGIN_PREFIX . 'search-vertical', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/search-vertical.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'jobs', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/jobs.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'job-alerts', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/job-alerts.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'jobs-apply', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/jobs-apply.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'jobs-animation', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/jobs-animation.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'jobs-category', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/jobs-category.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'category-carousel', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/category-carousel.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'jobs-location', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/jobs-location.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'companies', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/companies.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'freelancers', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/freelancer.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'companies-category', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/companies-category.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'freelancer-category', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/freelancer-category.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'freelancer-box', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/freelancer-box.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'package', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/package.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'advanced-archive', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/advanced-archive.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'categories-tabs', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/categories-tabs.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'categories-list', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/categories-list.min.css', array());

			wp_register_style(FELAN_PLUGIN_PREFIX . 'modern-menu', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/modern-menu.min.css', array());

			if (post_type_exists('service')) {
				wp_register_style(FELAN_PLUGIN_PREFIX . 'service', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/service.css', array());
				wp_register_style(FELAN_PLUGIN_PREFIX . 'service-category', FELAN_PLUGIN_URL . 'modules/elementor/assets/scss/service-category.css', array());
			}
		}

		/**
		 * @return Jobs taxonomy
		 */
		function is_jobs_taxonomy()
		{
			return is_tax(get_object_taxonomies('jobs'));
		}

		/**
		 * @return company taxonomy
		 */
		function is_company_taxonomy()
		{
			return is_tax(get_object_taxonomies('company'));
		}

		/**
		 * @return freelancer taxonomy
		 */
		function is_freelancer_taxonomy()
		{
			return is_tax(get_object_taxonomies('freelancer'));
		}

		/**
		 * @return service taxonomy
		 */
		function is_service_taxonomy()
		{
			return is_tax(get_object_taxonomies('service'));
		}

		/**
		 * @return project taxonomy
		 */
		function is_project_taxonomy()
		{
			return is_tax(get_object_taxonomies('project'));
		}

		/**
		 * @param $template
		 * @return string
		 */
		public function template_loader($template)
		{
			$find = array();
			$file = '';

			$jobs_type_url_slug = felan_get_option('jobs_type_url_slug') ? felan_get_option('jobs_type_url_slug') : 'jobs-type';
			$jobs_categories_url_slug = felan_get_option('jobs_categories_url_slug') ? felan_get_option('jobs_categories_url_slug') : 'jobs-categories';
			$jobs_skills_url_slug = felan_get_option('jobs_skills_url_slug') ? felan_get_option('jobs_skills_url_slug') : 'jobs-skills';
			$jobs_career_url_slug = felan_get_option('jobs_career_url_slug') ? felan_get_option('jobs_career_url_slug') : 'jobs-career';
			$jobs_experience_url_slug = felan_get_option('jobs_experience_url_slug') ? felan_get_option('jobs_experience_url_slug') : 'jobs-experience';
			$jobs_qualification_url_slug = felan_get_option('jobs_qualification_url_slug') ? felan_get_option('jobs_qualification_url_slug') : 'jobs-qualification';
			$jobs_gender_url_slug = felan_get_option('jobs_gender_url_slug') ? felan_get_option('jobs_gender_url_slug') : 'jobs-gender';
			$service_categories_url_slug = felan_get_option('service_categories_url_slug') ? felan_get_option('service_categories_url_slug') : 'service-categories';
			$service_skills_url_slug = felan_get_option('service_skills_url_slug') ? felan_get_option('service_skills_url_slug') : 'service-skills';
			$service_language_url_slug = felan_get_option('service_language_url_slug') ? felan_get_option('service_language_url_slug') : 'service-language';
			$company_categories_url_slug = felan_get_option('company_categories_url_slug') ? felan_get_option('company_categories_url_slug') : 'company-categories';
			$company_size_url_slug = felan_get_option('company_size_url_slug') ? felan_get_option('company_size_url_slug') : 'company-size';
			$freelancer_categories_url_slug = felan_get_option('freelancer_categories_url_slug') ? felan_get_option('freelancer_categories_url_slug') : 'freelancer_categories';
			$freelancer_ages_url_slug = felan_get_option('freelancer_ages_url_slug') ? felan_get_option('freelancer_ages_url_slug') : 'freelancer_ages';
			$freelancer_languages_url_slug = felan_get_option('freelancer_languages_url_slug') ? felan_get_option('freelancer_languages_url_slug') : 'freelancer_languages';
			$freelancer_qualification_url_slug = felan_get_option('freelancer_qualification_url_slug') ? felan_get_option('freelancer_qualification_url_slug') : 'freelancer_qualification';
			$freelancer_yoe_url_slug = felan_get_option('freelancer_yoe_url_slug') ? felan_get_option('freelancer_yoe_url_slug') : 'freelancer_yoe';
			$freelancer_education_levels_url_slug = felan_get_option('freelancer_education_levels_url_slug') ? felan_get_option('freelancer_education_levels_url_slug') : 'freelancer_education_levels';
			$freelancer_skills_url_slug = felan_get_option('freelancer_skills_url_slug') ? felan_get_option('freelancer_skills_url_slug') : 'freelancer_skills';
			$freelancer_gender_url_slug = felan_get_option('freelancer_gender_url_slug') ? felan_get_option('freelancer_gender_url_slug') : 'freelancer_gender';
			$project_categories_url_slug = 'project-categories';
			$project_skills_url_slug = 'project-skills';
			$project_language_url_slug = 'project-language';
			$project_career_url_slug = 'project-career';

			if (is_embed()) {
				return $template;
			}
			//Jobs
			if (is_single() && (get_post_type() == 'jobs')) {
				if (get_post_type() == 'jobs') {
					$file = 'single-jobs.php';
				}
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (
				$this->is_jobs_taxonomy()
				&& (is_string_in_current_url('jobs-location') ||
					is_string_in_current_url($jobs_type_url_slug) ||
					is_string_in_current_url($jobs_categories_url_slug) ||
					is_string_in_current_url($jobs_skills_url_slug) ||
					is_string_in_current_url($jobs_career_url_slug) ||
					is_string_in_current_url($jobs_experience_url_slug) ||
					is_string_in_current_url($jobs_qualification_url_slug) ||
					is_string_in_current_url($jobs_gender_url_slug))
			) {
				$term = get_queried_object();

				if (is_tax()) {
					$file = 'taxonomy-jobs.php';
				} else {
					$file = 'archive-jobs.php';
				}

				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (is_post_type_archive('jobs') || is_page('jobs')) {
				$file = 'archive-jobs.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			}

			//Company
			if (is_single() && (get_post_type() == 'company')) {
				if (get_post_type() == 'company') {
					$file = 'single-company.php';
				}
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (
				$this->is_company_taxonomy()
				&& (is_string_in_current_url('company-location') ||
					is_string_in_current_url($company_categories_url_slug) ||
					is_string_in_current_url($company_size_url_slug))
			) {
				$term = get_queried_object();

				if (is_tax()) {
					$file = 'taxonomy-company.php';
				} else {
					$file = 'archive-company.php';
				}

				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (is_post_type_archive('company') || is_page('company')) {
				$file = 'archive-company.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			}

			// Freelancer
			if (is_single() && (get_post_type() == 'freelancer')) {
				if (get_post_type() == 'freelancer') {
					$file = 'single-freelancer.php';
				}
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (
				$this->is_freelancer_taxonomy() &&
				(is_string_in_current_url('freelancer-location') ||
					is_string_in_current_url($freelancer_categories_url_slug) ||
					is_string_in_current_url($freelancer_education_levels_url_slug) ||
					is_string_in_current_url($freelancer_gender_url_slug) ||
					is_string_in_current_url($freelancer_ages_url_slug) ||
					is_string_in_current_url($freelancer_skills_url_slug) ||
					is_string_in_current_url($freelancer_yoe_url_slug) ||
					is_string_in_current_url($freelancer_qualification_url_slug) ||
					is_string_in_current_url($freelancer_languages_url_slug))
			) {
				$term = get_queried_object();

				if (is_tax()) {
					$file = 'taxonomy-freelancers.php';
				} else {
					$file = 'archive-freelancers.php';
				}

				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (is_post_type_archive('freelancer') || is_page('freelancer')) {
				$file = 'archive-freelancers.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			}

			//Service
			if (is_single() && (get_post_type() == 'service')) {
				if (get_post_type() == 'service') {
					$file = 'single-service.php';
				}
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (
				$this->is_service_taxonomy() &&
				(is_string_in_current_url('service-location') ||
					is_string_in_current_url($service_categories_url_slug) ||
					is_string_in_current_url($service_skills_url_slug) ||
					is_string_in_current_url($service_language_url_slug))
			) {
				$term = get_queried_object();

				if (is_tax()) {
					$file = 'taxonomy-service.php';
				} else {
					$file = 'archive-service.php';
				}

				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (is_post_type_archive('service') || is_page('service')) {
				$file = 'archive-service.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			}

			//Projects
			if (is_single() && (get_post_type() == 'project')) {
				if (get_post_type() == 'project') {
					$file = 'single-project.php';
				}
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (
				$this->is_project_taxonomy() &&
				(is_string_in_current_url('project-location') ||
					is_string_in_current_url($project_categories_url_slug) ||
					is_string_in_current_url($project_skills_url_slug) ||
					is_string_in_current_url($project_language_url_slug) ||
					is_string_in_current_url($project_career_url_slug))
			) {
				$term = get_queried_object();

				if (is_tax()) {
					$file = 'taxonomy-project.php';
				} else {
					$file = 'archive-project.php';
				}

				$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] = 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = FELAN()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			} elseif (is_post_type_archive('project') || is_page('project')) {
				$file = 'archive-project.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			}


			//Shortcode
			if (
				felan_page_shortcode('[felan_dashboard]') || felan_page_shortcode('[felan_freelancer_dashboard]') || felan_page_shortcode('[felan_jobs]') || felan_page_shortcode('[felan_jobs_performance]')
				|| felan_page_shortcode('[felan_jobs_submit]') || felan_page_shortcode('[felan_applicants]') || felan_page_shortcode('[felan_freelancers]') || felan_page_shortcode('[felan_projects]') || felan_page_shortcode('[felan_proposals]')
				|| felan_page_shortcode('[felan_user_package]') || felan_page_shortcode('[felan_messages]') || felan_page_shortcode('[felan_projects_submit]') || felan_page_shortcode('[felan_project_payment]') || felan_page_shortcode('[felan_project_payment_completed]')
				|| felan_page_shortcode('[felan_company]') || felan_page_shortcode('[felan_submit_company]') || felan_page_shortcode('[felan_my_project]') || felan_page_shortcode('[felan_freelancer_wallet]')
				|| felan_page_shortcode('[felan_meetings]') || felan_page_shortcode('[felan_settings]') || felan_page_shortcode('[felan_freelancer_settings]') || felan_page_shortcode('[felan_package]') || felan_page_shortcode('[felan_payment]')
				|| felan_page_shortcode('[felan_payment_completed]') || felan_page_shortcode('[felan_my_jobs]') || felan_page_shortcode('[felan_freelancer_company]') || felan_page_shortcode('[felan_disputes]') || felan_page_shortcode('[felan_freelancer_disputes]')
				|| felan_page_shortcode('[felan_freelancer_profile]') || felan_page_shortcode('[felan_user_verification]') || felan_page_shortcode('[felan_freelancer_my_review]') || felan_page_shortcode('[felan_freelancer_meetings]') || felan_page_shortcode('[felan_wallet_topup]')
				|| felan_page_shortcode('[felan_submit_service]') || felan_page_shortcode('[felan_freelancer_service]') || felan_page_shortcode('[felan_employer_service]') || felan_page_shortcode('[felan_freelancer_user_package]') || felan_page_shortcode('[felan_freelancer_package]')
				|| felan_page_shortcode('[felan_freelancer_payment]') || felan_page_shortcode('[felan_freelancer_payment_completed]') || felan_page_shortcode('[woocommerce_checkout]') || felan_page_shortcode('[felan_thank_you]') || felan_page_shortcode('[felan_wallet]')
			) {
				$file = 'page-dashboard.php';
				$find[] = $file;
				$find[] = FELAN()->template_path() . $file;
			}

			if ($file) {
				$template = locate_template(array_unique($find));
				if (!$template) {
					$template = FELAN_PLUGIN_DIR . 'templates/' . $file;
				}
			}

			return $template;
		}

		/**
		 * Register all of the hooks jobs
		 */
		private function template_jobs_hooks()
		{
			// Global
			add_action('felan_layout_wrapper_start', 'layout_wrapper_start');
			add_action('felan_layout_wrapper_end', 'layout_wrapper_end');
			add_action('felan_output_content_jobs_wrapper_start', 'output_content_wrapper_start');
			add_action('felan_output_content_wrapper_start', 'output_content_wrapper_start');
			add_action('felan_output_content_wrapper_end', 'output_content_wrapper_end');
			add_action('felan_sidebar_jobs', 'sidebar_jobs');

			// Taxonomy Jobs & Categories
			$archive_city_layout_style = felan_get_option('archive_city_layout_style', 'layout-default');
			$layout = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : '';
			if (!empty($layout)) {
				$archive_city_layout_style = $layout;
			}

			switch ($archive_city_layout_style) {
				case 'layout-list':

					add_action('felan_archive_jobs_before', 'archive_page_title', 5);
					add_action('felan_archive_jobs_before', 'archive_jobs_post', 5);

					add_action('felan_tax_categories_before', 'archive_page_title', 5);
					add_action('felan_tax_categories_before', 'archive_categories', 10);

					break;

				case 'layout-top-filter':

					add_action('felan_archive_jobs_before', 'archive_page_title', 5);
					add_action('felan_archive_jobs_before', 'archive_jobs_post', 5);

					add_action('felan_tax_categories_before', 'archive_page_title', 5);
					add_action('felan_tax_categories_before', 'archive_categories', 10);

					break;

				case 'layout-default':

					add_action('felan_archive_jobs_before', 'archive_page_title', 5);
					add_action('felan_archive_jobs_before', 'archive_information', 10);
					add_action('felan_archive_jobs_before', 'archive_categories', 15);
					add_action('felan_archive_jobs_before', 'archive_jobs_post', 20);

					add_action('felan_tax_categories_before', 'archive_page_title', 5);
					add_action('felan_tax_categories_before', 'archive_information', 10);
					add_action('felan_tax_categories_before', 'archive_categories', 20);
					break;

				default:
					# code...
					break;
			}

			add_action('felan_archive_map_filter', 'archive_map_filter');
			add_action('felan_archive_jobs_sidebar_filter', 'archive_jobs_sidebar_filter', 10, 2);
			add_action('felan_archive_jobs_top_filter', 'archive_jobs_top_filter', 10, 3);

			//Jobs details order default
			$jobs_details_order_default = array(
				'sort_order' => 'enable_sp_skills|enable_sp_gallery|enable_sp_description|enable_sp_map|enable_sp_video|enable_sp_insights',
				'enable_sp_head' => 'enable_sp_head',
				'enable_sp_insights' => 'enable_sp_insights',
				'enable_sp_description' => 'enable_sp_description',
				'enable_sp_skills' => 'enable_sp_skills',
				'enable_sp_gallery' => 'enable_sp_gallery',
				'enable_sp_video' => 'enable_sp_video',
				'enable_sp_map' => 'enable_sp_map',
			);

			$jobs_details_order = felan_get_option('jobs_details_order', $jobs_details_order_default);

			$skills_nb_order = $video_nb_order = $related_nb_order = $description_nb_order = $insights_nb_order = $map_nb_order = $thumbnail_nb_order = $head_nb_order = $apply_nb_order = $gallery_nb_order = 0;

			if (!empty($jobs_details_order)) {
				$jobs_details_sort_order = explode('|', $jobs_details_order['sort_order']);

				foreach ($jobs_details_sort_order as $key => $value) {
					switch ($value) {
						case 'enable_sp_skills':
							$skills_nb_order = $key;
							break;

						case 'enable_sp_description':
							$description_nb_order = $key;
							break;

						case 'enable_sp_insights':
							$insights_nb_order = $key;
							break;

						case 'enable_sp_map':
							$map_nb_order = $key;
							break;

						case 'enable_sp_video':
							$video_nb_order = $key;
							break;

						case 'enable_sp_head':
							$head_nb_order = $key;
							break;

						case 'enable_sp_gallery':
							$gallery_nb_order = $key;
							break;

						default:
							# code...
							break;
					}
				}
			}

			//Jobs details order sidebar
			$jobs_details_sidebar_order_default = array(
				'sort_order' => 'enable_sidebar_sp_apply|enable_sidebar_sp_insights|enable_sidebar_sp_related',
				'enable_sidebar_sp_apply' => 'enable_sidebar_sp_apply',
				'enable_sidebar_sp_insights' => 'enable_sidebar_sp_insights',
				'enable_sidebar_sp_company' => 'enable_sidebar_sp_company',
			);

			$jobs_details_sidebar_order = felan_get_option('jobs_details_sidebar_order', $jobs_details_sidebar_order_default);

			$insights_nb_sidebar_order = $company_nb_sidebar_order = 0;

			if (!empty($jobs_details_order)) {
				$jobs_details_sidebar_sort_order = explode('|', $jobs_details_sidebar_order['sort_order']);
				foreach ($jobs_details_sidebar_sort_order as $key => $value) {
					switch ($value) {

						case 'enable_sidebar_sp_insights':
							$insights_nb_sidebar_order = $key;
							break;

						case 'enable_sidebar_sp_company':
							$company_nb_sidebar_order = $key;
							break;
						default:
							# code...
							break;
					}
				}
			}

			//Type single jobs
			$type_single_jobs = felan_get_option('single_job_layout', '01');
			$type_single_jobs = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : $type_single_jobs;
			$enable_single_jobs_related = felan_get_option('enable_single_jobs_related', '1');
			$enable_single_jobs_info_left = felan_get_option('enable_single_jobs_info_left', '0');
			$enable_single_jobs_info_left = !empty($_GET['info-left']) ? felan_clean(wp_unslash($_GET['info-left'])) : $enable_single_jobs_info_left;
			$content_jobs = felan_get_option('archive_jobs_layout', 'layout-list');
			$content_jobs = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : $content_jobs;
			$content_jobs = !empty($_POST['layout']) ? felan_clean(wp_unslash($_POST['layout'])) : $content_jobs;

			$render_custom_field_jobs = felan_render_custom_field('jobs');

			if ($content_jobs == 'layout-full') {
				//add_action('felan_preview_jobs_before_summary', 'single_jobs_thumbnail', 0);
				if (in_array('enable_sp_head', $jobs_details_order)) {
					add_action('felan_preview_jobs_before_summary', 'single_jobs_head', $head_nb_order, 2);
				}

				if (in_array('enable_sp_insights', $jobs_details_order)) {
					add_action('felan_preview_jobs_summary', 'single_jobs_skills', $skills_nb_order);
				}

				add_action('felan_preview_jobs_summary', 'single_jobs_insigh', 1);


				if (in_array('enable_sp_skills', $jobs_details_order)) {
					add_action('felan_preview_jobs_summary', 'single_jobs_skills', $skills_nb_order);
				}

				if (in_array('enable_sp_description', $jobs_details_order)) {
					add_action('felan_preview_jobs_summary', 'single_jobs_description', $description_nb_order);
				}

				if (in_array('enable_sp_gallery', $jobs_details_order)) {
					add_action('felan_preview_jobs_summary', 'gallery_jobs', $gallery_nb_order);
				}

				if (in_array('enable_sp_video', $jobs_details_order)) {
					add_action('felan_preview_jobs_summary', 'single_jobs_video', $video_nb_order);
				}

				if (in_array('enable_sp_map', $jobs_details_order)) {
					add_action('felan_preview_jobs_summary', 'single_jobs_map', $map_nb_order);
				}
			}

			switch ($type_single_jobs) {
				case '01':

					//add_action('felan_single_jobs_after_summary', 'single_jobs_thumbnail');

					if (in_array('enable_sp_head', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_head', $head_nb_order, 2);
					}

					if (in_array('enable_sp_skills', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_skills', $skills_nb_order, 1);
					}

					if (in_array('enable_sp_description', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_description', $description_nb_order, 1);
					}

					if (in_array('enable_sp_video', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_video', $video_nb_order, 1);
					}

					if (in_array('enable_sp_map', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_map', $map_nb_order, 1);
					}

					if (in_array('enable_sp_gallery', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'gallery_jobs', $gallery_nb_order, 1);
					}

					if (count($render_custom_field_jobs) > 0) {
						add_action('felan_single_jobs_summary', 'single_jobs_additional', 1);
					}

					if ($enable_single_jobs_related) {
						add_action('felan_after_content_single_jobs_summary', 'single_jobs_related', 1);
					}

					//Sidebar
					if (in_array('enable_sidebar_sp_insights', $jobs_details_sidebar_order)) {
						add_action('felan_single_jobs_sidebar', 'single_jobs_sidebar_insights', $insights_nb_sidebar_order, 1);
					}

					if ($enable_single_jobs_info_left === '1') {
						add_action('felan_output_content_jobs_wrapper_start', 'single_jobs_sidebar_company', 1);
					} else {
						add_action('felan_single_jobs_sidebar', 'single_jobs_sidebar_company', $company_nb_sidebar_order, 1);
					}

					break;

				case '02':

					if (in_array('enable_sp_head', $jobs_details_order)) {
						add_action('felan_layout_wrapper_start', 'single_jobs_head', $head_nb_order, 2);
					}

					if (in_array('enable_sp_skills', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_skills', $skills_nb_order, 1);
					}

					if (in_array('enable_sp_insights', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_insigh', $insights_nb_order, 1);
					}

					if (in_array('enable_sp_description', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_description', $description_nb_order, 1);
					}

					if (in_array('enable_sp_video', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_video', $video_nb_order, 1);
					}

					if (in_array('enable_sp_map', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_map', $map_nb_order, 1);
					}

					if (in_array('enable_sp_gallery', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'gallery_jobs', $gallery_nb_order, 1);
					}

					if (count($render_custom_field_jobs) > 0) {
						add_action('felan_single_jobs_summary', 'single_jobs_additional', 1);
					}

					if ($enable_single_jobs_related) {
						add_action('felan_after_content_single_jobs_summary', 'single_jobs_related', 1);
					}

					//Sidebar
					add_action('felan_single_jobs_sidebar', 'single_jobs_sidebar_company', $company_nb_sidebar_order, 1);

					break;

				case '03':

					if (in_array('enable_sp_head', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_head', $head_nb_order, 2);
					}

					if (in_array('enable_sp_skills', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_skills', $skills_nb_order, 1);
					}

					if (in_array('enable_sp_insights', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_insigh', $insights_nb_order, 1);
					}

					if (in_array('enable_sp_description', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_description', $description_nb_order, 1);
					}

					if (in_array('enable_sp_video', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_video', $video_nb_order, 1);
					}

					if (in_array('enable_sp_map', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_map', $map_nb_order, 1);
					}

					if (in_array('enable_sp_gallery', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'gallery_jobs', $gallery_nb_order, 1);
					}

					if (count($render_custom_field_jobs) > 0) {
						add_action('felan_single_jobs_summary', 'single_jobs_additional', 1);
					}

					if ($enable_single_jobs_related) {
						add_action('felan_after_content_single_jobs_summary', 'single_jobs_related', 1);
					}

					//Sidebar
					add_action('felan_single_jobs_sidebar', 'single_jobs_sidebar_company', $company_nb_sidebar_order, 1);

					break;

				case '04':

					if (in_array('enable_sp_head', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_head', $head_nb_order, 2);
					}

					if (in_array('enable_sp_skills', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_skills', $skills_nb_order, 1);
					}

					if (in_array('enable_sp_insights', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_insigh', $insights_nb_order, 1);
					}

					if (in_array('enable_sp_description', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_description', $description_nb_order, 1);
					}

					if (in_array('enable_sp_video', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_video', $video_nb_order, 1);
					}

					if (in_array('enable_sp_map', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'single_jobs_map', $map_nb_order, 1);
					}

					if (in_array('enable_sp_gallery', $jobs_details_order)) {
						add_action('felan_single_jobs_summary', 'gallery_jobs', $gallery_nb_order, 1);
					}

					if (count($render_custom_field_jobs) > 0) {
						add_action('felan_single_jobs_summary', 'single_jobs_additional', 1);
					}

					if ($enable_single_jobs_related) {
						add_action('felan_after_content_single_jobs_summary', 'single_jobs_related', 1);
					}

					//Sidebar
					add_action('felan_single_jobs_sidebar', 'single_jobs_sidebar_company', $company_nb_sidebar_order, 1);

					break;

				default:
					# code...
					break;
			}
		}

		/**
		 * Register all of the hooks company
		 */
		private function template_company_hooks()
		{
			// Global
			add_action('felan_layout_wrapper_start', 'layout_wrapper_start');
			add_action('felan_layout_wrapper_end', 'layout_wrapper_end');
			add_action('felan_output_content_wrapper_start', 'output_content_wrapper_start');
			add_action('felan_output_content_wrapper_end', 'output_content_wrapper_end');
			add_action('felan_sidebar_company', 'sidebar_company');

			add_action('felan_archive_company_sidebar_filter', 'archive_company_sidebar_filter', 10, 3);
			add_action('felan_archive_company_top_filter', 'archive_company_top_filter', 10, 3);

			//Jobs details order default
			$company_details_order_default = array(
				'sort_order' => 'enable_sp_overview',
				'enable_sp_overview' => 'enable_sp_overview',
				'enable_sp_video' => 'enable_sp_video',
			);

			$company_details_order = felan_get_option('company_details_order', $company_details_order_default);

			$video_nb_order = $overview_nb_order = 0;

			if (!empty($company_details_order)) {
				$company_details_sort_order = explode('|', $company_details_order['sort_order']);

				foreach ($company_details_sort_order as $key => $value) {
					switch ($value) {
						case 'enable_sp_overview':
							$overview_nb_order = $key;
							break;
						case 'enable_sp_video':
							$video_nb_order = $key;
							break;

						default:
							# code...
							break;
					}
				}
			}

			//Company details order sidebar
			$company_details_sidebar_order_default = array(
				'sort_order' => 'enable_sidebar_sp_info|enable_sidebar_sp_location',
				'enable_sidebar_sp_info' => 'enable_sidebar_sp_info',
				'enable_sidebar_sp_location' => 'enable_sidebar_sp_location',
			);

			$company_details_sidebar_order = felan_get_option('company_details_sidebar_order', $company_details_sidebar_order_default);

			$info_nb_sidebar_order = $location_nb_sidebar_order = 0;

			$render_custom_field_company = felan_render_custom_field('company');

			if (!empty($company_details_order)) {
				$company_details_sidebar_sort_order = explode('|', $company_details_sidebar_order['sort_order']);
				foreach ($company_details_sidebar_sort_order as $key => $value) {
					switch ($value) {
						case 'enable_sidebar_sp_info':
							$info_nb_sidebar_order = $key;
							break;

						case 'enable_sidebar_sp_location':
							$location_nb_sidebar_order = $key;
							break;

						default:
							# code...
							break;
					}
				}
			}

			//Type single company
			$type_single_company = 'type-1';
			$enable_single_company_related = felan_get_option('enable_single_company_related', '1');
			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			$single_company_style = felan_get_option('single_company_style');
			$single_company_style = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : $single_company_style;

			switch ($type_single_company) {
				case 'type-1':

					if ($single_company_style == 'cover-img') {
						add_action('felan_tab_single_company_about_us', 'single_company_thumbnail', 0);
					} else {
						add_action('felan_single_company_before', 'single_company_thumbnail', 1);
					}

					if (in_array('enable_sp_overview', $company_details_order)) {
						add_action('felan_tab_single_company_about_us', 'single_company_overview', $overview_nb_order, 1);
					}

					if (in_array('enable_sp_video', $company_details_order)) {
						add_action('felan_tab_single_company_about_us', 'single_company_video', $video_nb_order, 1);
					}

					if (count($render_custom_field_company) > 0) {
						add_action('felan_tab_single_company_about_us', 'single_company_additional', 1);
					}

					if ($enable_single_company_related && $enable_post_type_jobs == '1') {
						add_action('felan_tab_single_company_about_us', 'single_company_related', 1);
					}

					if ($enable_post_type_project == '1') {
						add_action('felan_tab_single_company_projects', 'single_company_projects', 1);
					}

					add_action('felan_tab_single_company_reviews', 'single_company_review', 1);

					add_action('felan_tab_single_company_photos', 'single_company_photos', 1);

					//Sidebar
					if (in_array('enable_sidebar_sp_info', $company_details_sidebar_order)) {
						add_action('felan_single_company_sidebar', 'single_company_sidebar_info', $info_nb_sidebar_order, 1);
					}
					if (in_array('enable_sidebar_sp_location', $company_details_sidebar_order)) {
						add_action('felan_single_company_sidebar', 'single_company_sidebar_location', $location_nb_sidebar_order, 1);
					}

					break;

				default:
					# code...
					break;
			}
		}

		/**
		 * Register all of the hooks freelancer
		 */
		private function template_freelancer_hooks()
		{
			// Global
			add_action('felan_layout_wrapper_start', 'layout_wrapper_start');
			add_action('felan_layout_wrapper_end', 'layout_wrapper_end');
			add_action('felan_output_content_wrapper_start', 'output_content_wrapper_start');
			add_action('felan_output_content_wrapper_end', 'output_content_wrapper_end');
			add_action('felan_freelancer_sidebar', 'sidebar_freelancer');

			// Freelancer
			add_action('felan_single_freelancer_hero', 'single_freelancer_cover_hero', 9);

			// Freelancer Search and Filter page
			add_action('felan_archive_freelancer_sidebar_filter', 'archive_freelancer_sidebar_filter', 10, 3);
			add_action('felan_archive_freelancer_top_filter', 'archive_freelancer_top_filter', 10, 3);

			// Freelancer details order default
			$freelancers_details_order_default = array(
				'sort_order' => 'enable_content_sp_thumbnail|enable_sp_head|enable_content_sp_descriptions|enable_content_sp_video|enable_content_sp_experience|enable_content_sp_education|enable_content_sp_projects|enable_content_sp_awards',
				'enable_sp_thumbnail' => 'enable_sp_thumbnail',
				'enable_sp_head' => 'enable_sp_head',
				'enable_sp_descriptions' => 'enable_sp_descriptions',
				'enable_sp_video' => 'enable_sp_video',
				'enable_sp_experience' => 'enable_sp_experience',
				'enable_sp_education' => 'enable_sp_education',
				'enable_sp_photos' => 'enable_sp_photos',
				'enable_sp_portfolio' => 'enable_sp_portfolio',
				'enable_sp_awards' => 'enable_sp_awards',
			);

			$freelancers_details_order = felan_get_option('freelancers_details_order', $freelancers_details_order_default);

			$thumbnail_nb_order = $descriptions_nb_order = $video_nb_order  = $experience_nb_order = $enable_sp_photos = $education_nb_order = $projects_nb_order = $awards_nb_order = 0;

			if (!empty($freelancers_details_order)) {
				$freelancer_details_sort_order = explode('|', $freelancers_details_order['sort_order']);

				foreach ($freelancer_details_sort_order as $key => $value) {
					switch ($value) {
						case 'enable_sp_thumbnail':
							$thumbnail_nb_order = $key;
							break;
						case 'enable_sp_head':
							$head_nb_order = $key;
							break;
						case 'enable_sp_descriptions':
							$descriptions_nb_order = $key;
							break;
						case 'enable_sp_video':
							$video_nb_order = $key;
							break;
						case 'enable_sp_experience':
							$experience_nb_order = $key;
							break;
						case 'enable_sp_education':
							$education_nb_order = $key;
							break;
						case 'enable_sp_portfolio':
							$projects_nb_order = $key;
							break;
						case 'enable_sp_photos':
							$enable_sp_photos = $key;
							break;
						case 'enable_sp_awards':
							$awards_nb_order = $key;
							break;

						default:
							# code...
							break;
					}
				}
			}

			//freelancer details order sidebar
			$freelancer_details_sidebar_order_default = array(
				'sort_order' => 'enable_sidebar_sp_info|enable_sidebar_sp_location',
				'enable_sidebar_sp_info' => 'enable_sidebar_sp_info',
				'enable_sidebar_sp_location' => 'enable_sidebar_sp_location',
			);

			$freelancer_details_sidebar_order = felan_get_option('freelancer_details_sidebar_order', $freelancer_details_sidebar_order_default);

			$info_nb_sidebar_order = $location_nb_sidebar_order = 0;

			$freelancer_details_sidebar_sort_order = explode('|', $freelancer_details_sidebar_order['sort_order']);
			foreach ($freelancer_details_sidebar_sort_order as $key => $value) {
				switch ($value) {
					case 'enable_sidebar_sp_info':
						$info_nb_sidebar_order = $key;
						break;

					case 'enable_sidebar_sp_location':
						$location_nb_sidebar_order = $key;
						break;

					default:
						# code...
						break;
				}
			}

			//Type single freelancer
			$type_single_freelancer = 'type-1';
			$enable_single_freelancer_review = felan_get_option('enable_single_freelancer_review', '1');
			$single_freelancer_style = felan_get_option('single_freelancer_style');
			$single_freelancer_style = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : $single_freelancer_style;
			$custom_field_freelancer = felan_render_custom_field('freelancer');

			switch ($type_single_freelancer) {
				case 'type-1':

					if ($single_freelancer_style == 'cover-img') {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_thumbnail', $thumbnail_nb_order, 1);
					} else {
						add_action('felan_single_freelancer_before', 'single_freelancer_thumbnail', 1);
					}

					if (in_array('enable_sp_head', $freelancers_details_order)) {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_head', $head_nb_order, 1);
					}

					if (in_array('enable_sp_descriptions', $freelancers_details_order)) {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_descriptions', $descriptions_nb_order, 1);
					}

					if (in_array('enable_sp_video', $freelancers_details_order)) {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_video', $video_nb_order, 1);
					}

					if (in_array('enable_sp_experience', $freelancers_details_order)) {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_experience', $experience_nb_order, 1);
					}

					if (in_array('enable_sp_education', $freelancers_details_order)) {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_education', $education_nb_order, 1);
					}

					if (in_array('enable_sp_awards', $freelancers_details_order)) {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_awards', $awards_nb_order, 1);
					}

					if (in_array('enable_sp_photos', $freelancers_details_order)) {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_photos', $enable_sp_photos, 1);
					}

					if (in_array('enable_sp_portfolio', $freelancers_details_order)) {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_portfolio', $projects_nb_order, 1);
					}

					if (count($custom_field_freelancer) > 0) {
						add_action('felan_tab_single_freelancer_about_me', 'single_freelancer_additional', 10, 1);
					}

					add_action('felan_tab_single_freelancer_projects', 'single_freelancer_projects', 10, 1);

					add_action('felan_tab_single_freelancer_services', 'single_freelancer_service', 10, 1);

					add_action('felan_tab_single_freelancer_reviews', 'single_freelancer_review', 10, 1);

					//Sidebar
					if (in_array('enable_sidebar_sp_info', $freelancer_details_sidebar_order)) {
						add_action('felan_single_freelancer_sidebar', 'single_freelancer_sidebar_info', $info_nb_sidebar_order, 1);
					}
					if (in_array('enable_sidebar_sp_location', $freelancer_details_sidebar_order)) {
						add_action('felan_single_freelancer_sidebar', 'single_freelancer_sidebar_location', $location_nb_sidebar_order, 1);
					}

					break;

				default:
					break;
			}
		}

		/**
		 * Register all of the hooks company
		 */
		private function template_service_hooks()
		{
			// Global
			add_action('felan_layout_wrapper_start', 'layout_wrapper_start');
			add_action('felan_layout_wrapper_end', 'layout_wrapper_end');
			add_action('felan_output_content_service_wrapper_start', 'output_content_wrapper_start');
			add_action('felan_output_content_wrapper_start', 'output_content_wrapper_start');
			add_action('felan_output_content_wrapper_end', 'output_content_wrapper_end');
			add_action('felan_sidebar_service', 'sidebar_service');

			add_action('felan_archive_service_sidebar_filter', 'archive_service_sidebar_filter', 10, 3);
			add_action('felan_archive_service_top_filter', 'archive_service_top_filter', 10, 3);

			//Service details order default
			$services_details_order_default = array(
				'sort_order' => 'enable_sp_gallery|enable_sp_descriptions|enable_sp_skills|enable_sp_location|enable_sp_faq|enable_sp_review',
				'enable_sp_gallery' => 'enable_sp_gallery',
				'enable_sp_descriptions' => 'enable_sp_descriptions',
				'enable_sp_skills' => 'enable_sp_skills',
				'enable_sp_package' => 'enable_sp_package',
				'enable_sp_location' => 'enable_sp_location',
				'enable_sp_faq' => 'enable_sp_faq',
				'enable_sp_review' => 'enable_sp_review',
			);

			$services_details_order = felan_get_option('services_details_order', $services_details_order_default);

			$gallery_nb_order = $descriptions_nb_order = $skills_nb_order = $package_nb_order = $location_nb_order = $faq_nb_order = $video_nb_order = $review_nb_order = 0;

			if (!empty($services_details_order)) {
				$service_details_sort_order = explode('|', $services_details_order['sort_order']);

				foreach ($service_details_sort_order as $key => $value) {
					switch ($value) {
						case 'enable_sp_gallery':
							$gallery_nb_order = $key;
							break;

						case 'enable_sp_descriptions':
							$descriptions_nb_order = $key;
							break;

						case 'enable_sp_skills':
							$skills_nb_order = $key;
							break;

						case 'enable_sp_package':
							$package_nb_order = $key;
							break;

						case 'enable_sp_location':
							$location_nb_order = $key;
							break;

						case 'enable_sp_faq':
							$faq_nb_order = $key;
							break;

						case 'enable_sp_video':
							$video_nb_order = $key;
							break;

						case 'enable_sp_review':
							$review_nb_order = $key;
							break;

						default:
							# code...
							break;
					}
				}
			}

			//Service details order sidebar
			$service_details_sidebar_order_default = array(
				'sort_order' => 'enable_sidebar_sp_package|enable_sidebar_sp_info',
				'enable_sidebar_sp_package' => 'enable_sidebar_sp_package',
				'enable_sidebar_sp_info' => 'enable_sidebar_sp_info',
			);

			$service_details_sidebar_order = felan_get_option('service_details_sidebar_order', $service_details_sidebar_order_default);

			$package_nb_sidebar_order = $info_nb_sidebar_order = 0;

			if (!empty($services_details_order)) {
				$service_details_sidebar_sort_order = explode('|', $service_details_sidebar_order['sort_order']);
				foreach ($service_details_sidebar_sort_order as $key => $value) {
					switch ($value) {
						case 'enable_sidebar_sp_package':
							$package_nb_sidebar_order = $key;
							break;

						case 'enable_sidebar_sp_info':
							$info_nb_sidebar_order = $key;
							break;

						default:
							# code...
							break;
					}
				}
			}

			//Type single service
			$type_single_service = 'type-1';
			$enable_service_single_popup = felan_get_option('enable_service_single_popup', '0');
			$enable_single_service_related = felan_get_option('enable_single_service_related');
			$enable_single_service_info_left = felan_get_option('enable_single_service_info_left', '0');
			$enable_single_service_info_left = !empty($_GET['info-left']) ? felan_clean(wp_unslash($_GET['info-left'])) : $enable_single_service_info_left;
			$render_custom_field_service = felan_render_custom_field('service');
			switch ($type_single_service) {
				case 'type-1':

					add_action('felan_single_service_before', 'single_service_head');

					if (in_array('enable_sp_gallery', $services_details_order)) {
						add_action('felan_single_service_summary', 'single_service_gallery', $gallery_nb_order, 1);
					}

					if (in_array('enable_sp_descriptions', $services_details_order)) {
						add_action('felan_single_service_summary', 'single_service_descriptions', $descriptions_nb_order, 1);
					}

					if (in_array('enable_sp_skills', $services_details_order)) {
						add_action('felan_single_service_summary', 'single_service_skills', $skills_nb_order, 1);
					}

					if (count($render_custom_field_service) > 0) {
						add_action('felan_single_service_summary', 'single_service_additional', 1);
					}

					if (in_array('enable_sp_package', $services_details_order)) {
						add_action('felan_single_service_summary', 'single_service_package', $package_nb_order, 1);
					}

					if (in_array('enable_sp_location', $services_details_order)) {
						add_action('felan_single_service_summary', 'single_service_location', $location_nb_order, 1);
					}

					if (in_array('enable_sp_faq', $services_details_order)) {
						add_action('felan_single_service_summary', 'single_service_faq', $faq_nb_order, 1);
					}

					if (in_array('enable_sp_video', $services_details_order)) {
						add_action('felan_single_service_summary', 'single_service_video', $video_nb_order, 1);
					}

					if (in_array('enable_sp_review', $services_details_order)) {
						add_action('felan_single_service_summary', 'single_service_review', $review_nb_order, 1);
					}

					if ($enable_single_service_related) {
						add_action('felan_after_content_single_service_summary', 'single_service_related', 1);
					}

					//Sidebar
					if ($enable_single_service_info_left === '1') {
						if (in_array('enable_sidebar_sp_info', $service_details_sidebar_order)) {
							add_action('felan_output_content_service_wrapper_start', 'single_service_sidebar_info', $info_nb_sidebar_order, 1);
						}
						if (in_array('enable_sidebar_sp_package', $service_details_sidebar_order)) {
							add_action('felan_output_content_service_wrapper_start', 'single_service_sidebar_package', $package_nb_sidebar_order, 1);
						}
					} else {
						if (in_array('enable_sidebar_sp_info', $service_details_sidebar_order)) {
							add_action('felan_single_service_sidebar', 'single_service_sidebar_info', $info_nb_sidebar_order, 1);
						}
						if (in_array('enable_sidebar_sp_package', $service_details_sidebar_order)) {
							add_action('felan_single_service_sidebar', 'single_service_sidebar_package', $package_nb_sidebar_order, 1);
						}
					}

					break;

				default:
					# code...
					break;
			}
		}

		private function template_project_hooks()
		{
			// Global
			add_action('felan_layout_wrapper_start', 'layout_wrapper_start');
			add_action('felan_layout_wrapper_end', 'layout_wrapper_end');
			add_action('felan_output_content_project_wrapper_start', 'output_content_wrapper_start');
			add_action('felan_output_content_wrapper_start', 'output_content_wrapper_start');
			add_action('felan_output_content_wrapper_end', 'output_content_wrapper_end');
			add_action('felan_sidebar_project', 'sidebar_project');

			add_action('felan_archive_project_sidebar_filter', 'archive_project_sidebar_filter', 10, 3);
			add_action('felan_archive_project_top_filter', 'archive_project_top_filter', 10, 3);

			//Service details order default
			$projects_details_order_default = array(
				'sort_order' => 'enable_sp_gallery|enable_sp_descriptions|enable_sp_skills|enable_sp_location|enable_sp_faq|enable_sp_review',
				'enable_sp_gallery' => 'enable_sp_gallery',
				'enable_sp_descriptions' => 'enable_sp_descriptions',
				'enable_sp_skills' => 'enable_sp_skills',
				'enable_sp_package' => 'enable_sp_package',
				'enable_sp_location' => 'enable_sp_location',
				'enable_sp_faq' => 'enable_sp_faq',
				'enable_sp_review' => 'enable_sp_review',
			);

			$projects_details_order = felan_get_option('projects_details_order', $projects_details_order_default);

			$gallery_nb_order = $descriptions_nb_order = $skills_nb_order = $package_nb_order = $location_nb_order = $faq_nb_order = $video_nb_order = $review_nb_order = 0;

			$render_custom_field_project = felan_render_custom_field('project');
			if (!empty($projects_details_order)) {
				$project_details_sort_order = explode('|', $projects_details_order['sort_order']);

				foreach ($project_details_sort_order as $key => $value) {
					switch ($value) {
						case 'enable_sp_gallery':
							$gallery_nb_order = $key;
							break;

						case 'enable_sp_descriptions':
							$descriptions_nb_order = $key;
							break;

						case 'enable_sp_skills':
							$skills_nb_order = $key;
							break;

						case 'enable_sp_package':
							$package_nb_order = $key;
							break;

						case 'enable_sp_location':
							$location_nb_order = $key;
							break;

						case 'enable_sp_faq':
							$faq_nb_order = $key;
							break;

						case 'enable_sp_video':
							$video_nb_order = $key;
							break;

						case 'enable_sp_review':
							$review_nb_order = $key;
							break;

						default:
							# code...
							break;
					}
				}
			}

			//Service details order sidebar
			$project_details_sidebar_order_default = array(
				'sort_order' => 'enable_sidebar_sp_package|enable_sidebar_sp_info',
				'enable_sidebar_sp_package' => 'enable_sidebar_sp_package',
				'enable_sidebar_sp_info' => 'enable_sidebar_sp_info',
			);

			$project_details_sidebar_order = felan_get_option('project_details_sidebar_order', $project_details_sidebar_order_default);

			$package_nb_sidebar_order = $info_nb_sidebar_order = 0;

			if (!empty($projects_details_order)) {
				$project_details_sidebar_sort_order = explode('|', $project_details_sidebar_order['sort_order']);
				foreach ($project_details_sidebar_sort_order as $key => $value) {
					switch ($value) {
						case 'enable_sidebar_sp_package':
							$package_nb_sidebar_order = $key;
							break;

						case 'enable_sidebar_sp_info':
							$info_nb_sidebar_order = $key;
							break;

						default:
							# code...
							break;
					}
				}
			}

			//Type single project
			$type_single_project = 'type-1';
			$enable_project_single_popup = felan_get_option('enable_project_single_popup', '0');
			$enable_single_project_related = felan_get_option('enable_single_project_related');
			$enable_single_project_info_left = felan_get_option('enable_single_project_info_left', '0');
			$enable_single_project_info_left = !empty($_GET['info-left']) ? felan_clean(wp_unslash($_GET['info-left'])) : $enable_single_project_info_left;

			switch ($type_single_project) {
				case 'type-1':

					add_action('felan_single_project_before', 'single_project_head');

					if (in_array('enable_sp_gallery', $projects_details_order)) {
						add_action('felan_single_project_summary', 'single_project_gallery', $gallery_nb_order, 1);
					}

					if (in_array('enable_sp_descriptions', $projects_details_order)) {
						add_action('felan_single_project_summary', 'single_project_descriptions', $descriptions_nb_order, 1);
					}

					if (in_array('enable_sp_skills', $projects_details_order)) {
						add_action('felan_single_project_summary', 'single_project_skills', $skills_nb_order, 1);
					}

					if (in_array('enable_sp_location', $projects_details_order)) {
						add_action('felan_single_project_summary', 'single_project_location', $location_nb_order, 1);
					}

					if (in_array('enable_sp_video', $projects_details_order)) {
						add_action('felan_single_project_summary', 'single_project_video', $video_nb_order, 1);
					}

					if (in_array('enable_sp_faq', $projects_details_order)) {
						add_action('felan_single_project_summary', 'single_project_faq', $faq_nb_order, 1);
					}

					if (count($render_custom_field_project) > 0) {
						add_action('felan_single_project_summary', 'single_project_additional', 10);
					}

					if ($enable_single_project_related) {
						add_action('felan_after_content_single_project_summary', 'single_project_related', 1);
					}

					//Sidebar
					if (in_array('enable_sidebar_sp_apply', $project_details_sidebar_order)) {
						add_action('felan_single_project_sidebar', 'single_project_sidebar_apply', $package_nb_sidebar_order, 1);
					}

					if ($enable_single_project_info_left === '1') {
						add_action('felan_output_content_project_wrapper_start', 'single_project_sidebar_info', 1);
					} else {
						add_action('felan_single_project_sidebar', 'single_project_sidebar_info', $info_nb_sidebar_order, 1);
					}

					break;

				default:
					# code...
					break;
			}
		}

		public function send_meeting_notification()
		{
			global $current_user;
			$user_id = $current_user->ID;
			$args_upcoming = array(
				'post_type' => 'meetings',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'author' => $user_id,
				'orderby' => 'date',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => FELAN_METABOX_PREFIX . 'meeting_status',
						'value' => 'completed',
						'compare' => '!='
					)
				),
			);
			$data_upcoming = new WP_Query($args_upcoming);
			if ($data_upcoming->have_posts()) {
				while ($data_upcoming->have_posts()) : $data_upcoming->the_post();
					$meeting_id = get_the_ID();
					$meeting_date = get_post_meta($meeting_id, FELAN_METABOX_PREFIX . 'meeting_date', true);
					$meeting_time = get_post_meta($meeting_id, FELAN_METABOX_PREFIX . 'meeting_time', true);
					$meeting_with = get_post_meta($meeting_id, FELAN_METABOX_PREFIX . 'meeting_with', true);
					$current_date = date('Y-m-d');

					$email_sent = get_post_meta($meeting_id, 'meeting_email_sent', true);

					if ($email_sent) {
						return;
					}

					$user = get_user_by('login', $meeting_with);
					$user_email = '';
					if (!empty($user)) {
						$user_email = $user->user_email;
					}
					$date = new DateTime($meeting_date);
					$date->modify('-1 day');
					$previousDay = $date->format('Y-m-d');
					$meeting_date_time = $meeting_date . ' ' . $meeting_time;

					$args_mail = array(
						'website_url' =>  get_option('siteurl'),
						'jobs_meetings' => get_the_title($meeting_id),
						'date_time' => $meeting_date_time,
					);
					if (strtotime($meeting_date) > strtotime($current_date) && strtotime($previousDay) == strtotime($current_date)) {
						felan_send_email($user_email, 'mail_notification_meetings', $args_mail);
						wp_clear_scheduled_hook('send_meeting_notification', array($meeting_id));

						update_post_meta($meeting_id, 'meeting_email_sent', true);
					}
				endwhile;
			}
		}

		public function schedule_meeting_notification($meeting_id, $meeting_time)
		{
			$email_time = strtotime('-1 day', strtotime($meeting_time));
			wp_schedule_single_event($email_time, 'send_meeting_notification', array($meeting_id));
		}

		public function setup_meeting_notifications()
		{
			global $current_user;
			$user_id = $current_user->ID;
			$args_upcoming = array(
				'post_type' => 'meetings',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'author' => $user_id,
				'orderby' => 'date',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => FELAN_METABOX_PREFIX . 'meeting_status',
						'value' => 'completed',
						'compare' => '!='
					)
				),
			);
			$data_upcoming = new WP_Query($args_upcoming);
			if ($data_upcoming->have_posts()) {
				while ($data_upcoming->have_posts()) : $data_upcoming->the_post();
					$meeting_id = get_the_ID();
					$meeting_time = get_post_meta($meeting_id, FELAN_METABOX_PREFIX . 'meeting_time', true);
					$this->schedule_meeting_notification($meeting_id, $meeting_time);
				endwhile;
			}
		}

		/**
		 * Form apply jobs
		 */
		public function felan_form_apply_jobs($jobs_id)
		{
			if (!empty($jobs_id)) {
				$jobs_select_apply = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_apply');
				$jobs_select_apply = isset($jobs_select_apply) ? $jobs_select_apply[0] : '';
				if ($jobs_select_apply == 'email') {
					felan_get_template('jobs/apply/gmail.php');
				} elseif ($jobs_select_apply == 'internal') {
					felan_get_template('jobs/apply/internal.php');
				} else {
					felan_get_template('jobs/apply/call-to.php', array(
						'jobs_id' => $jobs_id,
					));
				}
			}
		}

		/**
		 * Form reschedule meeting
		 */
		public function felan_form_reschedule_meeting()
		{
			$projects = felan_get_option('felan_projects_page_id');
			$jobs_dashboard = felan_get_option('felan_jobs_dashboard_page_id');
			$meetings_employer = felan_get_option('felan_meetings_page_id');
			$meetings_freelancer = felan_get_option('felan_freelancer_meetings_page_id');
			if (is_page($jobs_dashboard) || is_page($projects) || is_page($meetings_employer) || is_page($meetings_freelancer)) :
				felan_get_template('jobs/meeting/reschedule.php');
			endif;
		}

		/**
		 * Form meetings popup
		 */
		public function felan_form_setting_meetings()
		{
			global $current_user;
			$user_id = $current_user->ID;
			$meetings = felan_get_option('felan_meetings_page_id');
			$zoom_link = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'metting_zoom_link', true);
			$zoom_link = isset($zoom_link) ? $zoom_link : '';
			$zoom_pw = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'metting_zoom_pw', true);
			$zoom_pw = isset($zoom_pw) ? $zoom_pw : '';
			if (is_page($meetings)) : ?>
				<div class="form-popup felan-form-meetings" id="felan-form-setting-meetings">
					<div class="bg-overlay"></div>
					<form class="meetings-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Zoom Settings', 'felan-framework'); ?></h5>
						<div class="row">
							<div class="form-group col-md-12">
								<label for="zoomlink"><?php esc_html_e('Personal Link', 'felan-framework'); ?>
									<sup>*</sup></label>
								<input type="url" id="zoomlink" value="<?php esc_html_e($zoom_link) ?>" name="zoomlink" placeholder="<?php echo esc_attr('Enter Link', 'felan-framework') ?>" required>
							</div>
							<div class="form-group col-md-12">
								<label for="zoompw"><?php esc_html_e('Password', 'felan-framework'); ?>
									<sup>*</sup></label>
								<input class="form-control" type="password" id="zoompw" name="zoompw" value="<?php esc_html_e($zoom_pw) ?>" placeholder="<?php esc_attr_e('Enter password', 'felan-framework'); ?>" required>
								<span toggle="#zoompw" class="fa fa-fw fa-eye field-icon felan-toggle-password"></span>
							</div>
						</div>
						<div class="button-warpper">
							<a href="#" class="felan-button button-outline button-block  button-cancel"><?php esc_html_e('Cancel', 'felan-framework'); ?></a>
							<button class="felan-button button-block" id="btn-saved-meetings" type="submit">
								<?php esc_html_e('Saved settings', 'felan-framework'); ?>
								<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
							</button>
						</div>
					</form>
				</div>
			<?php endif;
		}

		/**
		 * Form messages popup
		 */
		public function felan_form_setting_messages()
		{
			if (is_single() && ((get_post_type() == 'freelancer') || (get_post_type() == 'company') || get_post_type() == 'jobs' || get_post_type() == 'service' || get_post_type() == 'project') || is_post_type_archive('jobs') || is_post_type_archive('freelancer') || is_post_type_archive('service') || is_post_type_archive('project')) :

				wp_enqueue_script('plupload');
				wp_enqueue_script('jquery-validate');
				wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'messages-dashboard');
				$mess_image_upload_nonce = wp_create_nonce('felan_mess_image_allow_upload');
				$mess_image_type = felan_get_option('felan_image_type');
				$mess_image_id = rand(1, 999999);
				$mess_image_file_size = felan_get_option('felan_image_max_file_size', '1000kb');

				$file_type = felan_get_option('felan-cv-type');
				$max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
				$file_upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
				$file_url = FELAN_AJAX_URL . '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($file_upload_nonce);


				wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'mess_image');
				wp_localize_script(
					FELAN_PLUGIN_PREFIX . 'mess_image',
					'felan_mess_image_vars',
					array(
						'ajax_url' => FELAN_AJAX_URL,
						'mess_image_title' => esc_html__('Valid file formats', 'felan-framework'),
						'mess_image_type' => $mess_image_type,
						'mess_image_file_size' => $mess_image_file_size,
						'mess_image_id' => $mess_image_id,
						'mess_image_upload_nonce' => $mess_image_upload_nonce,
					)
				);

				wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'mess_file');
				wp_localize_script(
					FELAN_PLUGIN_PREFIX . 'mess_file',
					'felan_mess_file_vars',
					array(
						'ajax_url' => FELAN_AJAX_URL,
						'title' => esc_html__('Valid file formats', 'felan-framework'),
						'file_type' => $file_type,
						'max_file_size' => $max_file_size,
						'file_upload_nonce' => $file_upload_nonce,
						'file_url' => $file_url,
					)
				);
			?>
				<div class="form-popup felan-form-popup" id="form-messages-popup">
					<div class="bg-overlay"></div>
					<form class="messages-popup inner-popup ricetheme-messages custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5>
							<?php esc_html_e('Send message', 'felan-framework'); ?>
						</h5>
						<div class="row">
							<div class="form-group col-md-12">
								<label for="title_message"><?php esc_html_e('Title', 'felan-framework'); ?>
									<sup>*</sup></label>
								<input type="text" id="title_message" value="" name="title_message" placeholder="<?php echo esc_attr('Enter Title', 'felan-framework') ?>" required>
							</div>
							<div class="form-group col-md-12">
								<label><?php esc_html_e('Content', 'felan-framework') ?><sup> *</sup></label>
								<textarea name="content_message" cols="30" rows="7" placeholder="<?php esc_attr_e('Enter Content', 'felan-framework'); ?>"></textarea>
							</div>
							<div class="form-group col-md-12">
								<div id="felan_mess_image_view_<?php echo esc_attr($mess_image_id); ?>" class="custom-image-view"></div>
								<div id="felan_mess_file_view"></div>
								<div class="filed-inner" style="display: flex;justify-content: right">
									<div class="felan-fields-mess_image">
										<div id="felan_mess_image_container_<?php echo esc_attr($mess_image_id); ?>" class="file-upload-block preview">
											<div id="felan_add_mess_image_<?php echo esc_attr($mess_image_id); ?>" class="custom-image-add">
												<p id="felan_drop_mess_image_<?php echo esc_attr($mess_image_id); ?>" style="margin-bottom: 0">
													<button type="button" class="tooltip" id="felan_select_mess_image_<?php echo esc_attr($mess_image_id); ?>"
														data-title="<?php echo esc_attr('Upload Image', 'felan-framework') ?>">
														<i class="far fa-images"></i>
													</button>
												</p>
											</div>
											<input type="hidden" class="mess_image_url" value="" id="mess_image_url_<?php echo esc_attr($mess_image_id); ?>">
											<input type="hidden" class="mess_image_id" value="" id="mess_image_id_<?php echo esc_attr($mess_image_id); ?>" />
										</div>
										<input type="hidden" class="image-id" value="<?php echo esc_attr($mess_image_id); ?>">
									</div>
									<div class="felan-upload-file">
										<div class="form-field">
											<div id="felan_file_container" class="file-upload-block preview">
												<div class="felan_cv_file felan_add-cv">
													<p id="felan_drop_file" style="margin-bottom: 0;margin-right: 0">
														<button type="button" class="tooltip" id="felan_select_file"
															data-title="<?php echo esc_attr('Upload File', 'felan-framework') ?>">
															<i class="far fa-file-upload"></i>
														</button>
													</p>
												</div>
												<input type="hidden" class="file_url form-control" name="file_url" value="" id="file_url">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="felan-message-error"></div>
						<div class="button-warpper">
							<a href="#" class="felan-button button-outline button-block button-cancel"><?php esc_html_e('Cancel', 'felan-framework'); ?></a>
							<button class="felan-button button-block" id="btn-send-messages" type="submit">
								<?php esc_html_e('Send Messages', 'felan-framework'); ?>
								<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
							</button>
						</div>
					</form>
				</div>
			<?php endif;
		}

		/**
		 * Form apply project popup
		 */
		public function felan_form_apply_project()
		{
			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'proposal');
			global $current_user;
			$user_id = $current_user->ID;
			$currency_sign_default = felan_get_option('currency_sign_default');
			$enable_employer_project_fee = felan_get_option('enable_employer_project_fee');
			$employer_number_project_fee = felan_get_option('employer_number_project_fee');

			$withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
			$wallet_fee_amount = felan_get_option('wallet_project_proposals_fee_amount', '');
			$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
			?>
			<div class="form-popup felan-form-popup" id="form-apply-project">
				<div class="bg-overlay"></div>
				<form class="project-popup inner-popup custom-scrollbar">
					<a href="#" class="btn-close"><i class="far fa-times"></i></a>
					<h5>
						<?php esc_html_e('Send your proposal', 'felan-framework'); ?>
					</h5>

					<?php felan_withdraw_noti_print('project_proposals', 'You will be charged %s for project proposals.'); ?>

					<div class="row">
						<div class="form-group col-md-12">
							<label for="proposal_price"><?php esc_html_e('Your budget working rate', 'felan-framework'); ?><sup> *</sup></label>
							<input type="number" id="proposal_price" value="" name="proposal_price" placeholder="<?php echo esc_attr('0.00', 'felan-framework') ?>" required>
							<p class="info-budget mb-0 mt-2" style="font-size: 14px;color: #111">
								<span class="text">
									<?php echo esc_html__('Project budget:', 'felan-framework') ?>
								</span>
								<span class="number" style="font-weight: 500"></span>
							</p>
						</div>
						<div class="form-group col-md-12">
							<label><?php esc_html_e('Your estimated hours', 'felan-framework'); ?></label>
							<div class="price-inner">
								<input type="number" id="proposal_time" value="" name="proposal_time" placeholder="<?php echo esc_attr('1', 'felan-framework') ?>" required>
							</div>
							<p class="info-hours mb-0 mt-2" style="font-size: 14px;color: #111">
								<span class="text">
									<?php echo esc_html__('Project estimated hours:', 'felan-framework') ?>
								</span>
								<span class="number" style="font-weight: 500"></span>
							</p>
						</div>
						<div class="form-group col-md-6" style="padding-right: 10px;">
							<label><?php esc_html_e('Your estimated time', 'felan-framework'); ?></label>
							<div class="price-inner">
								<input type="number" id="proposal_fixed_time" value="" name="proposal_fixed_time" placeholder="<?php echo esc_attr('1', 'felan-framework') ?>" required>
							</div>
						</div>
						<div class="form-group col-md-6" id="proposal_rate" style="padding-left: 10px;">
							<label><?php esc_html_e('Rate', 'felan-framework'); ?></label>
							<div class="select2-field">
								<select name="proposal_rate" class="felan-select2">
									<option value="hour"><?php esc_html_e('Per Hour', 'felan-framework'); ?></option>
									<option value="day"><?php esc_html_e('Per Day', 'felan-framework'); ?></option>
									<option value="week"><?php esc_html_e('Per Week', 'felan-framework'); ?></option>
									<option value="month"><?php esc_html_e('Per Month', 'felan-framework'); ?></option>
									<option value="year"><?php esc_html_e('Per Year', 'felan-framework'); ?></option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-12">
							<label><?php esc_html_e('Cover letter', 'felan-framework') ?><sup> *</sup></label>
							<textarea name="content_message" cols="30" rows="7" placeholder="<?php esc_attr_e('Write message here...', 'felan-framework'); ?>"></textarea>
						</div>
					</div>
					<ul>
						<li class="budget">
							<span class="text"><?php esc_html_e('Your budget working rate', 'felan-framework') ?></span>
							<span class="price">
								<span class="sign"><?php echo esc_html($currency_sign_default); ?></span><span class="number">0</span>
							</span>
						</li>
						<?php if ($enable_employer_project_fee == '1' && (!empty($employer_number_project_fee) || $employer_number_project_fee !== '0')) : ?>
							<li class="fee">
								<span class="text">
									<?php echo sprintf(esc_html__('Admin commission fee (%s)', 'felan-framework'), $employer_number_project_fee . '%') ?>
								</span>
								<span class="price">
									<span class="sign">-<?php echo esc_html($currency_sign_default); ?></span><span class="number">0</span>
								</span>
							</li>
						<?php endif; ?>
						<li class="total-hours" style="border-top: 1px solid #eee;padding-top: 5px;">
							<span class="text" style="color: #111;font-weight: 500"><?php esc_html_e("Total amount you'll get per hour", "felan-framework") ?></span>
							<span class="price">
								<span class="sign"><?php echo esc_html($currency_sign_default); ?></span><span class="number">0</span>
							</span>
						</li>
						<li class="estimated-hours">
							<span class="text"><?php esc_html_e("Your estimated hours", "felan-framework") ?></span>
							<span class="price">
								<span class="number">0</span>
							</span>
						</li>
						<li class="total" style="border-top: 1px solid #eee;padding-top: 5px;">
							<span class="text"><?php esc_html_e("Total amount you'll get", "felan-framework") ?></span>
							<span class="price">
								<span class="sign"><?php echo esc_html($currency_sign_default); ?></span><span class="number">0</span>
							</span>
						</li>
					</ul>
					<div class="felan-message-error"></div>
					<div class="button-warpper">
						<a href="#" class="felan-button button-link button-cancel"><?php esc_html_e('Cancel', 'felan-framework'); ?></a>

						<?php if ($enable_wallet_mode == '1') {
							if (intval($withdraw_price) > intval($wallet_fee_amount)) { ?>
								<button class="felan-button" id="btn-send-proposal" type="submit">
									<?php esc_html_e('Submit a proposal', 'felan-framework'); ?>
									<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
								</button>
							<?php } else { ?>
								<a class="felan-button btn-add-to-message" href="#"
									data-text="<?php echo esc_attr('Your wallet balance is currently insufficient.', 'felan-framework'); ?>">
									<?php esc_html_e('Submit a proposal', 'felan-framework'); ?>
								</a>
							<?php } ?>
						<?php } else { ?>
							<button class="felan-button" id="btn-send-proposal" type="submit">
								<?php esc_html_e('Submit a proposal', 'felan-framework'); ?>
								<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
							</button>
						<?php } ?>

					</div>
					<input type="hidden" id="enable_commission" value="<?php echo esc_attr($enable_employer_project_fee); ?>">
					<input type="hidden" id="commission_fee" value="<?php echo esc_attr($employer_number_project_fee); ?>">
					<input type="hidden" id="project_maximum_time" value="">
					<input type="hidden" id="project_author_id" value="">
					<input type="hidden" id="project_post_current" value="">
					<input type="hidden" id="proposal_id" value="">
				</form>
			</div>
			<?php
		}

		/**
		 * Form review employer
		 */
		public function felan_form_employer_review()
		{
			global $current_user;
			$user_id = $current_user->ID;
			$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
			$jobs_dashboard = felan_get_option('felan_jobs_dashboard_page_id');
			$employer_project = felan_get_option('felan_projects_page_id');
			$proposals_dashboard = felan_get_option('felan_proposal_page_id');
			$applicants_id = isset($_GET['applicants_id']) ? felan_clean(wp_unslash($_GET['applicants_id'])) : '';
			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-review');
			wp_localize_script(
				FELAN_PLUGIN_PREFIX . 'freelancer-review',
				'felan_freelancer_review_vars',
				array(
					'ajax_url'  => FELAN_AJAX_URL,
				)
			);

			if (
				($employer_project && is_page($employer_project)) ||
				($jobs_dashboard && is_page($jobs_dashboard)) ||
				( $proposals_dashboard && is_page( $proposals_dashboard ) )
			) : ?>
				<div class="form-popup felan-form-popup form-review-jobs" id="form-popup-review">
					<div class="bg-overlay"></div>
					<form class="project-popup inner-popup custom-scrollbar reviewForm">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php echo esc_html__('Leave Feedback', 'felan-framework'); ?></h5>
						<p class="help"><?php echo esc_html__('Please leave a reason and a comment for your rating', 'felan-framework'); ?></p>
						<div class="content-popup-review">
							<div class="row">
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Clarity in Specification', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Requirements were clear, precise, and well-structured.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Requirements were clear, precise, and well-structured.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_team5" name="rating_team" value="5" /><label for="rating_team5" title="5 stars"></label>
											<input type="radio" id="rating_team4" name="rating_team" value="4" /><label for="rating_team4" title="4 stars"></label>
											<input type="radio" id="rating_team3" name="rating_team" value="3" /><label for="rating_team3" title="3 stars"></label>
											<input type="radio" id="rating_team2" name="rating_team" value="2" /><label for="rating_team2" title="2 stars"></label>
											<input type="radio" id="rating_team1" name="rating_team" value="1" /><label for="rating_team1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Communication', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Responsive, collaborative, and proactive in all interactions', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Responsive, collaborative, and proactive in all interactions', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_working5" name="rating_working" value="5" /><label for="rating_working5" title="5 stars"></label>
											<input type="radio" id="rating_working4" name="rating_working" value="4" /><label for="rating_working4" title="4 stars"></label>
											<input type="radio" id="rating_working3" name="rating_working" value="3" /><label for="rating_working3" title="3 stars"></label>
											<input type="radio" id="rating_working2" name="rating_working" value="2" /><label for="rating_working2" title="2 stars"></label>
											<input type="radio" id="rating_working1" name="rating_working" value="1" /><label for="rating_working1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Payment Promptness', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Payments were timely and handled without issues.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Payments were timely and handled without issues.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_skill5" name="rating_skill" value="5" /><label for="rating_skill5" title="5 stars"></label>
											<input type="radio" id="rating_skill4" name="rating_skill" value="4" /><label for="rating_skill4" title="4 stars"></label>
											<input type="radio" id="rating_skill3" name="rating_skill" value="3" /><label for="rating_skill3" title="3 stars"></label>
											<input type="radio" id="rating_skill2" name="rating_skill" value="2" /><label for="rating_skill2" title="2 stars"></label>
											<input type="radio" id="rating_skill1" name="rating_skill" value="1" /><label for="rating_skill1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Professionalism', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Highly organized, respectful, and focused on quality.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Highly organized, respectful, and focused on quality.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_salary5" name="rating_salary" value="5" /><label for="rating_salary5" title="5 stars"></label>
											<input type="radio" id="rating_salary4" name="rating_salary" value="4" /><label for="rating_salary4" title="4 stars"></label>
											<input type="radio" id="rating_salary3" name="rating_salary" value="3" /><label for="rating_salary3" title="3 stars"></label>
											<input type="radio" id="rating_salary2" name="rating_salary" value="2" /><label for="rating_salary2" title="2 stars"></label>
											<input type="radio" id="rating_salary1" name="rating_salary" value="1" /><label for="rating_salary1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<textarea class="form-control" name="message" placeholder="<?php esc_attr_e('Your review...', 'felan-framework'); ?>"></textarea>
								</div>
							</div>
						</div>
						<div class="felan-message-error"></div>
						<div class="button-warpper">
							<?php if (!empty($applicants_id)) : ?>
								<?php if ($user_demo == 'yes') { ?>
									<a class="felan-button button-outline btn-add-to-message btn-message-complete" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#">
										<?php esc_html_e('Complete without feedback', 'felan-framework') ?>
									</a>
								<?php } else { ?>
									<a href="#" class="felan-button button-outline btn-complete">
										<?php esc_html_e('Complete without feedback', 'felan-framework'); ?>
										<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
									</a>
								<?php } ?>
							<?php else: ?>
								<a href="#" class="felan-button button-outline button-cancel">
									<?php esc_html_e('Cancel', 'felan-framework'); ?>
								</a>
							<?php endif; ?>
							<?php if ($user_demo == 'yes') { ?>
								<a class="felan-button btn-add-to-message" id="btn-submit-review" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#">
									<?php esc_html_e('Submit', 'felan-framework') ?>
								</a>
							<?php } else { ?>
								<button class="felan-button btn-complete" id="btn-submit-review" type="submit" style="color: #fff;border-color: #1F72F2;margin-right: 0">
									<?php esc_html_e('Submit', 'felan-framework'); ?>
									<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
								</button>
							<?php } ?>
						</div>
						<input type="hidden" name="action" value="felan_freelancer_submit_review_ajax">
						<input type="hidden" name="freelancer_id" value="">
						<input type="hidden" name="order_id" value="">
					</form>
				</div>
			<?php endif;
		}

		/**
		 * Form view employer
		 */
		public function felan_form_employer_view_review()
		{
			$my_project = felan_get_option('felan_projects_page_id');
			$proposals_dashboard = felan_get_option('felan_proposal_page_id');
			if (is_page($my_project) || is_page( $proposals_dashboard )) : ?>
				<div class="form-popup felan-form-popup" id="form-popup-view">
					<div class="bg-overlay"></div>
					<form class="project-popup inner-popup custom-scrollbar reviewForm">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php echo esc_html__('Your Review', 'felan-framework'); ?></h5>
						<div class="content-popup-review">
							<div class="row">
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Clarity in Specification', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Requirements were clear, precise, and well-structured.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Requirements were clear, precise, and well-structured.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_team5" name="rating_team" value="5" /><label for="rating_team5" title="5 stars"></label>
											<input type="radio" id="rating_team4" name="rating_team" value="4" /><label for="rating_team4" title="4 stars"></label>
											<input type="radio" id="rating_team3" name="rating_team" value="3" /><label for="rating_team3" title="3 stars"></label>
											<input type="radio" id="rating_team2" name="rating_team" value="2" /><label for="rating_team2" title="2 stars"></label>
											<input type="radio" id="rating_team1" name="rating_team" value="1" /><label for="rating_team1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Communication', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Responsive, collaborative, and proactive in all interactions', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Responsive, collaborative, and proactive in all interactions', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_working5" name="rating_working" value="5" /><label for="rating_working5" title="5 stars"></label>
											<input type="radio" id="rating_working4" name="rating_working" value="4" /><label for="rating_working4" title="4 stars"></label>
											<input type="radio" id="rating_working3" name="rating_working" value="3" /><label for="rating_working3" title="3 stars"></label>
											<input type="radio" id="rating_working2" name="rating_working" value="2" /><label for="rating_working2" title="2 stars"></label>
											<input type="radio" id="rating_working1" name="rating_working" value="1" /><label for="rating_working1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Payment Promptness', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Payments were timely and handled without issues.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Payments were timely and handled without issues.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_skill5" name="rating_skill" value="5" /><label for="rating_skill5" title="5 stars"></label>
											<input type="radio" id="rating_skill4" name="rating_skill" value="4" /><label for="rating_skill4" title="4 stars"></label>
											<input type="radio" id="rating_skill3" name="rating_skill" value="3" /><label for="rating_skill3" title="3 stars"></label>
											<input type="radio" id="rating_skill2" name="rating_skill" value="2" /><label for="rating_skill2" title="2 stars"></label>
											<input type="radio" id="rating_skill1" name="rating_skill" value="1" /><label for="rating_skill1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Professionalism', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Highly organized, respectful, and focused on quality.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Highly organized, respectful, and focused on quality.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_salary5" name="rating_salary" value="5" /><label for="rating_salary5" title="5 stars"></label>
											<input type="radio" id="rating_salary4" name="rating_salary" value="4" /><label for="rating_salary4" title="4 stars"></label>
											<input type="radio" id="rating_salary3" name="rating_salary" value="3" /><label for="rating_salary3" title="3 stars"></label>
											<input type="radio" id="rating_salary2" name="rating_salary" value="2" /><label for="rating_salary2" title="2 stars"></label>
											<input type="radio" id="rating_salary1" name="rating_salary" value="1" /><label for="rating_salary1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<p class="comment"><?php echo esc_html('Comment...', 'felan-framework'); ?></p>
								</div>
							</div>
						</div>
					</form>
				</div>
			<?php endif;
		}

		/**
		 * Form review freelancer
		 */
		public function felan_form_freelancer_review()
		{
			$my_project = felan_get_option('felan_my_project_page_id');
			$my_jobs = felan_get_option('felan_my_jobs_page_id');
			if (($my_project && is_page($my_project)) || ($my_jobs && is_page($my_jobs))) : ?>
				<div class="form-popup felan-form-popup form-review-project" id="form-popup-review">
					<div class="bg-overlay"></div>
					<form class="project-popup inner-popup custom-scrollbar reviewForm">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php echo esc_html__('Leave Feedback', 'felan-framework'); ?></h5>
						<p class="help"><?php echo esc_html__('Please leave a reason and a comment for your rating', 'felan-framework'); ?></p>
						<div class="content-popup-review">
							<div class="row">
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Salary & Benefits', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Salary review every 6 months based on the work performance', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Salary review every 6 months based on the work performance', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_salary5" name="rating_salary" value="5" /><label for="rating_salary5" title="5 stars"></label>
											<input type="radio" id="rating_salary4" name="rating_salary" value="4" /><label for="rating_salary4" title="4 stars"></label>
											<input type="radio" id="rating_salary3" name="rating_salary" value="3" /><label for="rating_salary3" title="3 stars"></label>
											<input type="radio" id="rating_salary2" name="rating_salary" value="2" /><label for="rating_salary2" title="2 stars"></label>
											<input type="radio" id="rating_salary1" name="rating_salary" value="1" /><label for="rating_salary1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Company Culture', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Company trip once a year and Team building once a month', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Company trip once a year and Team building once a month', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_company5" name="rating_company" value="5" /><label for="rating_company5" title="5 stars"></label>
											<input type="radio" id="rating_company4" name="rating_company" value="4" /><label for="rating_company4" title="4 stars"></label>
											<input type="radio" id="rating_company3" name="rating_company" value="3" /><label for="rating_company3" title="3 stars"></label>
											<input type="radio" id="rating_company2" name="rating_company" value="2" /><label for="rating_company2" title="2 stars"></label>
											<input type="radio" id="rating_company1" name="rating_company" value="1" /><label for="rating_company1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Skill Development', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_skill5" name="rating_skill" value="5" /><label for="rating_skill5" title="5 stars"></label>
											<input type="radio" id="rating_skill4" name="rating_skill" value="4" /><label for="rating_skill4" title="4 stars"></label>
											<input type="radio" id="rating_skill3" name="rating_skill" value="3" /><label for="rating_skill3" title="3 stars"></label>
											<input type="radio" id="rating_skill2" name="rating_skill" value="2" /><label for="rating_skill2" title="2 stars"></label>
											<input type="radio" id="rating_skill1" name="rating_skill" value="1" /><label for="rating_skill1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Work Satisfaction', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_work5" name="rating_work" value="5" /><label for="rating_work5" title="5 stars"></label>
											<input type="radio" id="rating_work4" name="rating_work" value="4" /><label for="rating_work4" title="4 stars"></label>
											<input type="radio" id="rating_work3" name="rating_work" value="3" /><label for="rating_work3" title="3 stars"></label>
											<input type="radio" id="rating_work2" name="rating_work" value="2" /><label for="rating_work2" title="2 stars"></label>
											<input type="radio" id="rating_work1" name="rating_work" value="1" /><label for="rating_work1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<textarea class="form-control" name="message" placeholder="<?php esc_attr_e('Your review...', 'felan-framework'); ?>"></textarea>
								</div>
							</div>
						</div>
						<div class="felan-message-error"></div>
						<div class="button-warpper">
							<a href="#" class="felan-button button-link button-cancel"><?php esc_html_e('Cancel', 'felan-framework'); ?></a>
							<button class="felan-button" id="btn-submit-review" type="submit">
								<?php esc_html_e('Submit Review', 'felan-framework'); ?>
								<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
							</button>
						</div>
						<input type="hidden" name="action" value="felan_company_submit_review_ajax">
						<input type="hidden" name="company_id" value="">
					</form>
				</div>
			<?php endif;
		}

		/**
		 * Form review service
		 */
		public function felan_form_service_review()
		{
			global $current_user;
			$user_id = $current_user->ID;
			$employer_service = felan_get_option('felan_employer_service_page_id');
			$order_id = isset($_GET['order_id']) ? felan_clean(wp_unslash($_GET['order_id'])) : '';
			$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
			if ($employer_service && is_page($employer_service)) : ?>
				<div class="form-popup felan-form-popup form-review-service" id="form-popup-review">
					<div class="bg-overlay"></div>
					<form class="service-popup inner-popup custom-scrollbar reviewForm">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php echo esc_html__('Complete and Review', 'felan-framework'); ?></h5>
						<p class="help"><?php echo esc_html__('To help the author improve this service, please leave a reason and a comment for your rating', 'felan-framework'); ?></p>
						<div class="content-popup-review">
							<div class="row">
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Salary & Benefits', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Salary review every 6 months based on the work performance', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Salary review every 6 months based on the work performance', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_salary5" name="rating_salary" value="5" /><label for="rating_salary5" title="5 stars"></label>
											<input type="radio" id="rating_salary4" name="rating_salary" value="4" /><label for="rating_salary4" title="4 stars"></label>
											<input type="radio" id="rating_salary3" name="rating_salary" value="3" /><label for="rating_salary3" title="3 stars"></label>
											<input type="radio" id="rating_salary2" name="rating_salary" value="2" /><label for="rating_salary2" title="2 stars"></label>
											<input type="radio" id="rating_salary1" name="rating_salary" value="1" /><label for="rating_salary1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Work Speed', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Does the speed of project completion meet the deadline?', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Does the speed of project completion meet the deadline?', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_service5" name="rating_service" value="5" /><label for="rating_service5" title="5 stars"></label>
											<input type="radio" id="rating_service4" name="rating_service" value="4" /><label for="rating_service4" title="4 stars"></label>
											<input type="radio" id="rating_service3" name="rating_service" value="3" /><label for="rating_service3" title="3 stars"></label>
											<input type="radio" id="rating_service2" name="rating_service" value="2" /><label for="rating_service2" title="2 stars"></label>
											<input type="radio" id="rating_service1" name="rating_service" value="1" /><label for="rating_service1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Skill Development', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_skill5" name="rating_skill" value="5" /><label for="rating_skill5" title="5 stars"></label>
											<input type="radio" id="rating_skill4" name="rating_skill" value="4" /><label for="rating_skill4" title="4 stars"></label>
											<input type="radio" id="rating_skill3" name="rating_skill" value="3" /><label for="rating_skill3" title="3 stars"></label>
											<input type="radio" id="rating_skill2" name="rating_skill" value="2" /><label for="rating_skill2" title="2 stars"></label>
											<input type="radio" id="rating_skill1" name="rating_skill" value="1" /><label for="rating_skill1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Work Satisfaction', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_work5" name="rating_work" value="5" /><label for="rating_work5" title="5 stars"></label>
											<input type="radio" id="rating_work4" name="rating_work" value="4" /><label for="rating_work4" title="4 stars"></label>
											<input type="radio" id="rating_work3" name="rating_work" value="3" /><label for="rating_work3" title="3 stars"></label>
											<input type="radio" id="rating_work2" name="rating_work" value="2" /><label for="rating_work2" title="2 stars"></label>
											<input type="radio" id="rating_work1" name="rating_work" value="1" /><label for="rating_work1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<label style="margin-top: 8px"><?php esc_html_e('Comments', 'felan-framework'); ?></label>
									<textarea class="form-control" name="message" placeholder="<?php esc_attr_e('Enter your comments', 'felan-framework'); ?>"></textarea>
								</div>
							</div>
						</div>
						<div class="felan-message-error"></div>
						<div class="button-warpper">
							<?php if (!empty($order_id)) : ?>
								<?php if ($user_demo == 'yes') { ?>
									<a class="felan-button button-outline btn-add-to-message btn-message-complete" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#">
										<?php esc_html_e('Complete without feedback', 'felan-framework') ?>
									</a>
								<?php } else { ?>
									<a href="#" class="felan-button button-outline btn-complete">
										<?php esc_html_e('Complete without feedback', 'felan-framework'); ?>
										<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
									</a>
								<?php } ?>
							<?php else: ?>
								<a href="#" class="felan-button button-outline button-cancel">
									<?php esc_html_e('Cancel', 'felan-framework'); ?>
								</a>
							<?php endif; ?>
							<?php if ($user_demo == 'yes') { ?>
								<a class="felan-button btn-add-to-message" id="btn-submit-review" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#">
									<?php esc_html_e('Submit', 'felan-framework') ?>
								</a>
							<?php } else { ?>
								<button class="felan-button btn-complete" id="btn-submit-review" type="submit" style="color: #fff;border-color: #1F72F2;margin-right: 0">
									<?php esc_html_e('Submit', 'felan-framework'); ?>
									<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
								</button>
							<?php } ?>
						</div>
						<input type="hidden" name="action" value="felan_service_submit_review_ajax">
						<input type="hidden" name="service_id" value="">
						<input type="hidden" name="order_id" value="">
					</form>
				</div>
			<?php endif;
		}

		/**
		 * Form review view service
		 */
		public function felan_form_service_view_review()
		{
			$employer_service = felan_get_option('felan_employer_service_page_id');
			if (is_page($employer_service)) : ?>
				<div class="form-popup felan-form-popup" id="form-popup-view">
					<div class="bg-overlay"></div>
					<form class="service-popup inner-popup custom-scrollbar reviewForm">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php echo esc_html__('Your Review', 'felan-framework'); ?></h5>
						<div class="content-popup-review">
							<div class="row">
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Salary & Benefits', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Salary review every 6 months based on the work performance', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Salary review every 6 months based on the work performance', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_salary5" name="rating_salary" value="5" /><label for="rating_salary5" title="5 stars"></label>
											<input type="radio" id="rating_salary4" name="rating_salary" value="4" /><label for="rating_salary4" title="4 stars"></label>
											<input type="radio" id="rating_salary3" name="rating_salary" value="3" /><label for="rating_salary3" title="3 stars"></label>
											<input type="radio" id="rating_salary2" name="rating_salary" value="2" /><label for="rating_salary2" title="2 stars"></label>
											<input type="radio" id="rating_salary1" name="rating_salary" value="1" /><label for="rating_salary1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Work Speed', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Does the speed of project completion meet the deadline?', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Does the speed of project completion meet the deadline?', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_service5" name="rating_service" value="5" /><label for="rating_service5" title="5 stars"></label>
											<input type="radio" id="rating_service4" name="rating_service" value="4" /><label for="rating_service4" title="4 stars"></label>
											<input type="radio" id="rating_service3" name="rating_service" value="3" /><label for="rating_service3" title="3 stars"></label>
											<input type="radio" id="rating_service2" name="rating_service" value="2" /><label for="rating_service2" title="2 stars"></label>
											<input type="radio" id="rating_service1" name="rating_service" value="1" /><label for="rating_service1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Skill Development', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Well trained and dedicated to being able to catch the pace smoothly.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_skill5" name="rating_skill" value="5" /><label for="rating_skill5" title="5 stars"></label>
											<input type="radio" id="rating_skill4" name="rating_skill" value="4" /><label for="rating_skill4" title="4 stars"></label>
											<input type="radio" id="rating_skill3" name="rating_skill" value="3" /><label for="rating_skill3" title="3 stars"></label>
											<input type="radio" id="rating_skill2" name="rating_skill" value="2" /><label for="rating_skill2" title="2 stars"></label>
											<input type="radio" id="rating_skill1" name="rating_skill" value="1" /><label for="rating_skill1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="rating-bars">
										<div class="rate-title">
											<span><?php esc_html_e('Work Satisfaction', 'felan-framework'); ?></span>
											<div class="tip" data-tip-content="<?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'felan-framework'); ?>">
												<div class="tip-content"><?php esc_html_e('Our office is located with creative, open workspaces and a high-quality engaging environment.', 'felan-framework'); ?></div>
											</div>
										</div>
										<fieldset class="rate">
											<input type="radio" id="rating_work5" name="rating_work" value="5" /><label for="rating_work5" title="5 stars"></label>
											<input type="radio" id="rating_work4" name="rating_work" value="4" /><label for="rating_work4" title="4 stars"></label>
											<input type="radio" id="rating_work3" name="rating_work" value="3" /><label for="rating_work3" title="3 stars"></label>
											<input type="radio" id="rating_work2" name="rating_work" value="2" /><label for="rating_work2" title="2 stars"></label>
											<input type="radio" id="rating_work1" name="rating_work" value="1" /><label for="rating_work1" title="1 star"></label>
										</fieldset>
									</div>
								</div>
								<div class="form-group col-md-12">
									<p class="comment"><?php echo esc_html('Comment...', 'felan-framework'); ?></p>
								</div>
							</div>
						</div>
					</form>
				</div>
			<?php endif;
		}

		/**
		 * Form messages applicants
		 */
		public function felan_form_mess_applicants()
		{
			$projects = felan_get_option('felan_projects_page_id');
			$jobs_dashboard = felan_get_option('felan_jobs_dashboard_page_id');
			if (is_page($projects) || is_page($jobs_dashboard)) :
			?>
				<div class="form-popup felan-form-popup" id="form-messages-applicants">
					<div class="bg-overlay"></div>
					<form class="messages-popup inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5>
							<?php esc_html_e('Content Message', 'felan-framework'); ?>
						</h5>
						<div class="content-mess"></div>
						<div class="button-warpper">
							<a href="#" class="felan-button button-block btn-realy-mess">
								<?php esc_html_e('Reply messages', 'felan-framework'); ?>
								<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
							</a>
							<a href="#" class="felan-button button-block button-outline-accent button-cancel"><?php esc_html_e('Cancel messages', 'felan-framework'); ?></a>
						</div>
					</form>
				</div>
			<?php
			endif;
		}

		/**
		 * Form setting deactive
		 */
		public function felan_form_setting_deactive()
		{
			global $current_user;
			$user_id = $current_user->ID;
			$settings_employer = felan_get_option('felan_settings_page_id');
			$settings_freelancer = felan_get_option('felan_freelancer_settings_page_id');
			$nonce_url = wp_nonce_url(get_site_url() . '?action=felan_deactive_user&user_id=' . $user_id, 'deactive_' . $user_id);

			if (is_page($settings_employer) || is_page($settings_freelancer)) :
			?>
				<div class="form-popup felan-form-popup" id="form-setting-deactive">
					<div class="bg-overlay"></div>
					<form class="setting-popup inner-popup custom-scrollbar">
						<h5><?php esc_html_e('Are you sure you want to deactivate this account?', 'felan-framework'); ?></h5>
						<div class="button-warpper">
							<a href="#" class="felan-button button-outline button-block button-cancel"><?php esc_html_e('No', 'felan-framework'); ?></a>
							<a href="<?php echo $nonce_url ?>" class="felan-button button-block"><?php esc_html_e('Yes', 'felan-framework') ?></a>
						</div>
					</form>
				</div>
			<?php
			endif;
		}

		/**
		 * Form freelancer user package
		 */
		public function felan_form_freelancer_user_package()
		{
			$freelancer_user_package = felan_get_option('felan_freelancer_user_package_page_id');
			if (is_page($freelancer_user_package)) : ?>
				<div class="form-popup felan-form-popup" id="form-freelancer-user-package">
					<div class="bg-overlay"></div>
					<form class="user-package-popup inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Package overview', 'felan-framework'); ?></h5>
						<?php felan_get_template('freelancer/package/overview.php'); ?>
					</form>
				</div>

			<?php
			endif;
		}

		/**
		 * Form employer user package
		 */
		public function felan_form_employer_user_package()
		{
			$employer_user_package = felan_get_option('felan_user_package_page_id');
			if (is_page($employer_user_package)) : ?>
				<div class="form-popup felan-form-popup" id="form-employer-user-package">
					<div class="bg-overlay"></div>
					<form class="user-package-popup inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Package overview', 'felan-framework'); ?></h5>
						<?php felan_get_template('company/package/overview.php'); ?>
					</form>
				</div>

			<?php
			endif;
		}

		/**
		 * Form freelancer order refund
		 */
		public function felan_form_service_order_refund()
		{
			global $current_user;
			$user_id = $current_user->ID;
			$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
			$felan_employer_service = felan_get_option('felan_employer_service_page_id');
			if (is_page($felan_employer_service)) : ?>
				<div class="form-popup felan-form-popup" id="form-service-order-refund">
					<div class="bg-overlay"></div>
					<form class="service-order-refund-popup inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Create refund request', 'felan-framework'); ?></h5>
						<p class="des"><?php esc_html_e('This will only be shared with Admin.', 'felan-framework'); ?></p>
						<div class="row">
							<div class="form-group col-md-12 tell-us">
								<p><?php esc_html_e('Tell us the reason', 'felan-framework'); ?></p>
								<input type="radio" id="reason_support" name="reason" value="support">
								<label for="reason_support"><?php esc_html_e('Customer Support', 'felan-framework'); ?></label><br>
								<input type="radio" id="reason_code" name="reason" value="code">
								<label for="reason_code"><?php esc_html_e('Code Quality', 'felan-framework'); ?></label><br>
								<input type="radio" id="reason_design" name="reason" value="desgin">
								<label for="reason_design"><?php esc_html_e('Design Quality', 'felan-framework'); ?></label><br>
								<input type="radio" id="reason_others" name="reason" value="others">
								<label for="reason_others" class="mb-0"><?php esc_html_e('Others', 'felan-framework'); ?></label><br>
							</div>
							<div class="form-group col-md-12">
								<label><?php esc_html_e('Provide a detailed description', 'felan-framework'); ?></label>
								<textarea name="service_content_refund" cols="30" rows="5" placeholder="<?php esc_attr_e('Enter the details of your request', 'felan-framework'); ?>"></textarea>
							</div>
						</div>
						<div class="felan-message-error"></div>
						<div class="btn-warpper">
							<?php if ($user_demo == 'yes') { ?>
								<a class="felan-button btn-add-to-message"
									data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#">
									<?php esc_html_e('Submit', 'felan-framework') ?>
								</a>
							<?php } else { ?>
								<button class="felan-button" id="btn-service-refund" type="submit">
									<?php esc_html_e('Submit', 'felan-framework'); ?>
									<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
								</button>
							<?php } ?>
						</div>
					</form>
				</div>

			<?php
			endif;
		}

		/**
		 * Form freelancer view-reason
		 */
		public function felan_form_service_view_reason()
		{
			$felan_employer_service = felan_get_option('felan_employer_service_page_id');
			$felan_freelancer_service = felan_get_option('felan_freelancer_service_page_id');
			if (is_page($felan_employer_service) || is_page($felan_freelancer_service)) : ?>
				<div class="form-popup felan-form-popup" id="form-service-view-reason">
					<div class="bg-overlay"></div>
					<form class="service-view-reason-popup inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Refund reason', 'felan-framework'); ?></h5>
						<div class="content-refund-reason"></div>
					</form>
				</div>

			<?php
			endif;
		}

		/**
		 * Form Freelancer Withdraw
		 */
		public function felan_form_freelancer_withdraw()
		{
			$felan_freelancer_wallet = felan_get_option('felan_freelancer_wallet_page_id');
			$link_freelancer_settings = felan_get_permalink('freelancer_settings');
			$custom_payout = felan_get_option('custom_payout_setting');
			$enable_paypal = felan_get_option('enable_payout_paypal');
			$enable_stripe = felan_get_option('enable_payout_stripe');
			$enable_bank = felan_get_option('enable_payout_bank_transfer');
			$enable_freelancer_withdrawal_fee = felan_get_option('enable_freelancer_withdrawal_fee', '1');
			$freelancer_number_withdrawal_fee = felan_get_option('freelancer_number_withdrawal_fee');
			if (is_page($felan_freelancer_wallet)) : ?>
				<div class="form-popup felan-form-popup" id="form-freelancer-withdraw">
					<div class="bg-overlay"></div>
					<form class="freelancer-withdraw-popup inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Withdrawals', 'felan-framework'); ?></h5>
						<p>
							<?php esc_html_e('If you have not entered your payout information', 'felan-framework'); ?>
							<a href="<?php echo $link_freelancer_settings; ?>"><?php esc_html_e('click here!', 'felan-framework'); ?></a>
						</p>
						<div class="row">
							<div class="form-group col-md-12">
								<div class="form-select">
									<div class="select2-field">
										<select class="search-control felan-select2" name="withdraw_payment">
											<?php if ($enable_bank === '1') { ?>
												<option value="wire_transfer"><?php esc_html_e('Wire Transfer', 'felan-framework') ?></option>
											<?php } ?>
											<?php if ($enable_stripe === '1') { ?>
												<option value="stripe"><?php esc_html_e('Pay With Stripe', 'felan-framework') ?></option>
											<?php } ?>
											<?php if ($enable_paypal === '1') { ?>
												<option value="paypal"><?php esc_html_e('Pay With Paypal', 'felan-framework') ?></option>
											<?php } ?>
											<?php
											$name_same = array();
											if (!empty($custom_payout)) :
												foreach ($custom_payout as $field) :
													if (!empty($field['name'])) :
														if (!in_array($field['name'], $name_same)) {
															$field_id = str_replace(' ', '-', $field['name']);
															echo '<option value="' . $field_id . '">' . $field['name'] . '</option>';
															$name_same[] = $field['name'];
														}
													endif;
												endforeach;
											endif;
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group col-md-12">
								<input type="number" name="withdraw_price" pattern="[-+]?[0-9]" placeholder="<?php echo esc_attr('Add amount of money', 'felan-framework') ?>">
							</div>
						</div>
						<?php if ($enable_freelancer_withdrawal_fee == '1' || !empty($freelancer_number_withdrawal_fee) || $freelancer_number_withdrawal_fee !== '0') : ?>
							<p class="withdrawals-fee">
								<?php echo sprintf(esc_html__('You will be charged a %s fee upon withdrawal', 'felan-framework'), $freelancer_number_withdrawal_fee . '%'); ?>
							</p>
						<?php endif; ?>
						<div class="felan-message-error"></div>
						<div class="btn-warpper">
							<button class="felan-button" id="btn-submit-withdraw" type="submit">
								<span class="btn-loader"><i class="far fa-arrow-to-bottom"></i></span>
								<?php esc_html_e('Withdrawals', 'felan-framework'); ?>
							</button>
						</div>
					</form>
				</div>

			<?php
			endif;
		}

		/**
		 * Form Employer Withdraw
		 */
		public function felan_form_employer_withdraw()
		{
			$felan_employer_wallet = felan_get_option('felan_wallet_page_id');
			$link_employer_settings = felan_get_permalink('settings');
			$custom_payout = felan_get_option('custom_payout_setting');
			$enable_paypal = felan_get_option('enable_payout_paypal');
			$enable_stripe = felan_get_option('enable_payout_stripe');
			$enable_bank = felan_get_option('enable_payout_bank_transfer');
			if (is_page($felan_employer_wallet)) : ?>
				<div class="form-popup felan-form-popup" id="form-employer-withdraw">
					<div class="bg-overlay"></div>
					<form class="employer-withdraw-popup inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Withdrawals', 'felan-framework'); ?></h5>
						<p>
							<?php esc_html_e('If you have not entered your payout information', 'felan-framework'); ?>
							<a href="<?php echo $link_employer_settings; ?>"><?php esc_html_e('click here!', 'felan-framework'); ?></a>
						</p>
						<div class="row">
							<div class="form-group col-md-12">
								<div class="form-select">
									<div class="select2-field">
										<select class="search-control felan-select2" name="withdraw_payment">
											<?php if ($enable_bank === '1') { ?>
												<option value="wire_transfer"><?php esc_html_e('Wire Transfer', 'felan-framework') ?></option>
											<?php } ?>
											<?php if ($enable_stripe === '1') { ?>
												<option value="stripe"><?php esc_html_e('Pay With Stripe', 'felan-framework') ?></option>
											<?php } ?>
											<?php if ($enable_paypal === '1') { ?>
												<option value="paypal"><?php esc_html_e('Pay With Paypal', 'felan-framework') ?></option>
											<?php } ?>
											<?php
											$name_same = array();
											if (!empty($custom_payout)) :
												foreach ($custom_payout as $field) :
													if (!empty($field['name'])) :
														if (!in_array($field['name'], $name_same)) {
															$field_id = str_replace(' ', '-', $field['name']);
															echo '<option value="' . $field_id . '">' . $field['name'] . '</option>';
															$name_same[] = $field['name'];
														}
													endif;
												endforeach;
											endif;
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group col-md-12">
								<input type="number" name="withdraw_price" pattern="[-+]?[0-9]" placeholder="<?php echo esc_attr('Add amount of money', 'felan-framework') ?>">
							</div>
						</div>
						<div class="felan-message-error"></div>
						<div class="btn-warpper">
							<button class="felan-button" id="btn-submit-withdraw" type="submit">
								<span class="btn-loader"><i class="far fa-arrow-to-bottom"></i></span>
								<?php esc_html_e('Withdrawals', 'felan-framework'); ?>
							</button>
						</div>
					</form>
				</div>

			<?php
			endif;
		}

		/**
		 * Form Wallet Topup
		 */
		public function felan_form_wallet_topup()
		{
			$felan_freelancer_wallet = felan_get_option('felan_freelancer_wallet_page_id');
			$felan_employer_wallet = felan_get_option('felan_wallet_page_id');
			$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
			if ((is_page($felan_freelancer_wallet) || is_page($felan_employer_wallet)) && $enable_wallet_mode == '1') : ?>
				<div class="form-popup felan-form-popup" id="form-wallet-topup">
					<div class="bg-overlay"></div>
					<div class="inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Wallet Topup', 'felan-framework'); ?></h5>
						<p>
							<?php esc_html_e('Top up your wallet and update your payout details.', 'felan-framework'); ?>
						</p>
						<?php felan_get_template("dashboard/wallet-topup/wallet-topup.php"); ?>
					</div>
				</div>

			<?php
			endif;
		}

		/**
		 * Form project order refund
		 */
		public function felan_form_project_order_refund()
		{
			global $current_user;
			$user_id = $current_user->ID;
			$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
			$felan_employer_project = felan_get_option('felan_projects_page_id');
			if (is_page($felan_employer_project)) : ?>
				<div class="form-popup felan-form-popup" id="form-project-order-refund">
					<div class="bg-overlay"></div>
					<form class="project-order-refund-popup inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Create refund request', 'felan-framework'); ?></h5>
						<p class="des"><?php esc_html_e('This will only be shared with Admin.', 'felan-framework'); ?></p>
						<div class="row">
							<div class="form-group col-md-12 tell-us">
								<p><?php esc_html_e('Tell us the reason', 'felan-framework'); ?></p>
								<input type="radio" id="reason_support" name="reason" value="support">
								<label for="reason_support"><?php esc_html_e('Customer Support', 'felan-framework'); ?></label><br>
								<input type="radio" id="reason_code" name="reason" value="code">
								<label for="reason_code"><?php esc_html_e('Code Quality', 'felan-framework'); ?></label><br>
								<input type="radio" id="reason_design" name="reason" value="desgin">
								<label for="reason_design"><?php esc_html_e('Design Quality', 'felan-framework'); ?></label><br>
								<input type="radio" id="reason_others" name="reason" value="others">
								<label for="reason_others" class="mb-0"><?php esc_html_e('Others', 'felan-framework'); ?></label><br>
							</div>
							<div class="form-group col-md-12">
								<label><?php esc_html_e('Provide a detailed description', 'felan-framework'); ?></label>
								<textarea name="project_content_refund" cols="30" rows="5" placeholder="<?php esc_attr_e('Enter the details of your request', 'felan-framework'); ?>"></textarea>
							</div>
						</div>
						<div class="felan-message-error"></div>
						<div class="btn-warpper">
							<?php if ($user_demo == 'yes') { ?>
								<a class="felan-button btn-add-to-message"
									data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#">
									<?php esc_html_e('Submit', 'felan-framework') ?>
								</a>
							<?php } else { ?>
								<button class="felan-button" id="btn-project-refund" type="submit">
									<?php esc_html_e('Submit', 'felan-framework'); ?>
									<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
								</button>
							<?php } ?>
						</div>
					</form>
				</div>

			<?php
			endif;
		}

		/**
		 * Form project view reason
		 */
		public function felan_form_project_view_reason()
		{
			$felan_employer_project = felan_get_option('felan_projects_page_id');
			$felan_freelancer_project = felan_get_option('felan_my_project_page_id');
			if (is_page($felan_employer_project) || is_page($felan_freelancer_project)) : ?>
				<div class="form-popup felan-form-popup" id="form-project-view-reason">
					<div class="bg-overlay"></div>
					<form class="project-view-reason-popup inner-popup custom-scrollbar">
						<a href="#" class="btn-close"><i class="far fa-times"></i></a>
						<h5><?php esc_html_e('Refund reason', 'felan-framework'); ?></h5>
						<div class="content-refund-reason"></div>
					</form>
				</div>

			<?php
			endif;
		}

		/**
		 * Form Invite Freelancer
		 */
		public function felan_form_invite_freelancer()
		{
			if ((is_single() && (get_post_type() == 'freelancer')) || is_post_type_archive('freelancer')) :
				felan_get_template('jobs/invite.php');
			endif;
		}

		/**
		 * Form Single Popup
		 */
		public function felan_form_single_popup()
		{
			wp_enqueue_style('lightgallery');
			wp_enqueue_script('lightgallery');
			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'light-gallery');
			wp_enqueue_script('slick');
			wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'single-popup');
			wp_localize_script(
				FELAN_PLUGIN_PREFIX . 'single-popup',
				'felan_ajax_single_popup',
				array(
					'ajax_url'    => FELAN_AJAX_URL,
				)
			);
			$enable_project_single_popup = felan_get_option('enable_project_single_popup', '0');
			$enable_project_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_project_single_popup;
			$enable_service_single_popup = felan_get_option('enable_service_single_popup', '0');
			$enable_service_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_service_single_popup;
			$enable_freelancer_single_popup = felan_get_option('enable_freelancer_single_popup', '0');
			$enable_freelancer_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_freelancer_single_popup;
			$enable_company_single_popup = felan_get_option('enable_company_single_popup', '0');
			$enable_company_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_company_single_popup;
			$enable_jobs_single_popup = felan_get_option('enable_jobs_single_popup', '0');
			$enable_jobs_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_jobs_single_popup;

			if ((is_post_type_archive('freelancer') && $enable_freelancer_single_popup === '1')
				|| $this->is_freelancer_taxonomy() && $enable_freelancer_single_popup === '1'
				|| is_post_type_archive('jobs') && $enable_jobs_single_popup === '1'
				|| $this->is_jobs_taxonomy() && $enable_jobs_single_popup === '1'
				|| is_post_type_archive('company') && $enable_company_single_popup === '1'
				|| $this->is_company_taxonomy() && $enable_company_single_popup === '1'
				|| is_post_type_archive('service') && $enable_service_single_popup === '1'
				|| $this->is_service_taxonomy() && $enable_service_single_popup === '1'
				|| is_post_type_archive('project') && $enable_project_single_popup === '1'
				|| $this->is_project_taxonomy() && $enable_project_single_popup === '1'
			) : ?>
				<div class="felan-form-single-popup" id="felan-form-single">
					<div class="bg-overlay"></div>
					<div class="single-inner-popup custom-scrollbar">
						<div class="content-header">
							<a href="#" class="btn-single-close"><i class="far fa-times"></i></a>
							<a href="#" target="_blank" class="btn-new-tab"><?php echo esc_html__('Open in new tab', 'felan-framework') ?>
								<i class="far fa-external-link"></i>
							</a>
						</div>
						<div class="content-popup"></div>
					</div>
				</div>
			<?php endif;
		}

		/**
		 * Google job schema
		 */
		public function felan_add_google_job_schema()
		{
			$enable_google_job_schema = felan_get_option('enable_google_job_schema');
			if ((is_single() && (get_post_type() == 'jobs') && $enable_google_job_schema)) :
				global $post;

				$jobs_id = get_the_ID();
				$job_title = get_the_title($post);
				$job_description = get_post_field('post_content', $post);
				$job_date_posted = get_the_date('Y-m-d', $post);

				$jobs_days_single = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_days_closing', true);
				$enable_jobs_expires = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'enable_jobs_expires', true);
				if ($enable_jobs_expires == '1') {
					$jobs_days_closing   = '0';
				} else {
					if ($jobs_days_single) {
						$jobs_days_closing = $jobs_days_single;
					} else {
						$jobs_days_closing   = felan_get_option('jobs_number_days', true);
					}
				}
				$current_date = get_the_date('Y-m-d', $jobs_id);
				$expiration_date = date('Y-m-d', strtotime($current_date . '+' . $jobs_days_closing . ' days'));
				$job_expiration_date = felan_convert_date_format($expiration_date);
				$jobs_skills = get_the_terms($jobs_id, 'jobs-skills');
				$skills_name = array();
				if (is_array($jobs_skills)) :
					foreach ($jobs_skills as $skills) {
						if ($skills->term_id !== '') {
							$skills_name[] = $skills->name;
						}
					}
				endif;
				$skills_name = implode(',', $skills_name);

				$jobs_select_company    = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_company');
				$company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
				$job_hiring_organization_name = get_the_title($company_id);
				$job_hiring_organization_url = get_post_permalink($company_id);
				$jobs_location = get_the_terms($jobs_id, 'felan_location');
				$location_name = $location_id = array();
				if (is_array($jobs_location)) :
					foreach ($jobs_location as $location) {
						if ($location->term_id !== '') {
							$location_name[] = $location->name;
							$location_id[] =  $location->term_id;
						}
					}
				endif;

				$enable_option_state = felan_get_option('enable_option_state');
				$enable_option_country = felan_get_option('enable_option_country');
				$job_address_locality = implode(',', $location_name);
				$job_address_region = 'State';
				$job_address_country = 'Country';
				if ($enable_option_state === '1') {
					$state_id = get_term_meta(implode(',', $location_id), 'felan-location-state', true);
					if (!empty($state_id)) {
						$state_by_id = get_term_by('id', $state_id, 'felan-state');
						if (!empty($state_by_id)) {
							$job_address_region = $state_by_id->name;
						}
					}
				}
				if ($enable_option_state === '1' && $enable_option_country === '1') {
					$country_id = get_term_meta($state_id, 'felan-state-country', true);
					$countries = felan_get_countries();
					foreach ($countries as $k => $v) {
						if ($k == $country_id) {
							$country_val[] = $v;
						}
					}
					if (!empty($country_val)) {
						$job_address_country = implode('', $country_val);
					}
				}
			?>
				<script type="application/ld+json">
					{
						"@context": "https://schema.org",
						"@type": "JobPosting",
						"title": "<?php echo $job_title; ?>",
						"datePosted": "<?php echo $job_date_posted; ?>",
						"validThrough": "<?php echo $job_expiration_date; ?>",
						"skills": "<?php echo $skills_name; ?>",
						"description": <?php echo json_encode($job_description); ?>,
						"hiringOrganization": {
							"@type": "Organization",
							"name": "<?php echo $job_hiring_organization_name; ?>",
							"sameAs": "<?php echo $job_hiring_organization_url; ?>"
						},
						"jobLocation": {
							"@type": "Place",
							"address": {
								"@type": "PostalAddress",
								"addressLocality": "<?php echo $job_address_locality; ?>",
								"addressRegion": "<?php echo $job_address_region; ?>",
								"addressCountry": "<?php echo $job_address_country; ?>"
							}
						}
					}
				</script>
			<?php endif;
		}

		public function wpa_show_permalinks($post_link, $post)
		{
			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');

			if (is_object($post) && $post->post_type == 'jobs' && $enable_post_type_jobs == '1') {
				$terms = wp_get_object_terms($post->ID, 'jobs-categories');
				$jobs_categories_url_slug = felan_get_option('jobs_categories_url_slug');
				if ($terms) {
					return str_replace('%jobs-categories%', $terms[0]->slug, $post_link);
				} else {
					return str_replace('%jobs-categories%', $jobs_categories_url_slug, $post_link);
				}
			}
			if (is_object($post) && $post->post_type == 'company') {
				$terms = wp_get_object_terms($post->ID, 'company-categories');
				$company_categories_url_slug = felan_get_option('company_categories_url_slug');
				if ($terms) {
					return str_replace('%company-categories%', $terms[0]->slug, $post_link);
				} else {
					return str_replace('%company-categories%', $company_categories_url_slug, $post_link);
				}
			}
			if (is_object($post) && $post->post_type == 'freelancer') {
				$terms = wp_get_object_terms($post->ID, 'freelancer_categories');
				$freelancer_categories_url_slug = felan_get_option('freelancer_categories_url_slug');
				if ($terms) {
					return str_replace('%freelancer_categories%', $terms[0]->slug, $post_link);
				} else {
					return str_replace('%freelancer_categories%', $freelancer_categories_url_slug, $post_link);
				}
			}
			if (is_object($post) && $post->post_type == 'service' && $enable_post_type_service == '1') {
				$terms = wp_get_object_terms($post->ID, 'service-categories');
				$service_categories_url_slug = felan_get_option('service_categories_url_slug');
				if ($terms) {
					return str_replace('%service-categories%', $terms[0]->slug, $post_link);
				} else {
					return str_replace('%service-categories%', $service_categories_url_slug, $post_link);
				}
			}
			return $post_link;
		}

		public function generated_rewrite_rules()
		{
			add_rewrite_rule(
				'^jobs/(.*)/(.*)/?$',
				'index.php?post_type=jobs&name=$matches[2]',
				'top'
			);
			add_rewrite_rule(
				'^company/(.*)/(.*)/?$',
				'index.php?post_type=company&name=$matches[2]',
				'top'
			);
		}

		public function add_defer_facebook($tag, $handle)
		{
			if ('facebook-api' === $handle) {
				$tag = str_replace(' src', ' defer="defer" src', $tag);
			}
			return $tag;
		}

		public function felan_login_to_view($classes)
		{
			$enable_job_login_to_view = felan_get_option('enable_job_login_to_view');
			$enable_company_login_to_view = felan_get_option('enable_company_login_to_view');
			$enable_freelancer_login_to_view = felan_get_option('enable_freelancer_login_to_view');

			if ((($enable_job_login_to_view == 1 && get_post_type() == 'jobs')
					|| ($enable_company_login_to_view == 1 && get_post_type() == 'company')
					|| ($enable_freelancer_login_to_view == 1 && get_post_type() == 'freelancer'))
				&& is_single() && !is_user_logged_in()
			) {
				$classes[] = 'felan-ltw';
			} else {
				$classes[] = '';
			}
			return $classes;
		}

		public function ai_form_generate($post_id)
		{
			$enable_ai_helper = felan_get_option('enable_ai_helper');
			$ai_key = felan_get_option('ai_key');

			if ($enable_ai_helper != 1 || $ai_key == '') {
				return;
			}
			?>
			<div id="ai-popup" class="ai-popup popup">
				<div class="bg-overlay"></div>
				<div class="inner-popup">
					<a href="#" class="btn-close">
						<i class="far fa-times"></i>
					</a>
					<h4><?php esc_html_e('Generate description', 'felan-framework'); ?></h4>
					<div class="generate-content">
						<div class="left">
							<form action="#" class="ai-generate">
								<div class="field-group">
									<label for="ai_prompt"><?php esc_html_e('Prompt', 'felan-framework'); ?></label>
									<div class="form-textarea">
										<textarea name="ai_prompt" id="ai_prompt" cols="30" rows="6"><?php
																										$post_title = empty(get_the_title($post_id)) ? '' : get_the_title($post_id);
																										if (get_post_type($post_id) == 'jobs') {
																											esc_html_e('Write a job description for a ' . $post_title . ' role at [Company Name]', 'felan-framework');
																										} elseif (get_post_type($post_id) == 'service') {
																											esc_html_e('Can you write a professional service description for ' . $post_title . '? It should be engaging, clear, and highlight my expertise and unique value.', 'felan-framework');
																										} elseif (get_post_type($post_id) == 'project') {
																											esc_html_e('Can you write a clear and engaging project description for ' . $post_title . '? It should outline the scope, requirements, and expectations to attract the right freelancers.', 'felan-framework');
																										} else {
																											esc_html_e('Write a description', 'felan-framework');
																										}
																										?>
										</textarea>
									</div>
								</div>
								<div class="field-group">
									<label for="ai_tone"><?php esc_html_e('Tone of voice', 'felan-framework') ?></label>
									<div class="form-select">
										<div class="select2-field select2-multiple">
											<select id="ai_tone" data-placeholder="<?php esc_attr_e('Select tone', 'felan-framework'); ?>" class="felan-select2" name="ai_tone">
												<?php
												if (tone_ai_helper()) {
													$ai_tone = felan_get_option('ai_tone');
													foreach (tone_ai_helper() as $tone_ai_helper_key => $tone_ai_helper_value) {
												?>
														<option value="<?php echo esc_attr($tone_ai_helper_key); ?>" <?php if ($ai_tone == $tone_ai_helper_key) {
																															echo 'selected';
																														} ?>><?php echo esc_html($tone_ai_helper_value); ?></option>
												<?php
													}
												}
												?>
											</select>
										</div>
									</div>
								</div>
								<div class="field-group">
									<label for="ai_language"><?php esc_html_e('Languages', 'felan-framework') ?></label>
									<div class="form-select">
										<div class="select2-field select2-multiple">
											<select id="ai_language" data-placeholder="<?php esc_attr_e('Select language', 'felan-framework'); ?>" class="felan-select2" name="ai_language">
												<?php
												if (language_ai_helper()) {
													$ai_language = felan_get_option('ai_language');
													foreach (language_ai_helper() as $language_ai_helper_key => $language_ai_helper_value) {
												?>
														<option value="<?php echo esc_attr($language_ai_helper_key); ?>" <?php if ($ai_language == $language_ai_helper_key) {
																																echo 'selected';
																															} ?>><?php echo esc_html($language_ai_helper_value); ?></option>
												<?php
													}
												}
												?>
											</select>
										</div>
									</div>
								</div>
								<div class="field-notice">
									<p></p>
								</div>
								<div class="field-submit">
									<button class="felan-button button-outline">
										<span class="text"><?php esc_html_e('Generate', 'felan-framework') ?></span>
										<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
									</button>
								</div>
							</form>
						</div>
						<div class="right">
							<h5><?php esc_html_e('Suggestion', 'felan-framework'); ?></h5>
							<div class="suggestion"></div>
							<a href="#" class="felan-button keep-generate"><i class="far fa-check"></i><?php esc_html_e('Keep', 'felan-framework'); ?></a>
						</div>
					</div>

				</div>
			</div>
<?php
		}

		// Define custom "From" name
		public function custom_wp_mail_from_name($name)
		{
			return get_bloginfo();
		}
	}
}
?>