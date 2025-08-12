<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Felan_Core')) {
	/**
	 *  The core plugin class
	 *  Class Felan_Core
	 */
	class Felan_Core
	{

		/**
		 * Instance variable for singleton pattern
		 */
		private static $instance = null;

		/**
		 * Return class instance
		 */
		public static function instance()
		{
			if (null == self::$instance) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Define the core functionality of the plugin
		 */
		public function __construct()
		{
			$this->include_library();
			$this->template_hooks();
			$this->admin_hooks();
		}

		/**
		 * Load the required dependencies for this plugin
		 */
		private function include_library()
		{
			require_once FELAN_PLUGIN_DIR . 'includes/felan-helper.php';
			require_once FELAN_PLUGIN_DIR . 'includes/felan-util.php';

			require_once FELAN_PLUGIN_DIR . 'includes/class-felan-capability.php';
			require_once FELAN_PLUGIN_DIR . 'includes/class-felan-template-loader.php';
			require_once FELAN_PLUGIN_DIR . 'includes/class-felan-shortcodes.php';
			require_once FELAN_PLUGIN_DIR . 'includes/class-felan-ajax.php';
			require_once FELAN_PLUGIN_DIR . 'includes/class-felan-user.php';
			require_once FELAN_PLUGIN_DIR . 'includes/class-felan-breadcrumb.php';
			require_once FELAN_PLUGIN_DIR . 'includes/class-felan-header.php';
			require_once FELAN_PLUGIN_DIR . 'includes/class-felan-footer.php';

			// Mega Menu
			require_once FELAN_PLUGIN_DIR . 'includes/mega-menu/class-mega-menu.php';
			require_once FELAN_PLUGIN_DIR . 'includes/mega-menu/class-walker-nav-menu.php';

			// Google Review
			include_once FELAN_PLUGIN_DIR . 'includes/google-review/class-google-review.php';

			// Export
			require_once FELAN_PLUGIN_DIR . 'includes/import/class-exporter.php';

			// Import
			require_once ABSPATH . '/wp-admin/includes/class-wp-importer.php';
			require_once FELAN_PLUGIN_DIR . 'includes/import/wp-importer/WXRImporter.php';
			require_once FELAN_PLUGIN_DIR . 'includes/import/wp-importer/WPImporterLogger.php';
			require_once FELAN_PLUGIN_DIR . 'includes/import/wp-importer/WPImporterLoggerCLI.php';
			require_once FELAN_PLUGIN_DIR . 'includes/import/class-wxrimporter.php';
			require_once FELAN_PLUGIN_DIR . 'includes/import/class-import-logger.php';
			require_once FELAN_PLUGIN_DIR . 'includes/import/class-importer.php';
			require_once FELAN_PLUGIN_DIR . 'includes/import/class-content-importer.php';
			require_once FELAN_PLUGIN_DIR . 'includes/import/class-widgets-importer.php';
			Felan_Importer::instance();

			// Update
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-updater.php';

			// Admin
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-plugins.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-setup.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-package.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-user-package.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-invoice.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-wallet-order.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-metaboxes.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-profile.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-schedule.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-rest-api.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-applicants.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-meetings.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-disputes.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-project-disputes.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-messages.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-notification.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-jobs.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-job-alerts.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-company.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-freelancer.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-freelancer-package.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-freelancer-order.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-service.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-service-order.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-freelancer-withdraw.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-employer-withdraw.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-project.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-project-order.php';
			require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-admin-project-proposal.php';

			// Partials
			include_once FELAN_PLUGIN_DIR . 'includes/partials/package/class-felan-package.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/payment/class-felan-payment.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/payment/class-felan-trans-log.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/invoice/class-felan-invoice.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/applicants/class-felan-applicants.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/meetings/class-felan-meetings.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/messages/class-felan-messages.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/notification/class-felan-notification.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/jobs/class-felan-jobs.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/company/class-felan-company.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/freelancer/class-felan-freelancer.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/freelancer/class-felan-freelancer-order.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/freelancer/class-felan-freelancer-package.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/freelancer/class-felan-freelancer-payment.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/freelancer/class-felan-freelancer-trans-log.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/freelancer/class-felan-freelancer-withdraw.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/service/class-felan-service.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/service/class-felan-service-order.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/service/class-felan-service-payment.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/project/class-felan-project.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/project/class-felan-project-order.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/project/class-felan-project-payment.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/wallet/class-felan-wallet-order.php';
			include_once FELAN_PLUGIN_DIR . 'includes/partials/wallet/class-felan-wallet-payment.php';
		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 */
		private function admin_hooks()
		{
			/**
			 * Hook Felan_Admin_Setup
			 */
			if (is_admin()) {
				$setup_page = new Felan_Admin_Setup();
				add_action('admin_menu', array($setup_page, 'admin_menu'), 12);
				add_action('admin_menu', array($setup_page, 'reorder_admin_menu'), 999);
			}

			/**
			 * Hook Felan_Admin
			 */
			$felan_admin = new Felan_Admin();
			add_filter('glf_meta_box_config', array($felan_admin, 'register_meta_boxes'));
			add_filter('glf_register_post_type', array($felan_admin, 'register_post_type'));
			add_filter('glf_register_taxonomy', array($felan_admin, 'register_taxonomy'));
			add_filter('glf_register_term_meta', array($felan_admin, 'register_term_meta'));

			add_filter('glf_option_config', array($felan_admin, 'register_options_config'));
			add_action('init', array($felan_admin, 'register_post_status'));
			add_action('after_setup_theme', array($felan_admin, 'remove_admin_bar'));

			/**
			 * Hook Felan_Admin_Jobs
			 */
			$felan_admin_jobs = new Felan_Admin_Jobs();
			add_filter('felan_jobs_slug', array($felan_admin_jobs, 'modify_jobs_slug'));
			add_filter('felan_jobs_has_archive', array($felan_admin_jobs, 'modify_jobs_has_archive'));
			add_filter('felan_jobs_type_slug', array($felan_admin_jobs, 'modify_jobs_type_slug'));
			add_filter('felan_jobs_tags_slug', array($felan_admin_jobs, 'modify_jobs_tags_slug'));
			add_filter('felan_jobs_categories_slug', array($felan_admin_jobs, 'modify_jobs_categories_slug'));
			add_filter('felan_jobs_skills_slug', array($felan_admin_jobs, 'modify_jobs_skills_slug'));
			add_filter('felan_jobs_location_slug', array($felan_admin_jobs, 'modify_jobs_location_slug'));
			add_filter('felan_jobs_career_slug', array($felan_admin_jobs, 'modify_jobs_career_slug'));
			add_filter('felan_jobs_experience_slug', array($felan_admin_jobs, 'modify_jobs_experience_slug'));
			add_filter('felan_jobs_qualification_slug', array($felan_admin_jobs, 'modify_jobs_qualification_slug'));
			add_filter('felan_jobs_gender_slug', array($felan_admin_jobs, 'modify_jobs_gender_slug'));
			add_action('restrict_manage_posts', array($felan_admin_jobs, 'filter_restrict_manage_jobs'));

			add_filter('parse_query', array($felan_admin_jobs, 'jobs_filter'));
			add_action('admin_init', array($felan_admin_jobs, 'approve_jobs'));
			add_action('admin_init', array($felan_admin_jobs, 'expire_jobs'));
			add_action('admin_init', array($felan_admin_jobs, 'hidden_jobs'));
			add_action('admin_init', array($felan_admin_jobs, 'show_jobs'));

			add_action('wp_ajax_felan_action_claim_listing', array($felan_admin_jobs, 'action_claim_listing'));
			add_action('wp_ajax_nopriv_felan_action_claim_listing', array($felan_admin_jobs, 'action_claim_listing'));

			add_action('wp_ajax_auto_description_generate', array($felan_admin_jobs, 'auto_description_generate'));
			add_action('wp_ajax_nopriv_auto_description_generate', array($felan_admin_jobs, 'auto_description_generate'));

			$felan_admin_job_alerts = new Felan_Admin_Job_Alerts();

			/**
			 * Hook Felan_Package_Admin
			 */
			$felan_admin_package = new Felan_Admin_Package();
			add_filter('felan_package_slug', array($felan_admin_package, 'modify_package_slug'));

			/**
			 * Hook Felan_Admin_freelancer_package
			 */
			$felan_admin_freelancer_package = new Felan_Admin_freelancer_package();
			add_filter('felan_freelancer_package_slug', array($felan_admin_freelancer_package, 'modify_freelancer_package_slug'));

			// User Packages Post Type
			$felan_user_package_admin = new Felan_User_Package_Admin();
			add_filter('felan_user_package_slug', array($felan_user_package_admin, 'modify_user_package_slug'));
			add_action('restrict_manage_posts', array($felan_user_package_admin, 'filter_restrict_manage_user_package'));
			add_action('before_delete_post', array($felan_user_package_admin, 'action_delete_post'));
			add_filter('parse_query', array($felan_user_package_admin, 'user_package_filter'));

			/**
			 * Hook Felan_Invoice_Admin
			 */
			$felan_admin_invoice = new Felan_Admin_Invoice();
			add_action('felan_invoice_slug', array($felan_admin_invoice, 'modify_invoice_slug'));
			add_action('restrict_manage_posts', array($felan_admin_invoice, 'filter_restrict_manage_invoice'));
			add_action('parse_query', array($felan_admin_invoice, 'invoice_filter'));
			add_action('admin_init', array($felan_admin_invoice, 'invoice_active'));
			add_action('admin_init', array($felan_admin_invoice, 'invoice_pending'));

			/**
			 * Hook Felan_Admin_Applicants
			 */
			$felan_admin_applicants = new Felan_Admin_Applicants();
			add_action('felan_applicants_slug', array($felan_admin_applicants, 'modify_applicants_slug'));
			add_action('restrict_manage_posts', array($felan_admin_applicants, 'filter_restrict_manage_applicants'));
			add_action('parse_query', array($felan_admin_applicants, 'applicants_filter'));

            /**
             * Hook Felan_Meetings_Admin
             */
            $felan_admin_meetings = new Felan_Admin_Meetings();
            add_action('felan_meetings_slug', array($felan_admin_meetings, 'modify_meetings_slug'));
            add_action('restrict_manage_posts', array($felan_admin_meetings, 'filter_restrict_manage_meetings'));
            add_action('parse_query', array($felan_admin_meetings, 'meetings_filter'));

			/**
			 * Hook Felan_Admin_Messages
			 */
			$felan_admin_messages = new Felan_Admin_Messages();
			add_action('parse_query', array($felan_admin_messages, 'messages_filter'));

			/**
			 * Hook Felan_Admin_Notification
			 */
			$felan_admin_notification = new Felan_Admin_Notification();
			add_action('parse_query', array($felan_admin_notification, 'notification_filter'));

			/**
			 * Hook Felan_Admin_freelancer_order
			 */
			$felan_admin_freelancer_order = new Felan_Admin_freelancer_order();
			add_action('parse_query', array($felan_admin_freelancer_order, 'freelancer_order_filter'));
			add_action('restrict_manage_posts', array($felan_admin_freelancer_order, 'filter_restrict_manage_freelancer_order'));
			add_action('admin_init', array($felan_admin_freelancer_order, 'freelancer_order_active'));
			add_action('admin_init', array($felan_admin_freelancer_order, 'freelancer_order_pending'));

			/**
			 * Hook Felan_Admin_service_order
			 */
			$felan_admin_service_order = new Felan_Admin_service_order();
			add_action('parse_query', array($felan_admin_service_order, 'service_order_filter'));
			add_action('restrict_manage_posts', array($felan_admin_service_order, 'filter_restrict_manage_service_order'));
			add_action('admin_init', array($felan_admin_service_order, 'service_order_inprogress'));
			add_action('admin_init', array($felan_admin_service_order, 'service_order_pending'));

            /**
             * Hook Felan_Disputes_Admin
             */
            $felan_admin_disputes = new Felan_Admin_Disputes();
            add_action('parse_query', array($felan_admin_disputes, 'disputes_filter'));
            add_action('restrict_manage_posts', array($felan_admin_disputes, 'filter_restrict_manage_disputes'));

            /**
             * Hook Felan_Admin_Project_Disputes
             */
            $felan_admin_project_disputes = new Felan_Admin_Project_Disputes();
            add_action('parse_query', array($felan_admin_project_disputes, 'disputes_filter'));
            add_action('restrict_manage_posts', array($felan_admin_project_disputes, 'filter_restrict_manage_disputes'));

			/**
			 * Hook Felan_Admin_freelancer_withdraw
			 */
			$felan_admin_freelancer_withdraw = new Felan_Admin_freelancer_withdraw();
			add_action('parse_query', array($felan_admin_freelancer_withdraw, 'freelancer_withdraw_filter'));
			add_action('restrict_manage_posts', array($felan_admin_freelancer_withdraw, 'filter_restrict_manage_freelancer_withdraw'));
			add_action('admin_init', array($felan_admin_freelancer_withdraw, 'freelancer_withdraw_active'));
			add_action('admin_init', array($felan_admin_freelancer_withdraw, 'freelancer_withdraw_pending'));
			add_action('admin_init', array($felan_admin_freelancer_withdraw, 'freelancer_withdraw_canceled'));


            /**
             * Hook Felan_Admin_employer_withdraw
             */
            $felan_admin_employer_withdraw = new Felan_Admin_employer_withdraw();
            add_action('parse_query', array($felan_admin_employer_withdraw, 'employer_withdraw_filter'));
            add_action('restrict_manage_posts', array($felan_admin_employer_withdraw, 'filter_restrict_manage_employer_withdraw'));
            add_action('admin_init', array($felan_admin_employer_withdraw, 'employer_withdraw_active'));
            add_action('admin_init', array($felan_admin_employer_withdraw, 'employer_withdraw_pending'));
            add_action('admin_init', array($felan_admin_employer_withdraw, 'employer_withdraw_canceled'));

			/**
			 * Hook Felan_Admin_Project_Proposal
			 */
			$felan_admin_project_proposal = new Felan_Admin_Project_Proposal();
			add_action('felan_applicants_slug', array($felan_admin_project_proposal, 'modify_project_proposal_slug'));
			add_action('restrict_manage_posts', array($felan_admin_project_proposal, 'filter_restrict_manage_project_proposal'));
			add_action('parse_query', array($felan_admin_project_proposal, 'project_proposal_filter'));

			/**
			 * Hook Felan_Admin_project_order
			 */
			$felan_admin_project_order = new Felan_Admin_project_order();
			add_action('parse_query', array($felan_admin_project_order, 'project_order_filter'));
			add_action('restrict_manage_posts', array($felan_admin_project_order, 'filter_restrict_manage_project_order'));
			add_action('admin_init', array($felan_admin_project_order, 'project_order_approved'));
			add_action('admin_init', array($felan_admin_project_order, 'project_order_pending'));

			/**
			 * Hook Felan_Commany_Admin
			 */
			$felan_admin_company = new Felan_Admin_Company();
			add_filter('felan_company_slug', array($felan_admin_company, 'modify_company_url_slug'));
			add_filter('felan_company_has_archive', array($felan_admin_company, 'modify_company_has_archive'));
			add_filter('felan_company_categories_slug', array($felan_admin_company, 'modify_company_categories_url_slug'));
			add_filter('felan_company_location_slug', array($felan_admin_company, 'modify_company_location_url_slug'));
			add_filter('felan_company_size_slug', array($felan_admin_company, 'modify_company_size_url_slug'));
			add_action('restrict_manage_posts', array($felan_admin_company, 'filter_restrict_manage_company'));
			add_action('parse_query', array($felan_admin_company, 'company_filter'));
			add_action('admin_init', array($felan_admin_company, 'approve_company'));
			add_action('admin_init', array($felan_admin_company, 'add_badge_menu'));

			/**
			 * Hook Felan_Admin_Freelancer
			 */
			$felan_admin_freelancer = new Felan_Admin_Freelancer();
			add_filter('felan_freelancer_slug', array($felan_admin_freelancer, 'modify_freelancer_slug'));
			add_filter('felan_freelancer_has_archive', array($felan_admin_freelancer, 'modify_freelancer_has_archive'));
			add_filter('felan_freelancer_categories_slug', array($felan_admin_freelancer, 'modify_freelancer_categories_url_slug'));
			add_filter('felan_freelancer_ages_slug', array($felan_admin_freelancer, 'modify_freelancer_ages_url_slug'));
			add_filter('felan_freelancer_languages_slug', array($felan_admin_freelancer, 'modify_freelancer_languages_url_slug'));
			add_filter('felan_freelancer_qualification_slug', array($felan_admin_freelancer, 'modify_freelancer_qualification_url_slug'));
			add_filter('felan_freelancer_yoe_slug', array($felan_admin_freelancer, 'modify_freelancer_yoe_url_slug'));
			add_filter('felan_freelancer_salary_types_slug', array($felan_admin_freelancer, 'modify_freelancer_salary_types_url_slug'));
			add_filter('felan_freelancer_education_levels_slug', array($felan_admin_freelancer, 'modify_freelancer_education_levels_url_slug'));
			add_filter('felan_freelancer_skills_slug', array($felan_admin_freelancer, 'modify_freelancer_skills_url_slug'));
			add_filter('felan_freelancer_gender_slug', array($felan_admin_freelancer, 'modify_freelancer_gender_url_slug'));
			add_filter('felan_freelancer_locations_slug', array($felan_admin_freelancer, 'modify_freelancer_locations_url_slug'));
			add_action('restrict_manage_posts', array($felan_admin_freelancer, 'filter_restrict_manage_freelancer'));
			add_action('parse_query', array($felan_admin_freelancer, 'freelancer_filter'));
			add_action('admin_init', array($felan_admin_freelancer, 'show_freelancers'));
			add_action('admin_init', array($felan_admin_freelancer, 'approve_freelancer'));
			add_action('admin_init', array($felan_admin_freelancer, 'add_badge_menu'));

			/**
             * Hook felan_verifications
             */
			add_action('wp_ajax_felan_verifications', array($felan_admin_freelancer, 'felan_verifications'));
			add_action('wp_ajax_nopriv_felan_verifications', array($felan_admin_freelancer, 'felan_verifications'));

			add_action('wp_ajax_felan_cancel_verifications', array($felan_admin_freelancer, 'felan_cancel_verifications'));
			add_action('wp_ajax_nopriv_felan_cancel_verifications', array($felan_admin_freelancer, 'felan_cancel_verifications'));

			add_action('wp_ajax_felan_user_verifications_info', array($felan_admin_freelancer, 'felan_user_verifications_info'));


			/**
			 * Hook Felan_Admin_Freelancer
			 */
			$felan_admin_service = new Felan_Admin_Service();
			add_filter('felan_service_slug', array($felan_admin_service, 'modify_service_slug'));
			add_filter('felan_service_has_archive', array($felan_admin_service, 'modify_service_has_archive'));
			add_filter('felan_service_categories_slug', array($felan_admin_service, 'modify_service_categories_url_slug'));
			add_filter('felan_service_skills_slug', array($felan_admin_service, 'modify_service_skills_url_slug'));
			add_filter('felan_service_location_slug', array($felan_admin_service, 'modify_service_location_url_slug'));
			add_filter('felan_service_language_slug', array($felan_admin_service, 'modify_service_language_url_slug'));
			add_action('restrict_manage_posts', array($felan_admin_service, 'filter_restrict_manage_service'));
			add_action('parse_query', array($felan_admin_service, 'service_filter'));
			add_action('admin_init', array($felan_admin_service, 'approve_service'));

			/**
			 * Hook Felan_Admin_Project
			 */
			$felan_admin_project = new Felan_Admin_Project();
			add_filter('felan_project_slug', array($felan_admin_project, 'modify_project_slug'));
			add_filter('felan_project_has_archive', array($felan_admin_project, 'modify_project_has_archive'));
			add_filter('felan_project_categories_slug', array($felan_admin_project, 'modify_project_categories_url_slug'));
			add_filter('felan_project_skills_slug', array($felan_admin_project, 'modify_project_skills_url_slug'));
			add_filter('felan_project_location_slug', array($felan_admin_project, 'modify_project_location_url_slug'));
			add_filter('felan_project_language_slug', array($felan_admin_project, 'modify_project_language_url_slug'));
			add_action('restrict_manage_posts', array($felan_admin_project, 'filter_restrict_manage_project'));
			add_action('parse_query', array($felan_admin_project, 'project_filter'));
			add_action('admin_init', array($felan_admin_project, 'approve_project'));

            /**
             * Hook Felan_Order
             */
            $felan_admin_wallet_order = new Felan_Admin_Wallet_Order();
            add_action('parse_query', array($felan_admin_wallet_order, 'order_filter'));
            add_action('restrict_manage_posts', array($felan_admin_wallet_order, 'filter_restrict_manage_wallet_order'));
            add_action('admin_init', array($felan_admin_wallet_order, 'order_approve'));
            add_action('admin_init', array($felan_admin_wallet_order, 'order_pending'));

			/**
			 * Hook Felan_Rest_API
			 */
			$felan_rest_api = new Felan_Rest_API();
			add_action('rest_api_init', array($felan_rest_api, 'register_fields_api'));

			$profile = new Felan_Profile();
			add_filter('show_user_profile', array($profile, 'custom_user_profile_fields'));
			add_filter('edit_user_profile', array($profile, 'custom_user_profile_fields'));
			add_action('edit_user_profile_update', array($profile, 'update_custom_user_profile_fields'));
			add_action('personal_options_update', array($profile, 'update_custom_user_profile_fields'));
			add_action('admin_head', array($profile, 'my_profile_upload_js'));

			/**
			 * Hook Felan_Plugins
			 */
			$felan_plugins = new Felan_Plugins();
			add_action('wp_ajax_process_plugin_actions', array($felan_plugins, 'process_plugin_actions'));
			add_action('wp_ajax_nopriv_process_plugin_actions', array($felan_plugins, 'process_plugin_actions'));

			/**
			 * Hook Felan_Metaboxes
			 */
			$felan_metaboxes = new Felan_Metaboxes();
			add_action('load-post.php', array($felan_metaboxes, 'meta_boxes_setup'));
			add_action('load-post-new.php', array($felan_metaboxes, 'meta_boxes_setup'));

			/**
			 * Hook Felan Schedule
			 */
			$felan_schedule = new Felan_Schedule();
			register_deactivation_hook(__FILE__, array($felan_schedule, 'felan_per_listing_check_expire'));
			add_action('init', array($felan_schedule, 'scheduled_hook'));
			add_action('felan_per_listing_check_expire', array($felan_schedule, 'per_listing_check_expire'));

			if (is_admin()) {
				global $pagenow;

				// freelancers custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'freelancer') {
					add_filter('manage_edit-freelancer_columns', array($felan_admin_freelancer, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_freelancer, 'display_custom_column'));
					add_filter('manage_edit-freelancer_sortable_columns', array($felan_admin_freelancer, 'sortable_columns'));
					add_filter('post_row_actions', array($felan_admin_freelancer, 'modify_list_row_actions'), 10, 2);
				}

				// service custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'service') {
					add_filter('manage_edit-service_columns', array($felan_admin_service, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_service, 'display_custom_column'));
					add_filter('manage_edit-service_sortable_columns', array($felan_admin_service, 'sortable_columns'));
					add_filter('post_row_actions', array($felan_admin_service, 'modify_list_row_actions'), 10, 2);
				}

				// project custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'project') {
					add_filter('manage_edit-project_columns', array($felan_admin_project, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_project, 'display_custom_column'));
					add_filter('manage_edit-project_sortable_columns', array($felan_admin_project, 'sortable_columns'));
					add_filter('post_row_actions', array($felan_admin_project, 'modify_list_row_actions'), 10, 2);
				}

				// jobs custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'jobs') {
					add_filter('manage_edit-jobs_columns', array($felan_admin_jobs, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_jobs, 'display_custom_column'));
					add_filter('manage_edit-jobs_sortable_columns', array($felan_admin_jobs, 'sortable_columns'));
					add_filter('request', array($felan_admin_jobs, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_jobs, 'modify_list_row_actions'), 10, 2);
				}

				// job alerts custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'job_alerts') {
					add_filter('manage_edit-job_alerts_columns', array($felan_admin_job_alerts, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_job_alerts, 'display_custom_column'));
					add_filter('manage_edit-job_alerts_sortable_columns', array($felan_admin_job_alerts, 'sortable_columns'));
				}

				// package custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'package') {
					add_filter('manage_edit-package_columns', array($felan_admin_package, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_package, 'display_custom_column'));
					add_filter('post_row_actions', array($felan_admin_package, 'modify_list_row_actions'), 10, 2);
				}

				// user package custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'user_package') {
					add_filter('manage_edit-user_package_columns', array($felan_user_package_admin, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_user_package_admin, 'display_custom_column'));
					add_action('before_delete_post', array($felan_user_package_admin, 'action_delete_post'));
					add_filter('post_row_actions', array($felan_user_package_admin, 'modify_list_row_actions'), 10, 2);
				}

				// Invoice custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'invoice') {
					add_filter('manage_edit-invoice_columns', array($felan_admin_invoice, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_invoice, 'display_custom_column'));
					add_filter('manage_edit-invoice_sortable_columns', array($felan_admin_invoice, 'sortable_columns'));
					add_filter('request', array($felan_admin_invoice, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_invoice, 'modify_list_row_actions'), 10, 2);
				}

				// Company custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'company') {
					add_filter('manage_edit-company_columns', array($felan_admin_company, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_company, 'display_custom_column'));
					add_filter('manage_edit-company_sortable_columns', array($felan_admin_company, 'sortable_columns'));
					add_filter('post_row_actions', array($felan_admin_company, 'modify_list_row_actions'), 10, 2);
				}

				// Applicants custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'applicants') {
					add_filter('manage_edit-applicants_columns', array($felan_admin_applicants, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_applicants, 'display_custom_column'));
					add_filter('manage_edit-applicants_sortable_columns', array($felan_admin_applicants, 'sortable_columns'));
					add_filter('request', array($felan_admin_applicants, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_applicants, 'modify_list_row_actions'), 10, 2);
				}

				// Meetings custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'meetings') {
					add_filter('manage_edit-meetings_columns', array($felan_admin_meetings, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_meetings, 'display_custom_column'));
					add_filter('manage_edit-meetings_sortable_columns', array($felan_admin_meetings, 'sortable_columns'));
					add_filter('request', array($felan_admin_meetings, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_meetings, 'modify_list_row_actions'), 10, 2);
				}

				// Messages custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'messages') {
					add_filter('manage_edit-messages_columns', array($felan_admin_messages, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_messages, 'display_custom_column'));
					add_filter('manage_edit-messages_sortable_columns', array($felan_admin_messages, 'sortable_columns'));
					add_filter('request', array($felan_admin_messages, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_messages, 'modify_list_row_actions'), 10, 2);
				}

				// Notification custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'notification') {
					add_filter('manage_edit-notification_columns', array($felan_admin_notification, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_notification, 'display_custom_column'));
					add_filter('manage_edit-notification_sortable_columns', array($felan_admin_notification, 'sortable_columns'));
					add_filter('request', array($felan_admin_notification, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_notification, 'modify_list_row_actions'), 10, 2);
				}

				// Freelancer order custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'freelancer_order') {
					add_filter('manage_edit-freelancer_order_columns', array($felan_admin_freelancer_order, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_freelancer_order, 'display_custom_column'));
					add_filter('manage_edit-freelancer_order_sortable_columns', array($felan_admin_freelancer_order, 'sortable_columns'));
					add_filter('request', array($felan_admin_freelancer_order, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_freelancer_order, 'modify_list_row_actions'), 10, 2);
				}

				// Freelancer package custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'freelancer_package') {
					add_filter('manage_edit-freelancer_package_columns', array($felan_admin_freelancer_package, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_freelancer_package, 'display_custom_column'));
					add_filter('post_row_actions', array($felan_admin_freelancer_package, 'modify_list_row_actions'), 10, 2);
				}

				// Service order custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'service_order') {
					add_filter('manage_edit-service_order_columns', array($felan_admin_service_order, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_service_order, 'display_custom_column'));
					add_filter('manage_edit-service_order_sortable_columns', array($felan_admin_service_order, 'sortable_columns'));
					add_filter('request', array($felan_admin_service_order, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_service_order, 'modify_list_row_actions'), 10, 2);
				}

				// Freelancer Withdraw custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'freelancer_withdraw') {
					add_filter('manage_edit-freelancer_withdraw_columns', array($felan_admin_freelancer_withdraw, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_freelancer_withdraw, 'display_custom_column'));
					add_filter('manage_edit-freelancer_withdraw_sortable_columns', array($felan_admin_freelancer_withdraw, 'sortable_columns'));
					add_filter('request', array($felan_admin_freelancer_withdraw, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_freelancer_withdraw, 'modify_list_row_actions'), 10, 2);
				}

                // Employer Withdraw custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'employer_withdraw') {
                    add_filter('manage_edit-employer_withdraw_columns', array($felan_admin_employer_withdraw, 'register_custom_column_titles'));
                    add_action('manage_posts_custom_column', array($felan_admin_employer_withdraw, 'display_custom_column'));
                    add_filter('manage_edit-employer_withdraw_sortable_columns', array($felan_admin_employer_withdraw, 'sortable_columns'));
                    add_filter('request', array($felan_admin_employer_withdraw, 'column_orderby'));
                    add_filter('post_row_actions', array($felan_admin_employer_withdraw, 'modify_list_row_actions'), 10, 2);
                }

				// Project proposal custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'project-proposal') {
					add_filter('manage_edit-project-proposal_columns', array($felan_admin_project_proposal, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_project_proposal, 'display_custom_column'));
					add_filter('manage_edit-project-proposal_sortable_columns', array($felan_admin_project_proposal, 'sortable_columns'));
					add_filter('request', array($felan_admin_project_proposal, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_project_proposal, 'modify_list_row_actions'), 10, 2);
				}

				// Project order custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'project_order') {
					add_filter('manage_edit-project_order_columns', array($felan_admin_project_order, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($felan_admin_project_order, 'display_custom_column'));
					add_filter('manage_edit-project_order_sortable_columns', array($felan_admin_project_order, 'sortable_columns'));
					add_filter('request', array($felan_admin_project_order, 'column_orderby'));
					add_filter('post_row_actions', array($felan_admin_project_order, 'modify_list_row_actions'), 10, 2);
				}

                // Service disputes custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'disputes') {
                    add_filter('manage_edit-disputes_columns', array($felan_admin_disputes, 'register_custom_column_titles'));
                    add_action('manage_posts_custom_column', array($felan_admin_disputes, 'display_custom_column'));
                    add_filter('manage_edit-disputes_sortable_columns', array($felan_admin_disputes, 'sortable_columns'));
                    add_filter('request', array($felan_admin_disputes, 'column_orderby'));
                }

                // Project disputes custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'project_disputes') {
                    add_filter('manage_edit-project_disputes_columns', array($felan_admin_project_disputes, 'register_custom_column_titles'));
                    add_action('manage_posts_custom_column', array($felan_admin_project_disputes, 'display_custom_column'));
                    add_filter('manage_edit-project_disputes_sortable_columns', array($felan_admin_project_disputes, 'sortable_columns'));
                    add_filter('request', array($felan_admin_project_disputes, 'column_orderby'));
                }

                // Wallet order custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'wallet_order') {
                    add_filter('manage_edit-wallet_order_columns', array($felan_admin_wallet_order, 'register_custom_column_titles'));
                    add_action('manage_posts_custom_column', array($felan_admin_wallet_order, 'display_custom_column'));
                    add_filter('manage_edit-wallet_order_sortable_columns', array($felan_admin_wallet_order, 'sortable_columns'));
                    add_filter('request', array($felan_admin_wallet_order, 'column_orderby'));
                    add_filter('post_row_actions', array($felan_admin_wallet_order, 'modify_list_row_actions'), 10, 2);
                }
			}
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 */
		private function template_hooks()
		{
			/**
			 * Hook Felan_Template_Loader
			 */
			$felan_template_loader = new Felan_Template_Loader();

			add_action('felan_apply_single_jobs', array($felan_template_loader, 'felan_form_apply_jobs'), 1);
			add_action('wp_footer', array($felan_template_loader, 'felan_form_setting_meetings'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_reschedule_meeting'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_setting_messages'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_invite_freelancer'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_mess_applicants'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_setting_deactive'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_freelancer_user_package'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_employer_user_package'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_service_order_refund'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_service_view_reason'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_freelancer_withdraw'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_employer_withdraw'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_project_order_refund'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_project_view_reason'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_single_popup'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_apply_project'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_employer_review'));
            add_action('wp_footer', array($felan_template_loader, 'felan_form_employer_view_review'));
            add_action('wp_footer', array($felan_template_loader, 'felan_form_freelancer_review'));
			add_action('wp_footer', array($felan_template_loader, 'felan_form_service_review'));
            add_action('wp_footer', array($felan_template_loader, 'felan_form_service_view_review'));
            add_action('wp_footer', array($felan_template_loader, 'felan_form_wallet_topup'));

            add_action('wp_head', array($felan_template_loader, 'felan_add_google_job_schema'));
			add_action('send_meeting_notification', array($felan_template_loader, 'send_meeting_notification'));
			add_action('init', array($felan_template_loader, 'setup_meeting_notifications'));

			add_action('post_type_link', array($felan_template_loader, 'wpa_show_permalinks'), 1, 2);
			add_action('init', array($felan_template_loader, 'generated_rewrite_rules'));

			add_filter('template_include', array($felan_template_loader, 'template_loader'));
			add_action('admin_enqueue_scripts', array($felan_template_loader, 'admin_enqueue'));
			add_action('wp_enqueue_scripts', array($felan_template_loader, 'enqueue_styles'));
			add_action('wp_enqueue_scripts', array($felan_template_loader, 'enqueue_scripts'));

			/**
			 * Hook Felan_Ajax
			 */
			$felan_ajax = new Felan_Ajax();
            //Employer Project Order
            add_action('wp_ajax_felan_project_order_message', array($felan_ajax, 'felan_project_order_message'));
            add_action('wp_ajax_nopriv_felan_project_order_message', array($felan_ajax, 'felan_project_order_message'));

            //Employer Service isputes Order
            add_action('wp_ajax_felan_disputes_message', array($felan_ajax, 'felan_disputes_message'));
            add_action('wp_ajax_nopriv_felan_disputes_message', array($felan_ajax, 'felan_disputes_message'));

            //Employer Service Order
            add_action('wp_ajax_felan_service_order_message', array($felan_ajax, 'felan_service_order_message'));
            add_action('wp_ajax_nopriv_felan_service_order_message', array($felan_ajax, 'felan_service_order_message'));

			//Switch Account
			add_action('wp_ajax_felan_switch_account_ajax', array($felan_ajax, 'felan_switch_account_ajax'));
			add_action('wp_ajax_nopriv_felan_switch_account_ajax', array($felan_ajax, 'felan_switch_account_ajax'));

			//Search Ajax
			add_action('wp_ajax_felan_canvas_search_ajax', array($felan_ajax, 'felan_canvas_search_ajax'));
			add_action('wp_ajax_nopriv_felan_canvas_search_ajax', array($felan_ajax, 'felan_canvas_search_ajax'));

			//Login
			add_action('wp_ajax_get_login_user', array($felan_ajax, 'get_login_user'));
			add_action('wp_ajax_nopriv_get_login_user', array($felan_ajax, 'get_login_user'));

			add_action('wp_ajax_get_register_user', array($felan_ajax, 'get_register_user'));
			add_action('wp_ajax_nopriv_get_register_user', array($felan_ajax, 'get_register_user'));

			add_action('wp_ajax_verify_code', array($felan_ajax, 'verify_code'));
			add_action('wp_ajax_nopriv_verify_code', array($felan_ajax, 'verify_code'));

			add_action('wp_ajax_felan_verify_resend', array($felan_ajax, 'felan_verify_resend'));
			add_action('wp_ajax_nopriv_felan_verify_resend', array($felan_ajax, 'felan_verify_resend'));

			add_action('wp_ajax_fb_ajax_login_or_register', array($felan_ajax, 'fb_ajax_login_or_register'));
			add_action('wp_ajax_nopriv_fb_ajax_login_or_register', array($felan_ajax, 'fb_ajax_login_or_register'));

			add_action('wp_ajax_google_ajax_login_or_register', array($felan_ajax, 'google_ajax_login_or_register'));
			add_action('wp_ajax_nopriv_google_ajax_login_or_register', array(
				$felan_ajax,
				'google_ajax_login_or_register'
			));

			add_action('wp_ajax_get_script_social_login', array($felan_ajax, 'get_script_social_login'));
			add_action('wp_ajax_nopriv_get_script_social_login', array($felan_ajax, 'get_script_social_login'));

			add_action('wp_ajax_keyup_site_search', array($felan_ajax, 'keyup_site_search'));
			add_action('wp_ajax_nopriv_keyup_site_search', array($felan_ajax, 'keyup_site_search'));

			// Reset password
			add_action('wp_ajax_felan_reset_password_ajax', array($felan_ajax, 'reset_password_ajax'));
			add_action('wp_ajax_nopriv_felan_reset_password_ajax', array($felan_ajax, 'reset_password_ajax'));

			add_action('wp_ajax_change_password_ajax', array($felan_ajax, 'change_password_ajax'));
			add_action('wp_ajax_nopriv_change_password_ajax', array($felan_ajax, 'change_password_ajax'));

			//Job
			add_action('wp_ajax_preview_job', array($felan_ajax, 'preview_job'));
			add_action('wp_ajax_nopriv_preview_job', array($felan_ajax, 'preview_job'));

			add_action('wp_ajax_felan_jobs_archive_ajax', array($felan_ajax, 'felan_jobs_archive_ajax'));
			add_action('wp_ajax_nopriv_felan_jobs_archive_ajax', array($felan_ajax, 'felan_jobs_archive_ajax'));

			add_action('wp_ajax_felan_company_archive_ajax', array($felan_ajax, 'felan_company_archive_ajax'));
			add_action('wp_ajax_nopriv_felan_company_archive_ajax', array($felan_ajax, 'felan_company_archive_ajax'));

			add_action('wp_ajax_felan_service_archive_ajax', array($felan_ajax, 'felan_service_archive_ajax'));
			add_action('wp_ajax_nopriv_felan_service_archive_ajax', array($felan_ajax, 'felan_service_archive_ajax'));

			add_action('wp_ajax_felan_project_archive_ajax', array($felan_ajax, 'felan_project_archive_ajax'));
			add_action('wp_ajax_nopriv_felan_project_archive_ajax', array($felan_ajax, 'felan_project_archive_ajax'));

			add_action('wp_ajax_felan_filter_jobs_dashboard', array($felan_ajax, 'felan_filter_jobs_dashboard'));
			add_action('wp_ajax_nopriv_felan_filter_jobs_dashboard', array($felan_ajax, 'felan_filter_jobs_dashboard'));

			add_action('wp_ajax_felan_filter_applicants_dashboard', array($felan_ajax, 'felan_filter_applicants_dashboard'));
			add_action('wp_ajax_nopriv_felan_filter_applicants_dashboard', array($felan_ajax, 'felan_filter_applicants_dashboard'));

			add_action('wp_ajax_felan_read_mess_ajax_load', array($felan_ajax, 'felan_read_mess_ajax_load'));
			add_action('wp_ajax_nopriv_felan_read_mess_ajax_load', array($felan_ajax, 'felan_read_mess_ajax_load'));

			add_action('wp_ajax_felan_realy_mess_ajax_load', array($felan_ajax, 'felan_realy_mess_ajax_load'));
			add_action('wp_ajax_nopriv_felan_realy_mess_ajax_load', array($felan_ajax, 'felan_realy_mess_ajax_load'));

			add_action('wp_ajax_felan_realy_mess_project_ajax_load', array($felan_ajax, 'felan_realy_mess_project_ajax_load'));
			add_action('wp_ajax_nopriv_felan_realy_mess_project_ajax_load', array($felan_ajax, 'felan_realy_mess_project_ajax_load'));

			add_action('wp_ajax_felan_filter_my_wishlist', array($felan_ajax, 'felan_filter_my_wishlist'));
			add_action('wp_ajax_nopriv_felan_filter_my_wishlist', array($felan_ajax, 'felan_filter_my_wishlist'));

			add_action('wp_ajax_felan_filter_employer_wishlist', array($felan_ajax, 'felan_filter_employer_wishlist'));
			add_action('wp_ajax_nopriv_felan_filter_employer_wishlist', array($felan_ajax, 'felan_filter_employer_wishlist'));

			add_action('wp_ajax_felan_filter_my_follow', array($felan_ajax, 'felan_filter_my_follow'));
			add_action('wp_ajax_nopriv_felan_filter_my_follow', array($felan_ajax, 'felan_filter_my_follow'));

			add_action('wp_ajax_felan_filter_my_review', array($felan_ajax, 'felan_filter_my_review'));
			add_action('wp_ajax_nopriv_felan_filter_my_review', array($felan_ajax, 'felan_filter_my_review'));

			add_action('wp_ajax_felan_filter_my_invite', array($felan_ajax, 'felan_filter_my_invite'));
			add_action('wp_ajax_nopriv_felan_filter_my_invite', array($felan_ajax, 'felan_filter_my_invite'));

			add_action('wp_ajax_felan_filter_follow_freelancer', array($felan_ajax, 'felan_filter_follow_freelancer'));
			add_action('wp_ajax_nopriv_felan_filter_follow_freelancer', array($felan_ajax, 'felan_filter_follow_freelancer'));

			add_action('wp_ajax_felan_filter_invite_freelancer', array($felan_ajax, 'felan_filter_invite_freelancer'));
			add_action('wp_ajax_nopriv_felan_filter_invite_freelancer', array($felan_ajax, 'felan_filter_invite_freelancer'));

			add_action('wp_ajax_felan_filter_my_apply', array($felan_ajax, 'felan_filter_my_apply'));
			add_action('wp_ajax_nopriv_felan_filter_my_apply', array($felan_ajax, 'felan_filter_my_apply'));

			add_action('wp_ajax_felan_filter_company_dashboard', array($felan_ajax, 'felan_filter_company_dashboard'));
			add_action('wp_ajax_nopriv_felan_filter_company_dashboard', array($felan_ajax, 'felan_filter_company_dashboard'));

			add_action('wp_ajax_felan_company_related', array($felan_ajax, 'felan_company_related'));
			add_action('wp_ajax_nopriv_felan_company_related', array($felan_ajax, 'felan_company_related'));

			add_action('wp_ajax_felan_filter_freelancers_dashboard', array($felan_ajax, 'felan_filter_freelancers_dashboard'));
			add_action('wp_ajax_nopriv_felan_filter_freelancers_dashboard', array($felan_ajax, 'felan_filter_freelancers_dashboard'));

			add_action('wp_ajax_felan_update_profile_ajax', array($felan_ajax, 'felan_update_profile_ajax'));
			add_action('wp_ajax_nopriv_felan_update_profile_ajax', array($felan_ajax, 'felan_update_profile_ajax'));

			add_action('wp_ajax_felan_change_password_ajax', array($felan_ajax, 'felan_change_password_ajax'));
			add_action('wp_ajax_nopriv_felan_change_password_ajax', array($felan_ajax, 'felan_change_password_ajax'));

			//update payout
			add_action('wp_ajax_felan_update_payout_ajax', array($felan_ajax, 'felan_update_payout_ajax'));
			add_action('wp_ajax_nopriv_felan_update_payout_ajax', array($felan_ajax, 'felan_update_payout_ajax'));

			//chart jobs
			add_action('wp_ajax_felan_chart_ajax', array($felan_ajax, 'felan_chart_ajax'));
			add_action('wp_ajax_nopriv_felan_chart_ajax', array($felan_ajax, 'felan_chart_ajax'));

			//chart project
			add_action('wp_ajax_felan_chart_project_ajax', array($felan_ajax, 'felan_chart_project_ajax'));
			add_action('wp_ajax_nopriv_felan_chart_project_ajax', array($felan_ajax, 'felan_chart_project_ajax'));

			//chart employer
			add_action('wp_ajax_felan_chart_employer_ajax', array($felan_ajax, 'felan_chart_employer_ajax'));
			add_action('wp_ajax_nopriv_felan_chart_employer_ajax', array($felan_ajax, 'felan_chart_employer_ajax'));

			//chart freelancer
			add_action('wp_ajax_felan_chart_freelancer_ajax', array($felan_ajax, 'felan_chart_freelancer_ajax'));
			add_action('wp_ajax_nopriv_felan_chart_freelancer_ajax', array($felan_ajax, 'felan_chart_freelancer_ajax'));

			// Add to wishlist
			add_action('wp_ajax_felan_add_to_wishlist', array($felan_ajax, 'felan_add_to_wishlist'));
			add_action('wp_ajax_nopriv_felan_add_to_wishlist', array($felan_ajax, 'felan_add_to_wishlist'));

			// Add to service wishlist
			add_action('wp_ajax_felan_service_wishlist', array($felan_ajax, 'felan_service_wishlist'));
			add_action('wp_ajax_nopriv_felan_service_wishlist', array($felan_ajax, 'felan_service_wishlist'));

			// Add to service addons
			add_action('wp_ajax_felan_service_package', array($felan_ajax, 'felan_service_package'));
			add_action('wp_ajax_nopriv_felan_service_package', array($felan_ajax, 'felan_service_package'));

            // Add to service wallet
            add_action('wp_ajax_felan_service_wallet', array($felan_ajax, 'felan_service_wallet'));
            add_action('wp_ajax_nopriv_felan_service_wallet', array($felan_ajax, 'felan_service_wallet'));

			// Add to follow company
			add_action('wp_ajax_felan_add_to_follow', array($felan_ajax, 'felan_add_to_follow'));
			add_action('wp_ajax_nopriv_felan_add_to_follow', array($felan_ajax, 'felan_add_to_follow'));

			// Add to follow freelancer
			add_action('wp_ajax_felan_add_to_follow_freelancer', array($felan_ajax, 'felan_add_to_follow_freelancer'));
			add_action('wp_ajax_nopriv_felan_add_to_follow_freelancer', array($felan_ajax, 'felan_add_to_follow_freelancer'));

			// Add to download cv freelancer
			add_action('wp_ajax_felan_freelancer_download_cv', array($felan_ajax, 'felan_freelancer_download_cv'));
			add_action('wp_ajax_nopriv_felan_freelancer_download_cv', array($felan_ajax, 'felan_freelancer_download_cv'));


			// Add to apply
			add_action('wp_ajax_jobs_add_to_apply', array($felan_ajax, 'jobs_add_to_apply'));
			add_action('wp_ajax_nopriv_jobs_add_to_apply', array($felan_ajax, 'jobs_add_to_apply'));

			// Add to invite
			add_action('wp_ajax_felan_add_to_invite', array($felan_ajax, 'felan_add_to_invite'));
			add_action('wp_ajax_nopriv_felan_add_to_invite', array($felan_ajax, 'felan_add_to_invite'));

			// Ajax search
			add_action('wp_ajax_felan_search_jobs_ajax', array($felan_ajax, 'felan_search_jobs_ajax'));
			add_action('wp_ajax_nopriv_felan_search_jobs_ajax', array($felan_ajax, 'felan_search_jobs_ajax'));

			// Ajax Search Freelancer
			add_action('wp_ajax_felan_freelancer_archive_ajax', array($felan_ajax, 'felan_freelancer_archive_ajax'));
			add_action('wp_ajax_nopriv_felan_freelancer_archive_ajax', array($felan_ajax, 'felan_freelancer_archive_ajax'));

			// Add to project wishlist
			add_action('wp_ajax_felan_project_wishlist', array($felan_ajax, 'felan_project_wishlist'));
			add_action('wp_ajax_nopriv_felan_project_wishlist', array($felan_ajax, 'felan_project_wishlist'));

			// Ajax Thumbnail
			add_action('wp_ajax_felan_thumbnail_upload_ajax', array($felan_ajax, 'felan_thumbnail_upload_ajax'));
			add_action('wp_ajax_nopriv_felan_thumbnail_upload_ajax', array($felan_ajax, 'felan_thumbnail_upload_ajax'));

			add_action('wp_ajax_felan_thumbnail_remove_ajax', array($felan_ajax, 'felan_thumbnail_remove_ajax'));
			add_action('wp_ajax_nopriv_felan_thumbnail_remove_ajax', array($felan_ajax, 'felan_thumbnail_remove_ajax'));

			// Ajax Avatar
			add_action('wp_ajax_felan_avatar_upload_ajax', array($felan_ajax, 'felan_avatar_upload_ajax'));
			add_action('wp_ajax_nopriv_felan_avatar_upload_ajax', array($felan_ajax, 'felan_avatar_upload_ajax'));

			add_action('wp_ajax_felan_avatar_remove_ajax', array($felan_ajax, 'felan_avatar_remove_ajax'));
			add_action('wp_ajax_nopriv_felan_avatar_remove_ajax', array($felan_ajax, 'felan_avatar_remove_ajax'));

			// Ajax Custom Image
			add_action('wp_ajax_felan_custom_image_upload_ajax', array($felan_ajax, 'felan_custom_image_upload_ajax'));
			add_action('wp_ajax_nopriv_felan_custom_image_upload_ajax', array($felan_ajax, 'felan_custom_image_upload_ajax'));

			add_action('wp_ajax_felan_custom_image_remove_ajax', array($felan_ajax, 'felan_custom_image_remove_ajax'));
			add_action('wp_ajax_nopriv_felan_custom_image_remove_ajax', array($felan_ajax, 'felan_custom_image_remove_ajax'));

            // Ajax Mess Image
            add_action('wp_ajax_felan_mess_image_upload_ajax', array($felan_ajax, 'felan_mess_image_upload_ajax'));
            add_action('wp_ajax_nopriv_felan_mess_image_upload_ajax', array($felan_ajax, 'felan_mess_image_upload_ajax'));

            add_action('wp_ajax_felan_mess_image_remove_ajax', array($felan_ajax, 'felan_mess_image_remove_ajax'));
            add_action('wp_ajax_nopriv_felan_mess_image_remove_ajax', array($felan_ajax, 'felan_mess_image_remove_ajax'));

			// Ajax Gallery
			add_action('wp_ajax_felan_gallery_upload_ajax', array($felan_ajax, 'felan_gallery_upload_ajax'));
			add_action('wp_ajax_nopriv_felan_gallery_upload_ajax', array($felan_ajax, 'felan_gallery_upload_ajax'));

			add_action('wp_ajax_felan_gallery_remove_ajax', array($felan_ajax, 'felan_gallery_remove_ajax'));
			add_action('wp_ajax_nopriv_felan_gallery_remove_ajax', array($felan_ajax, 'felan_agallery_remove_ajax'));

			// Ajax Elementor
			add_action('wp_ajax_felan_el_jobs_pagination_ajax', array($felan_ajax, 'felan_el_jobs_pagination_ajax'));
			add_action('wp_ajax_nopriv_felan_el_jobs_pagination_ajax', array($felan_ajax, 'felan_el_jobs_pagination_ajax'));

			// Service
			add_action('wp_ajax_felan_filter_my_service', array($felan_ajax, 'felan_filter_my_service'));
			add_action('wp_ajax_nopriv_felan_filter_my_service', array($felan_ajax, 'felan_filter_my_service'));

			add_action('wp_ajax_felan_employer_order_service', array($felan_ajax, 'felan_employer_order_service'));
			add_action('wp_ajax_nopriv_felan_employer_order_service', array($felan_ajax, 'felan_employer_order_service'));

            add_action('wp_ajax_felan_employer_disputes', array($felan_ajax, 'felan_employer_disputes'));
            add_action('wp_ajax_nopriv_felan_employer_disputes', array($felan_ajax, 'felan_employer_disputes'));

            add_action('wp_ajax_felan_freelancer_disputes', array($felan_ajax, 'felan_freelancer_disputes'));
            add_action('wp_ajax_nopriv_felan_freelancer_disputes', array($felan_ajax, 'felan_freelancer_disputes'));

            add_action('wp_ajax_felan_employer_service_detail', array($felan_ajax, 'felan_employer_service_detail'));
            add_action('wp_ajax_nopriv_felan_employer_service_detail', array($felan_ajax, 'felan_employer_service_detail'));

            add_action('wp_ajax_felan_employer_disputes_detail', array($felan_ajax, 'felan_employer_disputes_detail'));
            add_action('wp_ajax_nopriv_felan_employer_disputes_detail', array($felan_ajax, 'felan_employer_disputes_detail'));

			add_action('wp_ajax_felan_freelancer_order_service', array($felan_ajax, 'felan_freelancer_order_service'));
			add_action('wp_ajax_nopriv_felan_freelancer_order_service', array($felan_ajax, 'felan_freelancer_order_service'));

			add_action('wp_ajax_felan_freelancer_wallet_service', array($felan_ajax, 'felan_freelancer_wallet_service'));
			add_action('wp_ajax_nopriv_felan_freelancer_wallet_service', array($felan_ajax, 'felan_freelancer_wallet_service'));

            add_action('wp_ajax_felan_employer_wallet_dashboard', array($felan_ajax, 'felan_employer_wallet_dashboard'));
            add_action('wp_ajax_nopriv_felan_employer_wallet_dashboard', array($felan_ajax, 'felan_employer_wallet_dashboard'));

			add_action('wp_ajax_felan_submit_withdraw', array($felan_ajax, 'felan_submit_withdraw'));
			add_action('wp_ajax_nopriv_felan_submit_withdraw', array($felan_ajax, 'felan_submit_withdraw'));

            add_action('wp_ajax_felan_employer_submit_withdraw', array($felan_ajax, 'felan_employer_submit_withdraw'));
            add_action('wp_ajax_nopriv_felan_employer_submit_withdraw', array($felan_ajax, 'felan_employer_submit_withdraw'));

            add_action('wp_ajax_felan_my_wallet_topup', array($felan_ajax, 'felan_my_wallet_topup'));
            add_action('wp_ajax_nopriv_felan_my_wallet_topup', array($felan_ajax, 'felan_my_wallet_topup'));

            //Project
            add_action('wp_ajax_felan_employer_project_disputes', array($felan_ajax, 'felan_employer_project_disputes'));
            add_action('wp_ajax_nopriv_felan_employer_project_disputes', array($felan_ajax, 'felan_employer_project_disputes'));

            add_action('wp_ajax_felan_project_disputes_detail', array($felan_ajax, 'felan_project_disputes_detail'));
            add_action('wp_ajax_nopriv_felan_project_disputes_detail', array($felan_ajax, 'felan_project_disputes_detail'));

            add_action('wp_ajax_felan_freelancer_project_disputes', array($felan_ajax, 'felan_freelancer_project_disputes'));
            add_action('wp_ajax_nopriv_felan_freelancer_project_disputes', array($felan_ajax, 'felan_freelancer_project_disputes'));

            add_action('wp_ajax_felan_project_disputes_message', array($felan_ajax, 'felan_project_disputes_message'));
            add_action('wp_ajax_nopriv_felan_project_disputes_message', array($felan_ajax, 'felan_project_disputes_message'));

            add_action('wp_ajax_felan_employer_project_detail', array($felan_ajax, 'felan_employer_project_detail'));
            add_action('wp_ajax_nopriv_felan_employer_project_detail', array($felan_ajax, 'felan_employer_project_detail'));

            add_action('wp_ajax_felan_freelancer_edit_proposals', array($felan_ajax, 'felan_freelancer_edit_proposals'));
            add_action('wp_ajax_nopriv_felan_freelancer_edit_proposals', array($felan_ajax, 'felan_freelancer_edit_proposals'));

            // Locations
			add_action('wp_ajax_felan_select_country', array($felan_ajax, 'felan_select_country'));
			add_action('wp_ajax_nopriv_felan_select_country', array($felan_ajax, 'felan_select_country'));

			add_action('wp_ajax_felan_select_state', array($felan_ajax, 'felan_select_state'));
			add_action('wp_ajax_nopriv_felan_select_state', array($felan_ajax, 'felan_select_state'));

			//Single Popup
			add_action('wp_ajax_felan_ajax_single_popup', array($felan_ajax, 'felan_ajax_single_popup'));
			add_action('wp_ajax_nopriv_felan_ajax_single_popup', array($felan_ajax, 'felan_ajax_single_popup'));

			// Write a review
			add_action('wp_ajax_felan_service_write_a_review', array($felan_ajax, 'felan_service_write_a_review'));
			add_action('wp_ajax_nopriv_felan_service_write_a_review', array($felan_ajax, 'felan_service_write_a_review'));

            add_action('wp_ajax_felan_service_view_review', array($felan_ajax, 'felan_service_view_review'));
            add_action('wp_ajax_nopriv_felan_service_view_review', array($felan_ajax, 'felan_service_view_review'));

			add_action('wp_ajax_felan_company_write_a_review', array($felan_ajax, 'felan_company_write_a_review'));
			add_action('wp_ajax_nopriv_felan_company_write_a_review', array($felan_ajax, 'felan_company_write_a_review'));

            add_action('wp_ajax_felan_freelancer_view_review', array($felan_ajax, 'felan_freelancer_view_review'));
            add_action('wp_ajax_nopriv_felan_freelancer_view_review', array($felan_ajax, 'felan_freelancer_view_review'));

			add_action('wp_ajax_felan_freelancer_write_a_review', array($felan_ajax, 'felan_freelancer_write_a_review'));
			add_action('wp_ajax_nopriv_felan_freelancer_write_a_review', array($felan_ajax, 'felan_freelancer_write_a_review'));
			/**
			 * Hook Felan_Jobs
			 */
			$felan_jobs = new Felan_Jobs();
			add_filter('felan_single_jobs_before', array($felan_jobs, 'felan_set_jobs_view_date'));
			add_filter('felan_single_jobs_before', array($felan_jobs, 'felan_jobs_breadcrumb'));

			add_action('wp_ajax_jobs_submit_ajax', array($felan_jobs, 'jobs_submit_ajax'));
			add_action('wp_ajax_nopriv_jobs_submit_ajax', array($felan_jobs, 'jobs_submit_ajax'));

			/**
			 * Hook Felan_company
			 */
			$felan_company = new Felan_Company();
			add_action('felan_single_company_before', array($felan_company, 'felan_company_breadcrumb'), 5);

			add_action('wp_ajax_felan_company_submit_review_ajax', array($felan_company, 'submit_review_ajax'));
			add_action('wp_ajax_nopriv_felan_company_submit_review_ajax', array($felan_company, 'submit_review_ajax'));

			add_filter('felan_company_rating_meta', array($felan_company, 'rating_meta_filter'), 4, 9);

			add_action('wp_ajax_felan_company_submit_reply_ajax', array($felan_company, 'submit_reply_ajax'));
			add_action('wp_ajax_nopriv_company_submit_reply_ajax', array($felan_company, 'submit_reply_ajax'));

			add_action('wp_ajax_company_submit_ajax', array($felan_company, 'company_submit_ajax'));
			add_action('wp_ajax_nopriv_company_submit_ajax', array($felan_company, 'company_submit_ajax'));

			/**
			 * Hook Felan_Payment
			 */
			$felan_payment = new Felan_Payment();
			add_action('wp_ajax_felan_paypal_payment_per_package_ajax', array($felan_payment, 'paypal_payment_per_package_ajax'));
			add_action('wp_ajax_nopriv_felan_paypal_payment_per_package_ajax', array($felan_payment, 'paypal_payment_per_package_ajax'));

			add_action('wp_ajax_felan_wire_transfer_per_package_ajax', array($felan_payment, 'wire_transfer_per_package_ajax'));
			add_action('wp_ajax_nopriv_felan_wire_transfer_per_package_ajax', array($felan_payment, 'wire_transfer_per_package_ajax'));

			add_action('wp_ajax_felan_free_package_ajax', array($felan_payment, 'free_package_ajax'));
			add_action('wp_ajax_nopriv_felan_free_package_ajax', array($felan_payment, 'free_package_ajax'));

			add_action('wp_ajax_felan_woocommerce_payment_per_package_ajax', array($felan_payment, 'woocommerce_payment_per_package_ajax'));
			add_action('wp_ajax_nopriv_felan_woocommerce_payment_per_package_ajax', array($felan_payment, 'woocommerce_payment_per_package_ajax'));

			/**
			 * Hook Felan_Freelancer
			 */
			$felan_freelancer = new Felan_Freelancer();
			add_action('felan_single_freelancer_before', array($felan_freelancer, 'felan_freelancer_breadcrumb'), 5);
			add_filter('felan_single_freelancer_before', array($felan_freelancer, 'felan_set_freelancer_view_date'));
			add_filter('felan_freelancer_rating_meta', array($felan_freelancer, 'rating_meta_filter'), 4, 9);
			add_filter('update_felan_freelancer_meta_rating', array($felan_freelancer, 'update_rating_meta'), 4, 9);

			add_action('wp_ajax_felan_freelancer_submit_review_ajax', array($felan_freelancer, 'submit_review_ajax'));
			add_action('wp_ajax_nopriv_felan_freelancer_submit_review_ajax', array($felan_freelancer, 'submit_review_ajax'));

			add_action('wp_ajax_felan_freelancer_submit_reply_ajax', array($felan_company, 'submit_reply_ajax'));
			add_action('wp_ajax_nopriv_freelancer_submit_reply_ajax', array($felan_company, 'submit_reply_ajax'));

			add_action('wp_ajax_upload_freelancer_attachment_ajax', array($felan_freelancer, 'upload_freelancer_attachment_ajax'));
			add_action('wp_ajax_nopriv_upload_freelancer_attachment_ajax', array($felan_freelancer, 'upload_freelancer_attachment_ajax'));

			add_action('wp_ajax_remove_freelancer_attachment_ajax', array($felan_freelancer, 'remove_freelancer_attachment_ajax'));
			add_action('wp_ajax_nopriv_remove_freelancer_attachment_ajax', array($felan_freelancer, 'remove_freelancer_attachment_ajax'));

			add_action('wp_ajax_freelancer_submit_ajax', array($felan_freelancer, 'freelancer_submit_ajax'));
			add_action('wp_ajax_nopriv_freelancer_submit_ajax', array($felan_freelancer, 'freelancer_submit_ajax'));

			add_action('wp_ajax_user_verify_ajax', array($felan_freelancer, 'user_verify_ajax'));
			add_action('wp_ajax_nopriv_user_verify_ajax', array($felan_freelancer, 'user_verify_ajax'));

			add_action('wp_ajax_felan_freelancer_print_ajax', array($felan_ajax, 'felan_freelancer_print_ajax'));
			add_action('wp_ajax_nopriv_felan_freelancer_print_ajax', array($felan_ajax, 'felan_freelancer_print_ajax'));
			/**
			 * Hook Felan_Service
			 */
			$felan_service = new Felan_Service();
			add_filter('felan_single_service_before', array($felan_service, 'felan_set_service_view_date'));

			add_action('wp_ajax_felan_service_submit_review_ajax', array($felan_service, 'submit_review_ajax'));
			add_action('wp_ajax_nopriv_felan_service_submit_review_ajax', array($felan_service, 'submit_review_ajax'));

			add_filter('felan_service_rating_meta', array($felan_service, 'rating_meta_filter'), 4, 9);

			add_action('wp_ajax_felan_service_submit_reply_ajax', array($felan_service, 'submit_reply_ajax'));
			add_action('wp_ajax_nopriv_service_submit_reply_ajax', array($felan_service, 'submit_reply_ajax'));

			add_action('wp_ajax_service_submit_ajax', array($felan_service, 'service_submit_ajax'));
			add_action('wp_ajax_nopriv_service_submit_ajax', array($felan_service, 'service_submit_ajax'));

			add_action('wp_ajax_felan_filter_project_my_wishlist', array($felan_ajax, 'felan_filter_project_my_wishlist'));
			add_action('wp_ajax_nopriv_felan_filter_project_my_wishlist', array($felan_ajax, 'felan_filter_project_my_wishlist'));

			/**
			 * Hook Felan_Meetings
			 */
			$felan_meetings = new Felan_Meetings();
			add_action('wp_ajax_felan_meetings_settings', array($felan_meetings, 'felan_meetings_settings'));
			add_action('wp_ajax_nopriv_felan_meetings_settings', array($felan_meetings, 'felan_meetings_settings'));

			add_action('wp_ajax_felan_meetings_reschedule_ajax', array($felan_meetings, 'felan_meetings_reschedule_ajax'));
			add_action('wp_ajax_nopriv_felan_meetings_reschedule_ajax', array($felan_meetings, 'felan_meetings_reschedule_ajax'));

			add_action('wp_ajax_felan_meetings_upcoming_dashboard', array($felan_meetings, 'felan_meetings_upcoming_dashboard'));
			add_action('wp_ajax_nopriv_felan_meetings_upcoming_dashboard', array($felan_meetings, 'felan_meetings_upcoming_dashboard'));

			add_action('wp_ajax_felan_meetings_completed_dashboard', array($felan_meetings, 'felan_meetings_completed_dashboard'));
			add_action('wp_ajax_nopriv_felan_meetings_completed_dashboard', array($felan_meetings, 'felan_meetings_completed_dashboard'));

			add_action('wp_ajax_felan_meetings_freelancer_dashboard', array($felan_meetings, 'felan_meetings_freelancer_dashboard'));
			add_action('wp_ajax_nopriv_felan_meetings_freelancer_dashboard', array($felan_meetings, 'felan_meetings_freelancer_dashboard'));

			/**
			 * Hook Felan_Messages
			 */
			$felan_messages = new Felan_Messages();
			add_action('wp_ajax_felan_send_messages', array($felan_messages, 'felan_send_messages'));
			add_action('wp_ajax_nopriv_felan_send_messages', array($felan_messages, 'felan_send_messages'));

			add_action('wp_ajax_felan_write_messages', array($felan_messages, 'felan_write_messages'));
			add_action('wp_ajax_nopriv_felan_write_messages', array($felan_messages, 'felan_write_messages'));

			add_action('wp_ajax_felan_messages_list_user', array($felan_messages, 'felan_messages_list_user'));
			add_action('wp_ajax_nopriv_felan_messages_list_user', array($felan_messages, 'felan_messages_list_user'));

			add_action('wp_ajax_felan_refresh_messages', array($felan_messages, 'felan_refresh_messages'));
			add_action('wp_ajax_nopriv_felan_refresh_messages', array($felan_messages, 'felan_refresh_messages'));

			/**
			 * Hook Felan_Notification
			 */
			$felan_notification = new Felan_Notification();
			add_action('wp_ajax_felan_refresh_notification', array($felan_notification, 'felan_refresh_notification'));
			add_action('wp_ajax_nopriv_felan_refresh_notification', array($felan_notification, 'felan_refresh_notification'));

			/**
			 * Hook Felan Freelancer Payment
			 */
			$felan_freelancer_payment = new Felan_Freelancer_Payment();
			add_action('wp_ajax_felan_freelancer_paypal_payment_per_package_ajax', array($felan_freelancer_payment, 'freelancer_paypal_payment_per_package_ajax'));
			add_action('wp_ajax_nopriv_felan_freelancer_paypal_payment_per_package_ajax', array($felan_freelancer_payment, 'freelancer_paypal_payment_per_package_ajax'));

			add_action('wp_ajax_felan_freelancer_wire_transfer_per_package_ajax', array($felan_freelancer_payment, 'freelancer_wire_transfer_per_package_ajax'));
			add_action('wp_ajax_nopriv_felan_freelancer_wire_transfer_per_package_ajax', array($felan_freelancer_payment, 'freelancer_wire_transfer_per_package_ajax'));

			add_action('wp_ajax_felan_freelancer_free_package_ajax', array($felan_freelancer_payment, 'freelancer_free_package_ajax'));
			add_action('wp_ajax_nopriv_felan_freelancer_free_package_ajax', array($felan_freelancer_payment, 'freelancer_free_package_ajax'));

			add_action('wp_ajax_felan_freelancer_woocommerce_payment_per_package_ajax', array($felan_freelancer_payment, 'freelancer_woocommerce_payment_per_package_ajax'));
			add_action('wp_ajax_nopriv_felan_freelancer_woocommerce_payment_per_package_ajax', array($felan_freelancer_payment, 'freelancer_woocommerce_payment_per_package_ajax'));

            /**
             * Hook felan wallet order
             */
            $felan_wallet_payment = new Felan_Wallet_Payment();
            add_action('wp_ajax_felan_stripe_payment_wallet_addons', array($felan_wallet_payment, 'felan_stripe_payment_wallet_addons'));
            add_action('wp_ajax_nopriv_felan_stripe_payment_wallet_addons', array($felan_wallet_payment, 'felan_stripe_payment_wallet_addons'));

            add_action('wp_ajax_felan_paypal_payment_wallet_addons', array($felan_wallet_payment, 'felan_paypal_payment_wallet_addons'));
            add_action('wp_ajax_nopriv_felan_paypal_payment_wallet_addons', array($felan_wallet_payment, 'felan_paypal_payment_wallet_addons'));

            add_action('wp_ajax_felan_wire_transfer_wallet_addons', array($felan_wallet_payment, 'felan_wire_transfer_wallet_addons'));
            add_action('wp_ajax_nopriv_felan_wire_transfer_wallet_addons', array($felan_wallet_payment, 'felan_wire_transfer_wallet_addons'));

            add_action('wp_ajax_felan_woocommerce_payment_wallet_addons', array($felan_wallet_payment, 'felan_woocommerce_payment_wallet_addons'));
            add_action('wp_ajax_nopriv_felan_woocommerce_payment_wallet_addons', array($felan_wallet_payment, 'felan_woocommerce_payment_wallet_addons'));

			/**
			 * Hook Felan Service Payment
			 */
			$felan_service_payment = new Felan_Service_Payment();
			add_action('wp_ajax_felan_paypal_payment_service_addons', array($felan_service_payment, 'felan_paypal_payment_service_addons'));
			add_action('wp_ajax_nopriv_felan_paypal_payment_service_addons', array($felan_service_payment, 'felan_paypal_payment_service_addons'));

			add_action('wp_ajax_felan_wire_transfer_service_addons', array($felan_service_payment, 'felan_wire_transfer_service_addons'));
			add_action('wp_ajax_nopriv_felan_wire_transfer_service_addons', array($felan_service_payment, 'felan_wire_transfer_service_addons'));

			add_action('wp_ajax_felan_woocommerce_payment_service_addons', array($felan_service_payment, 'felan_woocommerce_payment_service_addons'));
			add_action('wp_ajax_nopriv_felan_woocommerce_payment_service_addons', array($felan_service_payment, 'felan_woocommerce_payment_service_addons'));

			/**
			 * Hook Felan Project Payment
			 */
			$felan_project_payment = new Felan_Project_Payment();
			add_action('wp_ajax_felan_paypal_payment_project_addons', array($felan_project_payment, 'felan_paypal_payment_project_addons'));
			add_action('wp_ajax_nopriv_felan_paypal_payment_project_addons', array($felan_project_payment, 'felan_paypal_payment_project_addons'));

			add_action('wp_ajax_felan_wire_transfer_project_addons', array($felan_project_payment, 'felan_wire_transfer_project_addons'));
			add_action('wp_ajax_nopriv_felan_wire_transfer_project_addons', array($felan_project_payment, 'felan_wire_transfer_project_addons'));

			add_action('wp_ajax_felan_woocommerce_payment_project_addons', array($felan_project_payment, 'felan_woocommerce_payment_project_addons'));
			add_action('wp_ajax_nopriv_felan_woocommerce_payment_project_addons', array($felan_project_payment, 'felan_woocommerce_payment_project_addons'));

			/**
			 * Hook Felan Project
			 */
			$felan_project = new Felan_Project();
			add_filter('felan_single_project_before', array($felan_project, 'felan_set_project_view_date'));

			add_action('wp_ajax_felan_send_proposal_project', array($felan_project, 'felan_send_proposal_project'));
			add_action('wp_ajax_nopriv_felan_send_proposal_project', array($felan_project, 'felan_send_proposal_project'));

			add_action('wp_ajax_felan_project_submit_review_ajax', array($felan_project, 'submit_review_ajax'));
			add_action('wp_ajax_nopriv_felan_project_submit_review_ajax', array($felan_project, 'submit_review_ajax'));

			add_action('wp_ajax_felan_filter_project_applicants', array($felan_project, 'felan_filter_project_applicants'));
			add_action('wp_ajax_nopriv_felan_filter_project_applicants', array($felan_project, 'felan_filter_project_applicants'));

			add_action('wp_ajax_felan_project_package', array($felan_project, 'felan_project_package'));
			add_action('wp_ajax_nopriv_felan_project_package', array($felan_project, 'felan_project_package'));

			add_action('wp_ajax_felan_project_submit_withdraw', array($felan_project, 'felan_project_submit_withdraw'));
			add_action('wp_ajax_nopriv_felan_project_submit_withdraw', array($felan_project, 'felan_project_submit_withdraw'));

			add_action('wp_ajax_felan_freelancer_proposal_project', array($felan_project, 'felan_freelancer_proposal_project'));
			add_action('wp_ajax_nopriv_felan_freelancer_proposal_project', array($felan_project, 'felan_freelancer_proposal_project'));

			add_action('wp_ajax_felan_filter_my_project', array($felan_project, 'felan_filter_my_project'));
			add_action('wp_ajax_nopriv_felan_filter_my_project', array($felan_project, 'felan_filter_my_project'));

			add_action('wp_ajax_felan_filter_employer_proposal', array($felan_project, 'felan_filter_employer_proposal'));
			add_action('wp_ajax_nopriv_felan_filter_employer_proposal', array($felan_project, 'felan_filter_employer_proposal'));

			add_filter('felan_project_rating_meta', array($felan_project, 'rating_meta_filter'), 4, 9);

			add_action('wp_ajax_felan_project_submit_reply_ajax', array($felan_project, 'submit_reply_ajax'));
			add_action('wp_ajax_nopriv_project_submit_reply_ajax', array($felan_project, 'submit_reply_ajax'));

			add_action('wp_ajax_project_submit_ajax', array($felan_project, 'project_submit_ajax'));
			add_action('wp_ajax_nopriv_project_submit_ajax', array($felan_project, 'project_submit_ajax'));
		}

		/**
		 * Get template path
		 */
		public function template_path()
		{
			return apply_filters('felan_template_path', 'felan-framework/');
		}
	}
}

if (!function_exists('FELAN')) {
	function FELAN()
	{
		return Felan_Core::instance();
	}
}
// Global for backwards compatibility.
$GLOBALS['Felan_Core'] = FELAN();
