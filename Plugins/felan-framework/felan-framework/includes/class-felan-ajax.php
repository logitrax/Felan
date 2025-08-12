<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Felan_Ajax')) {

    /**
     * Class Felan_Ajax
     */
    class Felan_Ajax
    {
        //Project Edit Proposals
        public function felan_freelancer_edit_proposals()
        {
            $project_id = isset($_REQUEST['project_id']) ? felan_clean(wp_unslash($_REQUEST['project_id'])) : '';
            $proposal_id = isset($_REQUEST['proposal_id']) ? felan_clean(wp_unslash($_REQUEST['proposal_id'])) : '';

            $currency_sign_default = felan_get_option('currency_sign_default');
            $enable_employer_project_fee = felan_get_option('enable_employer_project_fee');
            $employer_number_project_fee = felan_get_option('employer_number_project_fee');
            $proposal_price = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_price', true) ?: '';
            $proposal_time = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_time', true) ?: '';
            $proposal_fixed_time = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_fixed_time', true) ?: '';
            $proposal_rate = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_rate', true) ?: '';
            $proposal_message = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_message', true) ?: '';
            $proposal_total_hous = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_total_hous', true) ?: '';
            $proposal_estimated_hours = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_estimated_hours', true) ?: '';
            $projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);

            global $current_user;
            $user_id = $current_user->ID;
            $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);

            ob_start(); ?>

            <div class="bg-overlay"></div>
            <form class="project-popup inner-popup update-proposal custom-scrollbar">
                <a href="#" class="btn-close"><i class="far fa-times"></i></a>
                <h5>
                    <?php esc_html_e('Update your proposal', 'felan-framework'); ?>
                </h5>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="proposal_price"><?php esc_html_e('Your budget working rate', 'felan-framework'); ?><sup> *</sup></label>
                        <input type="number" id="proposal_price" value="<?php echo esc_attr($proposal_price); ?>" name="proposal_price" placeholder="<?php echo esc_attr('0.00', 'felan-framework') ?>" required>
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
                            <input type="number" id="proposal_time" value="<?php echo esc_attr($proposal_estimated_hours); ?>" name="proposal_time" placeholder="<?php echo esc_attr('1', 'felan-framework') ?>" required>
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
                            <input type="number" id="proposal_fixed_time" value="<?php echo esc_attr($proposal_fixed_time); ?>" name="proposal_fixed_time" placeholder="<?php echo esc_attr('1', 'felan-framework') ?>" required>
                        </div>
                    </div>
                    <div class="form-group col-md-6" id="proposal_rate" style="padding-left: 10px;">
                        <label><?php esc_html_e('Rate', 'felan-framework'); ?></label>
                        <div class="select2-field">
                            <select name="proposal_rate" class="felan-select2">
                                <option <?php if ($proposal_rate == "hour") {
                                            echo 'selected';
                                        } ?> value="hour"><?php esc_html_e('Per Hour', 'felan-framework'); ?></option>
                                <option <?php if ($proposal_rate == "day") {
                                            echo 'selected';
                                        } ?> value="day"><?php esc_html_e('Per Day', 'felan-framework'); ?></option>
                                <option <?php if ($proposal_rate == "week") {
                                            echo 'selected';
                                        } ?> value="week"><?php esc_html_e('Per Week', 'felan-framework'); ?></option>
                                <option <?php if ($proposal_rate == "month") {
                                            echo 'selected';
                                        } ?> value="month"><?php esc_html_e('Per Month', 'felan-framework'); ?></option>
                                <option <?php if ($proposal_rate == "year") {
                                            echo 'selected';
                                        } ?> value="year"><?php esc_html_e('Per Year', 'felan-framework'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label><?php esc_html_e('Cover letter', 'felan-framework') ?><sup> *</sup></label>
                        <textarea name="content_message" cols="30" rows="7" placeholder="<?php esc_attr_e('Write message here...', 'felan-framework'); ?>"><?php echo esc_attr($proposal_message); ?></textarea>
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
                    <?php if ($user_demo == 'yes') : ?>
                        <a class="btn-add-to-message felan-button" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#">
                            <?php esc_html_e('Update proposal', 'felan-framework') ?>
                        </a>
                    <?php else : ?>
                        <button class="felan-button" id="btn-send-proposal" type="submit">
                            <?php esc_html_e('Update proposal', 'felan-framework'); ?>
                            <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                        </button>
                    <?php endif; ?>
                </div>
                <input type="hidden" id="enable_commission" value="<?php echo esc_attr($enable_employer_project_fee); ?>">
                <input type="hidden" id="commission_fee" value="<?php echo esc_attr($employer_number_project_fee); ?>">
                <input type="hidden" id="project_maximum_time" value="">
                <input type="hidden" id="project_author_id" value="">
                <input type="hidden" id="project_post_current" value="">
                <input type="hidden" id="proposal_id" value="">
            </form>

        <?php $html_form_proposals = ob_get_clean();

            echo json_encode(array(
                'success' => true,
                'budget_show' => $projects_budget_show,
                'html_form_proposals' => $html_form_proposals,
            ));
            wp_die();
        }

        //Project Disputes Message
        function felan_project_disputes_message()
        {
            $message_content = isset($_REQUEST['message_content']) ? felan_clean(wp_unslash($_REQUEST['message_content'])) : '';
            $recipient_id = isset($_REQUEST['recipient_id']) ? felan_clean(wp_unslash($_REQUEST['recipient_id'])) : '';
            $disputes_id = isset($_REQUEST['disputes_id']) ? felan_clean(wp_unslash($_REQUEST['disputes_id'])) : '';
            $attachment_id = isset($_REQUEST['attachment_id']) ? felan_clean(wp_unslash($_REQUEST['attachment_id'])) : '';
            $user_role = isset($_REQUEST['user_role']) ? felan_clean(wp_unslash($_REQUEST['user_role'])) : '';
            $sender_id = get_current_user_id();

            if ($message_content == '') {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Please enter message content',
                ));
                wp_die();
            }

            if (!$sender_id || !$disputes_id) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Invalid message or disputes ID',
                ));
                wp_die();
            }

            $attachment_title = $attachment_url = '';
            if (!empty($attachment_id)) {
                $attachment_title = get_the_title(intval($attachment_id));
                $attachment_url = wp_get_attachment_url(intval($attachment_id));
            }

            if ($user_role == 'employer') {
                $sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_project_messages_employer_' . $disputes_id, true);
            } elseif ($user_role == 'freelancer') {
                $sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_project_messages_freelancer_' . $disputes_id, true);
            }

            if (!$sender_messages) {
                $sender_messages = array();
            }

            $message = array(
                'sender_id' => $sender_id,
                'recipient_id' => $recipient_id,
                'message_content' => $message_content,
                'attachment_title' => $attachment_title,
                'attachment_url' => $attachment_url,
                'date' => date('M d, Y', current_time('timestamp')),
                'time' => current_time('mysql')
            );

            $sender_messages[] = $message;

            if ($user_role == 'employer') {
                $user_freelancer        = get_user_by('id', $recipient_id);
                $user_freelancer_email  = $user_freelancer->user_email;
                $user_freelancer_name   = $user_freelancer->display_name;
                $user_employer          = get_user_by('id', $sender_id);
                $user_employer_name     = $user_employer->display_name;

                $felan_disputes_page_id = felan_get_option('felan_freelancer_disputes_page_id');
                $felan_disputes_page    = get_page_link($felan_disputes_page_id);
                $order_id               = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_order_id', true);
                $project_id             = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);

                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'project_name'    => get_the_title($project_id),
                    'dispute_url'     => $felan_disputes_page . '?listing=project&order_id=' . $order_id . '&disputes_id=' . $disputes_id,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_freelancer_email, 'mail_project_employer_send_message_dispute', $args_mail);
                    felan_get_data_ajax_notification($disputes_id, 'project-dispute-message-employer');
                }

                update_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_project_messages_employer_' . $disputes_id, $sender_messages);
            } elseif ($user_role == 'freelancer') {
                $user_employer        = get_user_by('id', $recipient_id);
                $user_employer_email  = $user_employer->user_email;
                $user_employer_name   = $user_employer->display_name;
                $user_freelancer      = get_user_by('id', $sender_id);
                $user_freelancer_name = $user_freelancer->display_name;

                $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
                $felan_disputes_page    = get_page_link($felan_disputes_page_id);
                $order_id               = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_order_id', true);
                $project_id             = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);

                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'project_name'    => get_the_title($project_id),
                    'dispute_url'     => $felan_disputes_page . '?listing=project&order_id=' . $order_id . '&disputes_id=' . $disputes_id,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_employer_email, 'mail_project_send_message_dispute', $args_mail);
                    felan_get_data_ajax_notification($disputes_id, 'project-dispute-message');
                }

                update_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_project_messages_freelancer_' . $disputes_id, $sender_messages);
            }

            echo json_encode(array(
                'success' => true,
                'message' => 'Message has been sent',
            ));

            wp_die();
        }

        //Project Order Message
        function felan_project_order_message()
        {
            $message_content = isset($_REQUEST['message_content']) ? felan_clean(wp_unslash($_REQUEST['message_content'])) : '';
            $recipient_id = isset($_REQUEST['recipient_id']) ? felan_clean(wp_unslash($_REQUEST['recipient_id'])) : '';
            $order_id = isset($_REQUEST['order_id']) ? felan_clean(wp_unslash($_REQUEST['order_id'])) : '';
            $user_role = isset($_REQUEST['user_role']) ? felan_clean(wp_unslash($_REQUEST['user_role'])) : '';
            $attachment_id = isset($_REQUEST['attachment_id']) ? felan_clean(wp_unslash($_REQUEST['attachment_id'])) : '';
            $sender_id = get_current_user_id();

            if ($message_content == '') {
                echo json_encode(array(
                    'success' => false,
                    's' => $message_content,
                    'message' => 'Please enter message content',
                ));
                wp_die();
            }

            if (!$recipient_id || !$sender_id || !$order_id) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Invalid message or order ID',
                ));
                wp_die();
            }

            $attachment_title = $attachment_url = '';
            if (!empty($attachment_id)) {
                $attachment_title = get_the_title(intval($attachment_id));
                $attachment_url = wp_get_attachment_url(intval($attachment_id));
            }

            if ($user_role == 'employer') {
                $sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'order_project_messages_employer_' . $order_id, true);
            } elseif ($user_role == 'freelancer') {
                $sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'order_project_messages_freelancer_' . $order_id, true);
            }

            if (!$sender_messages) {
                $sender_messages = array();
            }

            $message = array(
                'sender_id' => $sender_id,
                'recipient_id' => $recipient_id,
                'message_content' => $message_content,
                'attachment_title' => $attachment_title,
                'attachment_url' => $attachment_url,
                'date' => date('M d, Y', current_time('timestamp')),
                'time' => current_time('mysql')
            );

            $sender_messages[] = $message;

            if ($user_role == 'employer') {
                $user_freelancer       = get_user_by('id', $recipient_id);
                $user_freelancer_email = $user_freelancer->user_email;
                $user_freelancer_name  = $user_freelancer->display_name;
                $user_employer         = get_user_by('id', $sender_id);
                $user_employer_name    = $user_employer->display_name;

                $felan_project_page_id = felan_get_option('felan_projects_page_id');
                $felan_project_page    = get_page_link($felan_project_page_id);
                $project_id            = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'project_name'    => get_the_title($project_id),
                    'project_url'     => $felan_project_page . '?applicants_id=' . $order_id . '&project_id=' . $project_id,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_freelancer_email, 'mail_employer_send_message_proposal', $args_mail);
                    // felan_get_data_ajax_notification($recipient_message, 'add-proposal');
                }

                update_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'order_project_messages_employer_' . $order_id, $sender_messages);
            } elseif ($user_role == 'freelancer') {
                $user_employer        = get_user_by('id', $recipient_id);
                $user_employer_email  = $user_employer->user_email;
                $user_employer_name   = $user_employer->display_name;
                $user_freelancer      = get_user_by('id', $sender_id);
                $user_freelancer_name = $user_freelancer->display_name;

                $felan_project_page_id = felan_get_option('felan_projects_page_id');
                $felan_project_page    = get_page_link($felan_project_page_id);
                $project_id            = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'project_name'    => get_the_title($project_id),
                    'project_url'     => $felan_project_page . '?applicants_id=' . $order_id . '&project_id=' . $project_id,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_employer_email, 'mail_project_send_message_proposal', $args_mail);
                    // felan_get_data_ajax_notification($recipient_message, 'add-proposal');
                }

                update_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'order_project_messages_freelancer_' . $order_id, $sender_messages);
            }

            echo json_encode(array(
                'success' => true,
                'message' => 'Message has been sent',
                '$attachment_id' => $attachment_id,
            ));

            wp_die();
        }

        //Disputes Message
        function felan_disputes_message()
        {
            $message_content = isset($_REQUEST['message_content']) ? felan_clean(wp_unslash($_REQUEST['message_content'])) : '';
            $recipient_id = isset($_REQUEST['recipient_id']) ? felan_clean(wp_unslash($_REQUEST['recipient_id'])) : '';
            $disputes_id = isset($_REQUEST['disputes_id']) ? felan_clean(wp_unslash($_REQUEST['disputes_id'])) : '';
            $user_role = isset($_REQUEST['user_role']) ? felan_clean(wp_unslash($_REQUEST['user_role'])) : '';
            $sender_id = get_current_user_id();

            if ($message_content == '') {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Please enter message content',
                ));
                wp_die();
            }

            if (!$sender_id || !$disputes_id) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Invalid message or disputes ID',
                ));
                wp_die();
            }

            if ($user_role == 'employer') {
                $sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_messages_employer_' . $disputes_id, true);
            } elseif ($user_role == 'freelancer') {
                $sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_messages_freelancer_' . $disputes_id, true);
            }

            if (!$sender_messages) {
                $sender_messages = array();
            }

            $message = array(
                'sender_id' => $sender_id,
                'recipient_id' => $recipient_id,
                'message_content' => $message_content,
                'date' => date('M d, Y', current_time('timestamp')),
                'time' => current_time('mysql')
            );

            $sender_messages[] = $message;

            if ($user_role == 'employer') {
                $user_freelancer        = get_user_by('id', $recipient_id);
                $user_freelancer_email  = $user_freelancer->user_email;
                $user_freelancer_name   = $user_freelancer->display_name;
                $user_employer          = get_user_by('id', $sender_id);
                $user_employer_name     = $user_employer->display_name;

                $felan_disputes_page_id = felan_get_option('felan_freelancer_disputes_page_id');
                $felan_disputes_page    = get_page_link($felan_disputes_page_id);
                $order_id               = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', true);
                $service_id             = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);

                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'service_name'    => get_the_title($service_id),
                    'dispute_url'     => $felan_disputes_page . '?order_id=' . $order_id . '&disputes_id=' . $disputes_id,
                );

                $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
                if ($enable_post_type_service == '1') {
                    felan_send_email($user_freelancer_email, 'mail_service_employer_send_message_dispute', $args_mail);
                    felan_get_data_ajax_notification($disputes_id, 'service-dispute-message-employer');
                }

                update_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_messages_employer_' . $disputes_id, $sender_messages);
            } elseif ($user_role == 'freelancer') {
                $user_employer        = get_user_by('id', $recipient_id);
                $user_employer_email  = $user_employer->user_email;
                $user_employer_name   = $user_employer->display_name;
                $user_freelancer      = get_user_by('id', $sender_id);
                $user_freelancer_name = $user_freelancer->display_name;

                $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
                $felan_disputes_page    = get_page_link($felan_disputes_page_id);
                $order_id               = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', true);
                $service_id             = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);

                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'service_name'    => get_the_title($service_id),
                    'dispute_url'     => $felan_disputes_page . '?order_id=' . $order_id . '&disputes_id=' . $disputes_id,
                );

                $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
                if ($enable_post_type_service == '1') {
                    felan_send_email($user_employer_email, 'mail_service_send_message_dispute', $args_mail);
                    felan_get_data_ajax_notification($disputes_id, 'service-dispute-message');
                }

                update_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_messages_freelancer_' . $disputes_id, $sender_messages);
            }

            echo json_encode(array(
                'success' => true,
                'message' => 'Message has been sent',
            ));

            wp_die();
        }

        //Service Order Message
        function felan_service_order_message()
        {
            $message_content = isset($_REQUEST['message_content']) ? felan_clean(wp_unslash($_REQUEST['message_content'])) : '';
            $recipient_id = isset($_REQUEST['recipient_id']) ? felan_clean(wp_unslash($_REQUEST['recipient_id'])) : '';
            $order_id = isset($_REQUEST['order_id']) ? felan_clean(wp_unslash($_REQUEST['order_id'])) : '';
            $user_role = isset($_REQUEST['user_role']) ? felan_clean(wp_unslash($_REQUEST['user_role'])) : '';
            $attachment_id = isset($_REQUEST['attachment_id']) ? felan_clean(wp_unslash($_REQUEST['attachment_id'])) : '';
            $sender_id = get_current_user_id();

            if ($message_content == '') {
                echo json_encode(array(
                    'success' => false,
                    's' => $message_content,
                    'message' => 'Please enter message content',
                ));
                wp_die();
            }

            if (!$recipient_id || !$sender_id || !$order_id) {
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Invalid message or order ID',
                ));
                wp_die();
            }

            $attachment_title = $attachment_url = '';
            if (!empty($attachment_id)) {
                $attachment_title = get_the_title(intval($attachment_id));
                $attachment_url = wp_get_attachment_url(intval($attachment_id));
            }

            if ($user_role == 'employer') {
                $sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'order_service_messages_employer_' . $order_id, true);
            } elseif ($user_role == 'freelancer') {
                $sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'order_service_messages_freelancer_' . $order_id, true);
            }

            if (!$sender_messages) {
                $sender_messages = array();
            }

            $message = array(
                'sender_id' => $sender_id,
                'recipient_id' => $recipient_id,
                'message_content' => $message_content,
                'attachment_title' => $attachment_title,
                'attachment_url' => $attachment_url,
                'date' => date('M d, Y', current_time('timestamp')),
                'time' => current_time('mysql')
            );

            $sender_messages[] = $message;

            if ($user_role == 'employer') {
                $user_freelancer        = get_user_by('id', $recipient_id);
                $user_freelancer_email  = $user_freelancer->user_email;
                $user_freelancer_name   = $user_freelancer->display_name;
                $user_employer          = get_user_by('id', $sender_id);
                $user_employer_name     = $user_employer->display_name;

                $felan_service_page_id  = felan_get_option('felan_freelancer_service_page_id');
                $page_link              = get_page_link($felan_service_page_id) . '?order_id=' . $order_id;
                $service_id             = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);

                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'service_name'    => get_the_title($service_id),
                    'message_url'     => $page_link,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_service', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_freelancer_email, 'mail_service_employer_send_message', $args_mail);
                    felan_get_data_ajax_notification($order_id, 'service-message-employer');
                }

                update_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'order_service_messages_employer_' . $order_id, $sender_messages);
            } elseif ($user_role == 'freelancer') {
                $user_employer        = get_user_by('id', $recipient_id);
                $user_employer_email  = $user_employer->user_email;
                $user_employer_name   = $user_employer->display_name;
                $user_freelancer      = get_user_by('id', $sender_id);
                $user_freelancer_name = $user_freelancer->display_name;

                $felan_service_page_id  = felan_get_option('felan_employer_service_page_id');
                $page_link              = get_page_link($felan_service_page_id) . '?order_id=' . $order_id;
                $service_id             = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);

                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'service_name'    => get_the_title($service_id),
                    'message_url'     => $page_link,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_service', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_employer_email, 'mail_service_freelancer_send_message', $args_mail);
                    felan_get_data_ajax_notification($order_id, 'service-message-freelancer');
                }

                update_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'order_service_messages_freelancer_' . $order_id, $sender_messages);
            }

            echo json_encode(array(
                'success' => true,
                'message' => 'Message has been sent',
                '$attachment_id' => $attachment_id,
            ));

            wp_die();
        }

        //Switch Account
        public function felan_switch_account_ajax()
        {
            $new_role = isset($_POST['new_role']) ? sanitize_text_field($_POST['new_role']) : '';

            if (!in_array($new_role, ['felan_user_employer', 'felan_user_freelancer'])) {
                wp_send_json_error(['message' => 'Invalid role']);
            }

            $user_id = get_current_user_id();
            $user = new WP_User($user_id);

            $roles = ['felan_user_employer', 'felan_user_freelancer'];
            foreach ($roles as $role) {
                if (in_array($role, $user->roles)) {
                    $user->remove_role($role);
                }
            }
            $user->add_role($new_role);

            //Add Freelancer
            if ($new_role === 'felan_user_freelancer') {
                $args = [
                    'post_type'   => 'freelancer',
                    'post_status' => 'any',
                    'author'      => $user_id,
                    'fields'      => 'ids',
                    'posts_per_page' => 1,
                ];

                $existing_freelancer_posts = get_posts($args);

                if (empty($existing_freelancer_posts)) {
                    $type_name_freelancer = felan_get_option('type_name_freelancer');
                    $archive_freelancer_stautus = felan_get_option('archive_freelancer_stautus') ? felan_get_option('archive_freelancer_stautus') : 'pending';
                    $first_name = get_user_meta($user_id, 'first_name', true);
                    $last_name = get_user_meta($user_id, 'last_name', true);
                    $user_email = $user->user_email;
                    $user_login = $user->user_login;

                    $new_profile['post_author'] = $user_id;
                    $new_profile['post_type']   = 'freelancer';
                    $new_profile['post_title']  = $user_login;
                    $new_profile['post_status'] = $archive_freelancer_stautus;

                    if ($type_name_freelancer === 'fl-name') {
                        $new_profile['post_title'] =  $first_name . ' ' . $last_name;
                    } else {
                        $new_profile['post_title']  = sanitize_user($user_login, true);
                    }

                    $new_profile_id = 0;

                    if (!empty($new_profile['post_title'])) {
                        $new_profile_id = wp_insert_post($new_profile, true);
                    }

                    if ($new_profile_id > 0) {
                        $new_profile_first_name = empty($first_name) ? '' : $first_name;
                        $new_profile_last_name  = empty($last_name) ? '' : $last_name;
                        $new_profile_user_email = empty($user_email) ? '' : $user_email;
                        $new_profile_user_phone = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_mobile_number', true);
                        $new_profile_user_phone = !empty($new_profile_user_phone) ? $new_profile_user_phone : '';

                        update_post_meta($new_profile_id, FELAN_METABOX_PREFIX . 'freelancer_user_id', $user_id);
                        update_post_meta($new_profile_id, FELAN_METABOX_PREFIX . 'freelancer_first_name', $new_profile_first_name);
                        update_post_meta($new_profile_id, FELAN_METABOX_PREFIX . 'freelancer_last_name', $new_profile_last_name);
                        update_post_meta($new_profile_id, FELAN_METABOX_PREFIX . 'freelancer_email', $new_profile_user_email);
                        update_post_meta($new_profile_id, FELAN_METABOX_PREFIX . 'freelancer_phone', $new_profile_user_phone);
                        update_post_meta($new_profile_id, FELAN_METABOX_PREFIX . 'freelancer_profile_strength', 10);
                    }

                    if ($new_profile_id > 0) {
                        update_user_meta($user_id, 'felan-cpt_id', $new_profile_id);
                    }
                }
            }

            wp_send_json_success([
                'success' => true,
                'new_role' => $new_role,
            ]);

            wp_die();
        }

        public function felan_canvas_search_ajax()
        {
            $post_type = isset($_REQUEST['post_type']) ? felan_clean(wp_unslash($_REQUEST['post_type'])) : 'jobs';

            $taxonomy_name = 'felan_location';

            ob_start(); ?>

            <div class="select2-field">
                <select name="<?php echo esc_attr($taxonomy_name); ?>" class="felan-select2">
                    <option value=""><?php echo esc_html__('All location', 'felan-framework') ?></option>
                    <?php felan_get_taxonomy($taxonomy_name, true, false); ?>
                </select>
            </div>
            <i class="fas fa-map-marker-alt"></i>

            <?php $taxonomy_html = ob_get_clean();

            echo json_encode(array(
                'success' => true,
                'taxonomy_html' => $taxonomy_html,
            ));
            wp_die();
        }

        public function keyup_site_search()
        {
            $key = sanitize_text_field($_GET['key']);
            $search_by_post_type = isset($_GET['post_type']) ? felan_clean(wp_unslash($_GET['post_type'])) : 'jobs';
            $search_result_per_page = isset($_GET['posts_per_page']) ? felan_clean(wp_unslash($_GET['posts_per_page'])) : Felan_Helper::get_setting("search_result_per_page");

            $args = array(
                's' => $key,
                'post_type' => $search_by_post_type,
                'posts_per_page' => $search_result_per_page,
                'status' => 'publish',
                'ignore_sticky_posts' => 1
            );
            $html = '';
            $the_query = new WP_Query($args);
            if ($the_query->have_posts()) {
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    $html .= '<div class="search-item">';
                    if ($search_by_post_type == 'company') {
                        $company_logo   = get_post_meta(get_the_ID(), FELAN_METABOX_PREFIX . 'company_logo');
                        $html .= '<div class="search-thumbnail company">';
                        $html .= '<a href="' . get_the_permalink() . '">';
                        if (!empty($company_logo[0]['url'])) :
                            $html .= '<img class="logo-comnpany" src="' . $company_logo[0]['url'] . '" alt="" />';
                        else :
                            $html .= '<i class="far fa-camera"></i>';
                        endif;
                        $html .= '</a>';
                        $html .= '</div>';
                    } elseif ($search_by_post_type == 'freelancer') {
                        $author_id = get_post_field('post_author', get_the_ID());
                        $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                        $html .= '<div class="search-thumbnail freelancer">';
                        $html .= '<a href="' . get_the_permalink() . '">';
                        if (!empty($freelancer_avatar)) :
                            $html .= '<img src="' . $freelancer_avatar . '" alt="" />';
                        else :
                            $html .= '<i class="far fa-camera"></i>';
                        endif;
                        $html .= '</a>';
                        $html .= '</div>';
                    } else {
                        if (has_post_thumbnail(get_the_ID())) {
                            $html .= '<div class="search-thumbnail">';
                            $html .= '<a href="' . get_the_permalink() . '">';
                            $html .= get_the_post_thumbnail(get_the_ID());
                            $html .= '</a>';
                            $html .= '</div>';
                        }
                    }
                    $html .= '<h5 class="search-title">';
                    $html .= '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';
                    $html .= '</h5>';
                    $html .= '</div>';
                }
            } else {
                $html .= '<div class="search-item">';
                $html .= __('No result found', 'felan-framework');
                $html .= '</div>';
            }

            wp_reset_query();

            echo json_encode(array(
                'success' => true,
                'content' => $html,
            ));

            wp_die();
        }

        //////////////////////////////////////////////////////////////////
        // Ajax Login
        //////////////////////////////////////////////////////////////////
        public function get_login_user()
        {
            $email       = $_POST['email'];
            $password    = $_POST['password'];
            $reload      = $_POST['reload'];
            $captcha     = $_POST['captcha'];
            $num_captcha = $_POST['num_captcha'];

            $user_login                  = $email;
            $url_redirect                = '';
            $enable_redirect_after_login = Felan_Helper::felan_get_option('enable_redirect_after_login');
            $enable_status               = Felan_Helper::felan_get_option('enable_status_user');
            $enable_captcha              = Felan_Helper::felan_get_option('enable_captcha');
            $redirect_for_admin          = Felan_Helper::felan_get_option('redirect_for_admin');
            $redirect_for_employer       = Felan_Helper::felan_get_option('redirect_for_employer');
            $redirect_for_freelancer     = Felan_Helper::felan_get_option('redirect_for_freelancer');
            $current_page                = get_queried_object();

            if (is_email($email)) {
                if (!(email_exists($email))) {
                    $msg = esc_html__('Username or password is wrong. Please try again', 'felan-framework');
                    echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
                    wp_die();
                }
                $current_user = get_user_by('email', $email);
                $user_login   = $current_user->user_login;

                if (!wp_check_password($password, $current_user->data->user_pass, $current_user->ID)) {
                    $msg = esc_html__('Username or password is wrong. Please try again', 'felan-framework');
                    echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
                    wp_die();
                }
            }

            $array                  = array();
            $array['user_login']    = $user_login;
            $array['user_password'] = $password;
            $array['remember']      = true;
            $user                   = wp_signon($array, false);

            if ($enable_status) {
                $user        = get_user_by('login', $user_login);
                $user_id     = $user->ID;
                $user_status = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_status', $user_id);
                if ($user_status === 'approve') {
                    $msg = esc_html__('Login success', 'felan-framework');
                } else {
                    wp_logout();
                    $msg = esc_html__('Registration complete! Please wait for admin approval to log in.', 'felan-framework');
                    echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
                    wp_die();
                }
            }

            if ($enable_captcha) {
                if (intval($captcha) == intval($num_captcha)) {
                    $msg = esc_html__('Captcha success', 'felan-framework');
                } else {
                    $msg = esc_html__('Captcha failed', 'felan-framework');
                    echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
                    wp_die();
                }
            }

            if (!is_wp_error($user)) {
                $users = get_user_by('login', $user_login);
                if (in_array('felan_user_freelancer', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_freelancer != '') {
                    if ($redirect_for_freelancer == 'reload') {
                        $url_redirect = $reload;
                    } else {
                        $url_redirect = get_page_link($redirect_for_freelancer);
                    }
                } else if (in_array('felan_user_employer', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_employer != '') {
                    if ($redirect_for_employer == 'reload') {
                        $url_redirect = $reload;
                    } else {
                        $url_redirect = get_page_link($redirect_for_employer);
                    }
                } else if (in_array('administrator', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_admin != '') {
                    if ($redirect_for_admin == 'reload') {
                        $url_redirect = $reload;
                    } else {
                        $url_redirect = get_page_link($redirect_for_admin);
                    }
                }
                $msg = esc_html__('Login success', 'felan-framework');

                echo json_encode(array(
                    'success'      => true,
                    'messages'     => $msg,
                    'class'        => 'text-success',
                    'url_redirect' => $url_redirect
                ));
            } else {
                $msg = esc_html__('Username or password is wrong. Please try again', 'felan-framework');
                echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
            }
            wp_die();
        }

        //////////////////////////////////////////////////////////////////
        // Ajax Register
        //////////////////////////////////////////////////////////////////
        public function get_register_user()
        {
            $account_type                = $_POST['account_type'];
            $firstname                   = $_POST['firstname'];
            $lastname                    = $_POST['lastname'];
            $companyname                 = $_POST['companyname'];
            $email                       = $_POST['email'];
            $phone                       = $_POST['phone'];
            $phone_code                  = $_POST['phone_code'];
            $password                    = $_POST['password'];
            $captcha                     = $_POST['captcha'];
            $num_captcha                 = $_POST['num_captcha'];
            $user_login                  = $companyname;
            $url_redirect                = '';
            $enable_redirect_after_login = Felan_Helper::felan_get_option('enable_redirect_after_login');
            $enable_captcha              = Felan_Helper::felan_get_option('enable_captcha');
            $enable_status               = Felan_Helper::felan_get_option('enable_status_user');
            $verify_user_time            = Felan_Helper::felan_get_option('verify_user_time');
            $enable_verify_user          = Felan_Helper::felan_get_option('enable_verify_user');
            //$enable_verify_phone  = Felan_Helper::felan_get_option('enable_verify_phone');
            $redirect_for_admin     = Felan_Helper::felan_get_option('redirect_for_admin');
            $redirect_for_employer  = Felan_Helper::felan_get_option('redirect_for_employer');
            $redirect_for_freelancer = Felan_Helper::felan_get_option('redirect_for_freelancer');
            $type_name_freelancer    = Felan_Helper::felan_get_option('type_name_freelancer');

            $userdata = array(
                'user_login'   => $user_login,
                'first_name'   => $firstname,
                'last_name'    => $lastname,
                'user_email'   => $email,
                'user_phone'   => $phone,
                'user_pass'    => $password,
                'account_type' => $account_type
            );

            if ($type_name_freelancer === 'fl-name') {
                $userdata['display_name'] = $firstname . ' ' . $lastname;
            } else {
                $userdata['display_name'] = $companyname;
            }

            global $wpdb;
            $user = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT user_login, user_email FROM {$wpdb->users} WHERE user_login = %s OR user_email = %s",
                    $user_login,
                    $email
                )
            );
            if ($user) {
                $msg = '';
                if ($user->user_login == $user_login) {
                    $msg = esc_html__('Username already exists', 'felan-framework');
                } else {
                    $msg = esc_html__('Email already exists', 'felan-framework');
                }
                echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
                wp_die();
            } else {
                if ($enable_verify_user) {
                    $code = rand(100000, 999999);
                    update_option($email, $code);
                    $args = array(
                        'code_verify_user' => $code,
                    );
                    felan_send_email($email, 'mail_verify_user', $args);
                    echo json_encode(array(
                        'success'      => true,
                        'verify'       => true,
                        'user_login'   => $user_login,
                        'password'     => $password,
                        'email'        => $email,
                        'userdata'     => $userdata,
                        'class'        => 'text-success',
                        'url_redirect' => $url_redirect
                    ));
                } else {
                    $user_id = wp_insert_user($userdata);
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_mobile_number', $phone);
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'phone_code', $phone_code);
                    if ($user_id == 0) {
                        $user_login = substr($email, 0, strpos($email, '@'));
                        $userdata   = array(
                            'user_login'   => $user_login,
                            'first_name'   => $firstname,
                            'last_name'    => $lastname,
                            'user_email'   => $email,
                            'user_pass'    => $password,
                            'account_type' => $account_type
                        );

                        if ($type_name_freelancer === 'fl-name') {
                            $userdata['display_name'] = $firstname . ' ' . $lastname;
                        } else {
                            $userdata['display_name'] = $companyname;
                        }

                        $user_id = wp_insert_user($userdata);
                    }
                    $msg = '';

                    if ($enable_captcha) {
                        if (intval($captcha) == intval($num_captcha)) {
                            $msg = esc_html__('Captcha success', 'felan-framework');
                        } else {
                            $msg = esc_html__('Captcha failed', 'felan-framework');
                            echo json_encode(array(
                                'success'  => false,
                                'messages' => $msg,
                                'class'    => 'text-error'
                            ));
                            wp_die();
                        }
                    }

                    if (!is_wp_error($user_id)) {
                        if ($account_type == 'felan_user_employer') {
                            $u = new WP_User($user_id);

                            // Remove role
                            if (get_role('felan_user_freelancer')) {
                                $u->remove_role('felan_user_freelancer');
                            }

                            // Add role
                            $u->add_role('felan_user_employer');
                        }

                        $creds                  = array();
                        $creds['user_login']    = $user_login;
                        $creds['user_email']    = $email;
                        $creds['user_password'] = $password;
                        $creds['remember']      = true;
                        $user                   = wp_signon($creds, false);
                        $msg                    = esc_html__('Register success', 'felan-framework');

                        $admin_email = get_option('admin_email');

                        $args = array(
                            'your_name'           => $user_login,
                            'user_login_register' => $email,
                            'user_pass_register'  => $password
                        );

                        if ($enable_status) {
                            $user        = get_user_by('login', $user_login);
                            $user_id     = $user->ID;
                            $user_status = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_status', $user_id);
                            if ($user_status === 'approve') {
                                $msg = esc_html__('Login success', 'felan-framework');
                            } else {
                                wp_logout();
                                $msg = esc_html__('Registration complete! Please wait for admin approval to log in.', 'felan-framework');
                                echo json_encode(array(
                                    'success'  => false,
                                    'messages' => $msg,
                                    'class'    => 'text-error'
                                ));
                                wp_die();
                            }
                        }

                        felan_send_email($email, 'mail_register_user', $args);
                        felan_send_email($admin_email, 'admin_mail_register_user', $args);

                        $users = get_user_by('login', $user_login);
                        if (in_array('felan_user_freelancer', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_freelancer != '') {
                            $url_redirect = get_page_link($redirect_for_freelancer);
                        } else if (in_array('felan_user_employer', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_employer != '') {
                            $url_redirect = get_page_link($redirect_for_employer);
                        } else if (in_array('administrator', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_admin != '') {
                            $url_redirect = get_page_link($redirect_for_admin);
                        }
                        echo json_encode(array(
                            'success'      => true,
                            'messages'     => $msg,
                            'class'        => 'text-success',
                            'url_redirect' => $url_redirect
                        ));
                    } else {
                        $msg = esc_html__('Username/Email address is existing', 'felan-framework');
                        echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
                    }
                }
            }

            wp_die();
        }

        //////////////////////////////////////////////////////////////////
        // Verify User
        //////////////////////////////////////////////////////////////////
        public function verify_code()
        {
            $verify_code       = $_POST['verify_code'];
            $verify_code_phone = $_POST['verify_code_phone'];
            $account_type      = $_POST['account_type'];
            $firstname         = $_POST['firstname'];
            $lastname          = $_POST['lastname'];
            $phone             = $_POST['phone'];
            $phone_code        = $_POST['phone_code'];
            $companyname       = $_POST['companyname'];
            $email             = $_POST['email'];
            $password          = $_POST['password'];
            $user_login        = $companyname;
            $url_redirect      = '';

            $enable_redirect_after_login = Felan_Helper::felan_get_option('enable_redirect_after_login');
            $enable_verify_user          = Felan_Helper::felan_get_option('enable_verify_user');
            //$enable_verify_phone  = Felan_Helper::felan_get_option('enable_verify_phone');
            $enable_status               = Felan_Helper::felan_get_option('enable_status_user');
            $redirect_for_admin     = Felan_Helper::felan_get_option('redirect_for_admin');
            $redirect_for_employer  = Felan_Helper::felan_get_option('redirect_for_employer');
            $redirect_for_freelancer = Felan_Helper::felan_get_option('redirect_for_freelancer');
            $type_name_freelancer    = Felan_Helper::felan_get_option('type_name_freelancer');

            if ($enable_verify_user) {
                if (intval($verify_code) !== intval(get_option($email))) {
                    $msg = esc_html__('The code gmail is incorrect or has expired.', 'felan-framework');
                    echo json_encode(array(
                        'success'      => false,
                        'messages'     => $msg,
                        'class'        => 'text-error',
                        'url_redirect' => $url_redirect
                    ));
                    wp_die();
                }
            }

            $userdata = array(
                'user_login'   => $user_login,
                'first_name'   => $firstname,
                'last_name'    => $lastname,
                'user_email'   => $email,
                'user_pass'    => $password,
                'account_type' => $account_type
            );

            if ($type_name_freelancer === 'fl-name') {
                $userdata['display_name'] = $firstname . ' ' . $lastname;
            } else {
                $userdata['display_name'] = $companyname;
            }

            $user_id = wp_insert_user($userdata);

            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_mobile_number', $phone);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'phone_code', $phone_code);

            if ($user_id == 0) {
                $user_login = substr($email, 0, strpos($email, '@'));
                $userdata   = array(
                    'user_login'   => $user_login,
                    'first_name'   => $firstname,
                    'last_name'    => $lastname,
                    'user_email'   => $email,
                    'user_pass'    => $password,
                    'user_phone'   => $phone,
                    'account_type' => $account_type
                );

                if ($type_name_freelancer === 'fl-name') {
                    $userdata['display_name'] = $firstname . ' ' . $lastname;
                } else {
                    $userdata['display_name'] = $companyname;
                }

                $user_id = wp_insert_user($userdata);
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_mobile_number', $phone);
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'phone_code', $phone_code);
            }

            if ($account_type == 'felan_user_employer') {
                $u = new WP_User($user_id);

                // Remove role
                if (get_role('felan_user_freelancer')) {
                    $u->remove_role('felan_user_freelancer');
                }

                // Add role
                $u->add_role('felan_user_employer');
            }

            $creds                  = array();
            $creds['user_login']    = $user_login;
            $creds['user_email']    = $email;
            $creds['user_password'] = $password;
            $creds['remember']      = true;
            $user                   = wp_signon($creds, false);
            $msg                    = esc_html__('Verify success', 'felan-framework');

            $admin_email = get_option('admin_email');

            $args = array(
                'your_name'           => $user_login,
                'user_login_register' => $email,
                'user_pass_register'  => $password
            );

            felan_send_email($email, 'mail_register_user', $args);
            felan_send_email($admin_email, 'admin_mail_register_user', $args);

            $users = get_user_by('login', $user_login);
            if (in_array('felan_user_freelancer', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_freelancer != '') {
                $url_redirect = get_page_link($redirect_for_freelancer);
            } else if (in_array('felan_user_employer', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_employer != '') {
                $url_redirect = get_page_link($redirect_for_employer);
            } else if (in_array('administrator', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_admin != '') {
                $url_redirect = get_page_link($redirect_for_admin);
            }

            if ($enable_status) {
                $user        = get_user_by('login', $user_login);
                $user_id     = $user->ID;
                $user_status = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_status', $user_id);
                if ($user_status === 'approve') {
                    $msg = esc_html__('Login success', 'felan-framework');
                } else {
                    wp_logout();
                    $msg = esc_html__('Registration complete! Please wait for admin approval to log in.', 'felan-framework');
                    echo json_encode(array(
                        'success'  => false,
                        'messages' => $msg,
                        'class'    => 'text-error'
                    ));
                    wp_die();
                }
            }

            echo json_encode(array(
                'success'      => true,
                'messages'     => $msg,
                'class'        => 'text-success',
                'url_redirect' => $url_redirect
            ));

            delete_option($email);

            wp_die();
        }

        //////////////////////////////////////////////////////////////////
        // Ajax fb login or register
        //////////////////////////////////////////////////////////////////
        public function felan_verify_resend()
        {
            $companyname = $_POST['companyname'];
            $email       = $_POST['email'];
            $phone       = $_POST['phone'];
            $resend      = $_POST['resend'];
            $user_login  = $companyname;

            $verify_user_time   = Felan_Helper::felan_get_option('verify_user_time');
            $enable_verify_user = Felan_Helper::felan_get_option('enable_verify_user');
            //$enable_verify_phone  = Felan_Helper::felan_get_option('enable_verify_phone');

            if ($enable_verify_user && $resend == 'gmail') {
                $code = rand(100000, 999999);
                update_option($email, $code);
                $args = array(
                    'code_verify_user' => $code,
                );
                felan_send_email($email, 'mail_verify_user', $args);
                echo json_encode(array(
                    'success'      => true,
                    'cookie_name'  => str_replace(' ', '_', $cookie_name),
                    'cookie_value' => $cookie_value,
                    'user_login'   => $user_login,
                ));
                wp_die();
            }

            //            if($enable_verify_phone && $resend == 'phone') {
            //                Felan_Helper::felan_get_verify_phone_number($phone);
            //                echo json_encode(array(
            //                    'success' => true,
            //                    'user_login' => $user_login,
            //                ));
            //                wp_die();
            //            }
        }

        //////////////////////////////////////////////////////////////////
        // Ajax fb login or register
        //////////////////////////////////////////////////////////////////
        public function fb_ajax_login_or_register()
        {
            $id                     = $_POST['id'];
            $email                  = $_POST['email'];
            $name                   = $_POST['name'];
            $userdata               = array(
                'user_login'   => $id,
                'user_pass'    => $id,
                'user_email'   => $email,
                'display_name' => $name,
            );
            $felan_dashboard_page_id = Felan_Helper::felan_get_option('felan_freelancer_dashboard_page_id', 0);
            $url_redirect           = get_page_link($felan_dashboard_page_id);

            $user_id = wp_insert_user($userdata);
            if (is_wp_error($user_id)) {
                $creds                  = array();
                $creds['user_login']    = $id;
                $creds['user_password'] = $id;
                $creds['remember']      = true;
                $user                   = wp_signon($creds, false);

                $msg = '';
                if (!is_wp_error($user)) {
                    $msg = esc_html__('Login success', 'felan-framework');
                    echo json_encode(array(
                        'success'      => true,
                        'messages'     => $msg,
                        'class'        => 'text-success',
                        'url_redirect' => $url_redirect
                    ));
                } else {
                    $msg = esc_html__('This email has been used to register', 'felan-framework');
                    echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
                }
                wp_die();
            } else {
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id, true);

                $new_freelancer               = array(
                    'post_type'   => 'freelancer',
                    'post_status' => 'publish',
                );
                $new_freelancer['post_title'] = $name;
                $post_id                     = wp_insert_post($new_freelancer, true);
            }
            echo json_encode(array(
                'success'      => true,
                'class'        => 'text-success',
                'message'      => esc_html__('Login success', 'felan-framework'),
                'url_redirect' => $url_redirect
            ));
            wp_die();
        }

        //////////////////////////////////////////////////////////////////
        // Ajax reset password
        //////////////////////////////////////////////////////////////////
        public function reset_password_ajax()
        {
            if ($_POST['type'] == 'elementor') {
                check_ajax_referer('felan_reset_password_ajax_nonce', 'el_felan_security_reset_password');
            } else {
                check_ajax_referer('felan_reset_password_ajax_nonce', 'felan_security_reset_password');
            }

            $allowed_html = array();
            $user_login   = wp_kses($_POST['user_login'], $allowed_html);

            if (empty($user_login)) {
                echo json_encode(array(
                    'success' => false,
                    'class'   => 'text-warning',
                    'message' => esc_html__('Enter a username or email address.', 'felan-framework')
                ));
                wp_die();
            }

            if (strpos($user_login, '@')) {
                $user_data = get_user_by('email', trim($user_login));
                if (empty($user_data)) {
                    echo json_encode(array(
                        'success' => false,
                        'class'   => 'text-error',
                        'message' => esc_html__('There is no user registered with that email address.', 'felan-framework')
                    ));
                    wp_die();
                }
            } else {
                $login     = trim($user_login);
                $user_data = get_user_by('login', $login);

                if (!$user_data) {
                    echo json_encode(array(
                        'success' => false,
                        'class'   => 'text-error',
                        'message' => esc_html__('Invalid username', 'felan-framework')
                    ));
                    wp_die();
                }
            }
            $user_login = $user_data->user_login;
            $user_email = $user_data->user_email;
            $key        = get_password_reset_key($user_data);

            if (is_wp_error($key)) {
                echo json_encode(array('success' => false, 'message' => $key));
                wp_die();
            }

            $message = esc_html__('Someone has requested a password reset for the following account:', 'felan-framework') . "\r\n\r\n";
            $message .= network_home_url('/') . "\r\n\r\n";
            $message .= sprintf(esc_html__('Username: %s', 'felan-framework'), $user_login) . "\r\n\r\n";
            $message .= esc_html__('If this was a mistake, just ignore this email and nothing will happen.', 'felan-framework') . "\r\n\r\n";
            $message .= esc_html__('To reset your password, visit the following address:', 'felan-framework') . "\r\n\r\n";
            $message .= '<' . get_home_url() . '?action=rp&key=' . $key . '&login=' . rawurlencode($user_login) . ">\r\n";

            if (is_multisite()) {
                $blogname = $GLOBALS['current_site']->site_name;
            } else {
                $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
            }

            $title   = sprintf(esc_html__('[%s] Password Reset', 'felan-framework'), $blogname);
            $title   = apply_filters('retrieve_password_title', $title, $user_login, $user_data);
            $message = apply_filters('retrieve_password_message', $message, $key, $user_login, $user_data);
            if ($message && !wp_mail($user_email, wp_specialchars_decode($title), $message)) {
                echo json_encode(array(
                    'success' => false,
                    'class'   => 'text-error',
                    'message' => esc_html__('The email could not be sent.', 'felan-framework') . "\r\n" . esc_html__('Possible reason: your host may have disabled the mail() function.', 'felan-framework')
                ));
                wp_die();
            } else {
                echo json_encode(array(
                    'success' => true,
                    'class'   => 'text-success',
                    'message' => esc_html__('Please, Check your email to get new password', 'felan-framework')
                ));
                wp_die();
            }
        }

        public function change_password_ajax()
        {
            $new_password = $_POST['new_password'];
            $login        = $_POST['login'];
            $user_data    = get_user_by('login', $login);

            $password = wp_set_password($new_password, $user_data->ID);

            echo json_encode(array(
                'success' => true,
                'class'   => 'text-success',
                'message' => esc_html__('Please, re-login!', 'felan-framework')
            ));

            wp_die();
        }

        //////////////////////////////////////////////////////////////////
        // Ajax fb login or register
        //////////////////////////////////////////////////////////////////
        public function google_ajax_login_or_register()
        {
            $id                     = $_POST['id'];
            $email                  = $_POST['email'];
            $name                   = $_POST['name'];
            $avatar                 = $_POST['avatar'];
            $userdata               = array(
                'user_login'   => $id,
                'user_pass'    => $id,
                'user_email'   => $email,
                'display_name' => $name,
            );
            $felan_dashboard_page_id = Felan_Helper::felan_get_option('felan_freelancer_dashboard_page_id', 0);
            $url_redirect           = get_page_link($felan_dashboard_page_id);

            $user_id = wp_insert_user($userdata);

            if (is_wp_error($user_id)) {
                $creds                  = array();
                $creds['user_login']    = $id;
                $creds['user_password'] = $id;
                $creds['remember']      = true;
                $user                   = wp_signon($creds, false);

                $msg = '';
                if (!is_wp_error($user)) {
                    $msg = esc_html__('Login success', 'felan-framework');
                    echo json_encode(array(
                        'success'      => true,
                        'messages'     => $msg,
                        'class'        => 'text-success',
                        'url_redirect' => $url_redirect
                    ));
                } else {
                    $msg = esc_html__('This email has been used to register', 'felan-framework');
                    echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
                }
                wp_die();
            } else {
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id, true);

                $new_freelancer               = array(
                    'post_type'   => 'freelancer',
                    'post_status' => 'publish',
                );
                $new_freelancer['post_title'] = $name;
                $post_id                     = wp_insert_post($new_freelancer, true);

                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'user-google-email', $email);
            }
            echo json_encode(array(
                'success'      => true,
                'class'        => 'text-success',
                'message'      => esc_html__('Login success', 'felan-framework'),
                'url_redirect' => $url_redirect
            ));
            wp_die();
        }


        //////////////////////////////////////////////////////////////////
        // get script social login
        //////////////////////////////////////////////////////////////////
        public function get_script_social_login()
        {
            // Facebook API
            $enable_social_login = felan_get_option('enable_social_login');
            $facebook_app_id     = felan_get_option('facebook_app_id');
            $fb_script           = '';
            if ($facebook_app_id && $enable_social_login && !is_user_logged_in()) {
                if (is_ssl()) {
                    $fb_script = '<script defer="defer" src="https://connect.facebook.net/' . get_locale() . '/sdk.js#xfbml=1&version=v4.0&appId=' . $facebook_app_id . '&autoLogAppEvents=1" id="facebook-api-js"></script>';
                } else {
                    $fb_script = '<script defer="defer" src="http://connect.facebook.net/' . get_locale() . '/sdk.js#xfbml=1&version=v4.0&appId=' . $facebook_app_id . '&autoLogAppEvents=1" id="facebook-api-js"></script>';
                }
            }

            //Google API
            $google_script = '';
            if ($enable_social_login && !is_user_logged_in()) {
                $google_script = '<script src="https://apis.google.com/js/platform.js?ver=1.0.0" id="google-api-js" gapi_processed="true"></script>';
            }

            //Captcha
            ob_start();
            $captcha        = rand(1000, 9999);
            $enable_captcha = Felan_Helper::felan_get_option('enable_captcha');
            if ($enable_captcha) : ?>
                <input type="text" class="form-control felan-captcha" name="ip_captcha" />
                <input type="hidden" class="form-control felan-num-captcha" name="ip_num_captcha" data-captcha="<?php echo esc_attr($captcha); ?>" />
                <?php felan_image_captcha($captcha); ?>
            <?php endif;
            $html_captcha = ob_get_clean();

            echo json_encode(array(
                'success' => true,
                'google'  => $google_script,
                'fb'      => $fb_script,
                'captcha' => $html_captcha,
            ));

            wp_die();
        }


        /**
         * Preview Job
         */
        public function preview_job()
        {
            ob_start();
            $post_id = isset($_REQUEST['id']) ? felan_clean(wp_unslash($_REQUEST['id'])) : '';
            $post_id = apply_filters('felan_preview_job_id', $post_id);
            $company_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'jobs_select_company');
            $company_id = $company_id[0];
            $enable_social_twitter = felan_get_option('enable_social_twitter', '1');
            $enable_social_linkedin = felan_get_option('enable_social_linkedin', '1');
            $enable_social_facebook = felan_get_option('enable_social_facebook', '1');
            $enable_social_instagram = felan_get_option('enable_social_instagram', '1');
            if ($company_id !== '') {
                $company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
                $company_categories = get_the_terms($company_id, 'company-categories');
                $company_founded = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_founded');
                $company_phone = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_phone');
                $company_email = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_email');
                $company_size = get_the_terms($company_id, 'company-size');
                $company_website = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_website');
                $company_twitter = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_twitter');
                $company_facebook = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_facebook');
                $company_instagram = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_instagram');
                $company_linkedin = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_linkedin');
                $mycompany = get_post($company_id);
                $meta_query = felan_posts_company($company_id);
                $meta_query_post = felan_posts_company($company_id, 5);
                $company_location = get_the_terms($company_id, 'felan_location');
            }
            ?>
            <div id="jobs-<?php echo $post_id; ?>">
                <div class="block-jobs-warrper">
                    <div class="block-archive-top">
                        <?php
                        /**
                         * Hook: felan_preview_jobs_before_summary hook.
                         */
                        do_action('felan_preview_jobs_before_summary', $post_id, '03'); ?>
                        <div class="preview-tabs">
                            <div id="job-detail" class="tab-content is-active">
                                <?php
                                /**
                                 * Hook: felan_preview_jobs_summary hook.
                                 */
                                do_action('felan_preview_jobs_summary', $post_id);
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    /**
                     * Hook: felan_after_content_single_jobs_summary hook.
                     */
                    do_action('felan_after_content_single_jobs_summary', $post_id);
                    ?>
                    <?php
                    /**
                     * Hook: felan_apply_single_jobs hook.
                     */
                    do_action('felan_apply_single_jobs', $post_id);
                    ?>
                </div>
            </div>
            <?php
            $content = ob_get_contents();
            ob_end_clean();
            echo json_encode(array('success' => true, 'job_id' => $job_id, 'content' => $content));
            wp_die();
        }

        /**
         * Jobs Archive
         */
        public function felan_jobs_archive_ajax()
        {
            $title = isset($_REQUEST['title']) ? felan_clean(wp_unslash($_REQUEST['title'])) : '';
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $sort_by = isset($_REQUEST['sort_by']) ? felan_clean(wp_unslash($_REQUEST['sort_by'])) : '';
            $categories = isset($_REQUEST['categories']) ? felan_clean(wp_unslash($_REQUEST['categories'])) : '';
            $types = isset($_REQUEST['types']) ? felan_clean(wp_unslash($_REQUEST['types'])) : '';
            $has_map_val = isset($_REQUEST['has_map_val']) ? felan_clean(wp_unslash($_REQUEST['has_map_val'])) : '';
            $experience = isset($_REQUEST['experience']) ? felan_clean(wp_unslash($_REQUEST['experience'])) : '';
            $career = isset($_REQUEST['career']) ? felan_clean(wp_unslash($_REQUEST['career'])) : '';
            $skills = isset($_REQUEST['skills']) ? felan_clean(wp_unslash($_REQUEST['skills'])) : '';
            $gender = isset($_REQUEST['gender']) ? felan_clean(wp_unslash($_REQUEST['gender'])) : '';
            $qualification = isset($_REQUEST['qualification']) ? felan_clean(wp_unslash($_REQUEST['qualification'])) : '';
            $price_min = isset($_REQUEST['price_min']) ? felan_clean(wp_unslash($_REQUEST['price_min'])) : '';
            $price_max = isset($_REQUEST['price_max']) ? felan_clean(wp_unslash($_REQUEST['price_max'])) : '';
            $current_term = isset($_REQUEST['current_term']) ? felan_clean(wp_unslash($_REQUEST['current_term'])) : '';
            $type_term = isset($_REQUEST['type_term']) ? felan_clean(wp_unslash($_REQUEST['type_term'])) : '';
            $location = isset($_REQUEST['location']) ? felan_clean(wp_unslash($_REQUEST['location'])) : '';
            $location_country = isset($_REQUEST['location_country']) ? felan_clean(wp_unslash($_REQUEST['location_country'])) : '';
            $location_state = isset($_REQUEST['location_state']) ? felan_clean(wp_unslash($_REQUEST['location_state'])) : '';
            $location_city = isset($_REQUEST['location_city']) ? felan_clean(wp_unslash($_REQUEST['location_city'])) : '';
            $radius_cities = isset($_REQUEST['radius_cities']) ? felan_clean(wp_unslash($_REQUEST['radius_cities'])) : '';
            $jobs_layout = isset($_REQUEST['jobs_layout']) ? felan_clean(wp_unslash($_REQUEST['jobs_layout'])) : '';

            $meta_query = array();
            $tax_query = array();

            $args = array(
                'posts_per_page' => ($item_amount > 0) ? $item_amount : -1,
                'post_type' => 'jobs',
                'paged' => $paged,
                'meta_key' => 'felan-jobs_featured',
                'orderby' => 'meta_value date',
            );

            //Jobs expires
            $enable_jobs_show_expires = felan_get_option('enable_jobs_show_expires');
            if ($enable_jobs_show_expires == 1) {
                $args['post_status'] = array('publish', 'expired');
            } else {
                $args['post_status'] = 'publish';
            }

            $meta_query[] = array(
                array(
                    'key' => FELAN_METABOX_PREFIX . 'enable_jobs_package_expires',
                    'value' => 0,
                    'compare' => '=='
                )
            );

            //meta query jobs sort by
            if (!empty($sort_by)) {
                if ($sort_by == 'featured') {
                    $meta_query[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'jobs_featured',
                        'value' => 1,
                        'type' => 'NUMERIC',
                        'compare' => '=',
                    );
                }

                if ($sort_by == 'oldest') {
                    $args['order'] = 'ASC';
                }

                if ($sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
            }

            if (!empty($title)) {
                $args['fields'] = 'ids';
                $args_search = $args_tax = $args;
                $args_search['s'] = $title;
                $data_search = new WP_Query($args_search);
                $args_tax['tax_query'] = array(
                    array(
                        'taxonomy' => 'jobs-skills',
                        'field' => 'name',
                        'terms' => $title,
                    )
                );
                $data_tax = new WP_Query($args_tax);
                $jobs_ids = array_merge($data_tax->posts, $data_search->posts);
                $jobs_ids = array_unique($jobs_ids);
                if (!empty($jobs_ids)) {
                    $args['post__in'] = $jobs_ids;
                } else {
                    $args['s'] = $title;
                }
            }

            //tax query current term
            if (!empty($current_term) && !empty($type_term)) {
                $tax_query[] = array(
                    'taxonomy' => $type_term,
                    'field' => 'id',
                    'terms' => $current_term
                );
            }

            //tax query jobs categories
            if (!empty($categories)) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-categories',
                    'field' => 'term_id',
                    'terms' => $categories
                );
            }

            //location country
            if (!empty($location_country)) {
                $taxonomy_state = get_categories(
                    array(
                        'taxonomy' => 'felan_state',
                        'hide_empty' => false,
                        'parent' => 0,
                        'meta_query' => array(
                            array(
                                'key' => 'felan-state-country',
                                'value' => $location_country,
                                'compare' => '=',
                            )
                        )
                    )
                );

                if (!empty($taxonomy_state)) {
                    $keys_state = array();
                    foreach ($taxonomy_state as $terms_state) {
                        $keys_state[] = $terms_state->term_id;
                    }
                    $taxonomy_city = get_categories(
                        array(
                            'taxonomy' => 'felan_location',
                            'meta_query' => array(
                                array(
                                    'key' => 'felan-location-state',
                                    'value' => $keys_state,
                                    'compare' => 'IN'
                                )
                            )
                        )
                    );
                    $keys_city = array();
                    foreach ($taxonomy_city as $terms_city) {
                        $keys_city[] = $terms_city->term_id;
                    }
                } else {
                    $keys_city = '';
                }
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys_city
                );
            }

            //location state
            if (!empty($location_state)) {
                $taxonomy_terms = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'meta_query' => array(
                            array(
                                'key' => 'felan-location-state',
                                'value' => $location_state,
                                'compare' => '=',
                            )
                        )
                    )
                );
                $keys = array();
                foreach ($taxonomy_terms as $terms) {
                    $keys[] = $terms->term_id;
                }
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys
                );
            }

            //Location city
            if (!empty($location_city)) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $location_city
                );
            }

            //tax query jobs search location
            if (!empty($location) && ($radius_cities == 0 || empty($radius_cities))) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'name',
                    'terms' => $location
                );
            }

            //Nearby cities
            $resultsCities = array();
            if (!empty($radius_cities) && $radius_cities !== 0 && !empty($location)) {
                $taxonomies = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => true,
                        'parent' => 0
                    )
                );

                if (!empty($taxonomies)) {
                    $resultsCities = felan_find_nearby_cities($location, intval($radius_cities));
                    if (empty($resultsCities)) {
                        $resultsCities = $location;
                    }
                    $tax_query[] = array(
                        'taxonomy' => 'felan_location',
                        'field' => 'name',
                        'terms' => $resultsCities,
                    );
                }
            }

            //tax query jobs types
            if (!empty($types)) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-type',
                    'field' => 'term_id',
                    'terms' => $types
                );
            }

            //tax query jobs experience
            if (!empty($experience)) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-experience',
                    'field' => 'term_id',
                    'terms' => $experience
                );
            }

            //tax query jobs career
            if (!empty($career)) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-career',
                    'field' => 'term_id',
                    'terms' => $career
                );
            }

            //tax query jobs skills
            if (!empty($skills)) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-skills',
                    'field' => 'term_id',
                    'terms' => $skills
                );
            }

            //tax query jobs gender
            if (!empty($gender)) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-gender',
                    'field' => 'term_id',
                    'terms' => $gender
                );
            }

            //tax query jobs qualification
            if (!empty($qualification)) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-qualification',
                    'field' => 'term_id',
                    'terms' => $qualification
                );
            }


            //Jobs salary
            if (!empty($price_min) && !empty($price_max)) {
                $meta_query[] = array(
                    'relation' => 'OR',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'jobs_salary_maximum',
                        'value' => array($price_min, $price_max),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'jobs_maximum_price',
                        'value' => array($price_min, $price_max),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'jobs_salary_minimum',
                        'value' => array($price_min, $price_max),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'jobs_minimum_price',
                        'value' => array($price_min, $price_max),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    ),
                );
            }

            $company_id = isset($_REQUEST['company_id']) ? felan_clean(wp_unslash($_REQUEST['company_id'])) : '';
            if ($company_id) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'jobs_select_company',
                    'value' => $company_id,
                    'compare' => '=='
                );
            }

            $custom_fields_value = isset($_REQUEST['custom_fields_value']) ? felan_clean(wp_unslash($_REQUEST['custom_fields_value'])) : [];
            foreach ($custom_fields_value as $custom_field) {
                if ($custom_field) {
                    $meta_query[] = [
                        'relation' => 'OR',
                        [
                            'key'     => array_keys($custom_field)[0],
                            'value'   => array_values($custom_field)[0],
                            'compare' => 'IN'
                        ],
                    ];
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

            $args_map = $args;
            $args_map['paged'] = '';
            $args_map['posts_per_page'] = '-1';

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $jobs_html = $filter_html = '';
            $jobs = array();

            if (!empty($total_post)) {
                if (!empty($title)) {
                    $count_post = sprintf(esc_html__('%1$s jobs for "%2$s"', 'felan-framework'), '<span class="count">' . $total_post . '</span>', $title);
                } else {
                    $count_post = sprintf(_n('%s Jobs', '%s Jobs', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                }
            } else {
                $count_post = esc_html__('0 jobs', 'felan-framework');
            }

            if (!empty($current_term)) {
                $count_post = sprintf(_n('%s Jobs', '%s Jobs', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                if (empty($total_post)) {
                    $count_post = sprintf(__('%s Jobs', 'felan-framework'), $total_post);
                }
            }

            $max_num_pages = $data->max_num_pages;
            $pagination_type = felan_get_option('jobs_pagination_type', 'loadmore');
            if ($pagination_type == 'number') {
                $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                    'total' => $max_num_pages,
                    'current' => $paged,
                    'mid_size' => 1,
                    'type' => 'array',
                    'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                    'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
                )));
            } else {
                $pagination = '<a class="page-numbers next" href="#"><span>' . __('Load More', 'felan-framework') . '</span><span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span></a>';
            }

            $hidden_pagination = '';
            if ($paged == $max_num_pages) {
                $hidden_pagination = 1;
            }

            $enable_jobs_show_map = felan_get_option('enable_jobs_show_map');
            $enable_jobs_show_map = !empty($has_map_val) ? $has_map_val : $enable_jobs_show_map;
            if ($total_post > 0 && $enable_jobs_show_map == 1) {

                $data_map = new WP_Query($args_map);

                while ($data_map->have_posts()) : $data_map->the_post();

                    $jobs_id = get_the_ID();

                    $jobs_meta_data = get_post_custom($jobs_id);

                    $map_zoom_level = felan_get_option('map_zoom_level', '15');

                    $jobs_address = isset($jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_address']) ? $jobs_meta_data[FELAN_METABOX_PREFIX . 'jobs_address'][0] : '';
                    $jobs_location = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_location', true);

                    if (!empty($jobs_location['location'])) {
                        $lat_lng = explode(',', $jobs_location['location']);
                    } else {
                        $lat_lng = array();
                    }

                    $jobs_select_company = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_company');
                    $company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
                    $company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');

                    $marker_icon = '';
                    if (!empty($company_logo[0]['url'])) {
                        $marker_icon = $company_logo[0]['url'];
                    } else {
                        $marker_icon = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                    };

                    $html_jobs = ob_start();
                    felan_get_template('content-jobs.php', array(
                        'jobs_id' => $jobs_id,
                        'jobs_layout' => 'layout-grid',
                        'effect_class' => '',
                    ));
                    $html_jobs = ob_get_clean();

                    $prop = new stdClass();
                    $prop->id = $jobs_id;
                    $prop->lat = isset($lat_lng[0]) ? $lat_lng[0] : 59.325;
                    $prop->lng = isset($lat_lng[1]) ? $lat_lng[1] : 18.070;
                    $prop->jobs = $html_jobs;

                    if (empty($jobs_url)) {
                        $jobs_url = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                        $default_marker = felan_get_option('marker_icon', '');
                        if ($default_marker != '') {
                            if (is_array($default_marker) && $default_marker['url'] != '') {
                                $jobs_url = $default_marker['url'];
                            }
                        }
                    }

                    if ($marker_icon) {
                        $prop->marker_icon = $marker_icon;
                    } else {
                        $prop->marker_icon = $jobs_url;
                    }

                    array_push($jobs, $prop);

                endwhile;
            }
            wp_reset_postdata();

            $jobs_html = ob_start();

            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post();

                    felan_get_template('content-jobs.php', array(
                        'jobs_layout' => $jobs_layout,
                    ));

                endwhile;
            }
            wp_reset_postdata();

            $jobs_html = ob_get_clean();

            $enable_jobs_url_push = felan_get_option('enable_jobs_url_push', '0');
            if (empty($location) && !empty($location_country) && !empty($location_state)) {
                $location = felan_get_term_slug_by_id($location_city);
            }
            $query_var = array(
                'jobs-location' => $location,
                'jobs-categories' => felan_get_term_slug_by_id($categories),
                'jobs-skills' => felan_get_term_slug_by_id($skills),
                'jobs-type' => felan_get_term_slug_by_id($types),
                'jobs-experience' => felan_get_term_slug_by_id($experience),
                'jobs-career' => felan_get_term_slug_by_id($career),
                'jobs-gender' => felan_get_term_slug_by_id($gender),
                'jobs-qualification' => felan_get_term_slug_by_id($qualification),
                'jobs-country' => $location_country,
                'jobs-state' => felan_get_term_slug_by_id($location_state),
                'radius-cities' => $radius_cities,
            );

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'resultsCities' => $resultsCities,
                    'jobs' => $jobs,
                    'count_jobs' => count($jobs),
                    'pagination' => $pagination,
                    'hidden_pagination' => $hidden_pagination,
                    'pagination_type' => $pagination_type,
                    'jobs_html' => $jobs_html,
                    'total_post' => $total_post,
                    'count_post' => $count_post,
                    'query_var' => $query_var,
                    'url_push' => $enable_jobs_url_push,
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'total_post' => $total_post,
                    'count_post' => $count_post,
                    'query_var' => $query_var,
                    'url_push' => $enable_jobs_url_push,
                ));
            }
            wp_die();
        }

        /**
         * Company Archive
         */
        public function felan_company_archive_ajax()
        {
            $title = isset($_REQUEST['title']) ? felan_clean(wp_unslash($_REQUEST['title'])) : '';
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $sort_by = isset($_REQUEST['sort_by']) ? felan_clean(wp_unslash($_REQUEST['sort_by'])) : '';
            $has_map_val = isset($_REQUEST['has_map_val']) ? felan_clean(wp_unslash($_REQUEST['has_map_val'])) : '';
            $current_term = isset($_REQUEST['current_term']) ? felan_clean(wp_unslash($_REQUEST['current_term'])) : '';
            $type_term = isset($_REQUEST['type_term']) ? felan_clean(wp_unslash($_REQUEST['type_term'])) : '';
            $size = isset($_REQUEST['size']) ? felan_clean(wp_unslash($_REQUEST['size'])) : '';
            $rating = isset($_REQUEST['rating']) ? felan_clean(wp_unslash($_REQUEST['rating'])) : '';
            $range_min = isset($_REQUEST['range_min']) ? felan_clean(wp_unslash($_REQUEST['range_min'])) : '';
            $range_max = isset($_REQUEST['range_max']) ? felan_clean(wp_unslash($_REQUEST['range_max'])) : '';
            $founded = isset($_REQUEST['founded']) ? felan_clean(wp_unslash($_REQUEST['founded'])) : '';
            $location = isset($_REQUEST['location']) ? felan_clean(wp_unslash($_REQUEST['location'])) : '';
            $location_country = isset($_REQUEST['location_country']) ? felan_clean(wp_unslash($_REQUEST['location_country'])) : '';
            $location_state = isset($_REQUEST['location_state']) ? felan_clean(wp_unslash($_REQUEST['location_state'])) : '';
            $location_city = isset($_REQUEST['location_city']) ? felan_clean(wp_unslash($_REQUEST['location_city'])) : '';
            $radius_cities = isset($_REQUEST['radius_cities']) ? felan_clean(wp_unslash($_REQUEST['radius_cities'])) : '';
            $categories = isset($_REQUEST['categories']) ? felan_clean(wp_unslash($_REQUEST['categories'])) : '';
            $company_layout = isset($_REQUEST['company_layout']) ? felan_clean(wp_unslash($_REQUEST['company_layout'])) : '';

            $meta_query = array();
            $tax_query = array();

            $args = array(
                'posts_per_page' => ($item_amount > 0) ? $item_amount : -1,
                'post_type' => 'company',
                'paged' => $paged,
                'post_status' => 'publish',
            );

            if (!empty($title)) {
                $args['fields'] = 'ids';
                $args_search = $args_tax = $args;
                $args_search['s'] = $title;
                $data_search = new WP_Query($args_search);
                $args_tax['tax_query'] = array(
                    array(
                        'taxonomy' => 'company-categories',
                        'field' => 'name',
                        'terms' => $title,
                    )
                );
                $data_tax = new WP_Query($args_tax);
                $company_ids = array_merge($data_tax->posts, $data_search->posts);
                $company_ids = array_unique($company_ids);
                if (!empty($company_ids)) {
                    $args['post__in'] = $company_ids;
                } else {
                    $args['s'] = $title;
                }
            }

            //tax query current term
            if (!empty($current_term) && !empty($type_term)) {
                $tax_query[] = array(
                    'taxonomy' => $type_term,
                    'field' => 'id',
                    'terms' => $current_term
                );
            }

            //tax query company size
            if (!empty($size)) {
                $tax_query[] = array(
                    'taxonomy' => 'company-size',
                    'field' => 'term_id',
                    'terms' => $size
                );
            }

            //location country
            if (!empty($location_country)) {
                $taxonomy_state = get_categories(
                    array(
                        'taxonomy' => 'felan_state',
                        'hide_empty' => false,
                        'parent' => 0,
                        'meta_query' => array(
                            array(
                                'key' => 'felan-state-country',
                                'value' => $location_country,
                                'compare' => '=',
                            )
                        )
                    )
                );

                if (!empty($taxonomy_state)) {
                    $keys_state = array();
                    foreach ($taxonomy_state as $terms_state) {
                        $keys_state[] = $terms_state->term_id;
                    }
                    $taxonomy_city = get_categories(
                        array(
                            'taxonomy' => 'felan_location',
                            'meta_query' => array(
                                array(
                                    'key' => 'felan-location-state',
                                    'value' => $keys_state,
                                    'compare' => 'IN'
                                )
                            )
                        )
                    );
                    $keys_city = array();
                    foreach ($taxonomy_city as $terms_city) {
                        $keys_city[] = $terms_city->term_id;
                    }
                } else {
                    $keys_city = '';
                }

                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys_city
                );
            }

            //location state
            if (!empty($location_state)) {
                $taxonomy_terms = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'meta_query' => array(
                            array(
                                'key' => 'felan-location-state',
                                'value' => $location_state,
                                'compare' => '=',
                            )
                        )
                    )
                );
                $keys = array();
                foreach ($taxonomy_terms as $terms) {
                    $keys[] = $terms->term_id;
                }
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys
                );
            }

            //location city
            if (!empty($location_city)) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $location_city
                );
            }

            //tax query company search location
            if (!empty($location) && ($radius_cities == 0 || empty($radius_cities))) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'name',
                    'terms' => $location
                );
            }

            //Nearby cities
            $resultsCities = array();
            if (!empty($radius_cities) && $radius_cities !== 0 && !empty($location)) {
                $taxonomies = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => true,
                        'parent' => 0
                    )
                );

                if (!empty($taxonomies)) {
                    $resultsCities = felan_find_nearby_cities($location, intval($radius_cities));
                    if (empty($resultsCities)) {
                        $resultsCities = $location;
                    }
                    $tax_query[] = array(
                        'taxonomy' => 'felan_location',
                        'field' => 'name',
                        'terms' => $resultsCities,
                    );
                }
            }

            //tax query company size
            if (!empty($categories)) {
                $tax_query[] = array(
                    'taxonomy' => 'company-categories',
                    'field' => 'term_id',
                    'terms' => $categories
                );
            }

            //rating
            $rating_one = $rating_two = $rating_three = $rating_four = $rating_five = '';
            if (!empty($rating)) {
                if ((is_array($rating) && in_array('rating_five', $rating)) || $rating == 'rating_five') {
                    $rating_five = array(
                        'key' => FELAN_METABOX_PREFIX . 'company_rating',
                        'value' => 5,
                        'type' => 'NUMERIC',
                        'compare' => '==',
                    );
                }

                if ((is_array($rating) && in_array('rating_four', $rating)) || $rating == 'rating_four') {
                    $rating_four = array(
                        'key' => FELAN_METABOX_PREFIX . 'company_rating',
                        'value' => array(4, 4.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_three', $rating)) || $rating == 'rating_three') {
                    $rating_three = array(
                        'key' => FELAN_METABOX_PREFIX . 'company_rating',
                        'value' => array(3, 3.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_two', $rating)) || $rating == 'rating_two') {
                    $rating_two = array(
                        'key' => FELAN_METABOX_PREFIX . 'company_rating',
                        'value' => array(2, 2.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_one', $rating)) || $rating == 'rating_one') {
                    $rating_one = array(
                        'key' => FELAN_METABOX_PREFIX . 'company_rating',
                        'value' => array(1, 1.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                $meta_query[] = array(
                    'relation' => 'OR',
                    $rating_five,
                    $rating_four,
                    $rating_three,
                    $rating_two,
                    $rating_one
                );
            }

            //founded
            if (!empty($range_min) && !empty($range_max)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'company_founded',
                    'value' => array($range_min, $range_max),
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            }

            if (!empty($founded)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'company_founded',
                    'value' => $founded,
                    'compare' => '=',
                );
            }

            //meta query company sort by
            if (!empty($sort_by)) {
                if ($sort_by == 'newest') {
                    $args['orderby'] = array(
                        'menu_order' => 'ASC',
                        'date' => 'DESC',
                    );
                }

                if ($sort_by == 'oldest') {
                    $args['orderby'] = array(
                        'menu_order' => 'DESC',
                        'date' => 'ASC',
                    );
                }

                if ($sort_by == 'rating') {
                    $args['meta_key'] = FELAN_METABOX_PREFIX . 'company_rating';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                }
            }

            $custom_fields_value = isset($_REQUEST['custom_fields_value']) ? felan_clean(wp_unslash($_REQUEST['custom_fields_value'])) : '';
            foreach ($custom_fields_value as $custom_field) {
                if ($custom_field) {
                    $meta_query[] = [
                        'relation' => 'OR',
                        [
                            'key'     => array_keys($custom_field)[0],
                            'value'   => array_values($custom_field)[0],
                            'compare' => 'IN'
                        ],
                    ];
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

            $args_map = $args;
            $args_map['paged'] = '';
            $args_map['posts_per_page'] = '-1';

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $company_html = '';
            $company = array();

            if (!empty($total_post)) {
                if (!empty($title)) {
                    $count_post = sprintf(esc_html__('%1$s companies for "%2$s"', 'felan-framework'), '<span class="count">' . $total_post . '</span>', $title);
                } else {
                    $count_post = sprintf(_n('%s companies', '%s Companies', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                }
            } else {
                $count_post = esc_html__('0 company', 'felan-framework');
            }

            if (!empty($current_term)) {
                $count_post = sprintf(_n('%s Companies', '%s Companies', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                if (empty($total_post)) {
                    $count_post = sprintf(__('%s Companies', 'felan-framework'), $total_post);
                }
            }

            $pagination_type = felan_get_option('company_pagination_type', 'loadmore');
            if ($pagination_type == 'number') {
                $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                    'total' => $max_num_pages,
                    'current' => $paged,
                    'mid_size' => 1,
                    'type' => 'array',
                    'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                    'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
                )));
            } else {
                $pagination = '<a class="page-numbers next" href="#"><span>' . __('Load More', 'felan-framework') . '</span><span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span></a>';
            }

            $hidden_pagination = '';
            if ($paged == $max_num_pages) {
                $hidden_pagination = 1;
            }

            $enable_company_show_map = felan_get_option('enable_company_show_map');
            $enable_company_show_map = !empty($has_map_val) ? $has_map_val : $enable_company_show_map;
            if ($total_post > 0 && $enable_company_show_map == 1) {

                $data_map = new WP_Query($args_map);

                while ($data_map->have_posts()) : $data_map->the_post();

                    $company_id = get_the_ID();

                    $company_meta_data = get_post_custom($company_id);

                    $map_zoom_level = felan_get_option('map_zoom_level', '15');

                    $company_address = isset($company_meta_data[FELAN_METABOX_PREFIX . 'company_address']) ? $company_meta_data[FELAN_METABOX_PREFIX . 'company_address'][0] : '';
                    $company_location = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'felan_location', true);

                    if (!empty($company_location['location'])) {
                        $lat_lng = explode(',', $company_location['location']);
                    } else {
                        $lat_lng = array();
                    }

                    $company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
                    if (!empty($company_logo[0]['url'])) {
                        $marker_icon = $company_logo[0]['url'];
                    } else {
                        $marker_icon = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                    };

                    $html_company = ob_start();
                    felan_get_template('content-company.php', array(
                        'company_id' => $company_id,
                        'company_layout' => 'layout-grid',
                        'effect_class' => '',
                    ));
                    $html_company = ob_get_clean();

                    $prop = new stdClass();
                    $prop->id = $company_id;
                    $prop->lat = isset($lat_lng[0]) ? $lat_lng[0] : 59.325;
                    $prop->lng = isset($lat_lng[1]) ? $lat_lng[1] : 18.070;
                    $prop->company = $html_company;

                    if (empty($company_url)) {
                        $company_url = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                        $default_marker = felan_get_option('marker_icon', '');
                        if ($default_marker != '') {
                            if (is_array($default_marker) && $default_marker['url'] != '') {
                                $company_url = $default_marker['url'];
                            }
                        }
                    }

                    if ($marker_icon) {
                        $prop->marker_icon = $marker_icon;
                    } else {
                        $prop->marker_icon = $company_url;
                    }

                    array_push($company, $prop);

                endwhile;
            }
            wp_reset_postdata();

            ob_start();

            $count_data = 1;
            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post();
                    felan_get_template('content-company.php', array(
                        'company_layout' => $company_layout,
                    ));
                endwhile;
            }
            wp_reset_postdata();

            $enable_company_url_push = felan_get_option('enable_company_url_push', '0');
            if (empty($location) && !empty($location_country) && !empty($location_state)) {
                $location = felan_get_term_slug_by_id($location_city);
            }

            $query_var = array(
                'company-location' => $location,
                'company-categories' => felan_get_term_slug_by_id($categories),
                'company-founded' => $founded,
                'company-size' => felan_get_term_slug_by_id($size),
                'company-rating' => $rating,
                'range-min' => $range_min,
                'range-max' => $range_max,
                'company-country' => $location_country,
                'company-state' => felan_get_term_slug_by_id($location_state),
                'radius-cities' => $radius_cities,
            );

            $company_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'resultsCities' => $resultsCities,
                    'company' => $company,
                    'count_company' => $count_data,
                    'pagination' => $pagination,
                    'hidden_pagination' => $hidden_pagination,
                    'pagination_type' => $pagination_type,
                    'company_html' => $company_html,
                    'total_post' => $total_post,
                    'count_post' => $count_post,
                    'query_var' => $query_var,
                    'url_push' => $enable_company_url_push,
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'total_post' => $total_post,
                    'count_post' => $count_post,
                    'query_var' => $query_var,
                    'url_push' => $enable_company_url_push,
                ));
            }

            wp_die();
        }

        /**
         *  Freelancer Archive
         */
        public function felan_freelancer_archive_ajax()
        {
            $title = isset($_REQUEST['title']) ? felan_clean(wp_unslash($_REQUEST['title'])) : '';
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $sort_by = isset($_REQUEST['sort_by']) ? felan_clean(wp_unslash($_REQUEST['sort_by'])) : '';
            $current_term = isset($_REQUEST['current_term']) ? felan_clean(wp_unslash($_REQUEST['current_term'])) : '';
            $type_term = isset($_REQUEST['type_term']) ? felan_clean(wp_unslash($_REQUEST['type_term'])) : '';
            $has_map_val = isset($_REQUEST['has_map_val']) ? felan_clean(wp_unslash($_REQUEST['has_map_val'])) : '';
            $rating = isset($_REQUEST['rating']) ? felan_clean(wp_unslash($_REQUEST['rating'])) : '';
            $location = isset($_REQUEST['location']) ? felan_clean(wp_unslash($_REQUEST['location'])) : '';
            $location_country = isset($_REQUEST['location_country']) ? felan_clean(wp_unslash($_REQUEST['location_country'])) : '';
            $location_state = isset($_REQUEST['location_state']) ? felan_clean(wp_unslash($_REQUEST['location_state'])) : '';
            $location_city = isset($_REQUEST['location_city']) ? felan_clean(wp_unslash($_REQUEST['location_city'])) : '';
            $radius_cities = isset($_REQUEST['radius_cities']) ? felan_clean(wp_unslash($_REQUEST['radius_cities'])) : '';
            $categories = isset($_REQUEST['categories']) ? felan_clean(wp_unslash($_REQUEST['categories'])) : '';
            $yoe_ids = isset($_REQUEST['freelancer_yoe_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_yoe_id'])) : array();
            $qualification_ids = isset($_REQUEST['freelancer_qualification_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_qualification_id'])) : array();
            $ages_ids = isset($_REQUEST['freelancer_ages_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_ages_id'])) : array();
            $skills_ids = isset($_REQUEST['freelancer_skills_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_skills_id'])) : array();
            $languages_ids = isset($_REQUEST['freelancer_languages_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_languages_id'])) : array();
            $gender_ids = isset($_REQUEST['freelancer_gender']) ? felan_clean(wp_unslash($_REQUEST['freelancer_gender'])) : array();
            $freelancer_layout = isset($_REQUEST['freelancer_layout']) ? felan_clean(wp_unslash($_REQUEST['freelancer_layout'])) : '';

            $meta_query = array();
            $tax_query = array();

            $args = array(
                'posts_per_page' => ($item_amount > 0) ? $item_amount : -1,
                'post_type' => 'freelancer',
                'paged' => $paged,
                'post_status' => 'publish',
                'meta_key' => 'felan-freelancer_featured',
                'orderby' => 'meta_value date',
            );

            if (!empty($title)) {
                $args['fields'] = 'ids';
                $args_search = $args_tax = $args;
                $args_search['s'] = $title;
                $data_search = new WP_Query($args_search);
                $args_tax['tax_query'] = array(
                    array(
                        'taxonomy' => 'freelancer_skills',
                        'field' => 'name',
                        'terms' => $title,
                    )
                );
                $data_tax = new WP_Query($args_tax);
                $freelancer_ids = array_merge($data_tax->posts, $data_search->posts);
                $freelancer_ids = array_unique($freelancer_ids);
                if (!empty($freelancer_ids)) {
                    $args['post__in'] = $freelancer_ids;
                } else {
                    $args['s'] = $title;
                }
            }

            //tax query current term
            if (!empty($current_term) && !empty($type_term)) {
                $tax_query[] = array(
                    'taxonomy' => $type_term,
                    'field' => 'id',
                    'terms' => $current_term
                );
            }

            //tax query freelancer categories
            if (!empty($categories)) {
                $tax_query[] = array(
                    'taxonomy' => 'freelancer_categories',
                    'field' => 'term_id',
                    'terms' => $categories
                );
            }

            //location country
            if (!empty($location_country)) {
                $taxonomy_state = get_categories(
                    array(
                        'taxonomy' => 'felan_state',
                        'hide_empty' => false,
                        'parent' => 0,
                        'meta_query' => array(
                            array(
                                'key' => 'felan-state-country',
                                'value' => $location_country,
                                'compare' => '=',
                            )
                        )
                    )
                );

                if (!empty($taxonomy_state)) {
                    $keys_state = array();
                    foreach ($taxonomy_state as $terms_state) {
                        $keys_state[] = $terms_state->term_id;
                    }
                    $taxonomy_city = get_categories(
                        array(
                            'taxonomy' => 'felan_location',
                            'meta_query' => array(
                                array(
                                    'key' => 'felan-location-state',
                                    'value' => $keys_state,
                                    'compare' => 'IN'
                                )
                            )
                        )
                    );
                    $keys_city = array();
                    foreach ($taxonomy_city as $terms_city) {
                        $keys_city[] = $terms_city->term_id;
                    }
                } else {
                    $keys_city = '';
                }
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys_city
                );
            }

            //location state
            if (!empty($location_state)) {
                $taxonomy_terms = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'meta_query' => array(
                            array(
                                'key' => 'felan-location-state',
                                'value' => $location_state,
                                'compare' => '=',
                            )
                        )
                    )
                );
                $keys = array();
                foreach ($taxonomy_terms as $terms) {
                    $keys[] = $terms->term_id;
                }
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys
                );
            }

            //location city
            if (!empty($location_city)) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $location_city
                );
            }

            //tax query freelancer search location
            if (!empty($location) && ($radius_cities == 0 || empty($radius_cities))) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'name',
                    'terms' => $location
                );
            }

            //Nearby cities
            $resultsCities = array();
            if (!empty($radius_cities) && $radius_cities !== 0 && !empty($location)) {
                $taxonomies = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => true,
                        'parent' => 0
                    )
                );

                if (!empty($taxonomies)) {
                    $resultsCities = felan_find_nearby_cities($location, intval($radius_cities));
                    if (empty($resultsCities)) {
                        $resultsCities = $location;
                    }
                    $tax_query[] = array(
                        'taxonomy' => 'felan_location',
                        'field' => 'name',
                        'terms' => $resultsCities,
                    );
                }
            }

            //tax query freelancer experiences
            if (!empty($yoe_ids)) {
                $tax_query[] = array(
                    'taxonomy' => 'freelancer_yoe',
                    'field' => 'term_id',
                    'terms' => $yoe_ids
                );
            }

            //tax query freelancer qualification
            if (!empty($qualification_ids)) {
                $tax_query[] = array(
                    'taxonomy' => 'freelancer_qualification',
                    'field' => 'term_id',
                    'terms' => $qualification_ids
                );
            }

            //tax query freelancer ages
            if (!empty($ages_ids)) {
                $tax_query[] = array(
                    'taxonomy' => 'freelancer_ages',
                    'field' => 'term_id',
                    'terms' => $ages_ids
                );
            }

            //tax query freelancer skills
            if (!empty($skills_ids)) {
                $tax_query[] = array(
                    'taxonomy' => 'freelancer_skills',
                    'field' => 'term_id',
                    'terms' => $skills_ids
                );
            }

            //tax query freelancer languages
            if (!empty($languages_ids)) {
                $tax_query[] = array(
                    'taxonomy' => 'freelancer_languages',
                    'field' => 'term_id',
                    'terms' => $languages_ids
                );
            }

            //tax query freelancer gender
            if (!empty($gender_ids)) {
                $tax_query[] = array(
                    'taxonomy' => 'freelancer_gender',
                    'field' => 'term_id',
                    'terms' => $gender_ids
                );
            }

            //rating
            $rating_one = $rating_two = $rating_three = $rating_four = $rating_five = '';
            if (!empty($rating)) {
                if ((is_array($rating) && in_array('rating_five', $rating)) || $rating == 'rating_five') {
                    $rating_five = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_rating',
                        'value' => 5,
                        'type' => 'NUMERIC',
                        'compare' => '==',
                    );
                }

                if ((is_array($rating) && in_array('rating_four', $rating)) || $rating == 'rating_four') {
                    $rating_four = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_rating',
                        'value' => array(4, 4.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_three', $rating)) || $rating == 'rating_three') {
                    $rating_three = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_rating',
                        'value' => array(3, 3.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_two', $rating)) || $rating == 'rating_two') {
                    $rating_two = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_rating',
                        'value' => array(2, 2.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_one', $rating)) || $rating == 'rating_one') {
                    $rating_one = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_rating',
                        'value' => array(1, 1.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                $meta_query[] = array(
                    'relation' => 'OR',
                    $rating_five,
                    $rating_four,
                    $rating_three,
                    $rating_two,
                    $rating_one
                );
            }

            //meta query company sort by
            if (!empty($sort_by)) {
                if ($sort_by == 'newest') {
                    $args['orderby'] = array(
                        'menu_order' => 'ASC',
                        'date' => 'DESC',
                    );
                }

                if ($sort_by == 'oldest') {
                    $args['orderby'] = array(
                        'menu_order' => 'DESC',
                        'date' => 'ASC',
                    );
                }

                if ($sort_by == 'rating') {
                    $args['meta_key'] = FELAN_METABOX_PREFIX . 'freelancer_rating';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                }
            }

            $custom_fields_value = isset($_REQUEST['custom_fields_value']) ? felan_clean(wp_unslash($_REQUEST['custom_fields_value'])) : '';
            foreach ($custom_fields_value as $custom_field) {
                if ($custom_field) {
                    $meta_query[] = [
                        'relation' => 'OR',
                        [
                            'key'     => array_keys($custom_field)[0],
                            'value'   => array_values($custom_field)[0],
                            'compare' => 'IN'
                        ],
                    ];
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

            $args_map = $args;
            $args_map['paged'] = '';
            $args_map['posts_per_page'] = '-1';

            $data = new WP_Query($args);
            $total_post = $data->found_posts;

            $freelancer_html = '';
            $freelancer = array();

            $enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
            $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
            $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
            if ($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1') {
                if (!empty($total_post)) {
                    $count_post = sprintf(_n('%s candidates', '%s candidates', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                } else {
                    $count_post = esc_html__('0 candidate', 'felan-framework');
                }

                if (!empty($current_term)) {
                    $count_post = sprintf(_n('%s candidates', '%s candidates', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                    if (empty($total_post)) {
                        $count_post = sprintf(__('%s candidates', 'felan-framework'), $total_post);
                    }
                }

                $archive_freelancer_layout = felan_get_option('archive_freelancer_layout', 'layout-list');
                if (!empty($title) && $archive_freelancer_layout == 'layout-list') {
                    $count_post = sprintf(esc_html__('%1$s candidates for "%2$s"', 'felan-framework'), '<span>' . $total_post . '</span>', $title);
                }
            } else {
                if (!empty($total_post)) {
                    $count_post = sprintf(_n('%s Freelancers', '%s Freelancers', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                } else {
                    $count_post = esc_html__('0 freelancer', 'felan-framework');
                }

                if (!empty($current_term)) {
                    $count_post = sprintf(_n('%s Freelancers', '%s Freelancers', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                    if (empty($total_post)) {
                        $count_post = sprintf(__('%s Freelancers', 'felan-framework'), $total_post);
                    }
                }

                $archive_freelancer_layout = felan_get_option('archive_freelancer_layout', 'layout-list');
                if (!empty($title) && $archive_freelancer_layout == 'layout-list') {
                    $count_post = sprintf(esc_html__('%1$s freelancers for "%2$s"', 'felan-framework'), '<span>' . $total_post . '</span>', $title);
                }
            }

            $max_num_pages = $data->max_num_pages;
            $pagination_type = felan_get_option('freelancer_pagination_type', 'loadmore');
            if ($pagination_type == 'number') {
                $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                    'total' => $max_num_pages,
                    'current' => $paged,
                    'mid_size' => 1,
                    'type' => 'array',
                    'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                    'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
                )));
            } else {
                $pagination = '<a class="page-numbers next" href="#"><span>' . __('Load More', 'felan-framework') . '</span><span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span></a>';
            }

            $hidden_pagination = '';
            if ($paged == $max_num_pages) {
                $hidden_pagination = 1;
            }

            $enable_freelancer_show_map = felan_get_option('enable_freelancer_show_map');
            $enable_freelancer_show_map = !empty($has_map_val) ? $has_map_val : $enable_freelancer_show_map;
            if ($total_post > 0 && $enable_freelancer_show_map == 1) {

                $data_map = new WP_Query($args_map);

                while ($data_map->have_posts()) : $data_map->the_post();

                    $freelancer_id = get_the_ID();

                    $freelancer_meta_data = get_post_custom($freelancer_id);

                    $map_zoom_level = felan_get_option('map_zoom_level', '15');

                    $freelancer_address = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_address']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_address'][0] : '';
                    $freelancer_location = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_location', true);

                    if (!empty($freelancer_location['location'])) {
                        $lat_lng = explode(',', $freelancer_location['location']);
                    } else {
                        $lat_lng = array();
                    }

                    $author_id = get_post_field('post_author', $freelancer_id);
                    $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    if (!empty($freelancer_avatar)) {
                        $marker_icon = $freelancer_avatar;
                    } else {
                        $marker_icon = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                    };

                    $html_freelancer = ob_start();
                    felan_get_template('content-freelancer.php', array(
                        'freelancer_id' => $freelancer_id,
                        'freelancer_layout' => 'layout-grid',
                        'effect_class' => '',
                    ));
                    $html_freelancer = ob_get_clean();

                    $prop = new stdClass();
                    $prop->id = $freelancer_id;
                    $prop->lat = isset($lat_lng[0]) ? $lat_lng[0] : 59.325;
                    $prop->lng = isset($lat_lng[1]) ? $lat_lng[1] : 18.070;
                    $prop->freelancer = $html_freelancer;

                    if (empty($freelancer_url)) {
                        $freelancer_url = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                        $default_marker = felan_get_option('marker_icon', '');
                        if ($default_marker != '') {
                            if (is_array($default_marker) && $default_marker['url'] != '') {
                                $freelancer_url = $default_marker['url'];
                            }
                        }
                    }

                    if ($marker_icon) {
                        $prop->marker_icon = $marker_icon;
                    } else {
                        $prop->marker_icon = $freelancer_url;
                    }

                    array_push($freelancer, $prop);

                endwhile;
            }
            wp_reset_postdata();

            $freelancer_html = ob_start();

            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post();
                    felan_get_template('content-freelancer.php', array(
                        'freelancer_layout' => $freelancer_layout,
                    ));
                endwhile;
            }
            wp_reset_postdata();

            $freelancer_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'resultsCities' => $resultsCities,
                    'freelancer' => $freelancer,
                    'pagination' => $pagination,
                    'hidden_pagination' => $hidden_pagination,
                    'pagination_type' => $pagination_type,
                    'freelancer_html' => $freelancer_html,
                    'total_post' => $total_post,
                    'count_post' => $count_post
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'total_post' => $total_post,
                    'count_post' => $count_post
                ));
            }
            wp_die();
        }

        /**
         * Service Archive
         */
        public function felan_service_archive_ajax()
        {
            $title = isset($_REQUEST['title']) ? felan_clean(wp_unslash($_REQUEST['title'])) : '';
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $sort_by = isset($_REQUEST['sort_by']) ? felan_clean(wp_unslash($_REQUEST['sort_by'])) : '';
            $current_term = isset($_REQUEST['current_term']) ? felan_clean(wp_unslash($_REQUEST['current_term'])) : '';
            $type_term = isset($_REQUEST['type_term']) ? felan_clean(wp_unslash($_REQUEST['type_term'])) : '';
            $rating = isset($_REQUEST['rating']) ? felan_clean(wp_unslash($_REQUEST['rating'])) : '';
            $location = isset($_REQUEST['location']) ? felan_clean(wp_unslash($_REQUEST['location'])) : '';
            $has_map_val = isset($_REQUEST['has_map_val']) ? felan_clean(wp_unslash($_REQUEST['has_map_val'])) : '';
            $location_country = isset($_REQUEST['location_country']) ? felan_clean(wp_unslash($_REQUEST['location_country'])) : '';
            $location_state = isset($_REQUEST['location_state']) ? felan_clean(wp_unslash($_REQUEST['location_state'])) : '';
            $location_city = isset($_REQUEST['location_city']) ? felan_clean(wp_unslash($_REQUEST['location_city'])) : '';
            $radius_cities = isset($_REQUEST['radius_cities']) ? felan_clean(wp_unslash($_REQUEST['radius_cities'])) : '';
            $categories = isset($_REQUEST['categories']) ? felan_clean(wp_unslash($_REQUEST['categories'])) : '';
            $skills = isset($_REQUEST['skills']) ? felan_clean(wp_unslash($_REQUEST['skills'])) : '';
            $language = isset($_REQUEST['language']) ? felan_clean(wp_unslash($_REQUEST['language'])) : '';
            $range_min = isset($_REQUEST['range_min']) ? felan_clean(wp_unslash($_REQUEST['range_min'])) : '';
            $range_max = isset($_REQUEST['range_max']) ? felan_clean(wp_unslash($_REQUEST['range_max'])) : '';
            $service_layout = isset($_REQUEST['service_layout']) ? felan_clean(wp_unslash($_REQUEST['service_layout'])) : '';

            $meta_query = array();
            $tax_query = array();

            $args = array(
                'posts_per_page' => ($item_amount > 0) ? $item_amount : -1,
                'post_type' => 'service',
                'paged' => $paged,
                'meta_key' => FELAN_METABOX_PREFIX . 'service_featured',
                'orderby' => 'meta_value date',
                'order' => 'DESC',
                'post_status' => 'publish',
            );

            if (!empty($title)) {
                $args['fields'] = 'ids';
                $args_search = $args_tax = $args;
                $args_search['s'] = $title;
                $data_search = new WP_Query($args_search);
                $args_tax['tax_query'] = array(
                    array(
                        'taxonomy' => 'service-skills',
                        'field' => 'name',
                        'terms' => $title,
                    )
                );
                $data_tax = new WP_Query($args_tax);
                $service_ids = array_merge($data_tax->posts, $data_search->posts);
                $service_ids = array_unique($service_ids);
                if (!empty($service_ids)) {
                    $args['post__in'] = $service_ids;
                } else {
                    $args['s'] = $title;
                }
            }

            //tax query current term
            if (!empty($current_term) && !empty($type_term)) {
                $tax_query[] = array(
                    'taxonomy' => $type_term,
                    'field' => 'id',
                    'terms' => $current_term
                );
            }

            //location country
            if (!empty($location_country)) {
                $taxonomy_state = get_categories(
                    array(
                        'taxonomy' => 'felan_state',
                        'hide_empty' => false,
                        'parent' => 0,
                        'meta_query' => array(
                            array(
                                'key' => 'felan-state-country',
                                'value' => $location_country,
                                'compare' => '=',
                            )
                        )
                    )
                );

                if (!empty($taxonomy_state)) {
                    $keys_state = array();
                    foreach ($taxonomy_state as $terms_state) {
                        $keys_state[] = $terms_state->term_id;
                    }
                    $taxonomy_city = get_categories(
                        array(
                            'taxonomy' => 'felan_location',
                            'meta_query' => array(
                                array(
                                    'key' => 'felan-location-state',
                                    'value' => $keys_state,
                                    'compare' => 'IN'
                                )
                            )
                        )
                    );
                    $keys_city = array();
                    foreach ($taxonomy_city as $terms_city) {
                        $keys_city[] = $terms_city->term_id;
                    }
                } else {
                    $keys_city = '';
                }
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys_city
                );
            }

            //location state
            if (!empty($location_state)) {
                $taxonomy_terms = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'meta_query' => array(
                            array(
                                'key' => 'felan-location-state',
                                'value' => $location_state,
                                'compare' => '=',
                            )
                        )
                    )
                );
                $keys = array();
                foreach ($taxonomy_terms as $terms) {
                    $keys[] = $terms->term_id;
                }
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys
                );
            }

            //location city
            if (!empty($location_city)) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $location_city
                );
            }

            //tax query service search location
            if (!empty($location) && ($radius_cities == 0 || empty($radius_cities))) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'name',
                    'terms' => $location
                );
            }

            //Nearby cities
            $resultsCities = array();
            if (!empty($radius_cities) && $radius_cities !== 0 && !empty($location)) {
                $taxonomies = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => true,
                        'parent' => 0
                    )
                );

                if (!empty($taxonomies)) {
                    $resultsCities = felan_find_nearby_cities($location, intval($radius_cities));
                    if (empty($resultsCities)) {
                        $resultsCities = $location;
                    }
                    $tax_query[] = array(
                        'taxonomy' => 'felan_location',
                        'field' => 'name',
                        'terms' => $resultsCities,
                    );
                }
            }

            //tax query service categories
            if (!empty($categories)) {
                $tax_query[] = array(
                    'taxonomy' => 'service-categories',
                    'field' => 'term_id',
                    'terms' => $categories
                );
            }

            //tax query service skills
            if (!empty($skills)) {
                $tax_query[] = array(
                    'taxonomy' => 'service-skills',
                    'field' => 'term_id',
                    'terms' => $skills
                );
            }

            //tax query service language
            if (!empty($language)) {
                $tax_query[] = array(
                    'taxonomy' => 'service-language',
                    'field' => 'term_id',
                    'terms' => $language
                );
            }

            //rating
            $rating_one = $rating_two = $rating_three = $rating_four = $rating_five = '';
            if (!empty($rating)) {
                if ((is_array($rating) && in_array('rating_five', $rating)) || $rating == 'rating_five') {
                    $rating_five = array(
                        'key' => FELAN_METABOX_PREFIX . 'service_rating',
                        'value' => 5,
                        'type' => 'NUMERIC',
                        'compare' => '==',
                    );
                }

                if ((is_array($rating) && in_array('rating_four', $rating)) || $rating == 'rating_four') {
                    $rating_four = array(
                        'key' => FELAN_METABOX_PREFIX . 'service_rating',
                        'value' => array(4, 4.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_three', $rating)) || $rating == 'rating_three') {
                    $rating_three = array(
                        'key' => FELAN_METABOX_PREFIX . 'service_rating',
                        'value' => array(3, 3.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_two', $rating)) || $rating == 'rating_two') {
                    $rating_two = array(
                        'key' => FELAN_METABOX_PREFIX . 'service_rating',
                        'value' => array(2, 2.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_one', $rating)) || $rating == 'rating_one') {
                    $rating_one = array(
                        'key' => FELAN_METABOX_PREFIX . 'service_rating',
                        'value' => array(1, 1.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                $meta_query[] = array(
                    'relation' => 'OR',
                    $rating_five,
                    $rating_four,
                    $rating_three,
                    $rating_two,
                    $rating_one
                );
            }

            //service price
            if (!empty($range_min) && !empty($range_max)) { {
                    $meta_query[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'service_basic_price',
                        'value' => array($range_min, $range_max),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }
            }

            //meta query service sort by
            if (!empty($sort_by)) {
                if ($sort_by == 'oldest') {
                    $args['order'] = 'ASC';
                }
                if ($sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($sort_by == 'rating') {
                    $args['meta_key'] = FELAN_METABOX_PREFIX . 'service_rating';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                }
            }

            $custom_fields_value = isset($_REQUEST['custom_fields_value']) ? felan_clean(wp_unslash($_REQUEST['custom_fields_value'])) : '';
            foreach ($custom_fields_value as $custom_field) {
                if ($custom_field) {
                    $meta_query[] = [
                        'relation' => 'OR',
                        [
                            'key'     => array_keys($custom_field)[0],
                            'value'   => array_values($custom_field)[0],
                            'compare' => 'IN'
                        ],
                    ];
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
            $service_html = '';
            $service = array();

            if (!empty($total_post)) {
                if (!empty($title)) {
                    $count_post = sprintf(esc_html__('%1$s services for "%2$s"', 'felan-framework'), '<span class="count">' . $total_post . '</span>', $title);
                } else {
                    $count_post = sprintf(_n('%s services', '%s services', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                }
            } else {
                $count_post = esc_html__('0 service', 'felan-framework');
            }

            if (!empty($current_term)) {
                $count_post = sprintf(_n('%s services', '%s services', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                if (empty($total_post)) {
                    $count_post = sprintf(__('%s services', 'felan-framework'), $total_post);
                }
            }

            $pagination_type = felan_get_option('service_pagination_type', 'loadmore');
            if ($pagination_type == 'number') {
                $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                    'total' => $max_num_pages,
                    'current' => $paged,
                    'mid_size' => 1,
                    'type' => 'array',
                    'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                    'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
                )));
            } else {
                $pagination = '<a class="page-numbers next" href="#"><span>' . __('Load More', 'felan-framework') . '</span><span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span></a>';
            }

            $hidden_pagination = '';
            if ($paged == $max_num_pages) {
                $hidden_pagination = 1;
            }

            $enable_service_show_map = felan_get_option('enable_service_show_map');
            $enable_service_show_map = !empty($has_map_val) ? $has_map_val : $enable_service_show_map;
            if ($total_post > 0 &&  $enable_service_show_map == '1') {

                $args_map = $args;
                $args_map['paged'] = '';
                $args_map['posts_per_page'] = '-1';
                $data_map = new WP_Query($args_map);

                while ($data_map->have_posts()) : $data_map->the_post();

                    $service_id = get_the_ID();

                    $service_meta_data = get_post_custom($service_id);

                    $map_zoom_level = felan_get_option('map_zoom_level', '15');

                    $service_address = isset($service_meta_data[FELAN_METABOX_PREFIX . 'service_address']) ? $service_meta_data[FELAN_METABOX_PREFIX . 'service_address'][0] : '';
                    $service_location = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_location', true);

                    if (!empty($service_location['location'])) {
                        $lat_lng = explode(',', $service_location['location']);
                    } else {
                        $lat_lng = array();
                    }

                    $author_id = get_post_field('post_author', $service_id);
                    $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    if (!empty($freelancer_avatar)) {
                        $marker_icon = $freelancer_avatar;
                    } else {
                        $marker_icon = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                    };

                    $html_service = ob_start();
                    felan_get_template('content-service.php', array(
                        'services_id' => $service_id,
                        'service_layout' => 'layout-grid',
                        'effect_class' => '',
                    ));
                    $html_service = ob_get_clean();

                    $prop = new stdClass();
                    $prop->id = $service_id;
                    $prop->lat = isset($lat_lng[0]) ? $lat_lng[0] : 59.325;
                    $prop->lng = isset($lat_lng[1]) ? $lat_lng[1] : 18.070;
                    $prop->service = $html_service;

                    if (empty($service_url)) {
                        $service_url = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                        $default_marker = felan_get_option('marker_icon', '');
                        if ($default_marker != '') {
                            if (is_array($default_marker) && $default_marker['url'] != '') {
                                $service_url = $default_marker['url'];
                            }
                        }
                    }

                    if ($marker_icon) {
                        $prop->marker_icon = $marker_icon;
                    } else {
                        $prop->marker_icon = $service_url;
                    }

                    array_push($service, $prop);

                endwhile;
            }
            wp_reset_postdata();

            ob_start();

            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post();
                    felan_get_template('content-service.php', array(
                        'service_layout' => $service_layout,
                    ));
                endwhile;
            }
            wp_reset_postdata();

            $service_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'resultsCities' => $resultsCities,
                    'service' => $service,
                    'count_service' => count($service),
                    'pagination' => $pagination,
                    'hidden_pagination' => $hidden_pagination,
                    'pagination_type' => $pagination_type,
                    'service_html' => $service_html,
                    'total_post' => $total_post,
                    'count_post' => $count_post
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'total_post' => $total_post,
                    'count_post' => $count_post
                ));
            }
            wp_die();
        }


        /**
         * Project Archive
         */
        public function felan_project_archive_ajax()
        {
            $title = isset($_REQUEST['title']) ? felan_clean(wp_unslash($_REQUEST['title'])) : '';
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $sort_by = isset($_REQUEST['sort_by']) ? felan_clean(wp_unslash($_REQUEST['sort_by'])) : '';
            $current_term = isset($_REQUEST['current_term']) ? felan_clean(wp_unslash($_REQUEST['current_term'])) : '';
            $type_term = isset($_REQUEST['type_term']) ? felan_clean(wp_unslash($_REQUEST['type_term'])) : '';
            $rating = isset($_REQUEST['rating']) ? felan_clean(wp_unslash($_REQUEST['rating'])) : '';
            $location = isset($_REQUEST['location']) ? felan_clean(wp_unslash($_REQUEST['location'])) : '';
            $has_map_val = isset($_REQUEST['has_map_val']) ? felan_clean(wp_unslash($_REQUEST['has_map_val'])) : '';
            $location_country = isset($_REQUEST['location_country']) ? felan_clean(wp_unslash($_REQUEST['location_country'])) : '';
            $location_state = isset($_REQUEST['location_state']) ? felan_clean(wp_unslash($_REQUEST['location_state'])) : '';
            $location_city = isset($_REQUEST['location_city']) ? felan_clean(wp_unslash($_REQUEST['location_city'])) : '';
            $radius_cities = isset($_REQUEST['radius_cities']) ? felan_clean(wp_unslash($_REQUEST['radius_cities'])) : '';
            $categories = isset($_REQUEST['categories']) ? felan_clean(wp_unslash($_REQUEST['categories'])) : '';
            $skills = isset($_REQUEST['skills']) ? felan_clean(wp_unslash($_REQUEST['skills'])) : '';
            $language = isset($_REQUEST['language']) ? felan_clean(wp_unslash($_REQUEST['language'])) : '';
            $price_min = isset($_REQUEST['price_min']) ? felan_clean(wp_unslash($_REQUEST['price_min'])) : '';
            $price_max = isset($_REQUEST['price_max']) ? felan_clean(wp_unslash($_REQUEST['price_max'])) : '';
            $project_layout = isset($_REQUEST['project_layout']) ? felan_clean(wp_unslash($_REQUEST['project_layout'])) : '';

            $meta_query = array();
            $tax_query = array();

            $args = array(
                'posts_per_page' => ($item_amount > 0) ? $item_amount : -1,
                'post_type' => 'project',
                'paged' => $paged,
                'meta_key' => FELAN_METABOX_PREFIX . 'project_featured',
                'orderby' => 'meta_value date',
                'order' => 'DESC',
                'post_status' => 'publish',
            );

            if (!empty($title)) {
                $args['fields'] = 'ids';
                $args_search = $args_tax = $args;
                $args_search['s'] = $title;
                $data_search = new WP_Query($args_search);
                $args_tax['tax_query'] = array(
                    array(
                        'taxonomy' => 'project-skills',
                        'field' => 'name',
                        'terms' => $title,
                    )
                );
                $data_tax = new WP_Query($args_tax);
                $project_ids = array_merge($data_tax->posts, $data_search->posts);
                $project_ids = array_unique($project_ids);
                if (!empty($project_ids)) {
                    $args['post__in'] = $project_ids;
                } else {
                    $args['s'] = $title;
                }
            }

            //tax query current term
            if (!empty($current_term) && !empty($type_term)) {
                $tax_query[] = array(
                    'taxonomy' => $type_term,
                    'field' => 'id',
                    'terms' => $current_term
                );
            }

            //location country
            //location country
            if (!empty($location_country)) {
                $taxonomy_state = get_categories(
                    array(
                        'taxonomy' => 'felan_state',
                        'hide_empty' => false,
                        'parent' => 0,
                        'meta_query' => array(
                            array(
                                'key' => 'felan-state-country',
                                'value' => $location_country,
                                'compare' => '=',
                            )
                        )
                    )
                );

                if (!empty($taxonomy_state)) {
                    $keys_state = array();
                    foreach ($taxonomy_state as $terms_state) {
                        $keys_state[] = $terms_state->term_id;
                    }
                    $taxonomy_city = get_categories(
                        array(
                            'taxonomy' => 'felan_location',
                            'meta_query' => array(
                                array(
                                    'key' => 'felan-location-state',
                                    'value' => $keys_state,
                                    'compare' => 'IN'
                                )
                            )
                        )
                    );
                    $keys_city = array();
                    foreach ($taxonomy_city as $terms_city) {
                        $keys_city[] = $terms_city->term_id;
                    }
                } else {
                    $keys_city = '';
                }
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys_city
                );
            }

            //location state
            if (!empty($location_state)) {
                $taxonomy_terms = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'meta_query' => array(
                            array(
                                'key' => 'felan-location-state',
                                'value' => $location_state,
                                'compare' => '=',
                            )
                        )
                    )
                );
                $keys = array();
                foreach ($taxonomy_terms as $terms) {
                    $keys[] = $terms->term_id;
                }
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $keys
                );
            }

            //location city
            if (!empty($location_city)) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $location_city
                );
            }

            //tax query project search location
            if (!empty($location) && ($radius_cities == 0 || empty($radius_cities))) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'name',
                    'terms' => $location
                );
            }

            //Nearby cities
            $resultsCities = array();
            if (!empty($radius_cities) && $radius_cities !== 0 && !empty($location)) {
                $taxonomies = get_categories(
                    array(
                        'taxonomy' => 'felan_location',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => true,
                        'parent' => 0
                    )
                );

                if (!empty($taxonomies)) {
                    $resultsCities = felan_find_nearby_cities($location, intval($radius_cities));
                    if (empty($resultsCities)) {
                        $resultsCities = $location;
                    }
                    $tax_query[] = array(
                        'taxonomy' => 'felan_location',
                        'field' => 'name',
                        'terms' => $resultsCities,
                    );
                }
            }

            //Range Price
            if (!empty($price_min) && !empty($price_max)) {
                $meta_query[] = array(
                    'relation' => 'AND',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'project_budget_minimum',
                        'value' => $price_max,
                        'type' => 'NUMERIC',
                        'compare' => '<=',
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'project_budget_maximum',
                        'value' => $price_min,
                        'type' => 'NUMERIC',
                        'compare' => '>=',
                    ),
                );
            }

            //tax query project categories
            if (!empty($categories)) {
                $tax_query[] = array(
                    'taxonomy' => 'project-categories',
                    'field' => 'term_id',
                    'terms' => $categories
                );
            }

            //tax query project skills
            if (!empty($skills)) {
                $tax_query[] = array(
                    'taxonomy' => 'project-skills',
                    'field' => 'term_id',
                    'terms' => $skills
                );
            }

            //tax query project language
            if (!empty($language)) {
                $tax_query[] = array(
                    'taxonomy' => 'project-language',
                    'field' => 'term_id',
                    'terms' => $language
                );
            }

            //rating
            $rating_one = $rating_two = $rating_three = $rating_four = $rating_five = '';
            if (!empty($rating)) {
                if ((is_array($rating) && in_array('rating_five', $rating)) || $rating == 'rating_five') {
                    $rating_five = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_rating',
                        'value' => 5,
                        'type' => 'NUMERIC',
                        'compare' => '==',
                    );
                }

                if ((is_array($rating) && in_array('rating_four', $rating)) || $rating == 'rating_four') {
                    $rating_four = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_rating',
                        'value' => array(4, 4.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_three', $rating)) || $rating == 'rating_three') {
                    $rating_three = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_rating',
                        'value' => array(3, 3.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_two', $rating)) || $rating == 'rating_two') {
                    $rating_two = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_rating',
                        'value' => array(2, 2.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                if ((is_array($rating) && in_array('rating_one', $rating)) || $rating == 'rating_one') {
                    $rating_one = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_rating',
                        'value' => array(1, 1.99),
                        'type' => 'NUMERIC',
                        'compare' => 'BETWEEN',
                    );
                }

                $meta_query[] = array(
                    'relation' => 'OR',
                    $rating_five,
                    $rating_four,
                    $rating_three,
                    $rating_two,
                    $rating_one
                );
            }

            //Range Price
            if (!empty($range_min) && !empty($range_max)) {
                $meta_query[] = array(
                    'relation' => 'AND',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'project_budget_minimum',
                        'value' => $range_max,
                        'type' => 'NUMERIC',
                        'compare' => '<=',
                    ),
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'project_budget_maximum',
                        'value' => $range_min,
                        'type' => 'NUMERIC',
                        'compare' => '>=',
                    ),
                );
            }

            //meta query project sort by
            if (!empty($sort_by)) {
                if ($sort_by == 'oldest') {
                    $args['order'] = 'ASC';
                }
                if ($sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($sort_by == 'rating') {
                    $args['meta_key'] = FELAN_METABOX_PREFIX . 'project_rating';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                }
            }

            $custom_fields_value = isset($_REQUEST['custom_fields_value']) ? felan_clean(wp_unslash($_REQUEST['custom_fields_value'])) : '';
            foreach ($custom_fields_value as $custom_field) {
                if ($custom_field) {
                    $meta_query[] = [
                        'relation' => 'OR',
                        [
                            'key'     => array_keys($custom_field)[0],
                            'value'   => array_values($custom_field)[0],
                            'compare' => 'IN'
                        ],
                    ];
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
            $project_html = '';
            $project = array();

            if (!empty($total_post)) {
                if (!empty($title)) {
                    $count_post = sprintf(esc_html__('%1$s projects for "%2$s"', 'felan-framework'), '<span class="count">' . $total_post . '</span>', $title);
                } else {
                    $count_post = sprintf(_n('%s projects', '%s projects', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                }
            } else {
                $count_post = esc_html__('0 project', 'felan-framework');
            }

            if (!empty($current_term)) {
                $count_post = sprintf(_n('%s projects', '%s projects', $total_post, 'felan-framework'), '<span class="count">' . esc_html($total_post) . '</span>');
                if (empty($total_post)) {
                    $count_post = sprintf(__('%s projects', 'felan-framework'), $total_post);
                }
            }

            $pagination_type = felan_get_option('project_pagination_type', 'loadmore');
            if ($pagination_type == 'number') {
                $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                    'total' => $max_num_pages,
                    'current' => $paged,
                    'mid_size' => 1,
                    'type' => 'array',
                    'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                    'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
                )));
            } else {
                $pagination = '<a class="page-numbers next" href="#"><span>' . __('Load More', 'felan-framework') . '</span><span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span></a>';
            }

            $hidden_pagination = '';
            if ($paged == $max_num_pages) {
                $hidden_pagination = 1;
            }

            $enable_project_show_map = felan_get_option('enable_project_show_map');
            $enable_project_show_map = !empty($has_map_val) ? $has_map_val : $enable_project_show_map;
            if ($total_post > 0 && ($enable_project_show_map == '1')) {

                $args_map = $args;
                $args_map['paged'] = '';
                $args_map['posts_per_page'] = '-1';
                $data_map = new WP_Query($args_map);

                while ($data_map->have_posts()) : $data_map->the_post();

                    $project_id = get_the_ID();

                    $project_meta_data = get_post_custom($project_id);

                    $map_zoom_level = felan_get_option('map_zoom_level', '15');

                    $project_address = isset($project_meta_data[FELAN_METABOX_PREFIX . 'project_address']) ? $project_meta_data[FELAN_METABOX_PREFIX . 'project_address'][0] : '';
                    $project_location = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_location', true);

                    if (!empty($project_location['location'])) {
                        $lat_lng = explode(',', $project_location['location']);
                    } else {
                        $lat_lng = array();
                    }

                    $company_id = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_select_company', true);
                    $company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo', true);
                    if (!empty($company_logo)) {
                        $marker_icon = $company_logo['url'];
                    } else {
                        $marker_icon = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                    };

                    $html_project = ob_start();
                    felan_get_template('content-project.php', array(
                        'projects_id' => $project_id,
                        'project_layout' => 'layout-grid',
                        'effect_class' => '',
                    ));
                    $html_project = ob_get_clean();

                    $prop = new stdClass();
                    $prop->id = $project_id;
                    $prop->lat = isset($lat_lng[0]) ? $lat_lng[0] : 59.325;
                    $prop->lng = isset($lat_lng[1]) ? $lat_lng[1] : 18.070;
                    $prop->project = $html_project;

                    if (empty($project_url)) {
                        $project_url = FELAN_PLUGIN_URL . 'assets/images/map-marker-icon.png';
                        $default_marker = felan_get_option('marker_icon', '');
                        if ($default_marker != '') {
                            if (is_array($default_marker) && $default_marker['url'] != '') {
                                $project_url = $default_marker['url'];
                            }
                        }
                    }

                    if ($marker_icon) {
                        $prop->marker_icon = $marker_icon;
                    } else {
                        $prop->marker_icon = $project_url;
                    }

                    array_push($project, $prop);

                endwhile;
            }
            wp_reset_postdata();

            ob_start();

            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post();
                    felan_get_template('content-project.php', array(
                        'project_layout' => $project_layout,
                    ));
                endwhile;
            }
            wp_reset_postdata();

            $project_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'resultsCities' => $resultsCities,
                    'project' => $project,
                    'count_project' => count($project),
                    'pagination' => $pagination,
                    'hidden_pagination' => $hidden_pagination,
                    'pagination_type' => $pagination_type,
                    'project_html' => $project_html,
                    'total_post' => $total_post,
                    'count_post' => $count_post
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'total_post' => $total_post,
                    'count_post' => $count_post
                ));
            }
            wp_die();
        }

        /**
         * Jobs Filter
         */
        public function felan_filter_jobs_dashboard()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $jobs_search = isset($_REQUEST['jobs_search']) ? felan_clean(wp_unslash($_REQUEST['jobs_search'])) : '';
            $jobs_status = isset($_REQUEST['jobs_status']) ? felan_clean(wp_unslash($_REQUEST['jobs_status'])) : '';
            $sort_by = isset($_REQUEST['jobs_sort_by']) ? felan_clean(wp_unslash($_REQUEST['jobs_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $felan_profile = new Felan_Profile();

            $meta_query = array();
            $tax_query = array();
            $withdraw_total_price = '';

            $package_num_featured_jobs = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_number_featured', $user_id);
            $package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
            $package_unlimited_featured_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job_featured', true);
            $user_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
            $package_unlimited_listing = get_post_meta($user_package_id, FELAN_METABOX_PREFIX . 'package_unlimited_listing', true);

            if (!empty($item_id)) {
                $jobs = get_post($item_id);
                if ($action_click == 'mark-featured') {
                    if ($package_unlimited_featured_job !== '1' && $package_num_featured_jobs > 0) {
                        update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_featured', $package_num_featured_jobs - 1);
                    }
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'jobs_featured', 1);

                    $withdraw_total_price = felan_withdraw_total_price_ajax('featured_jobs', $user_id, 'employer');
                }

                if ($action_click == 'mark-filled') {
                    $data = array(
                        'ID' => $item_id,
                        'post_type' => 'jobs',
                        'post_status' => 'expired'
                    );
                    wp_update_post($data);
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'jobs_featured', 0);
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'enable_jobs_expires', 1);
                }

                if ($action_click == 'show') {
                    if ($jobs->post_status == 'pause') {
                        $data = array(
                            'ID' => $item_id,
                            'post_type' => 'jobs',
                            'post_status' => 'publish'
                        );
                    }
                    wp_update_post($data);
                }

                if ($action_click == 'pause') {
                    $data = array(
                        'ID' => $item_id,
                        'post_type' => 'jobs',
                        'post_status' => 'pause'
                    );
                    wp_update_post($data);
                }

                if ($action_click == 'extend') {
                    $date = date('Y-m-d');
                    $data = array(
                        'ID' => $item_id,
                        'post_type' => 'jobs',
                        'post_status' => 'publish',
                        'post_date' => $date,
                    );
                    wp_update_post($data);
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'enable_jobs_expires', 0);
                }
            }

            $args = array(
                'post_type' => 'jobs',
                'paged' => $paged,
                'post_status' => array('publish', 'expired', 'pending', 'pause'),
                'ignore_sticky_posts' => 1,
                'author' => $user_id,
                'orderby' => 'date',
            );

            if (!empty($jobs_search)) {
                $args['s'] = $jobs_search;
            }

            if (!empty($jobs_status)) {
                $args['post_status'] = $jobs_status;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query jobs sort_by
            if (!empty($sort_by)) {
                if ($sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($sort_by == 'oldest') {
                    $args['order'] = 'ASC';
                }
                if ($sort_by == 'featured') {
                    $meta_query[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'jobs_featured',
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

            $extend_expired_jobs = felan_get_option('enable_extend_expired_jobs');
            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    $id = get_the_ID();
                    $ids[] = $id;
                    global $current_user;
                    wp_get_current_user();
                    $user_id = $current_user->ID;
                    $status = get_post_status($id);
                    $jobs_categories =  get_the_terms($id, 'jobs-categories');
                    $jobs_location =  get_the_terms($id, 'felan_location');
                    $public_date = get_the_date('Y-m-d');
                    $current_date = date('Y-m-d');
                    $jobs_days_single = get_post_meta($id, FELAN_METABOX_PREFIX . 'jobs_days_closing', true);
                    $enable_jobs_expires = get_post_meta($id, FELAN_METABOX_PREFIX . 'enable_jobs_expires', true);
                    if ($enable_jobs_expires == '1') {
                        $jobs_days_closing   = '0';
                    } else {
                        if ($jobs_days_single) {
                            $jobs_days_closing = $jobs_days_single;
                        } else {
                            $jobs_days_closing   = felan_get_option('jobs_number_days', true);
                        }
                    }
                    $expiration_date = date('Y-m-d', strtotime($public_date . '+' . $jobs_days_closing . ' days'));
                    $jobs_featured    = get_post_meta($id, FELAN_METABOX_PREFIX . 'jobs_featured', true);

                    $val_expiration_date = date(get_option('date_format'), strtotime($public_date . '+' . $jobs_days_closing . ' days'));
                    $val_public_date = get_the_date(get_option('date_format'));
                    $company_id = get_post_meta($id, FELAN_METABOX_PREFIX . 'jobs_select_company', true);
                    $company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo', true);
                    ?>
                    <tr>
                        <td class="jobs-inner">
                            <div class="jobs-logo-company">
                                <?php if (!empty($company_logo['url'])) : ?>
                                    <img class="logo-company" src="<?php echo $company_logo['url'] ?>" alt="" />
                                <?php else : ?>
                                    <div class="logo-company"><i class="far fa-camera"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="jobs-content">
                                <h3 class="title-jobs-dashboard">
                                    <a href="<?php echo felan_get_permalink('jobs_dashboard') ?>?pages=performance&tab=statics&jobs_id=<?php echo esc_attr($id); ?>">
                                        <?php echo felan_get_icon_status($id); ?>
                                        <?php echo get_the_title($id); ?>
                                    </a>
                                </h3>
                                <p>
                                    <?php if (is_array($jobs_categories)) {
                                        foreach ($jobs_categories as $categories) { ?>
                                            <?php esc_html_e($categories->name); ?>
                                    <?php }
                                    } ?>
                                    <?php if (is_array($jobs_location)) {
                                        foreach ($jobs_location as $location) { ?>
                                            <?php esc_html_e('/ ' . $location->name); ?>
                                    <?php }
                                    } ?>
                                </p>
                            </div>
                        </td>
                        <td>
                            <div class="number-applicant">
                                <span class="number"><?php echo felan_total_applications_jobs_id($id); ?></span>
                                <?php if (felan_total_applications_jobs_id($id) > 1) { ?>
                                    <a href="<?php echo felan_get_permalink('jobs_dashboard') ?>?pages=performance&tab=applicants&jobs_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Applicants', 'felan-framework') ?></a>
                                <?php } else { ?>
                                    <a href="<?php echo felan_get_permalink('jobs_dashboard') ?>?pages=performance&tab=applicants&jobs_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Application', 'felan-framework') ?></a>
                                <?php } ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($enable_jobs_expires == '1' || $status == 'expired') : ?>
                                <span class="label label-close"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                            <?php endif; ?>
                            <?php if ($status == 'publish' && $enable_jobs_expires != '1') : ?>
                                <span class="label label-open"><?php esc_html_e('Opening', 'felan-framework') ?></span>
                            <?php endif; ?>
                            <?php if ($status == 'pending') : ?>
                                <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                            <?php endif; ?>
                            <?php if ($status == 'pause') : ?>
                                <span class="label label-pause"><?php esc_html_e('Pause', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="start-time"><?php echo $val_public_date ?></span>
                        </td>
                        <td>
                            <span class="expires-time">
                                <?php if ($expiration_date > $public_date && $expiration_date > $current_date) : ?>
                                    <?php echo $val_expiration_date ?>
                                <?php else : ?>
                                    <span><?php esc_html_e('Expires', 'felan-framework') ?></span>
                                <?php endif ?>
                            </span>
                        </td>
                        <?php
                        ?>
                        <td class="action-setting jobs-control">
                            <?php
                            if ($status !== 'expired') : ?>
                                <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                                <ul class="action-dropdown">
                                    <?php
                                    $jobs_dashboard_link = felan_get_permalink('jobs_dashboard');
                                    $paid_submission_type = felan_get_option('paid_submission_type', 'no');
                                    $check_package = $felan_profile->user_package_available($user_id);
                                    $package_num_featured_job = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_number_featured', $user_id);
                                    $package_unlimited_featured_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job_featured', true);
                                    $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                                    switch ($status) {
                                        case 'publish':
                                            if ($paid_submission_type == 'per_package') {

                                                if ($check_package != -1 && $check_package != 0) { ?>
                                                    <li><a class="btn-edit"
                                                            href="<?php echo esc_url($jobs_dashboard_link); ?><?php echo strpos(esc_url($jobs_dashboard_link), '?') ? '&' : '?' ?>pages=edit&jobs_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a>
                                                    </li>
                                                <?php }

                                                if ($user_demo == 'yes') { ?>

                                                    <li><a class="btn-add-to-message" href="#"
                                                            data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Paused', 'felan-framework'); ?></a>
                                                    </li>
                                                    <?php if ($jobs_featured != 1) { ?>
                                                        <li><a class="btn-add-to-message" href="#"
                                                                data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark featured', 'felan-framework'); ?></a>
                                                        </li>
                                                    <?php } ?>
                                                    <li><a class="btn-add-to-message" href="#"
                                                            data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark Filled', 'felan-framework'); ?></a>
                                                    </li>

                                                    <?php } else {

                                                    if ($check_package != -1 && $check_package != 0) { ?>
                                                        <li><a class="btn-pause" jobs-id="<?php echo esc_attr($id); ?>"
                                                                href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Paused', 'felan-framework') ?></a>
                                                        </li>
                                                    <?php } ?>

                                                    <?php if (($package_unlimited_featured_job == '1' || $package_num_featured_job > 0) && $jobs_featured != 1 && $check_package != -1 && $check_package != 0) { ?>
                                                        <li><a class="btn-mark-featured" jobs-id="<?php echo esc_attr($id); ?>"
                                                                href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a>
                                                        </li>
                                                    <?php } ?>

                                                    <?php if ($check_package != -1 && $check_package != 0) { ?>
                                                        <li><a class="btn-mark-filled" jobs-id="<?php echo esc_attr($id); ?>"
                                                                href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark Filled', 'felan-framework') ?></a>
                                                        </li>
                                                    <?php }
                                                }

                                                if ($check_package != -1 && $check_package != 0) { ?>
                                                    <li>
                                                        <a href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('View detail', 'felan-framework') ?></a>
                                                    </li>
                                                <?php }
                                            } else { ?>

                                                <li><a class="btn-edit"
                                                        href="<?php echo esc_url($jobs_dashboard_link); ?>?pages=edit&jobs_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a>
                                                </li>

                                                <?php if ($user_demo == 'yes') { ?>
                                                    <li><a class="btn-add-to-message" href="#"
                                                            data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Paused', 'felan-framework'); ?></a>
                                                    </li>
                                                    <?php if ($jobs_featured != 1) { ?>
                                                        <li><a class="btn-add-to-message" href="#"
                                                                data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark featured', 'felan-framework'); ?></a>
                                                        </li>
                                                    <?php } ?>
                                                    <li><a class="btn-add-to-message" href="#"
                                                            data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Mark Filled', 'felan-framework'); ?></a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li><a class="btn-pause" jobs-id="<?php echo esc_attr($id); ?>"
                                                            href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Paused', 'felan-framework') ?></a>
                                                    </li>

                                                    <?php if ($jobs_featured != 1) { ?>
                                                        <li><a class="btn-mark-featured" jobs-id="<?php echo esc_attr($id); ?>"
                                                                href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark featured', 'felan-framework') ?></a>
                                                        </li>
                                                    <?php } ?>

                                                    <li><a class="btn-mark-filled" jobs-id="<?php echo esc_attr($id); ?>"
                                                            href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Mark Filled', 'felan-framework') ?></a>
                                                    </li>
                                                <?php } ?>

                                                <li>
                                                    <a href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('View detail', 'felan-framework') ?></a>
                                                </li>
                                            <?php }
                                            break;
                                        case 'pending': ?>
                                            <li><a class="btn-edit"
                                                    href="<?php echo esc_url($jobs_dashboard_link); ?>?pages=edit&jobs_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a>
                                            </li>
                                        <?php
                                            break;
                                        case 'pause':
                                        ?>
                                            <li><a class="btn-edit"
                                                    href="<?php echo esc_url($jobs_dashboard_link); ?>?pages=edit&jobs_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a>
                                            </li>
                                            <li><a class="btn-show" jobs-id="<?php echo esc_attr($id); ?>"
                                                    href="<?php echo get_the_permalink($id); ?>"><?php esc_html_e('Continue', 'felan-framework'); ?></a>
                                        <?php
                                    } ?>
                                </ul>
                            <?php else : ?>
                                <?php if ($extend_expired_jobs == 1) : ?>
                                    <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                                    <ul class="action-dropdown">
                                        <li><a class="btn-extend" jobs-id="<?php echo esc_attr($id); ?>"><?php esc_html_e('Extend', 'felan-framework'); ?></a></li>
                                    </ul>
                                <?php else : ?>
                                    <a href="#" class="icon-setting btn-add-to-message" data-text="<?php echo esc_attr('Jobs has expired so you can not change it', 'felan-framework'); ?>"><i class="far fa-ellipsis-h"></i></a>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $jobs_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'jobs_html' => $jobs_html,
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
         * Employer Order
         */
        public function felan_employer_order_service()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $service_search = isset($_REQUEST['service_search']) ? felan_clean(wp_unslash($_REQUEST['service_search'])) : '';
            $service_status = isset($_REQUEST['service_status']) ? felan_clean(wp_unslash($_REQUEST['service_status'])) : '';
            $sort_by = isset($_REQUEST['service_sort_by']) ? felan_clean(wp_unslash($_REQUEST['service_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $content_refund = isset($_REQUEST['content_refund']) ? felan_clean(wp_unslash($_REQUEST['content_refund'])) : '';
            $service_payment = isset($_REQUEST['service_payment']) ? felan_clean(wp_unslash($_REQUEST['service_payment'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();
            $currency_sign_default = felan_get_option('currency_sign_default');

            $args = array(
                'post_type' => 'service_order',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'author' => $user_id,
            );

            if (!empty($service_search)) {
                $args['s'] = $service_search;
            }

            if (!empty($service_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'service_order_payment_status',
                    'value' => $service_status,
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
                    $order_id = get_the_ID();
                    $service_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $service_skills = get_the_terms($service_id, 'service-skills');
                    $service_categories = get_the_terms($service_id, 'service-categories');
                    $service_location = get_the_terms($service_id, 'felan_location');
                    $public_date = get_the_date(get_option('date_format'));
                    $thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
                    $author_id = get_post_field('post_author', $service_id);
                    $author_name = get_the_author_meta('display_name', $author_id);
                    $order_has_disputes_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'order_has_disputes_id', true);

                    $service_order_date = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_date', true);
                    $active_date = strtotime($service_order_date);
                    $current_time = strtotime(current_datetime()->format('Y-m-d H:i:s'));
                    $service_time_type = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_time_type', true);
                    $number_delivery_time = intval(get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_number_time', true));
                    switch ($service_time_type) {
                        case 'hr':
                            $seconds = 60 * 60;
                            break;
                        case 'day':
                            $seconds = 60 * 60 * 24;
                            break;
                        case 'week':
                            $seconds = 60 * 60 * 24 * 7;
                            break;
                        case 'month':
                            $seconds = 60 * 60 * 24 * 30;
                            break;
                    }
                    if (is_numeric($active_date) && is_numeric($seconds) && is_numeric($number_delivery_time)) {
                        $expired_time = $active_date + ($seconds * $number_delivery_time);
                    } else {
                        $expired_time = 0;
                    }

                    if ($current_time < $expired_time) {
                        $seconds = $expired_time - $current_time;
                        $dtF = new \DateTime('@0');
                        $dtT = new \DateTime("@$seconds");
                        $expired_days = $dtF->diff($dtT)->format('%a');
                        $expired_hours = $dtF->diff($dtT)->format('%h');
                        $expired_minutes = $dtF->diff($dtT)->format('%i');
                        if ($expired_days > 0) {
                            if ($expired_days === '1') {
                                $expired_date = sprintf(esc_html__('%1s day %2s hours', 'felan-framework'), $expired_days, $expired_hours);
                            } else {
                                $expired_date = sprintf(esc_html__('%1s days %2s hours', 'felan-framework'), $expired_days, $expired_hours);
                            }
                        } else {
                            if ($expired_hours === '1') {
                                $expired_date = sprintf(esc_html__('%1s hour %2s minutes', 'felan-framework'), $expired_hours, $expired_minutes);
                            } else {
                                $expired_date = sprintf(esc_html__('%1s hours %2s minutes', 'felan-framework'), $expired_hours, $expired_minutes);
                            }
                        }
                    } else {
                        $expired_date = esc_html__('expired', 'felan-framework');
                    }

                    $args_freelancer = array(
                        'post_type' => 'freelancer',
                        'posts_per_page' => 1,
                        'author' => $author_id,
                    );
                    $current_user_posts = get_posts($args_freelancer);
                    $freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
                    $service_featured = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true);
                    $service_refund_content = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_refund_content', true);
                    $status = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', true);

                    $currency_sign_default = felan_get_option('currency_sign_default');
                    $currency_position = felan_get_option('currency_position');
                    $price_order = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price', true);
                    $price_order_number = str_replace($currency_sign_default, '', $price_order);
                    $enable_freelancer_service_fee =  felan_get_option('enable_freelancer_service_fee');
                    $freelancer_number_service_fee =  felan_get_option('freelancer_number_service_fee');
                    $price_fee = round(intval($price_order_number) * intval($freelancer_number_service_fee) / 100);
                    $has_service_review = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'has_service_review', true);
                    ?>
                    <tr>
                        <td>
                            <div class="service-header">
                                <?php if (!empty($thumbnail)) : ?>
                                    <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                <?php endif; ?>
                                <div class="content">
                                    <h3 class="title-my-service">
                                        <a href="<?php echo get_the_permalink($service_id) ?>">
                                            <?php echo get_the_title($service_id); ?>
                                            <?php if ($service_featured === '1') : ?>
                                                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <span><?php echo esc_html__('by', 'felan-framework') ?></span>
                                        <span class="author"><?php echo $author_name; ?></span>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="start-time">
                            <span>
                                <?php echo esc_html__('Order date: ', 'felan-framework') ?>
                                <span class="time"><?php echo $public_date; ?></span>
                            </span>
                            <span>
                                <?php echo esc_html__('Deadline: ', 'felan-framework') ?>
                                <span class="time"><?php echo $expired_date; ?></span>
                            </span>
                        </td>
                        <td class="price">
                            <?php echo $price_order; ?>
                        </td>
                        <td class="status">
                            <?php felan_service_order_status($status); ?>
                            <?php if ($status == 'completed') : ?>
                                <?php if ($has_service_review == '1') : ?>
                                    <?php if (felan_get_option('enable_edit_review_service') == '1') : ?>
                                        <div class="action-review">
                                            <?php echo felan_get_total_rating('service', $service_id); ?>
                                            <a href="#" class="btn-action-review felan-button button-link" service-id="<?php echo $service_id; ?>">
                                                <?php echo esc_html__('View', 'felan-framework'); ?>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="action-review">
                                            <?php echo felan_get_total_rating('service', $service_id); ?>
                                            <a href="#" class="btn-action-view felan-button button-link" service-id="<?php echo $service_id; ?>">
                                                <?php echo esc_html__('View', 'felan-framework'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="action-review">
                                        <a href="#" class="btn-action-review felan-button button-link" service-id="<?php echo $service_id; ?>">
                                            <?php echo esc_html__('Write a review', 'felan-framework'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($status == 'canceled') : ?>
                                <?php if (!empty($order_has_disputes_id)) : ?>
                                    <div class="action-dispute">
                                        <a href="<?php echo esc_url(felan_get_permalink('disputes')); ?>?order_id=<?php echo esc_attr($order_id) ?>&disputes_id=<?php echo esc_attr($order_has_disputes_id); ?>" class="felan-button button-link">
                                            <?php echo esc_html__('View Dispute', 'felan-framework'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(felan_get_permalink('employer_service')); ?>?order_id=<?php echo esc_attr($order_id); ?>"
                                class="service-detail felan-button"><?php echo esc_html__('Detail', 'felan-framework') ?></a>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $service_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'content_refund' => $content_refund,
                    'pagination' => $pagination,
                    'service_html' => $service_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Employer Disputes
         */
        public function felan_employer_disputes()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $disputes_search = isset($_REQUEST['disputes_search']) ? felan_clean(wp_unslash($_REQUEST['disputes_search'])) : '';
            $disputes_status = isset($_REQUEST['disputes_status']) ? felan_clean(wp_unslash($_REQUEST['disputes_status'])) : '';
            $sort_by = isset($_REQUEST['disputes_sort_by']) ? felan_clean(wp_unslash($_REQUEST['disputes_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();
            $currency_sign_default = felan_get_option('currency_sign_default');

            $args = array(
                'post_type' => 'disputes',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'author' => $user_id,
            );

            if (!empty($disputes_search)) {
                $args['s'] = $disputes_search;
            }

            if (!empty($disputes_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'disputes_status',
                    'value' => $disputes_status,
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
                while ($data->have_posts()) : $data->the_post();
                    $disputes_id = get_the_ID();
                    $order_id = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', true);
                    $service_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $public_date = get_the_date(get_option('date_format'));
                    $thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
                    $author_id = get_post_field('post_author', $service_id);
                    $author_name = get_the_author_meta('display_name', $author_id);

                    $service_featured = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true);
                    $status = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_status', true);
                    $price_order = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price', true);
                ?>
                    <tr>
                        <td>
                            <div class="service-header">
                                <?php if (!empty($thumbnail)) : ?>
                                    <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                <?php endif; ?>
                                <div class="content">
                                    <h3 class="title-my-service">
                                        <a href="<?php echo get_the_permalink($service_id) ?>">
                                            <?php echo get_the_title($service_id); ?>
                                            <?php if ($service_featured === '1') : ?>
                                                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                        </td>
                        <td class="start-time">
                            <span class="time"><?php echo $public_date; ?></span>
                        </td>
                        <td class="price">
                            <?php echo $price_order; ?>
                        </td>
                        <td class="author">
                            <?php echo esc_html($author_name); ?>
                        </td>
                        <td class="status">
                            <?php if ($status == 'close') : ?>
                                <span class="label label-close tooltip"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                            <?php elseif ($status == 'refund') : ?>
                                <span class="label label-open tooltip"><?php esc_html_e('Refunded', 'felan-framework') ?></span>
                            <?php else : ?>
                                <span class="label label-inprogress tooltip"><?php esc_html_e('Open', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(felan_get_permalink('disputes')); ?>?order_id=<?php echo esc_attr($order_id) ?>&disputes_id=<?php echo esc_attr($disputes_id) ?>"
                                class="service-detail felan-button"><?php echo esc_html__('Detail', 'felan-framework') ?></a>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $disputes_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'disputes_html' => $disputes_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * Employer Project Disputes
         */
        public function felan_employer_project_disputes()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $disputes_search = isset($_REQUEST['disputes_search']) ? felan_clean(wp_unslash($_REQUEST['disputes_search'])) : '';
            $disputes_status = isset($_REQUEST['disputes_status']) ? felan_clean(wp_unslash($_REQUEST['disputes_status'])) : '';
            $sort_by = isset($_REQUEST['disputes_sort_by']) ? felan_clean(wp_unslash($_REQUEST['disputes_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();
            $currency_sign_default = felan_get_option('currency_sign_default');

            $args = array(
                'post_type' => 'project_disputes',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'author' => $user_id,
            );

            if (!empty($disputes_search)) {
                $args['s'] = $disputes_search;
            }

            if (!empty($disputes_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'project_disputes_status',
                    'value' => $disputes_status,
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
                    $disputes_id = get_the_ID();
                    $order_id = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_order_id', true);
                    $project_id = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);
                    $public_date = get_the_date(get_option('date_format'));
                    $thumbnail = get_the_post_thumbnail_url($project_id, '70x70');
                    $author_freelancer_id = get_post_field('post_author', $order_id);
                    $author_freelancer_name = get_the_author_meta('display_name', $author_freelancer_id);

                    $project_featured = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
                    $status = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_status', true);
                    $price_order = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_price', true);
                    $currency_sign_default = felan_get_option('currency_sign_default');
                    $currency_position = felan_get_option('currency_position');
                    if ($currency_position == 'before') {
                        $price_order = $currency_sign_default . $price_order;
                    } else {
                        $price_order = $price_order . $currency_sign_default;
                    }
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
                                                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                        </td>
                        <td class="start-time">
                            <span class="time"><?php echo $public_date; ?></span>
                        </td>
                        <td class="price">
                            <?php echo $price_order; ?>
                        </td>
                        <td class="author">
                            <?php echo esc_html($author_freelancer_name); ?>
                        </td>
                        <td class="status">
                            <?php if ($status == 'close') : ?>
                                <span class="label label-close tooltip"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                            <?php elseif ($status == 'refund') : ?>
                                <span class="label label-open tooltip"><?php esc_html_e('Refunded', 'felan-framework') ?></span>
                            <?php else : ?>
                                <span class="label label-inprogress tooltip"><?php esc_html_e('Open', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(felan_get_permalink('disputes')); ?>?listing=project&order_id=<?php echo esc_attr($order_id) ?>&disputes_id=<?php echo esc_attr($disputes_id) ?>"
                                class="project-detail felan-button"><?php echo esc_html__('Detail', 'felan-framework') ?></a>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $disputes_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'disputes_html' => $disputes_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Freelancer Disputes
         */
        public function felan_freelancer_disputes()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $disputes_search = isset($_REQUEST['disputes_search']) ? felan_clean(wp_unslash($_REQUEST['disputes_search'])) : '';
            $disputes_status = isset($_REQUEST['disputes_status']) ? felan_clean(wp_unslash($_REQUEST['disputes_status'])) : '';
            $sort_by = isset($_REQUEST['disputes_sort_by']) ? felan_clean(wp_unslash($_REQUEST['disputes_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();

            $args = array(
                'post_type' => 'disputes',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'disputes_freelancer_id',
                        'value' => $user_id,
                        'compare' => '='
                    ),
                ),
            );

            if (!empty($disputes_search)) {
                $args['s'] = $disputes_search;
            }

            if (!empty($user_id)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'disputes_freelancer_id',
                    'value' => $user_id,
                    'compare' => '='
                );
            }

            if (!empty($disputes_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'disputes_status',
                    'value' => $disputes_status,
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
                    $disputes_id = get_the_ID();
                    $order_id = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', true);
                    $service_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $public_date = get_the_date(get_option('date_format'));
                    $thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
                    $author_id = get_post_field('post_author', $disputes_id);
                    $author_name = get_the_author_meta('display_name', $author_id);

                    $service_featured = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true);
                    $status = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_status', true);
                    $price_order = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price', true);
                    ?>
                    <tr>
                        <td>
                            <div class="service-header">
                                <?php if (!empty($thumbnail)) : ?>
                                    <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                <?php endif; ?>
                                <div class="content">
                                    <h3 class="title-my-service">
                                        <a href="<?php echo get_the_permalink($service_id) ?>">
                                            <?php echo get_the_title($service_id); ?>
                                            <?php if ($service_featured === '1') : ?>
                                                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                        </td>
                        <td class="start-time">
                            <span class="time"><?php echo $public_date; ?></span>
                        </td>
                        <td class="price">
                            <?php echo $price_order; ?>
                        </td>
                        <td class="author">
                            <?php echo esc_html($author_name); ?>
                        </td>
                        <td class="status">
                            <?php if ($status == 'close') : ?>
                                <span class="label label-close tooltip"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                            <?php elseif ($status == 'refund') : ?>
                                <span class="label label-open tooltip"><?php esc_html_e('Refunded', 'felan-framework') ?></span>
                            <?php else : ?>
                                <span class="label label-inprogress tooltip"><?php esc_html_e('Open', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(felan_get_permalink('disputes')); ?>?order_id=<?php echo esc_attr($order_id) ?>&disputes_id=<?php echo esc_attr($disputes_id) ?>"
                                class="service-detail felan-button"><?php echo esc_html__('Detail', 'felan-framework') ?></a>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $disputes_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'disputes_html' => $disputes_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Freelancer Project Disputes
         */
        public function felan_freelancer_project_disputes()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $disputes_search = isset($_REQUEST['disputes_search']) ? felan_clean(wp_unslash($_REQUEST['disputes_search'])) : '';
            $disputes_status = isset($_REQUEST['disputes_status']) ? felan_clean(wp_unslash($_REQUEST['disputes_status'])) : '';
            $sort_by = isset($_REQUEST['disputes_sort_by']) ? felan_clean(wp_unslash($_REQUEST['disputes_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();

            $args = array(
                'post_type' => 'project_disputes',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'project_disputes_freelancer_id',
                        'value' => $user_id,
                        'compare' => '='
                    ),
                ),
            );

            if (!empty($disputes_search)) {
                $args['s'] = $disputes_search;
            }

            if (!empty($user_id)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'project_disputes_freelancer_id',
                    'value' => $user_id,
                    'compare' => '='
                );
            }

            if (!empty($disputes_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'project_disputes_status',
                    'value' => $disputes_status,
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
                    $disputes_id = get_the_ID();
                    $order_id = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_order_id', true);
                    $project_id = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);
                    $public_date = get_the_date(get_option('date_format'));
                    $thumbnail = get_the_post_thumbnail_url($project_id, '70x70');
                    $author_id = get_post_field('post_author', $disputes_id);
                    $author_name = get_the_author_meta('display_name', $author_id);

                    $project_featured = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
                    $status = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_status', true);
                    $price_order = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_price', true);
                    $currency_sign_default = felan_get_option('currency_sign_default');
                    $currency_position = felan_get_option('currency_position');
                    if ($currency_position == 'before') {
                        $price_order = $currency_sign_default . $price_order;
                    } else {
                        $price_order = $price_order . $currency_sign_default;
                    }
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
                                                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                        </td>
                        <td class="start-time">
                            <span class="time"><?php echo $public_date; ?></span>
                        </td>
                        <td class="price">
                            <?php echo $price_order; ?>
                        </td>
                        <td class="author">
                            <?php echo esc_html($author_name); ?>
                        </td>
                        <td class="status">
                            <?php if ($status == 'close') : ?>
                                <span class="label label-close tooltip"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                            <?php elseif ($status == 'refund') : ?>
                                <span class="label label-open tooltip"><?php esc_html_e('Refunded', 'felan-framework') ?></span>
                            <?php else : ?>
                                <span class="label label-inprogress tooltip"><?php esc_html_e('Open', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(felan_get_permalink('freelancer_disputes')); ?>?listing=project&order_id=<?php echo esc_attr($order_id) ?>&disputes_id=<?php echo esc_attr($disputes_id) ?>"
                                class="project-detail felan-button"><?php echo esc_html__('Detail', 'felan-framework') ?></a>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $disputes_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'disputes_html' => $disputes_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Employer Serivce Order Detail
         */
        public function felan_employer_service_detail()
        {
            $order_id = isset($_REQUEST['order_id']) ? felan_clean(wp_unslash($_REQUEST['order_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $order_price = isset($_REQUEST['order_price']) ? felan_clean(wp_unslash($_REQUEST['order_price'])) : '';
            $tell_us = isset($_REQUEST['tell_us']) ? felan_clean(wp_unslash($_REQUEST['tell_us'])) : '';
            $content_refund = isset($_REQUEST['content_refund']) ? felan_clean(wp_unslash($_REQUEST['content_refund'])) : '';


            $user_employer        = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_user_id', true);
            $user_employer        = get_user_by('id', $user_employer);
            $user_employer_name   = $user_employer->display_name;
            $user_freelancer      = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_author_id', true);
            $user_freelancer      = get_user_by('id', $user_freelancer);
            $user_freelancer_name = $user_freelancer->display_name;
            $user_freelancer_mail = $user_freelancer->user_email;

            $felan_service_page_id = felan_get_option('felan_freelancer_service_page_id');
            $felan_service_page    = get_page_link($felan_service_page_id);
            $service_id            = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);


            $args_mail = array(
                'employer_name'   => $user_employer_name,
                'freelancer_name' => $user_freelancer_name,
                'service_name'    => get_the_title($service_id),
                'order_url'       => $felan_service_page . '?order_id=' . $order_id,
            );

            if ($action_click == 'completed') {
                update_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', 'completed');

                $currency_sign_default = felan_get_option('currency_sign_default');
                $price = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price', true);
                if (!empty($price)) {
                    $author_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_author_id', true);
                    // $total_price = str_replace($currency_sign_default, '', $price);
                    $total_price = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price_not_format', true);

                    //Frelancer
                    $withdraw_price = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
                    if (empty($withdraw_price)) {
                        $withdraw_price = 0;
                    }
                    $withdraw_price = $withdraw_price + $total_price;
                    update_user_meta($author_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', $withdraw_price);
                }


                $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
                if ($enable_post_type_service == '1') {
                    felan_send_email($user_freelancer_mail, 'mail_employer_complete_service', $args_mail);
                    felan_get_data_ajax_notification($order_id, 'employer-complete-service');
                }
            }

            if ($action_click == 'canceled') {
                update_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', 'canceled');

                $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
                if ($enable_post_type_service == '1') {
                    felan_send_email($user_freelancer_mail, 'mail_employer_cancel_service', $args_mail);
                    felan_get_data_ajax_notification($order_id, 'employer-cancel-service');
                }
            }

            if ($action_click == 'refund') {
                if (empty($tell_us) || empty($content_refund)) {
                    echo json_encode(array('success' => false, 'message' => esc_html('All fields need to be completed', 'felan-framework')));
                    wp_die();
                }

                $post_title = esc_html__('Others', 'felan-framework');
                if ($tell_us == 'support') {
                    $post_title = esc_html__('Customer Support', 'felan-framework');
                } elseif ($tell_us == 'code') {
                    $post_title = esc_html__('Code Quality', 'felan-framework');
                } elseif ($tell_us == 'desgin') {
                    $post_title = esc_html__('Design Quality', 'felan-framework');
                }

                $args_disputes = array(
                    'post_title'    => $post_title,
                    'post_status'   => 'publish',
                    'post_type'     => 'disputes',
                    'post_excerpt'  => $content_refund,
                );
                $disputes_id =  wp_insert_post($args_disputes);

                $service_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                $author_id = get_post_field('post_author', $service_id);
                update_post_meta($order_id, FELAN_METABOX_PREFIX . 'order_has_disputes_id', $disputes_id);
                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_price', $order_price);
                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_freelancer_id', $author_id);
                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', $order_id);
                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_status', 'open');

                $user_freelancer_id     = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_author_id', true);
                $user_freelancer        = get_user_by('id', $user_freelancer_id);
                $user_freelancer_email  = $user_freelancer->user_email;
                $user_freelancer_name   = $user_freelancer->display_name;
                $user_employer_id       = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_user_id', true);
                $user_employer          = get_user_by('id', $user_employer_id);
                $user_employer_name     = $user_employer->display_name;

                $felan_disputes_page_id = felan_get_option('felan_freelancer_disputes_page_id');
                $felan_disputes_page    = get_page_link($felan_disputes_page_id);


                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'service_name'    => get_the_title($service_id),
                    'dispute_url'     => $felan_disputes_page,
                );

                $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
                if ($enable_post_type_service == '1') {
                    felan_send_email($user_freelancer_email, 'mail_service_employer_create_dispute', $args_mail);
                    felan_get_data_ajax_notification($order_id, 'employer-create-dispute-service');
                }
            }

            echo json_encode(array('success' => true,));
            wp_die();
        }

        /**
         * Employer Project Order Detail
         */
        public function felan_employer_project_detail()
        {
            $project_id = isset($_REQUEST['project_id']) ? felan_clean(wp_unslash($_REQUEST['project_id'])) : '';
            $order_id = isset($_REQUEST['order_id']) ? felan_clean(wp_unslash($_REQUEST['order_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $proposal_price = isset($_REQUEST['proposal_price']) ? felan_clean(wp_unslash($_REQUEST['proposal_price'])) : '';
            $projects_budget_show = isset($_REQUEST['projects_budget_show']) ? felan_clean(wp_unslash($_REQUEST['projects_budget_show'])) : '';
            $proposal_time = isset($_REQUEST['proposal_time']) ? felan_clean(wp_unslash($_REQUEST['proposal_time'])) : '';
            $proposal_fixed_time = isset($_REQUEST['proposal_fixed_time']) ? felan_clean(wp_unslash($_REQUEST['proposal_fixed_time'])) : '';
            $proposal_rate = isset($_REQUEST['proposal_rate']) ? felan_clean(wp_unslash($_REQUEST['proposal_rate'])) : '';
            $tell_us = isset($_REQUEST['tell_us']) ? felan_clean(wp_unslash($_REQUEST['tell_us'])) : '';
            $content_refund = isset($_REQUEST['content_refund']) ? felan_clean(wp_unslash($_REQUEST['content_refund'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            if ($action_click == 'inprogress') {
                $user_freelancer_id     = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'creator_message', true);
                $user_freelancer        = get_user_by('id', $user_freelancer_id);
                $user_freelancer_email  = $user_freelancer->user_email;
                $user_freelancer_name   = $user_freelancer->display_name;
                $user_employer_id       = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'reply_message', true);
                $user_employer          = get_user_by('id', $user_employer_id);
                $user_employer_name     = $user_employer->display_name;

                $felan_project_page_id = felan_get_option('felan_my_project_page_id');
                $felan_project_page    = get_page_link($felan_project_page_id);


                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'project_name'    => get_the_title($project_id),
                    'proposal_url'    => $felan_project_page . '?applicants_id=' . $order_id . '&project_id=' . $project_id,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_freelancer_email, 'mail_project_employer_approve_proposal', $args_mail);
                    felan_get_data_ajax_notification($order_id, 'employer-approve-proposal');
                }

                if (!empty($project_id)) {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_id', $project_id);
                }

                if (!empty($order_id)) {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_id', $order_id);
                }

                if (!empty($proposal_price)) {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_price', $proposal_price);
                }

                if (!empty($projects_budget_show)) {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_projects_budget_show',  $projects_budget_show);
                }

                if (!empty($proposal_time)) {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_time',  $proposal_time);
                }

                if (!empty($proposal_fixed_time)) {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_fixed_time', $proposal_fixed_time);
                }

                if (!empty($proposal_rate)) {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_rate', $proposal_rate);
                }
            }

            if ($action_click == 'reject') {
                $user_freelancer_id     = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'creator_message', true);
                $user_freelancer        = get_user_by('id', $user_freelancer_id);
                $user_freelancer_email  = $user_freelancer->user_email;
                $user_freelancer_name   = $user_freelancer->display_name;
                $user_employer_id       = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'reply_message', true);
                $user_employer          = get_user_by('id', $user_employer_id);
                $user_employer_name     = $user_employer->display_name;

                $felan_project_page_id = felan_get_option('felan_my_project_page_id');
                $felan_project_page    = get_page_link($felan_project_page_id);


                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'project_name'    => get_the_title($project_id),
                    'proposal_url'    => $felan_project_page . '?applicants_id=' . $order_id . '&project_id=' . $project_id,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_freelancer_email, 'mail_project_employer_rejected_proposal', $args_mail);
                    felan_get_data_ajax_notification($order_id, 'employer-reject-proposal');
                }

                update_post_meta($order_id, FELAN_METABOX_PREFIX . 'proposal_status', 'reject');
            }

            if ($action_click == 'completed') {
                update_post_meta($order_id, FELAN_METABOX_PREFIX . 'proposal_status', 'completed');

                $proposal_total_price = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'proposal_total_price', true);
                if (!empty($proposal_total_price)) {
                    $author_freelancer_id = get_post_field('post_author', $order_id);
                    $withdraw_price = get_user_meta($author_freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
                    if (empty($withdraw_price)) {
                        $withdraw_price = 0;
                    }
                    $withdraw_price = $withdraw_price + $proposal_total_price;
                    update_user_meta($author_freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', $withdraw_price);
                }
            }

            if ($action_click == 'canceled') {
                update_post_meta($order_id, FELAN_METABOX_PREFIX . 'proposal_status', 'canceled');
            }

            if ($action_click == 'refund') {
                if (empty($tell_us) || empty($content_refund)) {
                    echo json_encode(array('success' => false, 'message' => esc_html('All fields need to be completed', 'felan-framework')));
                    wp_die();
                }

                $post_title = esc_html__('Others', 'felan-framework');
                if ($tell_us == 'support') {
                    $post_title = esc_html__('Customer Support', 'felan-framework');
                } elseif ($tell_us == 'code') {
                    $post_title = esc_html__('Code Quality', 'felan-framework');
                } elseif ($tell_us == 'desgin') {
                    $post_title = esc_html__('Design Quality', 'felan-framework');
                }

                $args_disputes = array(
                    'post_title'    => $post_title,
                    'post_status'   => 'publish',
                    'post_type'     => 'project_disputes',
                    'post_excerpt'  => $content_refund,
                );
                $disputes_id =  wp_insert_post($args_disputes);

                $author_freelancer_id = get_post_field('post_author', $order_id);
                update_post_meta($order_id, FELAN_METABOX_PREFIX . 'proposal_has_disputes_id', $disputes_id);
                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_price', $proposal_price);
                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_freelancer_id', $author_freelancer_id);
                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_id', $project_id);
                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_order_id', $order_id);
                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_status', 'open');

                $user_freelancer_id     = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'creator_message', true);
                $user_freelancer        = get_user_by('id', $user_freelancer_id);
                $user_freelancer_email  = $user_freelancer->user_email;
                $user_freelancer_name   = $user_freelancer->display_name;
                $user_employer_id       = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'reply_message', true);
                $user_employer          = get_user_by('id', $user_employer_id);
                $user_employer_name     = $user_employer->display_name;

                $felan_disputes_page_id = felan_get_option('felan_freelancer_disputes_page_id');
                $felan_disputes_page    = get_page_link($felan_disputes_page_id);


                $args_mail = array(
                    'employer_name'   => $user_employer_name,
                    'freelancer_name' => $user_freelancer_name,
                    'project_name'    => get_the_title($project_id),
                    'dispute_url'     => $felan_disputes_page,
                );

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_freelancer_email, 'mail_project_employer_create_dispute_proposal', $args_mail);
                    felan_get_data_ajax_notification($order_id, 'employer-create-dispute-proposal');
                }
            }

            echo json_encode(array('success' => true,));
            wp_die();
        }

        /**
         * Employer Order Detail
         */
        public function felan_employer_disputes_detail()
        {
            $disputes_id = isset($_REQUEST['disputes_id']) ? felan_clean(wp_unslash($_REQUEST['disputes_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $recipient_id = isset($_REQUEST['recipient_id']) ? felan_clean(wp_unslash($_REQUEST['recipient_id'])) : '';
            $sender_id = get_current_user_id();

            $user_employer        = get_user_by('id', $recipient_id);
            $user_employer_email  = $user_employer->user_email;
            $user_employer_name   = $user_employer->display_name;
            $user_freelancer      = get_user_by('id', $sender_id);
            $user_freelancer_name = $user_freelancer->display_name;

            $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
            $felan_disputes_page    = get_page_link($felan_disputes_page_id);
            $order_id               = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_service_order_id', true);
            $service_id             = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);

            $args_mail = array(
                'employer_name'   => $user_employer_name,
                'freelancer_name' => $user_freelancer_name,
                'service_name'    => get_the_title($service_id),
                'dispute_url'     => $felan_disputes_page . '?order_id=' . $order_id . '&disputes_id=' . $disputes_id,
            );

            if ($action_click == 'approve') {
                $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
                if ($enable_post_type_service == '1') {
                    felan_send_email($user_employer_email, 'mail_approve_dispute_service', $args_mail);
                    felan_get_data_ajax_notification($disputes_id, 'approved-dispute-service');
                }

                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_status', 'refund');
            }

            if ($action_click == 'canceled') {
                $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
                if ($enable_post_type_service == '1') {
                    felan_send_email($user_employer_email, 'mail_denies_dispute_service', $args_mail);
                    felan_get_data_ajax_notification($disputes_id, 'denies-dispute-service');
                }

                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_status', 'close');
            }

            echo json_encode(array('success' => true));
            wp_die();
        }

        /**
         * Employer Project Disputes
         */
        public function felan_project_disputes_detail()
        {
            $disputes_id = isset($_REQUEST['disputes_id']) ? felan_clean(wp_unslash($_REQUEST['disputes_id'])) : '';
            $recipient_id = isset($_REQUEST['recipient_id']) ? felan_clean(wp_unslash($_REQUEST['recipient_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $sender_id = get_current_user_id();

            $user_employer        = get_user_by('id', $recipient_id);
            $user_employer_email  = $user_employer->user_email;
            $user_employer_name   = $user_employer->display_name;
            $user_freelancer      = get_user_by('id', $sender_id);
            $user_freelancer_name = $user_freelancer->display_name;

            $felan_disputes_page_id = felan_get_option('felan_disputes_page_id');
            $felan_disputes_page    = get_page_link($felan_disputes_page_id);
            $order_id               = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_order_id', true);
            $project_id             = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);

            $args_mail = array(
                'employer_name'   => $user_employer_name,
                'freelancer_name' => $user_freelancer_name,
                'project_name'    => get_the_title($project_id),
                'dispute_url'     => $felan_disputes_page . '?listing=project&order_id=' . $order_id . '&disputes_id=' . $disputes_id,
            );

            if ($action_click == 'approve') {

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_employer_email, 'mail_approve_dispute_project', $args_mail);
                    felan_get_data_ajax_notification($disputes_id, 'approved-dispute');
                }

                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_status', 'refund');
            }

            if ($action_click == 'canceled') {

                $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
                if ($enable_post_type_project == '1') {
                    felan_send_email($user_employer_email, 'mail_denies_dispute_project', $args_mail);
                    felan_get_data_ajax_notification($disputes_id, 'denies-dispute');
                }

                update_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_status', 'close');
            }

            echo json_encode(array('success' => true));
            wp_die();
        }

        /**
         * Freelancer Order
         */
        public function felan_freelancer_order_service()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $service_search = isset($_REQUEST['service_search']) ? felan_clean(wp_unslash($_REQUEST['service_search'])) : '';
            $service_status = isset($_REQUEST['service_status']) ? felan_clean(wp_unslash($_REQUEST['service_status'])) : '';
            $sort_by = isset($_REQUEST['service_sort_by']) ? felan_clean(wp_unslash($_REQUEST['service_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();

            if (!empty($item_id)) {
                if ($action_click == 'transferring') {
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', 'transferring');
                }
                if ($action_click == 'canceled') {
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', 'canceled');
                }
            }

            $args = array(
                'post_type' => 'service_order',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'service_order_author_id',
                        'value' => $user_id,
                        'compare' => '==',
                    )
                ),
            );

            if (!empty($service_search)) {
                $args['s'] = $service_search;
            }

            if (!empty($service_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'service_order_payment_status',
                    'value' => $service_status,
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
                    global $current_user;
                    $user_id = $current_user->ID;
                    $order_id = get_the_ID();
                    $service_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                    $service_skills = get_the_terms($service_id, 'service-skills');
                    $service_categories = get_the_terms($service_id, 'service-categories');
                    $service_location = get_the_terms($service_id, 'felan_location');
                    $public_date = get_the_date(get_option('date_format'));
                    $thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
                    $service_featured = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true);
                    $author_id = get_post_field('post_author', $order_id);
                    $author_name = get_the_author_meta('display_name', $author_id);
                    $status = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', true);
                    $service_refund_content = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_refund_content', true);

                    $currency_sign_default = felan_get_option('currency_sign_default');
                    $currency_position = felan_get_option('currency_position');
                    $price_order = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price', true);
                    $price_order_number = str_replace($currency_sign_default, '', $price_order);
                    $enable_freelancer_service_fee = felan_get_option('enable_freelancer_service_fee');
                    $freelancer_number_service_fee = felan_get_option('freelancer_number_service_fee');
                    $price_fee = round(intval($price_order_number) * intval($freelancer_number_service_fee) / 100);

                    $active_date = strtotime(get_the_date('Y-m-d H:i:s'));
                    $current_time = strtotime(current_datetime()->format('Y-m-d H:i:s'));
                    $service_time_type = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_time_type', true);
                    $number_delivery_time = intval(get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_number_time', true));
                    switch ($service_time_type) {
                        case 'hr':
                            $seconds = 60 * 60;
                            break;
                        case 'day':
                            $seconds = 60 * 60 * 24;
                            break;
                        case 'week':
                            $seconds = 60 * 60 * 24 * 7;
                            break;
                        case 'month':
                            $seconds = 60 * 60 * 24 * 30;
                            break;
                    }
                    if (is_numeric($active_date) && is_numeric($seconds) && is_numeric($number_delivery_time)) {
                        $expired_time = $active_date + ($seconds * $number_delivery_time);
                    } else {
                        $expired_time = 0;
                    }

                    if ($current_time < $expired_time) {
                        $seconds = $expired_time - $current_time;
                        $dtF = new \DateTime('@0');
                        $dtT = new \DateTime("@$seconds");
                        $expired_days = $dtF->diff($dtT)->format('%a');
                        $expired_hours = $dtF->diff($dtT)->format('%h');
                        $expired_minutes = $dtF->diff($dtT)->format('%i');
                        if ($expired_days > 0) {
                            if ($expired_days === '1') {
                                $expired_date = sprintf(esc_html__('%1s day %2s hours', 'felan-framework'), $expired_days, $expired_hours);
                            } else {
                                $expired_date = sprintf(esc_html__('%1s days %2s hours', 'felan-framework'), $expired_days, $expired_hours);
                            }
                        } else {
                            if ($expired_hours === '1') {
                                $expired_date = sprintf(esc_html__('%1s hour %2s minutes', 'felan-framework'), $expired_hours, $expired_minutes);
                            } else {
                                $expired_date = sprintf(esc_html__('%1s hours %2s minutes', 'felan-framework'), $expired_hours, $expired_minutes);
                            }
                        }
                    } else {
                        $expired_date = esc_html__('Expired', 'felan-framework');
                    }
                    $status = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', true);
                    ?>
                    <tr>
                        <td>
                            <div class="service-header">
                                <?php if (!empty($thumbnail)) : ?>
                                    <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                <?php endif; ?>
                                <div class="content">
                                    <h3 class="title-my-service">
                                        <a href="<?php echo get_the_permalink($service_id) ?>">
                                            <?php echo get_the_title($service_id); ?>
                                            <?php if ($service_featured === '1') : ?>
                                                <span class="tooltip featured"
                                                    data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>"
                                                        alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                    <div class="info-service-inner">
                                        <?php echo felan_get_total_rating('service', $service_id, false); ?>
                                        <div class="count-sales">
                                            <i class="fal fa-shopping-basket"></i>
                                            <?php echo felan_service_count_sale($user_id, $service_id); ?>
                                        </div>
                                        <?php felan_total_view_service_details($service_id); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="start-time">
                            <span>
                                <?php echo esc_html__('Order date: ', 'felan-framework') ?>
                                <span class="time"><?php echo $public_date; ?></span>
                            </span>
                            <span>
                                <?php echo esc_html__('Deadline: ', 'felan-framework') ?>
                                <span class="time"><?php echo $expired_date; ?></span>
                            </span>
                        </td>
                        <td class="price">
                            <?php echo $price_order; ?>
                        </td>
                        <td class="status">
                            <?php felan_service_order_status($status); ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(felan_get_permalink('freelancer_service')); ?>?order_id=<?php echo esc_attr($order_id); ?>"
                                class="service-detail felan-button"><?php echo esc_html__('Detail', 'felan-framework') ?></a>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $service_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'service_html' => $service_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * Wallet service
         */
        public function felan_freelancer_wallet_service()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $wallet_method = isset($_REQUEST['wallet_method']) ? felan_clean(wp_unslash($_REQUEST['wallet_method'])) : '';
            $wallet_status = isset($_REQUEST['wallet_status']) ? felan_clean(wp_unslash($_REQUEST['wallet_status'])) : '';
            $wallet_sort_by = isset($_REQUEST['wallet_sort_by']) ? felan_clean(wp_unslash($_REQUEST['wallet_sort_by'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();

            $args = array(
                'post_type' => 'freelancer_withdraw',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_user_id',
                        'value' => $user_id,
                        'compare' => '==',
                    )
                ),
            );

            if (!empty($wallet_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_status',
                    'value' => $wallet_status,
                    'compare' => '=',
                );
            }

            if (!empty($wallet_method)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_payment_method',
                    'value' => $wallet_method,
                    'compare' => '=',
                );
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            if (!empty($wallet_sort_by)) {
                if ($wallet_sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($wallet_sort_by == 'oldest') {
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
                    $withdraw_id = get_the_ID();
                    $payment_method = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_payment_method', true);
                    $payment_method = str_replace(['-', '_'], ' ', $payment_method);
                    $price = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_price', true);
                    $status = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_status', true);
                    $request_date =  get_the_date(get_option('date_format'));
                    $process_date = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_process_date', true);
                    if (empty($process_date)) {
                        $process_date = '...';
                    } else {
                        $process_date = felan_convert_date_format($process_date);
                    }
                    $currency_position = felan_get_option('currency_position');
                    $currency_sign_default = felan_get_option('currency_sign_default');
                    if ($currency_position == 'before') {
                        $price = $currency_sign_default . $price;
                    } else {
                        $price = $price . $currency_sign_default;
                    }
                    ?>
                    <tr>
                        <td>
                            <?php echo $payment_method; ?>
                        </td>
                        <td>
                            <?php if ($status == 'pending') : ?>
                                <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                            <?php elseif ($status == 'canceled') : ?>
                                <span class="label label-close"><?php esc_html_e('Canceled', 'felan-framework') ?></span>
                            <?php elseif ($status == 'completed') : ?>
                                <span class="label label-open"><?php esc_html_e('Completed', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="price">
                            <?php echo $price; ?>
                        </td>
                        <td>
                            <?php echo $request_date; ?>
                        </td>
                        <td>
                            <?php echo $process_date; ?>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $wallet_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'wallet_html' => $wallet_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * Employer Wallet Dashboard
         */
        public function felan_employer_wallet_dashboard()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $wallet_method = isset($_REQUEST['wallet_method']) ? felan_clean(wp_unslash($_REQUEST['wallet_method'])) : '';
            $wallet_status = isset($_REQUEST['wallet_status']) ? felan_clean(wp_unslash($_REQUEST['wallet_status'])) : '';
            $wallet_sort_by = isset($_REQUEST['wallet_sort_by']) ? felan_clean(wp_unslash($_REQUEST['wallet_sort_by'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();

            $args = array(
                'post_type' => 'employer_withdraw',
                'paged' => $paged,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'employer_withdraw_user_id',
                        'value' => $user_id,
                        'compare' => '==',
                    )
                ),
            );

            if (!empty($wallet_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'employer_withdraw_status',
                    'value' => $wallet_status,
                    'compare' => '=',
                );
            }

            if (!empty($wallet_method)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'employer_withdraw_payment_method',
                    'value' => $wallet_method,
                    'compare' => '=',
                );
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            if (!empty($wallet_sort_by)) {
                if ($wallet_sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($wallet_sort_by == 'oldest') {
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
                    $withdraw_id = get_the_ID();
                    $payment_method = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'employer_withdraw_payment_method', true);
                    $payment_method = str_replace(['-', '_'], ' ', $payment_method);
                    $price = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'employer_withdraw_price', true);
                    $status = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'employer_withdraw_status', true);
                    $request_date =  get_the_date(get_option('date_format'));
                    $process_date = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'employer_withdraw_process_date', true);
                    if (empty($process_date)) {
                        $process_date = '...';
                    } else {
                        $process_date = felan_convert_date_format($process_date);
                    }
                    $currency_position = felan_get_option('currency_position');
                    $currency_sign_default = felan_get_option('currency_sign_default');
                    if ($currency_position == 'before') {
                        $price = $currency_sign_default . $price;
                    } else {
                        $price = $price . $currency_sign_default;
                    }
                    ?>
                    <tr>
                        <td>
                            <?php echo $payment_method; ?>
                        </td>
                        <td>
                            <?php if ($status == 'pending') : ?>
                                <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                            <?php elseif ($status == 'canceled') : ?>
                                <span class="label label-close"><?php esc_html_e('Canceled', 'felan-framework') ?></span>
                            <?php elseif ($status == 'completed') : ?>
                                <span class="label label-open"><?php esc_html_e('Completed', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="price">
                            <?php echo $price; ?>
                        </td>
                        <td>
                            <?php echo $request_date; ?>
                        </td>
                        <td>
                            <?php echo $process_date; ?>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $wallet_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'wallet_html' => $wallet_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Wallet Topup
         */
        public function felan_my_wallet_topup()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $wallet_method = isset($_REQUEST['wallet_method']) ? felan_clean(wp_unslash($_REQUEST['wallet_method'])) : '';
            $wallet_status = isset($_REQUEST['wallet_status']) ? felan_clean(wp_unslash($_REQUEST['wallet_status'])) : '';
            $wallet_sort_by = isset($_REQUEST['wallet_sort_by']) ? felan_clean(wp_unslash($_REQUEST['wallet_sort_by'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();

            $args = array(
                'post_type' => 'wallet_order',
                'paged' => $paged,
                'post_status' => 'publish',
                'author' => $user_id,
            );

            if (!empty($wallet_status)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'wallet_order_payment_status',
                    'value' => $wallet_status,
                    'compare' => '=',
                );
            }

            if (!empty($wallet_method)) {
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'wallet_order_payment_method',
                    'value' => $wallet_method,
                    'compare' => '=',
                );
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            if (!empty($wallet_sort_by)) {
                if ($wallet_sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($wallet_sort_by == 'oldest') {
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
                    $wallet_id = get_the_ID();
                    $payment_method = get_post_meta($wallet_id, FELAN_METABOX_PREFIX . 'wallet_order_payment_method', true);
                    $payment_method = str_replace(['-', '_'], ' ', $payment_method);
                    $status = get_post_meta($wallet_id, FELAN_METABOX_PREFIX . 'wallet_order_payment_status', true);
                    $price = get_post_meta($wallet_id, FELAN_METABOX_PREFIX . 'wallet_order_price', true);
                    $currency_sign_default = felan_get_option('currency_sign_default');
                    $currency_position = felan_get_option('currency_position');
                    $currency_position = felan_get_option('currency_position');
                    $date =  get_the_date(get_option('date_format'));
                    if ($currency_position == 'before') {
                        $price_order = $currency_sign_default . $price;
                    } else {
                        $price_order = $price . $currency_sign_default;
                    }
                    ?>
                    <tr>
                        <td>
                            <?php echo '#' . esc_html($wallet_id); ?>
                        </td>
                        <td>
                            <?php echo esc_html($payment_method); ?>
                        </td>
                        <td>
                            <?php if ($status == 'pending') : ?>
                                <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                            <?php elseif ($status == 'approve') : ?>
                                <span class="label label-open"><?php esc_html_e('Completed', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="price">
                            <?php echo esc_html($price_order); ?>
                        </td>
                        <td>
                            <?php echo esc_html($date); ?>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $wallet_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'wallet_html' => $wallet_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Submit withdraw
         */
        public function felan_submit_withdraw()
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
            $enable_freelancer_withdrawal_fee = felan_get_option('enable_freelancer_withdrawal_fee', '1');
            $freelancer_number_withdrawal_fee = felan_get_option('freelancer_number_withdrawal_fee');

            $args_withdraw = array(
                'post_type' => 'freelancer_withdraw',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'author' => $user_id,
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_status',
                        'value' => 'pending',
                        'compare' => '==',
                    )
                ),
            );
            $withdraw_ids = get_posts($args_withdraw);
            $total_withdraw_price = 0;
            if (!empty($withdraw_ids)) {
                foreach ($withdraw_ids as $withdraw_id) {
                    $freelancer_withdraw_price = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_price', true);
                    $total_withdraw_price += intval($freelancer_withdraw_price);
                }
            }

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

            if ($withdraw_price > (intval($total_price) - ($total_withdraw_price))) {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('The total amount of your requested withdrawal exceeds your available balance.', 'felan-framework')
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

            if ($enable_freelancer_withdrawal_fee == '1' || !empty($freelancer_number_withdrawal_fee) || $freelancer_number_withdrawal_fee !== '0') {
                $price_fee = intval($withdraw_price) * intval($freelancer_number_withdrawal_fee) / 100;
                $withdraw_price = intval($withdraw_price) - $price_fee;
            }

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
				update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_enable_freelancer_withdrawal_fee', $enable_freelancer_withdrawal_fee);
				if ($enable_freelancer_withdrawal_fee == '1' || !empty($freelancer_number_withdrawal_fee) || $freelancer_number_withdrawal_fee !== '0') {
					update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdrawal_fee', $price_fee);
				} else {
					update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdrawal_fee', 0);
				}
            }

            echo json_encode(array('success' => true, '$withdraw_price' => $withdraw_price));

            wp_die();
        }


        /**
         * Employer Submit withdraw
         */
        public function felan_employer_submit_withdraw()
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

            $args_withdraw = array(
                'post_type' => 'employer_withdraw',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'author' => $user_id,
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'employer_withdraw_status',
                        'value' => 'pending',
                        'compare' => '==',
                    )
                ),
            );
            $withdraw_ids = get_posts($args_withdraw);
            $total_withdraw_price = 0;
            if (!empty($withdraw_ids)) {
                foreach ($withdraw_ids as $withdraw_id) {
                    $employer_withdraw_price = get_post_meta($withdraw_id, FELAN_METABOX_PREFIX . 'employer_withdraw_price', true);
                    $total_withdraw_price += intval($employer_withdraw_price);
                }
            }

            $total_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
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

            if ($withdraw_price > (intval($total_price) - ($total_withdraw_price))) {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('The total amount of your requested withdrawal exceeds your available balance.', 'felan-framework')
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

            $new_post = array(
                'post_type' => 'employer_withdraw',
                'post_status' => 'publish',
            );
            $post_title = $user_name;
            if (isset($post_title)) {
                $new_post['post_title'] = $post_title;
                $post_id = wp_insert_post($new_post, true);
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'user_total_price_withdraw', $total_price);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_status', 'pending');
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_user_id', $user_id);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_payment_method', $withdraw_payment);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_price', $withdraw_price);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', $total_price);
            }

            echo json_encode(array('success' => true, '$withdraw_price' => $withdraw_price));

            wp_die();
        }


        /**
         * Applicants Filter
         */
        public function felan_filter_applicants_dashboard()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $applicants_search = isset($_REQUEST['applicants_search']) ? felan_clean(wp_unslash($_REQUEST['applicants_search'])) : '';
            $sort_by = isset($_REQUEST['applicants_sort_by']) ? felan_clean(wp_unslash($_REQUEST['applicants_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $jobs_id = isset($_REQUEST['applicants_jobs_id']) ? felan_clean(wp_unslash($_REQUEST['applicants_jobs_id'])) : '';
            $filter_jobs = isset($_REQUEST['applicants_filter_jobs']) ? felan_clean(wp_unslash($_REQUEST['applicants_filter_jobs'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $meta_query = array();
            $tax_query = array();

            if (!empty($item_id)) {
                if ($action_click == 'approved') {
                    $email_freelancer = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'applicants_email', true);
                    $employer_name = get_the_author_meta('display_name', $user_id);
                    $args_mail = array(
                        'employer_name' => $employer_name,
                        'jobs_apply' => get_the_title($jobs_id),
                        'jobs_url' => get_permalink($jobs_id),
                    );

                    if (empty($email_freelancer)) {
                        $freelancer_id = get_post_field('post_author', $item_id);
                        $freelancer_obj = get_user_by('id', $freelancer_id);
                        $email_freelancer = $freelancer_obj->user_email;
                    }

                    felan_send_email($email_freelancer, 'mail_approved_applicants', $args_mail);

                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'applicants_status', 'approved');
                }

                if ($action_click == 'rejected') {
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'applicants_status', 'rejected');
                }
            }

            if (!empty($jobs_id)) {
                $jobs_employer_id = $jobs_id;
                $args_applicants = array(
                    'post_type' => 'applicants',
                    'ignore_sticky_posts' => 1,
                    'paged' => $paged,
                );
                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                    'value' => $jobs_id,
                    'compare' => '='
                );
                if (!empty($applicants_search)) {
                    $meta_query[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'applicants_author',
                        'value' => $applicants_search,
                        'compare' => '='
                    );
                }
            } else {
                $args_jobs = array(
                    'post_type' => 'jobs',
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'posts_per_page' => -1,
                    'author' => $user_id,
                    'orderby' => 'date',
                );
                $data_jobs = new WP_Query($args_jobs);
                $jobs_employer_id = array();
                if ($data_jobs->have_posts()) {
                    while ($data_jobs->have_posts()) : $data_jobs->the_post();
                        $jobs_employer_id[] = get_the_ID();
                    endwhile;
                }

                $args_applicants = array(
                    'post_type' => 'applicants',
                    'ignore_sticky_posts' => 1,
                    'paged' => $paged,
                );

                $meta_query[] = array(
                    'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                    'value' => $jobs_employer_id,
                    'compare' => 'IN'
                );

                if (!empty($applicants_search)) {
                    $args_applicants['s'] = $applicants_search;
                }

                if (!empty($filter_jobs)) {
                    $args_applicants['title'] = $filter_jobs;
                }
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
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0 && !empty($jobs_employer_id)) {

                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    $id = get_the_ID();
                    global $current_user;
                    wp_get_current_user();
                    $user_id = $current_user->ID;
                    $public_date = get_the_date(get_option('date_format'));
                    $jobs_id = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_jobs_id', true);
                    $applicants_email = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_email', true);
                    $applicants_phone = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_phone', true);
                    $applicants_message = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_message', true);
                    $applicants_cv = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_cv', true);
                    $applicants_status = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_status', true);
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
                                        <a href="<?php echo esc_url(get_permalink($jobs_id)); ?>" target="_blank">
                                            <span> <?php esc_html_e(get_the_title()); ?></span>
                                            <i class="far fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </td>
                        <td class="status">
                            <div class="approved">
                                <?php echo felan_applicants_status($id); ?>
                                <span class="applied-time"><?php esc_html_e('Applied:', 'felan-framework') ?><?php esc_html_e($public_date) ?></span>
                            </div>
                        </td>
                        <td class="info">
                            <?php if (!empty($applicants_email)) { ?>
                                <span class="gmail"><?php esc_html_e($applicants_email) ?></span>
                            <?php } ?>
                            <?php if (!empty($applicants_phone)) { ?>
                                <span class="phone"><?php esc_html_e($applicants_phone) ?></span>
                            <?php } ?>
                        </td>
                        <td class="applicants-control action-setting">
                            <div class="list-action">
                                <?php if (!empty(get_the_author())) { ?>
                                    <a href="#" class="action icon-video tooltip btn-reschedule-meetings" data-id="<?php echo esc_attr($id); ?>" data-title="<?php esc_attr_e('Meetings', 'felan-framework') ?>"><i class="far fa-video-plus"></i></a>
                                    <?php if ($reply_mess !== 'yes') : ?>
                                        <a href="#" class="action icon-messages tooltip" id="btn-mees-applicants" data-apply="<?php esc_html_e(get_the_title()); ?>" data-id="<?php echo esc_attr($id); ?>" data-mess="<?php echo $applicants_message; ?>" data-jobs-id="<?php echo $jobs_id; ?>" data-title="<?php esc_attr_e('Messages', 'felan-framework') ?>">
                                            <i class="far fa-comment-dots <?php if ($read_mess === 'yes') {
                                                                                echo 'active';
                                                                            } ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php } ?>
                                <a href="<?php echo esc_url($applicants_cv); ?>" class="action icon-download tooltip" data-title="<?php esc_attr_e('Download CV', 'felan-framework') ?>"><i class="far fa-download"></i></a>
                                <div class="action">
                                    <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                                    <ul class="action-dropdown">
                                        <?php if (empty($applicants_status)) { ?>
                                            <li><a class="btn-approved" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Approved', 'felan-framework') ?></a></li>
                                            <li><a class="btn-rejected" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Rejected', 'felan-framework') ?></a></li>
                                            <?php } else {
                                            if ($applicants_status == 'approved') { ?>
                                                <li><a class="btn-rejected" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Rejected', 'felan-framework') ?></a>
                                                </li>
                                                <li><a class="btn-action-review" freelancer-id="<?php echo $freelancer_id; ?>" href="#"><?php esc_html_e('Review', 'felan-framework') ?></a></li>
                                            <?php } else { ?>
                                                <li><a class="btn-approved" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Approved', 'felan-framework') ?></a>
                                                </li>
                                        <?php }
                                        } ?>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $applicants_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'applicants_html' => $applicants_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * Read mess
         */
        public function felan_read_mess_ajax_load()
        {
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';

            update_post_meta($item_id, FELAN_METABOX_PREFIX . 'read_mess', 'yes');

            wp_die();
        }

        /**
         * Realy mess
         */
        public function felan_realy_mess_ajax_load()
        {
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $title = isset($_REQUEST['title']) ? felan_clean(wp_unslash($_REQUEST['title'])) : '';
            $content = isset($_REQUEST['content']) ? felan_clean(wp_unslash($_REQUEST['content'])) : '';
            $jobs_id = isset($_REQUEST['jobs_id']) ? felan_clean(wp_unslash($_REQUEST['jobs_id'])) : '';

            $new_messages = array(
                'post_type' => 'messages',
                'post_status' => 'publish',
            );

            if (isset($title)) {
                $new_messages['post_title'] = $title;
            }

            if (isset($content)) {
                $new_messages['post_excerpt'] = $content;
            }

            if (!empty($new_messages['post_title'])) {
                $messages_id = wp_insert_post($new_messages, true);
            }

            $reply_message = get_post_field('post_author', $jobs_id);
            $creator_message = get_post_field('post_author', $item_id);

            felan_get_data_ajax_notification($item_id, 'add-message');

            if (isset($messages_id)) {
                update_post_meta($messages_id, FELAN_METABOX_PREFIX . 'creator_message', $creator_message);
                update_post_meta($messages_id, FELAN_METABOX_PREFIX . 'recipient_message', $jobs_id);
                update_post_meta($messages_id, FELAN_METABOX_PREFIX . 'reply_message', $reply_message);
                update_post_meta($item_id, FELAN_METABOX_PREFIX . 'reply_mess', 'yes');
            }

            echo json_encode(array('success' => true));

            wp_die();
        }


        /**
         * Realy mess
         */
        public function felan_realy_mess_project_ajax_load()
        {
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $title = isset($_REQUEST['title']) ? felan_clean(wp_unslash($_REQUEST['title'])) : '';
            $content = isset($_REQUEST['content']) ? felan_clean(wp_unslash($_REQUEST['content'])) : '';
            $project_id = isset($_REQUEST['project_id']) ? felan_clean(wp_unslash($_REQUEST['project_id'])) : '';

            $new_messages = array(
                'post_type' => 'messages',
                'post_status' => 'publish',
            );

            if (isset($title)) {
                $new_messages['post_title'] = $title;
            }

            if (isset($content)) {
                $new_messages['post_excerpt'] = $content;
            }

            if (!empty($new_messages['post_title'])) {
                $messages_id = wp_insert_post($new_messages, true);
            }

            $reply_message = get_post_field('post_author', $project_id);
            $creator_message = get_post_field('post_author', $item_id);

            felan_get_data_ajax_notification($item_id, 'add-message');

            if (isset($messages_id)) {
                update_post_meta($messages_id, FELAN_METABOX_PREFIX . 'creator_message', $creator_message);
                update_post_meta($messages_id, FELAN_METABOX_PREFIX . 'recipient_message', $project_id);
                update_post_meta($messages_id, FELAN_METABOX_PREFIX . 'reply_message', $reply_message);
                update_post_meta($item_id, FELAN_METABOX_PREFIX . 'reply_mess', 'yes');
            }

            echo json_encode(array('success' => true));

            wp_die();
        }


        /**
         * Project My Wishlist
         */
        public function felan_filter_project_my_wishlist()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $project_search = isset($_REQUEST['project_search']) ? felan_clean(wp_unslash($_REQUEST['project_search'])) : '';
            $sort_by = isset($_REQUEST['project_sort_by']) ? felan_clean(wp_unslash($_REQUEST['project_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $meta_query = array();
            $tax_query = array();

            $my_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_wishlist', true);
            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    $key = array_search($item_id, $my_wishlist);
                    if ($key !== false) {
                        unset($my_wishlist[$key]);
                    }
                }
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_wishlist', $my_wishlist);

            $args = array(
                'post_type' => 'project',
                'paged' => $paged,
                'post__in' => $my_wishlist,
                'ignore_sticky_posts' => 1,
            );

            if (!empty($my_wishlist)) {
                $args['post__in'] = $my_wishlist;
            } else {
                $args['post__in'] = array(0);
            }

            if (!empty($project_search)) {
                $args['s'] = $project_search;
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
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0) {

                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    $project_id = get_the_ID();
                    global $current_user;
                    wp_get_current_user();
                    $user_id = $current_user->ID;
                    $project_skills = get_the_terms($project_id, 'project-skills');
                    $project_categories = get_the_terms($project_id, 'project-categories');
                    $project_location = get_the_terms($project_id, 'felan_location');
                    $thumbnail = get_the_post_thumbnail_url($project_id, '70x70');
                    $author_id = get_post_field('post_author', $project_id);
                    $author_name = get_the_author_meta('display_name', $author_id);
                    $public_date = get_the_date(get_option('date_format'));
                    $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                    ?>
                    <tr>
                        <td>
                            <div class="project-header">
                                <?php if (!empty($thumbnail)) : ?>
                                    <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                <?php endif; ?>
                                <div class="info-project">
                                    <h3 class="title-project-dashboard">
                                        <a href="<?php echo get_the_permalink($project_id); ?>">
                                            <?php echo get_the_title($project_id); ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <?php if (is_array($project_categories)) {
                                            foreach ($project_categories as $categories) { ?>
                                                <span class="cate"><?php esc_html_e($categories->name); ?></span>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($project_skills)) {
                                            foreach ($project_skills as $skills) { ?>
                                                <?php esc_html_e('/ ' . $skills->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($project_location)) {
                                            foreach ($project_location as $location) { ?>
                                                <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                        } ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="author">
                            <?php echo $author_name; ?>
                        </td>
                        <td class="table-time">
                            <span class="start-time"><?php echo $public_date ?></span>
                        </td>
                        <?php
                        ?>
                        <td class="action-setting project-control">
                            <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                            <ul class="action-dropdown">
                                <?php if ($user_demo == 'yes') : ?>
                                    <li><a class="btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                                <?php else : ?>
                                    <li><a class="btn-delete" project-id="<?php echo esc_attr($project_id); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                                <?php endif; ?>
                            </ul>
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
         * My Wishlist
         */
        public function felan_filter_my_wishlist()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $jobs_search = isset($_REQUEST['jobs_search']) ? felan_clean(wp_unslash($_REQUEST['jobs_search'])) : '';
            $sort_by = isset($_REQUEST['jobs_sort_by']) ? felan_clean(wp_unslash($_REQUEST['jobs_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $meta_query = array();
            $tax_query = array();

            $my_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_wishlist', true);
            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    $key = array_search($item_id, $my_wishlist);
                    if ($key !== false) {
                        unset($my_wishlist[$key]);
                    }
                }
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_wishlist', $my_wishlist);

            $args = array(
                'post_type' => 'jobs',
                'paged' => $paged,
                'post__in' => $my_wishlist,
                'ignore_sticky_posts' => 1,
            );

            if (!empty($my_wishlist)) {
                $args['post__in'] = $my_wishlist;
            } else {
                $args['post__in'] = array(0);
            }

            if (!empty($jobs_search)) {
                $args['s'] = $jobs_search;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query jobs sort_by
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
                    $jobs_type = get_the_terms($id, 'jobs-type');
                    $jobs_categories = get_the_terms($id, 'jobs-categories');
                    $jobs_location = get_the_terms($id, 'felan_location');
                    $jobs_select_company = get_post_meta($id, FELAN_METABOX_PREFIX . 'jobs_select_company');
                    $company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
                    $company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
                    $public_date = get_the_date('Y-m-d');
                    ?>
                    <tr>
                        <td>
                            <div class="company-header">
                                <div class="img-comnpany">
                                    <?php if (!empty($company_logo[0]['url'])) : ?>
                                        <img class="logo-company" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                                    <?php else : ?>
                                        <div class="logo-company"><i class="far fa-camera"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="info-jobs">
                                    <h3 class="title-jobs-dashboard">
                                        <a href="<?php echo get_the_permalink($id); ?>">
                                            <?php echo get_the_title($id); ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <?php if (is_array($jobs_categories)) {
                                            foreach ($jobs_categories as $categories) { ?>
                                                <?php esc_html_e($categories->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($jobs_type)) {
                                            foreach ($jobs_type as $type) { ?>
                                                <?php esc_html_e('/ ' . $type->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($jobs_location)) {
                                            foreach ($jobs_location as $location) { ?>
                                                <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                        } ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="table-time">
                            <span class="start-time"><?php echo $public_date ?></span>
                        </td>
                        <?php
                        ?>
                        <td class="action-setting jobs-control">
                            <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                            <ul class="action-dropdown">
                                <li><a class="btn-delete" jobs-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                            </ul>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $jobs_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'jobs_html' => $jobs_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Employer Wishlist
         */
        public function felan_filter_employer_wishlist()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $service_search = isset($_REQUEST['service_search']) ? felan_clean(wp_unslash($_REQUEST['service_search'])) : '';
            $sort_by = isset($_REQUEST['service_sort_by']) ? felan_clean(wp_unslash($_REQUEST['service_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
            $meta_query = array();
            $tax_query = array();

            $service_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_wishlist', true);
            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    $key = array_search($item_id, $service_wishlist);
                    if ($key !== false) {
                        unset($service_wishlist[$key]);
                    }
                }
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_wishlist', $service_wishlist);

            $args = array(
                'post_type' => 'service',
                'paged' => $paged,
                'ignore_sticky_posts' => 1,
            );

            if (!empty($service_wishlist)) {
                $args['post__in'] = $service_wishlist;
            } else {
                $args['post__in'] = array(0);
            }

            if (!empty($service_search)) {
                $args['s'] = $service_search;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query service sort_by
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
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0) {

                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    $service_id = get_the_ID();
                    $service_skills = get_the_terms($service_id, 'service-skills');
                    $service_categories = get_the_terms($service_id, 'service-categories');
                    $service_location = get_the_terms($service_id, 'felan_location');
                    $public_date = get_the_date(get_option('date_format'));
                    $thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
                    $author_id = get_post_field('post_author', $service_id);
                    $author_name = get_the_author_meta('display_name', $author_id);
                    $args_freelancer = array(
                        'post_type' => 'freelancer',
                        'posts_per_page' => 1,
                        'author' => $author_id,
                    );
                    $current_user_posts = get_posts($args_freelancer);
                    $freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
                    $service_featured = intval(get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true));

                    $currency_sign_default = felan_get_option('currency_sign_default');
                    $number_start_price = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_price', true);
                    $start_price = felan_get_format_money($number_start_price, '', 0);
                    ?>
                    <tr>
                        <td>
                            <div class="service-header">
                                <?php if (!empty($thumbnail)) : ?>
                                    <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                <?php endif; ?>
                                <div class="content">
                                    <h3 class="title-my-service">
                                        <a href="<?php echo get_the_permalink($service_id) ?>">
                                            <?php echo get_the_title($service_id); ?>
                                            <?php if ($service_featured == 1) : ?>
                                                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <?php if (is_array($service_categories)) {
                                            foreach ($service_categories as $categories) { ?>
                                                <span class="cate"><?php esc_html_e($categories->name); ?></span>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($service_skills)) {
                                            foreach ($service_skills as $skills) { ?>
                                                <?php esc_html_e('/ ' . $skills->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($service_location)) {
                                            foreach ($service_location as $location) { ?>
                                                <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                        } ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="author">
                            <a href="<?php echo get_post_permalink($freelancer_id) ?>"><?php echo $author_name; ?></a>
                        </td>
                        <td class="price">
                            <?php echo $start_price; ?>
                        </td>
                        <td class="start-time">
                            <?php echo $public_date; ?>
                        </td>
                        <td class="action-setting service-control">
                            <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                            <ul class="action-dropdown">
                                <?php if ($user_demo == 'yes') : ?>
                                    <li><a class="btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                                <?php else : ?>
                                    <li><a class="btn-delete" service-id="<?php echo esc_attr($service_id); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                                <?php endif; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $service_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'service_html' => $service_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * My Follow
         */
        public function felan_filter_my_follow()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $company_search = isset($_REQUEST['company_search']) ? felan_clean(wp_unslash($_REQUEST['company_search'])) : '';
            $sort_by = isset($_REQUEST['company_sort_by']) ? felan_clean(wp_unslash($_REQUEST['company_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $meta_query = array();
            $tax_query = array();

            $my_follow = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_follow', true);
            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    $key = array_search($item_id, $my_follow);
                    if ($key !== false) {
                        unset($my_follow[$key]);
                    }
                }
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_follow', $my_follow);

            $args = array(
                'post_type' => 'company',
                'paged' => $paged,
                'post__in' => $my_follow,
                'ignore_sticky_posts' => 1,
            );

            if (!empty($company_search)) {
                $args['s'] = $company_search;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query company sort_by
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
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0) {

                while ($data->have_posts()) : $data->the_post();
                    $id = get_the_ID();
                    global $current_user;
                    wp_get_current_user();
                    $user_id = $current_user->ID;
                    $company_categories = get_the_terms($id, 'company-categories');
                    $company_location = get_the_terms($id, 'felan_location');
                    $company_logo = get_post_meta($id, FELAN_METABOX_PREFIX . 'company_logo');
                    $public_date = get_the_date('Y-m-d');
                ?>
                    <tr>
                        <td>
                            <div class="company-header">
                                <div class="img-comnpany">
                                    <?php if (!empty($company_logo[0]['url'])) : ?>
                                        <img class="logo-company" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                                    <?php else : ?>
                                        <div class="logo-company"><i class="far fa-camera"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="info-company">
                                    <h3 class="title-company-dashboard">
                                        <a href="<?php echo get_the_permalink($id); ?>">
                                            <?php echo get_the_title($id); ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <?php if (is_array($company_categories)) {
                                            foreach ($company_categories as $categories) { ?>
                                                <?php esc_html_e($categories->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($company_location)) {
                                            foreach ($company_location as $location) { ?>
                                                <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                        } ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="table-time">
                            <span class="start-time"><?php echo $public_date ?></span>
                        </td>
                        <?php
                        ?>
                        <td class="action-setting company-control">
                            <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                            <ul class="action-dropdown">
                                <li><a class="btn-delete" company-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                            </ul>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $company_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'company_html' => $company_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * My Invite
         */
        public function felan_filter_my_invite()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $jobs_search = isset($_REQUEST['jobs_search']) ? felan_clean(wp_unslash($_REQUEST['jobs_search'])) : '';
            $sort_by = isset($_REQUEST['jobs_sort_by']) ? felan_clean(wp_unslash($_REQUEST['jobs_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $meta_query = array();
            $tax_query = array();

            $my_invite = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_invite', true);
            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    $key = array_search($item_id, $my_invite);
                    if ($key !== false) {
                        unset($my_invite[$key]);
                    }
                }
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_invite', $my_invite);

            $args = array(
                'post_type' => 'jobs',
                'paged' => $paged,
                'post__in' => $my_invite,
                'ignore_sticky_posts' => 1,
            );

            if (!empty($jobs_search)) {
                $args['s'] = $jobs_search;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query jobs sort_by
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
                    $jobs_type = get_the_terms($id, 'jobs-type');
                    $jobs_categories = get_the_terms($id, 'jobs-categories');
                    $jobs_location = get_the_terms($id, 'felan_location');
                    $company_logo = get_post_meta($id, FELAN_METABOX_PREFIX . 'company_logo');
                    $public_date = get_the_date('Y-m-d');
                    ?>
                    <tr>
                        <td>
                            <div class="company-header">
                                <div class="img-comnpany">
                                    <?php if (!empty($company_logo[0]['url'])) : ?>
                                        <img class="logo-company" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                                    <?php else : ?>
                                        <div class="logo-company"><i class="far fa-camera"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="info-jobs">
                                    <h3 class="title-jobs-dashboard">
                                        <a href="<?php echo get_the_permalink($id); ?>">
                                            <?php echo get_the_title($id); ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <?php if (is_array($jobs_categories)) {
                                            foreach ($jobs_categories as $categories) { ?>
                                                <?php esc_html_e($categories->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($jobs_type)) {
                                            foreach ($jobs_type as $type) { ?>
                                                <?php esc_html_e('/ ' . $type->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($jobs_location)) {
                                            foreach ($jobs_location as $location) { ?>
                                                <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                        } ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="table-time">
                            <span class="start-time"><?php echo $public_date ?></span>
                        </td>
                        <?php
                        ?>
                        <td class="action-setting jobs-control">
                            <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                            <ul class="action-dropdown">
                                <li><a class="btn-apply" href="<?php echo get_the_permalink($id); ?>" target="_blank"><?php esc_html_e('Apply Now', 'felan-framework') ?></a></li>
                                <li><a class="btn-delete" jobs-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                            </ul>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $jobs_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'jobs_html' => $jobs_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * My Review
         */
        public function felan_filter_my_review()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $company_search = isset($_REQUEST['company_search']) ? felan_clean(wp_unslash($_REQUEST['company_search'])) : '';
            $sort_by = isset($_REQUEST['company_sort_by']) ? felan_clean(wp_unslash($_REQUEST['company_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user, $wpdb;
            wp_get_current_user();
            $user_id = $current_user->ID;

            if (!empty($item_id) && $action_click == 'delete') {
                wp_delete_comment($item_id, $force_delete = true);
            }

            $my_reviews = $wpdb->get_results("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.user_id = $user_id AND meta.meta_key = 'company_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC LIMIT 999");
            $company_ids = array();
            foreach ($my_reviews as $my_review) {
                $company_ids[] = $my_review->comment_post_ID;
            }
            $args = array(
                'post_type' => 'company',
                'paged' => $paged,
                'post__in' => $company_ids,
                'ignore_sticky_posts' => 1,
            );

            if (!empty($company_search)) {
                $args['s'] = $company_search;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query company sort_by
            if (!empty($sort_by)) {
                if ($sort_by == 'newest') {
                    $args['order'] = 'DESC';
                }
                if ($sort_by == 'oldest') {
                    $args['order'] = 'ASC';
                }
            }

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0 && !empty($company_ids)) {

                while ($data->have_posts()) : $data->the_post();
                    $company_id = get_the_ID();
                    $comment = get_comments(array(
                        'post_id' => $company_id,
                    ));
                    $comment_id = '';
                    if (!empty($comment)) {
                        $comment_id = $comment[0]->comment_ID;
                    }
                    $company_categories = get_the_terms($company_id, 'company-categories');
                    $company_location = get_the_terms($company_id, 'felan_location');
                    $company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
                    $rating = get_comment_meta($comment_id, 'company_rating', true);
                ?>
                    <tr>
                        <td>
                            <div class="company-header">
                                <div class="img-comnpany">
                                    <?php if (!empty($company_logo[0]['url'])) : ?>
                                        <img class="logo-company" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                                    <?php else : ?>
                                        <div class="logo-company"><i class="far fa-camera"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="info-company">
                                    <h3 class="title-company-dashboard">
                                        <a href="<?php echo get_the_permalink($company_id); ?>">
                                            <?php echo get_the_title($company_id) ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <?php if (is_array($company_categories)) {
                                            foreach ($company_categories as $categories) { ?>
                                                <?php esc_html_e($categories->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($company_location)) {
                                            foreach ($company_location as $location) { ?>
                                                <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                        } ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="rating-count">
                                <i class="fas fa-star"></i>
                                <span><?php esc_html_e($rating); ?></span>
                            </span>
                        </td>
                        <td>
                            <?php echo get_comment_date('Y-m-d', $comment_id); ?>
                        </td>
                        <td class="action-setting company-control">
                            <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                            <ul class="action-dropdown">
                                <li><a class="btn-edit" href="<?php echo get_the_permalink($company_id) . '/#company-review-details' ?>"><?php esc_html_e('Edit', 'felan-framework') ?></a>
                                </li>
                                <li><a class="btn-delete" comment-id="<?php echo esc_attr($comment_id); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                            </ul>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $company_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'company_html' => $company_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Filter Company Dashboard
         */
        public function felan_filter_company_dashboard()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $felan_profile = new Felan_Profile();

            $meta_query = array();
            if (!empty($item_id)) {
                $company = get_post($item_id);
                if ($action_click == 'delete') {
                    $item_author_id = get_post_field('post_author', $item_id);
                    if ($user_id == $item_author_id) {
                        wp_delete_post($item_id, true);
                    }
                }
            }

            $args = array(
                'post_type' => 'company',
                'paged' => $paged,
                'post_status' => array('publish', 'pending'),
                'ignore_sticky_posts' => 1,
                'author' => $user_id,
                'orderby' => 'date',
            );

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

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

                while ($data->have_posts()) : $data->the_post();
                    $id = get_the_ID();
                    $company_location = get_the_terms($id, 'felan_location');
                    $status = get_post_status($id);
                    $company_categories = get_the_terms($id, 'company-categories');
                    $company_logo = get_post_meta($id, FELAN_METABOX_PREFIX . 'company_logo');
                    $meta_company = felan_posts_company($id);
                    $company_dashboard_link = felan_get_permalink('company_dashboard');
                ?>
                    <tr>
                        <td class="info-user">
                            <?php
                            if (!empty($company_logo[0]['url'])) { ?>
                                <a href="<?php echo get_the_permalink($id); ?>">
                                    <img src="<?php echo $company_logo[0]['url'] ?>" alt="<?php echo get_the_title() ?>">
                                </a>
                            <?php } else { ?>
                                <div class="img-company"><i class="far fa-camera"></i></div>
                            <?php } ?>
                            <div class="info-details">
                                <h3><?php echo get_the_title() ?></h3>
                                <p>
                                    <?php if (is_array($company_location)) : ?>
                                        <?php foreach ($company_location as $location) { ?>
                                            <span><?php echo $location->name; ?></span>
                                        <?php } ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </td>
                        <td>
                            <?php if ($status == 'publish') : ?>
                                <span class="label label-open"><?php esc_html_e('Opening', 'felan-framework') ?></span>
                            <?php endif; ?>
                            <?php if ($status == 'pending') : ?>
                                <span class="label label-pending"><?php esc_html_e('Pending', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="cate">
                                <?php if (is_array($company_categories)) : ?>
                                    <?php foreach ($company_categories as $categories) { ?>
                                        <span><?php echo $categories->name; ?></span>
                                    <?php } ?>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <span class="active-jobs"><?php echo $meta_company->post_count ?></span>
                        </td>
                        <td class="action-setting company-control">
                            <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                            <ul class="action-dropdown">
                                <li><a class="btn-edit" href="<?php echo esc_url($company_dashboard_link); ?>?company_id=<?php echo esc_attr($id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a>
                                </li>
                                <li><a class="btn-delete" company-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                            </ul>
                        </td>
                    </tr>
                <?php
                endwhile;
            }
            wp_reset_postdata();

            $company_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'company_html' => $company_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * Company Related
         */
        public function felan_company_related()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '4';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $company_id = isset($_REQUEST['company_id']) ? felan_clean(wp_unslash($_REQUEST['company_id'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            $args = array(
                'post_type' => 'jobs',
                'post_status' => 'publish',
                'paged' => $paged,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'jobs_select_company',
                        'value' => $company_id,
                        'compare' => '=='
                    )
                ),

            );

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            $related = get_posts($args);
            $wp_query = new WP_Query($args);
            $total_post = $wp_query->found_posts;
            $max_num_pages = $wp_query->max_num_pages;
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

                foreach ($related as $relateds) { ?>
                    <?php felan_get_template('content-jobs.php', array(
                        'jobs_id' => $relateds->ID,
                        'jobs_layout' => 'layout-list',
                    )); ?>
                <?php }
            }
            wp_reset_postdata();

            $company_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'company_html' => $company_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Filter Freelancers Dashboard
         */
        public function felan_filter_freelancers_dashboard()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $freelancers_search = isset($_REQUEST['freelancers_search']) ? felan_clean(wp_unslash($_REQUEST['freelancers_search'])) : '';
            $sort_by = isset($_REQUEST['freelancers_sort_by']) ? felan_clean(wp_unslash($_REQUEST['freelancers_sort_by'])) : '';
            $freelancers_id = isset($_REQUEST['freelancers_id']) ? felan_clean(wp_unslash($_REQUEST['freelancers_id'])) : '';
            $follow_company = isset($_REQUEST['follow_company']) ? felan_clean(wp_unslash($_REQUEST['follow_company'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $author_id = isset($_REQUEST['author_id']) ? felan_clean(wp_unslash($_REQUEST['author_id'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $meta_query = array();
            $freelancers_id = explode(',', $freelancers_id);

            $my_follow = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'my_follow', true);
            if ($action_click == 'delete') {
                if (!empty($follow_company)) {
                    foreach ($my_follow as $key => $value) {
                        if (in_array($value, $my_follow)) {
                            unset($my_follow[$key]);
                        }
                    }
                }
                if (!empty($item_id && !empty($freelancers_id))) {
                    $key = array_search($item_id, $freelancers_id);
                    if ($key !== false) {
                        unset($freelancers_id[$key]);
                    }
                }
            }
            update_user_meta($author_id, FELAN_METABOX_PREFIX . 'my_follow', $my_follow);

            $args = array(
                'post_type' => 'freelancer',
                'paged' => $paged,
                'post__in' => $freelancers_id,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
            );

            if (!empty($freelancers_search)) {
                $args['s'] = $freelancers_search;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query freelancers sort_by
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

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0 && !empty($freelancers_id)) {
                $list_id_freelancers = array();
                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    global $post;
                    $author_id = $post->post_author;
                    $id = get_the_ID();
                    $list_id_freelancers[] = $id;
                    $freelancer_current_position = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
                    $freelancer_locations = get_the_terms($id, 'felan_location');
                    $freelancer_email = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_email', true);
                    $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    $user_follow_athour = '';
                    if (!empty($user_follow_company[$author_id])) {
                        $user_follow_athour = implode(',', $user_follow_company[$author_id]);
                    }
                    ?>
                    <tr>
                        <td class="info-user">
                            <?php if (!empty($freelancer_avatar)) : ?>
                                <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
                            <?php else : ?>
                                <div class="image-freelancers"><i class="far fa-camera"></i></div>
                            <?php endif; ?>
                            <div class="info-details">
                                <h3>
                                    <a href="<?php echo esc_url(get_the_permalink($id)); ?>"><?php esc_html_e(get_the_title($id)); ?></a>
                                    <i class="far fa-check"></i>
                                </h3>
                                <div class="cate-info">
                                    <?php if (!empty($freelancer_current_position)) { ?>
                                        <div class="freelancer-current-position">
                                            <?php esc_html_e($freelancer_current_position . ' /'); ?>
                                        </div>
                                    <?php } ?>
                                    <?php felan_get_salary_freelancer($id, '-'); ?>
                                    <?php if (is_array($freelancer_locations)) {
                                        foreach ($freelancer_locations as $location) { ?>
                                            <?php esc_html_e('/ ' . $location->name); ?>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </td>
                        <td class="action-setting">
                            <div class="list-action">
                                <a href="<?php echo esc_url(get_the_permalink($id)); ?>" target="_blank" class="action icon-view tooltip" data-title="<?php echo esc_attr('View', 'felan-framework') ?>"><i class="far fa-eye"></i></i></a>
                                <a href="mailto: <?php esc_html_e($freelancer_email); ?>" class="action icon-gmail tooltip" data-title="<?php echo esc_attr('Send Email', 'felan-framework') ?>"><i class="far fa-envelope-open-text"></i></a>

                                <a href="#" class="action btn-delete tooltip" athour-id="<?php echo esc_attr($author_id) ?>" follow_company="<?php echo $user_follow_athour; ?>" items-id="<?php echo esc_attr($id); ?>" data-title="<?php echo esc_attr('Delete', 'felan-framework') ?>"><i class="far fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $freelancers_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'freelancers_html' => $freelancers_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Filter Follow Freelancer
         */
        public function felan_filter_follow_freelancer()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $freelancer_search = isset($_REQUEST['freelancer_search']) ? felan_clean(wp_unslash($_REQUEST['freelancer_search'])) : '';
            $sort_by = isset($_REQUEST['freelancer_sort_by']) ? felan_clean(wp_unslash($_REQUEST['freelancer_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $meta_query = array();
            $tax_query = array();

            $follow_freelancer = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'follow_freelancer', true);
            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    $key = array_search($item_id, $follow_freelancer);
                    if ($key !== false) {
                        unset($follow_freelancer[$key]);
                    }
                }
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'follow_freelancer', $follow_freelancer);

            $args = array(
                'post_type' => 'freelancer',
                'paged' => $paged,
                'post__in' => $follow_freelancer,
                'ignore_sticky_posts' => 1,
            );

            if (!empty($freelancer_search)) {
                $args['s'] = $freelancer_search;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query freelancer sort_by
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
            if (!empty($follow_freelancer)) {
                $total_post = $data->found_posts;
            } else {
                $total_post = 0;
            }
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0) {

                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    global $post;
                    $author_id = $post->post_author;
                    $id = get_the_ID();
                    $freelancer_current_position = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
                    $freelancer_locations = get_the_terms($id, 'felan_location');
                    $freelancer_email = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_email', true);
                    $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    $freelancer_featured = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
                    $user_follow_athour = '';
                    if (!empty($user_follow_company[$author_id])) {
                        $user_follow_athour = implode(',', $user_follow_company[$author_id]);
                    }
                    ?>
                    <tr>
                        <td class="info-user">
                            <?php if (!empty($freelancer_avatar)) : ?>
                                <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
                            <?php else : ?>
                                <div class="image-freelancers"><i class="far fa-camera"></i></div>
                            <?php endif; ?>
                            <div class="info-details">
                                <h3>
                                    <a href="<?php echo esc_url(get_the_permalink($id)); ?>"><?php esc_html_e(get_the_title($id)); ?></a>
                                    <?php if ($freelancer_featured == 1) : ?>
                                        <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'felan-framework') ?>"><i class="far fa-check"></i></span>
                                    <?php endif; ?>
                                </h3>
                                <div class="cate-info">
                                    <?php if (!empty($freelancer_current_position)) { ?>
                                        <div class="freelancer-current-position">
                                            <?php esc_html_e($freelancer_current_position . ' /'); ?>
                                        </div>
                                    <?php } ?>
                                    <?php felan_get_salary_freelancer($id, '-'); ?>
                                    <?php if (is_array($freelancer_locations)) {
                                        foreach ($freelancer_locations as $location) { ?>
                                            <?php esc_html_e('/ ' . $location->name); ?>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </td>
                        <td class="action-setting">
                            <div class="list-action">
                                <a href="<?php echo esc_url(get_the_permalink($id)); ?>" target="_blank" class="action icon-view tooltip" data-title="<?php echo esc_attr('View', 'felan-framework') ?>"><i class="far fa-eye"></i></i></a>
                                <a href="mailto: <?php esc_html_e($freelancer_email); ?>" class="action icon-gmail tooltip" data-title="<?php echo esc_attr('Send Email', 'felan-framework') ?>"><i class="far fa-envelope-open-text"></i></a>
                                <a href="#" class="action btn-delete tooltip" athour-id="<?php echo esc_attr($author_id) ?>" follow_company="<?php echo $user_follow_athour; ?>" items-id="<?php echo esc_attr($id); ?>" data-title="<?php echo esc_attr('Delete', 'felan-framework') ?>"><i class="far fa-trash-alt"></i></a>

                            </div>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $freelancer_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'freelancer_html' => $freelancer_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * Filter Invite Freelancer
         */
        public function felan_filter_invite_freelancer()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $freelancer_search = isset($_REQUEST['freelancer_search']) ? felan_clean(wp_unslash($_REQUEST['freelancer_search'])) : '';
            $sort_by = isset($_REQUEST['freelancer_sort_by']) ? felan_clean(wp_unslash($_REQUEST['freelancer_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';
            $list_jobs = isset($_REQUEST['list_jobs']) ? felan_clean(wp_unslash($_REQUEST['list_jobs'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $author_id = get_post_field('post_author', $item_id);

            $meta_query = array();
            $tax_query = array();

            $invite_freelancer = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'invite_freelancer', true);
            $my_invite = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'my_invite', true);

            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    $key = array_search($item_id, $invite_freelancer);
                    if ($key !== false) {
                        unset($invite_freelancer[$key]);
                    }
                }
            }

            $list_jobs_decoded = json_decode($list_jobs, true);
            if (!empty($list_jobs_decoded) && is_array($list_jobs_decoded) && is_array($my_invite)) {
                foreach ($list_jobs_decoded as $list_job) {
                    $key_my_invite = array_search($list_job, $my_invite);
                    if ($key_my_invite !== false) {
                        unset($my_invite[$key_my_invite]);
                    }
                }
            }

            update_user_meta($author_id, FELAN_METABOX_PREFIX . 'my_invite', $my_invite);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'invite_freelancer', $invite_freelancer);


            $args = array(
                'post_type' => 'freelancer',
                'paged' => $paged,
                'post__in' => $invite_freelancer,
                'ignore_sticky_posts' => 1,
            );

            if (!empty($freelancer_search)) {
                $args['s'] = $freelancer_search;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query freelancer sort_by
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
            if (!empty($invite_freelancer)) {
                $total_post = $data->found_posts;
            } else {
                $total_post = 0;
            }
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0) {

                while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    global $post;
                    $author_id = $post->post_author;
                    $id = get_the_ID();
                    $freelancer_current_position = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
                    $freelancer_locations = get_the_terms($id, 'felan_location');
                    $freelancer_email = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_email', true);
                    $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    $freelancer_featured = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
                    $user_invite_athour = '';
                    if (!empty($user_invite_company[$author_id])) {
                        $user_invite_athour = implode(',', $user_invite_company[$author_id]);
                    }
                    ?>
                    <tr>
                        <td class="info-user">
                            <?php if (!empty($freelancer_avatar)) : ?>
                                <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
                            <?php else : ?>
                                <div class="image-freelancers"><i class="far fa-camera"></i></div>
                            <?php endif; ?>
                            <div class="info-details">
                                <h3>
                                    <a href="<?php echo esc_url(get_the_permalink($id)); ?>"><?php esc_html_e(get_the_title($id)); ?></a>
                                    <?php if ($freelancer_featured == 1) : ?>
                                        <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'felan-framework') ?>"><i class="far fa-check"></i></span>
                                    <?php endif; ?>
                                </h3>
                                <div class="cate-info">
                                    <?php if (!empty($freelancer_current_position)) { ?>
                                        <div class="freelancer-current-position">
                                            <?php esc_html_e($freelancer_current_position . ' /'); ?>
                                        </div>
                                    <?php } ?>
                                    <?php felan_get_salary_freelancer($id, '-'); ?>
                                    <?php if (is_array($freelancer_locations)) {
                                        foreach ($freelancer_locations as $location) { ?>
                                            <?php esc_html_e('/ ' . $location->name); ?>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </td>
                        <td class="action-setting">
                            <div class="list-action">
                                <a href="<?php echo esc_url(get_the_permalink($id)); ?>" target="_blank" class="action icon-view tooltip" data-title="<?php echo esc_attr('View', 'felan-framework') ?>"><i class="far fa-eye"></i></i></a>
                                <a href="mailto: <?php esc_html_e($freelancer_email); ?>" class="action icon-gmail tooltip" data-title="<?php echo esc_attr('Send Email', 'felan-framework') ?>"><i class="far fa-envelope-open-text"></i></a>
                                <a href="#" class="action btn-delete tooltip" athour-id="<?php echo esc_attr($author_id) ?>" invite_company="<?php echo $user_invite_athour; ?>" items-id="<?php echo esc_attr($id); ?>" data-title="<?php echo esc_attr('Delete', 'felan-framework') ?>"><i class="far fa-trash-alt"></i></a>

                            </div>
                        </td>
                    </tr>
                <?php endwhile;
            }
            wp_reset_postdata();

            $freelancer_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'freelancer_html' => $freelancer_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }

        /**
         * My Apply
         */
        public function felan_filter_my_apply()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $jobs_search = isset($_REQUEST['jobs_search']) ? felan_clean(wp_unslash($_REQUEST['jobs_search'])) : '';
            $sort_by = isset($_REQUEST['jobs_sort_by']) ? felan_clean(wp_unslash($_REQUEST['jobs_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $jobs_id = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'applicants_jobs_id');
            if (!empty($jobs_id)) {
                $jobs_id = intval($jobs_id[0]);
            }

            $meta_query = array();
            $tax_query = array();

            $my_apply = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_apply', true);
            if ($action_click == 'delete') {
                if (!empty($jobs_id)) {
                    $key = array_search($jobs_id, $my_apply);
                    if ($key !== false) {
                        unset($my_apply[$key]);
                    }
                }
                if (!empty($item_id)) {
                    $item_author_id = get_post_field('post_author', $item_id);
                    if ($user_id == $item_author_id) {
                        wp_delete_post($item_id, true);
                    }
                }
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_apply', $my_apply);

            $args = array(
                'post_type' => 'applicants',
                'ignore_sticky_posts' => 1,
                'paged' => $paged,
                'post_status' => 'publish',
                'author' => $user_id,
            );

            if (!empty($jobs_search)) {
                $args['s'] = $jobs_search;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query jobs sort_by
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
                'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
            )));

            ob_start();

            if ($total_post > 0) {

                while ($data->have_posts()) : $data->the_post();
                    $applicants_id = get_the_ID();
                    $jobs_id = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_jobs_id');
                    if (!empty($jobs_id)) {
                        $jobs_id = intval($jobs_id[0]);
                    }
                    global $current_user;
                    wp_get_current_user();
                    $user_id = $current_user->ID;
                    $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                    $jobs_type = wp_get_post_terms($jobs_id, 'jobs-type');
                    $jobs_categories = wp_get_post_terms($jobs_id, 'jobs-categories');
                    $jobs_location = wp_get_post_terms($jobs_id, 'felan_location');
                    $company_id = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_company', true);
                    $company_logo = isset($company_id) ? get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo', true) : '';
                    $public_date = get_the_date('Y-m-d');
                ?>
                    <tr>
                        <td>
                            <div class="company-header">
                                <div class="img-comnpany">
                                    <?php if (!empty($company_logo['url'])) : ?>
                                        <img class="logo-company" src="<?php echo $company_logo['url'] ?>" alt="" />
                                    <?php else : ?>
                                        <div class="logo-company"><i class="far fa-camera"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="info-jobs">
                                    <h3 class="title-jobs-dashboard">
                                        <a href="<?php echo get_permalink($jobs_id); ?>">
                                            <?php echo get_the_title($applicants_id); ?>
                                        </a>
                                    </h3>
                                    <p>
                                        <?php if (is_array($jobs_categories)) {
                                            foreach ($jobs_categories as $categories) { ?>
                                                <?php esc_html_e($categories->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($jobs_type)) {
                                            foreach ($jobs_type as $type) { ?>
                                                <?php esc_html_e('/ ' . $type->name); ?>
                                        <?php }
                                        } ?>
                                        <?php if (is_array($jobs_location)) {
                                            foreach ($jobs_location as $location) { ?>
                                                <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                        } ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="status">
                            <?php echo felan_applicants_status($applicants_id) ?>
                        </td>
                        <td class="table-time">
                            <span class="start-time"><?php esc_html_e($public_date); ?></span>
                        </td>
                        <?php
                        ?>
                        <td class="action-setting jobs-control">
                            <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                            <ul class="action-dropdown">
                                <?php if ($user_demo == 'yes') : ?>
                                    <li><a class="btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                                <?php else : ?>
                                    <li><a class="btn-delete" jobs-id="<?php echo esc_attr($applicants_id); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                                    <?php if ($applicants_status == 'approved') : ?>
                                        <?php if (!empty($company_id)) : ?>
                                            <li><a class="btn-action-review" employer-id="<?php echo $company_id; ?>" href="#"><?php esc_html_e('Review', 'felan-framework') ?></a></li>
                                        <?php else: ?>
                                            <li><a class="btn-add-to-message" data-text="<?php echo esc_attr("Jobs hasn't chosen a company yet so he won't review it", "felan-framework"); ?>" href="#"><?php esc_html_e('Review', 'felan-framework') ?></a></li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </td>
                    </tr>
            <?php endwhile;
            }
            wp_reset_postdata();

            $jobs_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'jobs_html' => $jobs_html,
                    'total_post' => $total_post,
                    'page' => $page
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * Single Popup
         */
        public function felan_ajax_single_popup()
        {
            $post_id = isset($_REQUEST['post_id']) ? felan_clean(wp_unslash($_REQUEST['post_id'])) : '';
            $post_type = isset($_REQUEST['post_type']) ? felan_clean(wp_unslash($_REQUEST['post_type'])) : '';

            ob_start(); ?>
            <div class="content-header">
                <a href="#" class="btn-single-close"><i class="far fa-times"></i></a>
                <a href="<?php echo get_post_permalink($post_id); ?>" target="_blank" class="btn-new-tab">
                    <?php echo esc_html__('Open in new tab', 'felan-framework') ?>
                    <i class="far fa-external-link"></i>
                </a>
            </div>
            <div class="content-popup">
                <?php if ($post_type == 'freelancer') : ?>
                    <div class="freelancer-sidebar-popup sidebar-popup">
                        <?php do_action('felan_single_freelancer_sidebar', array(
                            'post_id' => $post_id,
                        )); ?>
                    </div>
                    <?php felan_get_template('content-single-freelancer.php', array(
                        'post_id' => $post_id,
                    )); ?>
                <?php elseif ($post_type == 'company') : ?>
                    <div class="company-sidebar-popup sidebar-popup">
                        <?php do_action('felan_single_company_sidebar', array(
                            'post_id' => $post_id,
                        )); ?>
                    </div>
                    <?php felan_get_template('content-single-company.php', array(
                        'post_id' => $post_id,
                    )); ?>
                <?php elseif ($post_type == 'jobs') : ?>
                    <?php felan_get_template('content-single-jobs.php', array(
                        'post_id' => $post_id,
                    )); ?>
                    <div class="jobs-sidebar-popup sidebar-right-popup">
                        <?php do_action('felan_single_jobs_sidebar', $post_id); ?>
                    </div>
                    <?php do_action('felan_apply_single_jobs', $post_id); ?>
                <?php elseif ($post_type == 'service') : ?>
                    <?php felan_get_template('service/single/head.php', array(
                        'service_single_id' => $post_id
                    )); ?>
                    <div class="d-md-flex">
                        <?php felan_get_template('content-single-service.php', array(
                            'post_id' => $post_id,
                        )); ?>
                        <div class="service-sidebar-popup sidebar-right-popup">
                            <?php do_action('felan_single_service_sidebar', $post_id); ?>
                        </div>
                    </div>
                <?php elseif ($post_type == 'project') : ?>
                    <?php felan_get_template('project/single/head.php', array(
                        'project_single_id' => $post_id
                    )); ?>
                    <div class="d-md-flex">
                        <?php felan_get_template('content-single-project.php', array(
                            'post_id' => $post_id,
                        )); ?>
                        <div class="project-sidebar-popup sidebar-right-popup">
                            <?php do_action('felan_single_project_sidebar', $post_id); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php $popup_html = ob_get_clean();

            if (!empty($post_id)) {
                echo json_encode(array('success' => true, 'popup_html' => $popup_html, 'post_type' => $post_type));
            } else {
                echo json_encode(array('success' => false));
            }
            wp_die();
        }

        /**
         * Service Filter
         */
        public function felan_filter_my_service()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '10';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $service_search = isset($_REQUEST['service_search']) ? felan_clean(wp_unslash($_REQUEST['service_search'])) : '';
            $service_status = isset($_REQUEST['service_status']) ? felan_clean(wp_unslash($_REQUEST['service_status'])) : '';
            $sort_by = isset($_REQUEST['service_sort_by']) ? felan_clean(wp_unslash($_REQUEST['service_sort_by'])) : '';
            $item_id = isset($_REQUEST['item_id']) ? felan_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? felan_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $withdraw_total_price = '';
            $meta_query = array();
            $tax_query = array();

            if (!empty($item_id)) {
                $service = get_post($item_id);
                if ($action_click == 'show') {
                    if ($service->post_status == 'pause') {
                        $data = array(
                            'ID' => $item_id,
                            'post_type' => 'service',
                            'post_status' => 'publish'
                        );
                    }
                    wp_update_post($data);
                }

                if ($action_click == 'pause') {
                    $data = array(
                        'ID' => $item_id,
                        'post_type' => 'service',
                        'post_status' => 'pause'
                    );
                    wp_update_post($data);
                }

                if ($action_click == 'featured') {
                    update_post_meta($item_id, FELAN_METABOX_PREFIX . 'service_featured', 1);
                    $number_featured = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_number_service_featured', $user_id);
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service_featured', $number_featured - 1);

                    $withdraw_total_price = felan_withdraw_total_price_ajax('featured_service', $user_id, 'freelancer');
                }
            }

            $args = array(
                'post_type' => 'service',
                'paged' => $paged,
                'post_status' => array('publish', 'pending', 'pause'),
                'ignore_sticky_posts' => 1,
                'author' => $user_id,
                'orderby' => 'date',
            );

            if (!empty($service_search)) {
                $args['s'] = $service_search;
            }

            if (!empty($service_status)) {
                $args['post_status'] = $service_status;
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //meta query service sort_by
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
                    $service_id = get_the_ID();
                    $status = get_post_status($service_id);
                    $service_skills = get_the_terms($service_id, 'service-skills');
                    $service_categories = get_the_terms($service_id, 'service-categories');
                    $service_location = get_the_terms($service_id, 'felan_location');
                    $public_date = get_the_date(get_option('date_format'));
                    $thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
                    $service_featured = intval(get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true));
                    $author_id = get_post_field('post_author', $service_id);
                    $author_name = get_the_author_meta('display_name', $author_id);

                    $currency_sign_default = felan_get_option('currency_sign_default');
                    $number_start_price = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_price', true);
                    $start_price = felan_get_format_money($number_start_price, '', 0);

                    $withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
                    $wallet_fee_amount = felan_get_option('wallet_featured_service_fee_amount', '');
                    $enable_wallet_mode = felan_get_option('enable_wallet_mode', '0');
                    ?>
                    <tr>
                        <td>
                            <div class="service-header">
                                <?php if (!empty($thumbnail)) : ?>
                                    <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                <?php endif; ?>
                                <div class="content">
                                    <h3 class="title-my-service">
                                        <a href="<?php echo get_the_permalink($service_id) ?>">
                                            <?php echo get_the_title($service_id); ?>
                                            <?php if ($service_featured == 1) : ?>
                                                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                    <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                    <div class="info-service-inner">
                                        <?php echo felan_get_total_rating('service', $service_id, false); ?>
                                        <div class="count-sales">
                                            <i class="fal fa-shopping-basket"></i>
                                            <?php echo felan_service_count_sale($author_id, $service_id); ?>
                                        </div>
                                        <?php felan_total_view_service_details($service_id); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
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
                        <td>
                            <span class="start-time"><?php echo $public_date; ?></span>
                        </td>
                        <?php
                        ?>
                        <td class="action-setting service-control">
                            <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                            <ul class="action-dropdown">
                                <?php
                                $service_dashboard_link = felan_get_permalink('service_dashboard');
                                $freelancer_package_number_service_featured = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_number_service_featured', $user_id);
                                $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                                switch ($status) {
                                    case 'publish':
                                ?>
                                        <li><a class="btn-edit" href="<?php echo esc_url($service_dashboard_link); ?>?service_id=<?php echo esc_attr($service_id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a>
                                        </li>
                                        <?php if ($user_demo == 'yes') { ?>
                                            <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"><?php esc_html_e('Paused', 'felan-framework'); ?></a>
                                            </li>
                                        <?php } else { ?>
                                            <?php if ($check_freelancer_package !== -1 && $check_freelancer_package !== 0) { ?>
                                                <li><a class="btn-pause" service-id="<?php echo esc_attr($service_id); ?>" href="<?php echo get_the_permalink($service_id); ?>"><?php esc_html_e('Paused', 'felan-framework') ?></a>
                                                </li>
                                            <?php } ?>

                                            <?php if ($enable_wallet_mode == '1') {
                                                if (intval($withdraw_price) > intval($wallet_fee_amount)) { ?>
                                                    <?php if ($service_featured !== 1) { ?>
                                                        <li><a class="btn-featured" service-id="<?php echo esc_attr($service_id); ?>" href="<?php echo get_the_permalink($service_id); ?>"><?php esc_html_e('Featured', 'felan-framework') ?></a></li>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php if ($service_featured !== 1) { ?>
                                                        <li>
                                                            <a class="btn-add-to-message" href="#"
                                                                data-text="<?php echo esc_attr('Your wallet balance is currently insufficient.', 'felan-framework'); ?>">
                                                                <?php esc_html_e('Featured', 'felan-framework') ?>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <?php if ($freelancer_package_number_service_featured > 0 && $service_featured !== 1 && $check_freelancer_package !== -1  && $check_freelancer_package !== 0) { ?>
                                                    <li><a class="btn-featured" service-id="<?php echo esc_attr($service_id); ?>" href="<?php echo get_the_permalink($service_id); ?>"><?php esc_html_e('Featured', 'felan-framework') ?></a></li>
                                                <?php } ?>
                                            <?php } ?>

                                        <?php } ?>
                                    <?php
                                        break;
                                    case 'pending': ?>
                                        <li><a class="btn-edit" href="<?php echo esc_url($service_dashboard_link); ?>?service_id=<?php echo esc_attr($service_id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a>
                                        </li>
                                    <?php
                                        break;
                                    case 'pause':
                                    ?>
                                        <li><a class="btn-edit" href="<?php echo esc_url($service_dashboard_link); ?>?service_id=<?php echo esc_attr($service_id); ?>"><?php esc_html_e('Edit', 'felan-framework'); ?></a>
                                        </li>
                                        <?php if ($check_freelancer_package !== -1 && $check_freelancer_package !== 0) { ?>
                                            <li><a class="btn-show" service-id="<?php echo esc_attr($service_id); ?>" href="<?php echo get_the_permalink($service_id); ?>"><?php esc_html_e('Continue', 'felan-framework'); ?>
                                                </a>
                                            </li>
                                <?php }
                                        break;
                                }
                                ?>
                            </ul>
                        </td>
                    </tr>
            <?php endwhile;
            }
            wp_reset_postdata();

            $service_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'service_html' => $service_html,
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
         * Update profile
         */
        public function felan_update_profile_ajax()
        {
            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            check_ajax_referer('felan_update_profile_ajax_nonce', 'felan_security_update_profile');

            $user_firstname = $user_lastname = $user_email = $author_mobile_number = '';

            // Update first name
            if (!empty($_POST['user_firstname'])) {
                $user_firstname = sanitize_text_field(wp_unslash($_POST['user_firstname']));
                update_user_meta($user_id, 'first_name', $user_firstname);
            } else {
                delete_user_meta($user_id, 'first_name');
            }

            // Update last name
            if (!empty($_POST['user_lastname'])) {
                $user_lastname = sanitize_text_field(wp_unslash($_POST['user_lastname']));
                update_user_meta($user_id, 'last_name', $user_lastname);
            } else {
                delete_user_meta($user_id, 'last_name');
            }

            // Update display name
            if (!empty($_POST['user_firstname']) && !empty($_POST['user_lastname'])) {
                $type_name_freelancer = felan_get_option('type_name_freelancer');
                if ($type_name_freelancer === 'fl-name') {
                    $full_name = sanitize_text_field(wp_unslash($_POST['user_firstname'])) . ' ' . sanitize_text_field(wp_unslash($_POST['user_lastname']));
                    $userdata = array(
                        'ID' => $user_id,
                        'display_name' => $full_name,
                    );
                    wp_update_user($userdata);
                }
            }

            // Update Phone
            if (!empty($_POST['author_mobile_number'])) {
                $author_mobile_number = sanitize_text_field(wp_unslash($_POST['author_mobile_number']));
                if (0 < strlen(trim(preg_replace('/[\s\#0-9_\-\+\/\(\)\.]/', '', $author_mobile_number)))) {
                    echo json_encode(array(
                        'success' => false,
                        'message' => esc_html__('The phone number you entered is not valid. Please try again.', 'felan-framework')
                    ));
                    wp_die();
                }
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_mobile_number', $author_mobile_number);
            } else {
                delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_mobile_number');
            }

            // Update Phone Code
            if (!empty($_POST['phone_code'])) {
                $phone_code = sanitize_text_field(wp_unslash($_POST['phone_code']));
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'phone_code', $phone_code);
            } else {
                delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'phone_code');
            }

            // Update Profile Avatar
            if (isset($_POST['user_image_url']) && isset($_POST['user_image_id'])) {
                $user_image_url = sanitize_text_field($_POST['user_image_url']);
                $user_image_id = sanitize_text_field($_POST['user_image_id']);
                update_user_meta($user_id, 'author_avatar_image_url', $user_image_url);
                update_user_meta($user_id, 'author_avatar_image_id', $user_image_id);
            } else {
                delete_user_meta($user_id, 'author_avatar_image_url');
                delete_user_meta($user_id, 'author_avatar_image_id');
            }

            // Update email
            if (!empty($_POST['user_email'])) {
                $user_email = sanitize_email(wp_unslash($_POST['user_email']));
                $user_email = is_email($user_email);
                if (!$user_email) {
                    echo json_encode(array(
                        'success' => false,
                        'message' => esc_html__('The Email you entered is not valid. Please try again.', 'felan-framework')
                    ));
                    wp_die();
                } else {
                    $email_exists = email_exists($user_email);
                    if ($email_exists) {
                        if ($email_exists != $user_id) {
                            echo json_encode(array(
                                'success' => false,
                                'message' => esc_html__('This Email is already used by another user. Please try a different one.', 'felan-framework')
                            ));
                            wp_die();
                        }
                    } else {
                        $return = wp_update_user(array('ID' => $user_id, 'user_email' => $user_email));
                        if (is_wp_error($return)) {
                            $error = $return->get_error_message();
                            esc_html_e($error);
                            wp_die();
                        }
                    }
                }
            }

            echo json_encode(array(
                'success' => true,
                'message' => esc_html__('Profile updated', 'felan-framework')
            ));
            wp_die();
        }

        /**
         * Change password
         */
        public function felan_change_password_ajax()
        {
            check_ajax_referer('felan_change_password_ajax_nonce', 'felan_security_change_password');
            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $allowed_html = array();

            $oldpass = isset($_POST['oldpass']) ? felan_clean(wp_unslash($_POST['oldpass'])) : '';
            $newpass = isset($_POST['newpass']) ? felan_clean(wp_unslash($_POST['newpass'])) : '';
            $confirmpass = isset($_POST['confirmpass']) ? felan_clean(wp_slash($_POST['confirmpass'])) : '';


            if ($newpass == '' || $confirmpass == '') {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('New password or confirm password is blank', 'felan-framework')
                ));
                wp_die();
            }

            if ($newpass !== $confirmpass) {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('Passwords do not match', 'felan-framework')
                ));
                wp_die();
            }

            if (strlen($newpass) < 6 || strlen($confirmpass) < 6) {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('Please set a password with a length of more than 6 characters', 'felan-framework')
                ));
                wp_die();
            }

            $user = get_user_by('id', $user_id);
            if ($user && wp_check_password($oldpass, $user->data->user_pass, $user_id)) {
                wp_set_password($newpass, $user_id);
                echo json_encode(array(
                    'success' => true,
                    'message' => esc_html__('Password Updated', 'felan-framework')
                ));
            } else {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('Old password is not correct', 'felan-framework')
                ));
            }
            wp_die();
        }


        /**
         * Update payout
         */
        public function felan_update_payout_ajax()
        {
            $payout_paypal = isset($_REQUEST['payout_paypal']) ? felan_clean(wp_unslash($_REQUEST['payout_paypal'])) : '';
            $payout_stripe = isset($_REQUEST['payout_stripe']) ? felan_clean(wp_unslash($_REQUEST['payout_stripe'])) : '';
            $payout_card_number = isset($_REQUEST['payout_card_number']) ? felan_clean(wp_unslash($_REQUEST['payout_card_number'])) : '';
            $payout_card_name = isset($_REQUEST['payout_card_name']) ? felan_clean(wp_unslash($_REQUEST['payout_card_name'])) : '';
            $payout_bank_transfer_name = isset($_REQUEST['payout_bank_transfer_name']) ? felan_clean(wp_unslash($_REQUEST['payout_bank_transfer_name'])) : '';
            $custom_field = isset($_REQUEST['custom_field']) ? felan_clean(wp_unslash($_REQUEST['custom_field'])) : '';
            $enable_paypal = felan_get_option('enable_payout_paypal');
            $enable_stripe = felan_get_option('enable_payout_stripe');
            $enable_bank = felan_get_option('enable_payout_bank_transfer');

            global $current_user;
            $user_id = $current_user->ID;

            if (($enable_paypal === '1' && empty($payout_paypal)) && ($enable_stripe === '1' && empty($payout_stripe))
                && ($enable_bank && empty($payout_card_number) && empty($payout_card_name) && empty($payout_bank_transfer_name))
            ) {
                echo json_encode(array(
                    'success' => false,
                    'message' => esc_html__('Please select a payment method to save.', 'felan-framework')
                ));
                wp_die();
            }

            if (!empty($payout_paypal)) {
                if ($enable_paypal === '1') {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_paypal', $payout_paypal);
                }
            } else {
                delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_paypal');
            }

            if (!empty($payout_stripe)) {
                if ($enable_stripe === '1') {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_stripe', $payout_stripe);
                }
            } else {
                delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_stripe');
            }

            if (!empty($payout_card_number)) {
                if ($enable_bank === '1') {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_card_number', $payout_card_number);
                }
            } else {
                delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_card_number');
            }

            if (!empty($payout_card_name)) {
                if ($enable_bank === '1') {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_card_name', $payout_card_name);
                }
            } else {
                delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_card_name');
            }

            if (!empty($payout_bank_transfer_name)) {
                if ($enable_bank === '1') {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_bank_transfer_name', $payout_bank_transfer_name);
                }
            } else {
                delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_bank_transfer_name');
            }

            if (!empty($custom_field)) :
                foreach ($custom_field as $key => $field) :
                    if (!empty($field)) {
                        update_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_custom_' . $key, $field);
                    } else {
                        delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_payout_custom_' . $key);
                    }
                endforeach;
            endif;

            echo json_encode(array(
                'success' => true,
                'message' => esc_html__('You have successfully submitted your information', 'felan-framework')
            ));

            wp_die();
        }

        /**
         * Chart Jobs
         */
        public function felan_chart_ajax()
        {
            $number_days = isset($_REQUEST['number_days']) ? felan_clean(wp_unslash($_REQUEST['number_days'])) : '7';
            $jobs_id = isset($_REQUEST['jobs_id']) ? felan_clean(wp_unslash($_REQUEST['jobs_id'])) : '';

            // labels
            $labels = array();
            for ($i = $number_days; $i >= 0; $i--) {
                $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
                $labels[] = date('M j, Y', $date);
            }

            $values_view = felan_view_jobs_date($jobs_id, $number_days);
            $values_apply = felan_total_jobs_apply($jobs_id, $number_days);

            $return = array(
                'labels' => $labels,
                'values_view' => $values_view,
                'values_apply' => $values_apply,
                'label_view' => esc_html__('Page View', 'felan-framework'),
                'label_apply' => esc_html__('Apply Click', 'felan-framework'),
            );
            echo json_encode($return);

            wp_die();
        }

        /**
         * Chart Project
         */
        public function felan_chart_project_ajax()
        {
            $number_days = isset($_REQUEST['number_days']) ? felan_clean(wp_unslash($_REQUEST['number_days'])) : '7';
            $project_id = isset($_REQUEST['project_id']) ? felan_clean(wp_unslash($_REQUEST['project_id'])) : '';

            // labels
            $labels = array();
            for ($i = $number_days; $i >= 0; $i--) {
                $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
                $labels[] = date('M j, Y', $date);
            }

            $values_view = felan_view_project_date($project_id, $number_days);
            $values_apply = felan_total_project_apply($project_id, $number_days);

            $return = array(
                'labels' => $labels,
                'values_view' => $values_view,
                'values_apply' => $values_apply,
                'label_view' => esc_html__('Page View', 'felan-framework'),
                'label_apply' => esc_html__('Apply Click', 'felan-framework'),
            );
            echo json_encode($return);

            wp_die();
        }

        /**
         * Chart Employer
         */
        public function felan_chart_employer_ajax()
        {
            $number_days = isset($_REQUEST['number_days']) ? felan_clean(wp_unslash($_REQUEST['number_days'])) : '7';

            // labels
            $labels = array();
            for ($i = $number_days; $i >= 0; $i--) {
                $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
                $labels[] = date('M j, Y', $date);
            }

            $views_values = apply_filters('felan_total_view_function', felan_total_view_jobs($number_days), $number_days);

            $return = array(
                'labels_view' => $labels,
                'values_view' => $views_values,
                'label_view' => esc_html__('Page View', 'felan-framework'),
            );
            echo json_encode($return);

            wp_die();
        }

        /**
         * Chart Freelancer
         */
        public function felan_chart_freelancer_ajax()
        {
            $number_days = isset($_REQUEST['number_days']) ? felan_clean(wp_unslash($_REQUEST['number_days'])) : '7';

            // labels
            $labels = array();
            for ($i = $number_days; $i >= 0; $i--) {
                $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
                $labels[] = date('M j, Y', $date);
            }

            $views_values = felan_total_view_freelancer($number_days);

            $return = array(
                'labels_view' => $labels,
                'values_view' => $views_values,
                'label_view' => esc_html__('Page View', 'felan-framework'),
            );
            echo json_encode($return);

            wp_die();
        }

        /**
         * Apply Jobs
         */
        public function jobs_add_to_apply()
        {
            $jobs_id = isset($_REQUEST['jobs_id']) ? felan_clean(wp_unslash($_REQUEST['jobs_id'])) : '';
            $freelancer_id = isset($_REQUEST['freelancer_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_id'])) : '';
            $message = isset($_REQUEST['message']) ? felan_clean(wp_unslash($_REQUEST['message'])) : '';
            $email = isset($_REQUEST['emaill']) ? felan_clean(wp_unslash($_REQUEST['emaill'])) : '';
            $phone = isset($_REQUEST['phone']) ? felan_clean(wp_unslash($_REQUEST['phone'])) : '';
            $cv_url = isset($_REQUEST['cv_url']) ? felan_clean(wp_unslash($_REQUEST['cv_url'])) : '';
            $type_apply = isset($_REQUEST['type_apply']) ? felan_clean(wp_unslash($_REQUEST['type_apply'])) : '';

            $freelancer_current_position = isset($_REQUEST['freelancer_current_position']) ? felan_clean(wp_unslash($_REQUEST['freelancer_current_position'])) : '';
            $freelancer_categories = isset($_REQUEST['freelancer_categories']) ? felan_clean(wp_unslash($_REQUEST['freelancer_categories'])) : '';
            $freelancer_dob = isset($_REQUEST['freelancer_dob']) ? felan_clean(wp_unslash($_REQUEST['freelancer_dob'])) : '';
            $freelancer_age = isset($_REQUEST['freelancer_age']) ? felan_clean(wp_unslash($_REQUEST['freelancer_age'])) : '';
            $freelancer_gender = isset($_REQUEST['freelancer_gender']) ? felan_clean(wp_unslash($_REQUEST['freelancer_gender'])) : '';
            $freelancer_languages = isset($_REQUEST['freelancer_languages']) ? felan_clean(wp_unslash($_REQUEST['freelancer_languages'])) : '';
            $freelancer_qualification = isset($_REQUEST['freelancer_qualification']) ? felan_clean(wp_unslash($_REQUEST['freelancer_qualification'])) : '';
            $freelancer_yoe = isset($_REQUEST['freelancer_yoe']) ? felan_clean(wp_unslash($_REQUEST['freelancer_yoe'])) : '';


            global $current_user;
            $user_id = $current_user->ID;
            $user_name = $current_user->display_name;
            $show_field_jobs_apply = felan_get_option('show_field_jobs_apply');

            if ($type_apply == 'email') {
                $check_field = $message == '' || $email == '' || filter_var($email, FILTER_VALIDATE_EMAIL) == false || $phone == '' || $cv_url == ''
                    || $show_field_jobs_apply && in_array('position', $show_field_jobs_apply) && empty($freelancer_current_position)
                    || $show_field_jobs_apply && in_array('categories', $show_field_jobs_apply) && empty($freelancer_categories)
                    || $show_field_jobs_apply && in_array('date', $show_field_jobs_apply) && empty($freelancer_dob)
                    || $show_field_jobs_apply && in_array('age', $show_field_jobs_apply) && empty($freelancer_age)
                    || $show_field_jobs_apply && in_array('gender', $show_field_jobs_apply) && empty($freelancer_gender)
                    || $show_field_jobs_apply && in_array('languages', $show_field_jobs_apply) && empty($freelancer_languages)
                    || $show_field_jobs_apply && in_array('qualification', $show_field_jobs_apply) && empty($freelancer_qualification)
                    || $show_field_jobs_apply && in_array('experience', $show_field_jobs_apply) && empty($freelancer_yoe);
            } else {
                $check_field = $message == '' || $cv_url == '';
            }

            $author_employer_id = get_post_field('post_author', $jobs_id);
            $user_employer = get_user_by('id', $author_employer_id);
            $user_employer_email = $user_employer->user_email;
            $jobs_apply_email = !empty(get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_apply_email')) ? get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_apply_email')[0] : '';

            if (!empty($jobs_apply_email)) {
                $user_employer_email = $jobs_apply_email;
            } else {
                $user_employer_email = $user_employer->user_email;
            }

            if ($check_field) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                    echo json_encode(array(
                        'added' => false,
                        'success' => false,
                        'message' => esc_html__('Please enter a valid email.', 'felan-framework')
                    ));
                } else {
                    echo json_encode(array(
                        'added' => false,
                        'success' => false,
                        'message' => esc_html__('Please fill all form fields.', 'felan-framework')
                    ));
                }
            } else {

                $new_jobs = array(
                    'post_type' => 'applicants',
                    'post_status' => 'publish',
                );

                $jobs_title = get_the_title($jobs_id);
                if (isset($jobs_title)) {
                    $new_jobs['post_title'] = $jobs_title;
                }
                if (!empty($new_jobs['post_title'])) {
                    $applicants_id = wp_insert_post($new_jobs, true);
                }

                $date_applicants = get_the_date('Y-m-d', $applicants_id);

                if (isset($jobs_id)) {
                    update_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_jobs_id', $jobs_id);
                }

                if (isset($date_applicants)) {
                    update_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_date', $date_applicants);
                }

                if ($type_apply == 'email') {
                    if (isset($phone)) {
                        update_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_phone', $phone);
                    }

                    if (isset($email)) {
                        update_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_email', $email);
                    }
                }

                if (isset($message)) {
                    update_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_message', $message);
                }

                if (isset($cv_url)) {
                    update_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_cv', $cv_url);
                }

                if (isset($type_apply)) {
                    update_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_type', $type_apply);
                }

                $enable_apply_login = felan_get_option('enable_apply_login');
                if ($enable_apply_login == '1' || is_user_logged_in()) {

                    if ($user_id > 0) {
                        $my_apply = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_apply', true);

                        if (!empty($my_apply) && (!in_array($jobs_id, $my_apply))) {
                            array_push($my_apply, $jobs_id);
                        } else {
                            $my_apply = array($jobs_id);
                        }
                        update_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_apply', $my_apply);
                    }

                    if ($user_id > 0) {
                        update_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_author', $user_name);
                    }

                    felan_get_data_ajax_notification($jobs_id, 'add-apply');

                    felan_number_freelancer_package_ajax('jobs_apply');

                    $args_mail = array(
                        'jobs_apply' => get_the_title($jobs_id),
                        'jobs_url' => get_permalink($jobs_id),
                        'user_apply' => $user_name,
                        'user_url' => get_permalink($user_id),
                        'cv_url' => $cv_url,
                        'message' => $message,
                        'phone' => $phone,
                    );

                    if (empty($email)) {
                        $email = $current_user->user_email;
                    }

                    //Add Field
                    if (!empty($show_field_jobs_apply)) {
                        if (in_array('position', $show_field_jobs_apply) && !empty($freelancer_current_position)) {
                            $args_mail['freelancer_current_position'] = $freelancer_current_position;
                        }
                        if (in_array('date', $show_field_jobs_apply) && !empty($freelancer_dob)) {
                            $args_mail['freelancer_dob'] = $freelancer_dob;
                        }
                        if (in_array('categories', $show_field_jobs_apply) && !empty($freelancer_categories)) {
                            $args_mail['freelancer_categories'] = get_the_category_by_ID($freelancer_categories);
                        }
                        if (in_array('age', $show_field_jobs_apply) && !empty($freelancer_age)) {
                            $args_mail['freelancer_age'] = get_the_category_by_ID($freelancer_age);
                        }
                        if (in_array('gender', $show_field_jobs_apply) && !empty($freelancer_gender)) {
                            $args_mail['freelancer_gender'] = get_the_category_by_ID($freelancer_gender);
                        }
                        if (in_array('languages', $show_field_jobs_apply) && !empty($freelancer_languages)) {
                            $args_mail['freelancer_languages'] = get_the_category_by_ID($freelancer_languages);
                        }
                        if (in_array('qualification', $show_field_jobs_apply) && !empty($freelancer_qualification)) {
                            $args_mail['freelancer_qualification'] = get_the_category_by_ID($freelancer_qualification);
                        }
                        if (in_array('experience', $show_field_jobs_apply) && !empty($freelancer_yoe)) {
                            $args_mail['freelancer_yoe'] = get_the_category_by_ID($freelancer_yoe);
                        }
                    }

                    if ($type_apply == 'email') {
                        felan_send_email($email, 'mail_freelancer_apply', $args_mail);
                        felan_send_email($user_employer_email, 'mail_employer_apply', $args_mail);
                    }
                } else {
                    $user_name_nlogin = esc_html('User not logged in', 'felan-framework');
                    update_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'applicants_author', $user_name_nlogin);

                    $args_mail = array(
                        'cv_url' => $cv_url,
                        'jobs_apply' => get_the_title($jobs_id),
                        'jobs_url' => get_permalink($jobs_id),
                        'user_apply' => $email,
                        'user_url' => get_permalink($user_id),
                        'phone' => $phone,
                        'message' => $message,
                    );
                    $args_mail_nlogin = array(
                        'cv_url' => $cv_url,
                        'message' => $message,
                        'jobs_apply' => get_the_title($jobs_id),
                        'jobs_url' => get_permalink($jobs_id),
                        'phone' => $phone,
                    );

                    //Add Field
                    if (!empty($show_field_jobs_apply)) {
                        if (in_array('position', $show_field_jobs_apply) && !empty($freelancer_current_position)) {
                            $args_mail['freelancer_current_position'] = $freelancer_current_position;
                            $args_mail_nlogin['freelancer_current_position'] = $freelancer_current_position;
                        }
                        if (in_array('date', $show_field_jobs_apply) && !empty($freelancer_dob)) {
                            $args_mail['freelancer_dob'] = $freelancer_dob;
                            $args_mail_nlogin['freelancer_dob'] = $freelancer_dob;
                        }
                        if (in_array('categories', $show_field_jobs_apply) && !empty($freelancer_categories)) {
                            $args_mail['freelancer_categories'] = get_the_category_by_ID($freelancer_categories);
                            $args_mail_nlogin['freelancer_categories'] = get_the_category_by_ID($freelancer_categories);
                        }
                        if (in_array('age', $show_field_jobs_apply) && !empty($freelancer_age)) {
                            $args_mail['freelancer_age'] = get_the_category_by_ID($freelancer_age);
                            $args_mail_nlogin['freelancer_age'] = get_the_category_by_ID($freelancer_age);
                        }
                        if (in_array('gender', $show_field_jobs_apply) && !empty($freelancer_gender)) {
                            $args_mail['freelancer_gender'] = get_the_category_by_ID($freelancer_gender);
                            $args_mail_nlogin['freelancer_gender'] = get_the_category_by_ID($freelancer_gender);
                        }
                        if (in_array('languages', $show_field_jobs_apply) && !empty($freelancer_languages)) {
                            $args_mail['freelancer_languages'] = get_the_category_by_ID($freelancer_languages);
                            $args_mail_nlogin['freelancer_languages'] = get_the_category_by_ID($freelancer_languages);
                        }
                        if (in_array('qualification', $show_field_jobs_apply) && !empty($freelancer_qualification)) {
                            $args_mail['freelancer_qualification'] = get_the_category_by_ID($freelancer_qualification);
                            $args_mail_nlogin['freelancer_qualification'] = get_the_category_by_ID($freelancer_qualification);
                        }
                        if (in_array('experience', $show_field_jobs_apply) && !empty($freelancer_yoe)) {
                            $args_mail['freelancer_yoe'] = get_the_category_by_ID($freelancer_yoe);
                            $args_mail_nlogin['freelancer_yoe'] = get_the_category_by_ID($freelancer_yoe);
                        }
                    }

                    if ($type_apply == 'email') {
                        felan_send_email($user_employer_email, 'mail_employer_apply', $args_mail);
                        felan_send_email($email, 'mail_freelancer_apply_nlogin', $args_mail_nlogin);
                    }
                }

                echo json_encode(array(
                    'added' => true,
                    'success' => true,
                    'message' => esc_html__('You have applied successfully', 'felan-framework')
                ));
            }

            wp_die();
        }

        /**
         * Wishlist Jobs
         */
        public function felan_add_to_wishlist()
        {
            global $current_user;
            $jobs_id = $_POST['jobs_id'];
            $jobs_id = intval($jobs_id);
            wp_get_current_user();
            $user_id = $current_user->ID;
            $added = $removed = false;
            $ajax_response = '';
            if ($user_id > 0) {

                felan_number_freelancer_package_ajax('jobs_wishlist');
                $freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
                $check_package = felan_check_freelancer_package();
                $show_package_jobs_wishlist = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'show_package_jobs_wishlist', true);
                $freelancer_package_number_jobs_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_jobs_wishlist', true);
                $show_option_jobs_wishlist = felan_get_option('enable_freelancer_package_jobs_wishlist');

                if ($show_option_jobs_wishlist == 1 && $show_package_jobs_wishlist == '1' && ($freelancer_package_number_jobs_wishlist < 0 || $check_package == false)) {
                    $ajax_response = array('package_expires' => true);
                } else {

                    $my_favorites = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_wishlist', true);

                    if (!empty($my_favorites) && (!in_array($jobs_id, $my_favorites))) {
                        array_push($my_favorites, $jobs_id);
                        $added = true;
                    } else {
                        if (empty($my_favorites)) {
                            $my_favorites = array($jobs_id);
                            $added = true;
                        } else {
                            //Delete favorite
                            $key = array_search($jobs_id, $my_favorites);
                            if ($key !== false) {
                                unset($my_favorites[$key]);
                                $removed = true;
                            }
                        }
                    }

                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_wishlist', $my_favorites);

                    if ($added) {
                        felan_get_data_ajax_notification($jobs_id, 'add-wishlist');
                        $ajax_response = array('added' => true, 'message' => esc_html__('Added', 'felan-framework'));
                    }
                    if ($removed) {
                        $ajax_response = array(
                            'added' => false,
                            'message' => esc_html__('Removed', 'felan-framework')
                        );
                    }
                }
            } else {
                $ajax_response = array(
                    'added' => false,
                    'message' => esc_html__('You are not login', 'felan-framework')
                );
            }
            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * Wishlist Service
         */
        public function felan_service_wishlist()
        {
            global $current_user;
            $user_id = $current_user->ID;
            $service_id = $_POST['service_id'];
            $service_id = intval($service_id);
            $added = $removed = false;
            $ajax_response = '';

            if ($user_id > 0) {
                $service_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_wishlist', true);

                if (!empty($service_wishlist) && (!in_array($service_id, $service_wishlist))) {
                    array_push($service_wishlist, $service_id);
                    $added = true;
                } else {
                    if (empty($service_wishlist)) {
                        $service_wishlist = array($service_id);
                        $added = true;
                    } else {
                        $key = array_search($service_id, $service_wishlist);
                        if ($key !== false) {
                            unset($service_wishlist[$key]);
                            $removed = true;
                        }
                    }
                }
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_wishlist', $service_wishlist);

                if ($added) {
                    $ajax_response = array('added' => true, 'message' => esc_html__('Added', 'felan-framework'));
                }
                if ($removed) {
                    $ajax_response = array('added' => false, 'message' => esc_html__('Removed', 'felan-framework'));
                }
            } else {
                $ajax_response = array(
                    'added' => false,
                    'message' => esc_html__('You are not login', 'felan-framework')
                );
            }
            echo json_encode($ajax_response);
            wp_die();
        }


        /**
         * Service Package
         */
        public function felan_service_package()
        {
            $service_id = isset($_REQUEST['service_id']) ? felan_clean(wp_unslash($_REQUEST['service_id'])) : '';
            $service_package_price = isset($_REQUEST['service_package_price']) ? felan_clean(wp_unslash($_REQUEST['service_package_price'])) : '';
            $service_package_time = isset($_REQUEST['service_package_time']) ? felan_clean(wp_unslash($_REQUEST['service_package_time'])) : '';
            $service_package_time_type = isset($_REQUEST['service_package_time_type']) ? felan_clean(wp_unslash($_REQUEST['service_package_time_type'])) : '';
            $service_package_des = isset($_REQUEST['service_package_des']) ? felan_clean(wp_unslash($_REQUEST['service_package_des'])) : '';
            $service_package_new = isset($_REQUEST['service_package_new']) ? felan_clean(wp_unslash($_REQUEST['service_package_new'])) : '';

            global $current_user;
            $user_id = $current_user->ID;

            if (!empty($service_id)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_service_id', $service_id);
            }

            if (!empty($service_package_price)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_price', $service_package_price);
            }

            if (!empty($service_package_time)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_time', $service_package_time);
            }

            if (!empty($service_package_time_type)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_time_type', $service_package_time_type);
            }

            if (!empty($service_package_des)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_des', $service_package_des);
            }

            if (!empty($service_package_new)) {
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_new', json_decode($service_package_new));
            }

            $ajax_response = array('success' => true);
            echo json_encode($ajax_response);

            wp_die();
        }

        /**
         * Service Wallet
         */
        public function felan_service_wallet()
        {
            $service_id = isset($_REQUEST['service_id']) ? felan_clean(wp_unslash($_REQUEST['service_id'])) : '';
            $service_package_price = isset($_REQUEST['service_package_price']) ? felan_clean(wp_unslash($_REQUEST['service_package_price'])) : '';
            $service_package_time = isset($_REQUEST['service_package_time']) ? felan_clean(wp_unslash($_REQUEST['service_package_time'])) : '';
            $service_package_time_type = isset($_REQUEST['service_package_time_type']) ? felan_clean(wp_unslash($_REQUEST['service_package_time_type'])) : '';
            $service_package_des = isset($_REQUEST['service_package_des']) ? felan_clean(wp_unslash($_REQUEST['service_package_des'])) : '';
            $service_package_new = isset($_REQUEST['service_package_new']) ? felan_clean(wp_unslash($_REQUEST['service_package_new'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $service_order_date = current_time('Y-m-d H:i:s');
            $author_id = get_post_field('post_author', $service_id);
            $author_name = get_the_author_meta('display_name', $author_id);
            $format_total_price = felan_get_format_money($service_package_price);
            $status = 'pending';
            if (felan_get_option('enable_auto_approve_pending_service') == '1') {
                $status = 'inprogress';
            }

            $service_title = get_the_title($service_id);
            $args = array(
                'post_title'    => $service_title,
                'post_status'   => 'publish',
                'post_type'     => 'service_order'
            );

            $service_order_id =  wp_insert_post($args);

            $felan_meta = array();
            $felan_meta['service_order_item_id'] = $service_id;
            $felan_meta['service_order_item_price'] = $service_package_price;
            $felan_meta['service_order_item_price_not_format'] = $service_package_price;
            $felan_meta['service_order_purchase_date'] = $service_order_date;
            $felan_meta['service_order_user_id'] = $user_id;
            $felan_meta['service_order_author_service'] = $author_name;
            $felan_meta['service_order_time_type'] = $service_package_time_type;
            $felan_meta['service_order_number_time'] = $service_package_time;
            $felan_meta['service_order_price_default'] = $service_package_price;
            $felan_meta['service_order_package_des'] = $service_package_des;
            $felan_meta['service_order_package_new'] = $service_package_new;
            //$felan_meta['service_order_package_addons'] = $package_addons;

            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_user_id', $user_id);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_author_service', $author_name);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_author_id', $author_id);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', $service_id);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_time_type', $service_package_time_type);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_number_time', $service_package_time);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_price', $format_total_price);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_price_not_format', $service_package_price);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_date', $service_order_date);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', $status);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_meta', $felan_meta);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_price_default', $service_package_price);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_des', $service_package_des);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_new', $service_package_new);
            //update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_addons', $package_addons);

            $ajax_response = array('success' => true);
            echo json_encode($ajax_response);

            wp_die();
        }

        /**
         * Follow Company
         */
        public function felan_add_to_follow()
        {
            global $current_user;
            $company_id = $_POST['company_id'];
            $company_id = intval($company_id);
            wp_get_current_user();
            $user_id = $current_user->ID;
            $added = $removed = false;
            $ajax_response = '';
            if ($user_id > 0) {

                felan_number_freelancer_package_ajax('company_follow');
                $freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
                $check_package = felan_check_freelancer_package();
                $show_package_company_freelancer_follow = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'show_package_company_freelancer_follow', true);
                $freelancer_package_number_company_follow = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_company_follow', true);
                if ($show_package_company_freelancer_follow == '1' && ($freelancer_package_number_company_follow < 0 || $check_package == false)) {
                    $ajax_response = array('package_expires' => true);
                } else {
                    $my_follow = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_follow', true);

                    if (!empty($my_follow) && (!in_array($company_id, $my_follow))) {
                        array_push($my_follow, $company_id);
                        $added = true;
                    } else {
                        if (empty($my_follow)) {
                            $my_follow = array($company_id);
                            $added = true;
                        } else {
                            //Delete favorite
                            $key = array_search($company_id, $my_follow);
                            if ($key !== false) {
                                unset($my_follow[$key]);
                                $removed = true;
                            }
                        }
                    }

                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_follow', $my_follow);

                    if ($added) {
                        felan_get_data_ajax_notification($company_id, 'add-follow-company');
                        $ajax_response = array('added' => true, 'message' => esc_html__('Added', 'felan-framework'));
                    }
                    if ($removed) {
                        $ajax_response = array(
                            'added' => false,
                            'message' => esc_html__('Removed', 'felan-framework')
                        );
                    }
                }
            } else {
                $ajax_response = array(
                    'added' => false,
                    'message' => esc_html__('You are not login', 'felan-framework')
                );
            }
            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * Follow Company
         */
        public function felan_add_to_invite()
        {
            $freelancer_id = isset($_REQUEST['freelancer_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_id'])) : '';
            $author_id = isset($_REQUEST['author_id']) ? felan_clean(wp_unslash($_REQUEST['author_id'])) : '';
            $jobs_id = isset($_REQUEST['jobs_id']) ? felan_clean(wp_unslash($_REQUEST['jobs_id'])) : '';
            $list_jobs = isset($_REQUEST['list_jobs']) ? felan_clean(wp_unslash($_REQUEST['list_jobs'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $my_invite = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'my_invite', true);
            $invite_freelancer = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'invite_freelancer', true);
            if (empty($my_invite)) {
                $my_invite = array(0);
            }
            if (empty($invite_freelancer)) {
                $invite_freelancer = array(0);
            }

            if (!empty($jobs_id)) {
                //my_invite
                $jobs_diff = array_diff(json_decode($list_jobs), $jobs_id);
                foreach ($jobs_id as $invites) {
                    if (!empty($my_invite) && !in_array($invites, $my_invite)) {
                        array_push($my_invite, $invites);
                    }
                }
                if (!empty($jobs_diff)) {
                    foreach ($jobs_diff as $job_diff) {
                        $key_my_invite = array_search($job_diff, $my_invite);
                        if ($key_my_invite !== false) {
                            unset($my_invite[$key_my_invite]);
                        }
                    }
                }
                felan_get_data_ajax_notification($freelancer_id, 'add-invite');
            } else {
                foreach (json_decode($list_jobs) as $list_job) {
                    $key_my_invite = array_search($list_job, $my_invite);
                    if ($key_my_invite !== false) {
                        unset($my_invite[$key_my_invite]);
                    }
                }
            }

            // Send Email
            $jobs_title = array();
            foreach (json_decode($list_jobs) as $job) {
                $jobs_title[] = get_the_title($job);
            }
            $args = array(
                'employer_name' => $current_user->user_login,
                'jobs_invite' => implode(",", $jobs_title),
            );
            $args_freelancer     = array(
                'post_type'      => 'freelancer',
                'posts_per_page' => 1,
                'author'         => $author_id,
            );
            $current_user_posts = get_posts($args_freelancer);
            $freelancer_id       = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';;
            $freelancer_email = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_email', true);
            felan_send_email($freelancer_email, 'mail_job_invite', $args);

            //invite_freelancer
            if (!empty($jobs_id) && !in_array($freelancer_id, $invite_freelancer)) {
                array_push($invite_freelancer, $freelancer_id);
            } else if (empty($jobs_id) && in_array($freelancer_id, $invite_freelancer)) {
                $key_invite_freelancer = array_search($freelancer_id, $invite_freelancer);
                if ($key_invite_freelancer !== false) {
                    unset($invite_freelancer[$key_invite_freelancer]);
                }
            }

            update_user_meta($author_id, FELAN_METABOX_PREFIX . 'my_invite', $my_invite);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'invite_freelancer', $invite_freelancer);

            $ajax_response = array('success' => true);
            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * Follow Freelancer
         */
        public function felan_add_to_follow_freelancer()
        {
            global $current_user;
            $freelancer_id = $_POST['freelancer_id'];
            $freelancer_id = intval($freelancer_id);
            wp_get_current_user();
            $user_id = $current_user->ID;
            $added = $removed = false;
            $ajax_response = '';
            if ($user_id > 0) {
                $follow_freelancer = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'follow_freelancer', true);

                $paid_submission_type = felan_get_option('paid_submission_type');
                $package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
                $felan_profile = new Felan_Profile();
                $check_package = $felan_profile->user_package_available($user_id);
                $show_package_follow = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'show_package_company_freelancer_follow', true);
                $enable_company_package_follow = felan_get_option('enable_company_package_freelancer_follow');
                $company_package_number_freelancer_follow = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_freelancer_follow', true);

                if ($paid_submission_type == 'per_package' && $enable_company_package_follow === '1' && $show_package_follow === '1' && ($company_package_number_freelancer_follow <= 0 || ($check_package == -1 || $check_package == 0))) {
                    $ajax_response = array('package_expires' => true);
                } else {
                    if (!empty($follow_freelancer) && (!in_array($freelancer_id, $follow_freelancer))) {
                        array_push($follow_freelancer, $freelancer_id);
                        $added = true;
                    } else {
                        if (empty($follow_freelancer)) {
                            $follow_freelancer = array($freelancer_id);
                            $added = true;
                        } else {
                            //Delete favorite
                            $key = array_search($freelancer_id, $follow_freelancer);
                            if ($key !== false) {
                                unset($follow_freelancer[$key]);
                                $removed = true;
                            }
                        }
                    }

                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'follow_freelancer', $follow_freelancer);

                    if ($added) {
                        felan_get_data_ajax_notification($freelancer_id, 'add-follow-freelancer');
                        if (is_numeric($company_package_number_freelancer_follow) && $company_package_number_freelancer_follow - 1 >= 0) {
                            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_freelancer_follow', $company_package_number_freelancer_follow - 1);
                        }
                        $ajax_response = array('added' => true, 'message' => esc_html__('Added', 'felan-framework'));
                    }
                    if ($removed) {
                        $ajax_response = array(
                            'added' => false,
                            'message' => esc_html__('Removed', 'felan-framework')
                        );
                    }
                }
            } else {
                $ajax_response = array(
                    'added' => false,
                    'message' => esc_html__('You are not login', 'felan-framework')
                );
            }
            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * Download CV Freelancer
         */
        public function felan_freelancer_download_cv()
        {
            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            if ($user_id > 0) {
                $download_cv_freelancer = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'download_cv_freelancer', true);
                $paid_submission_type = felan_get_option('paid_submission_type');
                $package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
                $felan_profile = new Felan_Profile();
                $check_package = $felan_profile->user_package_available($user_id);
                $show_package_download_cv = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'show_package_company_download_cv', true);
                $enable_company_package_download_cv = felan_get_option('enable_company_package_download_cv');
                $company_package_number_download_cv = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_download_cv', true);

                if ($download_cv_freelancer < 1 && $paid_submission_type == 'per_package' && $enable_company_package_download_cv === '1' && $show_package_download_cv === '1' && ($company_package_number_download_cv <= 0 || ($check_package == -1 || $check_package == 0))) {
                    $ajax_response = array('message' => true);
                } else {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_download_cv', $company_package_number_download_cv - 1);
                    $ajax_response = array('message' => false);
                }
            }
            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * Services Write a review
         */
        public function felan_service_write_a_review()
        {
            $service_id = isset($_REQUEST['service_id']) ? felan_clean(wp_unslash($_REQUEST['service_id'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $comment_content = $salary_rating = $service_rating = $skill_rating = $work_rating = '';
            $comment_id = felan_comment_id_by_post_and_user($service_id, $user_id);
            if (!empty($comment_id)) {
                $salary_rating = intval(get_comment_meta($comment_id, 'service_salary_rating', true));
                $service_rating = intval(get_comment_meta($comment_id, 'service_service_rating', true));
                $skill_rating = intval(get_comment_meta($comment_id, 'service_skill_rating', true));
                $work_rating = intval(get_comment_meta($comment_id, 'service_work_rating', true));

                $comment = get_comment($comment_id);
                if ($comment) {
                    $comment_content = $comment->comment_content;
                }
                update_post_meta($service_id, FELAN_METABOX_PREFIX . 'has_service_review', '1');
            } else {
                update_post_meta($service_id, FELAN_METABOX_PREFIX . 'has_service_review', '0');
            }

            ob_start(); ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_salary<?php echo $i; ?>" name="rating_salary" value="<?php echo $i; ?>" <?php checked($salary_rating, $i); ?> />
                                <label for="rating_salary<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_service<?php echo $i; ?>" name="rating_service" value="<?php echo $i; ?>" <?php checked($service_rating, $i); ?> />
                                <label for="rating_service<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_skill<?php echo $i; ?>" name="rating_skill" value="<?php echo $i; ?>" <?php checked($skill_rating, $i); ?> />
                                <label for="rating_skill<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_work<?php echo $i; ?>" name="rating_work" value="<?php echo $i; ?>" <?php checked($work_rating, $i); ?> />
                                <label for="rating_work<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
                        </fieldset>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <label style="margin-top: 8px"><?php esc_html_e('Comments', 'felan-framework'); ?></label>
                    <textarea class="form-control" name="message" placeholder="<?php esc_attr_e('Enter your comments', 'felan-framework'); ?>"><?php echo esc_textarea($comment_content); ?></textarea>
                </div>
            </div>
        <?php
            $html_review = ob_get_clean();

            $ajax_response = array('html_review' => $html_review);
            echo json_encode($ajax_response);

            wp_die();
        }

        /**
         * Services view review
         */
        public function felan_service_view_review()
        {
            $service_id = isset($_REQUEST['service_id']) ? felan_clean(wp_unslash($_REQUEST['service_id'])) : '';

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $comment_content = $salary_rating = $service_rating = $skill_rating = $work_rating = '';
            $comment_id = felan_comment_id_by_post_and_user($service_id, $user_id);
            if (!empty($comment_id)) {
                $salary_rating = intval(get_comment_meta($comment_id, 'service_salary_rating', true));
                $service_rating = intval(get_comment_meta($comment_id, 'service_service_rating', true));
                $skill_rating = intval(get_comment_meta($comment_id, 'service_skill_rating', true));
                $work_rating = intval(get_comment_meta($comment_id, 'service_work_rating', true));

                $comment = get_comment($comment_id);
                if ($comment) {
                    $comment_content = $comment->comment_content;
                }
                update_post_meta($service_id, FELAN_METABOX_PREFIX . 'has_service_review', '1');
            } else {
                update_post_meta($service_id, FELAN_METABOX_PREFIX . 'has_service_review', '0');
            }

            ob_start(); ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_salary<?php echo $i; ?>" name="rating_salary" value="<?php echo $i; ?>" <?php checked($salary_rating, $i); ?> />
                                <label for="rating_salary<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_service<?php echo $i; ?>" name="rating_service" value="<?php echo $i; ?>" <?php checked($service_rating, $i); ?> />
                                <label for="rating_service<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_skill<?php echo $i; ?>" name="rating_skill" value="<?php echo $i; ?>" <?php checked($skill_rating, $i); ?> />
                                <label for="rating_skill<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_work<?php echo $i; ?>" name="rating_work" value="<?php echo $i; ?>" <?php checked($work_rating, $i); ?> />
                                <label for="rating_work<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
                        </fieldset>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <p class="comment"><?php echo esc_html($comment_content); ?></p>
                </div>
            </div>
        <?php
            $html_review = ob_get_clean();

            $ajax_response = array('html_review' => $html_review);
            echo json_encode($ajax_response);

            wp_die();
        }

        /**
         * Freelancer view review
         */
        public function felan_freelancer_view_review()
        {
            $freelancer_id = isset($_REQUEST['freelancer_id']) ? intval(wp_unslash($_REQUEST['freelancer_id'])) : 0;

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $ratings = array(
                'salary' => 0,
                'freelancer' => 0,
                'skill' => 0,
                'work' => 0,
            );

            $comment_id = felan_comment_id_by_post_and_user($freelancer_id, $user_id);
            if (!empty($comment_id)) {
                $ratings['salary'] = intval(get_comment_meta($comment_id, 'freelancer_salary_rating', true));
                $ratings['freelancer'] = intval(get_comment_meta($comment_id, 'freelancer_freelancer_rating', true));
                $ratings['skill'] = intval(get_comment_meta($comment_id, 'freelancer_skill_rating', true));
                $ratings['work'] = intval(get_comment_meta($comment_id, 'freelancer_work_rating', true));
                $comment = get_comment($comment_id);
                if ($comment) {
                    $comment_content = $comment->comment_content;
                }
            }

            ob_start(); ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_team<?php echo $i; ?>" name="rating_team" value="<?php echo $i; ?>" <?php checked($ratings['freelancer'], $i); ?> />
                                <label for="rating_team<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_working<?php echo $i; ?>" name="rating_working" value="<?php echo $i; ?>" <?php checked($ratings['work'], $i); ?> />
                                <label for="rating_working<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_skill<?php echo $i; ?>" name="rating_skill" value="<?php echo $i; ?>" <?php checked($ratings['skill'], $i); ?> />
                                <label for="rating_skill<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_salary<?php echo $i; ?>" name="rating_salary" value="<?php echo $i; ?>" <?php checked($ratings['salary'], $i); ?> />
                                <label for="rating_salary<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
                        </fieldset>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <?php echo $comment_content; ?>
                </div>
            </div>
        <?php
            $html_review = ob_get_clean();

            // Send response as JSON
            $ajax_response = array('html_review' => $html_review);
            echo json_encode($ajax_response);

            wp_die();
        }


        /**
         * Company Write a review
         */
        public function felan_company_write_a_review()
        {
            // Sanitize input
            $company_id = isset($_REQUEST['company_id']) ? intval(wp_unslash($_REQUEST['company_id'])) : 0;

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $ratings = array(
                'salary' => 0,
                'company' => 0,
                'skill' => 0,
                'work' => 0,
            );

            $comment_id = felan_comment_id_by_post_and_user($company_id, $user_id);
            if (!empty($comment_id)) {
                $ratings['salary'] = intval(get_comment_meta($comment_id, 'company_salary_rating', true));
                $ratings['company'] = intval(get_comment_meta($comment_id, 'company_company_rating', true));
                $ratings['skill'] = intval(get_comment_meta($comment_id, 'company_skill_rating', true));
                $ratings['work'] = intval(get_comment_meta($comment_id, 'company_work_rating', true));
                $comment = get_comment($comment_id);
                if ($comment) {
                    $comment_content = $comment->comment_content;
                }
            }

            ob_start(); ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_salary<?php echo $i; ?>" name="rating_salary" value="<?php echo $i; ?>" <?php checked($ratings['salary'], $i); ?> />
                                <label for="rating_salary<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_company<?php echo $i; ?>" name="rating_company" value="<?php echo $i; ?>" <?php checked($ratings['company'], $i); ?> />
                                <label for="rating_company<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_skill<?php echo $i; ?>" name="rating_skill" value="<?php echo $i; ?>" <?php checked($ratings['skill'], $i); ?> />
                                <label for="rating_skill<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
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
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <input type="radio" id="rating_work<?php echo $i; ?>" name="rating_work" value="<?php echo $i; ?>" <?php checked($ratings['work'], $i); ?> />
                                <label for="rating_work<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                            <?php endfor; ?>
                        </fieldset>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <textarea class="form-control" name="message" placeholder="<?php esc_attr_e('Your review...', 'felan-framework'); ?>"><?php echo $comment_content; ?></textarea>
                </div>
            </div>
        <?php
            $html_review = ob_get_clean();

            // Send response as JSON
            $ajax_response = array('html_review' => $html_review);
            echo json_encode($ajax_response);

            wp_die();
        }

        /**
         * Freelancer Write a review
         */
        public function felan_freelancer_write_a_review()
        {
            // Sanitize input
            $freelancer_id = isset($_REQUEST['freelancer_id']) ? intval(wp_unslash($_REQUEST['freelancer_id'])) : 0;

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;

            $ratings = array(
                'team' => 0,
                'working' => 0,
                'skill' => 0,
                'salary' => 0,
            );

            $comment_id = felan_comment_id_by_post_and_user($freelancer_id, $user_id);
            if (!empty($comment_id)) {
                $ratings['team'] = intval(get_comment_meta($comment_id, 'freelancer_freelancer_rating', true));
                $ratings['working'] = intval(get_comment_meta($comment_id, 'freelancer_salary_rating', true));
                $ratings['skill'] = intval(get_comment_meta($comment_id, 'freelancer_skill_rating', true));
                $ratings['salary'] = intval(get_comment_meta($comment_id, 'freelancer_work_rating', true));
                $comment = get_comment($comment_id);
                if ($comment) {
                    $comment_content = $comment->comment_content;
                }
            }

            ob_start(); ?>
            <div class="content-popup-review">
                <div class="row">
                    <div class="form-group col-md-6">
                        <div class="rating-bars">
                            <div class="rate-title">
                                <span><?php esc_html_e('Team work', 'felan-framework'); ?></span>
                                <div class="tip" data-tip-content="<?php esc_html_e('Good teamwork spirit', 'felan-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Good teamwork spirit', 'felan-framework'); ?></div>
                                </div>
                            </div>
                            <fieldset class="rate">
                                <?php for ($i = 5; $i >= 1; $i--) : ?>
                                    <input type="radio" id="rating_team<?php echo $i; ?>" name="rating_team" value="<?php echo $i; ?>" <?php checked($ratings['team'], $i); ?> />
                                    <label for="rating_team<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                                <?php endfor; ?>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <div class="rating-bars">
                            <div class="rate-title">
                                <span><?php esc_html_e('Working attitude', 'felan-framework'); ?></span>
                                <div class="tip" data-tip-content="<?php esc_html_e('Progressive working attitude', 'felan-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Progressive working attitude', 'felan-framework'); ?></div>
                                </div>
                            </div>
                            <fieldset class="rate">
                                <?php for ($i = 5; $i >= 1; $i--) : ?>
                                    <input type="radio" id="rating_working<?php echo $i; ?>" name="rating_working" value="<?php echo $i; ?>" <?php checked($ratings['working'], $i); ?> />
                                    <label for="rating_working<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                                <?php endfor; ?>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <div class="rating-bars">
                            <div class="rate-title">
                                <span><?php esc_html_e('Skill & Experience', 'felan-framework'); ?></span>
                                <div class="tip" data-tip-content="<?php esc_html_e('Skills and experience meet well', 'felan-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Skills and experience meet well', 'felan-framework'); ?></div>
                                </div>
                            </div>
                            <fieldset class="rate">
                                <?php for ($i = 5; $i >= 1; $i--) : ?>
                                    <input type="radio" id="rating_skill<?php echo $i; ?>" name="rating_skill" value="<?php echo $i; ?>" <?php checked($ratings['skill'], $i); ?> />
                                    <label for="rating_skill<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                                <?php endfor; ?>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <div class="rating-bars">
                            <div class="rate-title">
                                <span><?php esc_html_e('Offered Salary', 'felan-framework'); ?></span>
                                <div class="tip" data-tip-content="<?php esc_html_e('Suitable salary', 'felan-framework'); ?>">
                                    <div class="tip-content"><?php esc_html_e('Suitable salary', 'felan-framework'); ?></div>
                                </div>
                            </div>
                            <fieldset class="rate">
                                <?php for ($i = 5; $i >= 1; $i--) : ?>
                                    <input type="radio" id="rating_salary<?php echo $i; ?>" name="rating_salary" value="<?php echo $i; ?>" <?php checked($ratings['salary'], $i); ?> />
                                    <label for="rating_salary<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                                <?php endfor; ?>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <textarea class="form-control" name="message" placeholder="<?php esc_attr_e('Your review...', 'felan-framework'); ?>"><?php echo $comment_content; ?></textarea>
                    </div>
                </div>
            </div>
            <?php
            $html_review = ob_get_clean();

            // Send response as JSON
            $ajax_response = array('html_review' => $html_review);
            echo json_encode($ajax_response);

            wp_die();
        }

        /**
         * upload thumbnail
         */
        public function felan_thumbnail_upload_ajax()
        {
            $nonce = isset($_REQUEST['nonce']) ? felan_clean(wp_unslash($_REQUEST['nonce'])) : '';
            if (!wp_verify_nonce($nonce, 'felan_thumbnail_allow_upload')) {
                $ajax_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }

            $submitted_file = $_FILES['felan_thumbnail_upload_file']; // WPCS: sanitization ok, input var ok.

            $uploaded_image = wp_handle_upload($submitted_file, array('test_form' => false));

            if (isset($uploaded_image['file'])) {
                $file_name = basename($submitted_file['name']);
                $file_type = wp_check_filetype($uploaded_image['file']);
                $attachment_details = array(
                    'guid' => $uploaded_image['url'],
                    'post_mime_type' => $file_type['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id = wp_insert_attachment($attachment_details, $uploaded_image['file']);
                $attach_data = wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                $thumbnail_url = wp_get_attachment_url($attach_id);
                $fullimage_url = wp_get_attachment_image_src($attach_id, 'full');

                $ajax_response = array(
                    'success' => true,
                    'title' => $file_name,
                    'url' => $thumbnail_url,
                    'attachment_id' => $attach_id,
                    'full_image' => $fullimage_url[0]
                );
                echo json_encode($ajax_response);
                wp_die();
            } else {
                $ajax_response = array(
                    'success' => false,
                    'reason' => esc_html__('Image upload failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }
        }

        /**
         * Remove thumbnail img
         */
        public function felan_thumbnail_remove_ajax()
        {
            $nonce = isset($_POST['removeNonce']) ? felan_clean(wp_unslash($_POST['removeNonce'])) : '';
            $user_id = isset($_POST['user_id']) ? felan_clean(wp_unslash($_POST['user_id'])) : '';
            if (!wp_verify_nonce($nonce, 'felan_thumbnail_allow_upload')) {
                $json_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check fails', 'felan-framework')
                );
                echo json_encode($json_response);
                wp_die();
            }
            $success = false;
            if (isset($_POST['attachment_id'])) {
                $attachment_id = absint(wp_unslash($_POST['attachment_id']));
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
                'url' => get_the_author_meta('author_avatar_image_url', $user_id),
            );

            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * upload avatar
         */
        public function felan_avatar_upload_ajax()
        {
            $nonce = isset($_REQUEST['nonce']) ? felan_clean(wp_unslash($_REQUEST['nonce'])) : '';
            if (!wp_verify_nonce($nonce, 'felan_avatar_allow_upload')) {
                $ajax_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }

            $submitted_file = $_FILES['felan_avatar_upload_file']; // WPCS: sanitization ok, input var ok.

            $uploaded_image = wp_handle_upload($submitted_file, array('test_form' => false));

            if (isset($uploaded_image['file'])) {
                $file_name = basename($submitted_file['name']);
                $file_type = wp_check_filetype($uploaded_image['file']);
                $attachment_details = array(
                    'guid' => $uploaded_image['url'],
                    'post_mime_type' => $file_type['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id = wp_insert_attachment($attachment_details, $uploaded_image['file']);
                $attach_data = wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                $avatar_url = wp_get_attachment_url($attach_id);
                $fullimage_url = wp_get_attachment_image_src($attach_id, 'full');

                $ajax_response = array(
                    'success' => true,
                    'title' => $file_name,
                    'url' => $avatar_url,
                    'attachment_id' => $attach_id,
                    'full_image' => $fullimage_url[0]
                );
                echo json_encode($ajax_response);
                wp_die();
            } else {
                $ajax_response = array(
                    'success' => false,
                    'reason' => esc_html__('Image upload failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }
        }

        /**
         * Remove avatar img
         */
        public function felan_avatar_remove_ajax()
        {
            $nonce = isset($_POST['removeNonce']) ? felan_clean(wp_unslash($_POST['removeNonce'])) : '';
            $user_id = isset($_POST['user_id']) ? felan_clean(wp_unslash($_POST['user_id'])) : '';
            if (!wp_verify_nonce($nonce, 'felan_avatar_allow_upload')) {
                $json_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check fails', 'felan-framework')
                );
                echo json_encode($json_response);
                wp_die();
            }
            $success = false;
            if (isset($_POST['attachment_id'])) {
                $attachment_id = absint(wp_unslash($_POST['attachment_id']));
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
                'url' => get_the_author_meta('author_avatar_image_url', $user_id),
            );

            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * Wishlist Project
         */
        public function felan_project_wishlist()
        {
            global $current_user;
            $user_id = $current_user->ID;
            $project_id = $_POST['project_id'];
            $project_id = intval($project_id);
            $added = $removed = false;
            $ajax_response = '';

            if ($user_id > 0) {
                $project_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_wishlist', true);

                if (!empty($project_wishlist) && (!in_array($project_id, $project_wishlist))) {
                    array_push($project_wishlist, $project_id);
                    $added = true;
                } else {
                    if (empty($project_wishlist)) {
                        $project_wishlist = array($project_id);
                        $added = true;
                    } else {
                        $key = array_search($project_id, $project_wishlist);
                        if ($key !== false) {
                            unset($project_wishlist[$key]);
                            $removed = true;
                        }
                    }
                }
                update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_wishlist', $project_wishlist);

                if ($added) {
                    $ajax_response = array('added' => true, 'message' => esc_html__('Added', 'felan-framework'));
                }
                if ($removed) {
                    $ajax_response = array('added' => false, 'message' => esc_html__('Removed', 'felan-framework'));
                }
            } else {
                $ajax_response = array(
                    'added' => false,
                    'message' => esc_html__('You are not login', 'felan-framework')
                );
            }
            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * upload custom_image
         */
        public function felan_custom_image_upload_ajax()
        {
            $nonce = isset($_REQUEST['nonce']) ? felan_clean(wp_unslash($_REQUEST['nonce'])) : '';
            $custom_image_id = isset($_REQUEST['custom_image_id']) ? felan_clean(wp_unslash($_REQUEST['custom_image_id'])) : '';

            if (!wp_verify_nonce($nonce, 'felan_custom_image_allow_upload')) {
                $ajax_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }

            $submitted_file = $_FILES['felan_custom_image_upload_file_' . $custom_image_id]; // WPCS: sanitization ok, input var ok.

            $uploaded_image = wp_handle_upload($submitted_file, array('test_form' => false));

            if (isset($uploaded_image['file'])) {
                $file_name = basename($submitted_file['name']);
                $file_type = wp_check_filetype($uploaded_image['file']);
                $attachment_details = array(
                    'guid' => $uploaded_image['url'],
                    'post_mime_type' => $file_type['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id = wp_insert_attachment($attachment_details, $uploaded_image['file']);
                $attach_data = wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                $custom_image_url = wp_get_attachment_url($attach_id);
                $fullimage_url = wp_get_attachment_image_src($attach_id, 'full');

                $ajax_response = array(
                    'success' => true,
                    'title' => $file_name,
                    'url' => $custom_image_url,
                    'attachment_id' => $attach_id,
                    'full_image' => $fullimage_url[0]
                );
                echo json_encode($ajax_response);
                wp_die();
            } else {
                $ajax_response = array(
                    'success' => false,
                    'dsadsa' => $uploaded_image,
                    'reason' => esc_html__('Image upload failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }
        }

        /**
         * Remove custom_image img
         */
        public function felan_custom_image_remove_ajax()
        {
            $nonce = isset($_POST['removeNonce']) ? felan_clean(wp_unslash($_POST['removeNonce'])) : '';
            $user_id = isset($_POST['user_id']) ? felan_clean(wp_unslash($_POST['user_id'])) : '';
            if (!wp_verify_nonce($nonce, 'felan_custom_image_allow_upload')) {
                $json_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check fails', 'felan-framework')
                );
                echo json_encode($json_response);
                wp_die();
            }
            $success = false;
            if (isset($_POST['attachment_id'])) {
                $attachment_id = absint(wp_unslash($_POST['attachment_id']));
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
                'url' => get_the_author_meta('author_avatar_image_url', $user_id),
            );

            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * upload mess_image
         */
        public function felan_mess_image_upload_ajax()
        {
            $nonce = isset($_REQUEST['nonce']) ? felan_clean(wp_unslash($_REQUEST['nonce'])) : '';
            $mess_image_id = isset($_REQUEST['mess_image_id']) ? felan_clean(wp_unslash($_REQUEST['mess_image_id'])) : '';

            if (!wp_verify_nonce($nonce, 'felan_mess_image_allow_upload')) {
                $ajax_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }

            $submitted_file = $_FILES['felan_mess_image_upload_file_' . $mess_image_id]; // WPCS: sanitization ok, input var ok.

            $uploaded_image = wp_handle_upload($submitted_file, array('test_form' => false));

            if (isset($uploaded_image['file'])) {
                $file_name = basename($submitted_file['name']);
                $file_type = wp_check_filetype($uploaded_image['file']);
                $attachment_details = array(
                    'guid' => $uploaded_image['url'],
                    'post_mime_type' => $file_type['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id = wp_insert_attachment($attachment_details, $uploaded_image['file']);
                $attach_data = wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                $mess_image_url = wp_get_attachment_url($attach_id);
                $fullimage_url = wp_get_attachment_image_src($attach_id, 'full');

                $ajax_response = array(
                    'success' => true,
                    'title' => $file_name,
                    'url' => $mess_image_url,
                    'attachment_id' => $attach_id,
                    'full_image' => $fullimage_url[0]
                );
                echo json_encode($ajax_response);
                wp_die();
            } else {
                $ajax_response = array(
                    'success' => false,
                    'dsadsa' => $uploaded_image,
                    'reason' => esc_html__('Image upload failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }
        }


        /**
         * Remove mess_image img
         */
        public function felan_mess_image_remove_ajax()
        {
            $nonce = isset($_POST['removeNonce']) ? felan_clean(wp_unslash($_POST['removeNonce'])) : '';
            $user_id = isset($_POST['user_id']) ? felan_clean(wp_unslash($_POST['user_id'])) : '';
            if (!wp_verify_nonce($nonce, 'felan_mess_image_allow_upload')) {
                $json_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check fails', 'felan-framework')
                );
                echo json_encode($json_response);
                wp_die();
            }
            $success = false;
            if (isset($_POST['attachment_id'])) {
                $attachment_id = absint(wp_unslash($_POST['attachment_id']));
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
                'url' => get_the_author_meta('author_avatar_image_url', $user_id),
            );

            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * upload gallery
         */
        public function felan_gallery_upload_ajax()
        {
            $nonce = isset($_REQUEST['nonce']) ? felan_clean(wp_unslash($_REQUEST['nonce'])) : '';
            if (!wp_verify_nonce($nonce, 'felan_gallery_allow_upload')) {
                $ajax_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }

            $submitted_file = $_FILES['felan_gallery_upload_file']; // WPCS: sanitization ok, input var ok.

            $uploaded_image = wp_handle_upload($submitted_file, array('test_form' => false));

            if (isset($uploaded_image['file'])) {
                $file_name = basename($submitted_file['name']);
                $file_type = wp_check_filetype($uploaded_image['file']);
                $attachment_details = array(
                    'guid' => $uploaded_image['url'],
                    'post_mime_type' => $file_type['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id = wp_insert_attachment($attachment_details, $uploaded_image['file']);
                $attach_data = wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);
                $gallery_url = wp_get_attachment_url($attach_id);
                $fullimage_url = wp_get_attachment_image_src($attach_id, 'full');

                $ajax_response = array(
                    'success' => true,
                    'title' => $file_name,
                    'url' => $gallery_url,
                    'attachment_id' => $attach_id,
                    'full_image' => $fullimage_url[0]
                );
                echo json_encode($ajax_response);
                wp_die();
            } else {
                $ajax_response = array(
                    'success' => false,
                    'reason' => esc_html__('Image upload failed!', 'felan-framework')
                );
                echo json_encode($ajax_response);
                wp_die();
            }
        }

        /**
         * Remove gallery img
         */
        public function felan_gallery_remove_ajax()
        {
            $nonce = isset($_POST['removeNonce']) ? felan_clean(wp_unslash($_POST['removeNonce'])) : '';
            $user_id = isset($_POST['user_id']) ? felan_clean(wp_unslash($_POST['user_id'])) : '';
            if (!wp_verify_nonce($nonce, 'felan_gallery_allow_upload')) {
                $json_response = array(
                    'success' => false,
                    'reason' => esc_html__('Security check fails', 'felan-framework')
                );
                echo json_encode($json_response);
                wp_die();
            }
            $success = false;
            if (isset($_POST['attachment_id'])) {
                $attachment_id = absint(wp_unslash($_POST['attachment_id']));
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
                'url' => get_the_author_meta('author_avatar_image_url', $user_id),
            );

            echo json_encode($ajax_response);
            wp_die();
        }

        /**
         * Freelancer Print Ajax
         */
        public function felan_freelancer_print_ajax()
        {
            $freelancer_id = isset($_REQUEST['freelancer_id']) ? felan_clean(wp_unslash($_REQUEST['freelancer_id'])) : '';
            if (empty($freelancer_id)) {
                return;
            }
            $isRTL = 'false';
            if (isset($_POST['isRTL'])) {
                $isRTL = sanitize_text_field($_POST['isRTL']);
            }
            felan_get_template('freelancer/print.php', array('freelancer_id' => $freelancer_id, 'isRTL' => $isRTL));
            wp_die();
        }

        /**
         * Select Country
         */
        public function felan_select_country()
        {
            $country = isset($_REQUEST['country']) ? felan_clean(wp_unslash($_REQUEST['country'])) : '';
            $post_type = isset($_REQUEST['post_type']) ? felan_clean(wp_unslash($_REQUEST['post_type'])) : '';

            $taxonomy = 'felan_state';
            $taxonomy_key = 'felan-state-country';

            $taxonomy_terms = get_categories(
                array(
                    'taxonomy' => $taxonomy,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'parent' => 0,
                    'meta_query' => array(
                        array(
                            'key' => $taxonomy_key,
                            'value' => $country,
                            'compare' => '=',
                        )
                    )
                )
            );

            ob_start();
            foreach ($taxonomy_terms as $terms) {
                echo '<option value="' . $terms->term_id . '">' . $terms->name . '</option>';
            }
            $state_html = ob_get_clean();

            $ajax_response = array('success' => true, 'state_html' => $state_html);

            echo json_encode($ajax_response);

            wp_die();
        }

        /**
         * Select State
         */
        public function felan_select_state()
        {
            $state = isset($_REQUEST['state']) ? felan_clean(wp_unslash($_REQUEST['state'])) : '';
            $post_type = isset($_REQUEST['post_type']) ? felan_clean(wp_unslash($_REQUEST['post_type'])) : '';

            $taxonomy = 'felan_location';

            $taxonomy_key = 'felan-location-state';

            $taxonomy_terms = get_categories(
                array(
                    'taxonomy' => $taxonomy,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'parent' => 0,
                    'meta_query' => array(
                        array(
                            'key' => $taxonomy_key,
                            'value' => $state,
                            'compare' => '=',
                        )
                    )
                )
            );

            ob_start();
            foreach ($taxonomy_terms as $terms) {
                echo '<option value="' . $terms->term_id . '">' . $terms->name . '</option>';
            }
            $city_html = ob_get_clean();

            $ajax_response = array('success' => true, 'city_html' => $city_html);

            echo json_encode($ajax_response);

            wp_die();
        }

        /**
         * Elementor
         */
        public function felan_el_jobs_pagination_ajax()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? felan_clean(wp_unslash($_REQUEST['item_amount'])) : '4';
            $paged = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $page = isset($_REQUEST['paged']) ? felan_clean(wp_unslash($_REQUEST['paged'])) : '';
            $layout = isset($_REQUEST['layout']) ? felan_clean(wp_unslash($_REQUEST['layout'])) : '';
            $type_pagination = isset($_REQUEST['type_pagination']) ? felan_clean(wp_unslash($_REQUEST['type_pagination'])) : '';
            $include_ids = isset($_REQUEST['include_ids']) ? felan_clean(wp_unslash($_REQUEST['include_ids'])) : '';
            $type_query = isset($_REQUEST['type_query']) ? felan_clean(wp_unslash($_REQUEST['type_query'])) : '';
            $orderby = isset($_REQUEST['orderby']) ? felan_clean(wp_unslash($_REQUEST['orderby'])) : '';
            $jobs_categories = isset($_REQUEST['jobs_categories']) ? felan_clean(wp_unslash($_REQUEST['jobs_categories'])) : '';
            $jobs_skills = isset($_REQUEST['jobs_skills']) ? felan_clean(wp_unslash($_REQUEST['jobs_skills'])) : '';
            $jobs_type = isset($_REQUEST['jobs_type']) ? felan_clean(wp_unslash($_REQUEST['jobs_type'])) : '';
            $jobs_location = isset($_REQUEST['jobs_location']) ? felan_clean(wp_unslash($_REQUEST['jobs_location'])) : '';
            $jobs_career = isset($_REQUEST['jobs_career']) ? felan_clean(wp_unslash($_REQUEST['jobs_career'])) : '';
            $jobs_experience = isset($_REQUEST['jobs_experience']) ? felan_clean(wp_unslash($_REQUEST['jobs_experience'])) : '';

            $args = array(
                'post_type' => 'jobs',
                'paged' => $paged,
                'ignore_sticky_posts' => 1,
                'post_status' => 'publish',
            );

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            //Query
            $tax_query = array(
                array(
                    'key' => 'felan-enable_jobs_package_expires',
                    'value' => 0,
                    'compare' => '=='
                )
            );
            $meta_query = array();

            $include_ids = json_decode($include_ids);
            if (!empty($include_ids) && $type_query == 'title') {
                $args['post__in'] = $include_ids;
            }

            if ($type_query == 'orderby') {
                if (!empty($orderby)) {
                    if ($orderby == 'featured') {
                        $meta_query[] = array(
                            'key' => FELAN_METABOX_PREFIX . 'jobs_featured',
                            'value' => 1,
                            'type' => 'NUMERIC',
                            'compare' => '=',
                        );
                    }
                    if ($orderby == 'oldest') {
                        $args['orderby'] = array(
                            'menu_order' => 'DESC',
                            'date' => 'ASC',
                        );
                    }
                    if ($orderby == 'newest') {
                        $args['orderby'] = array(
                            'menu_order' => 'ASC',
                            'date' => 'DESC',
                        );
                    }
                    if ($orderby == 'random') {
                        $args['meta_key'] = '';
                        $args['orderby'] = 'rand';
                        $args['order'] = 'ASC';
                    }
                }
            }

            if ($jobs_categories) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-categories',
                    'field' => 'term_id',
                    'terms' => $jobs_categories,
                );
            }
            if ($jobs_skills) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-skills',
                    'field' => 'term_id',
                    'terms' => $jobs_skills,
                );
            }
            if ($jobs_type) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-type',
                    'field' => 'term_id',
                    'terms' => $jobs_type,
                );
            }
            if ($jobs_location) {
                $tax_query[] = array(
                    'taxonomy' => 'felan_location',
                    'field' => 'term_id',
                    'terms' => $jobs_location,
                );
            }
            if ($jobs_career) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-career',
                    'field' => 'term_id',
                    'terms' => $jobs_career,
                );
            }
            if ($jobs_experience) {
                $tax_query[] = array(
                    'taxonomy' => 'jobs-experience',
                    'field' => 'term_id',
                    'terms' => $jobs_experience,
                );
            }

            if (!empty($tax_query)) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            if (!empty($meta_query)) {
                $args['meta_query'] = array(
                    'relation' => 'AND',
                    $meta_query
                );
            }

            $data = new \WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;

            $hidden_pagination = '';
            if ($paged == $max_num_pages) {
                $hidden_pagination = 1;
            }

            if ($type_pagination == 'number') {
                $pagination = paginate_links(apply_filters('felan_pagination_args', array(
                    'total' => $max_num_pages,
                    'current' => $paged,
                    'mid_size' => 1,
                    'type' => 'array',
                    //'add_args'  => array_map( 'urlencode', $args ),
                    'prev_text' => __('<i class="far fa-chevron-left"></i>', 'felan-framework'),
                    'next_text' => __('<i class="far fa-chevron-right"></i>', 'felan-framework'),
                )));
            } else {
                $pagination = '<a class="page-numbers next" href="#"><span>' . __('Load More', 'felan-framework') . '</span><span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span></a>';
            }

            ob_start();

            if ($total_post > 0) { ?>
                <?php while ($data->have_posts()) : $data->the_post(); ?>
                    <?php felan_get_template('content-jobs.php', array(
                        'jobs_layout' => $layout,
                    )); ?>
                <?php endwhile; ?>
<?php }
            wp_reset_postdata();

            $jobs_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array(
                    'success' => true,
                    'pagination' => $pagination,
                    'layout' => $type_pagination,
                    'jobs_html' => $jobs_html,
                    'total_post' => $total_post,
                    'page' => $page,
                    'hidden_pagination' => $hidden_pagination
                ));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }
    }
}
