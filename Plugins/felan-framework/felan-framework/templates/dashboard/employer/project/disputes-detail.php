<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'project-disputes-detail');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'project-disputes-detail',
    'felan_project_disputes_detail_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
    )
);

$felan_image_type = felan_get_option('felan_image_type');
$cv_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
$text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_html__('Upload File ', 'felan-framework');
$upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
$url = FELAN_AJAX_URL . '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);
wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'upload-cv');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'upload-cv',
    'felan_upload_cv_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'title' => esc_html__('Valid file formats', 'felan-framework'),
        'cv_file' => $felan_image_type,
        'cv_max_file_size' => $cv_max_file_size,
        'upload_nonce' => $upload_nonce,
        'url' => $url,
        'text' => $text,
    )
);

global $current_user;
$user_id = $current_user->ID;
$disputes_id = isset($_GET['disputes_id']) ? felan_clean(wp_unslash($_GET['disputes_id'])) : '';
$applicants_id = isset($_GET['order_id']) ? felan_clean(wp_unslash($_GET['order_id'])) : '';
$project_id = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_project_id', true);
$author_id = get_post_field('post_author', $project_id);
$author_name = get_the_author_meta('display_name', $author_id);
$thumbnail = get_the_post_thumbnail_url($project_id, '70x70');
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);
$project_career = get_the_terms($project_id, 'project-career');
$project_language = get_the_terms($project_id, 'project-language');

$author_freelancer_id = get_post_field('post_author', $applicants_id);
$project_dashboard_link = felan_get_permalink('project_dashboard');
$freelancer_id = '';
if (!empty($author_id)) {
    $args_freelancer = array(
        'post_type' => 'freelancer',
        'posts_per_page' => 1,
        'author' => $author_freelancer_id,
    );
    $current_user_posts = get_posts($args_freelancer);
    $freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
    $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_freelancer_id);
}

$status = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'project_disputes_status', true);

$proposal_price = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_price', true);
$proposal_time = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_time', true);
$proposal_message = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_message', true);
$proposal_maximum_time = get_post_meta($applicants_id, FELAN_METABOX_PREFIX . 'proposal_maximum_time', true);
$currency_sign_default = felan_get_option('currency_sign_default');
$currency_position = felan_get_option('currency_position');
if ($currency_position == 'before') {
    $proposal_total_price = $proposal_price . $currency_sign_default;
} else {
    $proposal_total_price = $currency_sign_default . $proposal_price;
}

//Message
$user_name = $current_user->display_name;
$sender_id = get_current_user_id();

$sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_project_messages_employer_' . $disputes_id, true);
$receive_messages = get_user_meta($author_freelancer_id, FELAN_PLUGIN_PREFIX . 'disputes_project_messages_freelancer_' . $disputes_id, true);

$all_messages = [];

if ($sender_messages && is_array($sender_messages)) {
    foreach ($sender_messages as $message) {
        $message['from'] = 'sender';
        $message['timestamp'] = strtotime($message['time']);
        $all_messages[] = $message;
    }
}

if ($receive_messages && is_array($receive_messages)) {
    foreach ($receive_messages as $message) {
        $message['from'] = 'receiver';
        $message['timestamp'] = strtotime($message['time']);
        $all_messages[] = $message;
    }
}

usort($all_messages, function ($a, $b) {
    return $a['timestamp'] - $b['timestamp'];
});
?>
<div class="entry-my-page order-detail-project">
    <div class="felan-project-order-detail">
        <div class="row">
            <div class="col-md-8">
                <div class="order-content">
                    <div class="order-top">
                        <div class="info">
                            <?php if (!empty($thumbnail)) : ?>
                                <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt=""/>
                            <?php endif; ?>
                            <div class="content">
                                <h3 class="title-my-project">
                                    <a href="<?php echo get_the_permalink($project_id) ?>">
                                        <?php echo get_the_title($project_id); ?>
                                    </a>
                                </h3>
                                <p>
                                    <span><?php echo esc_html__('by', 'felan-framework') ?></span>
                                    <span class="author"><?php echo $author_name; ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="status">
                            <?php if($status == 'close') : ?>
                                <span class="label label-close tooltip"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                            <?php elseif ($status == 'refund') : ?>
                                <span class="label label-open tooltip"><?php esc_html_e('Refunded', 'felan-framework') ?></span>
                            <?php else : ?>
                                <span class="label label-inprogress tooltip"><?php esc_html_e('Open', 'felan-framework') ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="order-center">
                        <div class="order-center-left">
                            <div class="avatar-inner">
                                <?php if (!empty($freelancer_avatar)) : ?>
                                    <div class="image-applicants"><img class="image-freelancers"
                                                                       src="<?php echo esc_url($freelancer_avatar) ?>"
                                                                       alt=""/></div>
                                <?php else : ?>
                                    <div class="image-applicants"><i class="far fa-camera"></i></div>
                                <?php endif; ?>
                                <div class="info-details">
                                    <h3>
                                        <a href="<?php echo esc_url(get_permalink($freelancer_id)); ?>">
                                            <?php echo get_the_title($freelancer_id); ?>
                                        </a>
                                    </h3>
                                    <?php echo felan_get_total_rating('freelancer', $freelancer_id); ?>
                                </div>
                            </div>
                        </div>
                        <div class="order-center-right">
                            <p class="price"><?php echo sprintf(esc_html__('Budget: %1s', 'felan-framework'), $proposal_total_price) ?></p>
                            <?php if ($projects_budget_show == 'hourly') : ?>
                                <p><?php echo sprintf(esc_html__('in %2s hours', 'felan-framework'), $proposal_maximum_time) ?></p>
                            <?php else: ?>
                                <p><?php echo sprintf(esc_html__('in %2s', 'felan-framework'), $proposal_maximum_time) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="order-center-bot">
                        <div class="message-inner">
                            <h4><?php echo esc_html__('Cover letter', 'felan-framework') ?></h4>
                            <?php echo esc_html($proposal_message); ?>
                        </div>
                    </div>
                    <input type="hidden" name="order_price" value="<?php echo esc_attr($proposal_price); ?>">
                    <div class="order-bottom">
                        <div class="list-chat">
                            <?php if (!empty($all_messages)) { ?>
                                <div class="order-history">
                                    <?php
                                    foreach ($all_messages as $message) {
                                        $is_sender = ($message['from'] === 'sender');
                                        $avatar = get_the_author_meta('author_avatar_image_url', $message['sender_id']);
                                        ?>
                                        <div class="history-item <?php echo esc_attr($is_sender ? 'sender' : 'receiver'); ?>">
                                            <div class="info-history">
                                                <?php if (!empty($avatar)) : ?>
                                                    <a href="<?php echo esc_url(get_permalink($message['sender_id'])); ?>">
                                                        <img class="avatar" src="<?php echo esc_attr($avatar); ?>"
                                                             alt=""/>
                                                    </a>
                                                <?php endif; ?>

                                                <div class="info">
                                            <span class="name">
                                                <?php echo esc_html($is_sender ? esc_html__('You', 'felan-framework') : get_the_author_meta('display_name', $message['sender_id'])); ?>
                                            </span>
                                                    <?php if (!empty($message['date'])) : ?>
                                                        <span><?php echo esc_html($message['date']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="content">
                                                <?php if (!empty($message['message_content'])) : ?>
                                                    <?php echo esc_html($message['message_content']); ?>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (!empty($message['attachment_url']) && !empty($message['attachment_title'])) : ?>
                                                <div class="download">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10 16H14" stroke="#111111" stroke-width="1.5"
                                                              stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M9.72796 3H7.5C6.25736 3 5.25 4.00736 5.25 5.25V18.75C5.25 19.9926 6.25736 21 7.5 21H16.5C17.7426 21 18.75 19.9926 18.75 18.75V12M9.72796 3C10.9706 3 12 4.00736 12 5.25V7.5C12 8.74264 13.0074 9.75 14.25 9.75H16.5C17.7426 9.75 18.75 10.7574 18.75 12M9.72796 3C13.4179 3 18.75 8.3597 18.75 12"
                                                              stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                              stroke-linejoin="round"/>
                                                    </svg>
                                                    <span class="title"><?php echo esc_html($message['attachment_title']); ?></span>
                                                    <a href="<?php echo esc_url($message['attachment_url']); ?>">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                             xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M4 16.0042V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V16"
                                                                  stroke="#111111" stroke-width="1.5"
                                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M12 4.5V15.5" stroke="black" stroke-width="1.5"
                                                                  stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M15.5 12L12 15.5L8.5 12" stroke="black"
                                                                  stroke-width="1.5" stroke-linecap="round"
                                                                  stroke-linejoin="round"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($status == 'open') : ?>
                                <div class="order-chat">
                                    <form id="felan-form-message-disputes">
                                        <h4><?php echo esc_html__('Chat Now', 'felan-framework'); ?></h4>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <textarea id="message_content" name="message_content" rows="4" cols="50"
                                                          placeholder="<?php echo esc_attr__('Message', 'felan-framework') ?>"></textarea>
                                            </div>
                                            <div class="form-group col-md-12 felan-upload-cv">
                                                <div class="form-field">
                                                    <div id="cv_errors_log" class="errors-log"></div>
                                                    <div id="felan_cv_plupload_container"
                                                         class="file-upload-block preview">
                                                        <div class="felan_cv_file felan_add-cv">
                                                            <p id="felan_drop_cv">
                                                                <?php if (!empty($fileName)) { ?>
                                                                    <button type="button" id="felan_select_cv">
                                                                        <i class="far fa-arrow-from-bottom large"></i>
                                                                        <?php esc_html_e($fileName); ?>
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <button type="button" id="felan_select_cv">
                                                                        <i class="far fa-arrow-from-bottom large"></i>
                                                                        <?php echo esc_html__('Upload File', 'felan-framework'); ?>
                                                                    </button>
                                                                <?php } ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="info-image-type"><?php echo '(' . $felan_image_type . ')'; ?></p>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="message_error"></div>
                                                <?php if ($user_demo == 'yes') { ?>
                                                    <a class="felan-button button-block btn-add-to-message"
                                                       data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>"
                                                       href="#">
                                                        <?php esc_html_e('Send message', 'felan-framework') ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="#" class="felan-button button-block btn-send-message"
                                                       type="submit">
                                                        <?php echo esc_html__('Send message', 'felan-framework'); ?>
                                                        <span class="btn-loading"><i
                                                                    class="far fa-spinner fa-spin medium"></i></span>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <input type="hidden" name="recipient_id" id="recipient_id"
                                               value="<?php echo esc_attr($author_freelancer_id); ?>">
                                        <input type="hidden" name="disputes_id" id="disputes_id"
                                               value="<?php echo esc_attr($disputes_id); ?>">
                                        <input type="hidden" name="user_role" id="user_role" value="employer">
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="project_id" id="project_id" value="<?php echo esc_attr($project_id); ?>">
                <input type="hidden" name="order_id" id="order_id" value="<?php echo esc_attr($applicants_id); ?>">
            </div>
            <div class="col-md-4">
                <div class="project-info-sidebar block-archive-sidebar has-sticky">
                    <div class="project-info-warpper">
                        <div class="price-inner">
                            <p class="budget-show">
                                <?php if ($projects_budget_show == 'hourly') : ?>
                                    <?php echo esc_html__('Hourly Rate', 'felan-framework'); ?>
                                <?php else: ?>
                                    <?php echo esc_html__('Fixed Price', 'felan-framework'); ?>
                                <?php endif; ?>
                            </p>
                            <?php echo felan_get_budget_project($project_id); ?>
                        </div>
                        <ul class="info">
                            <li class="list-item">
                <span class="icon">
                   <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 4.5V9H12.375" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="9" r="6.75" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php if ($projects_budget_show == 'hourly') : ?>
                        <?php echo esc_html__('Estimated maximum hours', 'felan-framework'); ?>
                    <?php else: ?>
                        <?php echo esc_html__('Estimated maximum time', 'felan-framework'); ?>
                    <?php endif; ?>
                </span>
                                <span class="value"><?php echo esc_html(felan_project_maximum_time($project_id)); ?></span>
                            </li>
                            <?php if (!empty($project_career)) : ?>
                                <li class="list-item">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.231 15.75H11.769C14.5518 15.75 15.0503 14.6632 15.1956 13.3402L15.7148 7.9402C15.9017 6.2932 15.4172 4.94995 12.4613 4.94995H5.53875C2.58284 4.94995 2.09826 6.2932 2.28517 7.9402L2.80436 13.3402C2.94973 14.6632 3.44815 15.75 6.231 15.75Z"
                                  stroke="#333333" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path d="M6.23047 4.95V4.41C6.23047 3.21525 6.23047 2.25 8.44567 2.25H9.55327C11.7685 2.25 11.7685 3.21525 11.7685 4.41V4.95"
                                  stroke="#333333" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path d="M7.35946 9.99839C5.63341 9.80994 3.93944 9.22113 2.42578 8.25" stroke="black"
                                  stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path d="M10.6758 9.99839C12.4018 9.80994 14.0958 9.22113 15.6095 8.25" stroke="black"
                                  stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <circle cx="9" cy="10.125" r="1.5" stroke="black" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"/>
                        </svg>
                        <?php echo esc_html__('Career Level', 'felan-framework'); ?>
                    </span>
                                    <span class="value">
                        <?php foreach ($project_career as $career) { ?>
                            <span class="cate"><?php echo $career->name; ?></span>
                        <?php } ?>
                    </span>
                                </li>
                            <?php endif; ?>
                            <?php if (!empty($project_language)) : ?>
                                <li class="list-item">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 15.75C12.7279 15.75 15.75 12.7279 15.75 9C15.75 5.27208 12.7279 2.25 9 2.25M9 15.75C5.27208 15.75 2.25 12.7279 2.25 9C2.25 5.27208 5.27208 2.25 9 2.25M9 15.75C11.0711 15.75 11.956 11.8777 11.956 9C11.956 6.12228 11.0711 2.25 9 2.25M9 15.75C6.92893 15.75 6.04421 11.8777 6.04421 9C6.04421 6.1223 6.92893 2.25 9 2.25M2.62491 6.74998C7.60409 6.74998 10.3957 6.74998 15.3749 6.74998M2.62491 11.25C7.60409 11.25 10.3957 11.25 15.3749 11.25"
                                  stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php echo esc_html__('Language', 'felan-framework'); ?>
                    </span>
                                    <span class="value">
                    <?php foreach ($project_language as $language) { ?>
                        <span class="cate"><?php echo $language->name; ?></span>
                    <?php } ?>
                    </span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>