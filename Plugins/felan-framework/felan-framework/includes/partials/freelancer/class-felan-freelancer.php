<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Felan_Freelancer')) {
    /**
     * Class Felan_Freelancer
     */
    class Felan_Freelancer
    {

        /**
         * Jobs breadcrumb
         */
        public function felan_freelancer_breadcrumb()
        { ?>
            <div class="container container-breadcrumb">
                <?php get_template_part('templates/global/breadcrumb'); ?>
            </div>
<?php }

        public function felan_set_freelancer_view_date()
        {
            $id = get_the_ID();
            $today = date('Y-m-d', time());
            $views_date = get_post_meta($id, 'felan_view_freelancer_date', true);
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
            update_post_meta($id, 'felan_view_freelancer_date', $views_date);
        }

        /**
         * upload freelancer img
         */
        public function upload_freelancer_attachment_ajax()
        {

            $nonce = isset($_REQUEST['nonce']) ? felan_clean(wp_unslash($_REQUEST['nonce'])) : '';
            if (!wp_verify_nonce($nonce, 'freelancer_allow_upload')) {
                $ajax_response = array('success' => false, 'reason' => esc_html__('Security check failed!', 'felan-framework'));
                echo json_encode($ajax_response);
                wp_die();
            }

            $submitted_file = $_FILES['freelancer_upload_file']; // WPCS: sanitization ok, input var ok.

            $uploaded_image = wp_handle_upload($submitted_file, array('test_form' => false));
            if (isset($uploaded_image['file'])) {
                $file_name = basename($submitted_file['name']);
                $file_type = wp_check_filetype($uploaded_image['file']);
                $attachment_details = array(
                    'guid'           => $uploaded_image['url'],
                    'post_mime_type' => $file_type['type'],
                    'post_title'     => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                $attach_id     = wp_insert_attachment($attachment_details, $uploaded_image['file']);
                $attach_data   = wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                $thumbnail_url = wp_get_attachment_thumb_url($attach_id);
                $fullimage_url = wp_get_attachment_image_src($attach_id, 'full');

                $ajax_response = array(
                    'success'         => true,
                    'url'             => $thumbnail_url,
                    'attachment_id'   => $attach_id,
                    'attachment_name' => $file_name,
                    'full_image'      => $fullimage_url[0]
                );
                echo json_encode($ajax_response);
                wp_die();
            } else {
                $ajax_response = array('success' => false, 'reason' => esc_html__('Image upload failed!', 'felan-framework'));
                echo json_encode($ajax_response);
                wp_die();
            }
        }

        /**
         * Remove freelancer img
         */
        public function remove_freelancer_attachment_ajax()
        {
            $nonce   = isset($_POST['removeNonce']) ? felan_clean(wp_unslash($_POST['removeNonce'])) : '';
            $user_id = isset($_POST['user_id']) ? felan_clean(wp_unslash($_POST['user_id'])) : '';
            if (!wp_verify_nonce($nonce, 'freelancer_allow_upload')) {
                $json_response = array(
                    'success' => false,
                    'reason'  => esc_html__('Security check fails', 'felan-framework')
                );
                echo json_encode($json_response);
                wp_die();
            }

            $success = false;
            if (isset($_POST['freelancer_id']) && isset($_POST['attachment_id'])) {
                $freelancer_id  = absint(wp_unslash($_POST['freelancer_id']));
                $attachment_id = absint(wp_unslash($_POST['attachment_id']));
                $type          = isset($_POST['type']) ? felan_clean(wp_unslash($_POST['type'])) : '';

                if ($freelancer_id > 0) {
                    if ($type === 'gallery') {
                        $freelancer_gallery = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_gallery', true);

                        $found_img_key = array_search($attachment_id, $freelancer_gallery);

                        if ($found_img_key !== false) {
                            unset($freelancer_gallery[$found_img_key]);
                            update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_gallery', $freelancer_gallery);
                        }
                    } else {
                        delete_post_meta($freelancer_id, FELAN_METABOX_PREFIX . '_thumbnail_id', $attachment_id);
                    }

                    $success = true;
                }

                if ($attachment_id > 0) {
                    wp_delete_attachment($attachment_id);
                    $success = true;
                }
            }
            if ($user_id) {
                update_user_meta($user_id, 'author_avatar_image_url', FELAN_THEME_URI . '/assets/images/default-user-image.png');
            }
            $ajax_response = array(
                'success' => $success,
                'url'     => get_the_author_meta('author_avatar_image_url', $user_id),
            );

            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * Freelancer submit
         */
        public function freelancer_submit_ajax()
        {
            $freelancer_id                     = isset($_REQUEST['freelancer_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_id'])) : '';

            $freelancer_first_name             = isset($_REQUEST['freelancer_first_name']) ? felan_clean(wp_unslash($_REQUEST['freelancer_first_name'])) : '';
            $freelancer_last_name              = isset($_REQUEST['freelancer_last_name']) ? felan_clean(wp_unslash($_REQUEST['freelancer_last_name'])) : '';
            $freelancer_email                  = isset($_REQUEST['freelancer_email']) ? felan_clean(wp_unslash($_REQUEST['freelancer_email'])) : '';
            $freelancer_phone                  = isset($_REQUEST['freelancer_phone']) ? felan_clean(wp_unslash($_REQUEST['freelancer_phone'])) : '';
            $freelancer_phone_code             = isset($_REQUEST['freelancer_phone_code']) ? felan_clean(wp_unslash($_REQUEST['freelancer_phone_code'])) : '';
            $freelancer_current_position       = isset($_REQUEST['freelancer_current_position']) ? felan_clean(wp_unslash($_REQUEST['freelancer_current_position'])) : '';
            $freelancer_categories             = isset($_REQUEST['freelancer_categories']) ? (wp_unslash($_REQUEST['freelancer_categories'])) : '';
            $freelancer_des                    = isset($_REQUEST['freelancer_des']) ? wp_kses_post(wp_unslash($_REQUEST['freelancer_des'])) : '';
            $freelancer_dob                    = isset($_REQUEST['freelancer_dob']) ? felan_clean(wp_unslash($_REQUEST['freelancer_dob'])) : '';
            $freelancer_age                    = isset($_REQUEST['freelancer_age']) ? felan_clean(wp_unslash($_REQUEST['freelancer_age'])) : '';
            $freelancer_gender                 = isset($_REQUEST['freelancer_gender']) ? felan_clean(wp_unslash($_REQUEST['freelancer_gender'])) : '';
            $freelancer_languages              = isset($_REQUEST['freelancer_languages']) ? felan_clean(wp_unslash($_REQUEST['freelancer_languages'])) : '';
            $freelancer_qualification          = isset($_REQUEST['freelancer_qualification']) ? felan_clean(wp_unslash($_REQUEST['freelancer_qualification'])) : '';
            $freelancer_yoe                    = isset($_REQUEST['freelancer_yoe']) ? felan_clean(wp_unslash($_REQUEST['freelancer_yoe'])) : '';
            $freelancer_offer_salary           = isset($_REQUEST['freelancer_offer_salary']) ? felan_clean(wp_unslash($_REQUEST['freelancer_offer_salary'])) : '';
            $freelancer_salary_type            = isset($_REQUEST['freelancer_salary_type']) ? felan_clean(wp_unslash($_REQUEST['freelancer_salary_type'])) : '';
            $freelancer_currency_type          = isset($_REQUEST['freelancer_currency_type']) ? felan_clean(wp_unslash($_REQUEST['freelancer_currency_type'])) : '';

            $freelancer_education_title        = isset($_REQUEST['freelancer_education_title']) ? felan_clean(wp_unslash($_REQUEST['freelancer_education_title'])) : array();
            $freelancer_education_level        = isset($_REQUEST['freelancer_education_level']) ? felan_clean(wp_unslash($_REQUEST['freelancer_education_level'])) : array();
            $freelancer_education_from         = isset($_REQUEST['freelancer_education_from']) ? felan_clean(wp_unslash($_REQUEST['freelancer_education_from'])) : array();
            $freelancer_education_to           = isset($_REQUEST['freelancer_education_to']) ? felan_clean(wp_unslash($_REQUEST['freelancer_education_to'])) : array();
            $freelancer_education_description  = isset($_REQUEST['freelancer_education_description']) ? felan_clean(wp_unslash($_REQUEST['freelancer_education_description'])) : array();

            $freelancer_experience_job         = isset($_REQUEST['freelancer_experience_job']) ? felan_clean(wp_unslash($_REQUEST['freelancer_experience_job'])) : array();
            $freelancer_experience_company     = isset($_REQUEST['freelancer_experience_company']) ? felan_clean(wp_unslash($_REQUEST['freelancer_experience_company'])) : array();
            $freelancer_experience_from        = isset($_REQUEST['freelancer_experience_from']) ? felan_clean(wp_unslash($_REQUEST['freelancer_experience_from'])) : array();
            $freelancer_experience_to          = isset($_REQUEST['freelancer_experience_to']) ? felan_clean(wp_unslash($_REQUEST['freelancer_experience_to'])) : array();
            $freelancer_experience_description = isset($_REQUEST['freelancer_experience_description']) ? felan_clean(wp_unslash($_REQUEST['freelancer_experience_description'])) : array();

            $freelancer_skills                 = isset($_REQUEST['freelancer_skills']) ? felan_clean(wp_unslash($_REQUEST['freelancer_skills'])) : array();

            $freelancer_project_title          = isset($_REQUEST['freelancer_project_title']) ? felan_clean(wp_unslash($_REQUEST['freelancer_project_title'])) : array();
            $freelancer_project_link           = isset($_REQUEST['freelancer_project_link']) ? felan_clean(wp_unslash($_REQUEST['freelancer_project_link'])) : array();
            $freelancer_project_description    = isset($_REQUEST['freelancer_project_description']) ? felan_clean(wp_unslash($_REQUEST['freelancer_project_description'])) : array();
            $freelancer_project_image_id       = isset($_REQUEST['freelancer_project_image_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_project_image_id'])) : array();
            $freelancer_project_image_url      = isset($_REQUEST['freelancer_project_image_url']) ? felan_clean(wp_unslash($_REQUEST['freelancer_project_image_url'])) : array();

            $freelancer_award_title            = isset($_REQUEST['freelancer_award_title']) ? felan_clean(wp_unslash($_REQUEST['freelancer_award_title'])) : array();
            $freelancer_award_date             = isset($_REQUEST['freelancer_award_date']) ? felan_clean(wp_unslash($_REQUEST['freelancer_award_date'])) : array();
            $freelancer_award_description      = isset($_REQUEST['freelancer_award_description']) ? felan_clean(wp_unslash($_REQUEST['freelancer_award_description'])) : array();

            $freelancer_cover_image_id         = isset($_REQUEST['freelancer_cover_image_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_cover_image_id'])) : '';
            $freelancer_cover_image_url        = isset($_REQUEST['freelancer_cover_image_url']) ? felan_clean(wp_unslash($_REQUEST['freelancer_cover_image_url'])) : '';
            $author_avatar_image_id           = isset($_REQUEST['author_avatar_image_id']) ? felan_clean(wp_unslash($_REQUEST['author_avatar_image_id'])) : '';
            $author_avatar_image_url           = isset($_REQUEST['author_avatar_image_url']) ? felan_clean(wp_unslash($_REQUEST['author_avatar_image_url'])) : '';

            $freelancer_resume           = isset($_REQUEST['freelancer_resume']) ? felan_clean(wp_unslash($_REQUEST['freelancer_resume'])) : '';

            $freelancer_twitter = isset($_REQUEST['freelancer_twitter']) ? felan_clean(wp_unslash($_REQUEST['freelancer_twitter'])) : '';
            $freelancer_linkedin = isset($_REQUEST['freelancer_linkedin']) ? felan_clean(wp_unslash($_REQUEST['freelancer_linkedin'])) : '';
            $freelancer_facebook = isset($_REQUEST['freelancer_facebook']) ? felan_clean(wp_unslash($_REQUEST['freelancer_facebook'])) : '';
            $freelancer_instagram = isset($_REQUEST['freelancer_instagram']) ? felan_clean(wp_unslash($_REQUEST['freelancer_instagram'])) : '';
            $freelancer_social_name = isset($_REQUEST['freelancer_social_name']) ? felan_clean(wp_unslash($_REQUEST['freelancer_social_name'])) : '';
            $freelancer_social_url = isset($_REQUEST['freelancer_social_url']) ? felan_clean(wp_unslash($_REQUEST['freelancer_social_url'])) : '';
            $freelancer_social_data  = isset($_REQUEST['freelancer_social_data']) ? felan_clean(wp_unslash($_REQUEST['freelancer_social_data'])) : '';

            $freelancer_map_location       = isset($_REQUEST['freelancer_map_location']) ? felan_clean(wp_unslash($_REQUEST['freelancer_map_location'])) : '';
            $freelancer_map_address        = isset($_REQUEST['freelancer_map_address']) ? felan_clean(wp_unslash($_REQUEST['freelancer_map_address'])) : '';
            $freelancer_latitude        = isset($_REQUEST['freelancer_latitude']) ? felan_clean(wp_unslash($_REQUEST['freelancer_latitude'])) : '';
            $freelancer_longtitude        = isset($_REQUEST['freelancer_longtitude']) ? felan_clean(wp_unslash($_REQUEST['freelancer_longtitude'])) : '';
            $freelancer_location       = isset($_REQUEST['freelancer_location']) ? felan_clean(wp_unslash($_REQUEST['freelancer_location'])) : '';

            $felan_gallery_ids             = isset($_REQUEST['felan_gallery_ids']) ? felan_clean(wp_unslash($_REQUEST['felan_gallery_ids'])) : array();
            $freelancer_video_url              = isset($_REQUEST['freelancer_video_url']) ? felan_clean(wp_unslash($_REQUEST['freelancer_video_url'])) : '';
            $freelancer_profile_strength         = isset($_REQUEST['freelancer_profile_strength']) ? felan_clean(wp_unslash($_REQUEST['freelancer_profile_strength'])) : '';
            $custom_field_freelancer        = isset($_REQUEST['custom_field_freelancer']) ? felan_clean(wp_unslash($_REQUEST['custom_field_freelancer'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $archive_freelancer_stautus = felan_get_option('archive_freelancer_stautus') ? felan_get_option('archive_freelancer_stautus') : 'pending';
            $new_freelancer = array();
            $new_freelancer['post_type'] = 'freelancer';
            $new_freelancer['post_author'] = $user_id;

            if (isset($freelancer_des)) {
                $new_freelancer['post_content'] = $freelancer_des;
            }

            $freelancer_id        = absint(wp_unslash($freelancer_id));
            $new_freelancer['ID'] = intval($freelancer_id);

            $new_freelancer['post_status'] = $archive_freelancer_stautus;

            $freelancer_id = wp_update_post($new_freelancer);

            echo json_encode(array('success' => true));

            if ($freelancer_id > 0) {


                if (isset($freelancer_first_name)) {
                    update_user_meta($user_id, 'first_name', $freelancer_first_name);
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_first_name', $freelancer_first_name);
                }

                if (isset($freelancer_last_name)) {
                    update_user_meta($user_id, 'last_name', $freelancer_last_name);
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_last_name', $freelancer_last_name);
                }

                if (isset($freelancer_first_name) && isset($freelancer_last_name)) {
                    $type_name_freelancer = felan_get_option('type_name_freelancer');
                    if ($type_name_freelancer === 'fl-name') {
                        $full_name = $freelancer_first_name . ' ' . $freelancer_last_name;
                        $userdata = array(
                            'ID' => $user_id,
                            'display_name' => $full_name,
                        );
                        wp_update_user($userdata);

                        $data = array(
                            'ID' => $freelancer_id,
                            'post_type' => 'freelancer',
                            'post_title'   => $full_name,
                        );
                        wp_update_post($data);
                    }
                }

                if (isset($freelancer_email)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_email', $freelancer_email);
                }

                if (isset($freelancer_phone)) {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_mobile_number', $freelancer_phone);
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_phone', $freelancer_phone);
                }

                if (isset($freelancer_phone_code)) {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'phone_code', $freelancer_phone_code);
                }

                if (isset($freelancer_current_position)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_current_position', $freelancer_current_position);
                }

                if (isset($freelancer_dob)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_dob', $freelancer_dob);
                }

                if (isset($freelancer_offer_salary)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_offer_salary', $freelancer_offer_salary);
                }

                if (isset($freelancer_salary_type)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_salary_type', $freelancer_salary_type);
                }

                if (isset($freelancer_currency_type)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_currency_type', $freelancer_currency_type);
                }

                if (isset($freelancer_resume)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_resume_id_list', $freelancer_resume);
                }

                if (isset($freelancer_twitter)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_twitter', $freelancer_twitter);
                }

                if (isset($freelancer_linkedin)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_linkedin', $freelancer_linkedin);
                }

                if (isset($freelancer_facebook)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_facebook', $freelancer_facebook);
                }

                if (isset($freelancer_instagram)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_instagram', $freelancer_instagram);
                }

                if (isset($freelancer_video_url)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_video_url', $freelancer_video_url);
                }

                if (isset($freelancer_profile_strength)) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_profile_strength', $freelancer_profile_strength);
                }

                if ($freelancer_profile_strength == 100) {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_featured', 1);
                } else {
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_featured', 0);
                }

                //Taxnonomy
                if (isset($freelancer_categories)) {
                    $freelancer_categories = intval($freelancer_categories);
                    wp_set_object_terms($freelancer_id, $freelancer_categories, 'freelancer_categories');
                }

                if (isset($freelancer_age)) {
                    $freelancer_age = intval($freelancer_age);
                    wp_set_object_terms($freelancer_id, $freelancer_age, 'freelancer_ages');
                }

                if (!empty($freelancer_languages)) {
                    if (felan_get_option('enable_freelancer_language_multiple') === '1') {
                        $freelancer_languages = array_map('intval', $freelancer_languages);
                        wp_set_object_terms($freelancer_id, $freelancer_languages, 'freelancer_languages');
                    } else {
                        $freelancer_languages = intval($freelancer_languages);
                        wp_set_object_terms($freelancer_id, $freelancer_languages, 'freelancer_languages');
                    }
                }

                if (isset($freelancer_qualification)) {
                    $freelancer_qualification = intval($freelancer_qualification);
                    wp_set_object_terms($freelancer_id, $freelancer_qualification, 'freelancer_qualification');
                }

                if (isset($freelancer_yoe)) {
                    $freelancer_yoe = intval($freelancer_yoe);
                    wp_set_object_terms($freelancer_id, $freelancer_yoe, 'freelancer_yoe');
                }

                if (isset($freelancer_gender)) {
                    $freelancer_gender = intval($freelancer_gender);
                    wp_set_object_terms($freelancer_id, $freelancer_gender, 'freelancer_gender');
                }

                if (isset($freelancer_location)) {
                    $freelancer_location = intval($freelancer_location);
                    wp_set_object_terms($freelancer_id, $freelancer_location, 'felan_location');
                }

                if (isset($author_avatar_image_id) && isset($author_avatar_image_url)) {
                    update_user_meta($user_id, 'author_avatar_image_id', $author_avatar_image_id);
                    update_user_meta($user_id, 'author_avatar_image_url', $author_avatar_image_url);
                } else {
                    delete_user_meta($user_id, 'author_avatar_image_id');
                    delete_user_meta($user_id, 'author_avatar_image_url');
                }

                if (isset($felan_gallery_ids)) {
                    $str_img_ids = '';
                    foreach ($felan_gallery_ids as $gallery_id) {
                        $felan_gallery_ids[] = intval($gallery_id);
                        $str_img_ids .= '|' . intval($gallery_id);
                    }
                    $str_img_ids = substr($str_img_ids, 1);
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_galleries', $str_img_ids);
                }

                if (isset($freelancer_map_location)) {
                    $lat_lng = $freelancer_map_location;
                    $address = $freelancer_map_address;
                    $arr_location = array(
                        'location' => $lat_lng,
                        'address' => $address,
                    );
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_location', $arr_location);
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_address', $freelancer_map_address);
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_latitude', $freelancer_latitude);
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_longtitude', $freelancer_longtitude);
                }

                if (!empty($freelancer_social_name)) {
                    $social_data  = array();
                    for ($i = 1; $i < count($freelancer_social_name); $i++) {
                        $social_data[] = array(
                            FELAN_METABOX_PREFIX . 'freelancer_social_name'   => $freelancer_social_name[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_social_url'    => $freelancer_social_url[$i],
                        );
                    }
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_social_tabs', $social_data);
                }

                if (!empty($freelancer_social_data)) {
                    foreach ($freelancer_social_data as $key => $value) {
                        update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . $key, $value);
                    }
                }

                if (isset($freelancer_cover_image_id) && !empty($freelancer_cover_image_url)) {
                    update_post_meta($freelancer_id, '_thumbnail_id', $freelancer_cover_image_id);
                } else {
                    delete_post_meta($freelancer_id, FELAN_METABOX_PREFIX . '_thumbnail_id', $freelancer_cover_image_id);
                }

                if (isset($freelancer_education_title)) {
                    $education_data = array();
                    for ($i = 0; $i < count($freelancer_education_title); $i++) {
                        $education_data[] = array(
                            FELAN_METABOX_PREFIX . 'freelancer_education_title'       => $freelancer_education_title[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_education_level'       => $freelancer_education_level[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_education_from'        => $freelancer_education_from[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_education_to'          => $freelancer_education_to[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_education_description' => $freelancer_education_description[$i],
                        );
                    }
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_education_list', $education_data);
                }

                if (isset($freelancer_experience_job)) {

                    $experience_data = array();
                    for ($i = 0; $i < count($freelancer_experience_job); $i++) {
                        $experience_data[] = array(
                            FELAN_METABOX_PREFIX . 'freelancer_experience_job'         => $freelancer_experience_job[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_experience_company'     => $freelancer_experience_company[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_experience_from'        => $freelancer_experience_from[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_experience_to'          => $freelancer_experience_to[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_experience_description' => $freelancer_experience_description[$i]
                        );
                    }

                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_experience_list', $experience_data);
                }

                if (isset($freelancer_skills)) {
                    $freelancer_skills = array_map('intval', $freelancer_skills);
                    wp_set_object_terms($freelancer_id, $freelancer_skills, 'freelancer_skills');
                }

                if (isset($freelancer_project_title)) {

                    $project_data = array();
                    for ($i = 0; $i < count($freelancer_project_title); $i++) {
                        $freelancer_project_image = array(
                            'id'  => $freelancer_project_image_id[$i],
                            'url'  => $freelancer_project_image_url[$i],
                        );
                        $project_data[] = array(
                            FELAN_METABOX_PREFIX . 'freelancer_project_title'       => $freelancer_project_title[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_project_link'        => $freelancer_project_link[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_project_description' => $freelancer_project_description[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_project_image_id'    =>  $freelancer_project_image,
                        );
                    }

                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_project_list', $project_data);
                }

                if (isset($freelancer_award_title)) {

                    $award_data = array();
                    for ($i = 0; $i < count($freelancer_award_title); $i++) {
                        $award_data[] = array(
                            FELAN_METABOX_PREFIX . 'freelancer_award_title'       => $freelancer_award_title[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_award_date'        => $freelancer_award_date[$i],
                            FELAN_METABOX_PREFIX . 'freelancer_award_description' => $freelancer_award_description[$i],
                        );
                    }

                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_award_list', $award_data);
                }

                $get_additional = felan_render_custom_field('freelancer');
                if (count($get_additional) > 0 && !empty($custom_field_freelancer)) {
                    foreach ($get_additional as $key => $field) {
                        if (count($custom_field_freelancer) > 0 && isset($custom_field_freelancer[$field['id']])) {
                            if ($field['type'] == 'checkbox_list') {
                                $arr = array();
                                foreach ($custom_field_freelancer[$field['id']] as $v) {
                                    $arr[] = $v;
                                }
                                update_post_meta($freelancer_id, $field['id'], $arr);
                            } elseif ($field['type'] == 'image') {
                                $custom_field_freelancer_url = wp_get_attachment_url($custom_field_freelancer[$field['id']]);
                                $custom_image = array(
                                    'id'  => $custom_field_freelancer[$field['id']],
                                    'url'  => $custom_field_freelancer_url,
                                );
                                update_post_meta($freelancer_id, $field['id'], $custom_image);
                            } else {
                                update_post_meta($freelancer_id, $field['id'], $custom_field_freelancer[$field['id']]);
                            }
                        }
                    }
                }
            }
            wp_die();
        }

		/**
         * Freelancer verify
         */
        public function user_verify_ajax()
        {
            $user_id          = isset($_REQUEST['user_id']) ? felan_clean(wp_unslash($_REQUEST['user_id'])) : '';
            $front_image_id   = isset($_REQUEST['front_image_id']) ? felan_clean(wp_unslash($_REQUEST['front_image_id'])) : '';
            $front_image_url  = isset($_REQUEST['front_image_url']) ? felan_clean(wp_unslash($_REQUEST['front_image_url'])) : '';

			$back_image_id   = isset($_REQUEST['back_image_id']) ? felan_clean(wp_unslash($_REQUEST['back_image_id'])) : '';
            $back_image_url  = isset($_REQUEST['back_image_url']) ? felan_clean(wp_unslash($_REQUEST['back_image_url'])) : '';

            $verify_type  = isset($_REQUEST['verify_type']) ? felan_clean(wp_unslash($_REQUEST['verify_type'])) : '';

            // global $current_user;
            // wp_get_current_user();
            // $user_id = $current_user->ID;

            // $archive_freelancer_stautus = felan_get_option('archive_freelancer_stautus') ? felan_get_option('archive_freelancer_stautus') : 'pending';
            // $new_freelancer = array();
            // $new_freelancer['post_type'] = 'freelancer';
            // $new_freelancer['post_author'] = $user_id;

            // if (isset($freelancer_des)) {
            //     $new_freelancer['post_content'] = $freelancer_des;
            // }

            $user_id = absint(wp_unslash($user_id));
            // $new_freelancer['ID'] = intval($freelancer_id);

            // $new_freelancer['post_status'] = $archive_freelancer_stautus;

            // $freelancer_id = wp_update_post($new_freelancer);

            echo json_encode(array('success' => true));

            if ($user_id > 0) {

                if (isset($front_image_id) && isset($front_image_url)) {
                    update_user_meta($user_id, 'front_image_id', $front_image_id);
                    update_user_meta($user_id, 'front_image_url', $front_image_url);

					// update_post_meta($freelancer_id, 'front_image_id', $front_image_id);
                } else {
                    delete_user_meta($user_id, 'front_image_id');
                    delete_user_meta($user_id, 'front_image_url');

					// delete_post_meta($freelancer_id, 'front_image_id', $front_image_id);
                }

				if (isset($back_image_id) && !empty($back_image_url)) {
                    update_user_meta($user_id, 'back_image_id', $back_image_id);
                    update_user_meta($user_id, 'back_image_url', $back_image_url);

					// update_post_meta($freelancer_id, 'back_image_id', $back_image_id);
                } else {
                    delete_user_meta($user_id, 'back_image_id');
					delete_user_meta($user_id, 'back_image_url');

					// delete_post_meta($freelancer_id, 'back_image_id', $back_image_id);
                }

				if ( !empty($verify_type) ) {
                    update_user_meta($user_id, 'verify_type', $verify_type);
                } else {
                    delete_user_meta($user_id, 'verify_type');
                }

				update_user_meta( $user_id, 'date_verify_submit', strtotime( 'now' ) );

				$user_info = get_userdata($user_id);
				$email = $user_info->user_email;
				$admin_email = get_option('admin_email');
				felan_send_email($email, 'identity_mail_verify_user', []);
				felan_send_email($admin_email, 'admin_identity_mail_verify_user', []);

            }
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
            $order_id = isset($_POST['order_id']) ? felan_clean(wp_unslash($_POST['order_id'])) : '';
            $freelancer_id                   = isset($_POST['freelancer_id']) ? felan_clean(wp_unslash($_POST['freelancer_id'])) : '';
            $rating_working_value       = isset($_POST['rating_working']) ? felan_clean(wp_unslash($_POST['rating_working'])) : '';
            $rating_team_value         = isset($_POST['rating_team']) ? felan_clean(wp_unslash($_POST['rating_team'])) : '';
            $rating_skill_value      = isset($_POST['rating_skill']) ? felan_clean(wp_unslash($_POST['rating_skill'])) : '';
            $rating_salary_value   = isset($_POST['rating_salary']) ? felan_clean(wp_unslash($_POST['rating_salary'])) : '';
            $my_review    = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $freelancer_id AND comment.user_id = $user_id  AND meta.meta_key = 'freelancer_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");
            $comment_approved = 1;
            $auto_publish_review_freelancer = get_option('comment_moderation');
            if ($auto_publish_review_freelancer == 1) {
                $comment_approved = 0;
            }
            if ($my_review == null) {
                $data = array();
                $user = $user->data;

                $data['comment_post_ID']      = $freelancer_id;
                $data['comment_content']      = isset($_POST['message']) ?  wp_filter_post_kses($_POST['message']) : '';
                $data['comment_date']         = current_time('mysql');
                $data['comment_approved']     = $comment_approved;
                $data['comment_author']       = $user->user_login;
                $data['comment_author_email'] = $user->user_email;
                $data['comment_author_url']   = $user->user_url;
                $data['user_id']              = $user_id;

                $comment_id = wp_insert_comment($data);

                add_comment_meta($comment_id, 'freelancer_salary_rating', $rating_working_value);
                add_comment_meta($comment_id, 'freelancer_freelancer_rating', $rating_team_value);
                add_comment_meta($comment_id, 'freelancer_skill_rating', $rating_skill_value);
                add_comment_meta($comment_id, 'freelancer_work_rating', $rating_salary_value);

                $freelancer_rating = (intval($rating_working_value) + intval($rating_team_value) + intval($rating_skill_value) + intval($rating_salary_value)) / 4;
                $freelancer_rating = number_format((float)$freelancer_rating, 2, '.', '');

                add_comment_meta($comment_id, 'freelancer_rating', $freelancer_rating);

                if ($comment_approved == 1) {
                    apply_filters('felan_freelancer_rating_meta', $freelancer_id, $freelancer_rating);
                }
                felan_get_data_ajax_notification($freelancer_id, 'add-review-freelancer');
            } else {
                $data = array();

                $data['comment_ID']       = $my_review->comment_ID;
                $data['comment_post_ID']  = $freelancer_id;
                $data['comment_content']  = isset($_POST['message']) ? wp_filter_post_kses($_POST['message']) : '';
                $data['comment_date']     = current_time('mysql');
                $data['comment_approved'] = $comment_approved;

                wp_update_comment($data);
                update_comment_meta($my_review->comment_ID, 'freelancer_salary_rating', $rating_working_value);
                update_comment_meta($my_review->comment_ID, 'freelancer_freelancer_rating', $rating_team_value);
                update_comment_meta($my_review->comment_ID, 'freelancer_skill_rating', $rating_skill_value);
                update_comment_meta($my_review->comment_ID, 'freelancer_work_rating', $rating_salary_value);

                $freelancer_rating = (intval($rating_working_value) + intval($rating_team_value) + intval($rating_skill_value) + intval($rating_salary_value)) / 4;
                $freelancer_rating = number_format((float)$freelancer_rating, 2, '.', '');

                update_comment_meta($my_review->comment_ID, 'freelancer_rating', $freelancer_rating, $my_review->meta_value);

                if ($comment_approved == 1) {
                    apply_filters('felan_freelancer_rating_meta', $freelancer_id, $freelancer_rating, false, $my_review->meta_value);
                }

                if (!empty($order_id)) {
                    update_post_meta($order_id, FELAN_METABOX_PREFIX . 'proposal_status', 'completed');
                    update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'has_freelancer_review', '1');
                }
            }

            echo json_encode(array('success' => true));

            wp_die();
        }

        /**
         * @param $freelancer_id
         * @param int $added_star The new rating, can be negative or positive
         * @param int $old_overall_rate
         * @param int $new_review_count
         */

        /**
         * @param $freelancer_id
         * @param $rating_value
         * @param bool|true $comment_exist
         * @param int $old_rating_value
         */
        public function rating_meta_filter($freelancer_id, $rating_value, $comment_exist = true, $old_rating_value = 0)
        {
            update_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_rating', $rating_value);
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
            $freelancer_id = isset($_POST['freelancer_id']) ? felan_clean(wp_unslash($_POST['freelancer_id'])) : '';
            $comment_approved = 1;
            $auto_publish_review_freelancer = get_option('comment_moderation');
            if ($auto_publish_review_freelancer == 1) {
                $comment_approved = 0;
            }
            $data = array();
            $user = $user->data;

            $data['comment_post_ID']      = $freelancer_id;
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
    }
}
