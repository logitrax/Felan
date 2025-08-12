<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 *  Class Felan_Admin_Freelancer
 */
class Felan_Admin_Freelancer
{
	/**
	 *  Register custom columns
	 *
	 *  @param  $columns
	 *  @return  array
	 *
	 */
	public function register_custom_column_titles($columns)
	{
		unset($columns['tags']);

		$columns['thumb']       = esc_html__('Avatar', 'felan-framework');
		$columns['title']       = esc_html__('Freelancer', 'felan-framework');
		$columns['cate']  = esc_html__('Categories', 'felan-framework');
		$columns['skills'] = esc_html__('Skills', 'felan-framework');
		$columns['author']      = esc_html__('Author', 'felan-framework');
		$new_columns    = array();
		$custom_order   = array('cb', 'thumb', 'title', 'cate', 'skills', 'author', 'date');

		foreach ($custom_order as $colname) {
			$new_columns[$colname] = $columns[$colname];
		}

		return $new_columns;
	}

	/**
	 *  Display custom column for freelancers
	 *
	 *  @param  $column
	 *
	 */
	public function display_custom_column($column)
	{
		global $post;
		switch ($column) {
			case 'thumb':
				$author_id = get_post_field('post_author', $post->ID);
				$freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
				if (!empty($freelancer_avatar)) {
					echo '<img src = " ' . $freelancer_avatar . '" alt=""/>';
				} else {
					echo '&ndash;';
				}
				break;
			case 'cate':
				echo felan_admin_taxonomy_terms($post->ID, 'freelancer_categories', 'freelancer');
				break;
			case 'skills':
				echo felan_admin_taxonomy_terms($post->ID, 'freelancer_skills', 'freelancer');
				break;
			case 'author':
				echo '<a href="' . esc_url(add_query_arg('author', $post->post_author)) . '">' . get_the_author() . '</a>';
				break;
		}
	}

	/**
	 *  Sortable columns
	 *
	 *  @param  $columns
	 *  @return mixed
	 *
	 */
	public function sortable_columns($columns)
	{
		$columns['cate']  = 'cate';
		$columns['skills']  = 'skills';
		$columns['post_date']   = 'post_date';
		return $columns;
	}

	/**
	 *  Modify Freelancer Slug
	 *
	 *  @param  $existing_slug
	 *  @return $string
	 *
	 */
	public function modify_freelancer_slug($existing_slug)
	{
		$freelancer_url_slug = felan_get_option('freelancer_url_slug');
		$enable_slug_categories = felan_get_option('enable_slug_categories');
		if ($freelancer_url_slug) {
			if ($enable_slug_categories == 1) {
				return $freelancer_url_slug . '/%freelancer_categories%';
			} else {
				return $freelancer_url_slug;
			}
		}

		return $existing_slug;
	}

	public function modify_freelancer_has_archive($existing_slug)
	{
		$freelancer_url_slug = felan_get_option('freelancer_url_slug');
		if ($freelancer_url_slug) {
			return $freelancer_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer categories slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_categories_url_slug($existing_slug)
	{
		$freelancer_categories_url_slug = felan_get_option('freelancer_categories_url_slug');
		if ($freelancer_categories_url_slug) {
			return $freelancer_categories_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer ages slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_ages_url_slug($existing_slug)
	{
		$freelancer_ages_url_slug = felan_get_option('freelancer_ages_url_slug');
		if ($freelancer_ages_url_slug) {
			return $freelancer_ages_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer languages slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_languages_url_slug($existing_slug)
	{
		$freelancer_languages_url_slug = felan_get_option('freelancer_languages_url_slug');
		if ($freelancer_languages_url_slug) {
			return $freelancer_languages_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer qualification slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_qualification_url_slug($existing_slug)
	{
		$freelancer_qualification_url_slug = felan_get_option('freelancer_qualification_url_slug');
		if ($freelancer_qualification_url_slug) {
			return $freelancer_qualification_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer salary types slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_salary_types_url_slug($existing_slug)
	{
		$freelancer_salary_types_url_slug = felan_get_option('freelancer_salary_types_url_slug');
		if ($freelancer_salary_types_url_slug) {
			return $freelancer_salary_types_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer yoe slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_yoe_url_slug($existing_slug)
	{
		$freelancer_yoe_url_slug = felan_get_option('freelancer_yoe_url_slug');
		if ($freelancer_yoe_url_slug) {
			return $freelancer_yoe_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer education levels slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_education_levels_url_slug($existing_slug)
	{
		$freelancer_education_levels_url_slug = felan_get_option('freelancer_education_levels_url_slug');
		if ($freelancer_education_levels_url_slug) {
			return $freelancer_education_levels_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer skills slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_skills_url_slug($existing_slug)
	{
		$freelancer_skills_url_slug = felan_get_option('freelancer_skills_url_slug');
		if ($freelancer_skills_url_slug) {
			return $freelancer_skills_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer gender slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_gender_url_slug($existing_slug)
	{
		$freelancer_gender_url_slug = felan_get_option('freelancer_gender_url_slug');
		if ($freelancer_gender_url_slug) {
			return $freelancer_gender_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify freelancer locations slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_freelancer_locations_url_slug($existing_slug)
	{
		$freelancer_locations_url_slug = felan_get_option('freelancer_locations_url_slug');
		if ($freelancer_locations_url_slug) {
			return $freelancer_locations_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Approve freelancer
	 */
	public function approve_freelancer()
	{
		if (!empty($_GET['approve_freelancer']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'approve_freelancer') && current_user_can('publish_post', $_GET['approve_freelancer'])) {
			$post_id = absint(felan_clean(wp_unslash($_GET['approve_freelancer'])));
			$listing_data = array(
				'ID' => $post_id,
				'post_status' => 'publish'
			);
			wp_update_post($listing_data);
			wp_redirect(remove_query_arg('approve_freelancer', add_query_arg('approve_freelancer', $post_id, admin_url('edit.php?post_type=freelancer'))));
			exit;
		}
	}

	/**
	 * @param $actions
	 * @param $post
	 * @return mixed
	 */
	public function modify_list_row_actions($actions, $post)
	{
		// Check for your post type.
		if ($post->post_type == 'freelancer') {
			if (in_array($post->post_status, array('pending'))) {
				$actions['freelancer-approve'] = '<a href="' . wp_nonce_url(add_query_arg('approve_freelancer', $post->ID), 'approve_freelancer') . '">' . esc_html__('Approve', 'felan-framework') . '</a>';
			}
		}
		return $actions;
	}

	/**
	 * filter_restrict_manage_company
	 */
	public function filter_restrict_manage_freelancer()
	{
		global $typenow;
		$post_type = 'freelancer';
		if ($typenow == $post_type) {
			$taxonomy_arr  = array('freelancer_categories', 'freelancer_skills');
			foreach ($taxonomy_arr as $taxonomy) {
				$selected      = isset($_GET[$taxonomy]) ? felan_clean(wp_unslash($_GET[$taxonomy])) : '';
				$info_taxonomy = get_taxonomy($taxonomy);
				wp_dropdown_categories(array(
					'show_option_all' => __("All {$info_taxonomy->label}"),
					'taxonomy'        => $taxonomy,
					'name'            => $taxonomy,
					'orderby'         => 'name',
					'selected'        => $selected,
					'hide_empty'      => false,
				));
			}
			?>
            <?php
		};
	}

	/**
	 *  Show Freelancer
	 *
	 */
	public function show_freelancers()
	{
		if (!empty($_GET['show_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'show_listing') && current_user_can('publish_post', $_GET['show_listing'])) {
			$post_id = absint(felan_clean(wp_unslash($_GET['show_listing'])));
			$listing_data   = array(
				'ID'            => $post_id,
				'post_status'   => 'publish'
			);

			wp_update_post($listing_data);
			wp_redirect(remove_query_arg('show_listing', add_query_arg('show_listing', $post_id, admin_url('edit.php?post_type=freelancer'))));
			exit;
		}
	}

	/**
	 * h_filter
	 * @param $query
	 */
	public function freelancer_filter($query)
	{
		global $pagenow;
		$post_type = 'freelancer';
		$q_vars    = &$query->query_vars;
		if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
			$taxonomy_arr  = array('freelancer_categories', 'freelancer_skills');
			foreach ($taxonomy_arr as $taxonomy) {
				if (isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
					$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
					$q_vars[$taxonomy] = $term->slug;
				}
			}
		}
	}

	public function add_badge_menu()
	{
		global $menu;
		$freelancer_count = wp_count_posts('freelancer')->pending;
		if ($freelancer_count && is_array($menu)) {
			foreach ($menu as $key => $value) {
				if ($menu[$key][2] == 'edit.php?post_type=freelancer') {
					$menu[$key][0] .= ' <span class="update-plugins">' . $freelancer_count . '</span>';
					return;
				}
			}
		}
	}

	public function felan_verifications() {

		$user_id = isset($_POST['user_id']) ? sanitize_text_field(wp_unslash($_POST['user_id'])) : '';

		if (empty($user_id)) {
			wp_send_json_error(esc_html__('Missing user id.', 'felan-framework'));
		}

		update_user_meta($user_id, 'user_verify', 1);

		$user_info = get_userdata( $user_id );
		$email     = $user_info->user_email;
		felan_send_email( $email, 'identity_mail_verified', [] );

		echo json_encode(array('success' => true));

		wp_die();

	}

	public function felan_cancel_verifications() {

		$user_id = isset($_POST['user_id']) ? sanitize_text_field(wp_unslash($_POST['user_id'])) : '';

		if (empty($user_id)) {
			wp_send_json_error(esc_html__('Missing user id.', 'felan-framework'));
		}

		delete_user_meta($user_id, 'front_image_id');
        delete_user_meta($user_id, 'front_image_url');

		delete_user_meta($user_id, 'back_image_id');
		delete_user_meta($user_id, 'back_image_url');

		delete_user_meta($user_id, 'verify_type');
		delete_user_meta($user_id, 'user_verify');
		delete_user_meta($user_id, 'date_verify_submit');

		$user_info = get_userdata( $user_id );
		$email     = $user_info->user_email;
		felan_send_email( $email, 'identity_mail_cancel_verify', [] );

		echo json_encode(array('success' => true));

		wp_die();

	}

	public function felan_user_verifications_info() {
		$user_id         = isset( $_POST['user_id'] ) ? sanitize_text_field(wp_unslash($_POST['user_id'])) : '';
		$front_image_url = get_the_author_meta( 'front_image_url', $user_id );
		$back_image_url  = get_the_author_meta( 'back_image_url', $user_id );
		$verify_type     = get_the_author_meta( 'verify_type', $user_id );

		$front_image_html = $front_image_url ? '<img src="' . esc_url( $front_image_url ) . '">' : '<span>' . esc_html__( 'Empty', 'felan-framework' ) . '</span>';
		$back_image_html  = $back_image_url ? '<img src="' . esc_url( $back_image_url ) . '">' : '<span>' . esc_html__( 'Empty', 'felan-framework' ) . '</span>';


		$button_verify = '<a style="margin-right: 10px" class="cancel-verify-button button" data-user-id="' . esc_attr( $user_id ) . '">
			' . esc_html__( 'Cancel Verify', 'felan-framework' ) . '
			</a>';

		if ( get_the_author_meta( 'user_verify', $user_id ) != 1 ) {
			$button_verify .= '<a class="verify-button button" data-user-id="' . esc_attr( $user_id ) . '">
			' . esc_html__( 'Verify', 'felan-framework' ) . '
			</a>';
		}

		$html = '<div class="felan-verify-box">
			<h3>' . esc_html__( 'Verifications Info.', 'felan-framework' ) . '</h3>
			<h4>' . esc_html__( 'Type: ', 'felan-framework' ) . $verify_type . '</h4>
			<div class="felan-verify-item front-side">
				<h4>' . esc_html__( 'Front side.', 'felan-framework' ) . '</h4>
				' . $front_image_html . '
			</div>
			<div class="felan-verify-item back-side">
				<h4>' . esc_html__( 'Back side.', 'felan-framework' ) . '</h4>
				' . $back_image_html . '
			</div>
		</div>' . $button_verify;
		$data = [
			'status' => 1,
			'html' => $html,
		];
		echo json_encode( $data );
		wp_die();
	}
}
