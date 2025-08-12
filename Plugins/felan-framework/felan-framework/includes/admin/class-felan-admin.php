<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('Felan_Admin')) {
	/**
	 * Class Felan_Admin
	 */
	class Felan_Admin
	{

		public function __construct()
		{
			add_action('init', array($this, 'location_rewrite_rules'));
			add_filter('term_link', array($this, 'location_term_link'), 10, 3);
		}

		public function location_rewrite_rules()
		{
			$post_types = ['jobs', 'company', 'service', 'project', 'freelancer'];

			foreach ($post_types as $post_type) {
				add_rewrite_rule(
					"{$post_type}-location/([^/]+)/?$",
					'index.php?felan_location=$matches[1]&post_type=' . $post_type,
					'top'
				);
				add_rewrite_rule(
					"{$post_type}-state/([^/]+)/?$",
					'index.php?felan_state=$matches[1]&post_type=' . $post_type,
					'top'
				);
			}
		}

		public function location_term_link($termlink, $term, $taxonomy)
		{
			if ($taxonomy === 'felan_location') {
				$post_type = get_query_var('post_type');
				if (in_array($post_type, ['jobs', 'company', 'service', 'project', 'freelancer'])) {
					return home_url("/{$post_type}-location/{$term->slug}/");
				}
			}
			if ($taxonomy === 'felan_state') {
				$post_type = get_query_var('post_type');
				if (in_array($post_type, ['jobs', 'company', 'service', 'project', 'freelancer'])) {
					return home_url("/{$post_type}-state/{$term->slug}/");
				}
			}
			return $termlink;
		}

		/**
		 * Remove admin bar
		 * @return bool
		 */
		function remove_admin_bar()
		{
			if (!current_user_can('administrator') && !is_admin()) {
				show_admin_bar(false);
			}
		}

		/**
		 * Check if it is a jobs edit page.
		 * @return bool
		 */
		public function is_felan_admin()
		{
			if (is_admin()) {
				global $pagenow;
				if (in_array($pagenow, array('edit.php', 'post.php', 'post-new.php', 'edit-tags.php'))) {
					global $post_type;
					if ('jobs' == $post_type) {
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * Register admin_menu
		 */
		public function admin_menu()
		{
			$enable_claim_listing = felan_get_option('enable_claim_listing', '1');
			if ($enable_claim_listing) :
				add_menu_page(
					esc_html__('Claim Listing', 'felan-framework'),
					esc_html__('Claim Listing', 'felan-framework'),
					'manage_options',
					'claim_listing',
					array($this, 'menu_claim_listing_callback'),
					'dashicons-list-view',
					12
				);
			endif;
		}

		public function menu_claim_listing_callback()
		{
			$claim_email = $claim_name = $claim_username = $claim_status = '';

			$meta_query = array(
				'relative' => 'AND',
				array(
					'key' => 'felan-claim_request',
					'value' => 1,
					'compare' => '=',
				),
			);
			if (isset($_GET['claim_name']) && $_GET['claim_name'] != '') {
				$claim_name = $_GET['claim_name'];
				$meta_query[] = array(
					'key' => 'felan-cd_your_name',
					'value' => $_GET['claim_name'],
					'compare' => 'LIKE',
				);
			}
			if (isset($_GET['claim_email']) && $_GET['claim_email'] != '') {
				$claim_email = $_GET['claim_email'];
				$meta_query[] = array(
					'key' => 'felan-cd_your_email',
					'value' => $_GET['claim_email'],
					'compare' => 'LIKE',
				);
			}
			if (isset($_GET['claim_username']) && $_GET['claim_username'] != '') {
				$claim_username = $_GET['claim_username'];
				$meta_query[] = array(
					'key' => 'felan-cd_your_username',
					'value' => $_GET['claim_username'],
					'compare' => 'LIKE',
				);
			}
			if (isset($_GET['claim_status']) && $_GET['claim_status'] != '') {
				$claim_status = $_GET['claim_status'];
				$meta_query[] = array(
					'key' => 'felan-cd_status',
					'value' => $_GET['claim_status'],
					'compare' => '=',
				);
			}
			$paged = isset($_REQUEST['paged']) ? max(1, (int)$_REQUEST['paged']) : 1;
			$args = array(
				'post_type' => 'jobs',
				'posts_per_page' => 20,
				'paged' => $paged,
				'post_status' => 'publish',
				'meta_query' => $meta_query,
			);
			// The Query
			$the_query = new WP_Query($args);
			$count = $the_query->found_posts;
?>
			<div class="felan-wrap wrap about-wrap claim-wrap">
				<div class="entry-search">
					<div class="claim-action">
						<a href="#" class="button button-delete"><?php esc_html_e('Delete', 'felan-framework'); ?></a>
					</div>
					<form action="" method="GET" class="claimFilter">
						<div class="field-group">
							<input type="text" name="claim_name" value="<?php echo $claim_name; ?>" placeholder="<?php esc_html_e('Name', 'felan-framework'); ?>">
							<input type="email" name="claim_email" value="<?php echo $claim_email; ?>" placeholder="<?php esc_html_e('Email', 'felan-framework'); ?>">
							<input type="text" name="claim_username" value="<?php echo $claim_username; ?>" placeholder="<?php esc_html_e('Username', 'felan-framework'); ?>">
							<select name="claim_status" id="claim_status">
								<option value=""><?php esc_html_e('All Status', 'felan-framework'); ?></option>
								<option value="pending" <?php if ($claim_status == 'pending') {
															echo 'selected';
														} ?>><?php esc_html_e('Pending', 'felan-framework'); ?></option>
								<option value="accept" <?php if ($claim_status == 'accept') {
															echo 'selected';
														} ?>><?php esc_html_e('Accept', 'felan-framework'); ?></option>
								<option value="refuse" <?php if ($claim_status == 'refuse') {
															echo 'selected';
														} ?>><?php esc_html_e('Refuse', 'felan-framework'); ?></option>
							</select>
							<input type="hidden" name="page" value="claim_listing">
							<input type="submit" name="submit" value="<?php esc_html_e('Filter', 'felan-framework'); ?>">
						</div>
					</form>
					<div class="total"><?php printf(_n('%s item', '%s items', $count, 'felan-framework'), '<span class="count">' . esc_html($count) . '</span>'); ?></div>
				</div>

				<div class="wrap-content">
					<form action="" method="POST">
						<table class="table-changelogs">
							<thead>
								<tr>
									<th><input type="checkbox" id="checkall" name="claim_item"></th>
									<th><?php esc_html_e('Name', 'felan-framework'); ?></th>
									<th><?php esc_html_e('Email', 'felan-framework'); ?></th>
									<th><?php esc_html_e('Username', 'felan-framework'); ?></th>
									<th><?php esc_html_e('Listing Url', 'felan-framework'); ?></th>
									<th><?php esc_html_e('Messager', 'felan-framework'); ?></th>
									<th><?php esc_html_e('Status', 'felan-framework'); ?></th>
									<th><?php esc_html_e('Action', 'felan-framework'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								// The Loop
								if ($the_query->have_posts()) {

									$i = 0;
									while ($the_query->have_posts()) {
										$the_query->the_post();
										$i++;
										$id = get_the_ID();
										$cd_your_name = get_post_meta($id, FELAN_METABOX_PREFIX . 'cd_your_name', true);
										$cd_your_email = get_post_meta($id, FELAN_METABOX_PREFIX . 'cd_your_email', true);
										$cd_your_listing = get_post_meta($id, FELAN_METABOX_PREFIX . 'cd_your_listing', true);
										$cd_your_username = get_post_meta($id, FELAN_METABOX_PREFIX . 'cd_your_username', true);
										$cd_messager = get_post_meta($id, FELAN_METABOX_PREFIX . 'cd_messager', true);
										$cd_status = get_post_meta($id, FELAN_METABOX_PREFIX . 'cd_status', true);
										$verified_listing = get_post_meta($id, FELAN_METABOX_PREFIX . 'verified_listing', true);
										echo '<tr>';
										echo '<td><input type="checkbox" name="claim_item"></td>';
										echo '<td>' . $cd_your_name . '</td>';
										echo '<td>' . $cd_your_email . '</td>';
										echo '<td>' . $cd_your_username . '</td>';
										echo '<td><a href="' . $cd_your_listing . '" target="_Blank">' . $cd_your_listing . '</a></td>';
										echo '<td>' . $cd_messager . '</td>';
										if ($cd_status == 'pending') {
											$value = 'pending';
										} else if ($verified_listing == 1) {
											$value = 'accept';
										} else {
											$value = 'refuse';
										}
										$data = 'data-status="' . $value . '"';
										echo '<td class="status"' . $data . '>' . $value . '</td>';
										echo '<td>
                                                <input type="submit" data-status="accept" data-jobs_id="' . $id . '" class="button button-primary" value="' . esc_attr('Accept', 'felan-framework') . '">
                                                <input type="submit" data-status="refuse" data-jobs_id="' . $id . '" class="button button-secondary" value="' . esc_attr('Refuse', 'felan-framework') . '">
                                                <input type="submit" data-status="delete" data-jobs_id="' . $id . '" class="button button-delete" value="' . esc_attr('Delete', 'felan-framework') . '"></td>';
										echo '</tr>';
									}
								} else {
									echo '<tr class="align-center">';
									echo '<td colspan="7">' . esc_attr('No result', 'felan-framework') . '</td>';
									echo '</tr>';
								}
								/* Restore original Post Data */
								wp_reset_postdata();
								?>
							</tbody>
						</table>
						<div class="pagination">
							<?php
							$big = 999999999; // need an unlikely integer

							echo paginate_links(array(
								'base' => admin_url('admin.php?page=claim_listing&paged=%#%'),
								'format' => '?paged=%#%',
								'current' => max(1, $paged),
								'total' => $the_query->max_num_pages,
								'prev_text' => '<i class="far fa-angle-left"></i>',
								'next_text' => '<i class="far fa-angle-right"></i>',
							));
							?>
						</div>
						<div class="felan-loading-effect"><span class="felan-dual-ring small"></span></div>
					</form>
				</div><!-- end .wrap-content -->
			</div>
<?php
		}

		/**
		 * Register post_type
		 * @param $post_types
		 * @return mixed
		 */
		public function register_post_type($post_types)
		{
			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');

			if ($enable_wallet_mode == '1') {
				$post_types['wallet_order'] = array(
					'labels' => array(
						'name' => esc_html__('Wallet Order', 'felan-framework'),
						'singular_name' => esc_html__('Wallet Order', 'felan-framework'),
						'all_items' => esc_html__('Wallet Order', 'felan-framework'),
					),
					'supports' => array('title'),
					'menu_icon' => 'dashicons-printer',
					'capabilities' => $this->get_order_capabilities(),
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_wallet_order_slug', 'order'),
					),
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 10,
				);
				$post_types['employer_withdraw'] = array(
					'labels' => array(
						'name' => esc_html__('Withdraw', 'felan-framework'),
						'singular_name' => esc_html__('Withdraw', 'felan-framework'),
						'all_items' => esc_html__('Withdraw', 'felan-framework'),
					),
					'supports' => array('title'),
					'menu_icon' => 'dashicons-money-alt',
					'capabilities' => $this->get_freelancer_withdraw_capabilities(),
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_employer_withdraw_slug', 'employer_withdraw'),
					),
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 11,
				);
			}

			$post_types['company'] = array(
				'labels' => array(
					'name' => esc_html__('Companies', 'felan-framework'),
					'singular_name' => esc_html__('Companies', 'felan-framework'),
					'all_items' => esc_html__('Companies', 'felan-framework'),
					'add_new' => esc_html__('Add New Companies', 'felan-framework'),
					'add_new_item' => esc_html__('Add New Companies', 'felan-framework'),
					'edit_item' => esc_html__('Edit Companies', 'felan-framework'),
					'new_item' => esc_html__('Add New Companies', 'felan-framework'),
					'view_item' => esc_html__('View Companies', 'felan-framework'),
				),
				'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments'),
				'menu_icon' => 'dashicons-admin-multisite',
				'can_export' => true,
				'show_in_rest' => true,
				'capability_type' => 'company',
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('felan_company_slug', 'company'),
					'with_front' => false,
				),
				'has_archive' => apply_filters('felan_company_has_archive', 'company'),
				'show_in_admin_bar' => true,
				'menu_position' => 6,
			);

			$post_types['package'] = array(
				'labels' => array(
					'name' => esc_html__('Package', 'felan-framework'),
					'singular_name' => esc_html__('Package', 'felan-framework'),
					'all_items' => esc_html__('Package', 'felan-framework'),
				),
				'supports' => array('title', 'thumbnail'),
				'menu_icon' => 'dashicons-archive',
				'can_export' => true,
				'show_in_rest' => true,
				'capability_type' => 'package',
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('felan_package_slug', 'package'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 7,
			);

			$post_types['user_package'] = array(
				'labels' => array(
					'name' => esc_html__('User Packages', 'felan-framework'),
					'singular_name' => esc_html__('User Packages', 'felan-framework'),
					'all_items' => esc_html__('User Packages', 'felan-framework'),
				),
				'supports' => array('title', 'excerpt'),
				'menu_icon' => 'dashicons-money',
				'can_export' => true,
				'capabilities' => $this->get_user_package_capabilities(),
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('felan_user_package_slug', 'user_package'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 8,
			);

			$post_types['invoice'] = array(
				'labels' => array(
					'name' => esc_html__('Invoices', 'felan-framework'),
					'singular_name' => esc_html__('Invoices', 'felan-framework'),
					'all_items' => esc_html__('Invoices', 'felan-framework'),
				),
				'supports' => array('title', 'excerpt'),
				'menu_icon' => 'dashicons-list-view',
				'capabilities' => $this->get_invoice_capabilities(),
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('felan_invoice_slug', 'invoice'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 9,
			);

			$post_types['freelancer_withdraw'] = array(
				'labels' => array(
					'name' => esc_html__('Withdraw', 'felan-framework'),
					'singular_name' => esc_html__('Withdraw', 'felan-framework'),
					'all_items' => esc_html__('Withdraw', 'felan-framework'),
				),
				'supports' => array('title'),
				'menu_icon' => 'dashicons-money-alt',
				'capabilities' => $this->get_freelancer_withdraw_capabilities(),
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_withdraw_slug', 'freelancer_withdraw'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 11,
			);

			if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') {
				$post_types['freelancer'] = array(
					'labels' => array(
						'name' => esc_html__('Candidate', 'felan-framework'),
						'singular_name' => esc_html__('Candidate', 'felan-framework'),
						'all_items' => esc_html__('Candidate', 'felan-framework'),
						'add_new' => esc_html__('Add New Candidate', 'felan-framework'),
						'add_new_item' => esc_html__('Add New Candidate', 'felan-framework'),
						'edit_item' => esc_html__('Edit Candidate', 'felan-framework'),
						'new_item' => esc_html__('Add New Candidate', 'felan-framework'),
						'view_item' => esc_html__('View Candidate', 'felan-framework'),
					),
					'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'page-attributes', 'comments'),
					'menu_icon' => 'dashicons-buddicons-buddypress-logo',
					'can_export' => true,
					'show_in_rest' => true,
					'capabilities' => $this->get_freelancer_capabilities(),
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_freelancer_slug', 'candidate'),
						'with_front' => false,
					),
					'has_archive' => apply_filters('felan_freelancer_has_archive', 'candidate'),
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 13,
				);
			} else {
				$post_types['freelancer'] = array(
					'labels' => array(
						'name' => esc_html__('Freelancers', 'felan-framework'),
						'singular_name' => esc_html__('Freelancers', 'felan-framework'),
						'all_items' => esc_html__('Freelancers', 'felan-framework'),
						'add_new' => esc_html__('Add New Freelancers', 'felan-framework'),
						'add_new_item' => esc_html__('Add New Freelancers', 'felan-framework'),
						'edit_item' => esc_html__('Edit Freelancers', 'felan-framework'),
						'new_item' => esc_html__('Add New Freelancers', 'felan-framework'),
						'view_item' => esc_html__('View Freelancers', 'felan-framework'),
					),
					'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'page-attributes', 'comments'),
					'menu_icon' => 'dashicons-buddicons-buddypress-logo',
					'can_export' => true,
					'show_in_rest' => true,
					'capabilities' => $this->get_freelancer_capabilities(),
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_freelancer_slug', 'freelancer'),
						'with_front' => false,
					),
					'has_archive' => apply_filters('felan_freelancer_has_archive', 'freelancer'),
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 13,
				);
			}

			$post_types['freelancer_package'] = array(
				'labels' => array(
					'name' => esc_html__('Package', 'felan-framework'),
					'singular_name' => esc_html__('Package', 'felan-framework'),
					'all_items' => esc_html__('Package', 'felan-framework'),
				),
				'supports' => array('title', 'thumbnail'),
				'menu_icon' => 'dashicons-archive',
				'can_export' => true,
				'show_in_rest' => true,
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_package_slug', 'freelancer_package'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 15,
			);

			$post_types['freelancer_order'] = array(
				'labels' => array(
					'name' => esc_html__('Order', 'felan-framework'),
					'singular_name' => esc_html__('Order', 'felan-framework'),
					'all_items' => esc_html__('Service Order', 'felan-framework'),
				),
				'supports' => array('title'),
				'menu_icon' => 'dashicons-list-view',
				'capabilities' => $this->get_order_capabilities(),
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('felan_order_slug', 'freelancer_order'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 16,
			);

			$post_types['messages'] = array(
				'labels' => array(
					'name' => esc_html__('Messages', 'felan-framework'),
					'singular_name' => esc_html__('Messages', 'felan-framework'),
					'all_items' => esc_html__('Messages', 'felan-framework'),
				),
				'supports' => array('title', 'excerpt'),
				'menu_icon'         => 'dashicons-format-chat',
				'has_archive'       => false,
				'publicly_queryable' => false,
				'show_in_rest'		=> false,
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 17,
			);

			$post_types['notification'] = array(
				'labels' => array(
					'name' => esc_html__('Notification', 'felan-framework'),
					'singular_name' => esc_html__('Notification', 'felan-framework'),
					'all_items' => esc_html__('Notification', 'felan-framework'),
				),
				'supports' => array('title', 'excerpt'),
				'menu_icon'         => 'dashicons-bell',
				'has_archive'       => false,
				'publicly_queryable' => false,
				'show_in_rest'		=> false,
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 18,
			);

			$post_types['locations'] = array(
				'labels' => array(
					'name' => esc_html__('Locations', 'felan-framework'),
					'singular_name' => esc_html__('Locations', 'felan-framework'),
					'all_items' => esc_html__('Locations', 'felan-framework'),
				),
				'supports' => array(),
				'menu_icon' => 'dashicons-calendar-alt',
				'capabilities' => array( // Chỉ cho phép xem danh sách
					'read' => 'read',
					'edit_posts' => 'do_not_allow',
					'delete_posts' => 'do_not_allow',
					'publish_posts' => 'do_not_allow',
				),
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('felan_locations_slug', 'locations'),
				),
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_position' => 22,
			);

			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			if ($enable_post_type_jobs == '1') {
				$post_types['jobs'] = array(
					'labels' => array(
						'name' => esc_html__('Jobs', 'felan-framework'),
						'singular_name' => esc_html__('Jobs', 'felan-framework'),
						'all_items' => esc_html__('Jobs', 'felan-framework'),
						'add_new' => esc_html__('Add New Jobs', 'felan-framework'),
						'add_new_item' => esc_html__('Add New Jobs', 'felan-framework'),
						'edit_item' => esc_html__('Edit Jobs', 'felan-framework'),
						'new_item' => esc_html__('Add New Jobs', 'felan-framework'),
						'view_item' => esc_html__('View Jobs', 'felan-framework'),
					),
					'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', 'comments'),
					'menu_icon' => 'dashicons-hammer',
					'can_export' => true,
					'show_in_rest' => true,
					'capability_type' => 'jobs',
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_jobs_slug', 'jobs'),
						'with_front' => false,
					),
					'show_ui'	=> true,
					'menu_position' => 3,
					'has_archive' => apply_filters('felan_jobs_has_archive', 'jobs'),
					'show_in_menu' => true,
				);

				$post_types['applicants'] = array(
					'labels' => array(
						'name' => esc_html__('Applicants', 'felan-framework'),
						'singular_name' => esc_html__('Applicants', 'felan-framework'),
						'all_items' => esc_html__('Applicants', 'felan-framework'),
					),
					'menu_icon' => 'dashicons-universal-access-alt',
					'capabilities' => $this->get_applicants_capabilities(),
					'map_meta_cap' => true,
					'supports' => array('title'),
					'rewrite' => array(
						'slug' => apply_filters('felan_applicants_slug', 'applicants'),
					),
					'show_in_admin_bar' => true,
					'menu_position' => 4,
				);
				if (felan_get_option('enable_job_alerts') === '1') {
					$post_types['job_alerts'] = array(
						'labels' => array(
							'name' => esc_html__('Job Alerts', 'felan-framework'),
							'singular_name' => esc_html__('Job Alerts', 'felan-framework'),
							'all_items' => esc_html__('Job Alerts', 'felan-framework'),
						),
						'menu_icon'         => 'dashicons-email-alt',
						'map_meta_cap' => true,
						'supports' => array('title'),
						'show_in_admin_bar' => true,
						'menu_position' => 5,
					);
				}
				$post_types['meetings'] = array(
					'labels' => array(
						'name' => esc_html__('Meetings', 'felan-framework'),
						'singular_name' => esc_html__('Meetings', 'felan-framework'),
						'all_items' => esc_html__('Meetings', 'felan-framework'),
					),
					'supports' => array('title'),
					'menu_icon' => 'dashicons-calendar-alt',
					'capabilities' => $this->get_meetings_capabilities(),
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_meetings_slug', 'meetings'),
					),
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 19,
				);
			}

			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			if ($enable_post_type_service == '1') {
				$post_types['service_order'] = array(
					'labels' => array(
						'name' => esc_html__('Service Order', 'felan-framework'),
						'singular_name' => esc_html__('Service Order', 'felan-framework'),
						'all_items' => esc_html__('Service Order', 'felan-framework'),
					),
					'supports' => array('title'),
					'menu_icon' => 'dashicons-printer',
					'capabilities' => $this->get_order_capabilities(),
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_service_order_slug', 'service_order'),
					),
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 10,
				);

				$post_types['service'] = array(
					'labels' => array(
						'name' => esc_html__('Services', 'felan-framework'),
						'singular_name' => esc_html__('Services', 'felan-framework'),
						'all_items' => esc_html__('Services', 'felan-framework'),
						'add_new' => esc_html__('Add New Services', 'felan-framework'),
						'add_new_item' => esc_html__('Add New Services', 'felan-framework'),
						'edit_item' => esc_html__('Edit Services', 'felan-framework'),
						'new_item' => esc_html__('Add New Services', 'felan-framework'),
						'view_item' => esc_html__('View Services', 'felan-framework'),
					),
					'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', 'comments'),
					'menu_icon' => 'dashicons-hammer',
					'can_export' => true,
					'show_in_rest' => true,
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_service_slug', 'services'),
						'with_front' => false,
					),
					'show_ui'	=> true,
					'has_archive' => apply_filters('felan_service_has_archive', 'services'),
					'show_in_menu' => true,
					'menu_position' => 14,
				);

				$post_types['disputes'] = array(
					'labels' => array(
						'name' => esc_html__('Service Disputes', 'felan-framework'),
						'singular_name' => esc_html__('Service Disputes', 'felan-framework'),
						'all_items' => esc_html__('Service Disputes', 'felan-framework'),
					),
					'supports' => array('title', 'excerpt'),
					'menu_icon' => 'dashicons-calendar-alt',
					'capabilities' => $this->get_meetings_capabilities(),
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_disputes_slug', 'disputes'),
					),
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 20,
				);
			}

			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			if ($enable_post_type_project == '1') {
				$post_types['project'] = array(
					'labels' => array(
						'name' => esc_html__('Projects', 'felan-framework'),
						'singular_name' => esc_html__('Projects', 'felan-framework'),
						'all_items' => esc_html__('Projects', 'felan-framework'),
						'add_new' => esc_html__('Add New Projects', 'felan-framework'),
						'add_new_item' => esc_html__('Add New Projects', 'felan-framework'),
						'edit_item' => esc_html__('Edit Projects', 'felan-framework'),
						'new_item' => esc_html__('Add New Projects', 'felan-framework'),
						'view_item' => esc_html__('View Projects', 'felan-framework'),
					),
					'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments'),
					'menu_icon' => 'dashicons-sos',
					'can_export' => true,
					'show_in_rest' => true,
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_project_slug', 'projects'),
						'with_front' => false,
					),
					'has_archive' => apply_filters('felan_project_has_archive', 'projects'),
					'show_in_admin_bar' => true,
					'menu_position' => 12,
				);

				$post_types['project-proposal'] = array(
					'labels' => array(
						'name' => esc_html__('Projects Proposal', 'felan-framework'),
						'singular_name' => esc_html__('Projects Proposal', 'felan-framework'),
						'all_items' => esc_html__('Projects Proposal', 'felan-framework'),
					),
					'menu_icon' => 'dashicons-universal-access-alt',
					'capabilities' => $this->get_proposal_capabilities(),
					'map_meta_cap' => true,
					'supports' => array('title'),
					'rewrite' => array(
						'slug' => apply_filters('felan_proposal_slug', 'project-proposal'),
					),
					'show_in_admin_bar' => true,
					'menu_position' => 13,
				);

				$post_types['project_order'] = array(
					'labels' => array(
						'name' => esc_html__('Project Order', 'felan-framework'),
						'singular_name' => esc_html__('Project Order', 'felan-framework'),
						'all_items' => esc_html__('Project Order', 'felan-framework'),
					),
					'supports' => array('title'),
					'menu_icon' => 'dashicons-printer',
					'capabilities' => $this->get_order_capabilities(),
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_project_order_slug', 'project_order'),
					),
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 10,
				);

				$post_types['project_disputes'] = array(
					'labels' => array(
						'name' => esc_html__('Project Disputes', 'felan-framework'),
						'singular_name' => esc_html__('Project Disputes', 'felan-framework'),
						'all_items' => esc_html__('Project Disputes', 'felan-framework'),
					),
					'supports' => array('title', 'excerpt'),
					'menu_icon' => 'dashicons-calendar-alt',
					'capabilities' => $this->get_meetings_capabilities(),
					'map_meta_cap' => true,
					'rewrite' => array(
						'slug' => apply_filters('felan_project_disputes_slug', 'project_disputes'),
					),
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 21,
				);
			}

			return $post_types;
		}

		/**
		 * Register post status
		 */
		public function register_post_status()
		{
			register_post_status('expired', array(
				'label' => _x('Expired', 'post status', 'felan-framework'),
				'public' => true,
				'protected' => true,
				'exclude_from_search' => true,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				'label_count' => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'felan-framework'),
			));

			register_post_status('pause', array(
				'label' => _x('Pause', 'post status', 'felan-framework'),
				'public' => true,
				'protected' => true,
				'exclude_from_search' => true,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				'label_count' => _n_noop('Pause <span class="count">(%s)</span>', 'Pause <span class="count">(%s)</span>', 'felan-framework'),
			));

			register_post_status('canceled', array(
				'label' => _x('Canceled', 'post status', 'felan-framework'),
				'public' => true,
				'protected' => true,
				'exclude_from_search' => true,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				'label_count' => _n_noop('Canceled <span class="count">(%s)</span>', 'Canceled <span class="count">(%s)</span>', 'felan-framework'),
			));

			register_post_status('hidden', array(
				'label' => _x('Hidden', 'post status', 'felan-framework'),
				'public' => true,
				'protected' => true,
				'exclude_from_search' => true,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				'label_count' => _n_noop('Hidden <span class="count">(%s)</span>', 'Hidden <span class="count">(%s)</span>', 'felan-framework'),
			));
		}

		/**
		 * Get invoice capabilities
		 * @return mixed
		 */
		private function get_invoice_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_invoices',
				'delete_posts' => 'delete_invoices'
			);
			return apply_filters('get_invoice_capabilities', $caps);
		}

		/**
		 * Get order capabilities
		 * @return mixed
		 */
		private function get_order_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_order',
				'delete_posts' => true,
			);
			return apply_filters('get_order_capabilities', $caps);
		}

		/**
		 * Get order capabilities
		 * @return mixed
		 */
		private function get_freelancer_withdraw_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_order',
				'delete_posts' => true,
			);
			return apply_filters('get_order_capabilities', $caps);
		}

		/**
		 * Get applicants capabilities
		 * @return mixed
		 */
		private function get_applicants_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_applicants',
				'delete_posts' => true,
			);
			return apply_filters('get_applicants_capabilities', $caps);
		}

		/**
		 * Get applicants capabilities
		 * @return mixed
		 */
		private function get_proposal_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_proposal',
				'delete_posts' => true,
			);
			return apply_filters('get_proposal_capabilities', $caps);
		}

		/**
		 * Get meetings capabilities
		 * @return mixed
		 */
		private function get_meetings_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_meetings',
				'delete_posts' => true,
			);
			return apply_filters('get_meetings_capabilities', $caps);
		}

		/**
		 * Get user_package capabilities
		 * @return mixed
		 */
		private function get_user_package_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_user_packages',
				'delete_posts' => 'do_not_allow'
			);
			return apply_filters('get_user_package_capabilities', $caps);
		}

		/**
		 * Get freelancer capabilities
		 * @return mixed
		 */
		private function get_freelancer_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_freelancer',
				'delete_post' => 'delete_freelancer',
			);
			return apply_filters('get_freelancer_capabilities', $caps);
		}

		/**
		 * Register taxonomy
		 * @param $taxonomies
		 * @return mixed
		 */
		public function register_taxonomy($taxonomies)
		{
			// Location
			$post_types = ['jobs', 'company', 'service', 'project', 'freelancer'];

			register_taxonomy('felan_location', $post_types, [
				'label'        => __('City / Town', 'felan-framework'),
				'rewrite'      => false,
				'hierarchical' => true,
				'public'       => true,
				'show_ui'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
			]);

			if (felan_get_option('enable_option_state') === '1') {
				register_taxonomy('felan_state', $post_types, [
					'label'        => __('Province / State', 'felan-framework'),
					'rewrite'      => false,
					'hierarchical' => true,
					'public'       => true,
					'show_ui'      => true,
					'show_admin_column' => true,
					'show_in_rest'      => true,
				]);
			}

			// Freelancer taxonomy
			$taxonomies['freelancer_categories'] = array(
				'post_type' => 'freelancer',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Categories', 'felan-framework'),
				'singular_name' => esc_html__('Freelancer Category', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_categories_slug', 'freelancer_categories'),
				),
			);
			$taxonomies['freelancer_ages'] = array(
				'post_type' => 'freelancer',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Ages', 'felan-framework'),
				'singular_name' => esc_html__('Freelancer Age', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_ages_slug', 'freelancer_ages'),
				),
			);

			$taxonomies['freelancer_languages'] = array(
				'post_type' => 'freelancer',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Languages', 'felan-framework'),
				'singular_name' => esc_html__('Freelancer Language', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_languages_slug', 'freelancer_languages'),
				),
			);
			$taxonomies['freelancer_qualification'] = array(
				'post_type' => 'freelancer',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Qualification', 'felan-framework'),
				'singular_name' => esc_html__('Freelancer Qualification', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_qualification_slug', 'freelancer_qualification'),
				),
			);

			$taxonomies['freelancer_yoe'] = array(
				'post_type' => 'freelancer',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Years of Experience', 'felan-framework'),
				'singular_name' => esc_html__('Freelancer Years of Experience', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_yoe_slug', 'freelancer_yoe'),
				),
			);

			$taxonomies['freelancer_education_levels'] = array(
				'post_type' => 'freelancer',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Levels of Education', 'felan-framework'),
				'singular_name' => esc_html__('Freelancer Level of Education', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_education_levels_slug', 'freelancer_education_levels'),
				),
			);

			$taxonomies['freelancer_skills'] = array(
				'post_type' => 'freelancer',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Skills', 'felan-framework'),
				'singular_name' => esc_html__('Freelancer Skill', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_skills_slug', 'freelancer_skills'),
				),
			);

			$taxonomies['freelancer_gender'] = array(
				'post_type' => 'freelancer',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Gender', 'felan-framework'),
				'singular_name' => esc_html__('Freelancer Gender', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_freelancer_gender_slug', 'freelancer_gender'),
				),
			);

			//Jobs
			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			if ($enable_post_type_jobs == '1') {
				$taxonomies['jobs-categories'] = array(
					'post_type' => 'jobs',
					'hierarchical' => true,
					'show_in_rest' => true,
					'label' => esc_html__('Categories', 'felan-framework'),
					'singular_name' => esc_html__('Jobs Categories', 'felan-framework'),
					'rewrite' => array(
						'slug' => apply_filters('felan_jobs_categories_slug', 'jobs-categories'),
					),
				);
				$taxonomies['jobs-skills'] = array(
					'post_type' => 'jobs',
					'hierarchical' => true,
					'show_in_rest' => true,
					'label' => esc_html__('Skills', 'felan-framework'),
					'singular_name' => esc_html__('Jobs Skills', 'felan-framework'),
					'rewrite' => array(
						'slug' => apply_filters('felan_jobs_skills_slug', 'jobs-skills'),
					),
				);
				$taxonomies['jobs-type'] = array(
					'post_type' => 'jobs',
					'hierarchical' => true,
					'show_in_rest' => true,
					'label' => esc_html__('Type', 'felan-framework'),
					'singular_name' => esc_html__('Jobs Type', 'felan-framework'),
					'rewrite' => array(
						'slug' => apply_filters('felan_jobs_type_slug', 'jobs-type'),
					),
				);
				$taxonomies['jobs-career'] = array(
					'post_type' => 'jobs',
					'hierarchical' => true,
					'show_in_rest' => true,
					'label' => esc_html__('Career', 'felan-framework'),
					'singular_name' => esc_html__('Jobs Career', 'felan-framework'),
					'rewrite' => array(
						'slug' => apply_filters('felan_jobs_career_slug', 'jobs-career'),
					),
				);
				$taxonomies['jobs-experience'] = array(
					'post_type' => 'jobs',
					'hierarchical' => true,
					'show_in_rest' => true,
					'label' => esc_html__('Experience', 'jobs-framework'),
					'singular_name' => esc_html__('Jobs Experience', 'felan-framework'),
					'rewrite' => array(
						'slug' => apply_filters('felan_jobs_experience_slug', 'jobs-experience'),
					),
				);
				$taxonomies['jobs-qualification'] = array(
					'post_type' => 'jobs',
					'hierarchical' => true,
					'show_in_rest' => true,
					'label' => esc_html__('Qualification', 'felan-framework'),
					'singular_name' => esc_html__('Jobs Qualification', 'felan-framework'),
					'rewrite' => array(
						'slug' => apply_filters('felan_jobs_qualification_slug', 'jobs-qualification'),
					),
				);
				$taxonomies['jobs-gender'] = array(
					'post_type' => 'jobs',
					'hierarchical' => true,
					'show_in_rest' => true,
					'label' => esc_html__('Gender', 'felan-framework'),
					'singular_name' => esc_html__('Jobs Gender', 'felan-framework'),
					'rewrite' => array(
						'slug' => apply_filters('felan_jobs_gender_slug', 'jobs-gender'),
					),
				);
			}

			//Company
			$taxonomies['company-categories'] = array(
				'post_type' => 'company',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Categories', 'felan-framework'),
				'singular_name' => esc_html__('Company Categories', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_company_categories_slug', 'company-categories'),
				),
			);
			$taxonomies['company-size'] = array(
				'post_type' => 'company',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Size', 'felan-framework'),
				'singular_name' => esc_html__('Company Size', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_company_size_slug', 'company-size'),
				),
			);

			//Service
			$taxonomies['service-categories'] = array(
				'post_type' => 'service',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Categories', 'felan-framework'),
				'singular_name' => esc_html__('Service Categories', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_service_categories_slug', 'service-categories'),
				),
			);

			$taxonomies['service-skills'] = array(
				'post_type' => 'service',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Skills', 'felan-framework'),
				'singular_name' => esc_html__('Service Skills', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_service_skills_slug', 'service-skills'),
				),
			);
			$taxonomies['service-language'] = array(
				'post_type' => 'service',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Language', 'felan-framework'),
				'singular_name' => esc_html__('Service Language', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_service_language_slug', 'service-language'),
				),
			);


			//Project
			$taxonomies['project-categories'] = array(
				'post_type' => 'project',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Categories', 'felan-framework'),
				'singular_name' => esc_html__('Project Categories', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_project_categories_slug', 'project-categories'),
				),
			);

			$taxonomies['project-skills'] = array(
				'post_type' => 'project',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Skills', 'felan-framework'),
				'singular_name' => esc_html__('Project Skills', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_project_skills_slug', 'project-skills'),
				),
			);
			$taxonomies['project-language'] = array(
				'post_type' => 'project',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Language', 'felan-framework'),
				'singular_name' => esc_html__('Project Language', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_project_language_slug', 'project-language'),
				),
			);
			$taxonomies['project-career'] = array(
				'post_type' => 'project',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Career', 'felan-framework'),
				'singular_name' => esc_html__('Project Career', 'felan-framework'),
				'rewrite' => array(
					'slug' => apply_filters('felan_project_career_slug', 'project-career'),
				),
			);

			return $taxonomies;
		}

		/**
		 * Register meta term
		 * @param $configs
		 * @return mixed
		 */
		public function register_term_meta($configs)
		{
			$configs['jobs-experience-settings'] = apply_filters('felan_register_term_meta_jobs_experience', array(
				'name' => esc_html__('', 'felan-framework'),
				'layout' => 'horizontal',
				'taxonomy' => array('jobs-experience'),
				'fields' => array(
					array(
						'id' => 'jobs_experience_order',
						'title' => esc_html__('Number Order by', 'felan-framework'),
						'type' => 'text',
						'col' => '5',
						'pattern' => '[0-9]*',
						'default' => '',
					),
				)
			));

			$configs['company-size-settings'] = apply_filters('felan_register_term_meta_company_size', array(
				'name' => esc_html__('', 'felan-framework'),
				'layout' => 'horizontal',
				'taxonomy' => array('company-size'),
				'fields' => array(
					array(
						'id' => 'company_size_order',
						'title' => esc_html__('Number Order by', 'felan-framework'),
						'type' => 'text',
						'col' => '5',
						'pattern' => '[0-9]*',
						'default' => '',
					),
				)
			));

			$configs['canidate-experience-settings'] = apply_filters('felan_register_term_meta_freelancer_experience', array(
				'name' => esc_html__('', 'felan-framework'),
				'layout' => 'horizontal',
				'taxonomy' => array('freelancer_yoe'),
				'fields' => array(
					array(
						'id' => 'freelancer_experience_order',
						'title' => esc_html__('Number Order by', 'felan-framework'),
						'type' => 'text',
						'col' => '5',
						'pattern' => '[0-9]*',
						'default' => '',
					),
				)
			));

			if (felan_get_option('enable_option_country') === '1' && felan_get_option('enable_option_state') === '1') {
				$select_option_country = felan_get_option('select_option_country');
				$countries = felan_get_countries();
				$keys = $values = array();
				if (!empty($select_option_country)) {
					foreach ($select_option_country as $key_country => $option_country) {
						if (array_key_exists($option_country, $countries)) {
							$keys[] = $option_country;
							$values[] = $countries[$option_country];
						}
					}
					$list_country = array_combine($keys, $values);
				} else {
					$list_country = $countries;
				}

				$configs['felan-state-settings'] = apply_filters('felan_register_country_meta', array(
					'name'     => '',
					'layout' => 'horizontal',
					'taxonomy' => array('felan_state'),
					'fields' => array(
						array(
							'id'      => 'felan-state-country',
							'title'   => esc_html__('Country', 'felan-framework'),
							'type'    => 'select',
							'options' => $list_country,
						),
					)
				));
			}

			if (felan_get_option('enable_option_state') === '1') {
				$list_state = felan_get_option_taxonomy('felan_state');
				$configs['state-location'] = apply_filters('felan_register_state_meta', array(
					'name'     => '',
					'layout' => 'horizontal',
					'taxonomy' => array('felan_location'),
					'fields' => array(
						array(
							'id'      => 'felan-location-state',
							'title'   => esc_html__('Province / State', 'felan-framework'),
							'default' => '',
							'type'    => 'select',
							'options' => $list_state,
						),
					)
				));
			}

			return apply_filters('felan_register_term_meta', $configs);
		}

		/**
		 * Register meta boxes
		 * @param $configs
		 * @return mixed
		 */
		public function register_meta_boxes($configs)
		{
			$meta_prefix = FELAN_METABOX_PREFIX;
			$dec_point = felan_get_option('decimal_separator', '.');
			$format_number = '^[0-9]+([' . $dec_point . '][0-9]+)?$';

			//Custom field jobs
			$render_custom_field_jobs = felan_render_custom_field('jobs');
			$custom_field_jobs = array();
			if (count($render_custom_field_jobs) > 0) {
				$custom_field_jobs = array(
					array(
						'id' => "{$meta_prefix}custom_field_jobs_tab",
						'title' => esc_html__('Additional Fields', 'felan-framework'),
						'icon' => 'dashicons dashicons-welcome-add-page',
						'fields' => $render_custom_field_jobs
					),
				);
			}

			//Custom field company
			$render_custom_field_company = felan_render_custom_field('company');

			//Custom field project
			$render_custom_field_project = felan_render_custom_field('project');

			$render_custom_field_service = felan_render_custom_field('service');


			//Custom field freelancer
			$render_custom_field_freelancer = felan_render_custom_field('freelancer');
			$custom_field_freelancer = array();
			if (count($render_custom_field_freelancer) > 0) {
				$custom_field_freelancer = array(
					array(
						'id' => "{$meta_prefix}custom_field_freelancer_tab",
						'title' => esc_html__('Additional Fields', 'felan-framework'),
						'icon' => 'dashicons dashicons-welcome-add-page',
						'fields' => $render_custom_field_freelancer
					),
				);
			}

			$freelancer_package_service = $freelancer_package_service_featured = $freelancer_package_jobs_apply = $freelancer_package_jobs_wishlist =
				$freelancer_package_project_apply = $freelancer_package_company_follow = $freelancer_package_contact_company = $freelancer_package_info_company =
				$freelancer_package_send_message = $freelancer_package_review_and_commnent = array();


			//Package Service
			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			if ($enable_post_type_service == '1') {
				$freelancer_package_service = array(
					'type' => 'row',
					'col' => '4',
					'fields' => array(
						array(
							'id' => "{$meta_prefix}enable_package_service_unlimited",
							'title' => esc_html__('Unlimited Service', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "{$meta_prefix}freelancer_package_number_service",
							'title' => esc_html__('Number Service', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'pattern' => '[0-9]*',
							'required' => array("{$meta_prefix}enable_package_service_unlimited", '=', '0'),
						),
					)
				);
				$freelancer_package_service_featured = array(
					'type' => 'row',
					'col' => '4',
					'fields' => array(
						array(
							'id' => "{$meta_prefix}enable_package_service_featured_unlimited",
							'title' => esc_html__('Unlimited Service Featured', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "{$meta_prefix}freelancer_package_number_service_featured",
							'title' => esc_html__('Number Featured Service', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'pattern' => '[0-9]*',
							'required' => array("{$meta_prefix}enable_package_service_featured_unlimited", '=', '0'),
						),
					)
				);
			}

			//Single Service
			$services_custom_package = array();
			$package_service = felan_get_option('package_service');
			$service_id = get_the_ID();
			if (is_array($package_service) && !empty($package_service)) {
				foreach ($package_service as $key => $value) {
					//                    $service_package_title_key = FELAN_METABOX_PREFIX . 'service_package_title' . $key;
					//                    $service_package_title = get_post_meta($service_id, $service_package_title_key, true);

					$default_value = array();
					$services_custom_package[] = array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}service_package_title{$key}",
								'title' => esc_html__('Title', 'felan-framework'),
								'default' => $value['package_service_title'],
								'type' => 'text',
								'col' => '6',
							),
							array(
								'id' => "{$meta_prefix}service_package_list{$key}",
								'type' => 'checkbox_list',
								'title' => esc_html__('Package', 'felan-framework'),
								'options' =>  array(
									'basic' => esc_html__('Basic', 'felan-framework'),
									'standard' => esc_html__('Standard', 'felan-framework'),
									'premium' => esc_html__('Premium', 'felan-framework'),
								),
								'value_inline' => true,
								'default' => $default_value,
								'col' => '6',
							),
						),
					);
				}
			}

			$services_custom_package0 = isset($services_custom_package[0]) ? $services_custom_package[0] : array();
			$services_custom_package1 = isset($services_custom_package[1]) ? $services_custom_package[1] : array();
			$services_custom_package2 = isset($services_custom_package[2]) ? $services_custom_package[2] : array();
			$services_custom_package3 = isset($services_custom_package[3]) ? $services_custom_package[3] : array();
			$services_custom_package4 = isset($services_custom_package[4]) ? $services_custom_package[4] : array();
			$services_custom_package5 = isset($services_custom_package[5]) ? $services_custom_package[5] : array();
			$services_custom_package6 = isset($services_custom_package[6]) ? $services_custom_package[6] : array();
			$services_custom_package7 = isset($services_custom_package[7]) ? $services_custom_package[7] : array();
			$services_custom_package8 = isset($services_custom_package[8]) ? $services_custom_package[8] : array();

			$service_location_city =  array(
				'id' => "{$meta_prefix}service_location_city",
				'title' => esc_html__('Locations City', 'felan-framework'),
				'type' => 'select',
				'options'  => felan_get_option_city(),
				'col' => '4',
			);

			$configs['service_meta_boxes'] = apply_filters('felan_register_meta_boxes_service', array(
				'name' => esc_html__('Service Information', 'felan-framework'),
				'post_type' => array('service'),
				'section' => array_merge(
					apply_filters('felan_register_meta_boxes_service_top', array()),
					apply_filters(
						'felan_register_meta_boxes_service_main',
						array_merge(
							array(
								array(
									'id' => "{$meta_prefix}details_service_basic",
									'title' => esc_html__('Basic', 'felan-framework'),
									'icon' => 'dashicons dashicons-admin-home',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}service_featured",
											'title' => esc_html__('Mark this service as featured ?', 'felan-framework'),
											'type' => 'button_set',
											'col' => '4',
											'options' => array(
												'1' => esc_html__('Yes', 'felan-framework'),
												'0' => esc_html__('No', 'felan-framework'),
											),
											'default' => '0',
										),
									)
								),
								array(
									'id' => "{$meta_prefix}details_service_pricing",
									'title' => esc_html__('Pricing', 'felan-framework'),
									'icon' => 'dashicons dashicons-buddicons-replies',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}service_quantity",
													'title' => esc_html__('Package Quantity', 'felan-framework'),
													'type' => 'select',
													'options'  => array(
														'1' => '1',
														'2' => '2',
														'3' => '3',
													),
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}service_time",
													'title' => esc_html__('Time Type', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'hr' => esc_html__('Hour', 'felan-framework'),
														'day' => esc_html__('Day', 'felan-framework'),
														'week' => esc_html__('Week', 'felan-framework'),
														'month' => esc_html__('Month', 'felan-framework'),
													),
													'col' => '4',
													'default' => 'hr',
												),
											)
										),
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}service_basic_price",
													'title' => esc_html__('Price(Basic))', 'felan-framework'),
													'type' => 'text',
													'col' => '3',
												),
												array(
													'id' => "{$meta_prefix}service_basic_time",
													'title' => esc_html__('Delivery Time(Basic)', 'felan-framework'),
													'type' => 'text',
													'col' => '3',
												),
												array(
													'id' => "{$meta_prefix}service_basic_revisions",
													'title' => esc_html__('Revisions(Basic)', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'none' => esc_html__('None', 'felan-framework'),
														'unlimited' => esc_html__('Unlimited', 'felan-framework'),
														'custom' => esc_html__('Custom', 'felan-framework'),
													),
													'col' => '3',
													'default' => 'none',
												),
												array(
													'id' => "{$meta_prefix}service_basic_number_revisions",
													'title' => esc_html__('Number Of Revisions(Basic)', 'felan-framework'),
													'type' => 'text',
													'col' => '3',
													'required' => array("{$meta_prefix}service_basic_revisions", '=', 'custom'),
												),
												array(
													'id' => "{$meta_prefix}service_basic_des",
													'title' => esc_html__('Description (Basic)', 'felan-framework'),
													'type' => 'textarea',
													'col' => '12',
												),
											)
										),
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}service_standard_price",
													'title' => esc_html__('Price(Standard))', 'felan-framework'),
													'type' => 'text',
													'col' => '3',
													'required' => array(
														"{$meta_prefix}service_quantity",
														'!=',
														'1',
														"{$meta_prefix}service_quantity",
														'=',
														'2'
													),
												),
												array(
													'id' => "{$meta_prefix}service_standard_time",
													'title' => esc_html__('Delivery Time(Standard)', 'felan-framework'),
													'type' => 'text',
													'col' => '3',
													'required' => array(
														"{$meta_prefix}service_quantity",
														'!=',
														'1',
														"{$meta_prefix}service_quantity",
														'=',
														'2'
													),
												),
												array(
													'id' => "{$meta_prefix}service_standard_revisions",
													'title' => esc_html__('Revisions(Standard)', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'none' => esc_html__('None', 'felan-framework'),
														'unlimited' => esc_html__('Unlimited', 'felan-framework'),
														'custom' => esc_html__('Custom', 'felan-framework'),
													),
													'col' => '3',
													'default' => 'none',
													'required' => array(
														"{$meta_prefix}service_quantity",
														'!=',
														'1',
														"{$meta_prefix}service_quantity",
														'=',
														'2'
													),
												),
												array(
													'id' => "{$meta_prefix}service_standard_number_revisions",
													'title' => esc_html__('Number Of Revisions(Standard)', 'felan-framework'),
													'type' => 'text',
													'col' => '3',
													'required' => array(
														"{$meta_prefix}service_standard_revisions",
														'=',
														'custom',
														"{$meta_prefix}service_quantity",
														'!=',
														'1',
														"{$meta_prefix}service_quantity",
														'=',
														'2'
													),
												),
												array(
													'id' => "{$meta_prefix}service_standard_des",
													'title' => esc_html__('Description (Standard)', 'felan-framework'),
													'type' => 'textarea',
													'col' => '12',
													'required' => array(
														"{$meta_prefix}service_quantity",
														'!=',
														'1',
														"{$meta_prefix}service_quantity",
														'=',
														'2'
													),
												),
											)
										),
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}service_premium_price",
													'title' => esc_html__('Price(Premium))', 'felan-framework'),
													'type' => 'text',
													'col' => '3',
													'required' => array("{$meta_prefix}service_quantity", '=', '3'),
												),
												array(
													'id' => "{$meta_prefix}service_premium_time",
													'title' => esc_html__('Delivery Time(Premium)', 'felan-framework'),
													'type' => 'text',
													'col' => '3',
													'required' => array("{$meta_prefix}service_quantity", '=', '3'),
												),
												array(
													'id' => "{$meta_prefix}service_premium_revisions",
													'title' => esc_html__('Revisions(Premium)', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'none' => esc_html__('None', 'felan-framework'),
														'unlimited' => esc_html__('Unlimited', 'felan-framework'),
														'custom' => esc_html__('Custom', 'felan-framework'),
													),
													'col' => '3',
													'default' => 'none',
													'required' => array("{$meta_prefix}service_quantity", '=', '3'),
												),
												array(
													'id' => "{$meta_prefix}service_number_premium_revisions",
													'title' => esc_html__('Number Of Revisions(Premium)', 'felan-framework'),
													'type' => 'text',
													'col' => '3',
													'required' => array(
														"{$meta_prefix}service_premium_revisions",
														'=',
														'custom',
														"{$meta_prefix}service_quantity",
														'=',
														'3'
													),
												),
												array(
													'id' => "{$meta_prefix}service_premium_des",
													'title' => esc_html__('Description (Premium)', 'felan-framework'),
													'type' => 'textarea',
													'col' => '12',
													'required' => array("{$meta_prefix}service_quantity", '=', '3'),
												),
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}details_service_package_new",
									'title' => esc_html__('Package', 'felan-framework'),
									'icon' => 'dashicons dashicons-excerpt-view',
									'fields' => array(

										$services_custom_package0,
										$services_custom_package1,
										$services_custom_package2,
										$services_custom_package3,
										$services_custom_package4,
										$services_custom_package5,
										$services_custom_package6,
										$services_custom_package7,
										$services_custom_package8,

										array(
											'id' => "{$meta_prefix}service_package_new",
											'type' => 'panel',
											'title' => esc_html__('Package New', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}service_package_new_title",
															'title' => esc_html__('Title', 'felan-framework'),
															'type' => 'text',
															'col' => '6',
														),
														array(
															'id' => "{$meta_prefix}service_package_new_list",
															'type' => 'checkbox_list',
															'title' => esc_html__('Package', 'felan-framework'),
															'options' =>  array(
																'basic' => esc_html__('Basic', 'felan-framework'),
																'standard' => esc_html__('Standard', 'felan-framework'),
																'premium' => esc_html__('Premium', 'felan-framework'),
															),
															'value_inline' => true,
															'default' => array(),
															'col' => '6',
														),
													)
												)
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}details_service_addons",
									'title' => esc_html__('Add-ons', 'felan-framework'),
									'icon' => 'dashicons dashicons-carrot',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}service_tab_addon",
											'type' => 'panel',
											'title' => esc_html__('Add ons', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}service_addons_title",
															'title' => esc_html__('Title', 'felan-framework'),
															'type' => 'text',
															'col' => '4',
														),
														array(
															'id' => "{$meta_prefix}service_addons_price",
															'title' => esc_html__('Price', 'felan-framework'),
															'type' => 'text',
															'col' => '4',
														),
														array(
															'id' => "{$meta_prefix}service_addons_time",
															'title' => esc_html__('Delivery Time', 'felan-framework'),
															'type' => 'text',
															'col' => '4',
														),
													)
												)
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}details_service_faq",
									'title' => esc_html__('Faqs', 'felan-framework'),
									'icon' => 'dashicons dashicons-palmtree',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}service_tab_faq",
											'type' => 'panel',
											'title' => esc_html__('Faqs', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}service_faq_title",
															'title' => esc_html__('Title', 'felan-framework'),
															'type' => 'text',
															'col' => '12',
														),
														array(
															'id' => "{$meta_prefix}service_faq_description",
															'title' => esc_html__('Description', 'felan-framework'),
															'type' => 'textarea',
															'col' => '12',
														),
													)
												)
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}location_tab",
									'title' => esc_html__('Location', 'felan-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												$service_location_city,
												array(
													'id' => "{$meta_prefix}service_address",
													'title' => esc_html__('Maps location', 'felan-framework'),
													'desc' => esc_html__('Full Address', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}service_latitude",
													'title' => esc_html__('Latitude', 'felan-framework'),
													'desc' => esc_html__('Latitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}service_longtitude",
													'title' => esc_html__('Longtitude', 'felan-framework'),
													'desc' => esc_html__('Longtitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}service_location",
													'title' => esc_html__('Service Location at Google Map', 'felan-framework'),
													'desc' => esc_html__('Drag the google map marker to point your service location.', 'felan-framework'),
													'type' => 'map',
													'address_field' => "{$meta_prefix}service_address",
												),
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}gallery_service_tab",
									'title' => esc_html__('Gallery Images', 'felan-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}service_images",
											'title' => esc_html__('Gallery', 'felan-framework'),
											'type' => 'gallery',
										),
									)
								),
								array(
									'id' => "{$meta_prefix}video_service_tab",
									'title' => esc_html__('Video', 'felan-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}service_video_url",
											'title' => esc_html__('Video URL', 'felan-framework'),
											'desc' => esc_html__('Input only URL. YouTube, Vimeo, SWF File and MOV File', 'felan-framework'),
											'type' => 'text',
											'col' => 12,
										),
										array(
											'id' => "{$meta_prefix}service_video_image",
											'title' => esc_html__('Video Image', 'felan-framework'),
											'type' => 'gallery',
											'col' => 12,
										),
									)
								),
								array(
									'id' => "{$meta_prefix}custom_field_service_tab",
									'title' => esc_html__('Additional Fields', 'felan-framework'),
									'icon' => 'dashicons dashicons-welcome-add-page',
									'fields' => $render_custom_field_service
								)
							)
						)
					),
					apply_filters('felan_register_meta_boxes_service_bottom', array())
				),
			));

			//Order Service
			$configs['service_order_meta_boxes'] = array(
				'name' => esc_html__('Service Order Settings', 'felan-framework'),
				'post_type' => array('service_order'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}service_order_payment_status",
								'title' => esc_html__('Status', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'pending' => esc_html__('Pending', 'felan-framework'),
									'inprogress' => esc_html__('In Process', 'felan-framework'),
									'canceled' => esc_html__('Canceled', 'felan-framework'),
									'completed' => esc_html__('Completed', 'felan-framework'),
								),
								'default' => 'pending',
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}service_order_user_id",
								'title' => esc_html__('User Order id', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}service_order_author_service",
								'title' => esc_html__('Author Service', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}service_order_item_id",
								'title' => esc_html__('Package id', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}service_order_price",
								'title' => esc_html__('Price', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}service_order_date",
								'title' => esc_html__('Activate Date', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}service_order_payment_method",
								'title' => esc_html__('Payment Method', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
						)
					),
				),
			);

			//Disputes
			$configs['disputes_meta_boxes'] = array(
				'name' => esc_html__('Disputes Settings', 'felan-framework'),
				'post_type' => array('disputes'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}disputes_status",
								'title' => esc_html__('Status', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'open' => esc_html__('Open', 'felan-framework'),
									'close' => esc_html__('Close', 'felan-framework'),
									'refund' => esc_html__('Refunded', 'felan-framework'),
								),
								'default' => 'open',
							),
						)
					),
				),
			);

			//Project Disputes
			$configs['project_disputes_meta_boxes'] = array(
				'name' => esc_html__('Disputes Settings', 'felan-framework'),
				'post_type' => array('project_disputes'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}project_disputes_status",
								'title' => esc_html__('Status', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'open' => esc_html__('Open', 'felan-framework'),
									'close' => esc_html__('Close', 'felan-framework'),
									'refund' => esc_html__('Refunded', 'felan-framework'),
								),
								'default' => 'open',
							),
						)
					),
				),
			);

			//Withdrawals
			$configs['freelancer_withdraw_meta_boxes'] = array(
				'name' => esc_html__('Freelancer Withdraw Settings', 'felan-framework'),
				'post_type' => array('freelancer_withdraw'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}freelancer_withdraw_status",
								'title' => esc_html__('Status', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'pending' => esc_html__('Pending', 'felan-framework'),
									'completed' => esc_html__('Completed', 'felan-framework'),
									'canceled' => esc_html__('Canceled', 'felan-framework'),
								),
								'default' => 'pending',
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}freelancer_withdraw_user_id",
								'title' => esc_html__('User Id', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '6',
							),
							array(
								'id' => "{$meta_prefix}freelancer_withdraw_payment_method",
								'title' => esc_html__('Payment method refund', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '6',
							),
							array(
								'id' => "{$meta_prefix}freelancer_withdraw_price",
								'title' => esc_html__('Price', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '6',
							),
							array(
								'id' => "{$meta_prefix}freelancer_withdraw_total_price",
								'title' => esc_html__('Available Balance', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '6',
							),
						)
					),
				),
			);

			//Employer Withdrawals
			$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
			if ($enable_wallet_mode == '1') {
				$configs['employer_withdraw_meta_boxes'] = array(
					'name' => esc_html__('Freelancer Withdraw Settings', 'felan-framework'),
					'post_type' => array('employer_withdraw'),
					'fields' => array(
						array(
							'type' => 'row',
							'col' => '12',
							'fields' => array(
								array(
									'id' => "{$meta_prefix}employer_withdraw_status",
									'title' => esc_html__('Status', 'felan-framework'),
									'type' => 'button_set',
									'options' => array(
										'pending' => esc_html__('Pending', 'felan-framework'),
										'completed' => esc_html__('Completed', 'felan-framework'),
										'canceled' => esc_html__('Canceled', 'felan-framework'),
									),
									'default' => 'pending',
								),
							)
						),
						array(
							'type' => 'row',
							'col' => '12',
							'fields' => array(
								array(
									'id' => "{$meta_prefix}employer_withdraw_user_id",
									'title' => esc_html__('User Id', 'felan-framework'),
									'default' => '',
									'type' => 'text',
									'col' => '6',
								),
								array(
									'id' => "{$meta_prefix}employer_withdraw_payment_method",
									'title' => esc_html__('Payment method refund', 'felan-framework'),
									'default' => '',
									'type' => 'text',
									'col' => '6',
								),
								array(
									'id' => "{$meta_prefix}employer_withdraw_price",
									'title' => esc_html__('Price', 'felan-framework'),
									'default' => '',
									'type' => 'text',
									'col' => '6',
								),
								array(
									'id' => "{$meta_prefix}employer_withdraw_total_price",
									'title' => esc_html__('Available Balance', 'felan-framework'),
									'default' => '',
									'type' => 'text',
									'col' => '6',
								),
							)
						),
					),
				);
			}

			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			if (felan_get_option('enable_freelancer_package_jobs_apply') === '1' && $enable_post_type_jobs == '1') {
				$freelancer_package_jobs_apply = array(
					'type' => 'row',
					'col' => '4',
					'fields' => array(
						array(
							'id' => "{$meta_prefix}show_package_jobs_apply",
							'title' => esc_html__('Show Jobs Apply', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "{$meta_prefix}enable_package_jobs_apply_unlimited",
							'title' => esc_html__('Unlimited Jobs Apply', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
							'required' => array("{$meta_prefix}show_package_jobs_apply", '=', '1'),
						),
						array(
							'id' => "{$meta_prefix}freelancer_package_number_jobs_apply",
							'title' => esc_html__('Number Jobs Apply', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'pattern' => '[0-9]*',
							'required' => array(
								array("{$meta_prefix}show_package_jobs_apply", '=', '1'),
								array("{$meta_prefix}enable_package_jobs_apply_unlimited", '!=', '1')
							),
						),
					)
				);
			}

			if (felan_get_option('enable_freelancer_package_jobs_wishlist') === '1' && $enable_post_type_jobs == '1') {
				$freelancer_package_jobs_wishlist = array(
					'type' => 'row',
					'col' => '4',
					'fields' => array(
						array(
							'id' => "{$meta_prefix}show_package_jobs_wishlist",
							'title' => esc_html__('Show Jobs Wishlist', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "{$meta_prefix}enable_package_jobs_wishlist_unlimited",
							'title' => esc_html__('Unlimited Jobs Wishlist', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
							'required' => array(
								array("{$meta_prefix}show_package_jobs_wishlist", '=', '1'),
							),
						),
						array(
							'id' => "{$meta_prefix}freelancer_package_number_jobs_wishlist",
							'title' => esc_html__('Number Jobs Wishlist', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'pattern' => '[0-9]*',
							'required' => array(
								array("{$meta_prefix}show_package_jobs_wishlist", '=', '1'),
								array("{$meta_prefix}enable_package_jobs_wishlist_unlimited", '!=', '1')
							),
						),
					)
				);
			}

			if (felan_get_option('enable_freelancer_package_company_follow') === '1') {
				$freelancer_package_company_follow = array(
					'type' => 'row',
					'col' => '4',
					'fields' => array(
						array(
							'id' => "{$meta_prefix}show_package_company_follow",
							'title' => esc_html__('Show Company Follow', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "{$meta_prefix}enable_package_freelancer_follow_unlimited",
							'title' => esc_html__('Unlimited Company Follow', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
							'required' => array(
								array("{$meta_prefix}show_package_company_follow", '=', '1'),
							),
						),
						array(
							'id' => "{$meta_prefix}freelancer_package_number_company_follow",
							'title' => esc_html__('Number Company Follow', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'pattern' => '[0-9]*',
							'required' => array(
								array("{$meta_prefix}show_package_company_follow", '=', '1'),
								array("{$meta_prefix}enable_package_freelancer_follow_unlimited", '!=', '1')
							),
						),
					)
				);
			}

			if (felan_get_option('enable_freelancer_package_contact_company') === '1' && $enable_post_type_jobs == '1') {
				$freelancer_package_contact_company = array(
					'id' => "{$meta_prefix}show_package_contact_company",
					'title' => esc_html__('View contact company in jobs', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}

			if (felan_get_option('enable_freelancer_package_info_company') === '1') {
				$freelancer_package_info_company =  array(
					'id' => "{$meta_prefix}show_package_info_company",
					'title' => esc_html__('View info company', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}

			if (felan_get_option('enable_freelancer_package_send_message') === '1') {
				$freelancer_package_send_message =  array(
					'id' => "{$meta_prefix}show_package_send_message",
					'title' => esc_html__('Send messages', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}

			if (felan_get_option('enable_freelancer_package_review_and_commnent') === '1') {
				$freelancer_package_review_and_commnent =  array(
					'id' => "{$meta_prefix}show_package_review_and_commnent",
					'title' => esc_html__('Review and Commnet', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '',
				);
			}

			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			if (felan_get_option('enable_freelancer_package_project_apply') === '1' && $enable_post_type_project == '1') {
				$freelancer_package_project_apply = array(
					'type' => 'row',
					'col' => '4',
					'fields' => array(
						array(
							'id' => "{$meta_prefix}show_package_project_apply",
							'title' => esc_html__('Show Project Apply', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "{$meta_prefix}enable_package_project_apply_unlimited",
							'title' => esc_html__('Unlimited Project Apply', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
							'required' => array("{$meta_prefix}show_package_project_apply", '=', '1'),
						),
						array(
							'id' => "{$meta_prefix}freelancer_package_number_project_apply",
							'title' => esc_html__('Number Project Apply', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'pattern' => '[0-9]*',
							'required' => array(
								array("{$meta_prefix}show_package_project_apply", '=', '1'),
								array("{$meta_prefix}enable_package_project_apply_unlimited", '!=', '1')
							),
						),
					)
				);
			}

			//Company Package
			$company_package_download_cv_freelancer = $company_package_freelancer_follow  = $company_package_invite_freelancer = $company_package_send_message_freelancer =
				$company_package_print_freelancer = $company_package_review_freelancer = $company_package_info_freelancer = array();

			if (felan_get_option('enable_company_package_download_cv') === '1') {
				$company_package_download_cv_freelancer = array(
					'type' => 'row',
					'col' => '4',
					'fields' => array(
						array(
							'id' => "{$meta_prefix}show_package_company_download_cv",
							'title' => esc_html__('Download CV', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "{$meta_prefix}enable_package_download_cv_unlimited",
							'title' => esc_html__('Unlimited Download CV', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
							'required' => array("{$meta_prefix}show_package_company_download_cv", '=', '1'),
						),
						array(
							'id' => "{$meta_prefix}company_package_number_download_cv",
							'title' => esc_html__('Number Download CV', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'pattern' => '[0-9]*',
							'required' => array(
								array("{$meta_prefix}show_package_company_download_cv", '=', '1'),
								array("{$meta_prefix}enable_package_download_cv_unlimited", '!=', '1')
							),
						),
					)
				);
			}

			if (felan_get_option('enable_company_package_freelancer_follow') === '1') {
				$company_package_freelancer_follow = array(
					'type' => 'row',
					'col' => '4',
					'fields' => array(
						array(
							'id' => "{$meta_prefix}show_package_company_freelancer_follow",
							'title' => esc_html__('Show Freelancer Follow', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "{$meta_prefix}enable_package_freelancer_follow_unlimited",
							'title' => esc_html__('Unlimited Freelancer Follow', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
							'required' => array("{$meta_prefix}show_package_company_freelancer_follow", '=', '1'),
						),
						array(
							'id' => "{$meta_prefix}company_package_number_freelancer_follow",
							'title' => esc_html__('Number Freelancer Follow', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'pattern' => '[0-9]*',
							'required' => array(
								array("{$meta_prefix}show_package_company_freelancer_follow", '=', '1'),
								array("{$meta_prefix}enable_package_freelancer_follow_unlimited", '!=', '1')
							),
						),
					)
				);
			}


			if (felan_get_option('enable_company_package_invite') === '1') {
				$company_package_invite_freelancer =  array(
					'id' => "{$meta_prefix}show_package_company_invite",
					'title' => esc_html__('Invite Freelancers', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}

			if (felan_get_option('enable_company_package_send_message') === '1') {
				$company_package_send_message_freelancer =  array(
					'id' => "{$meta_prefix}show_package_company_send_message",
					'title' => esc_html__('Send Messages Freelancer', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}

			if (felan_get_option('enable_company_package_print') === '1') {
				$company_package_print_freelancer =  array(
					'id' => "{$meta_prefix}show_package_company_print",
					'title' => esc_html__('Print Freelancer Profile', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}

			if (felan_get_option('enable_company_package_review_and_commnent') === '1') {
				$company_package_review_freelancer =  array(
					'id' => "{$meta_prefix}show_package_company_review_and_commnent",
					'title' => esc_html__('Review And Commnent', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}

			if (felan_get_option('enable_company_package_info') === '1') {
				$company_package_info_freelancer =  array(
					'id' => "{$meta_prefix}show_package_company_info",
					'title' => esc_html__('View Freelancer Information', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}


			$freelancer_custom_social = array();
			$felan_social_fields = felan_get_option('felan_social_fields');
			if (is_array($felan_social_fields) && !empty($felan_social_fields)) {
				foreach ($felan_social_fields as $key => $value) {
					$freelancer_custom_social[] = array(
						'id' => "{$meta_prefix}freelancer_{$value['social_name']}",
						'title' => $value['social_name'],
						'type' => 'text',
						'col' => '6',
					);
				}
			}

			$freelancer_custom_social0 = isset($freelancer_custom_social[0]) ? $freelancer_custom_social[0] : array();
			$freelancer_custom_social1 = isset($freelancer_custom_social[1]) ? $freelancer_custom_social[1] : array();
			$freelancer_custom_social2 = isset($freelancer_custom_social[2]) ? $freelancer_custom_social[2] : array();
			$freelancer_custom_social3 = isset($freelancer_custom_social[3]) ? $freelancer_custom_social[3] : array();

			$jobs_location_city =  array(
				'id' => "{$meta_prefix}jobs_location_city",
				'title' => esc_html__('Locations City', 'felan-framework'),
				'type' => 'select',
				'options'  => felan_get_option_city(),
				'col' => '4',
			);
			$company_location_city =  array(
				'id' => "{$meta_prefix}company_location_city",
				'title' => esc_html__('Locations City', 'felan-framework'),
				'type' => 'select',
				'options'  => felan_get_option_city(),
				'col' => '4',
			);
			$freelancer_location_city =  array(
				'id' => "{$meta_prefix}freelancer_location_city",
				'title' => esc_html__('Locations City', 'felan-framework'),
				'type' => 'select',
				'options'  => felan_get_option_city(),
				'col' => '4',
			);
			$project_location_city =  array(
				'id' => "{$meta_prefix}project_location_city",
				'title' => esc_html__('Locations City', 'felan-framework'),
				'type' => 'select',
				'options'  => felan_get_option_city(),
				'col' => '4',
			);

			$configs['jobs_meta_boxes'] = apply_filters('felan_register_meta_boxes_jobs', array(
				'name' => esc_html__('Jobs Information', 'felan-framework'),
				'post_type' => array('jobs'),
				'section' => array_merge(
					apply_filters('felan_register_meta_boxes_jobs_top', array()),
					apply_filters(
						'felan_register_meta_boxes_jobs_main',
						array_merge(
							array(
								array(
									'id' => "{$meta_prefix}details_tab",
									'title' => esc_html__('Basic Infomation', 'felan-framework'),
									'icon' => 'dashicons-admin-home',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}enable_jobs_package_expires",
													'type' => 'button_set',
													'title' => esc_html__('Enable package expires', 'felan-framework'),
													'desc' => esc_html__('Turn on when you want package to expire', 'felan-framework'),
													'options' => array(
														'1' => esc_html__('On', 'felan-framework'),
														'0' => esc_html__('Off', 'felan-framework'),
													),
													'col' => '4',
													'default' => '0'
												),

												array(
													'id' => "{$meta_prefix}enable_jobs_expires",
													'type' => 'button_set',
													'title' => esc_html__('Enable jobs expires', 'felan-framework'),
													'desc' => esc_html__('Turn on when you want jobs to expire', 'felan-framework'),
													'options' => array(
														'1' => esc_html__('On', 'felan-framework'),
														'0' => esc_html__('Off', 'felan-framework'),
													),
													'col' => '4',
													'default' => '0'
												),
												array(
													'id' => "{$meta_prefix}jobs_days_closing",
													'title' => esc_html__('Number of days to apply', 'felan-framework'),
													'desc' => esc_html__('Enter the number of days to apply for jobs', 'felan-framework'),
													'default' => '',
													'type' => 'text',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}enable_jobs_expires", '=', '0'),
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_featured",
													'title' => esc_html__('Mark this jobs as featured ?', 'felan-framework'),
													'type' => 'button_set',
													'col' => '4',
													'options' => array(
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'default' => '0',
												),
												array(
													'id' => "{$meta_prefix}jobs_quantity",
													'title' => esc_html__('Quantity to be recruited ', 'felan-framework'),
													'type' => 'select',
													'desc' => esc_html__('Select quantity', 'felan-framework'),
													'options' => array(
														'' => 'None',
														'1' => '1',
														'2' => '2',
														'3' => '3',
														'4' => '4',
														'5' => '5',
														'6' => '6',
														'7' => '7',
														'8' => '8',
														'9' => '9',
														'10' => '10+',
													),
													'col' => '4',
													'default' => 'quantity1',
												),
											)
										),
									)
								),
							),
							array(
								array(
									'id' => "{$meta_prefix}details_salary",
									'title' => esc_html__('Salary', 'felan-framework'),
									'icon' => 'dashicons dashicons-money-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}jobs_salary_show",
													'title' => esc_html__('Show pay by', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'range' => 'Range',
														'starting_amount' => 'Starting amount',
														'maximum_amount' => 'Maximum amount',
														'agree' => 'Negotiable Price',
													),
													'col' => '4',
													'default' => 'range',
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_rate",
													'title' => esc_html__('Rate', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html('None', 'felan-framework'),
														'hour' => esc_html('Per Hour', 'felan-framework'),
														'day' => esc_html('Per Day', 'felan-framework'),
														'week' => esc_html('Per Week', 'felan-framework'),
														'month' => esc_html('Per Month', 'felan-framework'),
														'year' => esc_html('Per Year', 'felan-framework'),
													),
													'col' => '4',
													'default' => 'hour',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '!=', 'agree')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_currency_type",
													'title' => esc_html__('Currency Type', 'felan-framework'),
													'type' => 'select',
													'options' => felan_get_select_currency_type(),
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_minimum",
													'title' => esc_html__('Minimum', 'felan-framework'),
													'desc' => esc_html__('Example Value: 450', 'felan-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '450',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '=', 'range')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_maximum",
													'title' => esc_html__('Maximum', 'felan-framework'),
													'desc' => esc_html__('Example Value: 900', 'felan-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '900',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '=', 'range')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_maximum_price",
													'title' => esc_html__('Maximum Price', 'felan-framework'),
													'desc' => esc_html__('Example Value: 1000', 'felan-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '=', 'maximum_amount')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_minimum_price",
													'title' => esc_html__('Minimum Price', 'felan-framework'),
													'desc' => esc_html__('Example Value: 100', 'felan-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '=', 'starting_amount')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_convert_min",
													'title' => esc_html__('Convert Min', 'felan-framework'),
													'type' => 'text',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_convert_max",
													'title' => esc_html__('Convert Max', 'felan-framework'),
													'type' => 'text',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}jobs_price_convert_min",
													'title' => esc_html__('Convert Min', 'felan-framework'),
													'type' => 'text',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}jobs_price_convert_max",
													'title' => esc_html__('Convert Max', 'felan-framework'),
													'type' => 'text',
													'col' => '4',
												),
											)
										),
									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}jobs_apply",
									'title' => esc_html__('Apply', 'felan-framework'),
									'icon' => 'dashicons-email',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}jobs_select_apply",
													'title' => esc_html__('Select Type', 'felan-framework'),
													'type' => 'select',
													'col' => 6,
													'options' => apply_filters(
														'felan_fields_select_apply_jobs',
														array(
															'email' => esc_html__('By email', 'felan-framework'),
															'external' => esc_html__('External Apply', 'felan-framework'),
															'internal' => esc_html__('Internal Apply', 'felan-framework'),
															'call-to' => esc_html__('Call To Apply', 'felan-framework'),
														)
													)
												),
												array(
													'id' => "{$meta_prefix}jobs_apply_email",
													'title' => esc_html__('Job apply email', 'felan-framework'),
													'type' => 'text',
													'col' => 6,
													'required' => array(
														array("{$meta_prefix}jobs_select_apply", '=', 'email'),
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_apply_external",
													'title' => esc_html__('Job apply external', 'felan-framework'),
													'type' => 'text',
													'col' => 6,
													'required' => array(
														array("{$meta_prefix}jobs_select_apply", '=', 'external'),
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_apply_call_to",
													'title' => esc_html__('Job Call To Apply', 'felan-framework'),
													'type' => 'text',
													'col' => 6,
													'required' => array(
														array("{$meta_prefix}jobs_select_apply", '=', 'call-to'),
													),
												),
											)
										)
									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}jobs_company",
									'title' => esc_html__('Company', 'felan-framework'),
									'icon' => 'dashicons dashicons-building',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}jobs_select_company",
											'title' => esc_html__('Select company', 'felan-framework'),
											'type' => 'select',
											'options' => felan_select_post_company(),
										),
									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}location_tab",
									'title' => esc_html__('Location', 'felan-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												$jobs_location_city,
												array(
													'id' => "{$meta_prefix}jobs_address",
													'title' => esc_html__('Maps location', 'felan-framework'),
													'desc' => esc_html__('Address Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}jobs_latitude",
													'title' => esc_html__('Latitude', 'felan-framework'),
													'desc' => esc_html__('Latitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}jobs_longtitude",
													'title' => esc_html__('Longtitude', 'felan-framework'),
													'desc' => esc_html__('Longtitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}jobs_location",
													'title' => esc_html__('Jobs Location at Google Map', 'felan-framework'),
													'desc' => esc_html__('Drag the google map marker to point your jobs location. You can also use the address field above to search for your jobs', 'felan-framework'),
													'type' => 'map',
													'address_field' => "{$meta_prefix}jobs_address",
												),
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}gallery_tab",
									'title' => esc_html__('Gallery Images', 'felan-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}jobs_images",
											'title' => esc_html__('Gallery Images', 'felan-framework'),
											'type' => 'gallery',
										),
									)
								),
								array(
									'id' => "{$meta_prefix}video_tab",
									'title' => esc_html__('Video', 'felan-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}jobs_video_url",
											'title' => esc_html__('Video URL', 'felan-framework'),
											'desc' => esc_html__('Input only URL. YouTube, Vimeo, SWF File and MOV File', 'felan-framework'),
											'type' => 'text',
											'col' => 12,
										),
										array(
											'id' => "{$meta_prefix}jobs_video_image",
											'title' => esc_html__('Video Image', 'felan-framework'),
											'type' => 'gallery',
											'col' => 12,
										),
									)
								),
							),
							$custom_field_jobs
						)
					),
					apply_filters('felan_register_meta_boxes_jobs_bottom', array())
				),
			));

			$configs['company_meta_boxes'] = apply_filters('felan_register_meta_boxes_company', array(
				'name' => esc_html__('Company Information', 'felan-framework'),
				'post_type' => array('company'),
				'section' => array_merge(
					apply_filters('felan_register_meta_boxes_company_top', array()),
					apply_filters(
						'felan_register_meta_boxes_company_main',
						array_merge(
							array(
								array(
									'id' => "{$meta_prefix}details_company_tab",
									'title' => esc_html__('Basic Infomation', 'felan-framework'),
									'icon' => 'dashicons-admin-home',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}company_green_tick",
													'type' => 'button_set',
													'title' => esc_html__('Enable Green Tick', 'felan-framework'),
													'subtitle' => esc_html__('Enable/Disable Green Tick', 'felan-framework'),
													'options' => array(
														'1' => esc_html__('On', 'felan-framework'),
														'0' => esc_html__('Off', 'felan-framework'),
													),
													'default' => '0',
												),
												array(
													'id' => "{$meta_prefix}company_website",
													'title' => esc_html__(' Website ', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_phone",
													'title' => esc_html__('Phone number', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_email",
													'title' => esc_html__('Email', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_founded",
													'title' => esc_html__('Founded In', 'felan-framework'),
													'type' => 'select',
													'options' => felan_get_company_founded(false),
													'col' => '6',
												),
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}details_company_social",
									'title' => esc_html__('Social Network', 'felan-framework'),
									'icon' => 'dashicons dashicons-networking',
									'fields' => array(
										array(
											'type' => 'row',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}company_twitter",
													'title' => esc_html__('Twitter', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_linkedin",
													'title' => esc_html__('Linkedin', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),

												array(
													'id' => "{$meta_prefix}company_facebook",
													'title' => esc_html__('Facebook', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_instagram",
													'title' => esc_html__('Instagram', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),
											)
										),
										array(
											'type' => 'divide'
										),
										array(
											'id' => "{$meta_prefix}company_social_tabs",
											'type' => 'panel',
											'title' => esc_html__('Social Network', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}company_social_name",
															'type' => 'select',
															'options' => felan_get_repeater_social(''),
															'col' => '6',
															'title' => esc_html__('Name', 'felan-framework'),
														),
														array(
															'id' => "{$meta_prefix}company_social_url",
															'type' => 'text',
															'col' => '6',
															'title' => esc_html__('Url', 'felan-framework'),
														),
													)
												)
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}company_logo_tab",
									'title' => esc_html__('Logo', 'felan-framework'),
									'icon' => 'dashicons dashicons-format-image',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}company_logo",
													'title' => esc_html__('Logo', 'felan-framework'),
													'type' => 'image',
												),
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}location_tab",
									'title' => esc_html__('Location', 'felan-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												$company_location_city,
												array(
													'id' => "{$meta_prefix}company_address",
													'title' => esc_html__('Maps location', 'felan-framework'),
													'desc' => esc_html__('Full Address', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}company_latitude",
													'title' => esc_html__('Latitude', 'felan-framework'),
													'desc' => esc_html__('Latitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}company_longtitude",
													'title' => esc_html__('Longtitude', 'felan-framework'),
													'desc' => esc_html__('Longtitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}company_location",
													'title' => esc_html__('Company Location at Google Map', 'felan-framework'),
													'desc' => esc_html__('Drag the google map marker to point your company location. You can also use the address field above to search for your company', 'felan-framework'),
													'type' => 'map',
													'address_field' => "{$meta_prefix}company_address",
												),
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}gallery_company_tab",
									'title' => esc_html__('Gallery Images', 'felan-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}company_images",
											'title' => esc_html__('Felan Gallery Images', 'felan-framework'),
											'type' => 'gallery',
										),
									)
								),
								array(
									'id' => "{$meta_prefix}video_company_tab",
									'title' => esc_html__('Video', 'felan-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}company_video_url",
											'title' => esc_html__('Video URL', 'felan-framework'),
											'desc' => esc_html__('Input only URL. YouTube, Vimeo, SWF File and MOV File', 'felan-framework'),
											'type' => 'text',
											'col' => 12,
										),
										array(
											'id' => "{$meta_prefix}company_video_image",
											'title' => esc_html__('Video Image', 'felan-framework'),
											'type' => 'gallery',
											'col' => 12,
										),
									)
								),
								array(
									'id' => "{$meta_prefix}custom_field_company_tab",
									'title' => esc_html__('Additional Fields', 'felan-framework'),
									'icon' => 'dashicons dashicons-welcome-add-page',
									'fields' => $render_custom_field_company,
								)
							)
						)
					),
					apply_filters('felan_register_meta_boxes_company_bottom', array())
				),
			));

			$configs['freelancer_meta_boxes'] = apply_filters('felan_register_meta_boxes_freelancer', array(
				'name' => esc_html__('Freelancer Information', 'felan-framework'),
				'post_type' => array('freelancer'),
				'section' => array_merge(
					apply_filters('jobi_register_meta_boxes_freelancer_top', array()),
					apply_filters(
						'jobi_register_meta_boxes_freelancer_main',
						array_merge(
							array(
								array(
									'id' => "{$meta_prefix}details_tab",
									'title' => esc_html__('Basic Infomation', 'felan-framework'),
									'icon' => 'dashicons-admin-home',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}freelancer_first_name",
													'title' => esc_html__('First Name', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}freelancer_last_name",
													'title' => esc_html__('Last Name', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}freelancer_email",
													'title' => esc_html__('Email Address', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}freelancer_phone",
													'title' => esc_html__('Phone Number', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}freelancer_current_position",
													'title' => esc_html__('Current Position', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}freelancer_dob",
													'title' => esc_html__('Date Of Birth', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'pattern' => '(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))',
													'placeholder' => 'yyyy-mm-dd',
													'maxlength' => '10',
													'col' => '6',
												),
											)
										),


										array(
											'type' => 'divide'
										),

										array(
											'type' => 'row',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}freelancer_offer_salary",
													'title' => esc_html__('Offered Salary', 'felan-framework'),
													'type' => 'text',
													'pattern' => '^[0-9]+([.][0-9]+)?$',
													'default' => '',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}freelancer_salary_type",
													'title' => esc_html__('Salary Type', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('None', 'felan-framework'),
														'hr' => esc_html__('Hourly', 'felan-framework'),
														'day' => esc_html__('Daily', 'felan-framework'),
														'month' => esc_html__('Monthly', 'felan-framework'),
														'year' => esc_html__('Yearly', 'felan-framework'),
													),
													'col' => '4',
													'default' => 'hr',
												),
												array(
													'id' => "{$meta_prefix}freelancer_currency_type",
													'title' => esc_html__('Currency Type', 'felan-framework'),
													'type' => 'select',
													'options' => felan_get_select_currency_type(),
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}freelancer_featured",
													'title' => esc_html__('Mark this freelancer as featured ?', 'felan-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'default' => '0',
												),
											)
										)
									)
								)
							),

							array(
								array(
									'id' => "{$meta_prefix}details_freelancer_social",
									'title' => esc_html__('Social Network', 'felan-framework'),
									'icon' => 'dashicons dashicons-networking',
									'fields' => array(
										array(
											'type' => 'row',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}freelancer_twitter",
													'title' => esc_html__('Twitter', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}freelancer_linkedin",
													'title' => esc_html__('Linkedin', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),

												array(
													'id' => "{$meta_prefix}freelancer_facebook",
													'title' => esc_html__('Facebook', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}freelancer_instagram",
													'title' => esc_html__('Instagram', 'felan-framework'),
													'type' => 'text',
													'col' => '6',
												),

												$freelancer_custom_social0,
												$freelancer_custom_social1,
												$freelancer_custom_social2,
												$freelancer_custom_social3,
											)
										),
										array(
											'type' => 'divide'
										),
										array(
											'id' => "{$meta_prefix}freelancer_social_tabs",
											'type' => 'panel',
											'title' => esc_html__('Social Network', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}freelancer_social_name",
															'type' => 'select',
															'options' => felan_get_repeater_social(''),
															'col' => '6',
															'title' => esc_html__('Name', 'felan-framework'),
														),
														array(
															'id' => "{$meta_prefix}freelancer_social_url",
															'type' => 'text',
															'col' => '6',
															'title' => esc_html__('Url', 'felan-framework'),
														),
													)
												)
											)
										),
									)
								)
							),

							array(
								array(
									'id' => "{$meta_prefix}freelancer_resume_tab",
									'title' => esc_html__('My Resume', 'felan-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}freelancer_resume_id_list",
											'title' => esc_html__('Freelancer Resume', 'felan-framework'),
											'type' => 'file',
										),
									)
								),
								array(
									'id' => "{$meta_prefix}freelancer_location_tab",
									'title' => esc_html__('Location', 'felan-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												$freelancer_location_city,
												array(
													'id' => "{$meta_prefix}freelancer_address",
													'title' => esc_html__('Maps location', 'felan-framework'),
													'desc' => esc_html__('Address Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}freelancer_latitude",
													'title' => esc_html__('Latitude', 'felan-framework'),
													'desc' => esc_html__('Latitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}freelancer_longtitude",
													'title' => esc_html__('Longtitude', 'felan-framework'),
													'desc' => esc_html__('Longtitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}freelancer_location",
													'title' => esc_html__('Location at Google Map', 'felan-framework'),
													'desc' => esc_html__('Drag the google map marker to point your freelancer location. You can also use the address field above to search for your freelancer', 'felan-framework'),
													'type' => 'map',
													'address_field' => "{$meta_prefix}freelancer_address",
												),
											)
										)

									)
								),
								array(
									'id' => "{$meta_prefix}freelancer_education_tabs",
									'title' => esc_html__('Education', 'felan-framework'),
									'icon' => 'dashicons-editor-ul',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}freelancer_education_list",
											'type' => 'panel',
											'title' => esc_html__('Education', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}freelancer_education_title",
															'title' => esc_html__('Title', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_education_level",
															'title' => esc_html__('Level of Education', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_education_from",
															'title' => esc_html__('From', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_education_to",
															'title' => esc_html__('To', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_education_description",
															'title' => esc_html__('Description', 'felan-framework'),
															'type' => 'textarea',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}freelancer_experience_tab",
									'title' => esc_html__('Experiencies', 'felan-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}freelancer_experience_list",
											'type' => 'panel',
											'title' => esc_html__('Work Experience', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}freelancer_experience_job",
															'title' => esc_html__('Job Title', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_experience_company",
															'title' => esc_html__('Company', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_experience_from",
															'title' => esc_html__('From', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_experience_to",
															'title' => esc_html__('To', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_experience_description",
															'title' => esc_html__('Description', 'felan-framework'),
															'type' => 'textarea',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}freelancer_project_tab",
									'title' => esc_html__('Portfolio', 'felan-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}freelancer_project_list",
											'type' => 'panel',
											'title' => esc_html__('Portfolio', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}freelancer_project_image_id",
															'title' => esc_html__('A screenshot of Portfolio', 'felan-framework'),
															'type' => 'image',
															'default' => '',
															'col' => '12'
														),
														array(
															'id' => "{$meta_prefix}freelancer_project_title",
															'title' => esc_html__('Portfolio Title', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_project_link",
															'title' => esc_html__('Link', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_project_description",
															'title' => esc_html__('Description', 'felan-framework'),
															'type' => 'textarea',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}freelancer_award_tab",
									'title' => esc_html__('Awards', 'felan-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}freelancer_award_list",
											'type' => 'panel',
											'title' => esc_html__('Awards', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}freelancer_award_title",
															'title' => esc_html__('Title', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_award_date",
															'title' => esc_html__('Date Awarded', 'felan-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}freelancer_award_description",
															'title' => esc_html__('Description', 'felan-framework'),
															'type' => 'textarea',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}freelancer_video_gallery_tab",
									'title' => esc_html__('Video and Gallery', 'felan-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}freelancer_galleries",
											'title' => esc_html__('Gallery', 'felan-framework'),
											'type' => 'gallery',
											'default' => '',
											'col' => '12'
										),
										array(
											'id' => "{$meta_prefix}freelancer_video_url",
											'title' => esc_html__('Video URL', 'felan-framework'),
											'type' => 'text',
											'default' => '',
											'col' => '6'
										),
									)
								),
							),
							$custom_field_freelancer
						)
					),
					apply_filters('jobi_register_meta_boxes_freelancer_bottom', array())
				),
			));


			$package_unlimited_job = $package_number_job = $package_unlimited_job_featured = $package_number_featured
				= $package_unlimited_project = $package_number_project = $package_unlimited_project_featured = $package_number_project_featured = array();

			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			if ($enable_post_type_jobs == '1') {
				$package_unlimited_job = array(
					'id' => "{$meta_prefix}package_unlimited_job",
					'title' => esc_html__('Unlimited Job', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);

				$package_number_job = array(
					'id' => "{$meta_prefix}package_number_job",
					'title' => esc_html__('Number Listings', 'felan-framework'),
					'type' => 'text',
					'default' => '',
					'pattern' => '[0-9]*',
					'required' => array("{$meta_prefix}package_unlimited_job", '=', '0'),
				);
				$package_unlimited_job_featured = array(
					'id' => "{$meta_prefix}package_unlimited_job_featured",
					'title' => esc_html__('Unlimited Job Featured', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
				$package_number_featured = array(
					'id' => "{$meta_prefix}package_number_featured",
					'title' => esc_html__('Number Featured Listings', 'felan-framework'),
					'type' => 'text',
					'default' => '',
					'pattern' => '[0-9]*',
					'required' => array("{$meta_prefix}package_unlimited_job_featured", '=', '0'),
				);
			}

			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			if ($enable_post_type_project == '1') {
				$package_unlimited_project = array(
					'id' => "{$meta_prefix}package_unlimited_project",
					'title' => esc_html__('Unlimited project', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);

				$package_number_project = array(
					'id' => "{$meta_prefix}package_number_project",
					'title' => esc_html__('Number Listings', 'felan-framework'),
					'type' => 'text',
					'default' => '',
					'pattern' => '[0-9]*',
					'required' => array("{$meta_prefix}package_unlimited_project", '=', '0'),
				);

				$package_unlimited_project_featured = array(
					'id' => "{$meta_prefix}package_unlimited_project_featured",
					'title' => esc_html__('Unlimited project Featured', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);

				$package_number_project_featured = array(
					'id' => "{$meta_prefix}package_number_project_featured",
					'title' => esc_html__('Number Featured Listings', 'felan-framework'),
					'type' => 'text',
					'default' => '',
					'pattern' => '[0-9]*',
					'required' => array("{$meta_prefix}package_unlimited_project_featured", '=', '0'),
				);
			}


			$configs['package_meta_boxes'] = array(
				'name' => esc_html__('Package Settings', 'felan-framework'),
				'post_type' => array('package'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							$package_unlimited_job,
							$package_number_job,
						)
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							$package_unlimited_job_featured,
							$package_number_featured,
						)
					),

					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							$package_unlimited_project,
							$package_number_project,
						)
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							$package_unlimited_project_featured,
							$package_number_project_featured,
						)
					),

					$company_package_freelancer_follow,
					$company_package_download_cv_freelancer,
					$result_package = apply_filters('felan_register_field_package_jobs', array()),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							$company_package_invite_freelancer,
							$company_package_send_message_freelancer,
							$company_package_print_freelancer,
							$company_package_review_freelancer,
							$company_package_info_freelancer,
						)
					),
					array(
						'type' => 'divide'
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_free",
								'title' => esc_html__('Free package', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'felan-framework'),
									'0' => esc_html__('No', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}package_price",
								'title' => esc_html__('Package Price', 'felan-framework'),
								'type' => 'text',
								'required' => array("{$meta_prefix}package_free", '=', '0'),
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_unlimited_time",
								'title' => esc_html__('Unlimited time', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'felan-framework'),
									'0' => esc_html__('No', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}package_time_unit",
								'title' => esc_html__('Time Unit', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'Day' => esc_html__('Day', 'felan-framework'),
									'Week' => esc_html__('Week', 'felan-framework'),
									'Month' => esc_html__('Month', 'felan-framework'),
									'Year' => esc_html__('Year', 'felan-framework'),
								),
								'default' => 'Day',
								'required' => array("{$meta_prefix}package_unlimited_time", '=', '0'),
							),
							array(
								'id' => "{$meta_prefix}package_period",
								'title' => esc_html__('Number Time', 'felan-framework'),
								'type' => 'text',
								'default' => '1',
								'pattern' => '[0-9]*',
								'required' => array("{$meta_prefix}package_unlimited_time", '=', '0'),
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_order_display",
								'title' => esc_html__('Order Number Display Via Frontend', 'felan-framework'),
								'type' => 'text',
								'default' => '1',
								'pattern' => '[0-9]*',
							),
							array(
								'id' => "{$meta_prefix}package_featured",
								'title' => esc_html__('Is Featured?', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'felan-framework'),
									'0' => esc_html__('No', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}package_visible",
								'title' => esc_html__('Is Visible?', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'felan-framework'),
									'0' => esc_html__('No', 'felan-framework'),
								),
								'default' => '1',
							),
						)
					),
					array(
						'type' => 'divide'
					),
					array(
						'id' => "{$meta_prefix}package_additional_details",
						'type' => 'repeater',
						'title' => esc_html__('Additional details:', 'felan-framework'),
						'col' => '6',
						'sort' => true,
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_details_text",
								'type' => 'text',
								'default' => esc_html__('Limited support', 'felan-framework'),
							),
						)
					),
				),
			);

			$configs['freelancer_package_meta_boxes'] = array(
				'name' => esc_html__('Freelancer Package', 'felan-framework'),
				'post_type' => array('freelancer_package'),
				'fields' => array(
					$freelancer_package_service,
					$freelancer_package_service_featured,
					$freelancer_package_jobs_apply,
					$freelancer_package_jobs_wishlist,
					$freelancer_package_company_follow,
					$freelancer_package_project_apply,
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							$freelancer_package_contact_company,
							$freelancer_package_info_company,
							$freelancer_package_send_message,
							$freelancer_package_review_and_commnent,
						)
					),
					array(
						'type' => 'divide'
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}freelancer_package_free",
								'title' => esc_html__('Free Package', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'felan-framework'),
									'0' => esc_html__('No', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}freelancer_package_price",
								'title' => esc_html__('Package Price', 'felan-framework'),
								'type' => 'text',
								'required' => array("{$meta_prefix}freelancer_package_free", '=', '0'),
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}enable_package_service_unlimited_time",
								'title' => esc_html__('Unlimited time', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'felan-framework'),
									'0' => esc_html__('No', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}freelancer_package_time_unit",
								'title' => esc_html__('Time Unit', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'Day' => esc_html__('Day', 'felan-framework'),
									'Week' => esc_html__('Week', 'felan-framework'),
									'Month' => esc_html__('Month', 'felan-framework'),
									'Year' => esc_html__('Year', 'felan-framework'),
								),
								'default' => 'Day',
								'required' => array("{$meta_prefix}enable_package_service_unlimited_time", '=', '0'),
							),
							array(
								'id' => "{$meta_prefix}freelancer_package_period",
								'title' => esc_html__('Number Time', 'felan-framework'),
								'type' => 'text',
								'default' => '1',
								'pattern' => '[0-9]*',
								'required' => array("{$meta_prefix}enable_package_service_unlimited_time", '=', '0'),
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}freelancer_package_order_display",
								'title' => esc_html__('Order Number Display Via Frontend', 'felan-framework'),
								'type' => 'text',
								'default' => '1',
								'pattern' => '[0-9]*',
							),
							array(
								'id' => "{$meta_prefix}freelancer_package_featured",
								'title' => esc_html__('Is Featured?', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'felan-framework'),
									'0' => esc_html__('No', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}freelancer_package_visible",
								'title' => esc_html__('Is Visible?', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'felan-framework'),
									'0' => esc_html__('No', 'felan-framework'),
								),
								'default' => '1',
							),
						)
					),
					array(
						'type' => 'divide'
					),
					array(
						'id' => "{$meta_prefix}freelancer_package_additional_details",
						'type' => 'repeater',
						'title' => esc_html__('Custom Field Package', 'felan-framework'),
						'col' => '6',
						'sort' => true,
						'fields' => array(
							array(
								'id' => "{$meta_prefix}freelancer_package_details_text",
								'type' => 'text',
								'default' => esc_html__('Limited support', 'felan-framework'),
							),
						)
					),
				),
			);

			$configs['freelancer_order_meta_boxes'] = array(
				'name' => esc_html__('Service Order Settings', 'felan-framework'),
				'post_type' => array('freelancer_order'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}freelancer_order_payment_status",
								'title' => esc_html__('Status', 'felan-framework'),
								'type' => 'button_set',
								'col' => '4',
								'options' => array(
									'0' => esc_html__('Pending', 'felan-framework'),
									'1' => esc_html__('Active', 'felan-framework'),
								),
								'default' => 'pending',
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}freelancer_order_user_id",
								'title' => esc_html__('User Buyer id', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}freelancer_order_item_id",
								'title' => esc_html__('Package id', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}freelancer_order_price",
								'title' => esc_html__('Price', 'felan-framework'),
								'default' => '30',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}freelancer_order_date",
								'title' => esc_html__('Activate Date', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}freelancer_order_payment_method",
								'title' => esc_html__('Payment Method', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
						)
					),
				),
			);

			$date_applicants = get_the_date(get_option('date_format'));
			$configs['applicants_meta_boxes'] = array(
				'name' => esc_html__('Applicants Settings', 'felan-framework'),
				'post_type' => array('applicants'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '6',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}applicants_status",
								'title' => esc_html__('Status', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'pending' => esc_html__('Pending', 'felan-framework'),
									'approved' => esc_html__('Approved', 'felan-framework'),
									'rejected' => esc_html__('Rejected', 'felan-framework'),
								),
								'default' => 'pending',
							),
							array(
								'id' => "{$meta_prefix}applicants_type",
								'title' => esc_html__('Type Apply', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'email' => esc_html__('Email', 'felan-framework'),
									'internal' => esc_html__('Internal', 'felan-framework'),
								),
								'default' => 'email',
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '6',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}applicants_author",
								'title' => esc_html__('Name Apply', 'felan-framework'),
								'type' => 'text',
								'default' => '',
								'required' => array("{$meta_prefix}applicants_type", '=', 'email'),
							),
							array(
								'id' => "{$meta_prefix}applicants_phone",
								'title' => esc_html__('Phone', 'felan-framework'),
								'type' => 'text',
								'default' => '',
								'required' => array("{$meta_prefix}applicants_type", '=', 'email'),
							),
							array(
								'id' => "{$meta_prefix}applicants_email",
								'title' => esc_html__('Email Address', 'felan-framework'),
								'type' => 'text',
								'default' => '',
								'required' => array("{$meta_prefix}applicants_type", '=', 'email'),
							),
							array(
								'id' => "{$meta_prefix}applicants_cv",
								'title' => esc_html__('Cv Url', 'felan-framework'),
								'type' => 'text',
								'default' => '',
							),
						)
					),
					array(
						'id' => "{$meta_prefix}applicants_message",
						'title' => esc_html__('Message', 'felan-framework'),
						'type' => 'textarea',
						'default' => '',
					),
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}applicants_jobs_id",
								'title' => esc_html__('Jobs ID', 'felan-framework'),
								'type' => 'text',
								'col' => '6',
								'default' => '',
							),
							array(
								'id' => "{$meta_prefix}applicants_date",
								'title' => esc_html__('Post Date', 'felan-framework'),
								'type' => 'text',
								'col' => '6',
								'default' => $date_applicants,
							),
						)
					),
				),
			);

			$configs['proposal_meta_boxes'] = array(
				'name' => esc_html__('Proposal Settings', 'felan-framework'),
				'post_type' => array('project-proposal'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}proposal_status",
								'title' => esc_html__('Status', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'pending' => esc_html__('Pending', 'felan-framework'),
									'inprogress' => esc_html__('In Process', 'felan-framework'),
									'canceled' => esc_html__('Canceled', 'felan-framework'),
									'reject' => esc_html__('Rejected', 'felan-framework'),
									'completed' => esc_html__('Completed', 'felan-framework'),
								),
								'default' => 'pending',
							),
							array(
								'id' => "{$meta_prefix}proposal_price",
								'title' => esc_html__('Price', 'felan-framework'),
								'type' => 'text',
								'default' => '',
							),
							array(
								'id' => "{$meta_prefix}proposal_time",
								'title' => esc_html__('Time', 'felan-framework'),
								'type' => 'text',
								'default' => '',
							),
						)
					),
					array(
						'id' => "{$meta_prefix}proposal_message",
						'title' => esc_html__('Message', 'felan-framework'),
						'type' => 'textarea',
						'default' => '',
					),
					array(
						'type' => 'divide',
					),
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}project_refund_payment_method",
								'title' => esc_html__('Payment method refund', 'felan-framework'),
								'type' => 'select',
								'options' => array(
									'wire_transfer' => esc_html('Wire Transfer', 'felan-framework'),
									'stripe' => esc_html('Pay With Stripe', 'felan-framework'),
									'paypal' => esc_html('Pay With Paypal', 'felan-framework'),
								),
								'col' => '4',
								'default' => 'wire_transfer',
								'required' => array("{$meta_prefix}proposal_status", '=', 'refund'),

							),
							array(
								'id' => "{$meta_prefix}project_refund_content",
								'title' => esc_html__('Content refund', 'felan-framework'),
								'type' => 'textarea',
								'default' => '',
								'col' => '12',
								'required' => array("{$meta_prefix}proposal_status", '=', 'refund'),
							),
						)
					),
				),
			);

			//Order Project
			$configs['project_order_meta_boxes'] = array(
				'name' => esc_html__('Service Order Settings', 'felan-framework'),
				'post_type' => array('project_order'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}project_order_payment_status",
								'title' => esc_html__('Status', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'pending' => esc_html__('Pending', 'felan-framework'),
									'inprogress' => esc_html__('Approve', 'felan-framework'),
								),
								'default' => 'pending',
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}project_order_user_id",
								'title' => esc_html__('User Order id', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}project_order_author_project",
								'title' => esc_html__('Author Service', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}project_order_item_id",
								'title' => esc_html__('Package id', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}project_order_price",
								'title' => esc_html__('Price', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}project_order_date",
								'title' => esc_html__('Activate Date', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}project_order_payment_method",
								'title' => esc_html__('Payment Method', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
						)
					),
					array(
						'type' => 'divide',
					),
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}project_refund_payment_method",
								'title' => esc_html__('Payment method refund', 'felan-framework'),
								'type' => 'select',
								'options' => array(
									'wire_transfer' => esc_html('Wire Transfer', 'felan-framework'),
									'stripe' => esc_html('Pay With Stripe', 'felan-framework'),
									'paypal' => esc_html('Pay With Paypal', 'felan-framework'),
								),
								'col' => '4',
								'default' => 'wire_transfer',
								'required' => array("{$meta_prefix}project_order_payment_status", '=', 'refund'),

							),
							array(
								'id' => "{$meta_prefix}project_refund_content",
								'title' => esc_html__('Content refund', 'felan-framework'),
								'type' => 'textarea',
								'default' => '',
								'col' => '12',
								'required' => array("{$meta_prefix}project_order_payment_status", '=', 'refund'),
							),
						)
					),
				),
			);

			//Order Wallet
			$configs['wallet_order_meta_boxes'] = array(
				'name' => esc_html__('Wallet Order Settings', 'felan-framework'),
				'post_type' => array('wallet_order'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}wallet_order_payment_status",
								'title' => esc_html__('Status', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'pending' => esc_html__('Pending', 'felan-framework'),
									'approve' => esc_html__('Approve', 'felan-framework'),
								),
								'default' => 'pending',
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}wallet_order_user_id",
								'title' => esc_html__('User Order id', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}wallet_order_author",
								'title' => esc_html__('Author Wallet', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}wallet_order_user_role",
								'title' => esc_html__('User Role', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}wallet_order_price",
								'title' => esc_html__('Price', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}wallet_order_date",
								'title' => esc_html__('Activate Date', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
							array(
								'id' => "{$meta_prefix}wallet_order_payment_method",
								'title' => esc_html__('Payment Method', 'felan-framework'),
								'default' => '',
								'type' => 'text',
								'col' => '4',
							),
						)
					),
				),
			);

			$configs['meetings_meta_boxes'] = array(
				'name' => esc_html__('Meetings Settings', 'felan-framework'),
				'post_type' => array('meetings'),
				'fields' => array(
					array(
						'id' => "{$meta_prefix}meeting_status",
						'title' => esc_html__('Status', 'felan-framework'),
						'type' => 'button_set',
						'options' => array(
							'upcoming' => esc_html__('Upcoming', 'felan-framework'),
							'completed' => esc_html__('Completed', 'felan-framework'),
						),
						'default' => 'upcoming',
					),
					array(
						'type' => 'row',
						'col' => '6',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}meeting_with",
								'title' => esc_html__('Meeting With', 'felan-framework'),
								'type' => 'text',
								'default' => '',
							),
							array(
								'id' => "{$meta_prefix}meeting_date",
								'title' => esc_html__('Date', 'felan-framework'),
								'type' => 'text',
								'default' => '',
							),
							array(
								'id' => "{$meta_prefix}meeting_time",
								'title' => esc_html__('Time', 'felan-framework'),
								'type' => 'text',
								'default' => '',
							),
							array(
								'id' => "{$meta_prefix}meeting_time_duration",
								'title' => esc_html__('Time Duration', 'felan-framework'),
								'type' => 'text',
								'default' => '',
							),
						)
					),
					array(
						'id' => "{$meta_prefix}meeting_message",
						'title' => esc_html__('Message', 'felan-framework'),
						'type' => 'textarea',
						'default' => '',
					),
				),
			);

			if (post_type_exists('job_alerts')) {
				$configs['job_alerts_meta_boxes'] = array(
					'name' => esc_html__('Job Alerts Infomation', 'felan-framework'),
					'post_type' => array('job_alerts'),
					'fields' => array(
						array(
							'type' => 'row',
							'fields' => array(
								array(
									'id' => "{$meta_prefix}job_alerts_email",
									'title' => esc_html__('Email', 'felan-framework'),
									'type' => 'text',
									'default' => '',
									'col' => '12',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_location",
									'title' => esc_html__('Location', 'felan-framework'),
									'type' => 'select',
									'options' => felan_get_taxonomy('felan_location', false, true, true),
									'default' => '',
									'col' => '3',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_categories",
									'title' => esc_html__('Categories', 'felan-framework'),
									'type' => 'select',
									'options' => felan_get_taxonomy('jobs-categories', false, true, true),
									'default' => '',
									'col' => '3',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_experience",
									'title' => esc_html('Experience', 'felan-framework'),
									'type' => 'select',
									'options' => felan_get_taxonomy('jobs-experience', false, true, true),
									'default' => '',
									'col' => '3',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_frequency",
									'title' => esc_html__('Frequency', 'felan-framework'),
									'type' => 'select',
									'options' => array(
										''	=> esc_html('Select an option', 'felan-framework'),
										'daily'	=> esc_html('Daily', 'felan-framework'),
										'weekly'	=> esc_html('Weekly', 'felan-framework'),
										'monthly'	=> esc_html('Monthly', 'felan-framework'),
									),
									'default' => '',
									'col' => '3',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_skill",
									'type' => 'checkbox_list',
									'title' => esc_html__('Skills', 'felan-framework'),
									'options' => felan_get_taxonomy('jobs-skills', false, true, true),
									'value_inline' => true,
									'default' => array(),
									'col' => '12',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_type",
									'type' => 'checkbox_list',
									'title' => esc_html__('Type', 'felan-framework'),
									'options' => felan_get_taxonomy('jobs-type', false, true, true),
									'value_inline' => true,
									'default' => array(),
									'col' => '12',
								),
							)
						),
					),
				);
			}

			// Page
			$configs['felan_page_options'] = array(
				'name' => esc_html__('Page Options', 'felan-framework'),
				'post_type' => array('page'),
				'section' => array_merge(
					apply_filters('felan_register_meta_boxes_page_top', array()),
					apply_filters(
						'felan_register_meta_boxes_page_main',
						array_merge(
							array(
								array(
									'id' => "{$meta_prefix}page_Layout",
									'title' => esc_html__('Page Layout', 'felan-framework'),
									'icon' => 'dashicons-admin-home',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}page_body_bg",
													'title' => esc_html__('Body Background', 'felan-framework'),
													'type' => 'color',
													'col' => '6',
													'default' => '',
												),
												array(
													'id' => "{$meta_prefix}show_page_rtl",
													'title' => esc_html__('Page Rtl', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('Default', 'felan-framework'),
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'col' => '6',
													'default' => '',
												),
												array(
													'id' => "{$meta_prefix}page_pt_deskop",
													'title' => esc_html__('Padding Top (Deskop)', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
													'pattern' => '[0-9]*',
												),
												array(
													'id' => "{$meta_prefix}page_pb_deskop",
													'title' => esc_html__('Padding Bottom (Deskop)', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
													'pattern' => '[0-9]*',
												),
												array(
													'id' => "{$meta_prefix}page_pt_mobie",
													'title' => esc_html__('Padding Top (Mobie)', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
													'pattern' => '[0-9]*',
												),
												array(
													'id' => "{$meta_prefix}page_pb_mobie",
													'title' => esc_html__('Padding Bottom (Mobie)', 'felan-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
													'pattern' => '[0-9]*',
												),
											)
										),

									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}page_header",
									'title' => esc_html__('Page Header', 'felan-framework'),
									'icon' => 'dashicons-before dashicons-smiley',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}header_show",
													'title' => esc_html__('Show Header', 'felan-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'col' => '4',
													'default' => '1',
												),
												array(
													'id' => "{$meta_prefix}header_style",
													'title' => esc_html__('Header Style', 'felan-framework'),
													'type' => 'button_set',
													'options' => array(
														'dark' => esc_html__('Dark', 'felan-framework'),
														'light' => esc_html__('Light', 'felan-framework'),
													),
													'col' => '4',
													'default' => 'dark',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}show_top_bar",
													'title' => esc_html__('Show Top Bar', 'felan-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'col' => '4',
													'default' => '0',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}show_border_bottom",
													'title' => esc_html__('Show Border Bottom', 'felan-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'col' => '4',
													'default' => '1',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}header_type",
													'title' => esc_html__('Header Type', 'felan-framework'),
													'type' => 'select',
													'default' => '',
													'options' => felan_get_header_elementor(),
													'col' => '4',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}show_header_float",
													'title' => esc_html__('Header Float', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('Default', 'felan-framework'),
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}show_header_sticky",
													'title' => esc_html__('Header Sticky', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('Default', 'felan-framework'),
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}show_header_cate",
													'title' => esc_html__('Header Categories', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('Default', 'felan-framework'),
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}cate_border_color",
													'title' => esc_html__('Categories Border Color', 'felan-framework'),
													'type' => 'color',
													'col' => '4',
													'default' => '',
													'required' => array(
														"{$meta_prefix}header_show",
														'=',
														'1',
														"{$meta_prefix}show_header_cate",
														'!=',
														'0'
													),
												),
											)
										),

									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}page_footer",
									'title' => esc_html__('Page Footer', 'felan-framework'),
									'icon' => 'dashicons-excerpt-view',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}footer_show",
													'title' => esc_html__('Show Footer', 'felan-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'col' => '4',
													'default' => '1',
												),
												array(
													'id' => "{$meta_prefix}footer_type",
													'title' => esc_html__('Footer Type', 'felan-framework'),
													'type' => 'select',
													'default' => '',
													'options' => felan_get_footer_elementor(),
													'col' => '4',
													'required' => array("{$meta_prefix}footer_show", '=', '1'),
												)
											)
										),

									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}page_title",
									'title' => esc_html__('Page Title', 'felan-framework'),
									'icon' => 'dashicons-analytics',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}page_title_show",
													'title' => esc_html__('Show Page Title', 'felan-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'col' => '4',
													'default' => '0',
												),
												array(
													'id' => "{$meta_prefix}page_title_color",
													'title' => esc_html__('Text Color', 'felan-framework'),
													'type' => 'color',
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}page_title_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}page_title_bg",
													'title' => esc_html__('Background Color', 'felan-framework'),
													'type' => 'color',
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}page_title_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}page_title_image",
													'title' => esc_html__('Background Image', 'felan-framework'),
													'type' => 'image',
													'default' => '',
													'required' => array("{$meta_prefix}page_title_show", '=', '1'),
												)
											)
										),

									)
								)
							)
						)
					),
					apply_filters('felan_register_meta_boxes_page_bottom', array())
				),
			);

			//Meta box project
			$configs['project_meta_boxes'] = apply_filters('felan_register_meta_boxes_project', array(
				'name' => esc_html__('Project Information', 'felan-framework'),
				'post_type' => array('project'),
				'section' => array_merge(
					apply_filters('felan_register_meta_boxes_project_top', array()),
					apply_filters(
						'felan_register_meta_boxes_project_main',
						array_merge(
							array(
								array(
									'id' => "{$meta_prefix}details_project_basic",
									'title' => esc_html__('Basic', 'felan-framework'),
									'icon' => 'dashicons dashicons-admin-home',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}project_featured",
													'title' => esc_html__('Mark this project as featured ?', 'felan-framework'),
													'type' => 'button_set',
													'col' => '12',
													'options' => array(
														'1' => esc_html__('Yes', 'felan-framework'),
														'0' => esc_html__('No', 'felan-framework'),
													),
													'default' => '0',
												),
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}details_budget",
									'title' => esc_html__('Budget', 'felan-framework'),
									'icon' => 'dashicons dashicons-money-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}project_budget_show",
													'title' => esc_html__('Project Type', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'fixed' =>  esc_html('Fixed Price', 'felan-framework'),
														'hourly' =>  esc_html('Hourly Rate', 'felan-framework'),
													),
													'col' => '4',
													'default' => 'fixed',
												),
												array(
													'id' => "{$meta_prefix}project_budget_minimum",
													'title' => esc_html__('Minimum Price', 'felan-framework'),
													'desc' => esc_html__('Example Value: 450', 'felan-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '450',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}project_budget_maximum",
													'title' => esc_html__('Maximum Price', 'felan-framework'),
													'desc' => esc_html__('Example Value: 900', 'felan-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '900',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}project_maximum_hours",
													'title' => esc_html__('Estimated maximum hours', 'felan-framework'),
													'desc' => esc_html__('Example Value: 30', 'felan-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}project_budget_show", '=', 'hourly')
													),
												),
												array(
													'id' => "{$meta_prefix}project_value_rate",
													'title' => esc_html__('Value Rate', 'felan-framework'),
													'desc' => esc_html__('Example Value: 3', 'felan-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}project_budget_show", '!=', 'hourly')
													),
												),
												array(
													'id' => "{$meta_prefix}project_budget_rate",
													'title' => esc_html__('Rate', 'felan-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html('None', 'felan-framework'),
														'hour' => esc_html('Per Hour', 'felan-framework'),
														'day' => esc_html('Per Day', 'felan-framework'),
														'week' => esc_html('Per Week', 'felan-framework'),
														'month' => esc_html('Per Month', 'felan-framework'),
														'year' => esc_html('Per Year', 'felan-framework'),
													),
													'col' => '4',
													'default' => 'hour',
													'required' => array(
														array("{$meta_prefix}project_budget_show", '!=', 'hourly')
													),
												),
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}project_company",
									'title' => esc_html__('Company', 'felan-framework'),
									'icon' => 'dashicons dashicons-building',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}project_select_company",
											'title' => esc_html__('Select company', 'felan-framework'),
											'type' => 'select',
											'options' => felan_select_project_company(),
										),
									)
								),
								array(
									'id' => "{$meta_prefix}details_project_faq",
									'title' => esc_html__('Faqs', 'felan-framework'),
									'icon' => 'dashicons dashicons-palmtree',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}project_tab_faq",
											'type' => 'panel',
											'title' => esc_html__('Faqs', 'felan-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}project_faq_title",
															'title' => esc_html__('Title', 'felan-framework'),
															'type' => 'text',
															'col' => '12',
														),
														array(
															'id' => "{$meta_prefix}project_faq_description",
															'title' => esc_html__('Description', 'felan-framework'),
															'type' => 'textarea',
															'col' => '12',
														),
													)
												)
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}location_tab",
									'title' => esc_html__('Location', 'felan-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												$project_location_city,
												array(
													'id' => "{$meta_prefix}project_address",
													'title' => esc_html__('Maps location', 'felan-framework'),
													'desc' => esc_html__('Full Address', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}project_latitude",
													'title' => esc_html__('Latitude', 'felan-framework'),
													'desc' => esc_html__('Latitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}project_longtitude",
													'title' => esc_html__('Longtitude', 'felan-framework'),
													'desc' => esc_html__('Longtitude Details', 'felan-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}project_location",
													'title' => esc_html__('Service Location at Google Map', 'felan-framework'),
													'desc' => esc_html__('Drag the google map marker to point your project location.', 'felan-framework'),
													'type' => 'map',
													'address_field' => "{$meta_prefix}project_address",
												),
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}gallery_project_tab",
									'title' => esc_html__('Gallery Images', 'felan-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}project_images",
											'title' => esc_html__('Gallery', 'felan-framework'),
											'type' => 'gallery',
										),
									)
								),
								array(
									'id' => "{$meta_prefix}video_project_tab",
									'title' => esc_html__('Video', 'felan-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}project_video_url",
											'title' => esc_html__('Video URL', 'felan-framework'),
											'desc' => esc_html__('Input only URL. YouTube, Vimeo, SWF File and MOV File', 'felan-framework'),
											'type' => 'text',
											'col' => 12,
										),
										array(
											'id' => "{$meta_prefix}project_video_image",
											'title' => esc_html__('Video Image', 'felan-framework'),
											'type' => 'gallery',
											'col' => 12,
										),
									)
								),
								array(
									'id' => "{$meta_prefix}custom_field_project_tab",
									'title' => esc_html__('Additional Fields', 'felan-framework'),
									'icon' => 'dashicons dashicons-welcome-add-page',
									'fields' => $render_custom_field_project
								),
							)
						)
					),
					apply_filters('felan_register_meta_boxes_project_bottom', array())
				),
			));

			return apply_filters('felan_register_meta_boxes', $configs);
		}

		/**
		 * Register options config
		 * @param $configs
		 * @return mixed
		 */
		public function register_options_config($configs)
		{
			if (function_exists('pll_the_languages')) {
				$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
				$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
				$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
				$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');

				$configs['felan-framework'] = array(
					'layout' => 'horizontal',
					'page_title' => esc_html__('Theme Options', 'felan-framework'),
					'menu_title' => esc_html__('Theme Options', 'felan-framework'),
					'option_name' => pll_current_language() . '_felan-framework',
					'permission' => 'edit_theme_options',
					'section' => array_merge(
						apply_filters('felan_register_options_config_top', array()),
						array_filter(array(
							$this->general_option(),
							($enable_post_type_jobs == '1') ? $this->jobs_option() : null,
							$this->company_option(),
							$this->freelancer_option(),
							($enable_post_type_service == '1') ? $this->service_option() : null,
							($enable_post_type_project == '1') ? $this->project_option() : null,
							($enable_wallet_mode == '1' && $enable_post_type_jobs !== '1') ? $this->wallet_option() : null,
							($enable_wallet_mode == '0') ? $this->payment_option() : null,
							$this->payout_option(),
							$this->social_network(),
							$this->login_option(),
							$this->locations_option(),
							$this->google_map_option(),
							$this->price_format_option(),
							$this->user_option(),
							$this->url_slugs_option(),
							$this->setup_page(),
							$this->ai_helper(),
							$this->email_management_option(),
							($enable_post_type_jobs == '1') ? $this->custom_field_jobs_option() : null,
							$this->custom_field_company_option(),
							$this->custom_field_freelancer_option(),
							($enable_post_type_project == '1') ? $this->custom_field_project_option() : null,
							($enable_post_type_service == '1') ? $this->custom_field_service_option() : null,
						)),
						apply_filters('felan_register_options_config_bottom', array())
					)
				);
			} else if (defined('ICL_SITEPRESS_VERSION')) {
				$current_language = apply_filters('wpml_current_language', NULL);

				if ($current_language) {
					$option_name = $current_language . '_felan-framework';
				} else {
					$option_name = 'felan-framework';
				}
				$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
				$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
				$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
				$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');

				$configs['felan-framework'] = array(
					'layout' => 'horizontal',
					'page_title' => esc_html__('Theme Options', 'felan-framework'),
					'menu_title' => esc_html__('Theme Options', 'felan-framework'),
					'option_name' => $option_name,
					'permission' => 'edit_theme_options',
					'section' => array_merge(
						apply_filters('felan_register_options_config_top', array()),
						array_filter(array(
							$this->general_option(),
							($enable_post_type_jobs == '1') ? $this->jobs_option() : null,
							$this->company_option(),
							$this->freelancer_option(),
							($enable_post_type_service == '1') ? $this->service_option() : null,
							($enable_post_type_project == '1') ? $this->project_option() : null,
							($enable_wallet_mode == '1' && $enable_post_type_jobs !== '1') ? $this->wallet_option() : null,
							($enable_wallet_mode == '0') ? $this->payment_option() : null,
							$this->payout_option(),
							$this->social_network(),
							$this->login_option(),
							$this->locations_option(),
							$this->google_map_option(),
							$this->price_format_option(),
							$this->user_option(),
							$this->url_slugs_option(),
							$this->setup_page(),
							$this->ai_helper(),
							$this->email_management_option(),
							($enable_post_type_jobs == '1') ? $this->custom_field_jobs_option() : null,
							$this->custom_field_company_option(),
							$this->custom_field_freelancer_option(),
							($enable_post_type_project == '1') ? $this->custom_field_project_option() : null,
							($enable_post_type_service == '1') ? $this->custom_field_service_option() : null,
						)),
						apply_filters('felan_register_options_config_bottom', array())
					)
				);
			} else {
				$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
				$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
				$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
				$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');

				$configs['felan-framework'] = array(
					'layout' => 'horizontal',
					'page_title' => esc_html__('Theme Options', 'felan-framework'),
					'menu_title' => esc_html__('Theme Options', 'felan-framework'),
					'option_name' => 'felan-framework',
					'permission' => 'edit_theme_options',
					'section' => array_merge(
						apply_filters('felan_register_options_config_top', array()),
						array_filter(array(
							$this->general_option(),
							($enable_post_type_jobs == '1') ? $this->jobs_option() : null,
							$this->company_option(),
							$this->freelancer_option(),
							($enable_post_type_service == '1') ? $this->service_option() : null,
							($enable_post_type_project == '1') ? $this->project_option() : null,
							($enable_wallet_mode == '1' && $enable_post_type_jobs !== '1') ? $this->wallet_option() : null,
							($enable_wallet_mode == '0') ? $this->payment_option() : null,
							$this->payout_option(),
							$this->social_network(),
							$this->login_option(),
							$this->locations_option(),
							$this->google_map_option(),
							$this->price_format_option(),
							$this->user_option(),
							$this->url_slugs_option(),
							$this->setup_page(),
							$this->ai_helper(),
							$this->email_management_option(),
							($enable_post_type_jobs == '1') ? $this->custom_field_jobs_option() : null,
							$this->custom_field_company_option(),
							$this->custom_field_freelancer_option(),
							($enable_post_type_project == '1') ? $this->custom_field_project_option() : null,
							($enable_post_type_service == '1') ? $this->custom_field_service_option() : null,
						)),
						apply_filters('felan_register_options_config_bottom', array())
					)
				);
			}
			return apply_filters('felan_register_options_config', $configs);
		}

		/**
		 * @return mixed|void
		 */
		private function general_option()
		{
			$prefix_code = phone_prefix_code();
			$keys = $values = array();
			foreach ($prefix_code as $key => $value) {
				$keys[] = $key;
				$values[] = $value['name'];
			}
			$phone_code_select = array_combine($keys, $values);

			return apply_filters('felan_register_option_general', array(
				'id' => 'felan_general_option',
				'title' => esc_html__('General Option', 'felan-framework'),
				'icon' => 'dashicons-admin-multisite',
				'fields' => array_merge(
					apply_filters('felan_register_option_general_top', array()),
					array(
						array(
							'id' => 'enable_post_type_jobs',
							'type' => 'button_set',
							'title' => esc_html__('Enable post type jobs', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable post type jobs', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => 'enable_post_type_service',
							'type' => 'button_set',
							'title' => esc_html__('Enable post type services', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable post type services', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => 'enable_post_type_project',
							'type' => 'button_set',
							'title' => esc_html__('Enable post type projects', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable post type projects', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						// array(
						// 	'id' => 'enable_wallet_mode',
						// 	'type' => 'button_set',
						// 	'title' => esc_html__('Enable wallet mode', 'felan-framework'),
						// 	'subtitle' => esc_html__('Enable/Disable wallet mode', 'felan-framework'),
						// 	'desc' => '',
						// 	'options' => array(
						// 		'1' => esc_html__('On', 'felan-framework'),
						// 		'0' => esc_html__('Off', 'felan-framework'),
						// 	),
						// 	'default' => '0',
						// 	'required' => array(
						// 		array("enable_post_type_jobs", '=', '0')
						// 	),
						// ),
						array(
							'id' => 'enable_admin_approval_package',
							'type' => 'button_set',
							'title' => esc_html__('Enable admin approval for package', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Pending admin approval for package purchase', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => 'enable_switch_account',
							'type' => 'button_set',
							'title' => esc_html__('Enable Switch Account', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Switch Account', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'enable_24_time_format',
							'type' => 'button_set',
							'title' => esc_html__('Enable Time Format', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Time Format (24H)', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'enable_cookie',
							'type' => 'button_set',
							'title' => esc_html__('Enable Cookie Notice', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Popup Cookie Notice', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "enable_back_top",
							'type' => 'button_set',
							'title' => esc_html__('Enable Back To Top', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Enable Back To Top', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'enable_search_box_dropdown',
							'type' => 'button_set',
							'title' => esc_html__('Enable Search Box', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Search Box for Dropdown', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'limit_search_box',
							'title' => esc_html__('Limit Search Box', 'felan-framework'),
							'type' => 'text',
							'default' => '6',
							'pattern' => '[0-9]*',
							'subtitle' => 'If the option selects more than the set number, a search box will be displayed.',
							'required' => array(
								array("enable_search_box_dropdown", '=', '1')
							),
						),
						array(
							'id' => 'enable_rtl_mode',
							'type' => 'button_set',
							'title' => esc_html__('Enable RTL Mode', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable RTL mode', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '0'
						),
						array(
							'id' => 'default_phone_number',
							'type' => 'select',
							'title' => esc_html__('Default Phone Number', 'felan-framework'),
							'subtitle' => esc_html__('Chosse Default Phone Number', 'felan-framework'),
							'options' => $phone_code_select,
							'default' => '0',
						),
						array(
							'id' => 'felan-cv-type',
							'title' => esc_html__('Cv Types', 'felan-framework'),
							'type' => 'text',
							'default' => 'doc,docx,pdf',
							'subtitle' => 'Add "," to separate file formats',
						),
						array(
							'id' => 'felan_image_type',
							'title' => esc_html__('Types Upload Image', 'felan-framework'),
							'type' => 'text',
							'default' => 'jpg,jpeg,png,gif,webp',
							'subtitle' => 'Add "," to separate file formats',
						),
						array(
							'id' => 'felan_max_gallery_images',
							'type' => 'text',
							'title' => esc_html__('Maximum Images', 'felan-framework'),
							'subtitle' => esc_html__('Maximum images allowed for single jobs.', 'felan-framework'),
							'default' => '5',
						),
						array(
							'id' => 'felan_image_max_file_size',
							'type' => 'text',
							'title' => esc_html__('Maximum File Size', 'felan-framework'),
							'subtitle' => esc_html__('Maximum upload image size. For example 10kb, 500kb, 1mb, 10mb, 100mb', 'felan-framework'),
							'default' => '1000kb',
						),
						array(
							'id' => 'felan_price_min',
							'type' => 'text',
							'title' => esc_html__('Price Min', 'felan-framework'),
							'subtitle' => esc_html__('Enter Price Min', 'felan-framework'),
							'pattern' => '[0-9]*',
							'default' => '0',
						),
						array(
							'id' => 'felan_price_max',
							'type' => 'text',
							'title' => esc_html__('Price Max', 'felan-framework'),
							'subtitle' => esc_html__('Enter Price Max', 'felan-framework'),
							'pattern' => '[0-9]*',
							'default' => '1000',
						),
						array(
							'id' => "felan_distance_unit",
							'title' => esc_html__('Distance unit on location', 'felan-framework'),
							'subtitle' => esc_html__('Select the distance unit in the location', 'felan-framework'),
							'type' => 'select',
							'options' => array(
								'km' => 'Kilometers (km)',
								'mi' => 'Miles (mi)',
							),
						),
						array(
							'id' => 'social_sharing',
							'type' => 'checkbox_list',
							'title' => esc_html__('Show Social Sharing', 'felan-framework'),
							'subtitle' => esc_html__('Choose which fields you want to show on social sharing?', 'felan-framework'),
							'options' => array(
								'facebook' => esc_html__('Facebook', 'felan-framework'),
								'twitter' => esc_html__('Twitter', 'felan-framework'),
								'linkedin' => esc_html__('Linkedin', 'felan-framework'),
								'tumblr' => esc_html__('Tumblr', 'felan-framework'),
								'pinterest' => esc_html__('Pinterest', 'felan-framework'),
								'whatapp' => esc_html__('Whatapp', 'felan-framework'),
							),
							'value_inline' => false,
							'default' => array('facebook', 'twitter', 'linkedin', 'tumblr', 'pinterest', 'whatapp')
						),
						array(
							'id' => 'header_script',
							'type' => 'ace_editor',
							'title' => esc_html__('Header Script', 'felan-framework'),
							'subtitle' => esc_html__('Add custom scripts inside HEAD tag. You need to have a SCRIPT tag around scripts.', 'felan-framework'),
							'default' => ''
						),
						array(
							'id' => 'footer_script',
							'type' => 'ace_editor',
							'title' => esc_html__('Footer Script', 'felan-framework'),
							'subtitle' => esc_html__('Add custom scripts you might want to be loaded in the footer of your website. You need to have a SCRIPT tag around scripts.', 'felan-framework'),
							'default' => ''
						),
					),
					apply_filters('felan_register_option_general_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function payout_option()
		{
			return apply_filters('felan_register_option_payout', array(
				'id' => 'felan_payout_option',
				'title' => esc_html__('Payout Option', 'felan-framework'),
				'icon' => 'dashicons dashicons-index-card',
				'fields' => array_merge(
					apply_filters('felan_register_option_payout_top', array()),
					apply_filters('felan_register_option_payout_main', array(
						array(
							'id' => "enable_payout_paypal",
							'type' => 'button_set',
							'title' => esc_html__('Enable Payout Paypal', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Payout Paypal', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "enable_payout_stripe",
							'type' => 'button_set',
							'title' => esc_html__('Enable Payout Stripe', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Payout Stripe', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "enable_payout_bank_transfer",
							'type' => 'button_set',
							'title' => esc_html__('Enable Payout Bank Transfer', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Bank Transfer', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "custom_payout_setting",
							'type' => 'panel',
							'title' => esc_html__('Custom Payout', 'felan-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'title' => esc_html__('Name Payout', 'felan-framework'),
									'id' => "name",
									'type' => 'text',
									'subtitle' => esc_html__('Enter Same "Name Payout" if you want multiple fields in Payout', 'felan-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Label', 'felan-framework'),
									'id' => "label",
									'type' => 'text',
									'default' => '',
								),
								array(
									'title' => esc_html__('ID', 'felan-framework'),
									'id' => "id",
									'type' => 'text',
									'placeholder' => esc_html__('Enter field ID', 'felan-framework'),
									'desc' => esc_html__('ID cannot be duplicated', 'felan-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Field Type', 'felan-framework'),
									'id' => "type",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'text' => esc_html__('Text', 'felan-framework'),
										'number' => esc_html__('Number', 'felan-framework'),
										'email' => esc_html__('Email', 'felan-framework'),
									)
								),
							)
						)
					)),
					apply_filters('felan_register_option_payout_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function setup_page()
		{
			$service_page_id = $payment_service_page_id = $service_payment_completed_page_id = $freelancer_service_page_id = $submit_service_page_id
				= $jobs_dashboard_page_id = $jobs_submit_page_id = $meetings_page_id = $freelancer_meetings_page_id = $my_jobs_page_id
				= $projects_submit_page_id = $projects_page_id = $payment_project_page_id = $project_payment_completed_page_id = $my_project_page_id
				= $disputes_page_id = $freelancer_disputes_page_id = $employer_user_package_page = $employer_package_page = $employer_wallet
				= $freelancer_user_package_page = $freelancer_package_page = $employer_payment_page = $employer_payment_completed_page
				= $freelancer_payment_page = $freelancer_payment_completed_page = $payment_thank_you_id = array();

			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');

			if ($enable_post_type_jobs == '1') {
				$jobs_dashboard_page_id = array(
					'id' => 'felan_jobs_dashboard_page_id',
					'title' => esc_html__('Jobs Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
				$jobs_submit_page_id = array(
					'id' => 'felan_jobs_submit_page_id',
					'title' => esc_html__('Jobs Submit Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
				$meetings_page_id =  array(
					'id' => 'felan_meetings_page_id',
					'title' => esc_html__('Meetings Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
				$freelancer_meetings_page_id = array(
					'id' => 'felan_freelancer_meetings_page_id',
					'title' => esc_html__('Meetings Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);

				$my_jobs_page_id = array(
					'id' => 'felan_my_jobs_page_id',
					'title' => esc_html__('My Jobs Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
			}

			if ($enable_post_type_service == '1') {
				$service_page_id =  array(
					'id' => 'felan_employer_service_page_id',
					'title' => esc_html__('Services Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);

				if ($enable_wallet_mode == '0') {
					$payment_service_page_id =  array(
						'id' => 'felan_payment_service_page_id',
						'title' => esc_html__('Payment Service Page', 'felan-framework'),
						'type' => 'select',
						'data' => 'page',
						'data_args' => array(
							'numberposts' => -1,
						)
					);
					$service_payment_completed_page_id = array(
						'id' => 'felan_service_payment_completed_page_id',
						'title' => esc_html__('Payment Service Completed Page', 'felan-framework'),
						'type' => 'select',
						'data' => 'page',
						'data_args' => array(
							'numberposts' => -1,
						)
					);
				}

				$freelancer_service_page_id = array(
					'id' => 'felan_freelancer_service_page_id',
					'title' => esc_html__('Services Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
				$submit_service_page_id = array(
					'id' => 'felan_submit_service_page_id',
					'title' => esc_html__('Submit Service', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
			}

			if ($enable_post_type_project == '1') {
				$projects_submit_page_id = array(
					'id' => 'felan_projects_submit_page_id',
					'title' => esc_html__('Projects Submit Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
				$projects_page_id = array(
					'id' => 'felan_projects_page_id',
					'title' => esc_html__('Projects Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);

				$proposals_page_id = array(
					'id' => 'felan_proposal_page_id',
					'title' => esc_html__('Proposals Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);

				if ($enable_wallet_mode == '0') {
					$payment_project_page_id = array(
						'id' => 'felan_payment_project_page_id',
						'title' => esc_html__('Payment Project Page', 'felan-framework'),
						'type' => 'select',
						'data' => 'page',
						'data_args' => array(
							'numberposts' => -1,
						)
					);
					$project_payment_completed_page_id = array(
						'id' => 'felan_project_payment_completed_page_id',
						'title' => esc_html__('Payment Project Completed Page', 'felan-framework'),
						'type' => 'select',
						'data' => 'page',
						'data_args' => array(
							'numberposts' => -1,
						)
					);
				}

				$my_project_page_id =  array(
					'id' => 'felan_my_project_page_id',
					'title' => esc_html__('My project Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
			}

			if ($enable_post_type_service == '1' || $enable_post_type_project == '1') {
				$disputes_page_id =  array(
					'id' => 'felan_disputes_page_id',
					'title' => esc_html__('Disputes Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);

				$freelancer_disputes_page_id = array(
					'id' => 'felan_freelancer_disputes_page_id',
					'title' => esc_html__('Disputes Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
			}

			if ($enable_wallet_mode == '0') {
				$employer_user_package_page = array(
					'id' => 'felan_user_package_page_id',
					'title' => esc_html__('User Package Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);

				$employer_package_page = array(
					'id' => 'felan_package_page_id',
					'title' => esc_html__('Package Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);

				$freelancer_user_package_page = array(
					'id' => 'felan_freelancer_user_package_page_id',
					'title' => esc_html__('User Package Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);

				$freelancer_package_page = array(
					'id' => 'felan_freelancer_package_page_id',
					'title' => esc_html__('Package Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
				$employer_payment_page =  array(
					'id' => 'felan_payment_page_id',
					'title' => esc_html__('Payment Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
				$employer_payment_completed_page = array(
					'id' => 'felan_payment_completed_page_id',
					'title' => esc_html__('Payment Completed Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
				$freelancer_payment_page = array(
					'id' => 'felan_freelancer_payment_page_id',
					'title' => esc_html__('Payment Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);

				$freelancer_payment_completed_page = array(
					'id' => 'felan_freelancer_payment_completed_page_id',
					'title' => esc_html__('Payment Completed Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
			}

			if ($enable_wallet_mode == '1') {
				$employer_wallet = array(
					'id' => 'felan_wallet_page_id',
					'title' => esc_html__('Wallet Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
				$payment_thank_you_id = array(
					'id' => 'felan_thank_you_page_id',
					'title' => esc_html__('Thank You Page', 'felan-framework'),
					'type' => 'select',
					'data' => 'page',
					'data_args' => array(
						'numberposts' => -1,
					)
				);
			}

			$title_freelancers = esc_html__('Freelancers Page', 'felan-framework');
			$title_freelancer_settings = esc_html__('Freelancer Settings', 'felan-framework');
			if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') {
				$title_freelancers = esc_html__('Candidate Page', 'felan-framework');
				$title_freelancer_settings = esc_html__('Candidate Settings', 'felan-framework');
			}

			return apply_filters('felan_register_setup_page', array(
				'id' => 'felan_setup_page',
				'title' => esc_html__('Setup Page', 'felan-framework'),
				'icon' => 'dashicons-admin-page',
				'fields' => array_merge(
					apply_filters('felan_register_setup_page_employer_top', array()),
					array(
						array(
							'id' => 'sp_sign_in',
							'title' => esc_html__('Sign In', 'felan-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'sp_sign_up',
							'title' => esc_html__('Sign Up', 'felan-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'terms_condition',
							'title' => esc_html__('Terms & Conditions', 'felan-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'privacy_policy',
							'title' => esc_html__('Privacy Policy', 'felan-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'felan_update_profile_page_id',
							'title' => esc_html__('Update Profile', 'felan-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'felan_add_project_page_id',
							'title' => esc_html__('Post Project/Job (Login)', 'felan-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'felan_add_project_not_page_id',
							'title' => esc_html__('Post Project/Job (Not Login)', 'felan-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),

						$payment_thank_you_id,

						apply_filters('felan_register_setup_page_employer_option_main', array(
							'id' => 'felan_register_setup_page_employer_option_main',
							'type' => 'group',
							'title' => esc_html__('Employer Setting', 'felan-framework'),
							'fields' => array(
								array(
									'id' => 'felan_dashboard_page_id',
									'title' => esc_html__('Dashboard Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_freelancers_page_id',
									'title' => $title_freelancers,
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_company_page_id',
									'title' => esc_html__('Company Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_submit_company_page_id',
									'title' => esc_html__('Submit Company Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_messages_page_id',
									'title' => esc_html__('Messages Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_verification_page_id',
									'title' => esc_html__('Identity Verification Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_settings_page_id',
									'title' => esc_html__('Settings Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),

								$employer_user_package_page,
								$employer_package_page,
								$employer_wallet,

								$employer_payment_page,
								$employer_payment_completed_page,

								$disputes_page_id,
								$meetings_page_id,

								$jobs_dashboard_page_id,
								$jobs_submit_page_id,

								$service_page_id,
								$payment_service_page_id,
								$service_payment_completed_page_id,

								$projects_submit_page_id,
								$projects_page_id,
								$proposals_page_id,
								$payment_project_page_id,
								$project_payment_completed_page_id,
							),
						))
					),
					apply_filters('felan_register_setup_page_employer_bottom', array()),
					apply_filters('felan_register_setup_page_freelancer_top', array()),
					array(
						apply_filters('felan_register_setup_page_freelancer_option_main', array(
							'id' => 'felan_register_setup_page_freelancer_option_main',
							'type' => 'group',
							'title' => $title_freelancer_settings,
							'fields' => array(
								array(
									'id' => 'felan_freelancer_dashboard_page_id',
									'title' => esc_html__('Dashboard Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_freelancer_profile_page_id',
									'title' => esc_html__('Profile Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_freelancer_verification_page_id',
									'title' => esc_html__('Identity Verification Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_freelancer_reviews_page_id',
									'title' => esc_html__('My Reviews Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_freelancer_company_page_id',
									'title' => esc_html__('My Following', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_freelancer_messages_page_id',
									'title' => esc_html__('Messages Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'felan_freelancer_settings_page_id',
									'title' => esc_html__('Settings Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),

								$freelancer_user_package_page,
								$freelancer_package_page,

								$freelancer_payment_page,
								$freelancer_payment_completed_page,

								array(
									'id' => 'felan_freelancer_wallet_page_id',
									'title' => esc_html__('Wallet Page', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),

								$freelancer_disputes_page_id,
								$freelancer_meetings_page_id,
								$my_jobs_page_id,

								$freelancer_service_page_id,
								$submit_service_page_id,
								$my_project_page_id,
							),
						)),
					),
					apply_filters('felan_register_setup_page_freelancer_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function url_slugs_option()
		{
			$option_url_service_slugs = $option_url_jobs_slugs = array();
			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			$title_freelancers = esc_html__('Freelancer', 'felan-framework');
			if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') {
				$title_freelancers = esc_html__('Candidate', 'felan-framework');
			}

			if ($enable_post_type_jobs == '1') {
				$option_url_jobs_slugs = array(
					'id' => 'felan_main_group',
					'type' => 'group',
					'title' => esc_html__('Jobs', 'felan-framework'),
					'fields' => array(
						array(
							'id' => 'jobs_url_slug',
							'title' => esc_html__('Jobs Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'jobs',
						),
						array(
							'id' => 'jobs_type_url_slug',
							'title' => esc_html__('Type Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'jobs-type',
						),
						array(
							'id' => 'jobs_categories_url_slug',
							'title' => esc_html__('Categories Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'jobs-categories',
						),
						array(
							'id' => 'jobs_skills_url_slug',
							'title' => esc_html__('Skills Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'jobs-skills',
						),
						array(
							'id' => 'jobs_career_url_slug',
							'title' => esc_html__('Career Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'jobs-career',
						),
						array(
							'id' => 'jobs_experience_url_slug',
							'title' => esc_html__('Experience Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'jobs-experience',
						),
						array(
							'id' => 'jobs_qualification_url_slug',
							'title' => esc_html__('Qualification Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'jobs-qualification',
						),
						array(
							'id' => 'jobs_gender_url_slug',
							'title' => esc_html__('Gender Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'jobs-gender',
						),
					),
				);
			}

			if ($enable_post_type_service == '1') {
				$option_url_service_slugs = array(
					'id' => 'felan_main_group',
					'type' => 'group',
					'title' => esc_html__('Service', 'felan-framework'),
					'fields' => array(
						array(
							'id' => 'service_url_slug',
							'title' => esc_html__('Service Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'services',
						),
						array(
							'id' => 'service_categories_url_slug',
							'title' => esc_html__('Service Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'service-categories',
						),
						array(
							'id' => 'service_skills_url_slug',
							'title' => esc_html__('Skills Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'service-skills',
						),
						array(
							'id' => 'service_language_url_slug',
							'title' => esc_html__('Language Slug', 'felan-framework'),
							'type' => 'text',
							'default' => 'service-language',
						),
					),
				);
			}

			return
				apply_filters(
					'felan_register_option_url_slugs',
					array(
						'id' => 'felan_url_slugs_option',
						'title' => esc_html__('Url Slug', 'felan-framework'),
						'icon' => 'dashicons-admin-links',
						'fields' => array(
							array(
								'id' => 'enable_slug_categories',
								'type' => 'button_set',
								'title' => esc_html__('Slug Categories', 'felan-framework'),
								'subtitle' => esc_html__('Show/Hidden Slug Categories', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '1',
							),

							//Jobs
							apply_filters('felan_register_option_url_jobs_slugs_top', array()),
							apply_filters(
								'felan_register_option_url_jobs_slugs_center',
								$option_url_jobs_slugs
							),
							apply_filters('felan_register_option_url_jobs_slugs_bottom', array()),

							//Company
							apply_filters('felan_register_option_url_company_slugs_top', array()),
							apply_filters('felan_register_option_url_company_slugs_center', array(
								'id' => 'felan_main_group',
								'type' => 'group',
								'title' => esc_html__('Company', 'felan-framework'),
								'fields' => array(
									array(
										'id' => 'company_url_slug',
										'title' => esc_html__('Company Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'companies',
									),
									array(
										'id' => 'company_categories_url_slug',
										'title' => esc_html__('Categories Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'company-categories',
									),
									array(
										'id' => 'company_size_url_slug',
										'title' => esc_html__('Size Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'company-size',
									),
								),
							)),
							apply_filters('felan_register_option_url_company_slugs_bottom', array()),

							//Freelancer
							apply_filters('felan_register_option_url_freelancer_slugs_top', array()),
							apply_filters('felan_register_option_url_freelancer_slugs_center', array(
								'id' => 'felan_main_group',
								'type' => 'group',
								'title' => $title_freelancers,
								'fields' => array(
									array(
										'id' => 'freelancer_url_slug',
										'title' => $title_freelancers,
										'type' => 'text',
										'default' => 'freelancers',
									),
									array(
										'id' => 'freelancer_categories_url_slug',
										'title' => esc_html__('Categories Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'freelancer_categories',
									),
									array(
										'id' => 'freelancer_ages_url_slug',
										'title' => esc_html__('Ages Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'freelancer-ages',
									),
									array(
										'id' => 'freelancer_languages_url_slug',
										'title' => esc_html__('Languages Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'freelancer-languages',
									),
									array(
										'id' => 'freelancer_qualification_url_slug',
										'title' => esc_html__('Qualification Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'freelancer-qualification',
									),
									array(
										'id' => 'freelancer_yoe_url_slug',
										'title' => esc_html__('Yoe Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'freelancer-yoe',
									),
									array(
										'id' => 'freelancer_education_levels_url_slug',
										'title' => esc_html__('Education Levels Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'freelancer-education-levels',
									),
									array(
										'id' => 'freelancer_skills_url_slug',
										'title' => esc_html__('Skills Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'freelancer-skills',
									),
									array(
										'id' => 'freelancer_gender_url_slug',
										'title' => esc_html__('Gender Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'freelancer-gender',
									),
								),
							)),
							apply_filters('felan_register_option_url_freelancer_slugs_bottom', array()),

							//Service
							apply_filters('felan_register_option_url_service_slugs_top', array()),
							apply_filters(
								'felan_register_option_url_service_slugs_center',
								$option_url_service_slugs
							),
							apply_filters('felan_register_option_url_service_slugs_bottom', array()),

							//Other
							apply_filters('felan_register_option_url_other_slugs_top', array()),
							apply_filters('felan_register_option_url_other_slugs_center', array(
								'id' => 'felan_main_group',
								'type' => 'group',
								'title' => esc_html__('Other', 'felan-framework'),
								'fields' => array(
									array(
										'id' => 'package_url_slug',
										'title' => esc_html__('Package Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'package',
									),
									array(
										'id' => 'invoice_url_slug',
										'title' => esc_html__('Invoice Slug', 'felan-framework'),
										'type' => 'text',
										'default' => 'invoice',
									)
								),
							)),
							apply_filters('felan_register_option_url_other_slugs_bottom', array()),
						),
					)
				);
		}

		/**
		 * @return mixed|void
		 */
		private function custom_field_jobs_option()
		{
			return apply_filters('felan_register_option_custom_field_jobs', array(
				'id' => 'felan_custom_field_jobs_option',
				'title' => esc_html__('Custom Field (Jobs)', 'felan-framework'),
				'icon' => 'dashicons dashicons-admin-customizer',
				'fields' => array_merge(
					apply_filters('felan_register_option_custom_field_jobs_top', array()),
					apply_filters('felan_register_option_custom_field_jobs_main', array(
						array(
							'id' => "custom_field_jobs",
							'type' => 'panel',
							'title' => esc_html__('Additional Field', 'felan-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'title' => esc_html__('Label', 'felan-framework'),
									'id' => "label",
									'type' => 'text',
									'default' => '',
								),
								array(
									'title' => esc_html__('ID', 'felan-framework'),
									'id' => "id",
									'type' => 'text',
									'placeholder' => esc_html__('Enter field ID', 'felan-framework'),
									'desc' => esc_html__('ID values cannot be changed after being set!', 'felan-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Field Type', 'felan-framework'),
									'id' => "field_type",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'text' => esc_html__('Text', 'felan-framework'),
										'url' => esc_html__('Video', 'felan-framework'),
										'textarea' => esc_html__('Textarea', 'felan-framework'),
										'image' => esc_html__('Image', 'felan-framework'),
										'file' => esc_html__('Upload File', 'felan-framework'),
										'select' => esc_html__('Select', 'felan-framework'),
										'checkbox_list' => esc_html__('Checkbox', 'felan-framework'),
									)
								),
								array(
									'title' => esc_html__('Options Value', 'felan-framework'),
									'subtitle' => esc_html__('Input each per line', 'felan-framework'),
									'id' => "select_choices",
									'type' => 'textarea',
									'default' => '',
									'required' => array(
										"field_type",
										'in',
										array('checkbox_list', 'select')
									),
								),
							)
						)
					)),
					apply_filters('felan_register_option_custom_field_jobs_bottom', array())
				)
			));
		}

		private function custom_field_company_option()
		{
			return apply_filters('felan_register_option_custom_field_company', array(
				'id' => 'felan_custom_field_company_option',
				'title' => esc_html__('Custom Field (Company)', 'felan-framework'),
				'icon' => 'dashicons dashicons-admin-customizer',
				'fields' => array_merge(
					apply_filters('felan_register_option_custom_field_company_top', array()),
					apply_filters('felan_register_option_custom_field_company_main', array(
						array(
							'id' => "custom_field_company",
							'type' => 'panel',
							'title' => esc_html__('Additional Field', 'felan-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'title' => esc_html__('Label', 'felan-framework'),
									'id' => "label",
									'type' => 'text',
									'default' => '',
								),
								array(
									'title' => esc_html__('ID', 'felan-framework'),
									'id' => "id",
									'type' => 'text',
									'placeholder' => esc_html__('Enter field ID', 'felan-framework'),
									'desc' => esc_html__('ID values cannot be changed after being set!', 'felan-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Field Type', 'felan-framework'),
									'id' => "field_type",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'text' => esc_html__('Text', 'felan-framework'),
										'url' => esc_html__('Video', 'felan-framework'),
										'textarea' => esc_html__('Textarea', 'felan-framework'),
										'image' => esc_html__('Image', 'felan-framework'),
										'file' => esc_html__('Upload File', 'felan-framework'),
										'select' => esc_html__('Select', 'felan-framework'),
										'checkbox_list' => esc_html__('Checkbox', 'felan-framework'),
									)
								),
								array(
									'title' => esc_html__('Options Value', 'felan-framework'),
									'subtitle' => esc_html__('Input each per line', 'felan-framework'),
									'id' => "select_choices",
									'type' => 'textarea',
									'default' => '',
									'required' => array(
										"field_type",
										'in',
										array('checkbox_list', 'select')
									),
								),
							)
						)
					)),
					apply_filters('felan_register_option_custom_field_company_bottom', array())
				)
			));
		}

		private function custom_field_freelancer_option()
		{
			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			$title_freelancers = esc_html__('Custom Field (Freelancer)', 'felan-framework');
			if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') {
				$title_freelancers = esc_html__('Custom Field (Candidate)', 'felan-framework');
			}

			return apply_filters('felan_register_option_custom_field_freelancer', array(
				'id' => 'felan_custom_field_freelancer_option',
				'title' => $title_freelancers,
				'icon' => 'dashicons dashicons-admin-customizer',
				'fields' => array_merge(
					apply_filters('felan_register_option_custom_field_freelancer_top', array()),
					apply_filters('felan_register_option_custom_field_freelancer_main', array(
						array(
							'id' => "custom_field_freelancer",
							'type' => 'panel',
							'title' => esc_html__('Additional Field', 'felan-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'title' => esc_html__('Tabs', 'felan-framework'),
									'id' => "tabs",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'info' => esc_html__('Info', 'felan-framework'),
										'education' => esc_html__('Education', 'felan-framework'),
										'experience' => esc_html__('Experience', 'felan-framework'),
										'skills' => esc_html__('Skills', 'felan-framework'),
										'projects' => esc_html__('Projects', 'felan-framework'),
										'awards' => esc_html__('Awards', 'felan-framework'),
										'new' => esc_html__('New Tabs', 'felan-framework'),
									),
									'default' => 'new',
								),
								array(
									'title' => esc_html__('Name Tabs', 'felan-framework'),
									'id' => "section",
									'type' => 'text',
									'default' => '',
									'required' => array(
										array("tabs", '=', 'new')
									),
								),
								array(
									'title' => esc_html__('Label', 'felan-framework'),
									'id' => "label",
									'type' => 'text',
									'default' => '',
								),
								array(
									'title' => esc_html__('ID', 'felan-framework'),
									'id' => "id",
									'type' => 'text',
									'placeholder' => esc_html__('Enter field ID', 'felan-framework'),
									'desc' => esc_html__('ID values cannot be changed after being set!', 'felan-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Field Type', 'felan-framework'),
									'id' => "field_type",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'text' => esc_html__('Text', 'felan-framework'),
										'url' => esc_html__('Video', 'felan-framework'),
										'textarea' => esc_html__('Textarea', 'felan-framework'),
										'image' => esc_html__('Image', 'felan-framework'),
										'file' => esc_html__('Upload File', 'felan-framework'),
										'select' => esc_html__('Select', 'felan-framework'),
										'checkbox_list' => esc_html__('Checkbox', 'felan-framework'),
									)
								),
								array(
									'title' => esc_html__('Options Value', 'felan-framework'),
									'subtitle' => esc_html__('Input each per line', 'felan-framework'),
									'id' => "select_choices",
									'type' => 'textarea',
									'default' => '',
									'required' => array(
										"field_type",
										'in',
										array('checkbox_list', 'select')
									),
								),
							)
						)
					)),
					apply_filters('felan_register_option_custom_field_freelancer_bottom', array())
				)
			));
		}

		private function custom_field_project_option()
		{
			return apply_filters('felan_register_option_custom_field_project', array(
				'id' => 'felan_custom_field_project_option',
				'title' => esc_html__('Custom Field (Project)', 'felan-framework'),
				'icon' => 'dashicons dashicons-admin-customizer',
				'fields' => array_merge(
					apply_filters('felan_register_option_custom_field_project_top', array()),
					apply_filters('felan_register_option_custom_field_project_main', array(
						array(
							'id' => "custom_field_project",
							'type' => 'panel',
							'title' => esc_html__('Additional Field', 'felan-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'title' => esc_html__('Label', 'felan-framework'),
									'id' => "label",
									'type' => 'text',
									'default' => '',
								),
								array(
									'title' => esc_html__('ID', 'felan-framework'),
									'id' => "id",
									'type' => 'text',
									'placeholder' => esc_html__('Enter field ID', 'felan-framework'),
									'desc' => esc_html__('ID values cannot be changed after being set!', 'felan-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Field Type', 'felan-framework'),
									'id' => "field_type",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'text' => esc_html__('Text', 'felan-framework'),
										'url' => esc_html__('Video', 'felan-framework'),
										'textarea' => esc_html__('Textarea', 'felan-framework'),
										'image' => esc_html__('Image', 'felan-framework'),
										'file' => esc_html__('Upload File', 'felan-framework'),
										'select' => esc_html__('Select', 'felan-framework'),
										'checkbox_list' => esc_html__('Checkbox', 'felan-framework'),
									)
								),
								array(
									'title' => esc_html__('Options Value', 'felan-framework'),
									'subtitle' => esc_html__('Input each per line', 'felan-framework'),
									'id' => "select_choices",
									'type' => 'textarea',
									'default' => '',
									'required' => array(
										"field_type",
										'in',
										array('checkbox_list', 'select')
									),
								),
							)
						)
					)),
					apply_filters('felan_register_option_custom_field_project_bottom', array())
				)
			));
		}

		private function custom_field_service_option()
		{
			return apply_filters('felan_register_option_custom_field_service', array(
				'id' => 'felan_custom_field_service_option',
				'title' => esc_html__('Custom Field (Service)', 'felan-framework'),
				'icon' => 'dashicons dashicons-admin-customizer',
				'fields' => array_merge(
					apply_filters('felan_register_option_custom_field_service_top', array()),
					apply_filters('felan_register_option_custom_field_service_main', array(
						array(
							'id' => "custom_field_service",
							'type' => 'panel',
							'title' => esc_html__('Additional Field', 'felan-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'title' => esc_html__('Label', 'felan-framework'),
									'id' => "label",
									'type' => 'text',
									'default' => '',
								),
								array(
									'title' => esc_html__('ID', 'felan-framework'),
									'id' => "id",
									'type' => 'text',
									'placeholder' => esc_html__('Enter field ID', 'felan-framework'),
									'desc' => esc_html__('ID values cannot be changed after being set!', 'felan-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Field Type', 'felan-framework'),
									'id' => "field_type",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'text' => esc_html__('Text', 'felan-framework'),
										'url' => esc_html__('Video', 'felan-framework'),
										'textarea' => esc_html__('Textarea', 'felan-framework'),
										'image' => esc_html__('Image', 'felan-framework'),
										'file' => esc_html__('Upload File', 'felan-framework'),
										'select' => esc_html__('Select', 'felan-framework'),
										'checkbox_list' => esc_html__('Checkbox', 'felan-framework'),
									)
								),
								array(
									'title' => esc_html__('Options Value', 'felan-framework'),
									'subtitle' => esc_html__('Input each per line', 'felan-framework'),
									'id' => "select_choices",
									'type' => 'textarea',
									'default' => '',
									'required' => array(
										"field_type",
										'in',
										array('checkbox_list', 'select')
									),
								),
							)
						)
					)),
					apply_filters('felan_register_option_custom_field_service_bottom', array())
				)
			));
		}

		function additional_details_field($meta_prefix)
		{
			if (!class_exists('Felan_Framework')) {
				return array(
					'id' => "{$meta_prefix}additional_features",
					'title' => esc_html__('Additional details:', 'felan-framework'),
					'type' => 'custom',
					'default' => array(),
					'template' => FELAN_PLUGIN_DIR . '/includes/admin/templates/additional-details-field.php',
				);
			}
			return array(
				'id' => "{$meta_prefix}additional_features",
				'type' => 'repeater',
				'title' => esc_html__('Additional details:', 'felan-framework'),
				'col' => '6',
				'sort' => true,
				'fields' => array(
					array(
						'id' => "{$meta_prefix}additional_feature_title",
						'title' => esc_html__('Title:', 'felan-framework'),
						'desc' => esc_html__('Enter additional title', 'felan-framework'),
						'type' => 'text',
						'default' => '',
						'col' => '5',
					),
					array(
						'id' => "{$meta_prefix}additional_feature_value",
						'title' => esc_html__('Value', 'felan-framework'),
						'desc' => esc_html__('Enter additional value', 'felan-framework'),
						'type' => 'text',
						'default' => '',
						'col' => '7',
					),
				)
			);
		}

		/**
		 * @return mixed|void
		 */
		private function price_format_option()
		{
			return apply_filters('felan_register_option_price_format', array(
				'id' => 'felan_price_format_option',
				'title' => esc_html__('Currency Option', 'felan-framework'),
				'icon' => 'dashicons-money',
				'fields' => array_merge(
					apply_filters('felan_register_option_price_format_top', array()),
					apply_filters('felan_register_option_price_format_main', array(
						array(
							'id' => 'currency_position',
							'title' => esc_html__('Currency Sign Position', 'felan-framework'),
							'type' => 'select',
							'options' => array(
								'before' => esc_html__('Before ($59)', 'felan-framework'),
								'after' => esc_html__('After (59$)', 'felan-framework'),
							),
							'default' => 'before',
						),
						array(
							'id' => 'thousand_separator',
							'title' => esc_html__('Thousand Separator', 'felan-framework'),
							'type' => 'text',
							'default' => ',',
						),
						array(
							'id' => 'decimal_separator',
							'title' => esc_html__('Decimal Separator', 'felan-framework'),
							'type' => 'text',
							'default' => '.',
						),
						array(
							'id' => 'currency_type_default',
							'title' => esc_html__('Currency Type (Default)', 'felan-framework'),
							'type' => 'text',
							'default' => 'USD',
						),
						array(
							'id' => 'currency_sign_default',
							'title' => esc_html__('Currency Sign (Default)', 'felan-framework'),
							'type' => 'text',
							'default' => '$',
						),
						array(
							'id' => "currency_fields",
							'type' => 'panel',
							'title' => esc_html__('Currency Field', 'felan-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'id' => 'currency_type',
									'title' => esc_html__('Currency Type', 'felan-framework'),
									'type' => 'text',
									'default' => 'VND',
								),
								array(
									'id' => 'currency_sign',
									'title' => esc_html__('Currency Sign', 'felan-framework'),
									'type' => 'text',
									'default' => 'đ',
								),
								array(
									'id' => 'currency_conversion',
									'title' => esc_html__('Currency Conversion', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Convert currency values ​​based on default currency', 'felan-framework'),
									'default' => '',
								),
							)
						)
					)),
					apply_filters('felan_register_option_price_format_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function ai_helper()
		{
			return apply_filters('felan_register_option_ai_helper', array(
				'id' => 'felan_ai_helper',
				'title' => esc_html__('AI Helper', 'felan-framework'),
				'icon' => 'dashicons-smiley',
				'fields' => array_merge(
					apply_filters('felan_register_option_ai_helper_top', array()),
					apply_filters('felan_register_option_ai_helper_main', array(
						array(
							'id' => 'enable_ai_helper',
							'type' => 'button_set',
							'title' => esc_html__('Show AI Helper', 'felan-framework'),
							'subtitle' => esc_html__('Show/Hidden AI Helper in Post Job. Automatically generate your job descriptions with ChatGPT.', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'ai_type',
							'title' => esc_html__('AI Type', 'felan-framework'),
							'type' => 'select',
							'options' => array(
								'open_ai' => esc_html__('OpenAI', 'felan-framework'),
								'deep_seek' => esc_html__('DeepSeek', 'felan-framework'),
								'grok' => esc_html__('Grok', 'felan-framework'),
							),
							'default' => 'open_ai',
						),
						array(
							'id' => 'ai_key',
							'title' => esc_html__('OpenAI API Key', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'required' => array("ai_type", '=', 'open_ai'),
						),
						array(
							'id' => 'ai_model',
							'title' => esc_html__('Model', 'felan-framework'),
							'type' => 'select',
							'options' => model_ai_helper(),
							'default' => 'gpt-3.5-turbo',
							'required' => array("ai_type", '=', 'open_ai'),
						),
						array(
							'id' => 'ai_deepseek_key',
							'title' => esc_html__('DeepSeek API Key', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'required' => array("ai_type", '=', 'deep_seek'),
						),
						array(
							'id' => 'ai_deepseek_model',
							'title' => esc_html__('Model', 'felan-framework'),
							'type' => 'select',
							'options' => [
								'deepseek-chat' => 'deepseek-chat',
								'deepseek-reasoner' => 'deepseek-reasoner'
							],
							'default' => 'deepseek-chat',
							'required' => array("ai_type", '=', 'deep_seek'),
						),
						array(
							'id' => 'ai_grok_key',
							'title' => esc_html__('Grok API Key', 'felan-framework'),
							'type' => 'text',
							'default' => '',
							'required' => array("ai_type", '=', 'grok'),
						),
						array(
							'id' => 'ai_grok_model',
							'title' => esc_html__('Model', 'felan-framework'),
							'type' => 'select',
							'options' => [
								'grok-2-latest' => 'grok-2-latest',
							],
							'default' => 'grok-2-latest',
							'required' => array("ai_type", '=', 'grok'),
						),
						array(
							'id' => 'ai_temperature',
							'title' => esc_html__('Temperature', 'felan-framework'),
							'type' => 'text',
							'desc' => esc_html__('The "temperature" parameter is used to control the randomness of the generated text. A higher temperature value, such as 1.0, increases the randomness and diversity of the output, while a lower value, such as 0.2, makes the output more focused and deterministic.', 'felan-framework'),
							'default' => '0.7',
						),
						array(
							'id' => 'ai_tone',
							'title' => esc_html__('Tone', 'felan-framework'),
							'type' => 'select',
							'options' => tone_ai_helper(),
						),
						array(
							'id' => 'ai_language',
							'title' => esc_html__('Language', 'felan-framework'),
							'type' => 'select',
							'options' => language_ai_helper(),
						),
					)),
					apply_filters('felan_register_option_ai_helper_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function locations_option()
		{
			return apply_filters('felan_register_option_locations', array(
				'id' => 'felan_locations_option',
				'title' => esc_html__('Locations Option', 'felan-framework'),
				'icon' => 'dashicons-location-alt',
				'fields' => array_merge(
					apply_filters('felan_register_option_locations_top', array()),
					apply_filters('felan_register_option_locations_main', array(
						array(
							'id' => "enable_option_state",
							'type' => 'button_set',
							'title' => esc_html__('Enable State', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable State', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "enable_option_country",
							'type' => 'button_set',
							'title' => esc_html__('Enable Country', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Country', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '0',
							'required' => array("enable_option_state", '=', '1'),
						),
						array(
							'id' => "select_option_country",
							'title' => esc_html__('Country', 'felan-framework'),
							'subtitle' => esc_html__('Please Choose Country. If no country is selected will automatically take all the country', 'felan-framework'),
							'type' => 'checkbox_list',
							'options' => felan_get_countries(),
							'value_inline' => false,
							'default' => array(),
							'required' => array(
								array('enable_option_state', '=', '1'),
								array('enable_option_country', '=', '1')
							),
						),
					)),
					apply_filters('felan_register_option_locations_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function google_map_option()
		{
			return apply_filters('felan_register_option_google_map', array(
				'id' => 'felan_google_map_option',
				'title' => esc_html__('Maps Option', 'felan-framework'),
				'icon' => 'dashicons-admin-site',
				'fields' => array_merge(
					apply_filters('felan_register_option_google_map_top', array()),
					apply_filters('felan_register_option_google_map_main', array(
						array(
							'id' => 'map_effects',
							'title' => esc_html__('Maps Effects', 'felan-framework'),
							'type' => 'select',
							'options' => array(
								'' => esc_html__('None', 'felan-framework'),
								'shine' => esc_html__('Shine', 'felan-framework'),
								'popup' => esc_html__('Popup', 'felan-framework'),
							),
							'default' => 'shine',
						),
						array(
							'id' => 'map_type',
							'title' => esc_html__('Maps Type', 'felan-framework'),
							'type' => 'select',
							'options' => array(
								'google_map' => esc_html__('Google Map', 'felan-framework'),
								'mapbox' => esc_html__('Mapbox', 'felan-framework'),
								'openstreetmap' => esc_html__('OpenStreetMap', 'felan-framework'),
							),
							'default' => 'mapbox',
						),
						array(
							'id' => 'map_ssl',
							'title' => esc_html__('Maps SSL', 'felan-framework'),
							'subtitle' => esc_html__('Use maps with ssl', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'googlemap_type',
							'title' => esc_html__('Google Maps Type', 'felan-framework'),
							'type' => 'select',
							'options' => array(
								'roadmap' => esc_html__('Roadmap', 'felan-framework'),
								'satellite' => esc_html__('Satellite', 'felan-framework'),
								'hybrid' => esc_html__('Hybrid', 'felan-framework'),
								'terrain' => esc_html__('Terrain', 'felan-framework'),
							),
							'default' => 'roadmap',
							'required' => array("map_type", '=', 'google_map'),
						),
						array(
							'id' => 'googlemap_api_key',
							'type' => 'text',
							'title' => esc_html__('Google Maps API KEY', 'felan-framework'),
							'subtitle' => esc_html__('Enter your google maps api key', 'felan-framework'),
							'default' => 'AIzaSyBvPDNG6pePr9iFpeRKaOlaZF_l0oT3lWk',
							'required' => array("map_type", '=', 'google_map'),
						),
						array(
							'id' => 'mapbox_api_key',
							'type' => 'text',
							'title' => esc_html__('Mapbox API KEY', 'felan-framework'),
							'subtitle' => esc_html__('Enter your mapbox api key', 'felan-framework'),
							'default' => 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw',
							'required' => array("map_type", '=', 'mapbox'),
						),
						array(
							'id' => 'openstreetmap_api_key',
							'type' => 'text',
							'title' => esc_html__('OpenStreetMap API KEY', 'felan-framework'),
							'subtitle' => esc_html__('Enter your OpenStreetMap api key', 'felan-framework'),
							'default' => 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw',
							'required' => array("map_type", '=', 'openstreetmap'),
						),
						array(
							'id' => 'map_pin_cluster',
							'title' => esc_html__('Pin Cluster', 'felan-framework'),
							'subtitle' => esc_html__('Use pin cluster on map', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
							'required' => array("map_type", '=', 'google_map'),
						),
						array(
							'id' => 'googlemap_style',
							'type' => 'ace_editor',
							'title' => esc_html__('Style for Google Map', 'felan-framework'),
							'subtitle' => sprintf(
								__('Use %s https://snazzymaps.com/ %s to create styles', 'felan-framework'),
								'<a href="https://snazzymaps.com/" target="_blank">',
								'</a>'
							),
							'default' => '',
							'required' => array("map_type", '=', 'google_map'),
						),
						array(
							'id' => 'mapbox_style',
							'title' => esc_html__('Style for Mapbox', 'felan-framework'),
							'type' => 'select',
							'options' => array(
								'streets-v11' => esc_html__('Streets', 'felan-framework'),
								'light-v10' => esc_html__('Light', 'felan-framework'),
								'dark-v10' => esc_html__('Dark', 'felan-framework'),
								'outdoors-v11' => esc_html__('Outdoors', 'felan-framework'),
								'satellite-v9' => esc_html__('Satellite', 'felan-framework'),
							),
							'required' => array("map_type", '=', 'mapbox'),
						),
						array(
							'id' => 'openstreetmap_style',
							'title' => esc_html__('Style for OpenStreetMap', 'felan-framework'),
							'type' => 'select',
							'options' => array(
								'streets-v11' => esc_html__('Streets', 'felan-framework'),
								'light-v10' => esc_html__('Light', 'felan-framework'),
								'dark-v10' => esc_html__('Dark', 'felan-framework'),
								'outdoors-v11' => esc_html__('Outdoors', 'felan-framework'),
								'satellite-v9' => esc_html__('Satellite', 'felan-framework'),
							),
							'required' => array("map_type", '=', 'openstreetmap'),
						),
						array(
							'id' => 'map_zoom_level',
							'type' => 'text',
							'title' => esc_html__('Default Map Zoom', 'felan-framework'),
							'default' => '3'
						),
						array(
							'id' => 'map_lat_default',
							'type' => 'text',
							'title' => esc_html__('Default Map Latitude', 'felan-framework'),
							'default' => '59.325'
						),
						array(
							'id' => 'map_lng_default',
							'type' => 'text',
							'title' => esc_html__('Default Map Longitude ', 'felan-framework'),
							'default' => '18.070'
						),
					)),
					apply_filters('felan_register_option_google_map_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function payment_option()
		{
			$option_payment_service = $option_payment_project = array();
			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			$title_freelancers = esc_html__('Freelancer Settings', 'felan-framework');
			$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
			if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') {
				$title_freelancers = esc_html__('Candidate Settings', 'felan-framework');
			}

			if ($enable_post_type_service == '1') {
				$option_payment_service = array(
					'id' => 'felan_main_group',
					'type' => 'group',
					'title' => esc_html__('Service Settings', 'felan-framework'),
					'fields' => array(
						array(
							'id' => 'felan_service_paypal',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Paypal Setting', 'felan-framework'),
						),
						array(
							'id' => 'service_enable_paypal',
							'title' => esc_html__('Enable Paypal', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => 'service_paypal_api',
							'type' => 'select',
							'required' => array(
								array('service_enable_paypal', '=', '1'),
							),
							'title' => esc_html__('Paypal Api', 'felan-framework'),
							'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'felan-framework'),
							'desc' => esc_html__('Update PayPal settings according to API type selection', 'felan-framework'),
							'options' => array(
								'sandbox' => esc_html__('Sandbox', 'felan-framework'),
								'live' => esc_html__('Live', 'felan-framework')
							),
							'default' => 'sandbox',
						),
						array(
							'id' => 'service_paypal_client_id',
							'type' => 'text',
							'required' => array(
								array('service_enable_paypal', '=', '1'),
							),
							'title' => esc_html__('Paypal Client ID', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'service_paypal_client_secret_key',
							'type' => 'text',
							'required' => array(
								array('service_enable_paypal', '=', '1'),
							),
							'title' => esc_html__('Paypal Client Secret Key', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'felan_service_stripe',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Stripe Setting', 'felan-framework'),
						),
						array(
							'id' => 'service_enable_stripe',
							'title' => esc_html__('Enable Stripe', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => 'service_stripe_secret_key',
							'type' => 'text',
							'required' => array(
								array('service_enable_stripe', '=', '1'),
							),
							'title' => esc_html__('Stripe Secret Key', 'felan-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
							'default' => '',
						),
						array(
							'id' => 'service_tripe_publishable_key',
							'type' => 'text',
							'required' => array(
								array('service_enable_stripe', '=', '1'),
							),
							'title' => esc_html__('Stripe Publishable Key', 'felan-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
							'default' => '',
						),
						array(
							'id' => 'felan_service_razor',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Razor Setting', 'felan-framework'),
						),
						array(
							'id' => 'service_enable_razor',
							'title' => esc_html__('Enable Razor', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'service_razor_key_id',
							'type' => 'text',
							'required' => array(
								array('service_enable_razor', '=', '1'),
							),
							'title' => esc_html__('Razor Key ID', 'felan-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.razorpay.com/', 'felan-framework'),
							'default' => '',
						),
						array(
							'id' => 'service_razor_key_secret',
							'type' => 'text',
							'required' => array(
								array('service_enable_razor', '=', '1'),
							),
							'title' => esc_html__('Razor Key Secret', 'felan-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.razorpay.com/', 'felan-framework'),
							'default' => '',
						),
						array(
							'id' => 'felan_service_wire_transfer',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Wire Transfer Setting', 'felan-framework'),
						),
						array(
							'id' => 'service_enable_wire_transfer',
							'title' => esc_html__('Enable Wire Transfer', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => 'service_wire_transfer_card_number',
							'type' => 'text',
							'required' => array(
								array('service_enable_wire_transfer', '=', '1'),
							),
							'title' => esc_html__('Card Number', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'service_wire_transfer_card_name',
							'type' => 'text',
							'required' => array(
								array('service_enable_wire_transfer', '=', '1'),
							),
							'title' => esc_html__('Card Name', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'service_wire_transfer_bank_name',
							'type' => 'text',
							'required' => array(
								array('service_enable_wire_transfer', '=', '1'),
							),
							'title' => esc_html__('Bank Name', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'felan_service_woocheckout',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Woocommerce Setting', 'felan-framework'),
						),
						array(
							'id' => 'service_enable_woocheckout',
							'title' => esc_html__('Enable Woocommerce Checkout', 'felan-framework'),
							'type' => 'button_set',
							'subtitle' => esc_html__('Works when you activate plugin woocomerce and checkout page', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '0',
						),
					),
				);
			}

			if ($enable_post_type_project == '1') {
				$option_payment_project = array(
					'id' => 'felan_main_group',
					'type' => 'group',
					'title' => esc_html__('Project Settings', 'felan-framework'),
					'fields' => array(
						array(
							'id' => 'felan_project_paypal',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Paypal Setting', 'felan-framework'),
						),
						array(
							'id' => 'project_enable_paypal',
							'title' => esc_html__('Enable Paypal', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'project_paypal_api',
							'type' => 'select',
							'required' => array(
								array('project_enable_paypal', '=', '1'),
							),
							'title' => esc_html__('Paypal Api', 'felan-framework'),
							'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'felan-framework'),
							'desc' => esc_html__('Update PayPal settings according to API type selection', 'felan-framework'),
							'options' => array(
								'sandbox' => esc_html__('Sandbox', 'felan-framework'),
								'live' => esc_html__('Live', 'felan-framework')
							),
							'default' => 'sandbox',
						),
						array(
							'id' => 'project_paypal_client_id',
							'type' => 'text',
							'required' => array(
								array('project_enable_paypal', '=', '1'),
							),
							'title' => esc_html__('Paypal Client ID', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'project_paypal_client_secret_key',
							'type' => 'text',
							'required' => array(
								array('project_enable_paypal', '=', '1'),
							),
							'title' => esc_html__('Paypal Client Secret Key', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'felan_project_stripe',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Stripe Setting', 'felan-framework'),
						),
						array(
							'id' => 'project_enable_stripe',
							'title' => esc_html__('Enable Stripe', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'project_stripe_secret_key',
							'type' => 'text',
							'required' => array(
								array('project_enable_stripe', '=', '1'),
							),
							'title' => esc_html__('Stripe Secret Key', 'felan-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
							'default' => '',
						),
						array(
							'id' => 'project_tripe_publishable_key',
							'type' => 'text',
							'required' => array(
								array('project_enable_stripe', '=', '1'),
							),
							'title' => esc_html__('Stripe Publishable Key', 'felan-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
							'default' => '',
						),
						array(
							'id' => 'felan_project_razor',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Razor Setting', 'felan-framework'),
						),
						array(
							'id' => 'project_enable_razor',
							'title' => esc_html__('Enable Razor', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'project_razor_key_id',
							'type' => 'text',
							'required' => array(
								array('project_enable_razor', '=', '1'),
							),
							'title' => esc_html__('Razor Key ID', 'felan-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
							'default' => '',
						),
						array(
							'id' => 'project_razor_key_secret',
							'type' => 'text',
							'required' => array(
								array('project_enable_razor', '=', '1'),
							),
							'title' => esc_html__('Razor Key Secret', 'felan-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
							'default' => '',
						),
						array(
							'id' => 'felan_project_wire_transfer',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Wire Transfer Setting', 'felan-framework'),
						),
						array(
							'id' => 'project_enable_wire_transfer',
							'title' => esc_html__('Enable Wire Transfer', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'project_wire_transfer_card_number',
							'type' => 'text',
							'required' => array(
								array('project_enable_wire_transfer', '=', '1'),
							),
							'title' => esc_html__('Card Number', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'project_wire_transfer_card_name',
							'type' => 'text',
							'required' => array(
								array('project_enable_wire_transfer', '=', '1'),
							),
							'title' => esc_html__('Card Name', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'project_wire_transfer_bank_name',
							'type' => 'text',
							'required' => array(
								array('project_enable_wire_transfer', '=', '1'),
							),
							'title' => esc_html__('Bank Name', 'felan-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'felan_project_woocheckout',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Woocommerce Setting', 'felan-framework'),
						),
						array(
							'id' => 'project_enable_woocheckout',
							'title' => esc_html__('Enable Woocommerce Checkout', 'felan-framework'),
							'type' => 'button_set',
							'subtitle' => esc_html__('Works when you activate plugin woocomerce and checkout page', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('Enabled', 'felan-framework'),
								'0' => esc_html__('Disabled', 'felan-framework'),
							),
							'default' => '0',
						),
					),
				);
			}

			return apply_filters('felan_register_option_payment', array(
				'id' => 'felan_payment_option',
				'title' => esc_html__('Payment Option', 'felan-framework'),
				'icon' => 'dashicons-cart',
				'fields' => array(
					//Employer
					apply_filters('felan_register_option_payment_top', array()),
					apply_filters('felan_register_option_payment_main', array(
						'id' => 'felan_main_group',
						'type' => 'group',
						'title' => esc_html__('Employer Settings', 'felan-framework'),
						'fields' => array(
							array(
								'id' => 'paid_submission_type',
								'type' => 'select',
								'title' => esc_html__('Paid Submission Type', 'felan-framework'),
								'subtitle' => '',
								'options' => array(
									'no' => esc_html__('Free Submit', 'felan-framework'),
									'per_package' => esc_html__('Pay Per Package', 'felan-framework')
								),
								'default' => 'no',
							),
							array(
								'id' => 'felan_paypal',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Paypal Setting', 'felan-framework'),
								'required' => array('paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'enable_paypal',
								'title' => esc_html__('Enable Paypal', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'paypal_api',
								'type' => 'select',
								'required' => array(
									array('enable_paypal', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Paypal Api', 'felan-framework'),
								'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'felan-framework'),
								'desc' => esc_html__('Update PayPal settings according to API type selection', 'felan-framework'),
								'options' => array(
									'sandbox' => esc_html__('Sandbox', 'felan-framework'),
									'live' => esc_html__('Live', 'felan-framework')
								),
								'default' => 'sandbox',
							),
							array(
								'id' => 'paypal_client_id',
								'type' => 'text',
								'required' => array(
									array('enable_paypal', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Paypal Client ID', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'paypal_client_secret_key',
								'type' => 'text',
								'required' => array(
									array('enable_paypal', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Paypal Client Secret Key', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'felan_stripe',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Stripe Setting', 'felan-framework'),
								'required' => array('paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'enable_stripe',
								'title' => esc_html__('Enable Stripe', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'stripe_secret_key',
								'type' => 'text',
								'required' => array(
									array('enable_stripe', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Stripe Secret Key', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
								'default' => '',
							),
							array(
								'id' => 'stripe_publishable_key',
								'type' => 'text',
								'required' => array(
									array('enable_stripe', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Stripe Publishable Key', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
								'default' => '',
							),
							array(
								'id' => 'felan_razor',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Razor Setting', 'felan-framework'),
								'required' => array('paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'enable_razor',
								'title' => esc_html__('Enable Razor', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'razor_key_id',
								'type' => 'text',
								'required' => array(
									array('enable_razor', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Razor Key ID', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.razorpay.com/', 'felan-framework'),
								'default' => '',
							),
							array(
								'id' => 'razor_key_secret',
								'type' => 'text',
								'required' => array(
									array('enable_razor', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Razor Key Secret', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.razorpay.com/', 'felan-framework'),
								'default' => '',
							),
							array(
								'id' => 'felan_wire_transfer',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Wire Transfer Setting', 'felan-framework'),
								'required' => array('paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'enable_wire_transfer',
								'title' => esc_html__('Enable Wire Transfer', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'wire_transfer_card_number',
								'type' => 'text',
								'required' => array(
									array('enable_wire_transfer', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Card Number', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'wire_transfer_card_name',
								'type' => 'text',
								'required' => array(
									array('enable_wire_transfer', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Card Name', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'wire_transfer_bank_name',
								'type' => 'text',
								'required' => array(
									array('enable_wire_transfer', '=', '1'),
									array('paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Bank Name', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'felan_woocheckout',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Woocommerce Setting', 'felan-framework'),
								'required' => array('paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'enable_woocheckout',
								'title' => esc_html__('Enable Woocommerce Checkout', 'felan-framework'),
								'type' => 'button_set',
								'subtitle' => esc_html__('Works when you activate plugin woocomerce and checkout page', 'felan-framework'),
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('paid_submission_type', '!=', 'no'),
							),
						),
					)),
					apply_filters('felan_register_option_payment_bottom', array()),

					//Freelancer
					apply_filters('felan_register_option_payment_freelancer_top', array()),
					apply_filters('felan_register_option_payment_freelancer_main', array(
						'id' => 'felan_main_group',
						'type' => 'group',
						'title' => $title_freelancers,
						'fields' => array(
							array(
								'id' => 'freelancer_paid_submission_type',
								'type' => 'select',
								'title' => esc_html__('Paid Submission Type', 'felan-framework'),
								'subtitle' => '',
								'options' => array(
									'no' => esc_html__('Free Submit', 'felan-framework'),
									'freelancer_per_package' => esc_html__('Pay Per Package', 'felan-framework')
								),
								'default' => 'no',
							),
							array(
								'id' => 'felan_freelancer_paypal',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Paypal Setting', 'felan-framework'),
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'freelancer_enable_paypal',
								'title' => esc_html__('Enable Paypal', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'freelancer_paypal_api',
								'type' => 'select',
								'required' => array(
									array('freelancer_enable_paypal', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Paypal Api', 'felan-framework'),
								'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'felan-framework'),
								'desc' => esc_html__('Update PayPal settings according to API type selection', 'felan-framework'),
								'options' => array(
									'sandbox' => esc_html__('Sandbox', 'felan-framework'),
									'live' => esc_html__('Live', 'felan-framework')
								),
								'default' => 'sandbox',
							),
							array(
								'id' => 'freelancer_paypal_client_id',
								'type' => 'text',
								'required' => array(
									array('freelancer_enable_paypal', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Paypal Client ID', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'freelancer_paypal_client_secret_key',
								'type' => 'text',
								'required' => array(
									array('freelancer_enable_paypal', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Paypal Client Secret Key', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'felan_freelancer_stripe',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Stripe Setting', 'felan-framework'),
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'freelancer_enable_stripe',
								'title' => esc_html__('Enable Stripe', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'freelancer_stripe_secret_key',
								'type' => 'text',
								'required' => array(
									array('freelancer_enable_stripe', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Stripe Secret Key', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
								'default' => '',
							),
							array(
								'id' => 'freelancer_tripe_publishable_key',
								'type' => 'text',
								'required' => array(
									array('freelancer_enable_stripe', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Stripe Publishable Key', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
								'default' => '',
							),
							array(
								'id' => 'felan_freelancer_razor',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Razor Setting', 'felan-framework'),
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'freelancer_enable_razor',
								'title' => esc_html__('Enable Razor', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'freelancer_razor_key_id',
								'type' => 'text',
								'required' => array(
									array('freelancer_enable_razor', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Razor Key ID', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.razorpay.com/', 'felan-framework'),
								'default' => '',
							),
							array(
								'id' => 'freelancer_razor_key_secret',
								'type' => 'text',
								'required' => array(
									array('freelancer_enable_razor', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Razor Key Secret', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.razorpay.com/', 'felan-framework'),
								'default' => '',
							),
							array(
								'id' => 'felan_freelancer_wire_transfer',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Wire Transfer Setting', 'felan-framework'),
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'freelancer_enable_wire_transfer',
								'title' => esc_html__('Enable Wire Transfer', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'freelancer_wire_transfer_card_number',
								'type' => 'text',
								'required' => array(
									array('freelancer_enable_wire_transfer', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Card Number', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'freelancer_wire_transfer_card_name',
								'type' => 'text',
								'required' => array(
									array('freelancer_enable_wire_transfer', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Card Name', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'freelancer_wire_transfer_bank_name',
								'type' => 'text',
								'required' => array(
									array('freelancer_enable_wire_transfer', '=', '1'),
									array('freelancer_paid_submission_type', '!=', 'no')
								),
								'title' => esc_html__('Bank Name', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
							),
							array(
								'id' => 'felan_freelancer_woocheckout',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Woocommerce Setting', 'felan-framework'),
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
							array(
								'id' => 'freelancer_enable_woocheckout',
								'title' => esc_html__('Enable Woocommerce Checkout', 'felan-framework'),
								'type' => 'button_set',
								'subtitle' => esc_html__('Works when you activate plugin woocomerce and checkout page', 'felan-framework'),
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
								'required' => array('freelancer_paid_submission_type', '!=', 'no'),
							),
						),
					)),
					apply_filters('felan_register_option_payment_freelancer_bottom', array()),

					//Service
					apply_filters('felan_register_option_payment_service_top', array()),
					apply_filters(
						'felan_register_option_payment_service_main',
						$option_payment_service
					),
					apply_filters('felan_register_option_payment_service_bottom', array()),


					//Project
					apply_filters('felan_register_option_payment_project_top', array()),
					apply_filters(
						'felan_register_option_payment_project_main',
						$option_payment_project
					),
					apply_filters('felan_register_option_payment_project_bottom', array()),
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function login_option()
		{
			return apply_filters('felan_register_option_login', array(
				'id' => 'felan_login_option',
				'title' => esc_html__('Login/Register', 'felan-framework'),
				'icon' => 'dashicons-admin-users',
				'fields' => array(

					//General Login
					apply_filters('felan_register_option_genera_login_page_top', array()),
					apply_filters('felan_register_option_genera_login_page_main', array(
						'id' => 'felan_login_general_group',
						'type' => 'group',
						'title' => esc_html__('General Option', 'felan-framework'),
						'fields' => array(
							array(
								'id' => 'enable_user_name_after_login',
								'type' => 'button_set',
								'title' => esc_html__('Enable User Name After Login', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable User Name After Login', 'felan-framework'),
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '1'
							),
							array(
								'id' => 'enable_redirect_after_login',
								'type' => 'button_set',
								'title' => esc_html__('Enable Redirect After Login', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable Redirect After Login', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '0'
							),
							array(
								'id' => 'redirect_for_admin',
								'title' => esc_html__('Redirect For Admin', 'felan-framework'),
								'subtitle' => esc_html__('Select redirect page after admin login.', 'felan-framework'),
								'type' => 'select',
								'data' => 'page',
								'data_args' => array(
									'numberposts' => -1,
								),
								'required' => array('enable_redirect_after_login', '!=', '0'),
							),
							array(
								'id' => 'redirect_for_freelancer',
								'title' => esc_html__('Redirect For Freelancer', 'felan-framework'),
								'subtitle' => esc_html__('Select redirect page after freelancer login.', 'felan-framework'),
								'type' => 'select',
								'data' => 'page',
								'data_args' => array(
									'numberposts' => -1,
								),
								'required' => array('enable_redirect_after_login', '!=', '0'),
							),
							array(
								'id' => 'redirect_for_employer',
								'title' => esc_html__('Redirect For Employer', 'felan-framework'),
								'subtitle' => esc_html__('Select redirect page after employer login.', 'felan-framework'),
								'type' => 'select',
								'data' => 'page',
								'data_args' => array(
									'numberposts' => -1,
								),
								'required' => array('enable_redirect_after_login', '!=', '0'),
							),
							array(
								'id' => 'enable_user_role',
								'type' => 'button_set',
								'title' => esc_html__('Enable User Role', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable User Role In Form Register', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '1'
							),
							array(
								'id' => 'enable_default_user_role',
								'type' => 'button_set',
								'title' => esc_html__('Enable Default User Role', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable Default User Role', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'freelancer' => esc_html__('Freelancer', 'felan-framework'),
									'employer' => esc_html__('Employer', 'felan-framework'),
								),
								'default' => 'freelancer',
							),
						),
					)),
					apply_filters('felan_register_option_genera_login_page_bottom', array()),

					//Verify Login
					apply_filters('felan_register_option_verify_login_page_top', array()),
					apply_filters('felan_register_option_verify_login_page_main', array(
						'id' => 'felan_login_verify_group',
						'type' => 'group',
						'title' => esc_html__('Verify Option', 'felan-framework'),
						'fields' => array(
							array(
								'id' => 'enable_status_user',
								'type' => 'button_set',
								'title' => esc_html__('Enable Verify Your Account Information', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable Verify Your Account Information (Status) After Register', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '0'
							),
							array(
								'id' => 'enable_captcha',
								'type' => 'button_set',
								'title' => esc_html__('Enable Verify Captcha', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable Verify Captcha', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '0'
							),
							array(
								'id' => 'enable_verify_user',
								'type' => 'button_set',
								'title' => esc_html__('Enable Verify Gmail', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable Verify Gmail After Register', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '0'
							),
							array(
								'id' => "verify_user_time",
								'title' => esc_html__('Verification Expiration Time', 'felan-framework'),
								'subtitle' => esc_html__('Enter the expiration time of the verification code (second)', 'felan-framework'),
								'default' => '900',
								'type' => 'text',
								'required' => array("enable_verify_user", '!=', '0'),
							),
							//                            array(
							//                                'id' => 'enable_verify_phone',
							//                                'type' => 'button_set',
							//                                'title' => esc_html__('Enable Verify Phone Number', 'felan-framework'),
							//                                'subtitle' => esc_html__('Enable/Disable Verify Phone Number After Register', 'felan-framework'),
							//                                'desc' => '',
							//                                'options' => array(
							//                                    '1' => esc_html__('On', 'felan-framework'),
							//                                    '0' => esc_html__('Off', 'felan-framework'),
							//                                ),
							//                                'default' => '0'
							//                            ),
							//                            array(
							//                                'id' => "verify_phone_sid",
							//                                'title' => esc_html__('Account SID', 'felan-framework'),
							//                                'subtitle' => esc_html__('Enter account sid', 'felan-framework'),
							//                                'default' => '',
							//                                'type' => 'text',
							//                                'required' => array("enable_verify_phone", '!=', '0'),
							//                            ),
							//                            array(
							//                                'id' => "verify_phone_token",
							//                                'title' => esc_html__('Auth Token', 'felan-framework'),
							//                                'subtitle' => esc_html__('Enter auth token', 'felan-framework'),
							//                                'default' => '',
							//                                'type' => 'text',
							//                                'required' => array("enable_verify_phone", '!=', '0'),
							//                            ),
							//                            array(
							//                                'id' => "verify_phone_my_twilio",
							//                                'title' => esc_html__('My Twilio Phone Number', 'felan-framework'),
							//                                'subtitle' => esc_html__('Enter my twilio phone number', 'felan-framework'),
							//                                'default' => '',
							//                                'type' => 'text',
							//                                'required' => array("enable_verify_phone", '!=', '0'),
							//                            ),
						),
					)),
					apply_filters('felan_register_option_verify_login_page_bottom', array()),

					//Social Login
					apply_filters('felan_register_option_social_login_page_top', array()),
					apply_filters('felan_register_option_social_login_page_main', array(
						'id' => 'felan_login_social_group',
						'type' => 'group',
						'title' => esc_html__('Social Option', 'felan-framework'),
						'fields' => array(
							array(
								'id' => 'enable_social_login',
								'type' => 'button_set',
								'title' => esc_html__('Enable Social Login', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable Social Login', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '1'
							),
							array(
								'id' => 'enable_google_login',
								'type' => 'button_set',
								'title' => esc_html__('Enable Google Login', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable Google Login', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '1',
								'required' => array("enable_social_login", '=', '1'),
							),
							array(
								'id' => 'google_login_api',
								'type' => 'text',
								'title' => esc_html__('Google Login API', 'felan-framework'),
								'subtitle' => esc_html__('Enter your google login api key'),
								'default' => '128596758374-b8gac0mc9rjn5gnb96q0ma30naojdrcg.apps.googleusercontent.com',
								'required' => array(
									array('enable_social_login', '=', '1'),
									array('enable_google_login', '=', '1')
								),
							),
							array(
								'id' => 'enable_facebook_login',
								'type' => 'button_set',
								'title' => esc_html__('Enable Facebook Login', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable Facebook Login', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '1',
								'required' => array("enable_social_login", '=', '1'),
							),
							array(
								'id' => 'facebook_app_id',
								'type' => 'text',
								'title' => esc_html__('Facebook Login API', 'felan-framework'),
								'subtitle' => esc_html__('Enter your facebook login api key'),
								'default' => '1270446883532471',
								'required' => array(
									array('enable_social_login', '=', '1'),
									array('enable_facebook_login', '=', '1')
								),
							),
							array(
								'id' => 'enable_linkedin_login',
								'type' => 'button_set',
								'title' => esc_html__('Enable Linkedin Login', 'felan-framework'),
								'subtitle' => esc_html__('Enable/Disable Linkedin Login', 'felan-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'felan-framework'),
									'0' => esc_html__('Off', 'felan-framework'),
								),
								'default' => '1',
								'required' => array("enable_social_login", '=', '1'),
							),
							array(
								'id' => 'linkedin_client_id',
								'type' => 'text',
								'title' => esc_html__('Linkedin Client ID', 'felan-framework'),
								'subtitle' => esc_html__('Enter your linkedin client id'),
								'default' => '',
								'required' => array(
									array('enable_social_login', '=', '1'),
									array('enable_linkedin_login', '=', '1')
								),
							),
							array(
								'id' => 'linkedin_client_secret',
								'type' => 'text',
								'title' => esc_html__('Linkedin Client Secret', 'felan-framework'),
								'subtitle' => esc_html__('Enter your linkedin client secret'),
								'default' => '',
								'required' => array(
									array('enable_social_login', '=', '1'),
									array('enable_linkedin_login', '=', '1')
								),
							),
						),
					)),
					apply_filters('felan_register_option_social_login_page_bottom', array()),
				)
			));
		}

		/**
		 * Social network
		 * @return mixed
		 */

		private function social_network()
		{
			return apply_filters('felan_register_social_option', array(
				'id' => 'felan_social_option',
				'title' => esc_html__('Social Network', 'felan-framework'),
				'icon' => 'dashicons dashicons-networking',
				'fields' => array_merge(
					apply_filters('felan_register_social_option_top', array()),
					array(
						array(
							'id' => "enable_social_twitter",
							'type' => 'button_set',
							'title' => esc_html__('Enable Twitter', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Twitter', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "enable_social_linkedin",
							'type' => 'button_set',
							'title' => esc_html__('Enable Linkedin', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Linkedin', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "enable_social_facebook",
							'type' => 'button_set',
							'title' => esc_html__('Enable Facebook', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Facebook', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "enable_social_instagram",
							'type' => 'button_set',
							'title' => esc_html__('Enable Instagram', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Instagram', 'felan-framework'),
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "felan_social_fields",
							'type' => 'panel',
							'title' => esc_html__('Social Field', 'felan-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'id' => 'social_name',
									'title' => esc_html__('Social Name', 'felan-framework'),
									'type' => 'text',
								),
								array(
									'id' => 'social_icon',
									'title' => esc_html__('Social Icon', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
							)
						)
					),
					apply_filters('felan_register_social_option_bottom', array())
				),
			));
		}

		/**
		 * @return mixed|void
		 */
		private function user_option()
		{
			$user_navigation_employer_show_servive = $user_navigation_employer_image_service = $user_navigation_freelancer_image_service = $user_navigation_freelancer_show_servive
				= $user_navigation_employer_show_meetings = $user_navigation_employer_image_meetings = $user_navigation_freelancer_show_meetings = $user_navigation_freelancer_image_meetings
				= $user_navigation_employer_show_jobs = $user_navigation_employer_image_jobs = $user_navigation_freelancer_show_jobs = $user_navigation_freelancer_image_jobs
				= $user_navigation_employer_show_project = $user_navigation_employer_image_project = $user_navigation_freelancer_show_project = $user_navigation_freelancer_image_project
				= $user_navigation_employer_show_disputes = $user_navigation_employer_image_disputes = $user_navigation_freelancer_show_disputes = $user_navigation_freelancer_image_disputes
				= $user_navigation_employer_show_user_package = $user_navigation_employer_image_user_package = $user_navigation_freelancer_show_user_package = $user_navigation_freelancer_image_user_package
				= $user_navigation_freelancer_show_user_package = $user_navigation_freelancer_show_user_package = $user_navigation_employer_show_wallet = $user_navigation_employer_image_wallet = array();

			//Jobs
			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			if ($enable_post_type_jobs == '1') {
				$user_navigation_employer_show_jobs = array(
					'id' => 'show_employer_jobs_dashboard',
					'type' => 'button_set',
					'title' => esc_html__('Show "Jobs"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "Jobs" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_employer_image_jobs =  array(
					'id' => 'image_employer_jobs_dashboard',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon Jobs', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for jobs', 'felan-framework'),
					'required' => array('show_employer_jobs_dashboard', '!=', '0'),
				);

				$user_navigation_freelancer_show_jobs = array(
					'id' => 'show_my_jobs',
					'type' => 'button_set',
					'title' => esc_html__('Show "My Jobs"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "My Jobs" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_freelancer_image_jobs = array(
					'id' => 'image_my_jobs',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon My Jobs', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for My Jobs', 'felan-framework'),
					'required' => array('show_my_jobs', '!=', '0'),
				);

				$user_navigation_employer_show_meetings = array(
					'id' => 'show_employer_meetings',
					'type' => 'button_set',
					'title' => esc_html__('Show "Meetings"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "Meetings" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_employer_image_meetings = array(
					'id' => 'image_employer_meetings',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon Meetings', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for meetings', 'felan-framework'),
					'required' => array('show_employer_meetings', '!=', '0'),
				);

				$user_navigation_freelancer_show_meetings = array(
					'id' => 'show_freelancer_meetings',
					'type' => 'button_set',
					'title' => esc_html__('Show "Meetings"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "Meetings" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_freelancer_image_meetings = array(
					'id' => 'image_freelancer_meetings',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon Meetings', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for meetings', 'felan-framework'),
					'required' => array('show_freelancer_meetings', '!=', '0'),
				);
			}

			//Service
			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			if ($enable_post_type_service == '1') {
				$user_navigation_employer_show_servive = array(
					'id' => 'show_employer_employer_service',
					'type' => 'button_set',
					'title' => esc_html__('Show "Services"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "Services" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_employer_image_service = array(
					'id' => 'image_employer_employer_service',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon Services', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for services', 'felan-framework'),
					'required' => array('show_employer_employer_service', '!=', '0'),
				);

				$user_navigation_freelancer_show_servive = array(
					'id' => 'show_freelancer_service',
					'type' => 'button_set',
					'title' => esc_html__('Show "My Service"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "My Service" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_freelancer_image_service = array(
					'id' => 'image_freelancer_service',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon My Service', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for My Service', 'felan-framework'),
					'required' => array('show_freelancer_service', '!=', '0'),
				);
			}

			//Project
			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			if ($enable_post_type_project == '1') {
				$user_navigation_employer_show_project = array(
					'id' => 'show_employer_projects',
					'type' => 'button_set',
					'title' => esc_html__('Show "projects"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide projects"', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_employer_image_project = array(
					'id' => 'image_employer_projects',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon Projects', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for jobs', 'felan-framework'),
					'required' => array('show_employer_projects', '!=', '0'),
				);

				$user_navigation_employer_show_proposal = array(
					'id' => 'show_employer_proposal',
					'type' => 'button_set',
					'title' => esc_html__('Show Proposal', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide Proposal on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_employer_image_proposal = array(
					'id' => 'image_employer_proposal',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon Proposal', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for proposal', 'felan-framework'),
					'required' => array('show_employer_proposal', '!=', '0'),
				);

				$user_navigation_freelancer_show_project = array(
					'id' => 'show_my_project',
					'type' => 'button_set',
					'title' => esc_html__('Show "My Project"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "My Project" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_freelancer_image_project = array(
					'id' => 'image_my_project',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon My Project', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for My Project', 'felan-framework'),
					'required' => array('show_my_project', '!=', '0'),
				);
			}

			//Disputes
			if ($enable_post_type_project == '1' || $enable_post_type_service == '1') {
				$user_navigation_employer_show_disputes = array(
					'id' => 'show_employer_disputes',
					'type' => 'button_set',
					'title' => esc_html__('Show "Disputes"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "Disputes" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_employer_image_disputes = array(
					'id' => 'image_employer_disputes',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon Disputes', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for Disputes', 'felan-framework'),
					'required' => array('show_employer_disputes', '!=', '0'),
				);

				$user_navigation_freelancer_show_disputes =  array(
					'id' => 'show_freelancer_disputes',
					'type' => 'button_set',
					'title' => esc_html__('Show "Disputes"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "Disputes" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_freelancer_image_disputes =   array(
					'id' => 'image_freelancer_disputes',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon Disputes', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for disputes', 'felan-framework'),
					'required' => array('show_freelancer_disputes', '!=', '0'),
				);
			}

			$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
			if ($enable_wallet_mode == '0') {
				$user_navigation_employer_show_user_package = array(
					'id' => 'show_employer_user_package',
					'type' => 'button_set',
					'title' => esc_html__('Show "User Package"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "User Package" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_employer_image_user_package = array(
					'id' => 'image_employer_user_package',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon User Package', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for User Package', 'felan-framework'),
					'required' => array('show_employer_user_package', '!=', '0'),
				);

				$user_navigation_freelancer_show_user_package =  array(
					'id' => 'show_freelancer_user_package',
					'type' => 'button_set',
					'title' => esc_html__('Show "Package"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "Package" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);

				$user_navigation_freelancer_image_user_package = array(
					'id' => 'image_freelancer_user_package',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon User Package', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for User Package', 'felan-framework'),
					'required' => array('show_freelancer_user_package', '!=', '0'),
				);
			}

			if ($enable_wallet_mode == '1') {
				$user_navigation_employer_show_wallet = array(
					'id' => 'show_employer_wallet',
					'type' => 'button_set',
					'title' => esc_html__('Show "Wallet"', 'felan-framework'),
					'subtitle' => esc_html__('Show/Hide "Wallet" on navigation', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1'
				);
				$user_navigation_employer_image_wallet = array(
					'id' => 'image_employer_wallet',
					'type' => 'image',
					'url' => true,
					'title' => esc_html__('Icon Wallet', 'felan-framework'),
					'subtitle' => esc_html__('Choose icon for Wallet', 'felan-framework'),
					'required' => array('show_employer_wallet', '!=', '0'),
				);
			}

			$title_freelancers = esc_html__('Freelancer Settings', 'felan-framework');
			if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') {
				$title_freelancers = esc_html__('Candidate Settings', 'felan-framework');
			}

			return apply_filters('felan_register_user_option', array(
				'id' => 'felan_user_option',
				'title' => esc_html__('User Navigation', 'felan-framework'),
				'icon' => 'dashicons-groups',
				'fields' => array_merge(
					apply_filters('felan_register_user_employer_option_top', array()),
					array(
						apply_filters(
							'felan_register_user_employer_option_main',
							array(
								'id' => 'felan_user_option_employer',
								'type' => 'group',
								'title' => esc_html__('Employer Setting', 'felan-framework'),
								'fields' => array(
									array(
										'id' => 'show_employer_payout',
										'type' => 'button_set',
										'title' => esc_html__('Show "Payout"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Payout"', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'type_icon_employer',
										'type' => 'select',
										'title' => esc_html__('Icon Type', 'felan-framework'),
										'default' => 'svg',
										'options' => array(
											'image' => esc_html__('Image', 'felan-framework'),
											'svg' => esc_html__('Svg', 'felan-framework')
										),
									),
									array(
										'id' => 'show_employer_dashboard',
										'type' => 'button_set',
										'title' => esc_html__('Show "Dashboard"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Dashboard" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_dashboard',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Dashboard', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for dashboard', 'felan-framework'),
										'required' => array('show_employer_dashboard', '!=', '0'),
									),

									$user_navigation_employer_show_project,
									$user_navigation_employer_image_project,

									$user_navigation_employer_show_proposal,
									$user_navigation_employer_image_proposal,

									$user_navigation_employer_show_jobs,
									$user_navigation_employer_image_jobs,

									array(
										'id' => 'show_employer_freelancers',
										'type' => 'button_set',
										'title' => esc_html__('Show "Follow"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Follow" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_freelancers',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Follow', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for follow', 'felan-framework'),
										'required' => array('show_employer_freelancers', '!=', '0'),
									),

									$user_navigation_employer_show_disputes,
									$user_navigation_employer_image_disputes,

									$user_navigation_employer_show_user_package,
									$user_navigation_employer_image_user_package,

									$user_navigation_employer_show_wallet,
									$user_navigation_employer_image_wallet,

									array(
										'id' => 'show_employer_messages',
										'type' => 'button_set',
										'title' => esc_html__('Show "Messages"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Messages" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_messages',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Messages', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for messages', 'felan-framework'),
										'required' => array('show_employer_messages', '!=', '0'),
									),

									$user_navigation_employer_show_meetings,
									$user_navigation_employer_image_meetings,

									array(
										'id' => 'show_employer_company',
										'type' => 'button_set',
										'title' => esc_html__('Show "Company"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Company" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_company',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Company', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for company', 'felan-framework'),
										'required' => array('show_employer_company', '!=', '0'),
									),
									array(
										'id' => 'show_employer_settings',
										'type' => 'button_set',
										'title' => esc_html__('Show "Settings"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Settings" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_settings',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Settings', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for settings', 'felan-framework'),
										'required' => array('show_employer_settings', '!=', '0'),
									),
									array(
										'id' => 'show_employer_verification',
										'type' => 'button_set',
										'title' => esc_html__('Show Identity Verification', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide Identity Verification on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_verification',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Identity Verification', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for Identity Verification', 'felan-framework'),
										'required' => array('show_employer_verification', '!=', '0'),
									),
									array(
										'id' => 'show_employer_logout',
										'type' => 'button_set',
										'title' => esc_html__('Show "Logout"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Logout" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_logout',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Logout', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for logout', 'felan-framework'),
										'required' => array('show_employer_logout', '!=', '0'),
									),
									$user_navigation_employer_show_servive,
									$user_navigation_employer_image_service,
								),
							),
						),
					),
					apply_filters('felan_register_user_freelancer_option_top', array()),
					array(
						apply_filters(
							'felan_register_user_freelancer_option_main',
							array(
								'id' => 'felan_user_option_freelancer',
								'type' => 'group',
								'title' => $title_freelancers,
								'fields' => array(
									array(
										'id' => 'show_freelancer_payout',
										'type' => 'button_set',
										'title' => esc_html__('Show "Payout"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Payout"', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'type_icon_freelancer',
										'type' => 'select',
										'title' => esc_html__('Icon Type', 'felan-framework'),
										'default' => 'svg',
										'options' => array(
											'image' => esc_html__('Image', 'felan-framework'),
											'svg' => esc_html__('Svg', 'felan-framework')
										),
									),
									array(
										'id' => 'show_freelancer_dashboard',
										'type' => 'button_set',
										'title' => esc_html__('Show "Dashboard"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Dashboard" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_freelancer_dashboard',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Dashboard', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for dashboard', 'felan-framework'),
										'required' => array('show_freelancer_dashboard', '!=', '0'),
									),
									array(
										'id' => 'show_freelancer_profile',
										'type' => 'button_set',
										'title' => esc_html__('Show "Profile"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Profile" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_freelancer_profile',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Profile', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for profile', 'felan-framework'),
										'required' => array('show_freelancer_profile', '!=', '0'),
									),

									$user_navigation_freelancer_show_project,
									$user_navigation_freelancer_image_project,

									$user_navigation_freelancer_show_jobs,
									$user_navigation_freelancer_image_jobs,

									$user_navigation_freelancer_show_user_package,
									$user_navigation_freelancer_image_user_package,

									array(
										'id' => 'show_freelancer_reviews',
										'type' => 'button_set',
										'title' => esc_html__('Show "My Reviews"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "My Reviews" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_freelancer_reviews',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon My Reviews', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for My Reviews', 'felan-framework'),
										'required' => array('show_freelancer_reviews', '!=', '0'),
									),
									array(
										'id' => 'show_freelancer_company',
										'type' => 'button_set',
										'title' => esc_html__('Show "My Following"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "My Following" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_freelancer_company',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon My Following', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for My Following', 'felan-framework'),
										'required' => array('show_freelancer_company', '!=', '0'),
									),
									array(
										'id' => 'show_freelancer_messages',
										'type' => 'button_set',
										'title' => esc_html__('Show "Messages"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Messages" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_freelancer_messages',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Messages', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for messages', 'felan-framework'),
										'required' => array('show_freelancer_messages', '!=', '0'),
									),

									$user_navigation_freelancer_show_meetings,
									$user_navigation_freelancer_image_meetings,

									$user_navigation_freelancer_show_disputes,
									$user_navigation_freelancer_image_disputes,

									array(
										'id' => 'show_freelancer_wallet',
										'type' => 'button_set',
										'title' => esc_html__('Show "Wallet"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Wallet" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_freelancer_wallet',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Wallet', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for wallet', 'felan-framework'),
										'required' => array('show_freelancer_settings', '!=', '0'),
									),
									array(
										'id' => 'show_freelancer_settings',
										'type' => 'button_set',
										'title' => esc_html__('Show "Settings"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Settings" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_freelancer_settings',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Settings', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for settings', 'felan-framework'),
										'required' => array('show_freelancer_settings', '!=', '0'),
									),
									array(
										'id' => 'show_freelancer_verification',
										'type' => 'button_set',
										'title' => esc_html__('Show Identity Verification', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide Identity Verification on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_freelancer_verification',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Identity Verification', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for Identity Verification', 'felan-framework'),
										'required' => array('show_freelancer_verification', '!=', '0'),
									),
									array(
										'id' => 'show_freelancer_logout',
										'type' => 'button_set',
										'title' => esc_html__('Show "Logout"', 'felan-framework'),
										'subtitle' => esc_html__('Show/Hide "Logout" on navigation', 'felan-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'felan-framework'),
											'0' => esc_html__('Off', 'felan-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_freelancer_logout',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Logout', 'felan-framework'),
										'subtitle' => esc_html__('Choose icon for logout', 'felan-framework'),
										'required' => array('show_freelancer_logout', '!=', '0'),
									),
									$user_navigation_freelancer_show_servive,
									$user_navigation_freelancer_image_service,
								),
							),
						),
					),
					apply_filters('felan_register_user_option_bottom', array())
				)
			));
		}

		/**
		 * Jobs page option
		 * @return mixed
		 */
		private function jobs_option()
		{
			$sidebar_filter = [
				'jobs-type' => esc_html__('Type', 'felan-framework'),
				'jobs-salary' => esc_html__('Salary', 'felan-framework'),
				'jobs-career' => esc_html__('Career', 'felan-framework'),
				'jobs-experience' => esc_html__('Experience', 'felan-framework'),
			];
			$custom_field_filter = felan_render_custom_field('jobs');
			foreach ($custom_field_filter as $custom_field) {
				$sidebar_filter[$custom_field['id']] = $custom_field['title'];
			}
			return
				apply_filters('felan_register_option_listing_setting_page', array(
					'id' => 'felan_listing_setting_page_option',
					'title' => esc_html__('Jobs Option', 'felan-framework'),
					'icon' => 'dashicons-list-view',
					'fields' => array(
						//General Jobs
						apply_filters('felan_register_option_genera_jobs_page_top', array()),
						apply_filters('felan_register_option_genera_jobs_page_main', array(
							'id' => 'felan_main_group',
							'type' => 'group',
							'title' => esc_html__('General Jobs', 'felan-framework'),
							'fields' => array(
								array(
									'id' => 'enable_extend_expired_jobs',
									'type' => 'button_set',
									'title' => esc_html__('Extend Expired Jobs', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable extend expired jobs', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0'
								),
								array(
									'id' => 'enable_apply_login',
									'type' => 'button_set',
									'title' => esc_html__('Enable Apply Job Login', 'felan-framework'),
									'subtitle' => esc_html__('Only works in apply (gmail,phone,external)', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0'
								),
								array(
									'id' => "enable_job_alerts",
									'type' => 'button_set',
									'title' => esc_html__('Enable Job Alerts', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Job Alerts', 'felan-framework'),
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'felan_job_alerts_page_id',
									'title' => esc_html__('Job Alerts', 'felan-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									),
									'subtitle' => esc_html__('Select page for job alerts', 'felan-framework'),
									'required' => array("enable_job_alerts", '=', '1'),
								),
								array(
									'id' => "enable_status_urgent",
									'type' => 'button_set',
									'title' => esc_html__('Enable Status Urgent', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Status Urgent', 'felan-framework'),
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => "number_status_urgent",
									'title' => esc_html__('Number Status Urgent', 'felan-framework'),
									'subtitle' => esc_html__('Enter number of days status urgent', 'felan-framework'),
									'default' => '3',
									'type' => 'text',
									'required' => array("enable_status_urgent", '=', '1'),
								),
								array(
									'id' => "jobs_number_days",
									'title' => esc_html__('Number of days to apply', 'felan-framework'),
									'subtitle' => esc_html__('Enter number of days to apply', 'felan-framework'),
									'default' => '30',
									'type' => 'text',
								),
							),
						)),
						apply_filters('felan_register_option_genera_jobs_page_bottom', array()),

						//Archive Jobs
						apply_filters('felan_register_option_archive_jobs_page_top', array()),
						apply_filters('felan_register_option_archive_jobs_page_main', array(
							'id' => 'felan_main_group',
							'type' => 'group',
							'title' => esc_html__('Archive Jobs', 'felan-framework'),
							'fields' => array(
								array(
									'id' => 'archive_jobs_layout',
									'type' => 'select',
									'title' => esc_html__('Jobs Layout', 'felan-framework'),
									'default' => 'layout-list',
									'options' => array(
										'layout-list' => esc_html__('Layout List', 'felan-framework'),
										'layout-grid' => esc_html__('Layout Grid', 'felan-framework'),
										'layout-full' => esc_html__('Layout Full', 'felan-framework')
									)
								),
								array(
									'id' => 'archive_jobs_items_amount',
									'type' => 'text',
									'title' => esc_html__('Items Amount', 'felan-framework'),
									'default' => 12,
									'pattern' => '[0-9]*',
								),
								array(
									'id' => 'jobs_pagination_type',
									'type' => 'select',
									'title' => esc_html__('Type Pagination', 'felan-framework'),
									'default' => 'number',
									'options' => array(
										'number' => esc_html__('Number', 'felan-framework'),
										'loadmore' => esc_html__('Load More', 'felan-framework')
									)
								),
								array(
									'id' => "jobs_filter_sidebar_option",
									'title' => esc_html__('Postion Filter ', 'felan-framework'),
									'type' => 'select',
									'options' => array(
										'filter-left' => 'Filter Left',
										'filter-right' => 'Filter Right',
										'filter-canvas' => 'Filter Canvas',
									),
									'default' => 'left',
									'required' => array(
										array("enable_jobs_show_map", '=', '0'),
										array("archive_jobs_layout", '!=', 'layout-full'),
									),
								),

								array(
									'id' => 'enable_jobs_url_push',
									'type' => 'button_set',
									'title' => esc_html__('Show URL Push', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden URL Push', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),

								array(
									'id' => 'enable_jobs_single_popup',
									'type' => 'button_set',
									'title' => esc_html__('Show Single Popup', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Single Popup', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
									'required' => array(
										array("archive_jobs_layout", '!=', 'layout-full'),
									),
								),

								array(
									'id' => 'enable_jobs_filter_top',
									'type' => 'button_set',
									'title' => esc_html__('Show Top Filter', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Top Filter', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_jobs_show_map',
									'type' => 'button_set',
									'title' => esc_html__('Show Maps', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Maps', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
									'required' => array(
										array("archive_jobs_layout", '!=', 'layout-full'),
										array("enable_jobs_single_popup", '!=', '1'),
									),
								),
								array(
									'id' => "jobs_map_postion",
									'title' => esc_html__('Postion Maps ', 'felan-framework'),
									'type' => 'select',
									'options' => array(
										'map-right' => 'Map Right',
										'map-top' => 'Map Top',
									),
									'default' => 'right',
									'required' => array(
										array("enable_jobs_show_map", '=', '1'),
										array("archive_jobs_layout", '!=', 'layout-full'),
										array("enable_jobs_single_popup", '!=', '1'),
									),
								),
								array(
									'id' => 'enable_jobs_show_des',
									'type' => 'button_set',
									'title' => esc_html__('Show Description', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Description', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_jobs_show_expires',
									'type' => 'button_set',
									'title' => esc_html__('Show Jobs Expires', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Jobs Expires', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
							),
						)),
						apply_filters('felan_register_option_archive_jobs_page_bottom', array()),

						//Single Jobs
						apply_filters('felan_register_option_single_jobs_page_top', array()),
						apply_filters('felan_register_option_single_jobs_page_main', array(
							'id' => 'jobs_page_main_group',
							'type' => 'group',
							'title' => esc_html__('Single Jobs', 'felan-framework'),
							'fields' => array(
								array(
									'id' => 'single_job_layout',
									'type' => 'select',
									'title' => esc_html__('Layout', 'felan-framework'),
									'default' => '01',
									'options' => array(
										'01' => esc_html__('01', 'felan-framework'),
										'02' => esc_html__('02', 'felan-framework'),
										'03' => esc_html__('03', 'felan-framework'),
										'04' => esc_html__('04', 'felan-framework'),
									)
								),
								array(
									'id' => 'enable_single_jobs_info_left',
									'type' => 'button_set',
									'title' => esc_html__('Enable Company Sidebar Left', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Company Sidebar Left', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
									'required' => array(
										array("single_job_layout", '=', '01'),
									)
								),
								array(
									'id' => "enable_google_job_schema",
									'type' => 'button_set',
									'title' => esc_html__('Enable Google Job Schema', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Google Job Schema', 'felan-framework'),
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => "enable_job_login_to_view",
									'type' => 'button_set',
									'title' => esc_html__('Enable Job Login To View', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Job Login To View', 'felan-framework'),
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_sticky_sidebar_type',
									'type' => 'button_set',
									'title' => esc_html__('Enable Sticky Sidebar', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable sticky sidebar when scroll', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_single_jobs_salary',
									'type' => 'button_set',
									'title' => esc_html__('Enable Jobs Salary', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Jobs Salary', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_single_jobs_related',
									'type' => 'button_set',
									'title' => esc_html__('Enable Jobs Related', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Jobs Related', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => "enable_date_posted",
									'type' => 'button_set',
									'title' => esc_html__('Enable Date Posted', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Date Posted', 'felan-framework'),
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => "enable_closing_date",
									'type' => 'button_set',
									'title' => esc_html__('Enable Closing Date', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Closing Date', 'felan-framework'),
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'enable_single_jobs_apply',
									'type' => 'button_set',
									'title' => esc_html__('Enable Jobs Apply', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Apply', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'show_field_jobs_apply',
									'type' => 'checkbox_list',
									'title' => esc_html__('Show Field Form Apply', 'felan-framework'),
									'subtitle' => esc_html__('Choose the field you want to display in the Gmail application form', 'felan-framework'),
									'options' => array(
										'position' => esc_html__('Current Position', 'felan-framework'),
										'categories' => esc_html__('Categories', 'felan-framework'),
										'date' => esc_html__('Date of Birth', 'felan-framework'),
										'age' => esc_html__('Age', 'felan-framework'),
										'gender' => esc_html__('Gender', 'felan-framework'),
										'languages' => esc_html__('Languages', 'felan-framework'),
										'qualification' => esc_html__('Qualification', 'felan-framework'),
										'experience' => esc_html__('Years of Experience', 'felan-framework'),
									),
									'value_inline' => false,
									'default' => array(),
									'required' => array("enable_single_jobs_apply", '=', '1'),
								),

								array(
									'id' => 'single_jobs_image_size',
									'type' => 'text',
									'title' => esc_html__('Image Size', 'felan-framework'),
									'subtitle' => esc_html__('Enter image size. Alternatively enter size in pixels (Example : 770x250 (Not Include Unit, Space))', 'felan-framework'),
									'default' => '770x250',
								),

								array(
									'id' => 'jobs_details_order',
									'type' => 'sortable',
									'title' => esc_html__('Jobs Content Order', 'felan-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your jobs content details.', 'felan-framework'),
									'options' => array(
										'enable_sp_head' => esc_html__('Head', 'felan-framework'),
										'enable_sp_insights' => esc_html__('Insights', 'felan-framework'),
										'enable_sp_description' => esc_html__('Description', 'felan-framework'),
										'enable_sp_skills' => esc_html__('Skills', 'felan-framework'),
										'enable_sp_gallery' => esc_html__('Gallery', 'felan-framework'),
										'enable_sp_video' => esc_html__('Video', 'felan-framework'),
										'enable_sp_map' => esc_html__('Map', 'felan-framework'),
									),
									'default' => array('enable_sp_skills', 'enable_sp_head', 'enable_sp_description', 'enable_sp_video', 'enable_sp_map', 'enable_sp_insights')
								),
								array(
									'id' => 'jobs_details_sidebar_order',
									'type' => 'sortable',
									'title' => esc_html__('Jobs Sidebar Order', 'felan-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your jobs sidebar order.', 'felan-framework'),
									'options' => array(
										'enable_sidebar_sp_insights' => esc_html__('Insights', 'felan-framework'),
										'enable_sidebar_sp_company' => esc_html__('Company', 'felan-framework'),
									),
									'default' => array('enable_sidebar_sp_apply', 'enable_sidebar_sp_insights', 'enable_sidebar_sp_company')
								),
							),
						)),
						apply_filters('felan_register_option_single_jobs_page_bottom', array()),

						//Jobs Submit
						apply_filters('felan_jobs_option_jobs_submit_top', array()),
						apply_filters('felan_jobs_option_jobs_submit_main', array(
							'id' => 'jobs_submit_group',
							'title' => esc_html__('Jobs Submit', 'felan-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'auto_publish',
									'title' => esc_html__('Automatically publish the submitted jobs?', 'felan-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'felan-framework'),
										'0' => esc_html__('No', 'felan-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'auto_publish_edited',
									'title' => esc_html__('Automatically publish the edited jobs?', 'felan-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'felan-framework'),
										'0' => esc_html__('No', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_add_new_job_location',
									'title' => esc_html__('Enable Add New Location', 'felan-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'felan-framework'),
										'0' => esc_html__('No', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_add_new_job_categories',
									'title' => esc_html__('Enable Add New Categories', 'felan-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'felan-framework'),
										'0' => esc_html__('No', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'section_jobs_hide_group_fields',
									'title' => esc_html__('Hide Submit Group Form Fields', 'felan-framework'),
									'type' => 'group',

									'fields' => array(
										array(
											'id' => 'hide_jobs_group_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Groups', 'felan-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on group field jobs?', 'felan-framework'),
											'options' => array(
												'general' => esc_html__('General', 'felan-framework'),
												'salary' => esc_html__('Salary', 'felan-framework'),
												'apply' => esc_html__('Apply', 'felan-framework'),
												'social' => esc_html__('Social network', 'felan-framework'),
												'company' => esc_html__('Company', 'felan-framework'),
												'location' => esc_html__('Location', 'felan-framework'),
												'thumbnail' => esc_html__('Cover Image', 'felan-framework'),
												'gallery' => esc_html__('Gallery', 'felan-framework'),
												'video' => esc_html__('Video', 'felan-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
										array(
											'id' => 'hide_jobs_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Fields', 'felan-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on field jobs?', 'felan-framework'),
											'options' => array(
												'fields_jobs_name' => esc_html__('Name', 'felan-framework'),
												'fields_jobs_category' => esc_html__('Category', 'felan-framework'),
												'fields_jobs_type' => esc_html__('Type', 'felan-framework'),
												'fields_jobs_skills' => esc_html__('Skills', 'felan-framework'),
												'fields_jobs_des' => esc_html__('Description', 'felan-framework'),
												'fields_jobs_career' => esc_html__('Career', 'felan-framework'),
												'fields_jobs_experience' => esc_html__('Experience', 'felan-framework'),
												'fields_jobs_qualification' => esc_html__('Qualification', 'felan-framework'),
												'fields_jobs_quantity' => esc_html__('Quantity', 'felan-framework'),
												'fields_jobs_gender' => esc_html__('Gender', 'felan-framework'),
												'fields_closing_days' => esc_html__('Closing', 'felan-framework'),
												'fields_jobs_location' => esc_html__('Location', 'felan-framework'),
												'fields_map' => esc_html__('Maps', 'felan-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
										array(
											'id' => 'hide_jobs_apply_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Job apply type', 'felan-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on field job apply type?', 'felan-framework'),
											'options' => array(
												'fields_jobs_apply_email' => esc_html__('By Email', 'felan-framework'),
												'fields_jobs_apply_external' => esc_html__('External Apply', 'felan-framework'),
												'fields_jobs_apply_internal' => esc_html__('Internal Apply', 'felan-framework'),
												'fields_jobs_call_to_apply' => esc_html__('Call To Apply', 'felan-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
										array(
											'id' => 'hide_jobs_salary_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Job Salary', 'felan-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on field job salary?', 'felan-framework'),
											'options' => array(
												'fields_jobs_salary_range' => esc_html__('Range', 'felan-framework'),
												'fields_jobs_salary_starting' => esc_html__('Starting Amount', 'felan-framework'),
												'fields_jobs_salary_maximum' => esc_html__('Maximum Amount', 'felan-framework'),
												'fields_jobs_salary_negotiable' => esc_html__('Negotiable Price', 'felan-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
									)
								),
							)
						)),
						apply_filters('felan_jobs_option_jobs_submit_bottom', array()),

						//Jobs Search
						apply_filters('felan_register_option_search_page_top', array()),
						apply_filters('felan_register_option_search_page_main', array(
							'id' => 'jobs_search_group',
							'title' => esc_html__('Search Jobs', 'felan-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'enable_jobs_search_bg',
									'type' => 'button_set',
									'title' => esc_html__('Enable Background', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Background', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_jobs_search_location_top',
									'type' => 'button_set',
									'title' => esc_html__('Enable Search City/Town (Top)', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Search City/Town', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_jobs_search_location_radius',
									'type' => 'button_set',
									'title' => esc_html__('Enable Search location radius', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden search location radius', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => "jobs_search_color",
									'title' => esc_html__('Color', 'felan-framework'),
									'type' => 'color',
									'col' => '12',
									'default' => '',
									'required' => array(
										array("enable_jobs_search_bg", '=', '1'),
									),
								),
								array(
									'id' => "jobs_search_image",
									'title' => esc_html__('Image', 'felan-framework'),
									'type' => 'image',
									'default' => '',
									'col' => '12',
									'required' => array(
										array("enable_jobs_search_bg", '=', '1'),
									),
								),
								array(
									'id' => 'jobs_search_field',
									'title' => esc_html__('Search Fields', 'felan-framework'),
									'type' => 'sorter',
									'default' => array(
										'top' => array(
											'jobs-categories' => esc_html__('Categories', 'felan-framework'),
										),
										'sidebar' => $sidebar_filter,
										'disable' => array(
											'jobs-skills' => esc_html__('Skills', 'felan-framework'),
											'jobs-location' => esc_html__('Locations', 'felan-framework'),
											'jobs-gender' => esc_html__('Gender', 'felan-framework'),
											'jobs-qualification' => esc_html__('Qualification', 'felan-framework'),
										)
									),
								),
								array(
									'id' => 'jobs_search_fields_jobs-categories',
									'title' => esc_html__('Icon Categories', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'jobs_search_fields_jobs-type',
									'title' => esc_html__('Icon Type', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'jobs_search_fields_jobs-career',
									'title' => esc_html__('Icon Career', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'jobs_search_fields_jobs-experience',
									'title' => esc_html__('Icon Experience', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'jobs_search_fields_jobs-gender',
									'title' => esc_html__('Icon Gender', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'jobs_search_fields_location',
									'title' => esc_html__('Icon City', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'jobs_search_fields_state',
									'title' => esc_html__('Icon State', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'jobs_search_fields_country',
									'title' => esc_html__('Icon Country', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
							)
						)),
						apply_filters('felan_register_option_search_page_bottom', array()),
					),
				));
		}


		/**
		 * Company page option
		 * @return mixed
		 */
		private function company_option()
		{
			$enable_company_package_invite = $option_package_company = array();

			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			if ($enable_post_type_service == '1') {
				$enable_company_package_invite = array(
					'id' => "enable_company_package_invite",
					'title' => esc_html__('Enable/Disable Invite', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}

			$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
			if ($enable_wallet_mode == '0') {
				$option_package_company = array(
					'id' => 'company_package_group',
					'title' => esc_html__('Company Package', 'felan-framework'),
					'type' => 'group',
					'fields' => array(
						array(
							'id' => "enable_company_package_freelancer_follow",
							'title' => esc_html__('Enable/Disable Freelancer Follow', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),

						$enable_company_package_invite,

						array(
							'id' => "enable_company_package_send_message",
							'title' => esc_html__('Enable/Disable Send Message', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "enable_company_package_review_and_commnent",
							'title' => esc_html__('Enable/Disable Review And Commnet', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "enable_company_package_info",
							'title' => esc_html__('Enable/Disable profile information', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'hide_company_freelancer_info_fields',
							'type' => 'checkbox_list',
							'title' => esc_html__('Hide Field freelancer information', 'felan-framework'),
							'subtitle' => esc_html__('Choose which fields you want to hide on freelancer information', 'felan-framework'),
							'options' => array(
								'salary' => esc_html__('Offered Salary', 'felan-framework'),
								'social' => esc_html__('Social', 'felan-framework'),
								'locations' => esc_html__('Locations', 'felan-framework'),
								'languages' => esc_html__('Languages', 'felan-framework'),
								'phone' => esc_html__('Phone', 'felan-framework'),
								'email' => esc_html__('Email', 'felan-framework'),
							),
							'value_inline' => false,
							'default' => array('phone', 'email', 'social'),
							'required' => array("enable_company_package_info", '=', '1'),
						),
					)
				);
			}

			$sidebar_filter = [
				'company-rating' => esc_html__('Rating', 'felan-framework'),
				'company-founded' => esc_html__('Founded', 'felan-framework'),
				'company-size' => esc_html__('Size', 'felan-framework'),
			];
			$custom_field_filter = felan_render_custom_field('company');
			foreach ($custom_field_filter as $custom_field) {
				$sidebar_filter[$custom_field['id']] = $custom_field['title'];
			}
			return
				apply_filters('felan_register_company_option_listing_setting_page', array(
					'id' => 'felan_listing_company_setting_page_option',
					'title' => esc_html__('Companies Option', 'felan-framework'),
					'icon' => 'dashicons-awards',
					'fields' => array(
						//Archive Company
						apply_filters('felan_register_option_archive_company_page_top', array()),
						apply_filters('felan_register_option_archive_company_page_main', array(
							'id' => 'felan_main_group',
							'type' => 'group',
							'title' => esc_html__('Archive Company', 'felan-framework'),
							'fields' => array(
								array(
									'id' => 'archive_company_layout',
									'type' => 'select',
									'title' => esc_html__('Company Layout', 'felan-framework'),
									'default' => 'layout-list',
									'options' => array(
										'layout-list' => esc_html__('Layout List', 'felan-framework'),
										'layout-grid' => esc_html__('Layout Grid', 'felan-framework'),
									)
								),
								array(
									'id' => 'archive_company_thumbnail_size',
									'type' => 'text',
									'title' => esc_html__('Thumbnail Size', 'felan-framework'),
									'subtitle' => esc_html__('Enter image size. Alternatively enter size in pixels (Example : 330x180 (Not Include Unit, Space))', 'felan-framework'),
									'default' => '576x327',
								),
								array(
									'id' => 'archive_company_items_amount',
									'type' => 'text',
									'title' => esc_html__('Items Amount', 'felan-framework'),
									'default' => 12,
									'pattern' => '[0-9]*',
								),
								array(
									'id' => 'company_pagination_type',
									'type' => 'select',
									'title' => esc_html__('Type Pagination', 'felan-framework'),
									'default' => 'number',
									'options' => array(
										'number' => esc_html__('Number', 'felan-framework'),
										'loadmore' => esc_html__('Load More', 'felan-framework')
									)
								),
								array(
									'id' => "company_filter_sidebar_option",
									'title' => esc_html__('Postion Filter', 'felan-framework'),
									'type' => 'select',
									'options' => array(
										'filter-left' => 'Filter Left',
										'filter-right' => 'Filter Right',
										'filter-canvas' => 'Filter Canvas',
									),
									'default' => 'left',
								),
								array(
									'id' => 'enable_company_single_popup',
									'type' => 'button_set',
									'title' => esc_html__('Show Single Popup', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Single Popup', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_company_filter_top',
									'type' => 'button_set',
									'title' => esc_html__('Show Top Filter', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Top Filter', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_company_show_map',
									'type' => 'button_set',
									'title' => esc_html__('Show Maps', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Maps', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
									'required' => array(
										array("enable_company_single_popup", '!=', '1'),
									),
								),
								array(
									'id' => "company_map_postion",
									'title' => esc_html__('Postion Maps ', 'felan-framework'),
									'type' => 'select',
									'options' => array(
										'map-right' => 'Map Right',
										'map-top' => 'Map Top',
									),
									'default' => 'right',
									'required' => array(
										array("enable_company_show_map", '=', '1'),
										array("enable_company_single_popup", '!=', '1')
									),
								),
								array(
									'id' => 'enable_company_show_des',
									'type' => 'button_set',
									'title' => esc_html__('Show Description', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Description', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_company_url_push',
									'type' => 'button_set',
									'title' => esc_html__('Show URL Push', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden URL Push', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
							),
						)),
						apply_filters('felan_register_option_archive_company_page_bottom', array()),

						//Single Company
						apply_filters('felan_register_option_single_company_page_top', array()),
						apply_filters('felan_register_option_single_company_page_main', array(
							'id' => 'company_page_main_group',
							'type' => 'group',
							'title' => esc_html__('Single Company', 'felan-framework'),
							'fields' => array(
								array(
									'id' => "enable_company_login_to_view",
									'type' => 'button_set',
									'title' => esc_html__('Enable Company Login To View', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Company Login To View', 'felan-framework'),
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_sticky_company_sidebar_type',
									'type' => 'button_set',
									'title' => esc_html__('Enable Sticky Sidebar', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable sticky sidebar when scroll', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_single_company_related',
									'type' => 'button_set',
									'title' => esc_html__('Enable Company Related', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Company Related', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => "single_company_style",
									'title' => esc_html__('Style Thumbnail Images', 'felan-framework'),
									'type' => 'select',
									'options' => array(
										'cover-img' => 'Cover Image',
										'large-cover-img' => 'Large Cover Image',
									),
								),

								array(
									'id' => 'single_company_image_size',
									'type' => 'text',
									'title' => esc_html__('Image Size', 'felan-framework'),
									'subtitle' => esc_html__('Enter image size. Alternatively enter size in pixels (Example : 330x180 (Not Include Unit, Space))', 'felan-framework'),
									'default' => '',
								),

								array(
									'id' => 'hide_company_tabs_groups',
									'type' => 'checkbox_list',
									'title' => esc_html__('Hide Tabs Groups', 'felan-framework'),
									'subtitle' => esc_html__('Choose which tabs you want to hide on company detail', 'felan-framework'),
									'options' => array(
										'about_us' => esc_html__('About us', 'felan-framework'),
										'photos' => esc_html__('Photos', 'felan-framework'),
										'projects' => esc_html__('Projects', 'felan-framework'),
										'reviews' => esc_html__('Reviews', 'felan-framework'),
									),
									'value_inline' => false,
									'default' => array()
								),

								array(
									'id' => 'company_details_order',
									'type' => 'sortable',
									'title' => esc_html__('Company Content Order', 'felan-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your company content details.', 'felan-framework'),
									'options' => array(
										'enable_sp_overview' => esc_html__('Overview', 'felan-framework'),
										'enable_sp_video' => esc_html__('Video', 'felan-framework'),
									),
									'default' => array('enable_sp_company_overview', 'enable_sp_video')
								),
								array(
									'id' => 'company_details_sidebar_order',
									'type' => 'sortable',
									'title' => esc_html__('Company Sidebar Order', 'felan-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your company sidebar order.', 'felan-framework'),
									'options' => array(
										'enable_sidebar_sp_info' => esc_html__('Information', 'felan-framework'),
										'enable_sidebar_sp_location' => esc_html__('Location', 'felan-framework'),
									),
									'default' => array('enable_sidebar_sp_info', 'enable_sidebar_sp_location'),
								),
							),
						)),
						apply_filters('felan_register_option_single_company_page_bottom', array()),

						//Company Submit
						apply_filters('felan_option_company_submit_top', array()),
						apply_filters('felan_option_company_submit_main', array(
							'id' => 'company_submit_group',
							'title' => esc_html__('Company Submit', 'felan-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'company_auto_publish',
									'title' => esc_html__('Automatically publish the submitted Company?', 'felan-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'felan-framework'),
										'0' => esc_html__('No', 'felan-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'company_auto_publish_edited',
									'title' => esc_html__('Automatically publish the edited Company?', 'felan-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'felan-framework'),
										'0' => esc_html__('No', 'felan-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'enable_add_new_company_location',
									'title' => esc_html__('Enable Add New Location', 'felan-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'felan-framework'),
										'0' => esc_html__('No', 'felan-framework'),
									),
									'default' => '0',
								),

								array(
									'id' => 'enable_add_new_company_categories',
									'title' => esc_html__('Enable Add New Categories', 'felan-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'felan-framework'),
										'0' => esc_html__('No', 'felan-framework'),
									),
									'default' => '0',
								),

								array(
									'id' => 'value_founded_min',
									'type' => 'text',
									'title' => esc_html__('Founded Date Min', 'felan-framework'),
									'subtitle' => esc_html__('Enter values founded date min', 'felan-framework'),
									'default' => '2010',
								),
								array(
									'id' => 'value_founded_max',
									'type' => 'text',
									'title' => esc_html__('Founded Date Max', 'felan-framework'),
									'subtitle' => esc_html__('Enter values founded date max', 'felan-framework'),
									'default' => '2023',
								),
								array(
									'id' => 'section_company_hide_group_fields',
									'title' => esc_html__('Hide Submit Group Form Fields', 'felan-framework'),
									'type' => 'group',

									'fields' => array(
										array(
											'id' => 'hide_company_group_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Groups', 'felan-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on group fields company?', 'felan-framework'),
											'options' => array(
												'general' => esc_html__('General', 'felan-framework'),
												'media' => esc_html__('Media', 'felan-framework'),
												'social' => esc_html__('Social network', 'felan-framework'),
												'location' => esc_html__('Location', 'felan-framework'),
												'gallery' => esc_html__('Gallery', 'felan-framework'),
												'video' => esc_html__('Video', 'felan-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
										array(
											'id' => 'hide_company_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Fields', 'felan-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on fields company?', 'felan-framework'),
											'options' => array(
												'fields_company_name' => esc_html__('Name', 'felan-framework'),
												'fields_company_category' => esc_html__('Category', 'felan-framework'),
												'fields_company_url' => esc_html__('Url', 'felan-framework'),
												'fields_company_about' => esc_html__('About', 'felan-framework'),
												'fields_company_website' => esc_html__('Website', 'felan-framework'),
												'fields_company_phone' => esc_html__('Phone', 'felan-framework'),
												'fields_company_email' => esc_html__('Email', 'felan-framework'),
												'fields_company_founded' => esc_html__('Founded', 'felan-framework'),
												'fields_company_size' => esc_html__('Size', 'felan-framework'),
												'fields_closing_logo' => esc_html__('Logo', 'felan-framework'),
												'fields_company_thumbnail' => esc_html__('Thumbnail', 'felan-framework'),
												'fields_company_location' => esc_html__('Location', 'felan-framework'),
												'fields_company_map' => esc_html__('Maps', 'felan-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
									)
								),
							)
						)),
						apply_filters('felan_option_company_submit_bottom', array()),

						//Company Search
						apply_filters('felan_register_option_search_company_page_top', array()),
						apply_filters('felan_register_option_search_company_page_main', array(
							'id' => 'company_search_group',
							'title' => esc_html__('Search Company', 'felan-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'enable_company_search_bg',
									'type' => 'button_set',
									'title' => esc_html__('Enable Background', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Background', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_company_search_location_top',
									'type' => 'button_set',
									'title' => esc_html__('Enable Search City/Town (Top)', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Search City/Town', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_company_search_location_radius',
									'type' => 'button_set',
									'title' => esc_html__('Enable Search location radius', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden search location radius', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => "company_search_color",
									'title' => esc_html__('Color', 'felan-framework'),
									'type' => 'color',
									'col' => '12',
									'default' => '',
									'required' => array(
										array("enable_company_search_bg", '=', '1'),
									),
								),
								array(
									'id' => "company_search_image",
									'title' => esc_html__('Image', 'felan-framework'),
									'type' => 'image',
									'default' => '',
									'col' => '12',
									'required' => array(
										array("enable_company_search_bg", '=', '1'),
									),
								),
								array(
									'id' => 'company_search_fields',
									'title' => esc_html__('Search Fields', 'felan-framework'),
									'type' => 'sorter',
									'default' => array(
										'top' => array(
											'company-categories' => esc_html__('Categories', 'felan-framework'),
										),
										'sidebar' => $sidebar_filter,
										'disable' => array(
											'company-location' => esc_html__('Location', 'felan-framework'),
										)
									),
								),
								array(
									'id' => 'company_search_fields_company-categories',
									'title' => esc_html__('Icon Categories', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'company_search_fields_company-rating',
									'title' => esc_html__('Icon Rating', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'company_search_fields_company-founded',
									'title' => esc_html__('Icon Founded', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'company_search_fields_company-size',
									'title' => esc_html__('Icon Size', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'company_search_fields_location',
									'title' => esc_html__('Icon City', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'company_search_fields_state',
									'title' => esc_html__('Icon State', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'company_search_fields_country',
									'title' => esc_html__('Icon Country', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
							)
						)),
						apply_filters('felan_register_option_search_company_page_bottom', array()),

						//Company Package
						apply_filters('felan_register_option_company_package_page_top', array()),
						apply_filters('felan_register_option_company_package_page_main', $option_package_company),
						apply_filters('felan_register_option_company_package_page_bottom', array())
					),
				));
		}

		/**
		 * Freelancer page option
		 * @return mixed
		 */
		private function freelancer_option()
		{
			$enable_freelancer_package_jobs_apply = $enable_freelancer_package_jobs_wishlist = $enable_freelancer_package_project_apply
				= $enable_freelancer_package_contact_company = $hide_freelancer_contact_company_fields
				= $enable_freelancer_withdrawal_fee = $freelancer_number_withdrawal_fee = $option_freelancer_package = array();

			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			if ($enable_post_type_jobs == '1') {
				$enable_freelancer_package_jobs_apply = array(
					'id' => "enable_freelancer_package_jobs_apply",
					'title' => esc_html__('Enable/Disable Jobs Apply', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);

				$enable_freelancer_package_jobs_wishlist = 	array(
					'id' => "enable_freelancer_package_jobs_wishlist",
					'title' => esc_html__('Enable/Disable Jobs Wishlist', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);

				$enable_freelancer_package_contact_company = array(
					'id' => "enable_freelancer_package_contact_company",
					'title' => esc_html__('Enable/Disable Contact Company In Jobs', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);

				$hide_freelancer_contact_company_fields = array(
					'id' => 'hide_freelancer_contact_company_fields',
					'type' => 'checkbox_list',
					'title' => esc_html__('Hide Field Contact Company In Jobs', 'felan-framework'),
					'subtitle' => esc_html__('Choose which fields you want to hide on Contact Company In Jobs', 'felan-framework'),
					'options' => array(
						'categories' => esc_html__('Categories', 'felan-framework'),
						'location' => esc_html__('Location', 'felan-framework'),
						'phone' => esc_html__('Phone', 'felan-framework'),
						'email' => esc_html__('Email', 'felan-framework'),
					),
					'value_inline' => false,
					'default' => array('phone', 'email', 'social'),
					'required' => array("enable_freelancer_package_contact_company", '=', '1'),
				);
			}

			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			if ($enable_post_type_service == '1') {
				$enable_freelancer_withdrawal_fee =   array(
					'id' => 'enable_freelancer_withdrawal_fee',
					'type' => 'button_set',
					'title' => esc_html__('Enable Withdrawal Service Fee', 'felan-framework'),
					'subtitle' => esc_html__('Enable/Disable Freelancer Withdrawal Service Fee', 'felan-framework'),
					'desc' => '',
					'options' => array(
						'1' => esc_html__('On', 'felan-framework'),
						'0' => esc_html__('Off', 'felan-framework'),
					),
					'default' => '1',
				);

				$freelancer_number_withdrawal_fee = array(
					'id' => "freelancer_number_withdrawal_fee",
					'title' => esc_html__('Number Withdrawal Service Fee', 'felan-framework'),
					'subtitle' => esc_html__('Enter (%) Freelancer Withdrawal Service Fee', 'felan-framework'),
					'type' => 'text',
					'default' => '10',
					'pattern' => '[0-9]*',
					'required' => array(
						array("enable_freelancer_withdrawal_fee", '=', '1')
					),
				);
			}

			$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
			if ($enable_post_type_project == '1') {
				$enable_freelancer_package_project_apply = array(
					'id' => "enable_freelancer_package_project_apply",
					'title' => esc_html__('Enable/Disable Project Apply', 'felan-framework'),
					'type' => 'button_set',
					'options' => array(
						'1' => esc_html__('Yes', 'felan-framework'),
						'0' => esc_html__('No', 'felan-framework'),
					),
					'default' => '0',
				);
			}

			$title_freelancers = esc_html__('Freelancers Option', 'felan-framework');
			if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') {
				$title_freelancers = esc_html__('Candidate Option', 'felan-framework');
			}

			$enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
			if ($enable_wallet_mode == '0') {
				$option_freelancer_package = array(
					'id' => 'freelancer_package_group',
					'title' => esc_html__('Package Options', 'felan-framework'),
					'type' => 'group',
					'fields' => array(

						$enable_freelancer_package_jobs_apply,
						$enable_freelancer_package_jobs_wishlist,

						$enable_freelancer_package_project_apply,

						array(
							'id' => "enable_freelancer_package_company_follow",
							'title' => esc_html__('Enable/Disable Company follow', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "enable_freelancer_package_send_message",
							'title' => esc_html__('Enable/Disable Send Message', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => "enable_freelancer_package_review_and_commnent",
							'title' => esc_html__('Enable/Disable Review And Commnet', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),

						$enable_freelancer_package_contact_company,
						$hide_freelancer_contact_company_fields,

						array(
							'id' => "enable_freelancer_package_info_company",
							'title' => esc_html__('Enable/Disable Information Company', 'felan-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'felan-framework'),
								'0' => esc_html__('No', 'felan-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'hide_freelancer_info_company_fields',
							'type' => 'checkbox_list',
							'title' => esc_html__('Hide Field Info Company', 'felan-framework'),
							'subtitle' => esc_html__('Choose which fields you want to hide on Information Company', 'felan-framework'),
							'options' => array(
								'categories' => esc_html__('Categories', 'felan-framework'),
								'size' => esc_html__('Size', 'felan-framework'),
								'founded' => esc_html__('Founded in', 'felan-framework'),
								'location' => esc_html__('Location', 'felan-framework'),
								'phone' => esc_html__('Phone', 'felan-framework'),
								'email' => esc_html__('Email', 'felan-framework'),
								'social' => esc_html__('Social', 'felan-framework'),
							),
							'value_inline' => false,
							'default' => array('phone', 'email', 'social'),
							'required' => array("enable_freelancer_package_info_company", '=', '1'),
						),
					)
				);
			}

			$sidebar_filter = [
				'freelancer_rating' => esc_html__('Rating', 'felan-framework'),
				'freelancer_yoe' => esc_html__('Experience', 'felan-framework'),
				'freelancer_qualification' => esc_html__('Qualification', 'felan-framework'),
				'freelancer_gender' => esc_html__('Gender', 'felan-framework'),
			];
			$custom_field_filter = felan_render_custom_field('freelancer');
			foreach ($custom_field_filter as $custom_field) {
				$sidebar_filter[$custom_field['id']] = $custom_field['title'];
			}
			return
				apply_filters('felan_register_freelancer_option_listing_setting_page', array(
					'id' => 'felan_listing_freelancer_setting_page_option',
					'title' => $title_freelancers,
					'icon' => 'dashicons-businessperson',
					'fields' => array(
						$enable_freelancer_withdrawal_fee,
						$freelancer_number_withdrawal_fee,

						//Archive Freelancer
						apply_filters('felan_register_option_archive_freelancer_page_top', array()),
						apply_filters('felan_register_option_archive_freelancer_page_main', array(
							'id' => 'felan_main_group',
							'type' => 'group',
							'title' => esc_html__('Archive Options', 'felan-framework'),
							'fields' => array(
								array(
									'id' => 'archive_freelancer_layout',
									'type' => 'select',
									'title' => esc_html__('Archive Layout', 'felan-framework'),
									'default' => 'layout-list',
									'options' => array(
										'layout-list' => esc_html__('Layout List', 'felan-framework'),
										'layout-grid' => esc_html__('Layout Grid', 'felan-framework'),
									)
								),
								array(
									'id' => 'archive_freelancer_items_amount',
									'type' => 'text',
									'title' => esc_html__('Items Amount', 'felan-framework'),
									'default' => 12,
									'pattern' => '[0-9]*',
								),
								array(
									'id' => 'freelancer_pagination_type',
									'type' => 'select',
									'title' => esc_html__('Type Pagination', 'felan-framework'),
									'default' => 'number',
									'options' => array(
										'number' => esc_html__('Number', 'felan-framework'),
										'loadmore' => esc_html__('Load More', 'felan-framework')
									)
								),
								array(
									'id' => "freelancer_filter_sidebar_option",
									'title' => esc_html__('Postion Filter ', 'felan-framework'),
									'type' => 'select',
									'options' => array(
										'filter-left' => 'Filter Left',
										'filter-right' => 'Filter Right',
										'filter-canvas' => 'Filter Canvas',
									),
									'default' => 'left',
									'required' => array(
										array("enable_freelancer_show_map", '=', '0'),
									),
								),
								array(
									'id' => 'enable_freelancer_single_popup',
									'type' => 'button_set',
									'title' => esc_html__('Show Single Popup', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Single Popup', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_freelancer_filter_top',
									'type' => 'button_set',
									'title' => esc_html__('Show Top Filter', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Top Filter', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_freelancer_show_map',
									'type' => 'button_set',
									'title' => esc_html__('Show Maps', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Maps', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
									'required' => array(
										array("enable_freelancer_single_popup", '!=', '1'),
									),
								),
								array(
									'id' => "freelancer_map_postion",
									'title' => esc_html__('Postion Maps ', 'felan-framework'),
									'type' => 'select',
									'options' => array(
										'map-right' => 'Map Right',
										'map-top' => 'Map Top',
									),
									'default' => 'right',
									'required' => array(
										array("enable_freelancer_show_map", '=', '1'),
										array("enable_freelancer_single_popup", '!=', '1'),
									),
								),
								array(
									'id' => 'enable_freelancer_show_des',
									'type' => 'button_set',
									'title' => esc_html__('Show Description', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Description', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'archive_freelancer_stautus',
									'type' => 'select',
									'title' => esc_html__('Status', 'felan-framework'),
									'subtitle' => esc_html__('Select freelancer status after registration', 'felan-framework'),
									'default' => 'pending',
									'options' => array(
										'pending' => esc_html__('Pending', 'felan-framework'),
										'publish' => esc_html__('Published', 'felan-framework'),
									)
								),
							),
						)),
						apply_filters('felan_register_option_archive_freelancer_page_bottom', array()),

						//Single Freelancer
						apply_filters('felan_register_option_single_freelancer_page_top', array()),
						apply_filters('felan_register_option_single_freelancer_page_main', array(
							'id' => 'freelancer_page_main_group',
							'type' => 'group',
							'title' => esc_html__('Single Options', 'felan-framework'),
							'fields' => array(
								array(
									'id' => "enable_freelancer_login_to_view",
									'type' => 'button_set',
									'title' => esc_html__('Enable Login To View', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable Freelancer Login To View', 'felan-framework'),
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_sticky_freelancer_sidebar_type',
									'type' => 'button_set',
									'title' => esc_html__('Enable Sticky Sidebar', 'felan-framework'),
									'subtitle' => esc_html__('Enable/Disable sticky sidebar when scroll', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => "single_freelancer_style",
									'title' => esc_html__('Style Thumbnail Images', 'felan-framework'),
									'type' => 'select',
									'options' => array(
										'cover-img' => 'Cover Image',
										'large-cover-img' => 'Large Cover Image',
									),
								),

								array(
									'id' => 'single_freelancer_image_size',
									'type' => 'text',
									'title' => esc_html__('Image Size', 'felan-framework'),
									'subtitle' => esc_html__('Enter image size. Alternatively enter size in pixels (Example : 330x180 (Not Include Unit, Space))', 'felan-framework'),
									'default' => '',
								),
								array(
									'id' => 'hide_freelancer_tabs_groups',
									'type' => 'checkbox_list',
									'title' => esc_html__('Hide Tabs Groups', 'felan-framework'),
									'subtitle' => esc_html__('Choose which tabs you want to hide on Freelancer Profile', 'felan-framework'),
									'options' => array(
										'about_me' => esc_html__('About Me', 'felan-framework'),
										'projects' => esc_html__('Projects', 'felan-framework'),
										'services' => esc_html__('Services', 'felan-framework'),
										'reviews' => esc_html__('Reviews', 'felan-framework'),
									),
									'value_inline' => false,
									'default' => array()
								),
								array(
									'id' => 'freelancers_details_order',
									'type' => 'sortable',
									'title' => esc_html__('Content Order', 'felan-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your freelancer content order.', 'felan-framework'),
									'options' => array(
										'enable_sp_thumbnail' => esc_html__('Thumbnail', 'felan-framework'),
										'enable_sp_head' => esc_html__('Head', 'felan-framework'),
										'enable_sp_descriptions' => esc_html__('Descriptions', 'felan-framework'),
										'enable_sp_video' => esc_html__('Video', 'felan-framework'),
										'enable_sp_experience' => esc_html__('Experience', 'felan-framework'),
										'enable_sp_education' => esc_html__('Education', 'felan-framework'),
										'enable_sp_photos' => esc_html__('Photos', 'felan-framework'),
										'enable_sp_portfolio' => esc_html__('Portfolio', 'felan-framework'),
										'enable_sp_awards' => esc_html__('Awards', 'felan-framework'),
									),
									'default' => array(
										'enable_sp_thumbnail',
										'enable_sp_head',
										'enable_sp_descriptions',
										'enable_sp_video',
										'enable_sp_experience',
										'enable_sp_education',
										'enable_sp_portfolio',
										'enable_sp_awards'
									),
								),

								array(
									'id' => 'freelancer_details_sidebar_order',
									'type' => 'sortable',
									'title' => esc_html__('Sidebar Order', 'felan-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your freelancer sidebar order.', 'felan-framework'),
									'options' => array(
										'enable_sidebar_sp_info' => esc_html__('Information', 'felan-framework'),
										'enable_sidebar_sp_location' => esc_html__('Location', 'felan-framework'),
									),
									'default' => array('enable_sidebar_sp_info', 'enable_sidebar_sp_location'),
								),
							),
						)),
						apply_filters('felan_register_option_single_freelancer_page_bottom', array()),

						//Freelancer Profile
						apply_filters('felan_option_freelancer_profile_top', array()),
						apply_filters('felan_option_freelancer_profile_main', array(
							'id' => 'freelancer_profile_group',
							'title' => esc_html__('Profile Options', 'felan-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'section_freelancer_hide_group_fields',
									'title' => esc_html__('Hide Submit Group Form Fields', 'felan-framework'),
									'type' => 'group',
									'fields' => array(
										array(
											'id' => 'enable_freelancer_language_multiple',
											'type' => 'button_set',
											'title' => esc_html__('Enable Language Multiple', 'felan-framework'),
											'subtitle' => esc_html__('Show/Hidden Multiple', 'felan-framework'),
											'desc' => '',
											'options' => array(
												'1' => esc_html__('On', 'felan-framework'),
												'0' => esc_html__('Off', 'felan-framework'),
											),
											'default' => '0',
										),
										array(
											'id' => 'type_name_freelancer',
											'type' => 'select',
											'title' => esc_html__('Display Name', 'felan-framework'),
											'subtitle' => esc_html__('Display type name after registration', 'felan-framework'),
											'options' => array(
												'user-name' => esc_html__('User Name', 'felan-framework'),
												'fl-name' => esc_html__('First Name + Last Name', 'felan-framework'),
											),
											'default' => 'user-name',
										),
										array(
											'id' => 'hide_freelancer_group_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Groups', 'felan-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on profile', 'felan-framework'),
											'options' => array(
												'info' => esc_html__('Basic Info', 'felan-framework'),
												'education' => esc_html__('Education', 'felan-framework'),
												'experience' => esc_html__('Experience', 'felan-framework'),
												'skills' => esc_html__('Skills', 'felan-framework'),
												'projects' => esc_html__('Projects', 'felan-framework'),
												'awards' => esc_html__('Awards', 'felan-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
										array(
											'id' => 'hide_freelancer_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Fields', 'felan-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide profile', 'felan-framework'),
											'options' => array(
												'fields_freelancer_avatar' => esc_html__('Avatar', 'felan-framework'),
												'fields_freelancer_thumbnail' => esc_html__('Thumbnail', 'felan-framework'),
												'fields_freelancer_first_name' => esc_html__('First name', 'felan-framework'),
												'fields_freelancer_last_name' => esc_html__('Last name', 'felan-framework'),
												'fields_freelancer_email_address' => esc_html__('Email address', 'felan-framework'),
												'fields_freelancer_phone_number' => esc_html__('Phone number', 'felan-framework'),
												'fields_freelancer_current_position' => esc_html__('Current Position', 'felan-framework'),
												'fields_freelancer_categories' => esc_html__('Categories', 'felan-framework'),
												'fields_freelancer_description' => esc_html__('Description', 'felan-framework'),
												'fields_freelancer_date_of_birth' => esc_html__('Date of Birth', 'felan-framework'),
												'fields_freelancer_age' => esc_html__('Age', 'felan-framework'),
												'fields_freelancer_gender' => esc_html__('Gender', 'felan-framework'),
												'fields_closing_languages' => esc_html__('Languages', 'felan-framework'),
												'fields_freelancer_qualification' => esc_html__('Qualification', 'felan-framework'),
												'fields_freelancer_experience' => esc_html__('Years of Experience', 'felan-framework'),
												'fields_freelancer_salary' => esc_html__('Salary', 'felan-framework'),
												'fields_freelancer_resume' => esc_html__('Resume', 'felan-framework'),
												'fields_freelancer_social' => esc_html__('Social Network', 'felan-framework'),
												'fields_freelancer_my_profile' => esc_html__('My Profile', 'felan-framework'),
												'fields_freelancer_location' => esc_html__('Location', 'felan-framework'),
												'fields_freelancer_map' => esc_html__('Map', 'felan-framework'),
												'fields_freelancer_gallery' => esc_html__('Gallery', 'felan-framework'),
												'fields_freelancer_video' => esc_html__('Video', 'felan-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
									)
								),
							)
						)),
						apply_filters('felan_option_freelancer_submit_bottom', array()),

						//Freelancer Search
						apply_filters('felan_register_option_search_freelancer_page_top', array()),
						apply_filters('felan_register_option_search_freelancer_page_main', array(
							'id' => 'freelancer_search_group',
							'title' => esc_html__('Search Options', 'felan-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'enable_freelancer_search_bg',
									'type' => 'button_set',
									'title' => esc_html__('Enable Background', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Background', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_freelancer_search_location_top',
									'type' => 'button_set',
									'title' => esc_html__('Enable Search City/Town (Top)', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden Search City/Town', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_freelancer_search_location_radius',
									'type' => 'button_set',
									'title' => esc_html__('Enable Search location radius', 'felan-framework'),
									'subtitle' => esc_html__('Show/Hidden search location radius', 'felan-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'felan-framework'),
										'0' => esc_html__('Off', 'felan-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => "freelancer_search_color",
									'title' => esc_html__('Color', 'felan-framework'),
									'type' => 'color',
									'col' => '12',
									'default' => '',
									'required' => array(
										array("enable_freelancer_search_bg", '=', '1'),
									),
								),
								array(
									'id' => "freelancer_search_image",
									'title' => esc_html__('Image', 'felan-framework'),
									'type' => 'image',
									'default' => '',
									'col' => '12',
									'required' => array(
										array("enable_freelancer_search_bg", '=', '1'),
									),
								),
								array(
									'id' => 'freelancer_search_fields',
									'title' => esc_html__('Search Fields', 'felan-framework'),
									'type' => 'sorter',
									'default' => array(
										'top' => array(
											'freelancer_categories' => esc_html__('Categories', 'felan-framework'),
										),
										'sidebar' => $sidebar_filter,
										'disable' => array(
											'freelancer_locations' => esc_html__('Location', 'felan-framework'),
											'freelancer_ages' => esc_html__('Ages', 'felan-framework'),
											'freelancer_skills' => esc_html__('Skills', 'felan-framework'),
											'freelancer_languages' => esc_html__('Languages', 'felan-framework'),
										)
									),
								),
								array(
									'id' => 'freelancer_search_fields_freelancer_categories',
									'title' => esc_html__('Icon Categories', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_freelancer_rating',
									'title' => esc_html__('Icon Rating', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_freelancer_yoe',
									'title' => esc_html__('Icon Experience', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_freelancer_qualification',
									'title' => esc_html__('Icon Qualification', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_freelancer_ages',
									'title' => esc_html__('Icon Ages', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_freelancer_gender',
									'title' => esc_html__('Icon Gender', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_freelancer_skills',
									'title' => esc_html__('Icon Skills', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_freelancer_languages',
									'title' => esc_html__('Icon Languages', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_location',
									'title' => esc_html__('Icon City', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_state',
									'title' => esc_html__('Icon State', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
								array(
									'id' => 'freelancer_search_fields_country',
									'title' => esc_html__('Icon Country', 'felan-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
								),
							)
						)),
						apply_filters('felan_register_option_search_freelancer_page_bottom', array()),


						//Freelancer Package
						apply_filters('felan_register_option_freelancer_package_page_top', array()),
						apply_filters('felan_register_option_freelancer_package_page_main', $option_freelancer_package),
						apply_filters('felan_register_option_freelancer_package_page_bottom', array())
					),
				));
		}


		/**
		 * Service page option
		 * @return mixed
		 */

		private function service_option()
		{
			//Archive Service
			$option_archive_service = $option_single_service = $option_submit_service = $option_search_service = array();

			//Archive Service
			$option_archive_service = array(
				'id' => 'felan_archive_service_group',
				'type' => 'group',
				'title' => esc_html__('Archive Service', 'felan-framework'),
				'fields' => array(
					array(
						'id' => 'archive_service_layout',
						'type' => 'select',
						'title' => esc_html__('Service Layout', 'felan-framework'),
						'default' => 'layout-list',
						'options' => array(
							'layout-list' => esc_html__('Layout List', 'felan-framework'),
							'layout-grid' => esc_html__('Layout Grid', 'felan-framework'),
						)
					),
					array(
						'id' => 'archive_service_items_amount',
						'type' => 'text',
						'title' => esc_html__('Items Amount', 'felan-framework'),
						'default' => 12,
						'pattern' => '[0-9]*',
					),
					array(
						'id' => 'service_pagination_type',
						'type' => 'select',
						'title' => esc_html__('Type Pagination', 'felan-framework'),
						'default' => 'number',
						'options' => array(
							'number' => esc_html__('Number', 'felan-framework'),
							'loadmore' => esc_html__('Load More', 'felan-framework')
						)
					),
					array(
						'id' => "service_filter_sidebar_option",
						'title' => esc_html__('Postion Filter ', 'felan-framework'),
						'type' => 'select',
						'options' => array(
							'filter-left' => 'Filter Left',
							'filter-right' => 'Filter Right',
							'filter-canvas' => 'Filter Canvas',
						),
						'default' => 'left',
					),
					array(
						'id' => 'enable_service_single_popup',
						'type' => 'button_set',
						'title' => esc_html__('Show Single Popup', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Single Popup', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
						'required' => array(
							array("archive_jobs_layout", '!=', 'layout-full'),
						),
					),
					array(
						'id' => 'enable_service_filter_top',
						'type' => 'button_set',
						'title' => esc_html__('Show Top Filter', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Top Filter', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => 'enable_service_show_map',
						'type' => 'button_set',
						'title' => esc_html__('Show Maps', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Maps', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
						'required' => array(
							array("enable_service_single_popup", '!=', '1'),
						),
					),
					array(
						'id' => "service_map_postion",
						'title' => esc_html__('Postion Maps ', 'felan-framework'),
						'type' => 'select',
						'options' => array(
							'map-right' => 'Map Right',
							'map-top' => 'Map Top',
						),
						'default' => 'right',
						'required' => array(
							array("enable_service_show_map", '=', '1'),
							array("enable_service_single_popup", '!=', '1'),
						),
					),
					array(
						'id' => 'enable_service_show_des',
						'type' => 'button_set',
						'title' => esc_html__('Show Description', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Description', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
					),
				),
			);

			//Single Service
			$option_single_service = array(
				'id' => 'service_single_service_group',
				'type' => 'group',
				'title' => esc_html__('Single Service', 'felan-framework'),
				'fields' => array(
					array(
						'id' => 'enable_single_service_info_left',
						'type' => 'button_set',
						'title' => esc_html__('Enable Profile Sidebar Left', 'felan-framework'),
						'subtitle' => esc_html__('Enable/Disable Profile Sidebar Left', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
					),
					array(
						'id' => 'enable_sticky_service_sidebar_type',
						'type' => 'button_set',
						'title' => esc_html__('Enable Sticky Sidebar', 'felan-framework'),
						'subtitle' => esc_html__('Enable/Disable sticky sidebar when scroll', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => 'enable_single_service_related',
						'type' => 'button_set',
						'title' => esc_html__('Enable Service Related', 'felan-framework'),
						'subtitle' => esc_html__('Enable/Disable Service Related', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => 'services_details_order',
						'type' => 'sortable',
						'title' => esc_html__('Service Content Order', 'felan-framework'),
						'desc' => esc_html__('Drag and drop layout manager, to quickly organize your service content details.', 'felan-framework'),
						'options' => array(
							'enable_sp_gallery' => esc_html__('Gallery', 'felan-framework'),
							'enable_sp_descriptions' => esc_html__('Descriptions', 'felan-framework'),
							'enable_sp_skills' => esc_html__('Skills', 'felan-framework'),
							'enable_sp_package' => esc_html__('Packages', 'felan-framework'),
							'enable_sp_location' => esc_html__('Location', 'felan-framework'),
							'enable_sp_video' => esc_html__('Video', 'felan-framework'),
							'enable_sp_faq' => esc_html__('Faq', 'felan-framework'),
							'enable_sp_review' => esc_html__('Review', 'felan-framework'),
						),
						'default' => array('enable_sp_gallery', 'enable_sp_descriptions', 'enable_sp_skills', 'enable_sp_package', 'enable_sp_location', 'enable_sp_video', 'enable_sp_faq', 'enable_sp_review')
					),
					array(
						'id' => 'service_details_sidebar_order',
						'type' => 'sortable',
						'title' => esc_html__('Service Sidebar Order', 'felan-framework'),
						'desc' => esc_html__('Drag and drop layout manager, to quickly organize your service sidebar order.', 'felan-framework'),
						'options' => array(
							'enable_sidebar_sp_package' => esc_html__('Package', 'felan-framework'),
							'enable_sidebar_sp_info' => esc_html__('Information', 'felan-framework'),
						),
						'default' => array('enable_sidebar_sp_package', 'enable_sidebar_sp_info'),
					),
				),
			);

			//Submit Service
			$option_submit_service = array(
				'id' => 'service_submit_group',
				'title' => esc_html__('Service Submit', 'felan-framework'),
				'type' => 'group',
				'fields' => array(
					array(
						'id' => 'service_auto_publish',
						'title' => esc_html__('Automatically publish the submitted Service?', 'felan-framework'),
						'type' => 'button_set',
						'options' => array(
							'1' => esc_html__('Yes', 'felan-framework'),
							'0' => esc_html__('No', 'felan-framework'),
						),
						'default' => '1',
					),

					array(
						'id' => 'service_auto_publish_edited',
						'title' => esc_html__('Automatically publish the edited Service?', 'felan-framework'),
						'type' => 'button_set',
						'options' => array(
							'1' => esc_html__('Yes', 'felan-framework'),
							'0' => esc_html__('No', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => 'section_service_hide_fields',
						'title' => esc_html__('Hide Submit Form Fields', 'felan-framework'),
						'type' => 'group',
						'fields' => array(
							array(
								'id' => 'hide_service_fields',
								'type' => 'checkbox_list',
								'title' => esc_html__('Hide Submit Form Fields', 'felan-framework'),
								'subtitle' => esc_html__('Choose which fields you want to hide on New Property page?', 'felan-framework'),
								'options' => array(
									'fields_service_title' => esc_html__('Title', 'felan-framework'),
									'fields_service_category' => esc_html__('Category', 'felan-framework'),
									'fields_service_language' => esc_html__('Languages', 'felan-framework'),
									'fields_service_description' => esc_html__('Description', 'felan-framework'),
									'fields_service_skills' => esc_html__('Skills', 'felan-framework'),
									'fields_service_location' => esc_html__('Location', 'felan-framework'),
									'fields_service_map' => esc_html__('Maps', 'felan-framework'),
									'fields_service_cover_image' => esc_html__('Cover image', 'felan-framework'),
									'fields_closing_gallery' => esc_html__('Gallery', 'felan-framework'),
									'fields_service_video' => esc_html__('Video', 'felan-framework'),
									'fields_service_pricing' => esc_html__('Pricing', 'felan-framework'),
									'fields_service_package' => esc_html__('Package', 'felan-framework'),
									'fields_service_addons' => esc_html__('Addons', 'felan-framework'),
									'fields_service_faq' => esc_html__('Faqs', 'felan-framework'),
								),
								'value_inline' => false,
								'default' => array()
							),
						)
					),
				)
			);

			//Search Service
			$sidebar_filter = [
				'service-rating' => esc_html__('Rating', 'felan-framework'),
				'service-price' => esc_html__('Price', 'felan-framework'),
			];
			$custom_field_filter = felan_render_custom_field('service');
			foreach ($custom_field_filter as $custom_field) {
				$sidebar_filter[$custom_field['id']] = $custom_field['title'];
			}

			$option_search_service = array(
				'id' => 'service_search_group',
				'title' => esc_html__('Search Service', 'felan-framework'),
				'type' => 'group',
				'fields' => array(
					array(
						'id' => 'enable_service_search_bg',
						'type' => 'button_set',
						'title' => esc_html__('Enable Background', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Background', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
					),
					array(
						'id' => 'enable_service_search_location_top',
						'type' => 'button_set',
						'title' => esc_html__('Enable Search City/Town (Top)', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Search City/Town', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
					),
					array(
						'id' => 'enable_service_search_location_radius',
						'type' => 'button_set',
						'title' => esc_html__('Enable Search location radius', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden search location radius', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => "service_search_color",
						'title' => esc_html__('Color', 'felan-framework'),
						'type' => 'color',
						'col' => '12',
						'default' => '',
						'required' => array(
							array("enable_service_search_bg", '=', '1'),
						),
					),
					array(
						'id' => "service_search_image",
						'title' => esc_html__('Image', 'felan-framework'),
						'type' => 'image',
						'default' => '',
						'col' => '12',
						'required' => array(
							array("enable_service_search_bg", '=', '1'),
						),
					),
					array(
						'id' => 'service_search_fields',
						'title' => esc_html__('Search Fields', 'felan-framework'),
						'type' => 'sorter',
						'default' => array(
							'top' => array(
								'service-categories' => esc_html__('Categories', 'felan-framework'),
							),
							'sidebar' => $sidebar_filter,
							'disable' => array(
								'service-location' => esc_html__('Location', 'felan-framework'),
								'service-skills' => esc_html__('Skills', 'felan-framework'),
								'service-language' => esc_html__('Language', 'felan-framework'),
							)
						),
					),
					array(
						'id' => 'service_search_fields_service-categories',
						'title' => esc_html__('Icon Categories', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'service_search_fields_service-rating',
						'title' => esc_html__('Icon Rating', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'service_search_fields_service-skills',
						'title' => esc_html__('Icon Skills', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'service_search_fields_service-language',
						'title' => esc_html__('Icon Language', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'service_search_fields_location',
						'title' => esc_html__('Icon City', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'service_search_fields_state',
						'title' => esc_html__('Icon State', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'service_search_fields_country',
						'title' => esc_html__('Icon Country', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
				)
			);


			//Package Options
			$option_package_service = array(
				'id' => 'service_package_group',
				'title' => esc_html__('Package Service', 'felan-framework'),
				'type' => 'group',
				'fields' => array(
					array(
						'id' => "package_service",
						'type' => 'panel',
						'title' => esc_html__('Options', 'felan-framework'),
						'sort' => true,
						'panel_title' => 'label',
						'fields' => array(
							array(
								'id' => 'package_service_title',
								'title' => esc_html__('Service Title', 'felan-framework'),
								'type' => 'text',
								'default' => esc_html__('3D Mockup', 'felan-framework'),
							),
							array(
								'id' => "package_checkbox_list",
								'type' => 'checkbox_list',
								'title' => esc_html__('Package', 'felan-framework'),
								'options' =>  array(
									'basic' => esc_html__('Basic', 'felan-framework'),
									'standard' => esc_html__('Standard', 'felan-framework'),
									'premium' => esc_html__('Premium', 'felan-framework'),
								),
								'value_inline' => true,
								'default' => array(),
								'col' => '12',
							),
						)
					)
				)
			);

			return
				apply_filters('felan_register_service_option_listing_setting_page', array(
					'id' => 'felan_listing_service_setting_page_option',
					'title' => esc_html__('Services Option', 'felan-framework'),
					'icon' => 'dashicons-nametag',
					'fields' => array(

						array(
							'id' => 'enable_freelancer_service_fee',
							'type' => 'button_set',
							'title' => esc_html__('Enable Freelancer Service Fee', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Freelancer Service Fee', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),

						array(
							'id' => "freelancer_number_service_fee",
							'title' => esc_html__('Number Freelancer Service Fee', 'felan-framework'),
							'subtitle' => esc_html__('Enter (%) Freelancer Service Fee', 'felan-framework'),
							'type' => 'text',
							'default' => '10',
							'pattern' => '[0-9]*',
							'required' => array(
								array("enable_freelancer_service_fee", '=', '1')
							),
						),

						array(
							'id' => 'enable_edit_review_service',
							'type' => 'button_set',
							'title' => esc_html__('Enable Edit Review Service', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable  Edit Review Service', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),

						array(
							'id' => 'enable_auto_approve_pending_service',
							'type' => 'button_set',
							'title' => esc_html__('Enable Auto Approve For Service', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Auto Approve For Service', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),

						//Archive Service
						apply_filters('felan_register_option_archive_service_page_top', array()),
						apply_filters(
							'felan_register_option_archive_service_page_main',
							$option_archive_service
						),
						apply_filters('felan_register_option_archive_service_page_bottom', array()),

						//Single Service
						apply_filters('felan_register_option_single_service_page_top', array()),
						apply_filters(
							'felan_register_option_single_service_page_main',
							$option_single_service
						),
						apply_filters('felan_register_option_single_service_page_bottom', array()),

						//Service Submit
						apply_filters('felan_option_service_submit_top', array()),
						apply_filters(
							'felan_option_service_submit_main',
							$option_submit_service
						),
						apply_filters('felan_option_service_submit_bottom', array()),

						//Service Search
						apply_filters('felan_register_option_search_service_page_top', array()),
						apply_filters(
							'felan_register_option_search_service_page_main',
							$option_search_service
						),
						apply_filters('felan_register_option_search_service_page_bottom', array()),

						//Package Options
						apply_filters('felan_register_option_package_service_top', array()),
						apply_filters(
							'felan_register_option_package_service_main',
							$option_package_service
						),
						apply_filters('felan_register_option_package_service_bottom', array())
					),
				));
		}

		/**
		 * Project page option
		 * @return mixed
		 */

		private function project_option()
		{
			//Archive Project
			$option_archive_project = $option_single_project = $option_submit_project = $option_search_project = array();

			//Archive Project
			$option_archive_project = array(
				'id' => 'felan_archive_project_group',
				'type' => 'group',
				'title' => esc_html__('Archive Project', 'felan-framework'),
				'fields' => array(
					array(
						'id' => 'archive_project_layout',
						'type' => 'select',
						'title' => esc_html__('Project Layout', 'felan-framework'),
						'default' => 'layout-list',
						'options' => array(
							'layout-list' => esc_html__('Layout List', 'felan-framework'),
							'layout-grid' => esc_html__('Layout Grid', 'felan-framework'),
						)
					),
					array(
						'id' => 'archive_project_items_amount',
						'type' => 'text',
						'title' => esc_html__('Items Amount', 'felan-framework'),
						'default' => 12,
						'pattern' => '[0-9]*',
					),
					array(
						'id' => 'project_pagination_type',
						'type' => 'select',
						'title' => esc_html__('Type Pagination', 'felan-framework'),
						'default' => 'number',
						'options' => array(
							'number' => esc_html__('Number', 'felan-framework'),
							'loadmore' => esc_html__('Load More', 'felan-framework')
						)
					),
					array(
						'id' => "project_filter_sidebar_option",
						'title' => esc_html__('Postion Filter ', 'felan-framework'),
						'type' => 'select',
						'options' => array(
							'filter-left' => 'Filter Left',
							'filter-right' => 'Filter Right',
							'filter-canvas' => 'Filter Canvas',
						),
						'default' => 'left',
					),
					array(
						'id' => 'enable_project_single_popup',
						'type' => 'button_set',
						'title' => esc_html__('Show Single Popup', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Single Popup', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
						'required' => array(
							array("archive_jobs_layout", '!=', 'layout-full'),
						),
					),
					array(
						'id' => 'enable_project_filter_top',
						'type' => 'button_set',
						'title' => esc_html__('Show Top Filter', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Top Filter', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => 'enable_project_show_map',
						'type' => 'button_set',
						'title' => esc_html__('Show Maps', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Maps', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
						'required' => array(
							array("enable_project_single_popup", '!=', '1'),
						),
					),
					array(
						'id' => "project_map_postion",
						'title' => esc_html__('Postion Maps ', 'felan-framework'),
						'type' => 'select',
						'options' => array(
							'map-right' => 'Map Right',
							'map-top' => 'Map Top',
						),
						'default' => 'right',
						'required' => array(
							array("enable_project_show_map", '=', '1'),
							array("enable_project_single_popup", '!=', '1'),
						),
					),
					array(
						'id' => 'enable_project_show_des',
						'type' => 'button_set',
						'title' => esc_html__('Show Description', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Description', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
					),
				),
			);

			//Single Project
			$option_single_project = array(
				'id' => 'project_single_project_group',
				'type' => 'group',
				'title' => esc_html__('Single Project', 'felan-framework'),
				'fields' => array(
					array(
						'id' => 'enable_single_project_info_left',
						'type' => 'button_set',
						'title' => esc_html__('Enable Profile Sidebar Left', 'felan-framework'),
						'subtitle' => esc_html__('Enable/Disable Profile Sidebar Left', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
					),
					array(
						'id' => 'enable_sticky_project_sidebar_type',
						'type' => 'button_set',
						'title' => esc_html__('Enable Sticky Sidebar', 'felan-framework'),
						'subtitle' => esc_html__('Enable/Disable sticky sidebar when scroll', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => 'enable_single_project_related',
						'type' => 'button_set',
						'title' => esc_html__('Enable Project Related', 'felan-framework'),
						'subtitle' => esc_html__('Enable/Disable Project Related', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => 'projects_details_order',
						'type' => 'sortable',
						'title' => esc_html__('Project Content Order', 'felan-framework'),
						'desc' => esc_html__('Drag and drop layout manager, to quickly organize your project content details.', 'felan-framework'),
						'options' => array(
							'enable_sp_gallery' => esc_html__('Gallery', 'felan-framework'),
							'enable_sp_descriptions' => esc_html__('Descriptions', 'felan-framework'),
							'enable_sp_skills' => esc_html__('Skills', 'felan-framework'),
							'enable_sp_location' => esc_html__('Location', 'felan-framework'),
							'enable_sp_video' => esc_html__('Video', 'felan-framework'),
							'enable_sp_faq' => esc_html__('Faq', 'felan-framework'),
						),
						'default' => array('enable_sp_gallery', 'enable_sp_descriptions', 'enable_sp_skills', 'enable_sp_package', 'enable_sp_location', 'enable_sp_video', 'enable_sp_faq', 'enable_sp_review')
					),
					array(
						'id' => 'project_details_sidebar_order',
						'type' => 'sortable',
						'title' => esc_html__('Project Sidebar Order', 'felan-framework'),
						'desc' => esc_html__('Drag and drop layout manager, to quickly organize your project sidebar order.', 'felan-framework'),
						'options' => array(
							'enable_sidebar_sp_apply' => esc_html__('Apply', 'felan-framework'),
							'enable_sidebar_sp_info' => esc_html__('Information', 'felan-framework'),
						),
						'default' => array('enable_sidebar_sp_package', 'enable_sidebar_sp_info'),
					),
				),
			);

			//Submit Project
			$option_submit_project = array(
				'id' => 'project_submit_group',
				'title' => esc_html__('Project Submit', 'felan-framework'),
				'type' => 'group',
				'fields' => array(
					array(
						'id' => 'project_auto_publish',
						'title' => esc_html__('Automatically publish the submitted Project?', 'felan-framework'),
						'type' => 'button_set',
						'options' => array(
							'1' => esc_html__('Yes', 'felan-framework'),
							'0' => esc_html__('No', 'felan-framework'),
						),
						'default' => '1',
					),

					array(
						'id' => 'project_auto_publish_edited',
						'title' => esc_html__('Automatically publish the edited Project?', 'felan-framework'),
						'type' => 'button_set',
						'options' => array(
							'1' => esc_html__('Yes', 'felan-framework'),
							'0' => esc_html__('No', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => 'section_project_hide_fields',
						'title' => esc_html__('Hide Submit Form Fields', 'felan-framework'),
						'type' => 'group',
						'fields' => array(
							array(
								'id' => 'hide_project_fields',
								'type' => 'checkbox_list',
								'title' => esc_html__('Hide Submit Form Fields', 'felan-framework'),
								'subtitle' => esc_html__('Choose which fields you want to hide on New Property page?', 'felan-framework'),
								'options' => array(
									'fields_project_title' => esc_html__('Title', 'felan-framework'),
									'fields_project_category' => esc_html__('Category', 'felan-framework'),
									'fields_project_language' => esc_html__('Languages', 'felan-framework'),
									'fields_project_career' => esc_html__('Career', 'felan-framework'),
									'fields_project_description' => esc_html__('Description', 'felan-framework'),
									'fields_project_skills' => esc_html__('Skills', 'felan-framework'),
									'fields_project_location' => esc_html__('Location', 'felan-framework'),
									'fields_project_map' => esc_html__('Maps', 'felan-framework'),
									'fields_project_cover_image' => esc_html__('Cover image', 'felan-framework'),
									'fields_closing_gallery' => esc_html__('Gallery', 'felan-framework'),
									'fields_project_video' => esc_html__('Video', 'felan-framework'),
									'fields_project_budget' => esc_html__('Budget', 'felan-framework'),
									'fields_project_company' => esc_html__('Company', 'felan-framework'),
									'fields_project_faq' => esc_html__('Faqs', 'felan-framework'),
								),
								'value_inline' => false,
								'default' => array()
							),
						)
					),
				)
			);

			//Search Project
			$sidebar_filter = [
				'project-price' => esc_html__('Price', 'felan-framework'),
				'project-language' => esc_html__('Language', 'felan-framework'),
				'project-skills' => esc_html__('Skills', 'felan-framework'),
				'felan_location' => esc_html__('Location', 'felan-framework'),
			];
			$custom_field_filter = felan_render_custom_field('project');
			foreach ($custom_field_filter as $custom_field) {
				$sidebar_filter[$custom_field['id']] = $custom_field['title'];
			}
			$option_search_project = array(
				'id' => 'project_search_group',
				'title' => esc_html__('Search Project', 'felan-framework'),
				'type' => 'group',
				'fields' => array(
					array(
						'id' => 'enable_project_search_bg',
						'type' => 'button_set',
						'title' => esc_html__('Enable Background', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Background', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
					),
					array(
						'id' => 'enable_project_search_location_top',
						'type' => 'button_set',
						'title' => esc_html__('Enable Search City/Town (Top)', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden Search City/Town', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '0',
					),
					array(
						'id' => 'enable_project_search_location_radius',
						'type' => 'button_set',
						'title' => esc_html__('Enable Search location radius', 'felan-framework'),
						'subtitle' => esc_html__('Show/Hidden search location radius', 'felan-framework'),
						'desc' => '',
						'options' => array(
							'1' => esc_html__('On', 'felan-framework'),
							'0' => esc_html__('Off', 'felan-framework'),
						),
						'default' => '1',
					),
					array(
						'id' => "project_search_color",
						'title' => esc_html__('Color', 'felan-framework'),
						'type' => 'color',
						'col' => '12',
						'default' => '',
						'required' => array(
							array("enable_project_search_bg", '=', '1'),
						),
					),
					array(
						'id' => "project_search_image",
						'title' => esc_html__('Image', 'felan-framework'),
						'type' => 'image',
						'default' => '',
						'col' => '12',
						'required' => array(
							array("enable_project_search_bg", '=', '1'),
						),
					),
					array(
						'id' => 'project_search_fields',
						'title' => esc_html__('Search Fields', 'felan-framework'),
						'type' => 'sorter',
						'default' => array(
							'top' => array(
								'project-categories' => esc_html__('Categories', 'felan-framework'),
							),
							'sidebar' => $sidebar_filter,
							'disable' => array()
						),
					),
					array(
						'id' => 'project_search_fields_project-categories',
						'title' => esc_html__('Icon Categories', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'project_search_fields_project-rating',
						'title' => esc_html__('Icon Rating', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'project_search_fields_project-skills',
						'title' => esc_html__('Icon Skills', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'project_search_fields_project-language',
						'title' => esc_html__('Icon Language', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'project_search_fields_location',
						'title' => esc_html__('Icon City', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'project_search_fields_state',
						'title' => esc_html__('Icon State', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
					array(
						'id' => 'project_search_fields_country',
						'title' => esc_html__('Icon Country', 'felan-framework'),
						'type' => 'text',
						'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'felan-framework'),
					),
				)
			);

			return
				apply_filters('felan_register_project_option_listing_setting_page', array(
					'id' => 'felan_listing_project_setting_page_option',
					'title' => esc_html__('Projects Option', 'felan-framework'),
					'icon' => 'dashicons-image-filter',
					'fields' => array(
						array(
							'id' => 'enable_employer_project_fee',
							'type' => 'button_set',
							'title' => esc_html__('Enable Employer Project Fee', 'felan-framework'),
							'subtitle' => esc_html__('Enable/Disable Employer Project Fee', 'felan-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'felan-framework'),
								'0' => esc_html__('Off', 'felan-framework'),
							),
							'default' => '1',
						),

						array(
							'id' => "employer_number_project_fee",
							'title' => esc_html__('Number Employer Project Fee', 'felan-framework'),
							'subtitle' => esc_html__('Enter (%) Employer Project Fee', 'felan-framework'),
							'type' => 'text',
							'default' => '10',
							'pattern' => '[0-9]*',
							'required' => array(
								array("enable_employer_project_fee", '=', '1')
							),
						),

						//Archive Project
						apply_filters('felan_register_option_archive_project_page_top', array()),
						apply_filters(
							'felan_register_option_archive_project_page_main',
							$option_archive_project
						),
						apply_filters('felan_register_option_archive_project_page_bottom', array()),

						//Single Project
						apply_filters('felan_register_option_single_project_page_top', array()),
						apply_filters(
							'felan_register_option_single_project_page_main',
							$option_single_project
						),
						apply_filters('felan_register_option_single_project_page_bottom', array()),

						//Project Submit
						apply_filters('felan_option_project_submit_top', array()),
						apply_filters(
							'felan_option_project_submit_main',
							$option_submit_project
						),
						apply_filters('felan_option_project_submit_bottom', array()),

						//Project Search
						apply_filters('felan_register_option_search_project_page_top', array()),
						apply_filters(
							'felan_register_option_search_project_page_main',
							$option_search_project
						),
						apply_filters('felan_register_option_search_project_page_bottom', array()),
					),
				));
		}

		private function wallet_option()
		{
			$enable_wallet_posting_project = $wallet_posting_project_fee_amount = $enable_wallet_featured_project = $enable_wallet_featured_project =
				$enable_wallet_posting_service = $wallet_posting_service_fee_amount = $enable_wallet_featured_service = $wallet_featured_service_fee_amount =
				$enable_wallet_project_proposals = $wallet_project_proposals_fee_amount = $wallet_featured_service_fee_time = $wallet_featured_project_fee_time = $wallet_featured_project_fee_amount = array();

			$enable_wallet_posting_project =  array(
				'id' => 'enable_wallet_posting_project',
				'type' => 'button_set',
				'title' => esc_html__('Enable Fee For Posting Projects', 'felan-framework'),
				'subtitle' => esc_html__('Enable/Disable Employer Project Fee', 'felan-framework'),
				'desc' => '',
				'options' => array(
					'1' => esc_html__('On', 'felan-framework'),
					'0' => esc_html__('Off', 'felan-framework'),
				),
				'default' => '1',
			);

			$wallet_posting_project_fee_amount = array(
				'id' => "wallet_posting_project_fee_amount",
				'title' => esc_html__('Fee Amount', 'felan-framework'),
				'subtitle' => esc_html__('Enter the amount to charge for posting a project', 'felan-framework'),
				'type' => 'text',
				'default' => '10',
				'pattern' => '[0-9]*',
				'required' => array(
					array("enable_wallet_posting_project", '=', '1')
				),
			);

			$enable_wallet_featured_project = array(
				'id' => 'enable_wallet_featured_project',
				'type' => 'button_set',
				'title' => esc_html__('Enable Fee for Featured Project', 'felan-framework'),
				'subtitle' => esc_html__('Enable/Disable additional fee for featured project', 'felan-framework'),
				'desc' => '',
				'options' => array(
					'1' => esc_html__('On', 'felan-framework'),
					'0' => esc_html__('Off', 'felan-framework'),
				),
				'default' => '1',
			);

			$wallet_featured_project_fee_amount = array(
				'id' => "wallet_featured_project_fee_amount",
				'title' => esc_html__('Fee Amount', 'felan-framework'),
				'subtitle' => esc_html__('Enter the amount to charge for a featured project', 'felan-framework'),
				'type' => 'text',
				'default' => '10',
				'pattern' => '[0-9]*',
				'required' => array(array("enable_wallet_featured_project", '=', '1')),
			);

			$wallet_featured_project_fee_time = array(
				'id' => "wallet_featured_project_fee_time",
				'title' => esc_html__('Featured Project expiration days', 'felan-framework'),
				'subtitle' => esc_html__('Days until Featured Project expires', 'felan-framework'),
				'type' => 'text',
				'default' => '10',
				'pattern' => '[0-9]*',
				'required' => array(array("enable_wallet_featured_project", '=', '1')),
			);

			$enable_wallet_posting_service = array(
				'id' => 'enable_wallet_posting_service',
				'type' => 'button_set',
				'title' => esc_html__('Enable Fee For Posting Service', 'felan-framework'),
				'subtitle' => esc_html__('Enable/Disable Employer Service Fee', 'felan-framework'),
				'desc' => '',
				'options' => array(
					'1' => esc_html__('On', 'felan-framework'),
					'0' => esc_html__('Off', 'felan-framework'),
				),
				'default' => '1',
			);

			$wallet_posting_service_fee_amount = array(
				'id' => "wallet_posting_service_fee_amount",
				'title' => esc_html__('Fee Amount', 'felan-framework'),
				'subtitle' => esc_html__('Enter the amount to charge for posting a service', 'felan-framework'),
				'type' => 'text',
				'default' => '10',
				'pattern' => '[0-9]*',
				'required' => array(
					array("enable_wallet_posting_service", '=', '1')
				),
			);

			$enable_wallet_featured_service = array(
				'id' => 'enable_wallet_featured_service',
				'type' => 'button_set',
				'title' => esc_html__('Enable Fee for Featured Service', 'felan-framework'),
				'subtitle' => esc_html__('Enable/Disable additional fee for featured service', 'felan-framework'),
				'desc' => '',
				'options' => array(
					'1' => esc_html__('On', 'felan-framework'),
					'0' => esc_html__('Off', 'felan-framework'),
				),
				'default' => '1',
			);

			$wallet_featured_service_fee_amount =  array(
				'id' => "wallet_featured_service_fee_amount",
				'title' => esc_html__('Fee Amount', 'felan-framework'),
				'subtitle' => esc_html__('Enter the amount to charge for a featured service', 'felan-framework'),
				'type' => 'text',
				'default' => '10',
				'pattern' => '[0-9]*',
				'required' => array(array("enable_wallet_featured_service", '=', '1')),
			);

			$wallet_featured_service_fee_time = array(
				'id' => "wallet_featured_service_fee_time",
				'title' => esc_html__('Featured Project expiration days', 'felan-framework'),
				'subtitle' => esc_html__('Days until Featured Project expires', 'felan-framework'),
				'type' => 'text',
				'default' => '10',
				'pattern' => '[0-9]*',
				'required' => array(array("enable_wallet_featured_service", '=', '1')),
			);

			$enable_wallet_project_proposals = array(
				'id' => 'enable_wallet_project_proposals',
				'type' => 'button_set',
				'title' => esc_html__('Enable Fee for Project Proposals', 'felan-framework'),
				'subtitle' => esc_html__('Enable/Disable Fee for Employer Project Proposals', 'felan-framework'),
				'desc' => '',
				'options' => array(
					'1' => esc_html__('On', 'felan-framework'),
					'0' => esc_html__('Off', 'felan-framework'),
				),
				'default' => '1',
			);

			$wallet_project_proposals_fee_amount = array(
				'id' => "wallet_project_proposals_fee_amount",
				'title' => esc_html__('Fee Amount', 'felan-framework'),
				'subtitle' => esc_html__('Enter the amount to charge for posting a project proposal', 'felan-framework'),
				'type' => 'text',
				'default' => '10',
				'pattern' => '[0-9]*',
				'required' => array(
					array("enable_wallet_project_proposals", '=', '1')
				),
			);

			return apply_filters('felan_register_wallet_setting_page', array(
				'id'    => 'felan_wallet_page_option',
				'title' => esc_html__('Wallet Option', 'felan-framework'),
				'icon'  => 'dashicons-lock',
				'fields' => array(
					apply_filters('felan_register_option_wallet_employer_page_top', array()),
					apply_filters('felan_register_option_wallet_employer_page_main', array(
						'id'    => 'wallet_option_group',
						'title' => esc_html__('Employer Options', 'felan-framework'),
						'type'  => 'group',
						'fields' => array(
							$enable_wallet_posting_project,
							$wallet_posting_project_fee_amount,
							$enable_wallet_featured_project,
							$wallet_featured_project_fee_amount,
							$wallet_featured_project_fee_time,
						),
					)),
					apply_filters('felan_register_option_wallet_employer_page_bottom', array()),

					apply_filters('felan_register_option_wallet_freelancer_page_top', array()),
					apply_filters('felan_register_option_wallet_freelancer_page_main', array(
						'id'    => 'wallet_option_group',
						'title' => esc_html__('Freelancer Options', 'felan-framework'),
						'type'  => 'group',
						'fields' => array(
							$enable_wallet_posting_service,
							$wallet_posting_service_fee_amount,
							$enable_wallet_featured_service,
							$wallet_featured_service_fee_amount,
							$wallet_featured_service_fee_time,
							$enable_wallet_project_proposals,
							$wallet_project_proposals_fee_amount,
						),
					)),
					apply_filters('felan_register_option_wallet_freelancer_page_bottom', array()),

					apply_filters('felan_register_option_wallet_payment_page_top', array()),
					apply_filters('felan_register_option_wallet_payment_page_main', array(
						'id'    => 'wallet_option_group',
						'title' => esc_html__('Payment Options', 'felan-framework'),
						'type'  => 'group',
						'fields' => array(
							array(
								'id' => 'wallet_wire_transfer',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Wire Transfer Setting', 'felan-framework'),
							),
							array(
								'id' => 'enable_wallet_wire_transfer',
								'title' => esc_html__('Enable Wire Transfer', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => 'wallet_wire_transfer_card_number',
								'type' => 'text',
								'title' => esc_html__('Card Number', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
								'required' => array(
									array('enable_wallet_wire_transfer', '=', '1'),
								),
							),
							array(
								'id' => 'wallet_wire_transfer_card_name',
								'type' => 'text',
								'title' => esc_html__('Card Name', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
								'required' => array(
									array('enable_wallet_wire_transfer', '=', '1'),
								),
							),
							array(
								'id' => 'wallet_wire_transfer_bank_name',
								'type' => 'text',
								'title' => esc_html__('Bank Name', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
								'required' => array(
									array('enable_wallet_wire_transfer', '=', '1'),
								),
							),
							array(
								'id' => 'felan_wallet_paypal',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Paypal Setting', 'felan-framework'),
							),
							array(
								'id' => 'enable_wallet_paypal',
								'title' => esc_html__('Enable Paypal', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => 'wallet_paypal_api',
								'type' => 'select',
								'title' => esc_html__('Paypal Api', 'felan-framework'),
								'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'felan-framework'),
								'desc' => esc_html__('Update PayPal settings according to API type selection', 'felan-framework'),
								'options' => array(
									'sandbox' => esc_html__('Sandbox', 'felan-framework'),
									'live' => esc_html__('Live', 'felan-framework')
								),
								'default' => 'sandbox',
								'required' => array(
									array('enable_wallet_paypal', '=', '1'),
								),
							),
							array(
								'id' => 'wallet_paypal_client_id',
								'type' => 'text',
								'title' => esc_html__('Paypal Client ID', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
								'required' => array(
									array('enable_wallet_paypal', '=', '1'),
								),
							),
							array(
								'id' => 'wallet_paypal_client_secret_key',
								'type' => 'text',
								'title' => esc_html__('Paypal Client Secret Key', 'felan-framework'),
								'subtitle' => '',
								'default' => '',
								'required' => array(
									array('enable_wallet_paypal', '=', '1'),
								),
							),
							array(
								'id' => 'felan_wallet_stripe',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Stripe Setting', 'felan-framework'),
							),
							array(
								'id' => 'enable_wallet_stripe',
								'title' => esc_html__('Enable Stripe', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => 'wallet_stripe_secret_key',
								'type' => 'text',
								'title' => esc_html__('Stripe Secret Key', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
								'default' => '',
								'required' => array(
									array('enable_wallet_stripe', '=', '1'),
								),
							),
							array(
								'id' => 'wallet_stripe_publishable_key',
								'type' => 'text',
								'title' => esc_html__('Stripe Publishable Key', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'felan-framework'),
								'default' => '',
								'required' => array(
									array('enable_wallet_stripe', '=', '1'),
								),
							),
							array(
								'id' => 'wallet_razor',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Razor Setting', 'felan-framework'),
							),
							array(
								'id' => 'enable_wallet_razor',
								'title' => esc_html__('Enable Razor', 'felan-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => 'wallet_razor_key_id',
								'type' => 'text',
								'title' => esc_html__('Razor Key ID', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.razorpay.com/', 'felan-framework'),
								'default' => '',
								'required' => array(
									array('enable_wallet_razor', '=', '1'),
								),
							),
							array(
								'id' => 'wallet_razor_key_secret',
								'type' => 'text',
								'title' => esc_html__('Razor Key Secret', 'felan-framework'),
								'subtitle' => esc_html__('Info is taken from your account at https://dashboard.razorpay.com/', 'felan-framework'),
								'default' => '',
								'required' => array(
									array('enable_wallet_razor', '=', '1'),
								),
							),
							array(
								'id' => 'wallet_woocheckout',
								'type' => 'info',
								'style' => 'info',
								'title' => esc_html__('Woocommerce Setting', 'felan-framework'),
							),
							array(
								'id' => 'enable_wallet_woocheckout',
								'title' => esc_html__('Enable Woocommerce Checkout', 'felan-framework'),
								'type' => 'button_set',
								'subtitle' => esc_html__('Works when you activate plugin woocomerce and checkout page', 'felan-framework'),
								'options' => array(
									'1' => esc_html__('Enabled', 'felan-framework'),
									'0' => esc_html__('Disabled', 'felan-framework'),
								),
								'default' => '0',
							),
						),
					)),
					apply_filters('felan_register_option_wallet_payment_page_bottom', array()),
				),
			));
		}

		/**
		 * @return mixed|void
		 */
		private function email_management_option()
		{

			$new_jobs_apply = $jobs_activated_listing = $jobs_approved_listing = $jobs_expired_listing
				= $job_alerts = $job_approved_apply = $job_invite = array();

			$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
			if ($enable_post_type_jobs == '1') {
				$new_jobs_apply = array(
					'id' => 'new-jobs-apply',
					'title' => esc_html__('Apply Jobs', 'felan-framework'),
					'type' => 'group',
					'toggle_default' => false,
					'fields' => array(
						array(
							'id' => 'felan_info_mail_freelancer_apply',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('User Email', 'felan-framework'),
						),
						array(
							'id' => 'subject_mail_freelancer_apply',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('You have successfully applied on %website_url', 'felan-framework'),
						),
						array(
							'id' => 'mail_freelancer_apply',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__(
								'Hi %user_apply,
                                        You have applied for 1 job on %website_url.
                                        Jobs Title: %jobs_apply
                                        Jobs Url: %jobs_url',
								'felan-framework'
							),
						),
						array(
							'id' => 'felan_info_mail_employer_apply',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Admin Email', 'felan-framework'),
						),
						array(
							'id' => 'subject_mail_employer_apply',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('There is 1 freelancer applied for your job', 'felan-framework'),
						),
						array(
							'id' => 'mail_employer_apply',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__(
								'Hi,
                                        Your jobs on %website_url has been applied.
                                        Jobs Title: %jobs_apply
                                        Jobs Url: %jobs_url
                                        Cv Url: %cv_url
                                        User Apply: %user_apply
                                        User Info: %user_url',
								'felan-framework'
							),
						),
						array(
							'id' => 'felan_info_mail_freelancer_apply_nlogin',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Freelancer Email (Not Login)', 'felan-framework'),
						),
						array(
							'id' => 'subject_mail_freelancer_apply_nlogin',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('There is 1 freelancer applied for your job', 'felan-framework'),
						),
						array(
							'id' => 'mail_freelancer_apply_nlogin',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__(
								'Hi,
                                        Your jobs on %website_url has been applied.
                                        Jobs Title: %jobs_apply
                                        Jobs Url: %jobs_url
                                        CV Url: %cv_url
                                        Message: %message',
								'felan-framework'
							),
						),
					)
				);

				$jobs_activated_listing = array(
					'id' => 'email-activated-listing',
					'title' => esc_html__('Activated Jobs', 'felan-framework'),
					'type' => 'group',
					'toggle_default' => false,
					'fields' => array(
						array(
							'id' => 'felan_user_mail_activated_listing',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('User Email', 'felan-framework'),
						),
						array(
							'id' => 'subject_mail_activated_listing',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('Your purchase was activated', 'felan-framework'),
						),
						array(
							'id' => 'mail_activated_listing',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__('Hi thfelan,Your purchase on %website_url is activated! You should go and check it out.', 'felan-framework'),
						)
					)
				);

				$jobs_approved_listing = array(
					'id' => 'email-approved-listing',
					'title' => esc_html__('Approved Jobs', 'felan-framework'),
					'type' => 'group',
					'toggle_default' => false,
					'fields' => array(
						array(
							'id' => 'felan_user_mail_approved_listing',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('User Email', 'felan-framework'),
						),
						array(
							'id' => 'subject_mail_approved_listing',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('Your listing approved', 'felan-framework'),
						),
						array(
							'id' => 'mail_approved_listing',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__(
								"Hi thfelan,
                                        Your jobs on %website_url has been approved.
                                        Your Name:%your_name
                                        Jobs Title:%listing_title
                                        Jobs Url: %listing_url",
								'felan-framework'
							),
						)
					)
				);

				$jobs_expired_listing = array(
					'id' => 'email-expired-listing',
					'title' => esc_html__('Expired Jobs', 'felan-framework'),
					'type' => 'group',
					'toggle_default' => false,
					'fields' => array(
						array(
							'id' => 'felan_user_mail_expired_listing',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('User Email', 'felan-framework'),
						),
						array(
							'id' => 'subject_mail_expired_listing',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('Your listing expired', 'felan-framework'),
						),
						array(
							'id' => 'mail_expired_listing',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__(
								"Hi,
                                        Your jobs on %website_url has been expired.

                                        Jobs Title:%listing_title
                                        Jobs Url: %listing_url",
								'felan-framework'
							),
						)
					)
				);

				$job_alerts = array(
					'id' => 'email-job-alerts',
					'title' => esc_html__('Job Alerts', 'felan-framework'),
					'type' => 'group',
					'toggle_default' => false,
					'fields' => array(
						array(
							'id' => 'felan_first_mail_job_alerts',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('First Email', 'felan-framework'),
						),
						array(
							'id' => 'subject_first_mail_job_alerts',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('New Job Announcement', 'felan-framework'),
						),
						array(
							'id' => 'first_mail_job_alerts',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__(
								'Hello,
												Thank you for signing up, you will receive %frequency job related information.
												Best regards,
												Do not notify me anymore? <a href="%unregister_link">Click here</a>',
							),
						),
						array(
							'id' => 'felan_last_mail_job_alerts',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Last Email', 'felan-framework'),
						),
						array(
							'id' => 'subject_last_mail_job_alerts',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('New Job Announcement', 'felan-framework'),
						),
						array(
							'id' => 'last_mail_job_alerts',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__(
								'Hello,
												There are %number jobs found at your request, job listing below:
												%list_job
												Best regards,
												Do not notify me anymore? <a href="%unregister_link">Click here</a>',
							),
						),
					)
				);

				$job_approved_apply = array(
					'id' => 'email-approved-apply',
					'title' => esc_html__('Approved Applicants', 'felan-framework'),
					'type' => 'group',
					'toggle_default' => false,
					'fields' => array(
						array(
							'id' => 'felan_user_mail_approved_applicants',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('User Email', 'felan-framework'),
						),
						array(
							'id' => 'subject_mail_approved_applicants',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('Your Applicants approved', 'felan-framework'),
						),
						array(
							'id' => 'mail_approved_applicants',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__(
								"Hi,
                                        You have been approved by the employer: %employer_name
                                        Jobs Apply: %jobs_apply
                                        Jobs Url: %jobs_url",
								'felan-framework'
							),
						)
					)
				);

				$job_invite = array(
					'id' => 'email-job-invite',
					'title' => esc_html__('Invite Jobs', 'felan-framework'),
					'type' => 'group',
					'toggle_default' => false,
					'fields' => array(
						array(
							'id' => 'felan_user_mail_job_invite',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('User Email', 'felan-framework'),
						),
						array(
							'id' => 'subject_mail_job_invite',
							'type' => 'text',
							'title' => esc_html__('Subject', 'felan-framework'),
							'default' => esc_html__('Your Jobs Invite', 'felan-framework'),
						),
						array(
							'id' => 'mail_job_invite',
							'type' => 'editor',
							'args' => array(
								'media_buttons' => true,
								'quicktags' => true,
							),
							'title' => esc_html__('Content', 'felan-framework'),
							'default' => esc_html__(
								"Hi, You have been invite by the employer: %employer_name
                                                Jobs Invite: %jobs_invite",
								'felan-framework'
							),
						)
					)
				);
			}

			return apply_filters('felan_register_option_email_management', array(
				'id' => 'felan_email_management_option',
				'title' => esc_html__('Email Template', 'felan-framework'),
				'icon' => 'dashicons-email-alt',
				'fields' => array_merge(
					apply_filters('felan_register_option_email_management_top', array()),
					array(
						//Header
						array(
							'id' => 'email-header',
							'title' => esc_html__('Header Email', 'felan-framework'),
							'type' => 'group',
							'toggle_default' => false,
							'fields' => array(
								array(
									'id' => 'logo_email',
									'type' => 'text',
									'title' => esc_html__('Logo Email', 'felan-framework'),
									'default' => '',
									'subtitle' => esc_html__('Choose link logo for email', 'felan-framework'),
								),
								array(
									'id' => 'title_email',
									'type' => 'text',
									'title' => esc_html__('Title', 'felan-framework'),
									'default' => esc_html__('Welcome to %website_url!', 'felan-framework'),
								),
							)
						),
						//Content
						array(
							'id' => 'email-content',
							'title' => esc_html__('Content Email', 'felan-framework'),
							'type' => 'group',
							'toggle_default' => false,
							'fields' => array(
								array(
									'id' => 'email-new-user',
									'title' => esc_html__('New Registed User', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'felan_user_mail_register_user',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'felan-framework'),
										),
										array(
											'id' => 'subject_mail_register_user',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Your username and password on %website_url', 'felan-framework'),
										),
										array(
											'id' => 'mail_register_user',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'You can login now using the below credentials:
                                        Username: %your_name
                                        Password: %user_pass_register
                                        If you have any problems, please contact us.
                                        Thank you!',
												'felan-framework'
											),
										),
										array(
											'id' => 'felan_admin_mail_register_user',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Admin Email', 'felan-framework'),
										),
										array(
											'id' => 'subject_admin_mail_register_user',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New User Registration', 'felan-framework'),
										),
										array(
											'id' => 'admin_mail_register_user',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'New user registration on %website_url.
                                                E-mail: %user_login_register',
												'felan-framework'
											),
										)
									)
								),

								array(
									'id' => 'mail-verify-user',
									'title' => esc_html__('Verify User', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'felan_user_mail_verify_user',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'felan-framework'),
										),
										array(
											'id' => 'subject_mail_verify_user',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Account Verification', 'felan-framework'),
										),
										array(
											'id' => 'mail_verify_user',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												"To verify your email address, please use the following One Time Password (OTP):
													%code_verify_user
												If you have any problems, please contact us.
                                        		Thank you!",
												'felan-framework'
											),
										)
									)
								),

								array(
									'id' => 'mail-identity-verify',
									'title' => esc_html__('Identity Verify', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'felan_user_identity_mail_verify_user',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Send to User when submit', 'felan-framework'),
										),
										array(
											'id' => 'subject_identity_mail_verify_user',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Identity Verification', 'felan-framework'),
										),
										array(
											'id' => 'identity_mail_verify_user',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												"Thank you for submitting your information, please wait for our review.",
												'felan-framework'
											),
										),

										array(
											'id' => 'felan_admin_identity_mail_verify_user',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Send to Admin when submit', 'felan-framework'),
										),
										array(
											'id' => 'subject_admin_identity_mail_verify_user',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Identity Verification', 'felan-framework'),
										),
										array(
											'id' => 'admin_identity_mail_verify_user',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'New Identity Verification your website.
                                                Please go to Felan Extensions ->Identity Verification to verify',
												'felan-framework'
											),
										),

										array(
											'id' => 'felan_user_identity_mail_verified',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Send to User when admin verified', 'felan-framework'),
										),
										array(
											'id' => 'subject_identity_mail_verified',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Verified Information', 'felan-framework'),
										),
										array(
											'id' => 'identity_mail_verified',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												"Your information has been approved.",
												'felan-framework'
											),
										),

										array(
											'id' => 'felan_user_identity_mail_cancel_verify',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Send to User when admin cancel verifiy', 'felan-framework'),
										),
										array(
											'id' => 'subject_identity_mail_cancel_verify',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Your Information was canceled', 'felan-framework'),
										),
										array(
											'id' => 'identity_mail_cancel_verify',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												"Your information has been canceled. Please submit again.",
												'felan-framework'
											),
										),
									)
								),

								array(
									'id' => 'email-activated-package',
									'title' => esc_html__('Activated Package', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'felan_user_mail_activated_package',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'felan-framework'),
										),
										array(
											'id' => 'subject_mail_activated_package',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Your purchase was activated', 'felan-framework'),
										),
										array(
											'id' => 'mail_activated_package',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												"Hi thfelan,
                                        Welcome to %website_url and thank you for purchasing a plan with us. We are excited you have chosen %website_name . %website_name is a great jobs to advertise and search properties.
                                        You plan on  %website_url activated! You can now list your properties according to you plan.",
												'felan-framework'
											),
										)
									)
								),

								$new_jobs_apply,
								$jobs_activated_listing,
								$jobs_approved_listing,
								$jobs_expired_listing,
								$job_alerts,
								$job_approved_apply,
								$job_invite,

								array(
									'id' => 'email-new-wire-transfer',
									'title' => esc_html__('New Wire Transfer', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'felan_user_mail_new_wire_transfer',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'felan-framework'),
										),
										array(
											'id' => 'subject_mail_new_wire_transfer',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('You ordfeland a new Wire Transfer', 'felan-framework'),
										),
										array(
											'id' => 'mail_new_wire_transfer',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'We received your Wire Transfer payment request on  %website_url !
                                        Please follow the instructions below in order to start submitting properties as soon as possible.
                                        The invoice number is: %invoice_no, Amount: %total_price.',
												'felan-framework'
											),
										),
										array(
											'id' => 'felan_admin_mail_new_wire_transfer',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Admin Email', 'felan-framework'),
										),
										array(
											'id' => 'subject_admin_mail_new_wire_transfer',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Somebody ordfeland a new Wire Transfer', 'felan-framework'),
										),
										array(
											'id' => 'admin_mail_new_wire_transfer',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'We received your Wire Transfer payment request on  %website_url !
                                        Please follow the instructions below in order to start submitting properties as soon as possible.
                                        The invoice number is: %invoice_no, Amount: %total_price.',
												'felan-framework'
											),
										)
									)
								),
								array(
									'id' => 'email-meetings',
									'title' => esc_html__('Notification Meetings', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'felan_info_mail_notification_meetings',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Notification Meetings', 'felan-framework'),
										),
										array(
											'id' => 'subject_mail_notification_meetings',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('You have a notification about the meeting', 'felan-framework'),
										),
										array(
											'id' => 'mail_notification_meetings',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'There is a meeting coming up at %website_url!
                                                Job related meeting: %jobs_meetings!
                                                Time for the meeting to start %date_time.',
												'felan-framework'
											),
										),
									)
								),

								array(
									'id' => 'new-freelancer-send-proposal',
									'title' => esc_html__('Freelancer send a proposal', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'felan_info_mail_project_send_proposal',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Send proposal', 'felan-framework'),
										),
										array(
											'id' => 'subject_mail_project_send_proposal',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Proposal Submitted for Your Project', 'felan-framework'),
										),
										array(
											'id' => 'mail_project_send_proposal',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has submitted a new proposal for your project: %project_name.
												You can review the details of the proposal and respond directly here:
												View Proposal
												Best regards',
												'felan-framework'
											),
										),
										array(
											'id' => 'felan_info_mail_project_update_proposal',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Update proposal', 'felan-framework'),
										),
										array(
											'id' => 'subject_mail_project_update_proposal',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('A Proposal has been updated', 'felan-framework'),
										),
										array(
											'id' => 'mail_project_update_proposal',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has updated a proposal for your project: %project_name.
												You can review the details of the proposal and respond directly here:
												View Proposal
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-freelancer-send-message-dispute',
									'title' => esc_html__('Freelancer sends a message in the dispute activity detail for project', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_project_send_message_dispute',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Message in Dispute for Project', 'felan-framework'),
										),
										array(
											'id' => 'mail_project_send_message_dispute',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has sent a new message regarding the dispute for project: %project_name.
												You can view the message and respond here:
												View Dispute Details
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-freelancer-approves-dispute-project',
									'title' => esc_html__('Freelancer approves a dispute project', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_approve_dispute_project',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('A Freelancer Has Approved a dispute', 'felan-framework'),
										),
										array(
											'id' => 'mail_approve_dispute_project',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has Approved dispute for your project: %project_name.
												You can review here:
												View Dispute Details
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-freelancer-denies-dispute-project',
									'title' => esc_html__('Freelancer denies a dispute project', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_denies_dispute_project',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('A Freelancer Has denies a dispute', 'felan-framework'),
										),
										array(
											'id' => 'mail_denies_dispute_project',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has denies dispute for your project: %project_name.
												You can review here:
												View Dispute Details
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-freelancer-approves-dispute-service',
									'title' => esc_html__('Freelancer approves a dispute service', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_approve_dispute_service',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('A Freelancer Has Approved a dispute', 'felan-framework'),
										),
										array(
											'id' => 'mail_approve_dispute_service',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has Approved dispute for your service: %service_name.
												You can review here:
												View Dispute Details
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-freelancer-denies-dispute-service',
									'title' => esc_html__('Freelancer denies a dispute service', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_denies_dispute_service',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('A Freelancer Has denies a dispute', 'felan-framework'),
										),
										array(
											'id' => 'mail_denies_dispute_service',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has denies dispute for your service: %service_name.
												You can review here:
												View Dispute Details
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-freelancer-send-message-proposal',
									'title' => esc_html__('Freelancer sends a message in the proposal activity detail', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'felan_info_mail_project_send_message_proposal',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Send message', 'felan-framework'),
										),
										array(
											'id' => 'subject_mail_project_send_message_proposal',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Message in Proposal for Your Project', 'felan-framework'),
										),
										array(
											'id' => 'mail_project_send_message_proposal',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has sent a new message regarding the proposal for your project: %project_name.
												You can view the message and respond here:
												View Message
												Best regards',
												'felan-framework'
											),
										),

									)
								),
								array(
									'id' => 'new-freelancer-send-message-service',
									'title' => esc_html__('Freelancer send message in service activity detail', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_service_freelancer_send_message',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Message in Service', 'felan-framework'),
										),
										array(
											'id' => 'mail_service_freelancer_send_message',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has sent a new message for service: %service_name.
												You can review the details and respond directly here:
												View message
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-freelancer-send-message-dispute-service',
									'title' => esc_html__('Freelancer sends a message in the dispute activity detail for service', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_service_send_message_dispute',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Message in Dispute for service', 'felan-framework'),
										),
										array(
											'id' => 'mail_service_send_message_dispute',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %employer_name,
												%freelancer_name has sent a new message regarding the dispute for service: %service_name.
												You can view the message and respond here:
												View Dispute Details
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-place-order-service',
									'title' => esc_html__('Employer Orders New Service', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(

										array(
											'id' => 'subject_mail_service_employer_place_order',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Service Order Received', 'felan-framework'),
										),
										array(
											'id' => 'mail_service_employer_place_order',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												You have received a new service order from %employer_name for your service: %service_name.
												Please review the order details and take action here:
												View Order Details
												Thank you',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-complete-service',
									'title' => esc_html__('Employer Complete Service', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_employer_complete_service',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Service Completed', 'felan-framework'),
										),
										array(
											'id' => 'mail_employer_complete_service',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has marked the service: %service_name as completed..
												Please review the order details here:
												View Order Details
												Thank you',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-cancel-service',
									'title' => esc_html__('Employer Cancel Service', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_employer_cancel_service',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Service Canceled', 'felan-framework'),
										),
										array(
											'id' => 'mail_employer_cancel_service',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has marked the service: %service_name as canceled..
												Please review the order details here:
												View Order Details
												Thank you',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-create-dispute-service',
									'title' => esc_html__('Employer create dispute for service', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_service_employer_create_dispute',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Employer create dispute service', 'felan-framework'),
										),
										array(
											'id' => 'mail_service_employer_create_dispute',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has created a dispute for service: %service_name.
												You can review the details and respond directly here:
												View dispute
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-send-message-dispute-service',
									'title' => esc_html__('Employer sends a message in the dispute activity detail for service', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_service_employer_send_message_dispute',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Message in Dispute for service', 'felan-framework'),
										),
										array(
											'id' => 'mail_service_employer_send_message_dispute',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has sent a new message regarding the dispute for service: %service_name.
												You can view the message and respond here:
												View Dispute Details
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-send-message-dispute',
									'title' => esc_html__('Employer sends a message in the dispute activity detail for project', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_project_employer_send_message_dispute',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Message in Dispute for Project', 'felan-framework'),
										),
										array(
											'id' => 'mail_project_employer_send_message_dispute',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has sent a new message regarding the dispute for project: %project_name.
												You can view the message and respond here:
												View Dispute Details
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-approve-proposal',
									'title' => esc_html__('Employer Approve proposal', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_project_employer_approve_proposal',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Employer Approve proposal', 'felan-framework'),
										),
										array(
											'id' => 'mail_project_employer_approve_proposal',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has approved proposal for project: %project_name.
												You can review the details of the proposal and respond directly here:
												View proposal
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-rejected-proposal',
									'title' => esc_html__('Employer rejected proposal', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_project_employer_rejected_proposal',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Employer rejected proposal', 'felan-framework'),
										),
										array(
											'id' => 'mail_project_employer_rejected_proposal',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has rejected proposal for project: %project_name.
												You can review the details of the proposal and respond directly here:
												View proposal
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-create-dispute-proposal',
									'title' => esc_html__('Employer create dispute proposal', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_project_employer_create_dispute_proposal',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('Employer create dispute proposal', 'felan-framework'),
										),
										array(
											'id' => 'mail_project_employer_create_dispute_proposal',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has created a dispute proposal for project: %project_name.
												You can review the details and respond directly here:
												View dispute
												Best regards',
												'felan-framework'
											),
										),
									)
								),
								array(
									'id' => 'new-employer-send-message-proposal',
									'title' => esc_html__('Employer sends a message in the proposal activity detail', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'felan_info_mail_employer_send_message_proposal',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Send message', 'felan-framework'),
										),
										array(
											'id' => 'subject_mail_employer_send_message_proposal',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Message in Proposal for Project', 'felan-framework'),
										),
										array(
											'id' => 'mail_employer_send_message_proposal',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has sent a new message regarding the proposal for project: %project_name.
												You can view the message and respond here:
												View Message
												Best regards',
												'felan-framework'
											),
										),

									)
								),
								array(
									'id' => 'new-employer-send-message-service',
									'title' => esc_html__('Employer send message in service activity detail', 'felan-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'subject_mail_service_employer_send_message',
											'type' => 'text',
											'title' => esc_html__('Subject', 'felan-framework'),
											'default' => esc_html__('New Message in Service', 'felan-framework'),
										),
										array(
											'id' => 'mail_service_employer_send_message',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'felan-framework'),
											'default' => esc_html__(
												'Hello %freelancer_name,
												%employer_name has sent a new message for service: %service_name.
												You can review the details and respond directly here:
												View message
												Best regards',
												'felan-framework'
											),
										),
									)
								),
							)
						),
						//Footer
						array(
							'id' => 'email-footer',
							'title' => esc_html__('Footer Email', 'felan-framework'),
							'type' => 'group',
							'toggle_default' => false,
							'fields' => array(
								array(
									'id' => 'mail_footer_user',
									'type' => 'editor',
									'args' => array(
										'media_buttons' => true,
										'quicktags' => true,
									),
									'title' => esc_html__('Content', 'felan-framework'),
									'default' => esc_html__(
										'Do you need help? Contact us
                                        T. (00) 658 54332
                                        E. hello@ricetheme.com
                                        © 2024 RiceTheme. All Right Reserved.',
										'felan-framework'
									),
								),
							)
						),
					),
					apply_filters('felan_register_option_email_management_bottom', array())
				)
			));
		}
	}
}
