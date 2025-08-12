<?php
if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Felan_Company')) {
	/**
	 * Class Felan_Invoice
	 */
	class Felan_Company
	{

		/**
		 * Company breadcrumb
		 */
		public function felan_company_breadcrumb()
		{ ?>
			<div class="container container-breadcrumb">
				<?php get_template_part('templates/global/breadcrumb'); ?>
			</div>
<?php }

		/**
		 * Get total my company
		 * @return int
		 */
		public function get_total_my_company()
		{
			$args = array(
				'post_type' => 'company',
				'meta_query' => array(
					array(
						'key' => FELAN_METABOX_PREFIX . 'company_user_id',
						'value' => get_current_user_id(),
						'compare' => '='
					)
				)
			);
			$companys = new WP_Query($args);
			wp_reset_postdata();
			return $companys->found_posts;
		}

		/**
		 * Insert company
		 * @param $payment_type
		 * @param $item_id
		 * @param $user_id
		 * @param $payment_for
		 * @param $payment_method
		 * @param int $paid
		 * @param string $payment_id
		 * @param string $payer_id
		 * @return int|WP_Error
		 */
		public function insert_company($payment_type, $item_id, $user_id, $payment_for, $payment_method, $paid = 0, $payment_id = '', $payer_id = '')
		{

			$price_featured_submission = felan_get_option('price_featured_listing', '0');
			$price_featured_submission = floatval($price_featured_submission);
			$total_money = 0;
			if ($payment_type != 'Package') {
				if ($payment_for == 3) {
					$total_money = $price_featured_submission;
				}
			} else {
				$package_free = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'package_free', true);
				if ($package_free == 1) {
					$total_money = 0;
				} else {
					$total_company = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'package_price', true);
				}
			}
			$time = time();
			$company_date = date('Y-m-d H:i:s', $time);

			$felan_meta = array();
			$felan_meta['company_item_id'] = $item_id;
			$felan_meta['company_item_price'] = $total_money;
			$felan_meta['company_purchase_date'] = $company_date;
			$felan_meta['company_user_id'] = $user_id;
			$felan_meta['company_payment_type'] = $payment_type;
			$felan_meta['company_payment_method'] = $payment_method;
			$felan_meta['trans_payment_id'] = $payment_id;
			$felan_meta['trans_payer_id'] = $payer_id;
			$posttitle = 'Invoice_' . $payment_method . '_' . $total_money . $user_id;
			$args = array(
				'post_title'    => $posttitle,
				'post_status'    => 'publish',
				'post_type'     => 'company'
			);

			$rw = felan_get_page_by_title($posttitle, 'company');
			$company_payment_status = get_post_meta($rw->ID, FELAN_METABOX_PREFIX . 'company_payment_status', true);

			if (empty($rw->ID) || ($rw->ID && $company_payment_status == '1')) {
				$company_id =  wp_insert_post($args);
				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_user_id', $user_id);
				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_item_id', $item_id);
				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_price', $total_money);
				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_date', $company_date);
				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_payment_type', $payment_type);
				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_payment_method', $payment_method);
				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_payment_status', $paid);

				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'trans_payment_id', $payment_id);
				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'trans_payer_id', $payer_id);

				update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_meta', $felan_meta);
				$update_post = array(
					'ID'         => $company_id,
				);
				wp_update_post($update_post);
			} else {
				$company_id = $rw->ID;
			}
			return $company_id;
		}

		/**
		 * get_company_meta
		 * @param $post_id
		 * @param bool|false $field
		 * @return array|bool|mixed
		 */
		public function get_company_meta($post_id, $field = false)
		{
			$defaults = array(
				'company_item_id' => '',
				'company_item_price' => '',
				'company_purchase_date' => '',
				'company_user_id' => '',
				'company_payment_type' => '',
				'company_payment_method' => '',
				'trans_payment_id' => '',
				'trans_payer_id' => '',
			);
			$meta = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'company_meta', true);
			$meta = wp_parse_args((array)$meta, $defaults);

			if ($field) {
				if (isset($meta[$field])) {
					return $meta[$field];
				} else {
					return false;
				}
			}
			return $meta;
		}

		/**
		 * @param $payment_type
		 * @return string
		 */
		public static function get_company_payment_type($payment_type)
		{
			switch ($payment_type) {
				case 'Package':
					return esc_html__('Package', 'felan-framework');
					break;
				case 'Listing':
					return esc_html__('Listing', 'felan-framework');
					break;
				case 'Upgrade_To_Featured':
					return esc_html__('Upgrade to Featured', 'felan-framework');
					break;
				case 'Listing_With_Featured':
					return esc_html__('Listing with Featured', 'felan-framework');
					break;
				default:
					return '';
			}
		}

		/**
		 * @param $payment_method
		 * @return string
		 */
		public static function get_company_payment_method($payment_method)
		{
			switch ($payment_method) {
				case 'Paypal':
					return esc_html__('Paypal', 'felan-framework');
					break;
				case 'Stripe':
					return esc_html__('Stripe', 'felan-framework');
					break;
				case 'Wire_Transfer':
					return esc_html__('Wire Transfer', 'felan-framework');
					break;
				case 'Free_Package':
					return esc_html__('Free Package', 'felan-framework');
					break;
				default:
					return '';
			}
		}
		/**
		 * Print Invoice
		 */
		public function company_print_ajax()
		{
			if (!isset($_POST['company_id']) || !is_numeric($_POST['company_id'])) {
				return;
			}
			$company_id = absint(wp_unslash($_POST['company_id']));
			$isRTL = 'false';
			if (isset($_POST['isRTL'])) {
				$isRTL = $_POST['isRTL'];
			}
			felan_get_template('company/company-print.php', array('company_id' => intval($company_id), 'isRTL' => $isRTL));
			wp_die();
		}

		/**
		 * submit review
		 */
		public function submit_review_ajax()
		{
			global $wpdb, $current_user;
			wp_get_current_user();
			$user_id                    = $current_user->ID;
			$user                       = get_user_by('id', $user_id);
			$company_id                   = isset($_POST['company_id']) ? felan_clean(wp_unslash($_POST['company_id'])) : '';
			$rating_salary_value       = isset($_POST['rating_salary']) ? felan_clean(wp_unslash($_POST['rating_salary'])) : '';
			$rating_company_value         = isset($_POST['rating_company']) ? felan_clean(wp_unslash($_POST['rating_company'])) : '';
			$rating_skill_value      = isset($_POST['rating_skill']) ? felan_clean(wp_unslash($_POST['rating_skill'])) : '';
			$rating_work_value   = isset($_POST['rating_work']) ? felan_clean(wp_unslash($_POST['rating_work'])) : '';
			$my_review    = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $company_id AND comment.user_id = $user_id  AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
			$comment_approved = 1;
			$auto_publish_review_company = get_option('comment_moderation');
			if ($auto_publish_review_company == 1) {
				$comment_approved = 0;
			}
			if ($my_review == null) {
				$data = array();
				$user = $user->data;

				$data['comment_post_ID']      = $company_id;
				$data['comment_content']      = isset($_POST['message']) ?  wp_filter_post_kses($_POST['message']) : '';
				$data['comment_date']         = current_time('mysql');
				$data['comment_approved']     = $comment_approved;
				$data['comment_author']       = $user->user_login;
				$data['comment_author_email'] = $user->user_email;
				$data['comment_author_url']   = $user->user_url;
				$data['user_id']              = $user_id;

				$comment_id = wp_insert_comment($data);

				add_comment_meta($comment_id, 'company_salary_rating', $rating_salary_value);
				add_comment_meta($comment_id, 'company_company_rating', $rating_company_value);
				add_comment_meta($comment_id, 'company_skill_rating', $rating_skill_value);
				add_comment_meta($comment_id, 'company_work_rating', $rating_work_value);

				$company_rating = (intval($rating_salary_value) + intval($rating_company_value) + intval($rating_skill_value) + intval($rating_work_value)) / 4;
				$company_rating = number_format((float)$company_rating, 2, '.', '');

				add_comment_meta($comment_id, 'company_rating', $company_rating);

				if ($comment_approved == 1) {
					apply_filters('felan_company_rating_meta', $company_id, $company_rating);
				}

				felan_get_data_ajax_notification($company_id, 'add-review-company');
			} else {
				$data = array();

				$data['comment_ID']       = $my_review->comment_ID;
				$data['comment_post_ID']  = $company_id;
				$data['comment_content']  = isset($_POST['message']) ? wp_filter_post_kses($_POST['message']) : '';
				$data['comment_date']     = current_time('mysql');
				$data['comment_approved'] = $comment_approved;

				wp_update_comment($data);
				update_comment_meta($my_review->comment_ID, 'company_salary_rating', $rating_salary_value);
				update_comment_meta($my_review->comment_ID, 'company_company_rating', $rating_company_value);
				update_comment_meta($my_review->comment_ID, 'company_skill_rating', $rating_skill_value);
				update_comment_meta($my_review->comment_ID, 'company_work_rating', $rating_work_value);

				$company_rating = (intval($rating_salary_value) + intval($rating_company_value) + intval($rating_skill_value) + intval($rating_work_value)) / 4;
				$company_rating = number_format((float)$company_rating, 2, '.', '');

				update_comment_meta($my_review->comment_ID, 'company_rating', $company_rating, $my_review->meta_value);

				if ($comment_approved == 1) {
					apply_filters('felan_company_rating_meta', $company_id, $company_rating, false, $my_review->meta_value);
				}

				if (!empty($company_id)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'has_company_review', '1');
				}
			}

			echo json_encode(array('success' => true));

			wp_die();
		}

		/**
		 * @param $company_id
		 * @param $rating_value
		 * @param bool|true $comment_exist
		 * @param int $old_rating_value
		 */
		public function rating_meta_filter($company_id, $rating_value, $comment_exist = true, $old_rating_value = 0)
		{
			update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_rating', $rating_value);
		}

		/**
		 * submit review
		 */
		public function submit_reply_ajax()
		{
			check_ajax_referer('felan_submit_reply_ajax_nonce', 'felan_security_submit_reply');
			global $wpdb, $current_user;
			wp_get_current_user();
			$user_id  = $current_user->ID;
			$user     = get_user_by('id', $user_id);
			$company_id = isset($_POST['company_id']) ? felan_clean(wp_unslash($_POST['company_id'])) : '';
			$comment_approved = 1;
			$auto_publish_review_company = get_option('comment_moderation');
			if ($auto_publish_review_company == 1) {
				$comment_approved = 0;
			}
			$data = array();
			$user = $user->data;

			$data['comment_post_ID']      = $company_id;
			$data['comment_content']      = isset($_POST['message']) ? wp_filter_post_kses($_POST['message']) : '';
			$data['comment_date']         = current_time('mysql');
			$data['comment_approved']     = $comment_approved;
			$data['comment_author']       = $user->user_login;
			$data['comment_author_email'] = $user->user_email;
			$data['comment_author_url']   = $user->user_url;
			$data['comment_parent']       = isset($_POST['comment_id']) ? felan_clean(wp_unslash($_POST['comment_id'])) : '';
			$data['user_id']              = $user_id;

			$comment_id = wp_insert_comment($data);

			echo json_encode(array('success' => true));

			wp_die();
		}

		/**
		 * Company submit
		 */
		public function company_submit_ajax()
		{
			$company_form               = isset($_REQUEST['company_form']) ? felan_clean(wp_unslash($_REQUEST['company_form'])) : '';
			$company_action             = isset($_REQUEST['company_action']) ? felan_clean(wp_unslash($_REQUEST['company_action'])) : '';
			$company_id                 = isset($_REQUEST['company_id']) ? felan_clean(wp_unslash($_REQUEST['company_id'])) : '';
			$company_title              = isset($_REQUEST['company_title']) ? felan_clean(wp_unslash($_REQUEST['company_title'])) : '';
			$company_categories         = isset($_REQUEST['company_categories']) ? felan_clean(wp_unslash($_REQUEST['company_categories'])) : '';
			$company_new_categories     = isset($_REQUEST['company_new_categories']) ? felan_clean(wp_unslash($_REQUEST['company_new_categories'])) : '';
			$company_url      = isset($_REQUEST['company_url']) ? felan_clean(wp_unslash($_REQUEST['company_url'])) : '';
			$company_des       = isset($_REQUEST['company_des']) ? wp_kses_post(wp_unslash($_REQUEST['company_des'])) : '';
			$company_website      = isset($_REQUEST['company_website']) ? felan_clean(wp_unslash($_REQUEST['company_website'])) : '';
			$company_founded       = isset($_REQUEST['company_founded']) ? felan_clean(wp_unslash($_REQUEST['company_founded'])) : '';
			$company_phone        = isset($_REQUEST['company_phone']) ? felan_clean(wp_unslash($_REQUEST['company_phone'])) : '';
			$company_phone_code        = isset($_REQUEST['company_phone_code']) ? felan_clean(wp_unslash($_REQUEST['company_phone_code'])) : '';
			$company_email      = isset($_REQUEST['company_email']) ? felan_clean(wp_unslash($_REQUEST['company_email'])) : '';
			$company_size      = isset($_REQUEST['company_size']) ? felan_clean(wp_unslash($_REQUEST['company_size'])) : '';

			$company_twitter = isset($_REQUEST['company_twitter']) ? felan_clean(wp_unslash($_REQUEST['company_twitter'])) : '';
			$company_linkedin = isset($_REQUEST['company_linkedin']) ? felan_clean(wp_unslash($_REQUEST['company_linkedin'])) : '';
			$company_facebook = isset($_REQUEST['company_facebook']) ? felan_clean(wp_unslash($_REQUEST['company_facebook'])) : '';
			$company_instagram = isset($_REQUEST['company_instagram']) ? felan_clean(wp_unslash($_REQUEST['company_instagram'])) : '';
			$company_social_name = isset($_REQUEST['company_social_name']) ? felan_clean(wp_unslash($_REQUEST['company_social_name'])) : '';
			$company_social_url = isset($_REQUEST['company_social_url']) ? felan_clean(wp_unslash($_REQUEST['company_social_url'])) : '';

			$company_avatar_id = isset($_REQUEST['company_avatar_id']) ? felan_clean(wp_unslash($_REQUEST['company_avatar_id'])) : '';
			$company_avatar_url = isset($_REQUEST['company_avatar_url']) ? felan_clean(wp_unslash($_REQUEST['company_avatar_url'])) : '';
			$company_thumbnail_url = isset($_REQUEST['company_thumbnail_url']) ? felan_clean(wp_unslash($_REQUEST['company_thumbnail_url'])) : '';
			$company_thumbnail_id  = isset($_REQUEST['company_thumbnail_id']) ? felan_clean(wp_unslash($_REQUEST['company_thumbnail_id'])) : '';
			$felan_gallery_ids          = isset($_REQUEST['felan_gallery_ids']) ? felan_clean(wp_unslash($_REQUEST['felan_gallery_ids'])) : '';
			$company_video_url      = isset($_REQUEST['company_video_url']) ? felan_clean(wp_unslash($_REQUEST['company_video_url'])) : '';
			$company_map_location       = isset($_REQUEST['company_map_location']) ? felan_clean(wp_unslash($_REQUEST['company_map_location'])) : '';
			$company_new_location      = isset($_REQUEST['company_new_location']) ? felan_clean(wp_unslash($_REQUEST['company_new_location'])) : '';
			$company_map_address        = isset($_REQUEST['company_map_address']) ? felan_clean(wp_unslash($_REQUEST['company_map_address'])) : '';
			$company_location       = isset($_REQUEST['company_location']) ? felan_clean(wp_unslash($_REQUEST['company_location'])) : '';
			$company_latitude      = isset($_REQUEST['company_latitude']) ? felan_clean(wp_unslash($_REQUEST['company_latitude'])) : '';
			$company_longtitude       = isset($_REQUEST['company_longtitude']) ? felan_clean(wp_unslash($_REQUEST['company_longtitude'])) : '';

			$custom_field_company        = isset($_REQUEST['custom_field_company']) ? felan_clean(wp_unslash($_REQUEST['custom_field_company'])) : '';

			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;

			if ($company_new_location) {
				$custom_place_city = trim($company_new_location);
				$custom_place_city_slug = strtolower($custom_place_city);
				$custom_place_city_slug = str_replace(' ', '-', $custom_place_city_slug);
				$result = '';
				if (!term_exists($custom_place_city, 'felan_location')) {
					$result = wp_insert_term(
						$custom_place_city,
						'felan_location',
						array(
							'slug' => $custom_place_city,
						)
					);
				}
				if ($result && array_key_exists('term_id', $result)) {
					$company_location = $result['term_id'];
				}
			}

			if ($company_new_categories) {
				$new_categories = trim($company_new_categories);
				$result = '';
				if (!term_exists($new_categories, 'company-categories')) {
					$result = wp_insert_term(
						$new_categories,
						'company-categories',
						array(
							'slug' => $new_categories,
						)
					);
				}
				if ($result && array_key_exists('term_id', $result)) {
					$company_categories = $result['term_id'];
				}
			}

			$new_company = array();
			if ($company_action) {
				$new_company['post_type'] = 'company';
				$new_company['post_author'] = $user_id;

				if (isset($company_title)) {
					$new_company['post_title'] = $company_title;
				}

				if (isset($company_url)) {
					$new_company['post_name'] = $company_url;
				}

				if (isset($company_des)) {
					$new_company['post_content'] = $company_des;
				}

				$submit_action = $company_form;
				$auto_publish         = felan_get_option('company_auto_publish', 1);
				$auto_publish_edited  = felan_get_option('company_auto_publish_edited', 1);
				if ($submit_action == 'submit-company') {
					$company_id = 0;
					if ($auto_publish == 1) {
						$new_company['post_status'] = 'publish';
					} else {
						$new_company['post_status'] = 'pending';
					}
					if (!empty($new_company['post_title'])) {
						$company_id = wp_insert_post($new_company, true);
					}
					echo json_encode(array('success' => true));
				} elseif ($submit_action == 'edit-company') {
					$company_id        = absint(wp_unslash($company_id));
					$company = get_post($company_id);
					$new_company['ID'] = intval($company_id);

					if ($auto_publish_edited == 1) {
						$new_company['post_status'] = 'publish';
					} else {
						$new_company['post_status'] = 'pending';
					}

					$company_id = wp_update_post($new_company);
					echo json_encode(array('success' => true));
				}
			}

			if ($company_id > 0) {
				//category
				if (!empty($company_categories)) {
					$company_categories = intval($company_categories);
					wp_set_object_terms($company_id, $company_categories, 'company-categories');
				}

				if (!empty($company_size)) {
					$company_size = intval($company_size);
					wp_set_object_terms($company_id, $company_size, 'company-size');
				}

				if (!empty($company_location)) {
					$company_location = intval($company_location);
					wp_set_object_terms($company_id, $company_location, 'felan_location');
				}

				//field
				if (isset($company_website)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_website', $company_website);
				}

				if (isset($company_founded)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_founded', $company_founded);
				}

				if (isset($company_phone)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_phone', $company_phone);
				}

				if (isset($company_phone_code)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_phone_code', $company_phone_code);
				}

				if (isset($company_twitter)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_twitter', $company_twitter);
				}

				if (isset($company_linkedin)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_linkedin', $company_linkedin);
				}

				if (isset($company_facebook)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_facebook', $company_facebook);
				}

				if (isset($company_instagram)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_instagram', $company_instagram);
				}

				if (isset($company_email)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_email', $company_email);
				}

				if (isset($company_video_url)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_video_url', $company_video_url);
				}

				if (isset($company_map_address)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_address', $company_map_address);
				}

				if (!empty($company_website) && !empty($company_phone) && !empty($company_location)) {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_green_tick', 1);
				} else {
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_green_tick', 0);
				}

				if (isset($company_map_location)) {
					$lat_lng = $company_map_location;
					$address = $company_map_address;
					$arr_location = array(
						'location' => $lat_lng,
						'address' => $address,
					);
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'felan_location', $arr_location);
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_address', $company_map_address);
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_latitude', $company_latitude);
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_longtitude', $company_longtitude);
				}

				if (!empty($company_social_name)) {
					$social_data  = array();
					for ($i = 1; $i < count($company_social_name); $i++) {
						$social_data[] = array(
							FELAN_METABOX_PREFIX . 'company_social_name'   => $company_social_name[$i],
							FELAN_METABOX_PREFIX . 'company_social_url'    => $company_social_url[$i],
						);
					}
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_social_tabs', $social_data);
				}

				if (isset($company_avatar_url) && isset($company_avatar_id)) {
					$company_avatar = array(
						'id'  => $company_avatar_id,
						'url' => $company_avatar_url,
					);
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo', $company_avatar);
				}

				if (isset($company_thumbnail_url) && isset($company_thumbnail_id)) {
					$company_thumbnail = array(
						'id'  => $company_thumbnail_id,
						'url' => $company_thumbnail_url,
					);
					update_post_meta($company_id, '_thumbnail_id', $company_thumbnail_id);
				}

				if (isset($felan_gallery_ids)) {
					$str_img_ids = '';
					foreach ($felan_gallery_ids as $company_img_id) {
						$felan_gallery_ids[] = intval($company_img_id);
						$str_img_ids .= '|' . intval($company_img_id);
					}
					$str_img_ids = substr($str_img_ids, 1);
					update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_images', $str_img_ids);
				}

				$get_additional = felan_render_custom_field('company');
				if (count($get_additional) > 0 && !empty($custom_field_company)) {
					foreach ($get_additional as $key => $field) {
						if (count($custom_field_company) > 0 && isset($custom_field_company[$field['id']])) {
							if ($field['type'] == 'checkbox_list') {
								$arr = array();
								foreach ($custom_field_company[$field['id']] as $v) {
									$arr[] = $v;
								}
								update_post_meta($company_id, $field['id'], $arr);
							} elseif ($field['type'] == 'image') {
								$custom_field_company_url = wp_get_attachment_url($custom_field_company[$field['id']]);
								$custom_image = array(
									'id'  => $custom_field_company[$field['id']],
									'url'  => $custom_field_company_url,
								);
								update_post_meta($company_id, $field['id'], $custom_image);
							} else {
								update_post_meta($company_id, $field['id'], $custom_field_company[$field['id']]);
							}
						}
					}
				}
			}

			wp_die();
		}
	}
}
