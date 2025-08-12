<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Felan_Project')) {
    /**
     * Class Felan_Project
     */
    class Felan_Project
    {

        public function felan_set_project_view_date()
        {
            $id = get_the_ID();
            $today = date('Y-m-d', time());
            $views_date = get_post_meta($id, 'felan_view_project', true);
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
            update_post_meta($id, 'felan_view_project', $views_date);
        }

        /**
         * Proposal Project
         */
        public function felan_send_proposal_project()
        {
            $proposal_price = isset($_REQUEST['proposal_price']) ? felan_clean(wp_unslash($_REQUEST['proposal_price'])) : '';
            $proposal_price_fee = isset($_REQUEST['proposal_price_fee']) ? felan_clean(wp_unslash($_REQUEST['proposal_price_fee'])) : '';
            $proposal_total_price = isset($_REQUEST['proposal_total_price']) ? felan_clean(wp_unslash($_REQUEST['proposal_total_price'])) : '';
            $proposal_total_hous = isset($_REQUEST['proposal_total_hous']) ? felan_clean(wp_unslash($_REQUEST['proposal_total_hous'])) : '';
            $proposal_estimated_hours = isset($_REQUEST['proposal_estimated_hours']) ? felan_clean(wp_unslash($_REQUEST['proposal_estimated_hours'])) : '';
            $proposal_time = isset($_REQUEST['proposal_time']) ? felan_clean(wp_unslash($_REQUEST['proposal_time'])) : '';
            $proposal_fixed_time = isset($_REQUEST['proposal_fixed_time']) ? felan_clean(wp_unslash($_REQUEST['proposal_fixed_time'])) : '';
            $proposal_rate = isset($_REQUEST['proposal_rate']) ? felan_clean(wp_unslash($_REQUEST['proposal_rate'])) : '';
            $proposal_maximum_time = isset($_REQUEST['proposal_maximum_time']) ? felan_clean(wp_unslash($_REQUEST['proposal_maximum_time'])) : '';
            $content_message = isset($_REQUEST['content_message']) ? felan_clean(wp_unslash($_REQUEST['content_message'])) : '';
            $creator_message = isset($_REQUEST['creator_message']) ? felan_clean(wp_unslash($_REQUEST['creator_message'])) : '';
            $recipient_message = isset($_REQUEST['recipient_message']) ? felan_clean(wp_unslash($_REQUEST['recipient_message'])) : '';
            $proposal_id = isset($_REQUEST['proposal_id']) ? felan_clean(wp_unslash($_REQUEST['proposal_id'])) : '';

            $projects_budget_show = get_post_meta($recipient_message, FELAN_METABOX_PREFIX . 'project_budget_show', true);
            $project_maximum_hours = get_post_meta($recipient_message, FELAN_METABOX_PREFIX . 'project_maximum_hours', true);
            $project_budget_minimum = get_post_meta($recipient_message, FELAN_METABOX_PREFIX . 'project_budget_minimum', true);
            $project_budget_maximum = get_post_meta($recipient_message, FELAN_METABOX_PREFIX . 'project_budget_maximum', true);
            $reply_message = get_post_field('post_author', $recipient_message);

            global $current_user;
            $user_id = $current_user->ID;
            if ($proposal_price == '' || $content_message == '') {
                echo json_encode(array('success' => false, 'message' => esc_html__('Please fill all form fields', 'felan-framework')));
                wp_die();
            }

            if (intval($proposal_price) < intval($project_budget_minimum) || intval($proposal_price) > intval($project_budget_maximum)) {
                echo json_encode(array('success' => false, 'message' => esc_html__("Please enter the price within the project range", 'felan-framework')));
                wp_die();
            }

            if (!empty($proposal_time) && $projects_budget_show == 'hourly' && intval($proposal_time) > intval($project_maximum_hours)) {
                echo json_encode(array('success' => false, 'message' => esc_html__("Please enter hours less than the project estimate", 'felan-framework')));
                wp_die();
            }

            $new_proposal = array(
                'post_type' => 'project-proposal',
                'post_status'    => 'publish',
            );

            if (isset($proposal_price)) {
                $new_proposal['post_title'] = get_the_title($recipient_message);
            }

            $author_employer_id   = get_post_field('post_author', $recipient_message);
            $user_employer        = get_user_by('id', $author_employer_id);
            $user_employer_email  = $user_employer->user_email;
            $user_employer_name   = $user_employer->display_name;
            $user_freelancer      = get_user_by('id', $creator_message);
            $user_freelancer_name = $user_freelancer->display_name;

            $felan_project_page_id = felan_get_option('felan_projects_page_id');
            $felan_project_page    = get_page_link($felan_project_page_id);

            if (empty($proposal_id)) {
                if (!empty($new_proposal['post_title'])) {
                    $proposal_id = wp_insert_post($new_proposal, true);
                }

                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'project_name'    => get_the_title($recipient_message),
                    'proposal_url'    => $felan_project_page . '?applicants_id=' . $proposal_id . '&project_id=' . $recipient_message,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_employer_email, 'mail_project_send_proposal', $args_mail);
                    felan_get_data_ajax_notification($recipient_message, 'add-proposal');
                }
            } else {

                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'project_name'    => get_the_title($recipient_message),
                    'proposal_url'    => $felan_project_page . '?applicants_id=' . $proposal_id . '&project_id=' . $recipient_message,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_employer_email, 'mail_project_update_proposal', $args_mail);
                    felan_get_data_ajax_notification($recipient_message, 'update-proposal');
                }

                $new_proposal['ID'] = $proposal_id;
                if (!empty($new_proposal['post_title'])) {
                    wp_update_post($new_proposal, true);
                }
            }

            if (isset($proposal_price)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_price', $proposal_price);
            }

            if (isset($proposal_price_fee)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_price_fee', $proposal_price_fee);
            }

            if (isset($proposal_total_price)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_total_price', $proposal_total_price);
            }

            if (isset($proposal_total_hous)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_total_hous', $proposal_total_hous);
            }

            if (isset($proposal_estimated_hours)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_estimated_hours', $proposal_estimated_hours);
            }

            if (!empty($proposal_time)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_time', $proposal_time);
            } else {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_time', 1);
            }

            if (!empty($proposal_fixed_time)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_fixed_time', $proposal_fixed_time);
            } else {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_fixed_time', 1);
            }

            if (!empty($proposal_rate)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_rate', $proposal_rate);
            }

            if (isset($proposal_maximum_time)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_maximum_time', $proposal_maximum_time);
            }

            if (isset($content_message)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_message', $content_message);
            }

            if (isset($proposal_id)) {
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_status', 'pending');
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_project_id', $recipient_message);
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'creator_message', $creator_message);
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'recipient_message', $recipient_message);
                update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'reply_message', $reply_message);
                update_post_meta($recipient_message, FELAN_METABOX_PREFIX . 'has_project_proposal_' . $user_id, 1);
            }

            felan_withdraw_total_price_ajax('project_proposals', $user_id, 'freelancer');

            felan_number_freelancer_package_ajax('project_apply');

            ob_start(); ?>

            <a href="#" class="btn-close"><i class="far fa-times"></i></a>
            <div class="form-thank-project">
                <p class="image-thank">
                    <svg width="74" height="74" viewBox="0 0 74 74" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M48.3077 66.0565C46.5594 66.05 44.8585 65.4871 43.4515 64.4493L38.4565 60.7956C37.8367 60.3392 37.0871 60.093 36.3174 60.093C35.5477 60.093 34.7982 60.3392 34.1784 60.7956L29.1834 64.4493C27.8917 65.3927 26.3538 65.9404 24.7566 66.0261C23.1594 66.1118 21.5717 65.7316 20.1865 64.9319C18.8013 64.1321 17.6783 62.9472 16.9539 61.5211C16.2296 60.0951 15.935 58.4893 16.1062 56.899L16.7768 50.7477C16.8629 49.9833 16.7016 49.2115 16.3166 48.5455C15.9316 47.8795 15.3432 47.3546 14.6377 47.0477L8.98368 44.5502C7.5167 43.9071 6.26885 42.8505 5.39264 41.5097C4.51642 40.1688 4.0498 38.6017 4.0498 36.9999C4.0498 35.3982 4.51642 33.8311 5.39264 32.4902C6.26885 31.1493 7.5167 30.0928 8.98368 29.4496L14.6377 26.9521C15.3432 26.6453 15.9316 26.1204 16.3166 25.4544C16.7016 24.7884 16.8629 24.0165 16.7768 23.2521L16.1062 17.1009C15.935 15.5106 16.2296 13.9048 16.9539 12.4787C17.6783 11.0527 18.8013 9.86777 20.1865 9.06801C21.5717 8.26825 23.1594 7.8881 24.7566 7.97377C26.3538 8.05943 27.8917 8.60723 29.1834 9.55056L34.1784 13.2043C34.7982 13.6607 35.5477 13.9069 36.3174 13.9069C37.0871 13.9069 37.8367 13.6607 38.4565 13.2043L43.4515 9.55056C44.341 8.8922 45.3561 8.42324 46.4339 8.17265C47.5118 7.92207 48.6297 7.8952 49.7184 8.09369C50.8305 8.29549 51.8904 8.72012 52.8342 9.34206C53.7781 9.964 54.5864 10.7704 55.2106 11.7128C55.3836 11.9662 55.5045 12.2516 55.5661 12.5523C55.6277 12.8529 55.6289 13.1628 55.5695 13.4639C55.5102 13.7651 55.3915 14.0513 55.2203 14.3061C55.0492 14.5609 54.8291 14.779 54.5728 14.9479C54.3165 15.1167 54.0291 15.2328 53.7275 15.2894C53.4258 15.3461 53.1159 15.3421 52.8159 15.2777C52.5158 15.2134 52.2315 15.09 51.9796 14.9146C51.7277 14.7393 51.5132 14.5156 51.3487 14.2565C51.0696 13.8371 50.7086 13.4786 50.2873 13.2024C49.8661 12.9262 49.3933 12.738 48.8974 12.6493C48.42 12.5613 47.9294 12.5745 47.4573 12.688C46.9853 12.8015 46.5423 13.0129 46.1571 13.3084L41.1621 16.9621C39.7484 17.9983 38.0413 18.5569 36.2885 18.5569C34.5358 18.5569 32.8286 17.9983 31.4149 16.9621L26.4315 13.3084C25.8651 12.8783 25.1847 12.6247 24.475 12.5791C23.7654 12.5335 23.058 12.698 22.4413 13.052C21.8246 13.406 21.3258 13.9338 21.0073 14.5696C20.6887 15.2054 20.5646 15.9209 20.6502 16.6268L21.3209 22.7665C21.5224 24.5038 21.1647 26.2601 20.2997 27.7801C19.4347 29.3001 18.1074 30.5047 16.5109 31.2187L10.8452 33.7162C10.2031 34.0012 9.65737 34.4665 9.27437 35.0555C8.89137 35.6445 8.68751 36.332 8.68751 37.0346C8.68751 37.7372 8.89137 38.4247 9.27437 39.0138C9.65737 39.6028 10.2031 40.068 10.8452 40.3531L16.5109 42.7812C18.1161 43.4853 19.4543 44.6841 20.33 46.2026C21.2057 47.7211 21.5732 49.4797 21.3787 51.2218L20.7081 57.3615C20.6224 58.0674 20.7465 58.783 21.0651 59.4187C21.3836 60.0545 21.8824 60.5824 22.4991 60.9364C23.1158 61.2904 23.8232 61.4548 24.5328 61.4092C25.2425 61.3636 25.923 61.11 26.4893 60.6799L31.4727 57.0262C32.8864 55.99 34.5936 55.4314 36.3463 55.4314C38.0991 55.4314 39.8062 55.99 41.2199 57.0262L46.2149 60.6799C46.7813 61.11 47.4618 61.3636 48.1714 61.4092C48.881 61.4548 49.5884 61.2904 50.2051 60.9364C50.8219 60.5824 51.3206 60.0545 51.6392 59.4187C51.9577 58.783 52.0819 58.0674 51.9962 57.3615L51.3256 51.2218C51.1238 49.4875 51.4803 47.734 52.3432 46.2161C53.2061 44.6982 54.5305 43.495 56.124 42.7812L61.7896 40.2837C62.4318 39.9987 62.9775 39.5334 63.3605 38.9444C63.7435 38.3554 63.9473 37.6678 63.9473 36.9652C63.9473 36.2627 63.7435 35.5751 63.3605 34.9861C62.9775 34.3971 62.4318 33.9318 61.7896 33.6468L58.714 32.3749C58.4277 32.2584 58.1677 32.0856 57.9494 31.8669C57.731 31.6481 57.5588 31.3878 57.4427 31.1013C57.3267 30.8149 57.2693 30.508 57.2738 30.199C57.2784 29.89 57.3449 29.585 57.4693 29.302C57.5938 29.0191 57.7737 28.764 57.9984 28.5518C58.2231 28.3396 58.4881 28.1746 58.7777 28.0666C59.0673 27.9585 59.3756 27.9096 59.6844 27.9228C59.9932 27.9359 60.2962 28.0108 60.5756 28.1431L63.6512 29.4959C65.1182 30.139 66.366 31.1956 67.2422 32.5365C68.1184 33.8773 68.5851 35.4444 68.5851 37.0462C68.5851 38.648 68.1184 40.215 67.2422 41.5559C66.366 42.8968 65.1182 43.9534 63.6512 44.5965L57.9971 47.094C57.2918 47.4019 56.703 47.9267 56.3163 48.5921C55.9296 49.2574 55.765 50.0287 55.8465 50.794L56.5171 56.9453C56.7112 58.5372 56.428 60.151 55.7033 61.5817C54.9787 63.0125 53.8453 64.1957 52.4471 64.9812C51.1851 65.6955 49.7578 66.0663 48.3077 66.0565Z" fill="#0A65FC" />
                        <path d="M36.584 45.0937C36.2788 45.094 35.9767 45.0338 35.6949 44.9167C35.4131 44.7996 35.1572 44.6279 34.9421 44.4115L24.9868 34.4562C24.7387 34.25 24.5364 33.9941 24.3931 33.7051C24.2498 33.4161 24.1685 33.1002 24.1545 32.7779C24.1405 32.4556 24.1942 32.1339 24.3119 31.8335C24.4297 31.5331 24.609 31.2608 24.8384 31.0338C25.0677 30.8069 25.342 30.6305 25.6436 30.5159C25.9452 30.4014 26.2675 30.3512 26.5896 30.3686C26.9118 30.386 27.2268 30.4706 27.5143 30.617C27.8018 30.7634 28.0554 30.9684 28.259 31.2187L36.584 39.5437L65.999 10.0825C66.4396 9.68463 67.0163 9.47148 67.6097 9.48713C68.2031 9.50279 68.7678 9.74604 69.1868 10.1665C69.6058 10.587 69.8471 11.1526 69.8606 11.746C69.8742 12.3395 69.659 12.9155 69.2596 13.3546L38.2143 44.4C38.0015 44.6171 37.748 44.7901 37.4682 44.9092C37.1885 45.0282 36.888 45.0909 36.584 45.0937Z" fill="#FFB300" />
                    </svg>
                </p>
                <h4><?php echo esc_html__('Send proposal successful', 'felan-framework'); ?></h4>
                <p style="max-width: 300px;margin-left: auto;margin-right: auto"><?php echo esc_html__('Your proposal for has been sent to the employer. Stay tuned for updates!', 'felan-framework'); ?></p>
                <p class="mb-0 mt-3 mb-3">
                    <a href="<?php echo esc_url(get_page_link(felan_get_option('felan_my_project_page_id'))); ?>" class="felan-button"><?php echo esc_html__('Your Proposals', 'felan-framework'); ?></a>
                </p>
            </div>

            <?php $thank_proposals = ob_get_clean(); ?>

            <?php ob_start(); ?>

            <a href="#" class="btn-close"><i class="far fa-times"></i></a>
            <div class="form-thank-project">
                <p class="image-thank">
                    <svg width="74" height="74" viewBox="0 0 74 74" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M48.3077 66.0565C46.5594 66.05 44.8585 65.4871 43.4515 64.4493L38.4565 60.7956C37.8367 60.3392 37.0871 60.093 36.3174 60.093C35.5477 60.093 34.7982 60.3392 34.1784 60.7956L29.1834 64.4493C27.8917 65.3927 26.3538 65.9404 24.7566 66.0261C23.1594 66.1118 21.5717 65.7316 20.1865 64.9319C18.8013 64.1321 17.6783 62.9472 16.9539 61.5211C16.2296 60.0951 15.935 58.4893 16.1062 56.899L16.7768 50.7477C16.8629 49.9833 16.7016 49.2115 16.3166 48.5455C15.9316 47.8795 15.3432 47.3546 14.6377 47.0477L8.98368 44.5502C7.5167 43.9071 6.26885 42.8505 5.39264 41.5097C4.51642 40.1688 4.0498 38.6017 4.0498 36.9999C4.0498 35.3982 4.51642 33.8311 5.39264 32.4902C6.26885 31.1493 7.5167 30.0928 8.98368 29.4496L14.6377 26.9521C15.3432 26.6453 15.9316 26.1204 16.3166 25.4544C16.7016 24.7884 16.8629 24.0165 16.7768 23.2521L16.1062 17.1009C15.935 15.5106 16.2296 13.9048 16.9539 12.4787C17.6783 11.0527 18.8013 9.86777 20.1865 9.06801C21.5717 8.26825 23.1594 7.8881 24.7566 7.97377C26.3538 8.05943 27.8917 8.60723 29.1834 9.55056L34.1784 13.2043C34.7982 13.6607 35.5477 13.9069 36.3174 13.9069C37.0871 13.9069 37.8367 13.6607 38.4565 13.2043L43.4515 9.55056C44.341 8.8922 45.3561 8.42324 46.4339 8.17265C47.5118 7.92207 48.6297 7.8952 49.7184 8.09369C50.8305 8.29549 51.8904 8.72012 52.8342 9.34206C53.7781 9.964 54.5864 10.7704 55.2106 11.7128C55.3836 11.9662 55.5045 12.2516 55.5661 12.5523C55.6277 12.8529 55.6289 13.1628 55.5695 13.4639C55.5102 13.7651 55.3915 14.0513 55.2203 14.3061C55.0492 14.5609 54.8291 14.779 54.5728 14.9479C54.3165 15.1167 54.0291 15.2328 53.7275 15.2894C53.4258 15.3461 53.1159 15.3421 52.8159 15.2777C52.5158 15.2134 52.2315 15.09 51.9796 14.9146C51.7277 14.7393 51.5132 14.5156 51.3487 14.2565C51.0696 13.8371 50.7086 13.4786 50.2873 13.2024C49.8661 12.9262 49.3933 12.738 48.8974 12.6493C48.42 12.5613 47.9294 12.5745 47.4573 12.688C46.9853 12.8015 46.5423 13.0129 46.1571 13.3084L41.1621 16.9621C39.7484 17.9983 38.0413 18.5569 36.2885 18.5569C34.5358 18.5569 32.8286 17.9983 31.4149 16.9621L26.4315 13.3084C25.8651 12.8783 25.1847 12.6247 24.475 12.5791C23.7654 12.5335 23.058 12.698 22.4413 13.052C21.8246 13.406 21.3258 13.9338 21.0073 14.5696C20.6887 15.2054 20.5646 15.9209 20.6502 16.6268L21.3209 22.7665C21.5224 24.5038 21.1647 26.2601 20.2997 27.7801C19.4347 29.3001 18.1074 30.5047 16.5109 31.2187L10.8452 33.7162C10.2031 34.0012 9.65737 34.4665 9.27437 35.0555C8.89137 35.6445 8.68751 36.332 8.68751 37.0346C8.68751 37.7372 8.89137 38.4247 9.27437 39.0138C9.65737 39.6028 10.2031 40.068 10.8452 40.3531L16.5109 42.7812C18.1161 43.4853 19.4543 44.6841 20.33 46.2026C21.2057 47.7211 21.5732 49.4797 21.3787 51.2218L20.7081 57.3615C20.6224 58.0674 20.7465 58.783 21.0651 59.4187C21.3836 60.0545 21.8824 60.5824 22.4991 60.9364C23.1158 61.2904 23.8232 61.4548 24.5328 61.4092C25.2425 61.3636 25.923 61.11 26.4893 60.6799L31.4727 57.0262C32.8864 55.99 34.5936 55.4314 36.3463 55.4314C38.0991 55.4314 39.8062 55.99 41.2199 57.0262L46.2149 60.6799C46.7813 61.11 47.4618 61.3636 48.1714 61.4092C48.881 61.4548 49.5884 61.2904 50.2051 60.9364C50.8219 60.5824 51.3206 60.0545 51.6392 59.4187C51.9577 58.783 52.0819 58.0674 51.9962 57.3615L51.3256 51.2218C51.1238 49.4875 51.4803 47.734 52.3432 46.2161C53.2061 44.6982 54.5305 43.495 56.124 42.7812L61.7896 40.2837C62.4318 39.9987 62.9775 39.5334 63.3605 38.9444C63.7435 38.3554 63.9473 37.6678 63.9473 36.9652C63.9473 36.2627 63.7435 35.5751 63.3605 34.9861C62.9775 34.3971 62.4318 33.9318 61.7896 33.6468L58.714 32.3749C58.4277 32.2584 58.1677 32.0856 57.9494 31.8669C57.731 31.6481 57.5588 31.3878 57.4427 31.1013C57.3267 30.8149 57.2693 30.508 57.2738 30.199C57.2784 29.89 57.3449 29.585 57.4693 29.302C57.5938 29.0191 57.7737 28.764 57.9984 28.5518C58.2231 28.3396 58.4881 28.1746 58.7777 28.0666C59.0673 27.9585 59.3756 27.9096 59.6844 27.9228C59.9932 27.9359 60.2962 28.0108 60.5756 28.1431L63.6512 29.4959C65.1182 30.139 66.366 31.1956 67.2422 32.5365C68.1184 33.8773 68.5851 35.4444 68.5851 37.0462C68.5851 38.648 68.1184 40.215 67.2422 41.5559C66.366 42.8968 65.1182 43.9534 63.6512 44.5965L57.9971 47.094C57.2918 47.4019 56.703 47.9267 56.3163 48.5921C55.9296 49.2574 55.765 50.0287 55.8465 50.794L56.5171 56.9453C56.7112 58.5372 56.428 60.151 55.7033 61.5817C54.9787 63.0125 53.8453 64.1957 52.4471 64.9812C51.1851 65.6955 49.7578 66.0663 48.3077 66.0565Z" fill="#0A65FC" />
                        <path d="M36.584 45.0937C36.2788 45.094 35.9767 45.0338 35.6949 44.9167C35.4131 44.7996 35.1572 44.6279 34.9421 44.4115L24.9868 34.4562C24.7387 34.25 24.5364 33.9941 24.3931 33.7051C24.2498 33.4161 24.1685 33.1002 24.1545 32.7779C24.1405 32.4556 24.1942 32.1339 24.3119 31.8335C24.4297 31.5331 24.609 31.2608 24.8384 31.0338C25.0677 30.8069 25.342 30.6305 25.6436 30.5159C25.9452 30.4014 26.2675 30.3512 26.5896 30.3686C26.9118 30.386 27.2268 30.4706 27.5143 30.617C27.8018 30.7634 28.0554 30.9684 28.259 31.2187L36.584 39.5437L65.999 10.0825C66.4396 9.68463 67.0163 9.47148 67.6097 9.48713C68.2031 9.50279 68.7678 9.74604 69.1868 10.1665C69.6058 10.587 69.8471 11.1526 69.8606 11.746C69.8742 12.3395 69.659 12.9155 69.2596 13.3546L38.2143 44.4C38.0015 44.6171 37.748 44.7901 37.4682 44.9092C37.1885 45.0282 36.888 45.0909 36.584 45.0937Z" fill="#FFB300" />
                    </svg>
                </p>
                <h4><?php echo esc_html__('Update proposal successful', 'felan-framework'); ?></h4>
                <p style="max-width: 300px;margin-left: auto;margin-right: auto"><?php echo esc_html__('Your proposal for has been sent to the employer. Stay tuned for updates!', 'felan-framework'); ?></p>
            </div>

            <?php $update_proposal = ob_get_clean(); ?>

            <?php echo json_encode(array('success' => true, 'thank_proposals' => $thank_proposals, 'update_proposal' => $update_proposal, 'message' => esc_html__('You have sent the message successfully', 'felan-framework')));

            wp_die();
        }

        /**
         * Proposal Project
         */
        public function felan_filter_project_applicants()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $project_search = isset($_REQUEST['project_search']) ? felan_clean(wp_unslash($_REQUEST['project_search'])) : '';
            $sort_by = isset($_REQUEST['project_sort_by']) ? felan_clean(wp_unslash($_REQUEST['project_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $content_refund = isset($_REQUEST['content_refund']) ? felan_clean(wp_unslash($_REQUEST['content_refund'])) : '';
            $project_payment = isset($_REQUEST['project_payment']) ? felan_clean(wp_unslash($_REQUEST['project_payment'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();

            if (!empty($item_id)) {
                if ($action_click == 'completed') {
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'proposal_status', 'completed');
                    $price = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'proposal_price', true);
                    $author_id = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'creator_message', true);
                    $withdraw_price = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
                    if (empty($withdraw_price)) {
                        $withdraw_price = 0;
                    }
                    $withdraw_price = intval($withdraw_price) + intval($price);
                    update_user_meta($author_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', $withdraw_price);

                    //Employer Projects Completed
                    $project_completed = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_project_completed', true);
                    if (empty($project_completed)) {
                        $project_completed = 0;
                    }
                    $project_completed = $project_completed + 1;
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_project_completed', $project_completed);
                }
                if ($action_click == 'refund') {
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'proposal_status', 'refund');
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'project_refund_payment_method', $project_payment);
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'project_refund_content', $content_refund);
                }
            }

            $args_project = array(
                'post_type' => 'project',
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'author' => $user_id,
                'orderby' => 'date',
            );
            $data_project = new WP_Query($args_project);
            $project_employer_id = array();
            if ($data_project->have_posts()) {
                while ($data_project->have_posts()) : $data_project->the_post();
                    $project_employer_id[] = get_the_ID();
                endwhile;
            }

            $args_applicants = array(
                'post_type' => 'project-proposal',
                'ignore_sticky_posts' => 1,
                'paged' => $paged,
            );

            $meta_query[] = array(
                'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
                'value' => $project_employer_id,
                'compare' => 'IN'
            );

            if (!empty($project_search)) {
                $args_applicants['s'] = $project_search;
            }

            if (!empty($item_amount)) {
                $args_applicants['posts_per_page'] = $item_amount;
            }

            //meta query applicants sort_by
            if (!empty($sort_by)) {
                if ($sort_by == 'newest') {
                    $args_applicants['order'] = 'DESC';
                }
                if ($sort_by == 'oldest') {
                    $args_applicants['order'] = 'ASC';
                }
            }

            $args_applicants['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args_applicants['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $data = new WP_Query($args_applicants);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                //'add_args'  => array_map( 'urlencode', $args ),
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    $id = get_the_ID();
                    global $current_user;
                    wp_get_current_user();
                    $user_id = $current_user->ID;
                    $public_date = get_the_date(get_option('date_format'));
                    $project_id = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                    $proposal_price = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_price', true);
                    $proposal_time = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_time', true);
                    $proposal_time_type = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_time_type', true);
                    $proposal_message = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_message', true);
                    $proposal_status = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_status', true);
                    $project_refund_content = get_post_meta($id, FELAN_METABOX_PREFIX . 'project_refund_content', true);
                    $author_id = get_post_field('post_author', $id);

                    $freelancer_id = '';
                    if (!empty($author_id)) {
                        $args_freelancer = array(
                            'post_type' => 'freelancer',
                            'posts_per_page' => 1,
                            'author' => $author_id,
                        );
                        $current_user_posts = get_posts($args_freelancer);
                        $freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
                        $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    }
                    $read_mess = get_post_meta($id, FELAN_METABOX_PREFIX . 'read_mess', true);
                    $reply_mess = get_post_meta($id, FELAN_METABOX_PREFIX . 'reply_mess', true);
                    $project_currency_type = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_currency_type', true);
                    $currency_position = felan_get_option('currency_position');
                    $currency_leff = $currency_right = '';
                    if ($currency_position == 'before') {
                        $currency_leff = $project_currency_type;
                    } else {
                        $currency_right = $project_currency_type;
                    }
                    ?>
                    <tr>
                        <td class="info-user">
                            <?php if (!empty($freelancer_avatar)) : ?>
                                <div class="image-applicants"><img class="image-freelancers" src="<?php echo esc_url($freelancer_avatar) ?>" alt="" /></div>
                            <?php else : ?>
                                <div class="image-applicants"><i class="far fa-camera"></i></div>
                            <?php endif; ?>
                            <div class="info-details">
                                <?php if (!empty(get_the_author())) { ?>
                                    <h3>
                                        <a href="<?php echo get_post_permalink($freelancer_id); ?>"><?php echo get_the_author(); ?></a>
                                    </h3>
                                <?php } else { ?>
                                    <h3><?php esc_html_e('User not logged in', 'felan-framework'); ?></h3>
                                <?php } ?>
                                <?php if (!empty(get_the_title())) { ?>
                                    <div class="applied"><?php esc_html_e('Applied:', 'felan-framework') ?>
                                        <a href="<?php echo esc_url(get_permalink($project_id)); ?>" target="_blank">
                                            <span> <?php esc_html_e(get_the_title()); ?></span>
                                            <i class="far fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </td>
                        <td class="status">
                            <?php felan_project_package_status($proposal_status); ?>
                        </td>
                        <td>
                            <span><?php echo $currency_leff . $proposal_price . $currency_right; ?></span>
                        </td>
                        <td>
                            <span><?php echo sprintf(esc_html__('%1s %2s', 'felan-framework'), $proposal_time, $proposal_time_type) ?></span>
                        </td>
                        <td class="start-time">
                            <?php echo $public_date; ?>
                        </td>
                        <td class="applicants-control action-setting">
                            <div class="list-action">
                                <?php if (!empty(get_the_author())) { ?>
                                    <a href="#" class="action icon-video tooltip btn-reschedule-meetings" data-id="<?php echo esc_attr($id); ?>" data-title="<?php esc_attr_e('Create a Meeting', 'felan-framework') ?>"><i class="far fa-video-plus"></i></a>
                                    <?php if ($reply_mess !== 'yes') : ?>
                                        <a href="#" class="action icon-messages tooltip" id="btn-mees-applicants" data-apply="<?php esc_html_e(get_the_title()); ?>" data-id="<?php echo esc_attr($id); ?>" data-mess="<?php echo $proposal_message; ?>" data-project-id="<?php echo $project_id; ?>" data-title="<?php esc_attr_e('Messages Applicants', 'felan-framework') ?>">
                                            <i class="far fa-comment-dots <?php if ($read_mess === 'yes') {
                                                                                echo 'active';
                                                                            } ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php } ?>
                                <div class="action">
                                    <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                                    <ul class="action-dropdown">
                                        <?php switch ($proposal_status) {
                                            case 'completed': ?>
                                                <li><a class="btn-action-review" freelancer-id="<?php echo $freelancer_id; ?>" href="#"><?php esc_html_e('Leave a review', 'felan-framework') ?></a>
                                                </li>
                                            <?php break;
                                            case 'transferring': ?>
                                                <li><a class="btn-completed" order-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Complete', 'felan-framework') ?></a>
                                                </li>
                                                <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                </li>
                                            <?php break;
                                            case 'confirming': ?>
                                                <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                </li>
                                            <?php break;
                                            case 'inprogress': ?>
                                                <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                </li>
                                            <?php break;
                                            case 'pending': ?>
                                                <li><a class="btn-accept-pay" data-id="<?php echo esc_attr($id); ?>" data-price="<?php echo esc_attr($proposal_price); ?>" data-time="<?php echo esc_attr($proposal_time); ?>" data-time-type="<?php echo esc_attr($proposal_time_type); ?>" href="#"><?php esc_html_e('Payment Redirect To Admin', 'felan-framework') ?></a>
                                                </li>
                                            <?php break;
                                            case 'canceled': ?>
                                                <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                </li>
                                            <?php break;
                                            case 'expired': ?>
                                                <li><a class="btn-completed" order-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Complete', 'felan-framework') ?></a>
                                                </li>
                                                <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                </li>
                                            <?php break;
                                            case 'refund': ?>
                                                <?php if (!empty($project_refund_content)) : ?>
                                                    <li><a class="btn-view-reason" order-id="<?php echo esc_attr($id); ?>" data-content-refund="<?php echo $project_refund_content; ?>" href="#form-project-view-reason"><?php esc_html_e('View reason', 'felan-framework') ?></a>
                                                    </li>
                                                <?php else : ?>
                                                    <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Refund reason text is empty'); ?>"><?php esc_html_e('View reason', 'felan-framework'); ?></a>
                                                    </li>
                                                <?php endif; ?>
                                        <?php break;
                                        } ?>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $project_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'content_refund' => $content_refund,
                    'pagination' => $pagination,
                    'project_html' => $project_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Project Package
         */
        public function felan_project_package()
        {
            $project_id = isset($_REQUEST['project_id']) ? felan_clean(wp_unslash($_REQUEST['project_id'])) : '';
            $project_price = isset($_REQUEST['project_price']) ? felan_clean(wp_unslash($_REQUEST['project_price'])) : '';
            $project_time = isset($_REQUEST['project_time']) ? felan_clean(wp_unslash($_REQUEST['project_time'])) : '';
            $project_time_type = isset($_REQUEST['project_time_type']) ? felan_clean(wp_unslash($_REQUEST['project_time_type'])) : '';

            global $current_user;
            $user_id = $current_user->ID;

            if (!empty($project_id)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_id', $project_id);
            }

            if (!empty($project_price)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_price', $project_price);
            }

            if (!empty($project_time)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_time', $project_time);
            }

            if (!empty($project_time_type)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_time_type', $project_time_type);
            }

            $ajax_response = array('success' => true, '321' => $project_price);
            echo json_encode($ajax_response);

            wp_die();
        }


        /**
         * Submit withdraw
         */
        public function felan_project_submit_withdraw()
        {
            $withdraw_price = isset($_REQUEST['withdraw_price']) ? felan_clean(wp_unslash($_REQUEST['withdraw_price'])) : '10';
            $withdraw_payment = isset($_REQUEST['withdraw_payment']) ? felan_clean(wp_unslash($_REQUEST['withdraw_payment'])) : '1';

            global $current_user;
            $user_id = $current_user->ID;
            $user_name = $current_user->display_name;
            $author_payout_paypal = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_paypal', true);
            $author_payout_stripe = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_stripe', true);
            $author_payout_card_number = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_card_number', true);
            $author_payout_card_name = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_card_name', true);
            $author_payout_bank_transfer_name = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_bank_transfer_name', true);
            $enable_paypal = felan_get_option('enable_payout_paypal');
            $enable_stripe = felan_get_option('enable_payout_stripe');
            $enable_bank = felan_get_option('enable_payout_bank_transfer');
            $custom_payout = felan_get_option('custom_payout_setting');

            $total_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
            if (empty($total_price)) {
                $total_price = 0;
            }

            if ($withdraw_price == '') {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('Please enter the amount of money', 'felan-framework')
                ));
                wp_die();
            }

            if ($withdraw_price > $total_price) {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('The amount to withdraw is larger than the available amount', 'felan-framework')
                ));
                wp_die();
            }

            if ($withdraw_payment == 'paypal' && empty($author_payout_paypal) && $enable_paypal === '1') {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('Please enter full payout information paypal', 'felan-framework')
                ));
                wp_die();
            }

            if ($withdraw_payment == 'stripe' && empty($author_payout_stripe && $enable_stripe === '1')) {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('Please enter full payout information stripe', 'felan-framework')
                ));
                wp_die();
            }

            if ($withdraw_payment == 'wire_transfer' && $enable_bank === '1' && (empty($author_payout_card_number) || empty($author_payout_card_name) || empty($author_payout_bank_transfer_name))) {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('Please enter full payout information wire transfer', 'felan-framework')
                ));
                wp_die();
            }

            if (!empty($custom_payout)) :
                foreach ($custom_payout as $field) :
                    if (!empty($field['name'])) :
                        $author_payout = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_custom_' . $field['id'], true);
                        $field['name_id'] = str_replace(' ', '-', $field['name']);
                        if ($withdraw_payment == $field['name_id'] && empty($author_payout)) {
                            echo json_encode(array(
                                'success' => false,
                                'message' => sprintf(__('Please enter full payout information %s', 'felan-framework'), $field['name'])
                            ));
                            wp_die();
                        }
                    endif;
                endforeach;
            endif;

            $withdraw_payment = str_replace(['-', '_'], ' ', $withdraw_payment);
            $new_post = array(
                'post_type' => 'freelancer_withdraw',
                'post_status' => 'publish',
            );
            $post_title = $user_name;
            if (isset($post_title)) {
                $new_post['post_title'] = $post_title;
                $post_id = wp_insert_post($new_post, true);
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'user_total_price_withdraw', $total_price);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_status', 'pending');
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_user_id', $user_id);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_payment_method', $withdraw_payment);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_price', $withdraw_price);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', $total_price);
            }

            echo json_encode(array('success' => true));

            wp_die();
        }

        /**
         * Freelancer freelancer project
         */
        public function felan_freelancer_proposal_project()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $project_search = isset($_REQUEST['project_search']) ? felan_clean(wp_unslash($_REQUEST['project_search'])) : '';
            $project_status = isset($_REQUEST['project_status']) ? felan_clean(wp_unslash($_REQUEST['project_status'])) : '';
            $sort_by = isset($_REQUEST['project_sort_by']) ? felan_clean(wp_unslash($_REQUEST['project_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
            $my_project = felan_get_option('felan_my_project_page_id');
            $meta_query = array();
            $tax_query = array();

            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    $item_author_id = get_post_field('post_author', $item_id);
                    if ($user_id == $item_author_id) {
                        wp_delete_post($item_id, true);
                    }
                }
            }

            $args = array(
                'post_type' => 'project-proposal',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'author' => $user_id,
            );

            if (!empty($project_search)) {
                $args['s'] = $project_search;
            }

            if (!empty($project_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'proposal_status',
                    'value' => $project_status,
                    'compare' => '=',
                );
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            if (!empty($sort_by)) {
                if ($sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($sort_by == 'oldest') {
                    $args['order'] = 'ASC';
                }
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                //'add_args'  => array_map( 'urlencode', $args ),
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    $proposal_id = get_the_ID();
                    $project_id = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                    $projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);
                    $project_categories = get_the_terms($project_id, 'project-categories');
                    $project_location = get_the_terms($project_id, 'felan_location');
                    $thumbnail = get_the_post_thumbnail_url($project_id, '70x70');
                    $project_featured = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
                    $project_select_company = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_select_company', true);
                    $author_id = get_post_field('post_author', $project_id);
                    $author_name = get_the_author_meta('display_name', $author_id);

                    $projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);
                    $class_fixed = '';
                    if ($projects_budget_show == 'fixed') {
                        $class_fixed = 'fixed';
                    }

                    $proposal_has_disputes_id = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_has_disputes_id', true);
                    $proposal_status = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_status', true);
                    $proposal_price = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_price', true);
                    $proposal_time = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_time', true);
                    $proposal_fixed_time = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_fixed_time', true);
                    $proposal_rate = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_rate', true);
                    $proposal_maximum_time = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_maximum_time', true);
                    $currency_sign_default = felan_get_option('currency_sign_default');
                    $currency_position = felan_get_option('currency_position');
                    if ($currency_position == 'before') {
                        $proposal_total_price = $currency_sign_default . $proposal_price;
                        $proposal_maximum_hours = $currency_sign_default . $proposal_maximum_time;
                    } else {
                        $proposal_total_price = $proposal_price . $currency_sign_default;
                        $proposal_maximum_hours = $proposal_maximum_time . $currency_sign_default;
                    }

                    $public_date = get_the_date('Y-m-d');
                    $current_date = date('Y-m-d');
                    $public_timestamp = strtotime($public_date);
                    $current_timestamp = strtotime($current_date);
                    $time_difference = $current_timestamp - $public_timestamp;
                    $months_ago = floor($time_difference / (30 * 24 * 60 * 60));
                    $days_ago = floor($time_difference / (24 * 60 * 60));
                    ?>
                    <tr>
                        <td>
                            <div class="project-header">
                                <?php if (!empty($thumbnail)) : ?>
                                    <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                <?php endif; ?>
                                <div class="content">
                                    <h3 class="title-my-project">
                                        <a href="<?php echo get_the_permalink($project_id) ?>">
                                            <?php echo get_the_title($project_id); ?>
                                            <?php if ($project_featured === '1') : ?>
                                                <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                    <p class="d-flex align-items-center">
                                        <span class="mr-3">
                                            <?php echo sprintf(esc_html__('by %s', 'felan-framework'), $author_name) ?>
                                        </span>
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-1">
                                            <path d="M2.25 9C2.25 5.81802 2.25 4.97703 3.23851 3.98851C4.22703 3 5.81802 3 9 3C12.182 3 13.773 3 14.7615 3.98851C15.75 4.97703 15.75 5.81802 15.75 9C15.75 12.182 15.75 13.773 14.7615 14.7615C13.773 15.75 12.182 15.75 9 15.75C5.81802 15.75 4.22703 15.75 3.23851 14.7615C2.25 13.773 2.25 12.182 2.25 9Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12.375 3.75V2.25" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M5.625 3.75V2.25" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M2.4375 6H15.5625" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <?php if ($months_ago > 0) {
                                            echo esc_html(sprintf(_n('%s month ago', '%s months ago', $months_ago, 'felan-framework'), $months_ago));
                                        } elseif ($days_ago > 0) {
                                            echo esc_html(sprintf(_n('%s day ago', '%s days ago', $days_ago, 'felan-framework'), $days_ago));
                                        } else {
                                            echo esc_html__('Today', 'felan-framework');
                                        } ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="price-inner">
                            <p class="price"><?php echo esc_html($proposal_total_price); ?></p>
                            <?php if ($projects_budget_show == 'hourly') : ?>
                                <p class="maximum-time">
                                    <?php echo esc_html($proposal_time); ?>
                                    <?php echo esc_html__('hours', 'felan-framework'); ?>
                                </p>
                            <?php else: ?>
                                <p class="maximum-time"><?php echo sprintf(esc_html__('%1s %2s', 'felan-framework'), $proposal_fixed_time, $proposal_rate) ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="status">
                            <?php felan_project_package_status($proposal_status); ?>
                        </td>
                        <td class="action-order">
                            <?php if ($proposal_status == 'inprogress') : ?>
                                <a href="<?php echo esc_url(get_page_link($my_project)); ?>?applicants_id=<?php echo esc_attr($proposal_id); ?>&project_id=<?php echo esc_attr($project_id); ?>" class="felan-button">
                                    <?php echo esc_html__('Detail', 'felan-framework') ?>
                                </a>
                            <?php elseif ($proposal_status == 'canceled') : ?>
                                <?php if (!empty($proposal_has_disputes_id)) : ?>
                                    <a href="<?php echo esc_url(felan_get_permalink('freelancer_disputes')); ?>?listing=project&order_id=<?php echo esc_attr($proposal_id) ?>&disputes_id=<?php echo esc_attr($proposal_has_disputes_id) ?>"
                                        class="felan-button button-outline-gray btn-dispute">
                                        <?php echo esc_html('View Dispute', 'felan-framework'); ?>
                                    </a>
                                <?php else: ?>
                                    <?php if ($user_demo == 'yes') { ?>
                                        <a href="#" class="felan-button button-outline-gray btn-add-to-message"
                                            data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                            <?php echo esc_html__('Delete', 'felan-framework') ?>
                                        </a>
                                    <?php } else { ?>
                                        <a href="#" class="felan-button button-outline-gray btn-delete" item-id="<?php echo esc_attr($proposal_id); ?>">
                                            <?php echo esc_html('Delete', 'felan-framework'); ?>
                                        </a>
                                    <?php } ?>
                                <?php endif; ?>
                            <?php elseif ($proposal_status == 'reject') : ?>
                                <?php if ($user_demo == 'yes') { ?>
                                    <a href="#" class="felan-button button-outline-gray btn-add-to-message"
                                        data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                        <?php echo esc_html__('Delete', 'felan-framework') ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#" class="felan-button button-outline-gray btn-delete" item-id="<?php echo esc_attr($proposal_id); ?>">
                                        <?php echo esc_html('Delete', 'felan-framework'); ?>
                                    </a>
                                <?php } ?>
                            <?php elseif ($proposal_status == 'completed') : ?>
                                <a href="<?php echo esc_url(get_page_link($my_project)); ?>?applicants_id=<?php echo esc_attr($proposal_id); ?>&project_id=<?php echo esc_attr($project_id); ?>" class="felan-button">
                                    <?php echo esc_html__('Detail', 'felan-framework') ?>
                                </a>
                            <?php else: ?>
                                <a href="#form-apply-project" class="felan-button button-outline-gray btn-edit-project btn-edit-proposals <?php echo esc_attr($class_fixed); ?>" id="felan-apply-project"
                                    data-post-current="<?php echo intval($project_id); ?>"
                                    data-proposal-id="<?php echo intval($proposal_id); ?>"
                                    data-author-id="<?php echo intval($user_id); ?>"
                                    data-info-price='<?php echo felan_get_budget_project($project_id); ?>'
                                    data-info-hours="<?php echo esc_attr(felan_project_maximum_time($project_id)); ?>">
                                    <?php esc_html_e('Edit proposals', 'felan-framework') ?>
                                </a>
                                <?php if ($user_demo == 'yes') { ?>
                                    <a href="#" class="felan-button button-outline-gray btn-add-to-message"
                                        data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                        <?php echo esc_html__('Delete', 'felan-framework') ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#" class="felan-button button-outline-gray ml-1 btn-delete" item-id="<?php echo esc_attr($proposal_id); ?>">
                                        <?php echo esc_html__('Delete', 'felan-framework'); ?>
                                    </a>
                                <?php } ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $project_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'project_html' => $project_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * My Project
         */
        public function felan_filter_my_project()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $project_search = isset($_REQUEST['project_search']) ? felan_clean(wp_unslash($_REQUEST['project_search'])) : '';
            $project_status = isset($_REQUEST['project_status']) ? felan_clean(wp_unslash($_REQUEST['project_status'])) : '';
            $sort_by = isset($_REQUEST['project_sort_by']) ? felan_clean(wp_unslash($_REQUEST['project_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $felan_profile = new Felan_Profile();

            $withdraw_total_price = '';
            $meta_query = array();
            $tax_query = array();

            $package_num_featured_project = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_number_project_featured', $user_id);
            $package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
            $package_unlimited_featured_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project_featured', true);

            if (!empty($item_id)) {
                $project = get_post($item_id);
                if ($action_click == 'mark-featured') {
                    if ($package_unlimited_featured_project !== '1' && $package_num_featured_project > 0) {
                        update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_project_featured', $package_num_featured_project - 1);
                    }
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'project_featured', 1);

                    $withdraw_total_price = felan_withdraw_total_price_ajax('featured_project', $user_id, 'employer');
                }

                if ($action_click == 'mark-filled') {
                    $data = array(
                        'ID' => $item_id,
                        'post_type' => 'project',
                        'post_status' => 'expired'
                    );
                    wp_update_post($data);
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'project_featured', 0);
                }

                if ($action_click == 'show') {
                    if ($project->post_status == 'pause') {
                        $data = array(
                            'ID' => $item_id,
                            'post_type' => 'project',
                            'post_status' => 'publish'
                        );
                    }
                    wp_update_post($data);
                }

                if ($action_click == 'pause') {
                    $data = array(
                        'ID' => $item_id,
                        'post_type' => 'project',
                        'post_status' => 'pause'
                    );
                    wp_update_post($data);
                }
            }

            $args = array(
                'post_type' => 'project',
                'paged' => $paged,
                'post_status' => array('publish', 'expired', 'pending', 'pause'),
                'ignore_sticky_posts' => 1,
                'author' => $user_id,
                'orderby' => 'date',
            );

            if (!empty($project_search)) {
                $args['s'] = $project_search;
            }

            if (!empty($project_status)) {
                $args['post_status'] = $project_status;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query project sort_by
            if (!empty($sort_by)) {
                if ($sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($sort_by == 'oldest') {
                    $args['order'] = 'ASC';
                }
                if ($sort_by == 'featured') {
                    $meta_query[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_featured',
                        'value' => 1,
                        'type' => 'NUMERIC',
                        'compare' => '=',
                    );
                }
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                //'add_args'  => array_map( 'urlencode', $args ),
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();
            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    $id = get_the_ID();
                    $ids[] = $id;
                    global $current_user;
                    wp_get_current_user();
                    $user_id = $current_user->ID;
                    $status = get_post_status($id);
                    $project_categories =  get_the_terms($id, 'project-categories');
                    $public_date = get_the_date('Y-m-d');
                    $current_date = date('Y-m-d');
                    $project_featured    = get_post_meta($id, FELAN_METABOX_PREFIX . 'project_featured', true);
                    $val_public_date = get_the_date(get_option('date_format'));
                    $thumbnail_id = get_post_thumbnail_id();
                    $thumbnail_url = !empty($thumbnail_id) ? wp_get_attachment_image_src($thumbnail_id, 'full') : false;
                    $projects_budget_show = get_post_meta($id, FELAN_METABOX_PREFIX . 'project_budget_show', true);

                    $withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
                    $wallet_fee_amount = felan_get_option('wallet_featured_project_fee_amount', '');
                    $enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
                    ?>
                    <tr>
                        <td>
                            <div class="project-thumbnail-inner">
                                <?php if ($thumbnail_url) : ?>
                                    <div class="project-thumbnail">
                                        <img src="<?php echo $thumbnail_url[0]; ?>" alt="<?php the_title(); ?>">
                                    </div>
                                <?php endif; ?>
                                <div class="content-project">
                                    <h3 class="title-project-dashboard">
                                        <a href="<?php echo get_the_permalink($id); ?>" target="_blank">
                                            <?php echo get_the_title($id); ?>
                                            <?php if ($project_featured == '1') : ?>
                                                <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <span><?php echo esc_html__('in', 'felan-framework'); ?></span>
                                        <?php if (is_array($project_categories)) {
                                            foreach ($project_categories as $categories) {
                                                $categories_link = get_term_link($categories, 'project-categories'); ?>
                                                <a href="<?php echo esc_url($categories_link); ?>" class="cate">
                                                    <?php esc_html_e($categories->name); ?>
                                                </a>
                                        <?php }
                                        } ?>
                                    </p>
                                </div>
                            </div>
                            <?php if (felan_total_applications_project_id($id) > 0) { ?>
                                <a href="<?php echo esc_attr('#list-applicant-' . $id); ?>" class="project-number-applicant">
                                    <span class="number"><?php echo felan_total_applications_project_id($id); ?></span>
                                    <?php if (felan_total_applications_project_id($id) > 1) { ?>
                                        <span><?php echo esc_html__('Proposals', 'felan-framework') ?></span>
                                    <?php } else { ?>
                                        <span><?php echo esc_html__('Proposal', 'felan-framework') ?></span>
                                    <?php } ?>
                                </a>
                            <?php } else { ?>
                                <span class="project-number-applicant">
                                    <span class="number"><?php echo felan_total_applications_project_id($id); ?></span>
                                    <?php if (felan_total_applications_project_id($id) > 1) { ?>
                                        <span><?php echo esc_html__('Proposals', 'felan-framework') ?></span>
                                    <?php } else { ?>
                                        <span><?php echo esc_html__('Proposal', 'felan-framework') ?></span>
                                    <?php } ?>
                                </span>
                            <?php } ?>
                        </td>
                        <td>
                            <span class="start-time"><?php echo $val_public_date ?></span>
                        </td>
                        <td class="price">
                            <?php echo felan_get_budget_project($id); ?>
                            <p class="budget-show">
                                <?php if ($projects_budget_show == 'hourly') : ?>
                                    <?php echo esc_html__('Hourly Rate', 'felan-framework'); ?>
                                <?php else: ?>
                                    <?php echo esc_html__('Fixed Price', 'felan-framework'); ?>
                                <?php endif; ?>
                            </p>
                        </td>
                        <td>
                            <?php if ($status == 'expired') : ?>
                                <span class="label label-close"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                            <?php endif; ?>
                            <?php if ($status == 'publish') : ?>
                                <span class="label label-open"><?php esc_html_e('Opening', 'felan-framework') ?></span>
                            <?php endif; ?>
                            <?php if ($status == 'pending') : ?>
                                <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                            <?php endif; ?>
                            <?php if ($status == 'pause') : ?>
                                <span class="label label-pause"><?php esc_html_e('Pause', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="action-setting project-control">
                            <?php if ($status !== 'expired') : ?>
                                <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                                <ul class="action-dropdown">
                                    <?php
                                    $project_dashboard_link = felan_get_permalink('project_dashboard');
                                    $paid_submission_type = felan_get_option('paid_submission_type', 'no');
                                    $check_package = $felan_profile->user_package_available($user_id);
                                    $package_num_featured_project = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_number_project_featured', $user_id);
                                    $package_unlimited_featured_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project_featured', true);
                                    $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                                    switch ($status) {
                                        case 'publish':
                                            if ($paid_submission_type == 'per_package') { ?>
                                                <li><a class="btn-edit" href="<?php echo esc_url($project_dashboard_link); ?><?php echo strpos(esc_url($project_dashboard_link), '?') ? '&' : '?' ?>pages=edit&project_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a></li>
                                                <?php if ($user_demo == 'yes') { ?>

                                                    <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Paused', 'felan-framework'); ?></a></li>
                                                    <?php if ($project_featured != 1) { ?>
                                                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark featured', 'felan-framework'); ?></a></li>
                                                    <?php } ?>
                                                    <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark Filled', 'felan-framework'); ?></a></li>

                                                    <?php } else {

                                                    if ($check_package != -1 && $check_package != 0) { ?>
                                                        <li><a class="btn-pause" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Paused', 'felan-framework') ?></a></li>
                                                    <?php } ?>

                                                    <?php if ($enable_wallet_mode == '1') {
                                                        if (intval($withdraw_price) > intval($wallet_fee_amount)) { ?>
                                                            <?php if ($project_featured != 1) { ?>
                                                                <li><a class="btn-mark-featured" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a></li>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <?php if ($project_featured !== 1) { ?>
                                                                <li>
                                                                    <a class="btn-add-to-message" href="#"
                                                                        data-text="<?php echo esc_attr('Your wallet balance is currently insufficient.', 'felan-framework'); ?>">
                                                                        <?php esc_html_e('Mark featured', 'felan-framework') ?>
                                                                    </a>
                                                                </li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php if (($package_unlimited_featured_project == '1' || $package_num_featured_project > 0) && $project_featured != 1 && $check_package != -1  && $check_package != 0) { ?>
                                                            <li><a class="btn-mark-featured" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a></li>
                                                        <?php } ?>
                                                    <?php } ?>

                                                    <?php if ($check_package != -1 && $check_package != 0) { ?>
                                                        <li><a class="btn-mark-filled" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark Filled', 'felan-framework') ?></a></li>
                                                    <?php }
                                                }

                                                if ($check_package != -1 && $check_package != 0) { ?>
                                                    <li><a href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('View detail', 'felan-framework') ?></a></li>
                                                <?php }
                                            } else { ?>
                                                <li><a class="btn-edit" href="<?php echo esc_url($project_dashboard_link); ?>?pages=edit&project_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a></li>

                                                <?php if ($user_demo == 'yes') { ?>
                                                    <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Paused', 'felan-framework'); ?></a></li>
                                                    <?php if ($project_featured != 1) { ?>
                                                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark featured', 'felan-framework'); ?></a></li>
                                                    <?php } ?>
                                                    <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark Filled', 'felan-framework'); ?></a></li>
                                                <?php } else { ?>
                                                    <li><a class="btn-pause" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Paused', 'felan-framework') ?></a></li>

                                                    <?php if ($enable_wallet_mode == '1') {
                                                        if (intval($withdraw_price) > intval($wallet_fee_amount)) { ?>
                                                            <?php if ($project_featured != 1) { ?>
                                                                <li><a class="btn-mark-featured" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a></li>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <?php if ($project_featured !== 1) { ?>
                                                                <li>
                                                                    <a class="btn-add-to-message" href="#"
                                                                        data-text="<?php echo esc_attr('Your wallet balance is currently insufficient.', 'felan-framework'); ?>">
                                                                        <?php esc_html_e('Mark featured', 'felan-framework') ?>
                                                                    </a>
                                                                </li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php if ($project_featured != 1) { ?>
                                                            <li><a class="btn-mark-featured" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a></li>
                                                        <?php } ?>
                                                    <?php } ?>

                                                    <li><a class="btn-mark-filled" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark Filled', 'felan-framework') ?></a></li>
                                                <?php } ?>

                                                <li><a href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('View detail', 'felan-framework') ?></a></li>
                                            <?php }
                                            break;
                                        case 'pending': ?>
                                            <li><a class="btn-edit" href="<?php echo esc_url($project_dashboard_link); ?>?pages=edit&project_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a></li>
                                        <?php
                                            break;
                                        case 'pause':
                                        ?>
                                            <li><a class="btn-edit" href="<?php echo esc_url($project_dashboard_link); ?>?pages=edit&project_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a></li>
                                            <li><a class="btn-show" project-id="<?php echo esc_attr($id); ?>" href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Continue', 'felan-framework'); ?></a>
                                        <?php
                                    } ?>
                                </ul>
                            <?php else : ?>
                                <a href="#" class="icon-setting btn-add-to-message" data-text="<?php echo esc_attr('Project has expired so you can not change it', 'felan-framework'); ?>"><i class="far fa-ellipsis-h"></i></a></a>
                            <?php endif; ?>
                        </td>
                    </tr>

				<?php endwhile;
            }
            wp_reset_postdata();

            $project_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'project_html' => $project_html,
                    'total_post' => $total_post,
                    'withdraw_total_price' => $withdraw_total_price,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

		/**
         * Freelancer freelancer project
         */
        public function felan_filter_employer_proposal()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $project_search = isset($_REQUEST['project_search']) ? felan_clean(wp_unslash($_REQUEST['project_search'])) : '';
            $project_status = isset($_REQUEST['project_status']) ? felan_clean(wp_unslash($_REQUEST['project_status'])) : '';
            $sort_by = isset($_REQUEST['project_sort_by']) ? felan_clean(wp_unslash($_REQUEST['project_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
            $my_project = felan_get_option('felan_my_project_page_id');
            $meta_query = array();
            $tax_query = array();

            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    $item_author_id = get_post_field('post_author', $item_id);
                    if ($user_id == $item_author_id) {
                        wp_delete_post($item_id, true);
                    }
                }
            }

			$args_project = array(
				'post_type'           => 'project',
				'post_status'         => array('publish', 'expired', 'pending', 'pause'),
				'ignore_sticky_posts' => 1,
				'posts_per_page' => -1,
				'author'              => $user_id,
				'orderby'               => 'date',
			);
			$data_project = new WP_Query($args_project);

			$project_ids = [];
			if ($data_project->have_posts()) {
				while ($data_project->have_posts()) {
					$data_project->the_post();
					$project_ids[] = get_the_ID();
				}
				wp_reset_postdata();
			}

            $args = array(
                'post_type' => 'project-proposal',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
            );

            if (!empty($project_search)) {
                $args['s'] = $project_search;
            }

			$meta_query[] = array(
				'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
				'value' => $project_ids,
				'compare' => 'IN'
			);
            if (!empty($project_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'proposal_status',
                    'value' => $project_status,
                    'compare' => '=',
                );
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            if (!empty($sort_by)) {
                if ($sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($sort_by == 'oldest') {
                    $args['order'] = 'ASC';
                }
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                //'add_args'  => array_map( 'urlencode', $args ),
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
					$applicants_id = get_the_ID();
					$project_id = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);

                    $project_featured    = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
					$projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);

					$author_id = get_post_field('post_author', $applicants_id);

					$felan_project_page_id = felan_get_option('felan_projects_page_id');
					$project_dashboard_link    = get_page_link($felan_project_page_id);

					$freelancer_id = '';
					if (!empty($author_id)) {
						$args_freelancer = array(
							'post_type' => 'freelancer',
							'posts_per_page' => 1,
							'author' => $author_id,
							'post_status' => 'any'
						);
						$current_user_posts = get_posts($args_freelancer);
						$freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
						$freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
					}
					$has_freelancer_review = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'has_freelancer_review', true);

					$proposal_status = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_status', true);
					$proposal_price = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_price', true);
					$proposal_time = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_time', true);
					$proposal_fixed_time = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_fixed_time', true);
					$proposal_rate = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_rate', true);
					$proposal_maximum_time = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_maximum_time', true);
					$currency_sign_default = felan_get_option('currency_sign_default');
					$currency_position = felan_get_option('currency_position');
					if ($currency_position == 'before') {
						$proposal_total_price = $currency_sign_default . $proposal_price;
					} else {
						$proposal_total_price = $proposal_price . $currency_sign_default;
					}
                    ?>
                    <tr>
                        <td>
							<div class="info-user">
								<?php if (!empty($freelancer_avatar)) : ?>
									<div class="image-applicants"><img class="image-freelancers" src="<?php echo esc_url($freelancer_avatar) ?>" alt="" /></div>
								<?php else : ?>
									<div class="image-applicants"><i class="far fa-camera"></i></div>
								<?php endif; ?>
								<div class="info-details">
									<h3>
										<?php echo get_the_title($freelancer_id); ?>
									</h3>
									<?php echo felan_get_total_rating('freelancer', $freelancer_id); ?>
								</div>
							</div>
						</td>
						<td>
							<h3 class="title-project-dashboard">
								<a href="<?php echo get_the_permalink($project_id); ?>" target="_blank">
									<?php echo get_the_title($project_id); ?>
									<?php if ($project_featured == '1') : ?>
										<img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
									<?php endif; ?>
								</a>
							</h3>
						</td>
						<td>
							<p>
								<?php if($projects_budget_show == 'hourly') : ?>
									<?php echo sprintf(esc_html__('%1s / in %2s hours', 'felan-framework'),$proposal_total_price, $proposal_time) ?>
								<?php else: ?>
									<?php echo sprintf(esc_html__('%1s / in %2s %3s ', 'felan-framework'),$proposal_total_price, $proposal_fixed_time, $proposal_rate) ?>
								<?php endif; ?>
							</p>
						</td>
						<td>

							<?php echo felan_get_budget_project($project_id); ?>
							<p class="budget-show">
								<?php if($projects_budget_show == 'hourly') : ?>
									<?php echo esc_html__('Hourly Rate', 'felan-framework'); ?>
								<?php else: ?>
									<?php echo esc_html__('Fixed Price', 'felan-framework'); ?>
								<?php endif; ?>
							</p>

						</td>
						<td>
							<?php felan_project_package_status($proposal_status); ?>
						</td>
						<td>
							<div class="button-warpper d-flex justify-content-end">
								<?php if($proposal_status == 'completed') : ?>
									<?php if($has_freelancer_review == '1') : ?>
										<div class="action-review mr-2">
											<a href="#"
												class="btn-action-view felan-button button-outline-gray"
												freelancer-id="<?php echo esc_attr($freelancer_id); ?>"
												style="font-size: 14px;"
											>
												<?php echo esc_html__('Your Review', 'felan-framework'); ?>
											</a>
										</div>
									<?php else: ?>
										<div class="action-review mr-2">
											<a href="#"
												class="btn-action-review btn-review-project felan-button button-outline-gray"
												freelancer-id="<?php echo esc_attr($freelancer_id); ?>"
												order-id="<?php echo esc_attr($applicants_id); ?>"
												style="font-size: 14px;"
											>
												<?php echo esc_html__('Review', 'felan-framework'); ?>
											</a>
										</div>
									<?php endif; ?>
								<?php endif; ?>
								<a href="<?php echo esc_url($project_dashboard_link); ?>?applicants_id=<?php echo esc_attr($applicants_id); ?>&project_id=<?php echo esc_attr($project_id); ?>"
									class="felan-button"
									style="font-size: 14px;"
								>
									<?php echo esc_html__('Detail','felan-framework') ?>
								</a>
							</div>
						</td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $project_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'project_html' => $project_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * @param $project_id
         * @param $rating_value
         * @param bool|true $comment_exist
         * @param int $old_rating_value
         */
        public function rating_meta_filter($project_id, $rating_value, $comment_exist = true, $old_rating_value = 0)
        {
            update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_rating', $rating_value);
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
            $project_id = isset($_POST['project_id']) ? felan_clean(wp_unslash($_POST['project_id'])) : '';
            $comment_approved = 1;
            $auto_publish_review_project = get_option('comment_moderation');
            if ($auto_publish_review_project == 1) {
                $comment_approved = 0;
            }
            $data = array();
            $user = $user->data;

            $data['comment_post_ID']      = $project_id;
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
        public function project_submit_ajax()
        {
            $project_form               = isset($_REQUEST['project_form']) ? felan_clean(wp_unslash($_REQUEST['project_form'])) : '';
            $project_id                 = isset($_REQUEST['project_id']) ? felan_clean(wp_unslash($_REQUEST['project_id'])) : '';
            $project_title              = isset($_REQUEST['project_title']) ? felan_clean(wp_unslash($_REQUEST['project_title'])) : '';
            $project_categories         = isset($_REQUEST['project_categories']) ? felan_clean(wp_unslash($_REQUEST['project_categories'])) : '';
            $project_skills         = isset($_REQUEST['project_skills']) ? felan_clean(wp_unslash($_REQUEST['project_skills'])) : '';
            $project_des        = isset($_REQUEST['project_des']) ? wp_kses_post(wp_unslash($_REQUEST['project_des'])) : '';
            $project_language      = isset($_REQUEST['project_language']) ? felan_clean(wp_unslash($_REQUEST['project_language'])) : '';
            $project_career      = isset($_REQUEST['project_career']) ? felan_clean(wp_unslash($_REQUEST['project_career'])) : '';

            $project_thumbnail_url = isset($_REQUEST['project_thumbnail_url']) ? felan_clean(wp_unslash($_REQUEST['project_thumbnail_url'])) : '';
            $project_thumbnail_id  = isset($_REQUEST['project_thumbnail_id']) ? felan_clean(wp_unslash($_REQUEST['project_thumbnail_id'])) : '';
            $felan_gallery_ids          = isset($_REQUEST['felan_gallery_ids']) ? felan_clean(wp_unslash($_REQUEST['felan_gallery_ids'])) : '';
            $project_video_url      = isset($_REQUEST['project_video_url']) ? felan_clean(wp_unslash($_REQUEST['project_video_url'])) : '';
            $project_map_location       = isset($_REQUEST['project_map_location']) ? felan_clean(wp_unslash($_REQUEST['project_map_location'])) : '';
            $project_map_address        = isset($_REQUEST['project_map_address']) ? felan_clean(wp_unslash($_REQUEST['project_map_address'])) : '';
            $project_location       = isset($_REQUEST['project_location']) ? felan_clean(wp_unslash($_REQUEST['project_location'])) : '';
            $project_latitude      = isset($_REQUEST['project_latitude']) ? felan_clean(wp_unslash($_REQUEST['project_latitude'])) : '';
            $project_longtitude       = isset($_REQUEST['project_longtitude']) ? felan_clean(wp_unslash($_REQUEST['project_longtitude'])) : '';

            $project_budget_show        = isset($_REQUEST['project_budget_show']) ? felan_clean(wp_unslash($_REQUEST['project_budget_show'])) : '';
            $project_budget_minimum      = isset($_REQUEST['project_budget_minimum']) ? felan_clean(wp_unslash($_REQUEST['project_budget_minimum'])) : '';
            $project_budget_maximum       = isset($_REQUEST['project_budget_maximum']) ? felan_clean(wp_unslash($_REQUEST['project_budget_maximum'])) : '';
            $project_value_rate       = isset($_REQUEST['project_value_rate']) ? felan_clean(wp_unslash($_REQUEST['project_value_rate'])) : '';
            $project_budget_rate       = isset($_REQUEST['project_budget_rate']) ? felan_clean(wp_unslash($_REQUEST['project_budget_rate'])) : '';
            $project_price_per_hours     = isset($_REQUEST['project_price_per_hours']) ? felan_clean(wp_unslash($_REQUEST['project_price_per_hours'])) : '';
            $project_maximum_hours        = isset($_REQUEST['project_maximum_hours']) ? felan_clean(wp_unslash($_REQUEST['project_maximum_hours'])) : '';
            $project_select_company       = isset($_REQUEST['project_select_company']) ? felan_clean(wp_unslash($_REQUEST['project_select_company'])) : '';

            $project_faq_title      = isset($_REQUEST['project_faq_title']) ? felan_clean(wp_unslash($_REQUEST['project_faq_title'])) : '';
            $project_faq_description       = isset($_REQUEST['project_faq_description']) ? felan_clean(wp_unslash($_REQUEST['project_faq_description'])) : '';

            $custom_field_project        = isset($_REQUEST['custom_field_project']) ? felan_clean(wp_unslash($_REQUEST['custom_field_project'])) : '';

            $company_title = isset($_REQUEST['company_title']) ? felan_clean(wp_unslash($_REQUEST['company_title'])) : '';
            $company_email = isset($_REQUEST['company_email']) ? felan_clean(wp_unslash($_REQUEST['company_email'])) : '';
            $company_avatar_url = isset($_REQUEST['company_avatar_url']) ? felan_clean(wp_unslash($_REQUEST['company_avatar_url'])) : '';
            $company_avatar_id = isset($_REQUEST['company_avatar_id']) ? felan_clean(wp_unslash($_REQUEST['company_avatar_id'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $new_project = array();
            $new_project['post_type'] = 'project';
            $new_project['post_author'] = $user_id;

            if (isset($project_title)) {
                $new_project['post_title'] = $project_title;
            }

            if (isset($project_url)) {
                $new_project['post_name'] = $project_url;
            }

            if (isset($project_des)) {
                $new_project['post_content'] = $project_des;
            }

            $submit_action = $project_form;
            $auto_publish         = felan_get_option('project_auto_publish', 1);
            $auto_publish_edited  = felan_get_option('project_auto_publish_edited', 1);
            $paid_submission_type = felan_get_option('freelancer_paid_submission_type', 'no');
            $enable_freelancer_project_fee = felan_get_option('enable_freelancer_project_fee');
            $employer_number_project_fee = felan_get_option('employer_number_project_fee');

            if ($submit_action == 'submit-project') {
                $project_id = 0;
                if ($auto_publish == 1) {
                    $new_project['post_status'] = 'publish';
                } else {
                    $new_project['post_status'] = 'pending';
                }
                if (!empty($new_project['post_title'])) {
                    $project_id = wp_insert_post($new_project, true);
                }
                if ($project_id > 0) {
                    $freelancer_package_key = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_key', $user_id);
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'freelancer_package_key', $freelancer_package_key);
                    $package_number_project = intval(get_the_author_meta(FELAN_METABOX_PREFIX . 'package_number_project', $user_id));
                    if ($package_number_project - 1 >= 0) {
                        update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_project', $package_number_project - 1);
                    }
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'enable_freelancer_package_expires', 0);
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', 0);
                    update_post_meta($project_id, 'total_point_review', 0);

                    felan_withdraw_total_price_ajax('posting_project', $user_id, 'employer');
                }
                echo json_encode(array('success' => true));
            } elseif ($submit_action == 'edit-project') {
                $new_project['ID'] = intval($project_id);
                if ($auto_publish_edited == 1) {
                    $new_project['post_status'] = 'publish';
                } else {
                    $new_project['post_status'] = 'pending';
                }

                $project_id = wp_update_post($new_project);
                echo json_encode(array('success' => true));
            }

            if ($project_id > 0) {
                if (!empty($project_categories)) {
                    $project_categories = array_map('intval', $project_categories);
                    wp_set_object_terms($project_id, $project_categories, 'project-categories');
                }

                if (!empty($project_career)) {
                    $project_career = intval($project_career);
                    wp_set_object_terms($project_id, $project_career, 'project-career');
                }

                if (!empty($project_skills)) {
                    $project_skills = array_map('intval', $project_skills);
                    wp_set_object_terms($project_id, $project_skills, 'project-skills');
                }

                if (!empty($project_language)) {
                    $project_language = array_map('intval', $project_language);
                    wp_set_object_terms($project_id, $project_language, 'project-language');
                }

                if (!empty($project_location)) {
                    $project_location = intval($project_location);
                    wp_set_object_terms($project_id, $project_location, 'felan_location');
                }

                if (isset($project_video_url)) {
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_video_url', $project_video_url);
                }

                if (isset($project_map_address)) {
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_address', $project_map_address);
                }

                if (isset($project_map_location)) {
                    $lat_lng = $project_map_location;
                    $address = $project_map_address;
                    $arr_location = array(
                        'location' => $lat_lng,
                        'address' => $address,
                    );
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_location', $arr_location);
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_address', $project_map_address);
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_latitude', $project_latitude);
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_longtitude', $project_longtitude);
                }

                if (isset($project_budget_show)) {
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', $project_budget_show);
                }

                if (isset($project_budget_minimum)) {
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_minimum', $project_budget_minimum);
                }

                if (isset($project_budget_maximum)) {
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_maximum', $project_budget_maximum);
                }

                if (isset($project_value_rate)) {
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_value_rate', $project_value_rate);
                }

                if (isset($project_budget_rate)) {
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_rate', $project_budget_rate);
                }

                if (isset($project_price_per_hours)) {
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_price_per_hours', $project_price_per_hours);
                }

                if (isset($project_maximum_hours)) {
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_maximum_hours', $project_maximum_hours);
                }

                //Add Company
                if (!empty($project_select_company)) {
                    if ($project_select_company == 'new_company') {
                        if (!empty($company_title)) {
                            $new_company = array(
                                'post_type' => 'company',
                                'post_title' => $company_title,
                                'post_status' => 'publish',
                            );

                            $company_id = wp_insert_post($new_company, true);

                            if (isset($company_email)) {
                                update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_email', $company_email);
                            }

                            if (isset($company_avatar_url) && isset($company_avatar_id)) {
                                $company_avatar = array(
                                    'id'  => $company_avatar_id,
                                    'url' => $company_avatar_url,
                                );
                                update_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo', $company_avatar);
                            }

                            update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_select_company', $company_id);
                        }
                    } else {
                        update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_select_company', $project_select_company);
                    }
                }

                if (!empty($project_faq_title)) {
                    $faq_data  = array();
                    for ($i = 0; $i < count($project_faq_title); $i++) {
                        $faq_data[] = array(
                            FELAN_METABOX_PREFIX . 'project_faq_title'   => $project_faq_title[$i],
                            FELAN_METABOX_PREFIX . 'project_faq_description'    => $project_faq_description[$i],
                        );
                    }
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_tab_faq', $faq_data);
                }

                if (isset($project_thumbnail_url) && isset($project_thumbnail_id)) {
                    $project_thumbnail = array(
                        'id'  => $project_thumbnail_id,
                        'url' => $project_thumbnail_url,
                    );
                    update_post_meta($project_id, '_thumbnail_id', $project_thumbnail_id);
                }

                if (isset($felan_gallery_ids)) {
                    $str_img_ids = '';
                    foreach ($felan_gallery_ids as $project_img_id) {
                        $felan_gallery_ids[] = intval($project_img_id);
                        $str_img_ids .= '|' . intval($project_img_id);
                    }
                    $str_img_ids = substr($str_img_ids, 1);
                    update_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_images', $str_img_ids);
                }

                $get_additional = felan_render_custom_field('project');
                if (count($get_additional) > 0 && !empty($custom_field_project)) {
                    foreach ($get_additional as $key => $field) {
                        if (count($custom_field_project) > 0 && isset($custom_field_project[$field['id']])) {
                            if ($field['type'] == 'checkbox_list') {
                                $arr = array();
                                foreach ($custom_field_project[$field['id']] as $v) {
                                    $arr[] = $v;
                                }
                                update_post_meta($project_id, $field['id'], $arr);
                            } elseif ($field['type'] == 'image') {
                                $custom_field_project_url = wp_get_attachment_url($custom_field_project[$field['id']]);
                                $custom_image = array(
                                    'id'  => $custom_field_project[$field['id']],
                                    'url'  => $custom_field_project_url,
                                );
                                update_post_meta($project_id, $field['id'], $custom_image);
                            } else {
                                update_post_meta($project_id, $field['id'], $custom_field_project[$field['id']]);
                            }
                        }
                    }
                }
            }

            wp_die();
        }
    }
}
