<?php
if (!defined('ABSPATH')) {
	exit;
}
if (!class_exists('Felan_Service')) {
	/**
	 * Class Felan_Service
	 */
	class Felan_Service
	{

		public function felan_set_service_view_date()
		{
			$id = get_the_ID();
			$today = date('Y-m-d', time());
			$views_date = get_post_meta($id, 'felan_view_service', true);
			if ($views_date != '' || is_array($views_date)) {
				if (!isset($views_date[$today])) {
					if (count($views_date) > 60) {
						array_shift($views_date);
					}
					$views_date[$today] = 1;
				} else {
					$views_date[$today] = intval($views_date[$today]) + 1;
				}
			} else {
				$views_date = array();
				$views_date[$today] = 1;
			}
			update_post_meta($id, 'felan_view_service', $views_date);
		}

		/**
		 * Submit review
		 */
		public function submit_review_ajax()
		{
			global $wpdb, $current_user;
			wp_get_current_user();
			$user_id                    = $current_user->ID;
			$user                       = get_user_by('id', $user_id);
			$service_id                   = isset($_POST['service_id']) ? felan_clean(wp_unslash($_POST['service_id'])) : '';
			$order_id                   = isset($_POST['order_id']) ? felan_clean(wp_unslash($_POST['order_id'])) : '';
			$rating_salary_value       = isset($_POST['rating_salary']) ? felan_clean(wp_unslash($_POST['rating_salary'])) : '';
			$rating_service_value         = isset($_POST['rating_service']) ? felan_clean(wp_unslash($_POST['rating_service'])) : '';
			$rating_skill_value      = isset($_POST['rating_skill']) ? felan_clean(wp_unslash($_POST['rating_skill'])) : '';
			$rating_work_value   = isset($_POST['rating_work']) ? felan_clean(wp_unslash($_POST['rating_work'])) : '';
			$my_review    = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $service_id AND comment.user_id = $user_id  AND meta.meta_key = 'service_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
			$comment_approved = 1;
			$auto_publish_review_service = get_option('comment_moderation');
			if ($auto_publish_review_service == 1) {
				$comment_approved = 0;
			}
			if ($my_review == null) {
				$data = array();
				$user = $user->data;

				$data['comment_post_ID']      = $service_id;
				$data['comment_content']      = isset($_POST['message']) ?  wp_filter_post_kses($_POST['message']) : '';
				$data['comment_date']         = current_time('mysql');
				$data['comment_approved']     = $comment_approved;
				$data['comment_author']       = $user->user_login;
				$data['comment_author_email'] = $user->user_email;
				$data['comment_author_url']   = $user->user_url;
				$data['user_id']              = $user_id;

				$comment_id = wp_insert_comment($data);

				add_comment_meta($comment_id, 'service_salary_rating', $rating_salary_value);
				add_comment_meta($comment_id, 'service_service_rating', $rating_service_value);
				add_comment_meta($comment_id, 'service_skill_rating', $rating_skill_value);
				add_comment_meta($comment_id, 'service_work_rating', $rating_work_value);

				$service_rating = (intval($rating_salary_value) + intval($rating_service_value) + intval($rating_skill_value) + intval($rating_work_value)) / 4;
				$service_rating = number_format((float)$service_rating, 2, '.', '');

				add_comment_meta($comment_id, 'service_rating', $service_rating);

				if ($comment_approved == 1) {
					apply_filters('felan_service_rating_meta', $service_id, $service_rating);
				}

				felan_get_data_ajax_notification($service_id, 'add-review-service');
			} else {
				$data = array();

				$data['comment_ID']       = $my_review->comment_ID;
				$data['comment_post_ID']  = $service_id;
				$data['comment_content']  = isset($_POST['message']) ? wp_filter_post_kses($_POST['message']) : '';
				$data['comment_date']     = current_time('mysql');
				$data['comment_approved'] = $comment_approved;

				wp_update_comment($data);
				update_comment_meta($my_review->comment_ID, 'service_salary_rating', $rating_salary_value);
				update_comment_meta($my_review->comment_ID, 'service_service_rating', $rating_service_value);
				update_comment_meta($my_review->comment_ID, 'service_skill_rating', $rating_skill_value);
				update_comment_meta($my_review->comment_ID, 'service_work_rating', $rating_work_value);

				$service_rating = (intval($rating_salary_value) + intval($rating_service_value) + intval($rating_skill_value) + intval($rating_work_value)) / 4;
				$service_rating = number_format((float)$service_rating, 2, '.', '');

				update_comment_meta($my_review->comment_ID, 'service_rating', $service_rating, $my_review->meta_value);

				if ($comment_approved == 1) {
					apply_filters('felan_service_rating_meta', $service_id, $service_rating, false, $my_review->meta_value);
				}
			}

			$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $service_id AND meta.meta_key = 'service_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
			$get_comments   = $wpdb->get_results($comments_query);
			$rating_number = 0;

			if (!is_null($get_comments)) {

				$service_salary_rating = $service_service_rating = $service_skill_rating = $service_work_rating = array();
				foreach ($get_comments as $comment) {
					if (intval(get_comment_meta($comment->comment_ID, 'service_salary_rating', true)) != 0) {
						$service_salary_rating[]         = intval(get_comment_meta($comment->comment_ID, 'service_salary_rating', true));
					}
					if (intval(get_comment_meta($comment->comment_ID, 'service_service_rating', true)) != 0) {
						$service_service_rating[]         = intval(get_comment_meta($comment->comment_ID, 'service_service_rating', true));
					}
					if (intval(get_comment_meta($comment->comment_ID, 'service_skill_rating', true)) != 0) {
						$service_skill_rating[]         = intval(get_comment_meta($comment->comment_ID, 'service_skill_rating', true));
					}
					if (intval(get_comment_meta($comment->comment_ID, 'service_work_rating', true)) != 0) {
						$service_work_rating[]         = intval(get_comment_meta($comment->comment_ID, 'service_work_rating', true));
					}

					if ($comment->comment_approved == 1) {
						if (!empty($comment->meta_value) && $comment->meta_value != 0.00) {
							$total_reviews++;
						}
						if ($comment->meta_value > 0) {
							$total_stars += $comment->meta_value;
						}
					}
				}

				if ($total_reviews != 0) {
					$rating_number = number_format($total_stars / $total_reviews, 1);
				}
			}

			update_post_meta($service_id, 'total_point_review', (int)($rating_number));

			if (!empty($order_id)) {
				update_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', 'completed');
				update_post_meta($service_id, FELAN_METABOX_PREFIX . 'has_service_review', '1');
			}

			echo json_encode(array('success' => true, '$order_id' => $order_id));

			wp_die();
		}

		/**
		 * @param $service_id
		 * @param $rating_value
		 * @param bool|true $comment_exist
		 * @param int $old_rating_value
		 */
		public function rating_meta_filter($service_id, $rating_value, $comment_exist = true, $old_rating_value = 0)
		{
			update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_rating', $rating_value);
		}

		/**
		 * Submit review
		 */
		public function submit_reply_ajax()
		{
			check_ajax_referer('felan_submit_reply_ajax_nonce', 'felan_security_submit_reply');
			global $wpdb, $current_user;
			wp_get_current_user();
			$user_id  = $current_user->ID;
			$user     = get_user_by('id', $user_id);
			$service_id = isset($_POST['service_id']) ? felan_clean(wp_unslash($_POST['service_id'])) : '';
			$comment_approved = 1;
			$auto_publish_review_service = get_option('comment_moderation');
			if ($auto_publish_review_service == 1) {
				$comment_approved = 0;
			}
			$data = array();
			$user = $user->data;

			$data['comment_post_ID']      = $service_id;
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
		public function service_submit_ajax()
		{
			$service_form               = isset($_REQUEST['service_form']) ? felan_clean(wp_unslash($_REQUEST['service_form'])) : '';
			$service_id                 = isset($_REQUEST['service_id']) ? felan_clean(wp_unslash($_REQUEST['service_id'])) : '';
			$service_title              = isset($_REQUEST['service_title']) ? felan_clean(wp_unslash($_REQUEST['service_title'])) : '';
			$service_categories         = isset($_REQUEST['service_categories']) ? felan_clean(wp_unslash($_REQUEST['service_categories'])) : '';
			$service_skills         = isset($_REQUEST['service_skills']) ? felan_clean(wp_unslash($_REQUEST['service_skills'])) : '';
			$service_des       = isset($_REQUEST['service_des']) ?  wp_kses_post(wp_unslash($_REQUEST['service_des'])) : '';
			$service_language      = isset($_REQUEST['service_language']) ? felan_clean(wp_unslash($_REQUEST['service_language'])) : '';

			$service_time       = isset($_REQUEST['service_time']) ? felan_clean(wp_unslash($_REQUEST['service_time'])) : '';
			$service_quantity       = isset($_REQUEST['service_quantity']) ? wp_kses_post(wp_unslash($_REQUEST['service_quantity'])) : '';
			$service_basic_des      = isset($_REQUEST['service_basic_des']) ? wp_kses_post(wp_unslash($_REQUEST['service_basic_des'])) : '';
			$service_standard_des       = isset($_REQUEST['service_standard_des']) ? wp_kses_post(wp_unslash($_REQUEST['service_standard_des'])) : '';
			$service_premium_des      = isset($_REQUEST['service_premium_des']) ? wp_kses_post(wp_unslash($_REQUEST['service_premium_des'])) : '';
			$service_basic_price     = isset($_REQUEST['service_basic_price']) ? felan_clean(wp_unslash($_REQUEST['service_basic_price'])) : '';
			$service_standard_price     = isset($_REQUEST['service_standard_price']) ? felan_clean(wp_unslash($_REQUEST['service_standard_price'])) : '';
			$service_premium_price     = isset($_REQUEST['service_premium_price']) ? felan_clean(wp_unslash($_REQUEST['service_premium_price'])) : '';
			$service_basic_time     = isset($_REQUEST['service_basic_time']) ? felan_clean(wp_unslash($_REQUEST['service_basic_time'])) : '';
			$service_standard_time     = isset($_REQUEST['service_standard_time']) ? felan_clean(wp_unslash($_REQUEST['service_standard_time'])) : '';
			$service_premium_time     = isset($_REQUEST['service_premium_time']) ? felan_clean(wp_unslash($_REQUEST['service_premium_time'])) : '';
			$service_basic_revisions    = isset($_REQUEST['service_basic_revisions']) ? felan_clean(wp_unslash($_REQUEST['service_basic_revisions'])) : '';
			$service_standard_revisions     = isset($_REQUEST['service_standard_revisions']) ? felan_clean(wp_unslash($_REQUEST['service_standard_revisions'])) : '';
			$service_premium_revisions    = isset($_REQUEST['service_premium_revisions']) ? felan_clean(wp_unslash($_REQUEST['service_premium_revisions'])) : '';
			$service_basic_number_revisions  = isset($_REQUEST['service_basic_number_revisions']) ? felan_clean(wp_unslash($_REQUEST['service_basic_number_revisions'])) : '';
			$service_standard_number_revisions     = isset($_REQUEST['service_standard_number_revisions']) ? felan_clean(wp_unslash($_REQUEST['service_standard_number_revisions'])) : '';
			$service_premium_number_revisions    = isset($_REQUEST['service_premium_number_revisions']) ? felan_clean(wp_unslash($_REQUEST['service_premium_number_revisions'])) : '';

			$service_thumbnail_url = isset($_REQUEST['service_thumbnail_url']) ? felan_clean(wp_unslash($_REQUEST['service_thumbnail_url'])) : '';
			$service_thumbnail_id  = isset($_REQUEST['service_thumbnail_id']) ? felan_clean(wp_unslash($_REQUEST['service_thumbnail_id'])) : '';
			$felan_gallery_ids          = isset($_REQUEST['felan_gallery_ids']) ? felan_clean(wp_unslash($_REQUEST['felan_gallery_ids'])) : '';
			$service_video_url      = isset($_REQUEST['service_video_url']) ? felan_clean(wp_unslash($_REQUEST['service_video_url'])) : '';
			$service_map_location       = isset($_REQUEST['service_map_location']) ? felan_clean(wp_unslash($_REQUEST['service_map_location'])) : '';
			$service_map_address        = isset($_REQUEST['service_map_address']) ? felan_clean(wp_unslash($_REQUEST['service_map_address'])) : '';
			$service_location       = isset($_REQUEST['service_location']) ? felan_clean(wp_unslash($_REQUEST['service_location'])) : '';
			$service_latitude      = isset($_REQUEST['service_latitude']) ? felan_clean(wp_unslash($_REQUEST['service_latitude'])) : '';
			$service_longtitude       = isset($_REQUEST['service_longtitude']) ? felan_clean(wp_unslash($_REQUEST['service_longtitude'])) : '';

			$service_package_title       = isset($_REQUEST['service_package_title']) ? felan_clean(wp_unslash($_REQUEST['service_package_title'])) : '';
			$service_package_basic       = isset($_REQUEST['service_package_basic']) ? felan_clean(wp_unslash($_REQUEST['service_package_basic'])) : '';
			$service_package_standard       = isset($_REQUEST['service_package_standard']) ? felan_clean(wp_unslash($_REQUEST['service_package_standard'])) : '';
			$service_package_premium      = isset($_REQUEST['service_package_premium']) ? felan_clean(wp_unslash($_REQUEST['service_package_premium'])) : '';

			$service_custom_title       = isset($_REQUEST['service_custom_title']) ? felan_clean(wp_unslash($_REQUEST['service_custom_title'])) : '';
			$service_custom_basic      = isset($_REQUEST['service_custom_basic']) ? felan_clean(wp_unslash($_REQUEST['service_custom_basic'])) : '';
			$service_custom_standard      = isset($_REQUEST['service_custom_standard']) ? felan_clean(wp_unslash($_REQUEST['service_custom_standard'])) : '';
			$service_custom_premium      = isset($_REQUEST['service_custom_premium']) ? felan_clean(wp_unslash($_REQUEST['service_custom_premium'])) : '';

			$service_addons_title       = isset($_REQUEST['service_addons_title']) ? felan_clean(wp_unslash($_REQUEST['service_addons_title'])) : '';
			$service_addons_price        = isset($_REQUEST['service_addons_price']) ? felan_clean(wp_unslash($_REQUEST['service_addons_price'])) : '';
			$service_addons_time      = isset($_REQUEST['service_addons_time']) ? felan_clean(wp_unslash($_REQUEST['service_addons_time'])) : '';

			$service_faq_title      = isset($_REQUEST['service_faq_title']) ? felan_clean(wp_unslash($_REQUEST['service_faq_title'])) : '';
			$service_faq_description       = isset($_REQUEST['service_faq_description']) ? felan_clean(wp_unslash($_REQUEST['service_faq_description'])) : '';

			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;

			$new_service = array();
			$new_service['post_type'] = 'service';
			$new_service['post_author'] = $user_id;

			if (isset($service_title)) {
				$new_service['post_title'] = $service_title;
			}

			if (isset($service_url)) {
				$new_service['post_name'] = $service_url;
			}

			if (isset($service_des)) {
				$new_service['post_content'] = $service_des;
			}

			$submit_action = $service_form;
			$auto_publish         = felan_get_option('service_auto_publish', 1);
			$auto_publish_edited  = felan_get_option('service_auto_publish_edited', 1);
			$paid_submission_type = felan_get_option('freelancer_paid_submission_type', 'no');

			if ($submit_action == 'submit-service') {
				$service_id = 0;
				if ($auto_publish == 1) {
					$new_service['post_status'] = 'publish';
				} else {
					$new_service['post_status'] = 'pending';
				}
				if (!empty($new_service['post_title'])) {
					$service_id = wp_insert_post($new_service, true);
				}
				if ($service_id > 0) {
					if ($paid_submission_type == 'freelancer_per_package') {
						$freelancer_package_key = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_key', $user_id);
						update_post_meta($service_id, FELAN_METABOX_PREFIX . 'freelancer_package_key', $freelancer_package_key);
						$freelancer_package_number_service = intval(get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_number_service', $user_id));
						if ($freelancer_package_number_service - 1 >= 0) {
							update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service', $freelancer_package_number_service - 1);
						}
					}
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'enable_freelancer_package_expires', 0);
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', 0);
					update_post_meta($service_id, 'total_point_review', 0);

					felan_withdraw_total_price_ajax('posting_service', $user_id, 'freelancer');
				}
				echo json_encode(array('success' => true));
			} elseif ($submit_action == 'edit-service') {
				$service_id        = absint(wp_unslash($service_id));
				$new_service['ID'] = intval($service_id);
				if ($auto_publish_edited == 1) {
					$new_service['post_status'] = 'publish';
				} else {
					$new_service['post_status'] = 'pending';
				}
				if ($paid_submission_type == 'freelancer_per_package') {
					$felan_freelancer_package = new Felan_freelancer_package();
					$check_freelancer_package = $felan_freelancer_package->user_freelancer_package_available($user_id);
					if (($check_freelancer_package == -1) || ($check_freelancer_package == 0)) {
						return -1;
					}
				}

				$service_id = wp_update_post($new_service);
				echo json_encode(array('success' => true));
			}

			if ($service_id > 0) {
				//Category
				if (!empty($service_categories)) {
					$service_categories = array_map('intval', $service_categories);
					wp_set_object_terms($service_id, $service_categories, 'service-categories');
				}

				if (!empty($service_skills)) {
					$service_skills = array_map('intval', $service_skills);
					wp_set_object_terms($service_id, $service_skills, 'service-skills');
				}

				if (!empty($service_language)) {
					$service_language = array_map('intval', $service_language);
					wp_set_object_terms($service_id, $service_language, 'service-language');
				}

				if (!empty($service_location)) {
					$service_location = intval($service_location);
					wp_set_object_terms($service_id, $service_location, 'felan_location');
				}

				//Field

				if (isset($service_price)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_price', $service_price);
				}

				if (isset($service_video_url)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_video_url', $service_video_url);
				}

				if (isset($service_map_address)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_address', $service_map_address);
				}

				if (isset($service_time)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_time', $service_time);
				}

				if (isset($service_quantity)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_quantity', $service_quantity);
				}

				if (isset($service_basic_des)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_des', $service_basic_des);
				}

				if (isset($service_standard_des)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_des', $service_standard_des);
				}

				if (isset($service_premium_des)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_des', $service_premium_des);
				}

				if (isset($service_basic_price)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_price', $service_basic_price);
				}

				if (isset($service_standard_price)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_price', $service_standard_price);
				}

				if (isset($service_premium_price)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_price', $service_premium_price);
				}

				if (isset($service_basic_time)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_time', $service_basic_time);
				}

				if (isset($service_standard_time)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_time', $service_standard_time);
				}

				if (isset($service_premium_time)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_time', $service_premium_time);
				}

				if (isset($service_basic_revisions)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_revisions', $service_basic_revisions);
				}

				if (isset($service_standard_revisions)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_revisions', $service_standard_revisions);
				}

				if (isset($service_premium_revisions)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_revisions', $service_premium_revisions);
				}

				if (isset($service_basic_number_revisions)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_basic_number_revisions', $service_basic_number_revisions);
				}

				if (isset($service_standard_number_revisions)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_standard_number_revisions', $service_standard_number_revisions);
				}

				if (isset($service_premium_number_revisions)) {
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_premium_number_revisions', $service_premium_number_revisions);
				}

				if (isset($service_map_location)) {
					$lat_lng = $service_map_location;
					$address = $service_map_address;
					$arr_location = array(
						'location' => $lat_lng,
						'address' => $address,
					);
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_location', $arr_location);
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_address', $service_map_address);
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_latitude', $service_latitude);
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_longtitude', $service_longtitude);
				}

				if (!empty($service_package_title)) {
					foreach ($service_package_title as $key => $value) {
						$service_package_list_key = FELAN_METABOX_PREFIX . 'service_package_list' . $key;
						$service_package_title_key = FELAN_METABOX_PREFIX . 'service_package_title' . $key;

						$basic  = $service_package_basic[$key];
						$standard = $service_package_standard[$key];
						$premium = $service_package_premium[$key];
						$default_value = [$basic, $standard, $premium];

						update_post_meta($service_id, $service_package_title_key, $service_package_title[$key]);
						update_post_meta($service_id, $service_package_list_key, $default_value);
					}
				}

				if (!empty($service_custom_title)) {
					$service_custom_data  = array();
					for ($i = 0; $i < count($service_custom_title); $i++) {
						$new_list = [$service_custom_basic[$i], $service_custom_standard[$i], $service_custom_premium[$i]];
						$service_custom_data[] = array(
							FELAN_METABOX_PREFIX . 'service_package_new_title'   => $service_custom_title[$i],
							FELAN_METABOX_PREFIX . 'service_package_new_list'    => $new_list,
						);
					}
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_package_new', $service_custom_data);
				}

				if (!empty($service_addons_title)) {
					$addons_data  = array();
					for ($i = 0; $i < count($service_addons_title); $i++) {
						$addons_data[] = array(
							FELAN_METABOX_PREFIX . 'service_addons_title'   => $service_addons_title[$i],
							FELAN_METABOX_PREFIX . 'service_addons_price'    => $service_addons_price[$i],
							FELAN_METABOX_PREFIX . 'service_addons_time'    => $service_addons_time[$i],
						);
					}
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_tab_addon', $addons_data);
				}

				if (!empty($service_faq_title)) {
					$faq_data  = array();
					for ($i = 0; $i < count($service_faq_title); $i++) {
						$faq_data[] = array(
							FELAN_METABOX_PREFIX . 'service_faq_title'   => $service_faq_title[$i],
							FELAN_METABOX_PREFIX . 'service_faq_description'    => $service_faq_description[$i],
						);
					}
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_tab_faq', $faq_data);
				}

				if (isset($service_thumbnail_url) && isset($service_thumbnail_id)) {
					$service_thumbnail = array(
						'id'  => $service_thumbnail_id,
						'url' => $service_thumbnail_url,
					);
					update_post_meta($service_id, '_thumbnail_id', $service_thumbnail_id);
				}

				if (isset($felan_gallery_ids)) {
					$str_img_ids = '';
					foreach ($felan_gallery_ids as $service_img_id) {
						$felan_gallery_ids[] = intval($service_img_id);
						$str_img_ids .= '|' . intval($service_img_id);
					}
					$str_img_ids = substr($str_img_ids, 1);
					update_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_images', $str_img_ids);
				}

				do_action('felan_service_after_save', $service_id);
			}

			wp_die();
		}
	}
}
