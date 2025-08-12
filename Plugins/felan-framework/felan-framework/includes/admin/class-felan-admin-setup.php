<?php
if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Felan_Admin_Setup')) {
	/**
	 * Class Felan_Admin_Setup
	 */
	class Felan_Admin_Setup
	{
		/**
		 * admin_menu
		 */
		public function admin_menu()
		{
			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			$enable_wallet_mode  = felan_get_option('enable_wallet_mode', '0');

			add_menu_page(
				esc_html__('Felan', 'felan-framework'),
				esc_html__('Felan', 'felan-framework'),
				'manage_options',
				'felan_welcome',
				array($this, 'menu_welcome_page_callback'),
				FELAN_PLUGIN_URL . 'assets/images/icon.png',
				2
			);
			add_submenu_page(
				'felan_welcome',
				esc_html__('Welcome', 'felan-framework'),
				esc_html__('Welcome', 'felan-framework'),
				'manage_options',
				'felan_welcome',
				array($this, 'menu_welcome_page_callback')
			);
			add_submenu_page(
				'felan_welcome',
				esc_html__('System', 'felan-framework'),
				esc_html__('System', 'felan-framework'),
				'manage_options',
				'felan_system',
				array($this, 'system_page_callback')
			);
			add_submenu_page(
				'felan_welcome',
				esc_html__('Import', 'felan-framework'),
				esc_html__('Import', 'felan-framework'),
				'manage_options',
				'felan_import',
				array($this, 'import_page_callback')
			);

			if (defined('WP_DEBUG') && true === WP_DEBUG) {
				add_submenu_page(
					'felan_welcome',
					esc_html__('Export', 'felan-framework'),
					esc_html__('Export', 'felan-framework'),
					'manage_options',
					'felan_export',
					array($this, 'export_page_callback')
				);
			};

			add_submenu_page(
				'felan_welcome',
				esc_html__('Theme Options', 'felan-framework'),
				esc_html__('Theme Options', 'felan-framework'),
				'manage_options',
				'admin.php?page=felan-framework'
			);

			add_submenu_page(
				'felan_welcome',
				esc_html__('Setup Page', 'felan-framework'),
				esc_html__('Setup Page', 'felan-framework'),
				'manage_options',
				'felan_setup',
				array($this, 'setup_page')
			);

			add_menu_page(
				esc_html__('Felan Employer', 'felan-framework'),
				esc_html__('Felan Employer', 'felan-framework'),
				'manage_options',
				'felan_employer',
				'',
				FELAN_PLUGIN_URL . 'assets/images/icon2.png',
				7
			);

			add_submenu_page(
				'felan_employer',
				esc_html__('Companies', 'felan-framework'),
				esc_html__('Companies', 'felan-framework'),
				'manage_options',
				'edit.php?post_type=company'
			);

			if ($enable_wallet_mode == '0') {
				add_submenu_page(
					'felan_employer',
					esc_html__('Package', 'felan-framework'),
					esc_html__('Package', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=package'
				);

				add_submenu_page(
					'felan_employer',
					esc_html__('User Package', 'felan-framework'),
					esc_html__('User Package', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=user_package'
				);

				add_submenu_page(
					'felan_employer',
					esc_html__('Invoice', 'felan-framework'),
					esc_html__('Invoice', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=invoice'
				);
			}

			if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') {
				add_menu_page(
					esc_html__('Felan Candidate', 'felan-framework'),
					esc_html__('Felan Candidate', 'felan-framework'),
					'manage_options',
					'felan_freelancer',
					'',
					FELAN_PLUGIN_URL . 'assets/images/icon3.png',
					12
				);

				add_submenu_page(
					'felan_freelancer',
					esc_html__('Candidate', 'felan-framework'),
					esc_html__('Candidate', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=freelancer'
				);
			} else {
				add_menu_page(
					esc_html__('Felan Freelancers', 'felan-framework'),
					esc_html__('Felan Freelancers', 'felan-framework'),
					'manage_options',
					'felan_freelancer',
					'',
					FELAN_PLUGIN_URL . 'assets/images/icon3.png',
					12
				);

				add_submenu_page(
					'felan_freelancer',
					esc_html__('Freelancers', 'felan-framework'),
					esc_html__('Freelancers', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=freelancer'
				);
			}

			if ($enable_wallet_mode == '0') {
				add_submenu_page(
					'felan_freelancer',
					esc_html__('Package', 'felan-framework'),
					esc_html__('Package', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=freelancer_package'
				);

				add_submenu_page(
					'felan_freelancer',
					esc_html__('Order', 'felan-framework'),
					esc_html__('Order', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=freelancer_order'
				);
			}

			if ($enable_post_type_service == '1') {
				add_submenu_page(
					'felan_freelancer',
					esc_html__('Withdraw', 'felan-framework'),
					esc_html__('Withdraw', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=freelancer_withdraw'
				);
			}

			add_menu_page(
				esc_html__('Felan Extensions', 'felan-framework'),
				esc_html__('Felan Extensions', 'felan-framework'),
				'manage_options',
				'felan_extensions',
				'',
				FELAN_PLUGIN_URL . 'assets/images/icon4.png',
				18
			);

			add_submenu_page(
				'felan_extensions',
				esc_html__('Messages', 'felan-framework'),
				esc_html__('Messages', 'felan-framework'),
				'manage_options',
				'edit.php?post_type=messages'
			);

			add_submenu_page(
				'felan_extensions',
				esc_html__('Notification', 'felan-framework'),
				esc_html__('Notification', 'felan-framework'),
				'manage_options',
				'edit.php?post_type=notification'
			);

			if ($enable_wallet_mode == '1') {
				add_submenu_page(
					'felan_extensions',
					esc_html__('Wallet Order', 'felan-framework'),
					esc_html__('Wallet Order', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=wallet_order'
				);
			}

			add_menu_page(
				esc_html__('Felan Builder', 'felan-framework'),
				esc_html__('Felan Builder', 'felan-framework'),
				'manage_options',
				'felan_builder',
				'',
				FELAN_PLUGIN_URL . 'assets/images/icon5.png',
				22
			);

			add_submenu_page(
				'felan_builder',
				esc_html__('Header', 'felan-framework'),
				esc_html__('Header', 'felan-framework'),
				'manage_options',
				'edit.php?post_type=felan_header'
			);

			add_submenu_page(
				'felan_builder',
				esc_html__('Footer', 'felan-framework'),
				esc_html__('Footer', 'felan-framework'),
				'manage_options',
				'edit.php?post_type=felan_footer'
			);

			add_submenu_page(
				'felan_builder',
				esc_html__('Mega Menu', 'felan-framework'),
				esc_html__('Mega Menu', 'felan-framework'),
				'manage_options',
				'edit.php?post_type=felan_mega_menu'
			);

			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			if ($enable_post_type_jobs == '1') {
				add_menu_page(
					esc_html__('Felan Jobs', 'felan-framework'),
					esc_html__('Felan Jobs', 'felan-framework'),
					'manage_options',
					'felan_jobs',
					'',
					FELAN_PLUGIN_URL . 'assets/images/icon1.png',
					2,
					5
				);

				add_submenu_page(
					'felan_jobs',
					esc_html__('Jobs', 'felan-framework'),
					esc_html__('Jobs', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=jobs'
				);
				add_submenu_page(
					'felan_jobs',
					esc_html__('Applicants', 'felan-framework'),
					esc_html__('Applicants', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=applicants'
				);

				if (felan_get_option('enable_job_alerts') === '1') {
					add_submenu_page(
						'felan_jobs',
						esc_html__('Job Alerts', 'felan-framework'),
						esc_html__('Job Alerts', 'felan-framework'),
						'manage_options',
						'edit.php?post_type=job_alerts'
					);
				}

				add_submenu_page(
					'felan_extensions',
					esc_html__('Meetings', 'felan-framework'),
					esc_html__('Meetings', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=meetings'
				);
			}

			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			if ($enable_post_type_service == '1') {
				add_submenu_page(
					'felan_employer',
					esc_html__('Service Order', 'felan-framework'),
					esc_html__('Service Order', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=service_order'
				);

				add_submenu_page(
					'felan_freelancer',
					esc_html__('Service', 'felan-framework'),
					esc_html__('Service', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=service'
				);

				add_submenu_page(
					'felan_extensions',
					esc_html__('Service Disputes', 'felan-framework'),
					esc_html__('Service Disputes', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=disputes'
				);
			}

			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			if ($enable_post_type_project == '1') {
				add_submenu_page(
					'felan_employer',
					esc_html__('Projects', 'felan-framework'),
					esc_html__('Projects', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=project'
				);

				add_submenu_page(
					'felan_employer',
					esc_html__('Projects Proposal', 'felan-framework'),
					esc_html__('Projects Proposal', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=project-proposal'
				);

				if ($enable_wallet_mode == '1') {
					add_submenu_page(
						'felan_employer',
						esc_html__('Withdraw', 'felan-framework'),
						esc_html__('Withdraw', 'felan-framework'),
						'manage_options',
						'edit.php?post_type=employer_withdraw'
					);
				}

				if ($enable_wallet_mode == '0') {
					add_submenu_page(
						'felan_employer',
						esc_html__('Projects Order', 'felan-framework'),
						esc_html__('Projects Order', 'felan-framework'),
						'manage_options',
						'edit.php?post_type=project_order'
					);
				}

				add_submenu_page(
					'felan_extensions',
					esc_html__('Project Disputes', 'felan-framework'),
					esc_html__('Project Disputes', 'felan-framework'),
					'manage_options',
					'edit.php?post_type=project_disputes'
				);
			}

			add_submenu_page(
				'felan_extensions',
				esc_html__('Identity Verification', 'felan-framework'),
				esc_html__('Identity Verification', 'felan-framework'),
				'manage_options',
				'users.php',
				// array($this, 'identity_verification')
			);
		}

		/**
		 * Output page setup
		 */
		public function identity_verification() {
			$users = get_users();
			global $wp_roles;
			?>
			<div class="felan-identity-verification-wrap felan-wrap about-wrap felan-setup-wrap">
				<h3><?php esc_html_e('Identity Verification', 'felan-framework'); ?></h3>
				<table class="widefat">
					<thead>
						<tr>
							<th><?php esc_html_e('Username', 'felan-framework'); ?></th>
							<th><?php esc_html_e('Email', 'felan-framework'); ?></th>
							<th><?php esc_html_e('Role', 'felan-framework'); ?></th>
							<th><?php esc_html_e('Verifications', 'felan-framework'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $users as $user ) :
							$role_names = array_map( function($role_key) use ( $wp_roles ) {
								return $wp_roles->roles[$role_key]['name'] ?? $role_key;
							}, $user->roles );
						?>
						<tr>
							<td><?php echo esc_html( $user->user_login ); ?></td>
							<td><?php echo esc_html( $user->user_email ); ?></td>
							<td><?php echo esc_html( implode( ', ', $role_names ) ); ?></td>
							<td>
								<a data-user-id="<?= esc_attr( $user->ID ) ?>" href="#" class="button">
									<?php esc_html_e('View detail', 'felan-framework'); ?>
								</a>
								<div class="white-popup-block mfp-hide">

								</div>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php
		}

		public function reorder_admin_menu()
		{
			// Remove default menu items
			//remove_menu_page( 'edit-comments.php' );
			remove_menu_page('tools.php');
			remove_menu_page('edit.php'); // Remove posts
			remove_menu_page('edit.php?post_type=page'); // Remove pages
			remove_menu_page('upload.php');
			remove_menu_page('themes.php');
			remove_menu_page('plugins.php');
			remove_menu_page('users.php');
			//remove_menu_page( 'options-general.php' );

			// Reorder menu items
			add_menu_page(esc_html__('Posts', 'felan-framework'), esc_html__('Posts', 'felan-framework'), 'edit_posts', 'edit.php', '', 'dashicons-admin-post', 26);
			add_menu_page(esc_html__('Media', 'felan-framework'), esc_html__('Media', 'felan-framework'), 'manage_options', 'upload.php', '', 'dashicons-admin-media', 27);
			add_menu_page(esc_html__('Pages', 'felan-framework'), esc_html__('Pages', 'felan-framework'), 'edit_pages', 'edit.php?post_type=page', '', 'dashicons-admin-page', 28);
			//add_menu_page( esc_html__('Comments', 'felan-framework'), esc_html__('Comments', 'felan-framework'), 'manage_comments', 'edit-comments.php', '', 'dashicons-admin-comments', 29 );
			add_menu_page(esc_html__('Appearance', 'felan-framework'), esc_html__('Appearance', 'felan-framework'), 'edit_theme_options', 'themes.php', '', 'dashicons-admin-appearance', 30);
			add_menu_page(esc_html__('Plugins', 'felan-framework'), esc_html__('Plugins', 'felan-framework'), 'activate_plugins', 'plugins.php', '', 'dashicons-admin-plugins', 31);
			add_menu_page(esc_html__('Users', 'felan-framework'), esc_html__('Users', 'felan-framework'), 'promote_users', 'users.php', '', 'dashicons-admin-users', 32);
			add_menu_page(esc_html__('Tools', 'felan-framework'), esc_html__('Tools', 'felan-framework'), 'manage_options', 'tools.php', '', 'dashicons-admin-tools', 33);
			//add_menu_page( esc_html__('Settings', 'felan-framework'), esc_html__('Settings', 'felan-framework'), 'manage_options', 'options-general.php', '', 'dashicons-admin-settings', 34 );
		}

		public function menu_welcome_page_callback()
		{
			if (isset($_POST['purchase_code'])) {
				$purchase_info = Felan_Updater::check_purchase_code(sanitize_key($_POST['purchase_code']));
				update_option('ricetheme_purchase_code', $_POST['purchase_code']);
			}
			$purchase_code = get_option('ricetheme_purchase_code');
			$purchase_class = '';
			$verified = '';
			$check_code = esc_html__('Not verified', 'felan-framework');
			if ($purchase_code) {
				$purchase_code_info = Felan_Updater::check_purchase_code($purchase_code);
				if ($purchase_code_info['status_code'] === 200) {
					$purchase_class = 'verified hidden-code';
					$verified = 'verified';
					$check_code = esc_html__('Verified', 'felan-framework');
				}
			}
			?>

			<?php
			$update = Felan_Updater::check_theme_update();
			$new_version = isset($update['new_version']) ? $update['new_version'] : FELAN_THEME_VERSION;
			$get_info = Felan_Updater::get_info();
			if ($update) {
			?>
				<div class="alert-wrap alert-success about-wrap">
					<div class="msg-update">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
							<rect x="0" fill="none" width="24" height="24"></rect>
							<g>
								<path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-2h2v2zm0-4h-2l-.5-6h3l-.5 6z"></path>
							</g>
						</svg>

						<div class="inner-msg">
							<?php
							if (Felan_Updater::check_valid_update()) {

								printf(
									__(
										'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s" %6$s>update now</a>.',
										'felan-framework'
									),
									FELAN_THEME_NAME,
									esc_url(add_query_arg(
										'action',
										'ricetheme_get_changelogs',
										admin_url('admin-ajax.php')
									)),
									sprintf(
										'class="thickbox" name="Changelogs" aria-label="%s"',
										esc_attr(sprintf(
											__('View %1$s version %2$s details'),
											FELAN_THEME_NAME,
											FELAN_THEME_VERSION
										))
									),
									$new_version,
									wp_nonce_url(
										self_admin_url('update.php?action=upgrade-theme&theme=') . FELAN_THEME_SLUG,
										'upgrade-theme_' . FELAN_THEME_SLUG
									),
									sprintf(
										'id="update-theme" aria-label="%s"',
										esc_attr(sprintf(__('Update %s now'), FELAN_THEME_NAME))
									)
								);
							} else {

								printf(
									__(
										'There is a new version of %1$s available. <strong>Please enter your purchase code to update the theme.</strong>',
										'felan-framework'
									),
									FELAN_THEME_NAME
								);
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}
			?>

			<div class="felan-wrap wrap about-wrap purchase-wrap">
				<div class="entry-heading">
					<h4><?php esc_html_e('Purchase code', 'felan-framework'); ?><span class="check-code <?php esc_html_e($verified); ?>"><?php esc_html_e($check_code); ?></span>
					</h4>
				</div>

				<form action="" class="purchase-form <?php echo esc_attr($purchase_class); ?>" method="post">
					<span class="purchase-icon">
						<svg class="valid" fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="20px" height="20px">
							<path d="M 22.78125 0 C 21.605469 -0.00390625 20.40625 0.164063 19.21875 0.53125 C 12.902344 2.492188 9.289063 9.269531 11.25 15.59375 L 11.25 15.65625 C 11.507813 16.367188 12.199219 18.617188 12.625 20 L 9 20 C 7.355469 20 6 21.355469 6 23 L 6 47 C 6 48.644531 7.355469 50 9 50 L 41 50 C 42.644531 50 44 48.644531 44 47 L 44 23 C 44 21.355469 42.644531 20 41 20 L 14.75 20 C 14.441406 19.007813 13.511719 16.074219 13.125 15 L 13.15625 15 C 11.519531 9.722656 14.5 4.109375 19.78125 2.46875 C 25.050781 0.832031 30.695313 3.796875 32.34375 9.0625 C 32.34375 9.066406 32.34375 9.089844 32.34375 9.09375 C 32.570313 9.886719 33.65625 13.40625 33.65625 13.40625 C 33.746094 13.765625 34.027344 14.050781 34.386719 14.136719 C 34.75 14.226563 35.128906 14.109375 35.375 13.832031 C 35.621094 13.550781 35.695313 13.160156 35.5625 12.8125 C 35.5625 12.8125 34.433594 9.171875 34.25 8.53125 L 34.25 8.5 C 32.78125 3.761719 28.601563 0.542969 23.9375 0.0625 C 23.550781 0.0234375 23.171875 0 22.78125 0 Z M 9 22 L 41 22 C 41.554688 22 42 22.445313 42 23 L 42 47 C 42 47.554688 41.554688 48 41 48 L 9 48 C 8.445313 48 8 47.554688 8 47 L 8 23 C 8 22.445313 8.445313 22 9 22 Z M 25 30 C 23.300781 30 22 31.300781 22 33 C 22 33.898438 22.398438 34.6875 23 35.1875 L 23 38 C 23 39.101563 23.898438 40 25 40 C 26.101563 40 27 39.101563 27 38 L 27 35.1875 C 27.601563 34.6875 28 33.898438 28 33 C 28 31.300781 26.699219 30 25 30 Z" />
						</svg>

						<svg class="invalid" fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="20px" height="20px">
							<path d="M 25 3 C 18.363281 3 13 8.363281 13 15 L 13 20 L 9 20 C 7.355469 20 6 21.355469 6 23 L 6 47 C 6 48.644531 7.355469 50 9 50 L 41 50 C 42.644531 50 44 48.644531 44 47 L 44 23 C 44 21.355469 42.644531 20 41 20 L 37 20 L 37 15 C 37 8.363281 31.636719 3 25 3 Z M 25 5 C 30.566406 5 35 9.433594 35 15 L 35 20 L 15 20 L 15 15 C 15 9.433594 19.433594 5 25 5 Z M 9 22 L 41 22 C 41.554688 22 42 22.445313 42 23 L 42 47 C 42 47.554688 41.554688 48 41 48 L 9 48 C 8.445313 48 8 47.554688 8 47 L 8 23 C 8 22.445313 8.445313 22 9 22 Z M 25 30 C 23.300781 30 22 31.300781 22 33 C 22 33.898438 22.398438 34.6875 23 35.1875 L 23 38 C 23 39.101563 23.898438 40 25 40 C 26.101563 40 27 39.101563 27 38 L 27 35.1875 C 27.601563 34.6875 28 33.898438 28 33 C 28 31.300781 26.699219 30 25 30 Z" />
						</svg>
					</span>
					<input class="purchase-code" name="purchase_code" type="text" value="<?php echo esc_attr($purchase_code); ?>" placeholder="<?php esc_attr_e('Purchase code', 'felan-framework'); ?>" autocomplete="off" />
					<input type="submit" class="button action" value="Submit" />
				</form>
				<div class="purchase-desc">
					<?php
					if (isset($_POST['purchase_code'])) {
						$purchase_info = Felan_Updater::check_purchase_code(sanitize_key($_POST['purchase_code']));
						if ($purchase_info['status_code'] !== 200) {
							esc_html_e('The purchase code was invalid.', 'felan-framework');
						} else {
							esc_html_e('Success! The purchase code was valid.', 'felan-framework');
						}
					} else {
						if ($purchase_code) {
							$purchase_info = Felan_Updater::check_purchase_code($purchase_code);
							if ($purchase_info['status_code'] === 200) {
								esc_html_e('Please do not provide purchase code to anyone.', 'felan-framework');
							} else {
								esc_html_e('The purchase code was invalid. Please try again.', 'felan-framework');
							}
						} else {
							esc_html_e('Show us your ThemeForest purchase code to get the automatic update.', 'felan-framework');
						}
					}
					?>
				</div>
			</div>

			<div class="felan-wrap wrap about-wrap welcome-wrap">
				<div class="wrap-column wrap-column-2 col-started">
					<div class="panel-column column-content">
						<h3><?php esc_html_e('Welcome to Felan Theme', 'felan-framework'); ?></h3>
						<p><?php esc_html_e("We've assembled some links to get you started", 'felan-framework'); ?></p>
						<div class="entry-heading started">
							<h4><?php esc_html_e('Get Started', 'felan-framework'); ?></h4>
						</div>
						<div class="entry-detail">

							<a href="<?php echo esc_url(admin_url('admin.php?page=felan_import')); ?>" class="button button-primary"><?php esc_html_e('Install Sample Data', 'felan-framework'); ?></a>

							<p>
								<span><?php esc_html_e('or,', 'felan-framework') ?></span>
								<a href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php esc_html_e('Customize your site', 'felan-framework'); ?></a>
							</p>
						</div>
						<div class="box-wrap">
							<div class="box-detail">
								<span class="entry-title"><?php esc_html_e('Current Version: ', 'felan-framework'); ?></span>
								<p><?php esc_html_e(FELAN_THEME_VERSION); ?></p>
							</div>
							<div class="box-detail">
								<span class="entry-title">
									<?php esc_html_e('Lastest Version: ', 'felan-framework'); ?>
									<?php
									if (Felan_Updater::check_valid_update() && $update) {

										printf(
											__(
												'<a class="button ricetheme-update" href="%1$s" %2$s>Update now</a>',
												'felan-framework'
											),
											wp_nonce_url(
												self_admin_url('update.php?action=upgrade-theme&theme=') . FELAN_THEME_SLUG,
												'upgrade-theme_' . FELAN_THEME_SLUG
											),
											sprintf(
												'id="update-theme" aria-label="%s"',
												esc_attr(sprintf(__('Update %s now'), FELAN_THEME_NAME))
											)
										);
									}
									?>
								</span>
								<p><?php esc_html_e($new_version); ?></p>
							</div>
						</div>
						<div class="entry-detail">
							<a class="entry-title" href="<?php echo esc_attr($get_info['docs']); ?>" target="_blank"><?php esc_html_e('Online Documentation', 'felan-framework'); ?>
								<i class="far fa-external-link-alt"></i>
							</a>
							<a class="entry-title" href="<?php echo esc_attr($get_info['support']); ?>" target="_blank"><?php esc_html_e('Request Support', 'felan-framework'); ?>
								<i class="far fa-external-link-alt"></i>
							</a>
						</div>
					</div>
					<div class="panel-column column-image">
						<img src="<?php echo FELAN_PLUGIN_URL . '/assets/images/img-welcome.jpg' ?>" alt="" />
					</div>
				</div>
			</div>

			<?php
			$felan_tgm_plugins = apply_filters('felan_tgm_plugins', array());
			$installed_plugins = class_exists('TGM_Plugin_Activation') ? TGM_Plugin_Activation::$instance->plugins : array();
			$required_plugins_count = 0;
			?>
			<div class="felan-wrap wrap about-wrap plugins-wrap">
				<div class="entry-heading">
					<h4><?php esc_html_e('Plugins', 'felan-framework'); ?></h4>
					<p><?php esc_html_e('Please install and activate plugins to use all functionality.', 'felan-framework'); ?></p>
				</div>

				<div class="wrap-content">
					<?php if (!empty($felan_tgm_plugins) && class_exists('TGM_Plugin_Activation')) : ?>
						<div class="grid columns-3">
							<?php foreach ($felan_tgm_plugins as $plugin) : ?>
								<?php
								$plugin_obj = $installed_plugins[$plugin['slug']];
								$css_class = '';
								if ($plugin['required']) {
									if (TGM_Plugin_Activation::$instance->is_plugin_active($plugin['slug'])) {
										$css_class .= 'plugin-activated';
									} else {
										$css_class .= 'plugin-deactivated';
									}
								}

								$thumb = isset($plugin['thumb']) ? esc_html($plugin['thumb']) : '';
								?>
								<div class="item <?php echo esc_attr($css_class); ?>">
									<div class="plugin-thumb">
										<img src="<?php echo esc_url($thumb); ?>" alt="<?php esc_html_e($plugin['name']); ?>">

										<div class="plugin-type">
											<span><?php echo $plugin['required'] ? esc_html__('Required', 'felan-framework') : esc_html__('Recommended', 'felan-framework'); ?></span>
										</div>
									</div>
									<div class="entry-detail">
										<div class="plugin-name">
											<span><?php esc_html_e($plugin['name']); ?></span>
											<sup><?php echo isset($plugin['version']) ? esc_html($plugin['version']) : ''; ?></sup>
										</div>

										<div class="plugin-action">
											<?php echo Felan_Plugins::get_plugin_action($plugin_obj); ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>

					<?php else : ?>

						<p><?php esc_html_e('This theme doesn\'t require any plugins.', 'felan-framework'); ?></p>

					<?php endif; ?>

				</div><!-- end .wrap-content -->
			</div>

			<div class="felan-wrap wrap about-wrap changelogs-wrap">
				<div class="entry-heading">
					<h4><?php esc_html_e('Changelogs', 'felan-framework'); ?></h4>
				</div>

				<div class="wrap-content">
					<table class="table-changelogs">
						<thead>
							<tr>
								<th><?php esc_html_e('Version', 'felan-framework'); ?></th>
								<th><?php esc_html_e('Description', 'felan-framework'); ?></th>
								<th><?php esc_html_e('Date', 'felan-framework'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php echo Felan_Updater::get_changelogs(true); ?>
						</tbody>
					</table>
				</div><!-- end .wrap-content -->
			</div>

		<?php
		}

		public function system_page_callback()
		{
			add_thickbox();
			function felan_core_let_to_num($size)
			{
				$l = substr($size, -1);
				$ret = substr($size, 0, -1);
				switch (strtoupper($l)) {
					case 'P':
						$ret *= 1024;
					case 'T':
						$ret *= 1024;
					case 'G':
						$ret *= 1024;
					case 'M':
						$ret *= 1024;
					case 'K':
						$ret *= 1024;
				}

				return $ret;
			}

		?>
			<div class="felan-system-page">
				<div class="about-wrap box">
					<div class="box-header">
						<span class="icon"><i class="lar la-lightbulb"></i></span>
						<?php esc_html_e('WordPress Environment', 'felan-framework'); ?>
					</div>
					<div class="box-body">
						<table class="wp-list-table widefat striped system" cellspacing="0">
							<tbody>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The URL of your site\'s homepage.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Home URL', 'felan-framework'); ?></td>
									<td><?php form_option('home'); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The root URL of your site.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Site URL', 'felan-framework'); ?></td>
									<td><?php form_option('siteurl'); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The version of WordPress installed on your site.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('WP Version', 'felan-framework'); ?></td>
									<td><?php bloginfo('version'); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('Whether or not you have WordPress Multisite enabled.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('WP Multisite', 'felan-framework'); ?></td>
									<td>
										<?php if (is_multisite()) {
											echo '&#10004;';
										} else {
											echo '&ndash;';
										} ?>
									</td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The maximum amount of memory (RAM) that your site can use at one time.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('WP Memory Limit', 'felan-framework'); ?></td>
									<td>
										<?php
										$memory = felan_core_let_to_num(WP_MEMORY_LIMIT);

										if (function_exists('memory_get_usage')) {
											$server_memory = felan_core_let_to_num(@ini_get('memory_limit'));
											$memory = max($memory, $server_memory);
										}

										if ($memory < 134217728) {
											echo '<mark class="error">' . sprintf(__('%s - We recommend setting memory to at least 128MB. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'felan-framework'), size_format($memory), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP') . '</mark>';
										} else {
											echo '<mark class="yes">' . size_format($memory) . '</mark>';
										}
										?>
									</td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('Displays whether or not WordPress is in Debug Mode.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('WP Debug Mode', 'felan-framework'); ?></td>
									<td>
										<?php if (defined('WP_DEBUG') && WP_DEBUG) {
											echo '<mark class="yes">&#10004;</mark>';
										} else {
											echo '&ndash;';
										} ?>
									</td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The current language used by WordPress. Default = English', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Language', 'felan-framework'); ?></td>
									<td><?php echo get_locale() ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The current theme name', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Theme Name', 'felan-framework'); ?></td>
									<td><?php echo FELAN_THEME_NAME; ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The current theme version', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Theme Version', 'felan-framework'); ?></td>
									<td><?php echo FELAN_THEME_VERSION; ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('Installed plugins', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Activated Plugins', 'felan-framework'); ?></td>
									<td>
										<?php
										$all_plugins = get_plugins();
										foreach ($all_plugins as $key => $val) {
											if (is_plugin_active($key)) {
												echo $val['Name'] . ' ' . $val['Version'] . ', ';
											}
										}
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="about-wrap box">
					<div class="box-header">
						<span class="icon"><i class="lar la-lightbulb"></i></span>
						<?php esc_html_e('Server Environment', 'felan-framework'); ?>
					</div>
					<div class="box-body">
						<table class="wp-list-table widefat striped system" cellspacing="0">
							<tbody>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('Information about the web server that is currently hosting your site.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Server Info', 'felan-framework'); ?></td>
									<td><?php esc_html_e($_SERVER['SERVER_SOFTWARE']); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The version of PHP installed on your hosting server.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('PHP Version', 'felan-framework'); ?></td>
									<td><?php if (function_exists('phpversion')) {
											$php_version = esc_html(phpversion());

											if (version_compare($php_version, '5.6', '<')) {
												echo '<mark class="error">' . esc_html__('Felan framework requires PHP version 5.6 or greater. Please contact your hosting provider to upgrade PHP version.', 'felan-framework') . '</mark>';
											} else {
												echo $php_version;
											}
										}
										?></td>
								</tr>
								<?php if (function_exists('ini_get')) : ?>
									<tr>
										<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The largest filesize that can be contained in one post.', 'felan-framework') . '">[?]</a>'; ?></td>
										<td class="title"><?php _e('PHP Post Max Size', 'felan-framework'); ?></td>
										<td><?php echo size_format(felan_core_let_to_num(ini_get('post_max_size'))); ?></td>
									</tr>
									<tr>
										<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'felan-framework') . '">[?]</a>'; ?></td>
										<td class="title"><?php _e('PHP Time Limit', 'felan-framework'); ?></td>
										<td><?php
											$time_limit = ini_get('max_execution_time');

											if ($time_limit > 0 && $time_limit < 180) {
												echo '<mark class="error">' . sprintf(__('%s - We recommend setting max execution time to at least 180. See: <a href="%s" target="_blank">Increasing max execution to PHP</a>', 'felan-framework'), $time_limit, 'http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded') . '</mark>';
											} else {
												echo '<mark class="yes">' . $time_limit . '</mark>';
											}
											?></td>
									</tr>
									<tr>
										<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The maximum number of variables your server can use for a single function to avoid overloads.', 'felan-framework') . '">[?]</a>'; ?></td>
										<td class="title"><?php _e('PHP Max Input Vars', 'felan-framework'); ?></td>
										<td><?php
											$max_input_vars = ini_get('max_input_vars');

											if ($max_input_vars < 5000) {
												echo '<mark class="error">' . sprintf(__('%s - Max input vars limitation will truncate POST data such as menus. Required >= 5000', 'felan-framework'), $max_input_vars) . '</mark>';
											} else {
												echo '<mark class="yes">' . $max_input_vars . '</mark>';
											}
											?></td>
									</tr>
								<?php endif; ?>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The version of MySQL installed on your hosting server.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('MySQL Version', 'felan-framework'); ?></td>
									<td>
										<?php
										global $wpdb;
										echo $wpdb->db_version();
										?>
									</td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The largest filesize that can be uploaded to your WordPress installation.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Max Upload Size', 'felan-framework'); ?></td>
									<td><?php echo size_format(wp_max_upload_size()); ?></td>
								</tr>
								<tr>
									<td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__('The default timezone for your server.', 'felan-framework') . '">[?]</a>'; ?></td>
									<td class="title"><?php _e('Default Timezone is UTC', 'felan-framework'); ?></td>
									<td><?php
										$default_timezone = date_default_timezone_get();
										if ('UTC' !== $default_timezone) {
											echo '<mark class="error">&#10005; ' . sprintf(__('Default timezone is %s - it should be UTC', 'felan-framework'), $default_timezone) . '</mark>';
										} else {
											echo '<mark class="yes">&#10004;</mark>';
										} ?>
									</td>
								</tr>
								<?php
								$checks = array();
								// fsockopen/cURL
								$checks['fsockopen_curl']['name'] = 'fsockopen/cURL';
								$checks['fsockopen_curl']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__('Plugins may use it when communicating with remote services.', 'felan-framework') . '">[?]</a>';
								if (function_exists('fsockopen') || function_exists('curl_init')) {
									$checks['fsockopen_curl']['success'] = true;
								} else {
									$checks['fsockopen_curl']['success'] = false;
									$checks['fsockopen_curl']['note'] = __('Your server does not have fsockopen or cURL enabled. Please contact your hosting provider to enable it.', 'felan-framework') . '</mark>';
								}
								// DOMDocument
								$checks['dom_document']['name'] = 'DOMDocument';
								$checks['dom_document']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__('WordPress Importer use DOMDocument.', 'felan-framework') . '">[?]</a>';
								if (class_exists('DOMDocument')) {
									$checks['dom_document']['success'] = true;
								} else {
									$checks['dom_document']['success'] = false;
									$checks['dom_document']['note'] = sprintf(__('Your server does not have <a href="%s">the DOM extension</a> class enabled. Please contact your hosting provider to enable it.', 'felan-framework'), 'http://php.net/manual/en/intro.dom.php') . '</mark>';
								}
								// XMLReader
								$checks['xml_reader']['name'] = 'XMLReader';
								$checks['xml_reader']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__('WordPress Importer use XMLReader.', 'felan-framework') . '">[?]</a>';
								if (class_exists('XMLReader')) {
									$checks['xml_reader']['success'] = true;
								} else {
									$checks['xml_reader']['success'] = false;
									$checks['xml_reader']['note'] = sprintf(__('Your server does not have <a href="%s">the XMLReader extension</a> class enabled. Please contact your hosting provider to enable it.', 'felan-framework'), 'http://php.net/manual/en/intro.xmlreader.php') . '</mark>';
								}
								// WP Remote Get Check
								$checks['wp_remote_get']['name'] = __('Remote Get', 'felan-framework');
								$checks['wp_remote_get']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__('Retrieve the raw response from the HTTP request using the GET method.', 'felan-framework') . '">[?]</a>';
								$response = wp_remote_get(FELAN_PLUGIN_URL . 'assets/test.txt');

								if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300) {
									$checks['wp_remote_get']['success'] = true;
								} else {
									$checks['wp_remote_get']['note'] = __(' WordPress function <a href="https://codex.wordpress.org/Function_Reference/wp_remote_get">wp_remote_get()</a> test failed. Please contact your hosting provider to enable it.', 'felan-framework');
									if (is_wp_error($response)) {
										$checks['wp_remote_get']['note'] .= ' ' . sprintf(__('Error: %s', 'felan-framework'), sanitize_text_field($response->get_error_message()));
									} else {
										$checks['wp_remote_get']['note'] .= ' ' . sprintf(__('Status code: %s', 'felan-framework'), sanitize_text_field($response['response']['code']));
									}
									$checks['wp_remote_get']['success'] = false;
								}
								foreach ($checks as $check) {
									$mark = !empty($check['success']) ? 'yes' : 'error';
								?>
									<tr>
										<td class="help"><?php echo isset($check['help']) ? $check['help'] : ''; ?></td>
										<td class="title"><?php esc_html_e($check['name']); ?></td>
										<td>
											<mark class="<?php echo $mark; ?>">
												<?php echo !empty($check['success']) ? '&#10004' : '&#10005'; ?><?php echo !empty($check['note']) ? wp_kses_data($check['note']) : ''; ?>
											</mark>
										</td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php
		}

		public function import_page_callback()
		{
			$import_issues = Felan_Importer::get_import_issues();
			$ignore_import_issues = apply_filters('felan_ignore_import_issues', false);

		?>
			<div class="felan-wrap about-wrap">

				<?php
				/**
				 * Action: felan_page_import_before_content
				 */
				do_action('felan_page_import_before_content');
				?>

				<!-- Important Notes -->
				<?php require_once FELAN_PLUGIN_DIR . 'includes/import/views/box-import-notes.php'; ?>
				<!-- /Important Notes -->

				<?php if (!empty($import_issues) && !$ignore_import_issues) : ?>
					<!-- Issues -->
					<?php require_once FELAN_PLUGIN_DIR . 'includes/import/views/box-import-issues.php'; ?>
					<!-- /Issues -->
				<?php else : ?>
					<!-- Import Demos -->
					<?php require_once FELAN_PLUGIN_DIR . 'includes/import/views/box-import-demos.php'; ?>
					<!-- /Import Demos -->
				<?php endif; ?>

				<?php
				/**
				 * Action: felan_page_import_after_content
				 */
				do_action('felan_page_import_after_content');
				?>

			</div>
		<?php
		}

		public function export_page_callback()
		{
			$export_items = Felan_Exporter::get_export_items();
		?>
			<div class="about-wrap felan-box felan-box--gray felan-box--export">
				<div class="felan-box__body grid columns-3">

					<?php
					/**
					 * Action: felan_box_export_before_content
					 */
					do_action('felan_box_export_before_content');
					?>

					<?php if (!empty($export_items)) : ?>
						<?php foreach ($export_items as $item) : ?>
							<?php if (isset($item['name'], $item['action'], $item['icon'])) : ?>
								<!-- Export <?php esc_html_e($item['name']); ?>-->
								<div class="felan-export-item felan-export-item--<?php echo esc_attr(sanitize_title($item['name'])); ?>">
									<form action="<?php echo esc_url(admin_url('/admin-post.php')); ?>" method="POST" class="felan-export-item__form">
										<?php if (isset($item['description'])) : ?>
											<span class="felan-export-item__help hint--right" aria-label="<?php echo esc_attr($item['description']); ?>"><i class="far fa-question-circle"></i></span>
										<?php endif; ?>

										<input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce($item['action'])); ?>">
										<input type="hidden" name="action" value="<?php echo esc_attr($item['action']); ?>">

										<p class="felan-export-item__name"><i class="<?php echo esc_attr($item['icon']); ?>"></i><?php esc_html_e($item['name']); ?>
										</p>

										<p class="felan-export-item__description"><?php esc_html_e($item['description']); ?></p>

										<div class="felan-export-item__icon<?php echo esc_attr(isset($item['input_file_name']) && $item['input_file_name'] ? ' felan-export-item__icon--has-file-name-input' : ''); ?>">

											<?php if (isset($item['input_file_name'], $item['default_file_name']) && $item['input_file_name']) : ?>
												<input type="text" name="<?php echo esc_attr(sanitize_title($item['name']) . '-file-name'); ?>" id="<?php echo esc_attr(sanitize_title($item['name']) . '-file-name'); ?>" class="felan-export-item__input" value="<?php echo esc_attr($item['default_file_name']); ?>">
											<?php endif; ?>
										</div>

										<div class="felan-export-item__footer">
											<?php if (isset($item['export_page_url']) && !empty($item['export_page_url'])) : ?>
												<a href="<?php echo esc_url($item['export_page_url']); ?>" class="button felan-export-item__button"><?php esc_html_e('Export', 'felan-framework'); ?>
													<i class="las la-download"></i></a>
											<?php else : ?>
												<button type="submit" name="export" class="button felan-export-item__button"><?php esc_html_e('Export', 'felan-framework'); ?>
													<i class="las la-download"></i></button>
											<?php endif; ?>
										</div>
									</form>
								</div>
								<!-- /Export <?php esc_html_e($item['name']); ?> -->
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php
					/**
					 * Action: felan_box_export_after_content
					 */
					do_action('felan_box_export_after_content');
					?>
				</div>
			</div>
		<?php
		}

		/**
		 * Redirect the setup page on first activation
		 */
		public function redirect()
		{
			// Bail if no activation redirect transient is set
			if (!get_transient('_felan_activation_redirect')) {
				return;
			}

			if (!current_user_can('manage_options')) {
				return;
			}

			// Delete the redirect transient
			delete_transient('_felan_activation_redirect');

			// Bail if activating from network, or bulk, or within an iFrame
			if (is_network_admin() || isset($_GET['activate-multi']) || defined('IFRAME_REQUEST')) {
				return;
			}

			if ((isset($_GET['action']) && 'upgrade-plugin' == $_GET['action']) && (isset($_GET['plugin']) && strstr($_GET['plugin'], 'felan-framework.php'))) {
				return;
			}

			wp_redirect(admin_url('admin.php?page=felan_setup'));
			exit;
		}

		/**
		 * Create page on first activation
		 * @param $title
		 * @param $content
		 * @param $option
		 */
		private function create_page($title, $content, $option)
		{
			$page_data = array(
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_author' => 1,
				'post_name' => sanitize_title($title),
				'post_title' => $title,
				'post_content' => $content,
				'post_parent' => 0,
				'comment_status' => 'closed'
			);
			$page_id = wp_insert_post($page_data);
			if ($option) {
				if (function_exists('pll_the_languages')) {
					$config = get_option(pll_current_language() . '_felan-framework');
					$config[$option] = $page_id;
					update_option(pll_current_language() . '_felan-framework', $config);
				} else if (defined('ICL_SITEPRESS_VERSION')) {
					$current_language = apply_filters('wpml_current_language', NULL);
					if ($current_language) {
						$config = get_option($current_language . '_felan-framework');
						$config[$option] = $page_id;
						update_option($current_language . '_felan-framework', $config);
					} else {
						$config = get_option('felan-framework');
						$config[$option] = $page_id;
						update_option('felan-framework', $config);
					}
				} else {
					$config = get_option('felan-framework');
					$config[$option] = $page_id;
					update_option('felan-framework', $config);
				}
			}
		}

		/**
		 * Output page setup
		 */
		public function setup_page()
		{
			$step = !empty($_GET['step']) ? absint(wp_unslash($_GET['step'])) : 1;
			if (3 === $step && !empty($_POST)) {
				$create_pages = isset($_POST['felan-create-page']) ? felan_clean(wp_unslash($_POST['felan-create-page'])) : array();
				$page_titles = isset($_POST['felan-page-title']) ? felan_clean(wp_unslash($_POST['felan-page-title'])) : array();
				$pages_to_create = array(
					'dashboard' => '[felan_dashboard]',
					'freelancer_dashboard' => '[felan_freelancer_dashboard]',
					'meetings' => '[felan_meetings]',
					'disputes' => '[felan_disputes]',
					'freelancer_meetings' => '[felan_freelancer_meetings]',
					'settings' => '[felan_settings]',
					'freelancer_settings' => '[felan_freelancer_settings]',
					'jobs_dashboard' => '[felan_jobs]',
					'projects' => '[felan_projects]',
					'projects_submit' => '[felan_projects_submit]',
					'my_project' => '[felan_my_project]',
					'jobs_submit' => '[felan_jobs_submit]',
					'wallet' => '[felan_wallet]',
					'jobs_performance' => '[felan_jobs_performance]',
					'applicants' => '[felan_applicants]',
					'freelancers' => '[felan_freelancers]',
					'user_package' => '[felan_user_package]',
					'company' => '[felan_company]',
					'submit_company' => '[felan_submit_company]',
					'my_jobs' => '[felan_my_jobs]',
					'messages' => '[felan_messages]',
					'freelancer_messages' => '[felan_messages]',
					'package' => '[felan_package]',
					'payment' => '[felan_payment]',
					'payment_service' => '[felan_service_payment]',
					'payment_project' => '[felan_project_payment]',
					'service_payment_completed' => '[felan_service_payment_completed]',
					'project_payment_completed' => '[felan_project_payment_completed]',
					'freelancer_company' => '[felan_freelancer_company]',
					'payment_completed' => '[felan_payment_completed]',
					'freelancer_reviews' => '[felan_freelancer_my_review]',
					'freelancer_profile' => '[felan_freelancer_profile]',
					'freelancer_user_package' => '[felan_freelancer_user_package]',
					'freelancer_wallet' => '[felan_freelancer_wallet]',
					'employer_service' => '[felan_employer_service]',
					'freelancer_service' => '[felan_freelancer_service]',
					'submit_service' => '[felan_submit_service]',
					'freelancer_package' => '[felan_freelancer_package]',
					'freelancer_payment' => '[felan_freelancer_payment]',
					'freelancer_payment_completed' => '[felan_freelancer_payment_completed]',
					'disputes' => '[felan_disputes]',
					'freelancer_disputes' => '[felan_freelancer_disputes]',
				);
				foreach ($pages_to_create as $page => $content) {
					if (!isset($create_pages[$page]) || empty($page_titles[$page])) {
						continue;
					}
					$this->create_page(sanitize_text_field($page_titles[$page]), $content, 'felan_' . $page . '_page_id');
				}
			}
		?>
			<div class="felan-setup-wrap felan-wrap about-wrap setup-wrap">
				<h3><?php esc_html_e('Felan Setup', 'felan-framework'); ?></h3>
				<ul class="felan-setup-steps">
					<li class="<?php if ($step === 1) echo 'felan-setup-active-step'; ?>"><?php esc_html_e('1. Introduction', 'felan-framework'); ?></li>
					<li class="<?php if ($step === 2) echo 'felan-setup-active-step'; ?>"><?php esc_html_e('2. Page Setup', 'felan-framework'); ?></li>
					<li class="<?php if ($step === 3) echo 'felan-setup-active-step'; ?>"><?php esc_html_e('3. Done', 'felan-framework'); ?></li>
				</ul>

				<?php if (1 === $step) : ?>

					<h3><?php esc_html_e('Setup Wizard Introduction', 'felan-framework'); ?></h3>
					<p><?php _e('Thanks for installing <em>Felan</em>!', 'felan-framework'); ?></p>
					<p><?php esc_html_e('This setup wizard will help you get started by creating the pages for jobs submission, jobs management, profile management, listing jobs, jobs wishlist, jobs booking...', 'felan-framework'); ?></p>
					<p><?php printf(__('If you want to skip the wizard and setup the pages and shortcodes yourself manually, the process is still relatively simple. Refer to the %sdocumentation%s for help.', 'felan-framework'), '<a href="#"', '</a>'); ?></p>

					<p class="submit">
						<a href="<?php echo esc_url(add_query_arg('step', 2)); ?>" class="button button-primary"><?php esc_html_e('Continue to page setup', 'felan-framework'); ?></a>
						<a href="<?php echo esc_url(admin_url('admin.php?page=felan_setup&step=3')); ?>" class="button"><?php esc_html_e('Skip setup. I will setup the plugin manually (Not Recommended)', 'felan-framework'); ?></a>
					</p>

				<?php endif; ?>
				<?php if (2 === $step) : ?>

					<h3><?php esc_html_e('Page Setup', 'felan-framework'); ?></h3>

					<p><?php printf(__('<em>felan-framework</em> includes %1$sshortcodes%2$s which can be used within your %3$spages%2$s to output content. These can be created for you below. For more information on the felan-framework shortcodes view the %4$sshortcode documentation%2$s.', 'felan-framework'), '<a href="https://codex.wordpress.org/shortcode" title="What is a shortcode?" target="_blank" class="help-page-link">', '</a>', '<a href="http://codex.wordpress.org/Pages" target="_blank" class="help-page-link">', '<a href="#" target="_blank" class="help-page-link">'); ?></p>

					<form action="<?php echo esc_url(add_query_arg('step', 3)); ?>" method="post">
						<table class="felan-shortcodes widefat">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th><?php esc_html_e('Page Title', 'felan-framework'); ?></th>
									<th><?php esc_html_e('Page Description', 'felan-framework'); ?></th>
									<th><?php esc_html_e('Content Shortcode', 'felan-framework'); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[dashboard]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Dashboard Employer', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[dashboard]" /></td>
									<td>
										<p><?php esc_html_e('This page show dashboard.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_dashboard]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[jobs_performance]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Jobs Performance', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[jobs_performance]" /></td>
									<td>
										<p><?php esc_html_e('This page show jobs performance.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_jobs_performance]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[jobs_dashboard]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Jobs', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[jobs_dashboard]" /></td>
									<td>
										<p><?php esc_html_e('This page show all jobs.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_jobs]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[jobs_submit]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('New Jobs', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[jobs_submit]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to add jobs to your website via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_jobs_submit]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[applicants]" />
									</td>
									<td><input type="text" value="<?php echo esc_attr(_x('Applicants', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[applicants]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Applicants" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_applicants]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancers]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancers For Employer', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancers]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancers For Employer" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancers]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[user_package]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('User Packages', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[user_package]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "User Package" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_user_package]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[messages]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Messages Employer', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[messages]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Messages Employer" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_messages]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_messages]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Messages Freelancer', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_messages]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Messages Freelancer" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_messages]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[company]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Company', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[company]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Company" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_company]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[submit_company]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('New Company', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[submit_company]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Company" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_submit_company]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[settings]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Settings Employer', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[settings]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Settings Employer" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_settings]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[meetings]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Meetings Employer', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[meetings]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Meetings Employer" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_meetings]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[package]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Packages', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[package]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Packages" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_package]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[payment]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Payment', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[payment]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Payment" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan-payment]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[payment_completed]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Payment Completed', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[payment_completed]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Payment Completed" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_payment_completed]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_dashboard]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Dashboard Freelancer', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_dashboard]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Dashboard Freelancer" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_dashboard]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_settings]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Settings', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_settings]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Settings" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_settings]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_company]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Company', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_company]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Company" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_company]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_profile]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Profile', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_profile]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Profile" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_profile]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_reviews]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('My Review', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_reviews]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer My Review" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_my_review]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_meetings]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Meetings', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_meetings]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Meetings" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_meetings]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_user_package]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer User Package', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_user_package]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer User Package" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_user_package]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_package]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Package', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_package]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Package" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_package]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_payment]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Payment', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_payment]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Payment" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_payment]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_payment_completed]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Payment Completed', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_payment_completed]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Payment Completed" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_payment_completed]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[employer_service]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Employer Service', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[employer_service]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Employer Service" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_employer_service]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_service]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Service', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_service]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Service" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_service]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[submit_service]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Service Submit', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[submit_service]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Service Submit" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_submit_service]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[payment_service]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Service Payment', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[payment_service]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Service Payment" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_service_payment]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[service_payment_completed]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Service Payment Completed', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[service_payment_completed]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Service Payment Completed" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_service_payment_completed]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[projects]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Employer Projects', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[projects]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Employer Projects" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_projects]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[projects_submit]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Projects Submit', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[projects_submit]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Projects Submit" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_projects_submit]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[my_project]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Projects', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[my_project]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Projects" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_my_project]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[payment_project]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Projects Payment', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[payment_project]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Project Payment" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_project_payment]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[project_payment_completed]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Projects Payment Completed', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[project_payment_completed]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Projects Payment Completed" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_project_payment_completed]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[my_jobs]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('My Jobs', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[my_jobs]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "My Jobs" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_my_jobs]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_wallet]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Wallet', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_wallet]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Wallet" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_wallet]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[disputes]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Employer Disputes', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[disputes]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Employer Disputes" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_disputes]</code></td>
								</tr>
								<tr>
									<td><input type="checkbox" checked="checked" name="felan-create-page[freelancer_disputes]" /></td>
									<td><input type="text" value="<?php echo esc_attr(_x('Freelancer Disputes', 'Default page title (wizard)', 'felan-framework')); ?>" name="felan-page-title[freelancer_disputes]" /></td>
									<td>
										<p><?php esc_html_e('This page allows users to view their own "Freelancer Disputes" via the front-end.', 'felan-framework'); ?></p>
									</td>
									<td><code>[felan_freelancer_disputes]</code></td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="4">
										<input type="submit" class="button button-primary" value="<?php esc_html_e('Create selected pages', 'felan-framework'); ?>" />
										<a href="<?php echo esc_url(add_query_arg('step', 3)); ?>" class="button"><?php esc_html_e('Skip this step', 'felan-framework'); ?></a>
									</th>
								</tr>
							</tfoot>
						</table>
					</form>

				<?php endif; ?>
				<?php if (3 === $step) : ?>

					<h3><?php esc_html_e('All Done!', 'felan-framework'); ?></h3>

					<p><?php esc_html_e('Looks like you\'re all set to start using the plugin. In case you\'re wondering where to go next:', 'felan-framework'); ?></p>

					<ul class="felan-next-steps">
						<li>
							<a href="<?php echo admin_url('themes.php?page=felan-framework'); ?>"><?php esc_html_e('Plugin settings', 'felan-framework'); ?></a>
						</li>
						<li>
							<a href="<?php echo admin_url('post-new.php?post_type=jobs'); ?>"><?php esc_html_e('Add a jobs the back-end', 'felan-framework'); ?></a>
						</li>
						<?php if ($permalink = felan_get_permalink('jobs')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('Show all jobs', 'felan-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = felan_get_permalink('submit_jobs')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('Add a jobs via the front-end', 'felan-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = felan_get_permalink('jobs_dashboard')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user jobs', 'felan-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = felan_get_permalink('my_profile')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user profile', 'felan-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = felan_get_permalink('my_booking')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View my booking', 'felan-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = felan_get_permalink('bookings')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user bookings', 'felan-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = felan_get_permalink('packages')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View packages', 'felan-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = felan_get_permalink('payment')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View payment', 'felan-framework'); ?></a>
							</li>
						<?php endif; ?>
						<?php if ($permalink = felan_get_permalink('country')) : ?>
							<li>
								<a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View country detail', 'felan-framework'); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php
		}
	}
}
