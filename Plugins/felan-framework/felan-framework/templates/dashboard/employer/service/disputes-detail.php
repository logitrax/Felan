<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'disputes-detail');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'disputes-detail',
    'felan_disputes_detail_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
    )
);

global $current_user;
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$order_id = isset($_GET['order_id']) ? felan_clean(wp_unslash($_GET['order_id'])) : '';
$disputes_id = isset($_GET['disputes_id']) ? felan_clean(wp_unslash($_GET['disputes_id'])) : '';
$service_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
$service_skills = get_the_terms($service_id, 'service-skills');
$service_categories = get_the_terms($service_id, 'service-categories');
$service_location = get_the_terms($service_id, 'felan_location');
$public_date = get_the_date(get_option('date_format'));
$thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
$author_id = get_post_field('post_author', $service_id);
$author_name = get_the_author_meta('display_name', $author_id);

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

$args_freelancer = array(
    'post_type' => 'freelancer',
    'posts_per_page' => 1,
    'author' => $author_id,
);
$current_user_posts = get_posts($args_freelancer);

$freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
$service_featured = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true);
$service_refund_content = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_refund_content', true);
$status = get_post_meta($disputes_id, FELAN_METABOX_PREFIX . 'disputes_status', true);

$price_default = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price_default', true);
$order_des = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_des', true);
$order_new = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_new', true);
$service_order_addons = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_addons', true);
$package_service = felan_get_option('package_service');

$currency_sign_default = felan_get_option('currency_sign_default');
$currency_position = felan_get_option('currency_position');
$price_order = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price', true);
$price_order_number = str_replace($currency_sign_default, '', $price_order);
$enable_freelancer_service_fee =  felan_get_option('enable_freelancer_service_fee');
$freelancer_number_service_fee =  felan_get_option('freelancer_number_service_fee');
$price_fee = round(intval($price_default) * intval($freelancer_number_service_fee) / 100);
if ($currency_position == 'before') {
    $price_fee = $currency_sign_default . felan_get_format_number($price_fee);
    $price_default = $currency_sign_default . felan_get_format_number($price_default);
} else {
    $price_fee = felan_get_format_number($price_fee) . $currency_sign_default;
    $price_default = felan_get_format_number($price_default) . $currency_sign_default;
}

$enable_freelancer_review = felan_get_option('enable_single_freelancer_review', '1');
$employer_avatar = get_the_author_meta('author_avatar_image_url', $user_id);
$employer_name = get_the_author_meta('display_name', $user_id);

//Message
$user_name = $current_user->display_name;
$sender_id = get_current_user_id();
$sender_messages = get_user_meta($sender_id, FELAN_PLUGIN_PREFIX . 'disputes_messages_employer_' . $disputes_id, true);
$receive_messages = get_user_meta($author_id, FELAN_PLUGIN_PREFIX . 'disputes_messages_freelancer_' . $disputes_id, true);
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
<div class="felan-service-order-detail">
    <div class="row">
        <div class="col-md-8">
            <div class="order-content">
                <div class="order-top">
                    <div class="status">
                        <span><?php esc_html_e('Status: ', 'felan-framework') ?></span>
                        <?php if ($status == 'close') : ?>
                            <span class="label label-close tooltip"><?php esc_html_e('Closed', 'felan-framework') ?></span>
                        <?php elseif ($status == 'refund') : ?>
                            <span class="label label-open tooltip"><?php esc_html_e('Refunded', 'felan-framework') ?></span>
                        <?php else : ?>
                            <span class="label label-inprogress tooltip"><?php esc_html_e('Open', 'felan-framework') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="info">
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
                            <p>
                                <span><?php echo esc_html__('by', 'felan-framework') ?></span>
                                <span class="author"><?php echo $author_name; ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="order-date">
                        <span class="order-id">
                            <?php echo esc_html__('Order ID: ', 'felan-framework'); ?>
                            <span class="value"><?php echo '#' . esc_html($order_id) ?></span>
                        </span>
                        <span class="order-date">
                            <?php echo esc_html__('Date: ', 'felan-framework'); ?>
                            <span class="value"><?php echo esc_html($public_date) ?></span>
                        </span>
                    </div>
                </div>
                <div class="order-center">
                    <div class="order-package order-center-item">
                        <h3><?php echo esc_html__('Service Package', 'felan-framework') ?></h3>
                        <?php if (!empty($price_default)) : ?>
                            <p class="price-default"><?php echo sprintf(esc_html__('Basic (%s)', 'felan-framework'), $price_default) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($order_des)) : ?>
                            <p class="order-des"><?php echo esc_html($order_des); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($number_delivery_time)) : ?>
                            <p class="delivery-time">
                                <span><?php echo esc_html__('Delivery Time:', 'felan-framework') ?></span>
                                <span class="time"><?php echo $number_delivery_time . ' ' . $service_time_type; ?></span>
                            </p>
                        <?php endif; ?>
                        <ul class="content">
                            <?php if (is_array($package_service) && !empty($package_service)) :
                                foreach ($package_service as $key => $package) :
                                    $service_package_list_key = FELAN_METABOX_PREFIX . 'service_package_list' . $key;
                                    $service_package_title_key = FELAN_METABOX_PREFIX . 'service_package_title' . $key;
                                    $new_title = get_post_meta($service_id, $service_package_title_key, true);
                                    $new_list = get_post_meta($service_id, $service_package_list_key, true);

                                    if (!empty($new_title) && !empty($new_list)) :
                                        if (in_array('basic', $new_list)) :
                            ?>
                                            <li>
                                                <span class="check">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span><?php echo esc_html($new_title); ?></span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                            <?php endforeach;
                            endif; ?>
                            <?php if (!empty($order_new)) :
                                $order_new_array = json_decode($order_new, true);
                                if (is_array($order_new_array)) :
                                    foreach ($order_new_array as $index => $package) :
                                        $new_title = $package[FELAN_METABOX_PREFIX . 'service_package_new_title'];
                                        $new_list_key = FELAN_METABOX_PREFIX . 'service_package_new_list';
                                        $new_list = isset($package[$new_list_key]) ? $package[$new_list_key] : [];
                                        if (!empty($new_title) && !empty($new_list)) :
                                            if (in_array('basic', $new_list)) :
                            ?>
                                                <li>
                                                    <span class="check">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M9.71278 3.64026C10.2941 3.14489 10.5847 2.8972 10.8886 2.75195C11.5915 2.41602 12.4085 2.41602 13.1114 2.75195C13.4153 2.8972 13.7059 3.14489 14.2872 3.64026C14.8856 4.15023 15.4938 4.40761 16.2939 4.47146C17.0552 4.53222 17.4359 4.56259 17.7535 4.67477C18.488 4.93421 19.0658 5.51198 19.3252 6.24652C19.4374 6.5641 19.4678 6.94476 19.5285 7.70608C19.5924 8.50621 19.8498 9.11436 20.3597 9.71278C20.8551 10.2941 21.1028 10.5847 21.248 10.8886C21.584 11.5915 21.584 12.4085 21.248 13.1114C21.1028 13.4153 20.8551 13.7059 20.3597 14.2872C19.8391 14.8981 19.5911 15.5102 19.5285 16.2939C19.4678 17.0552 19.4374 17.4359 19.3252 17.7535C19.0658 18.488 18.488 19.0658 17.7535 19.3252C17.4359 19.4374 17.0552 19.4678 16.2939 19.5285C15.4938 19.5924 14.8856 19.8498 14.2872 20.3597C13.7059 20.8551 13.4153 21.1028 13.1114 21.248C12.4085 21.584 11.5915 21.584 10.8886 21.248C10.5847 21.1028 10.2941 20.8551 9.71278 20.3597C9.10185 19.8391 8.48984 19.5911 7.70608 19.5285C6.94476 19.4678 6.5641 19.4374 6.24652 19.3252C5.51198 19.0658 4.93421 18.488 4.67477 17.7535C4.56259 17.4359 4.53222 17.0552 4.47146 16.2939C4.40761 15.4938 4.15023 14.8856 3.64026 14.2872C3.14489 13.7059 2.8972 13.4153 2.75195 13.1114C2.41602 12.4085 2.41602 11.5915 2.75195 10.8886C2.8972 10.5847 3.14489 10.2941 3.64026 9.71278C4.16089 9.10185 4.40892 8.48984 4.47146 7.70608C4.53222 6.94476 4.56259 6.5641 4.67477 6.24652C4.93421 5.51198 5.51198 4.93421 6.24652 4.67477C6.5641 4.56259 6.94476 4.53222 7.70608 4.47146C8.50621 4.40761 9.11436 4.15023 9.71278 3.64026Z" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                            <path d="M8.66797 12.6302L10.1738 14.3512C10.5972 14.835 11.3606 14.7994 11.7371 14.2781L15.3346 9.29688" stroke="#3AB446" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                    <span><?php echo esc_html($new_title); ?></span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                            <?php endforeach;
                                endif;
                            endif; ?>
                        </ul>
                        <input type="hidden" name="disputes_id" value="<?php echo esc_attr($disputes_id); ?>">
                    </div>
                    <?php if (!empty($service_order_addons)) : ?>
                        <div class="order-addons order-center-item">
                            <h3><?php echo esc_html__('Service Addons', 'felan-framework') ?></h3>
                            <div class="order-addons-inner">
                                <?php if (is_array($service_order_addons)) :
                                    foreach ($service_order_addons as $index => $package) :
                                        if ($currency_position == 'before') {
                                            $value = $currency_sign_default . felan_get_format_number($package['value']);
                                        } else {
                                            $value = felan_get_format_number($package['value']) . $currency_sign_default;
                                        }
                                ?>
                                        <div class="content-addons">
                                            <p class="title"><?php echo $package['title'] . ' (' . $value . ')'; ?></p>
                                            <p class="delivery-time"><?php echo sprintf(esc_html__('%1s %2s delivery', 'felan-framework'), $package['deliveryTime'], $service_time_type) ?></p>
                                        </div>
                                <?php endforeach;
                                endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($enable_freelancer_service_fee == '1' && (!empty($freelancer_number_service_fee) || $freelancer_number_service_fee !== 0)) { ?>
                        <div class="order-fee order-center-item">
                            <h3><?php echo esc_html__('Service Fee', 'felan-framework') ?></h3>
                            <p class="fee"><?php echo sprintf(esc_html__('Fee: (%s)', 'felan-framework'), $price_fee) ?></p>
                            <p><?php echo esc_html__('Fee set by admin', 'felan-framework') ?></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="order-bottom <?php if ($status == 'canceled') : ?>order-canceled<?php endif; ?>">
                    <p class="total-budget"><?php echo sprintf(esc_html__('Total budget: (%s)', 'felan-framework'), $price_order) ?></p>
                </div>
                <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
            </div>
        </div>
        <div class="col-md-4">
            <div class="order-sidebar custom-scrollbar">
                <h4><?php echo esc_html__("Customer's request", 'felan-framework') ?></h4>
                <div class="info-freelancer">
                    <?php if (!empty($employer_avatar)) : ?>
                        <img class="image-freelancers" src="<?php echo esc_attr($employer_avatar) ?>" alt="" />
                    <?php endif; ?>
                    <div class="info">
                        <h5><?php echo esc_html($employer_name); ?></h5>
                        <p><?php echo esc_html__('Employer', 'felan-framework') ?></p>
                    </div>
                </div>
                <div class="content-request">
                    <h5><?php echo esc_html(get_the_title($disputes_id)); ?></h5>
                    <p><?php echo esc_html(get_the_excerpt($disputes_id)); ?></p>
                </div>

                <?php
                if (!empty($all_messages)) { ?>
                    <div class="disputes-history">
                        <?php foreach ($all_messages as $message) {
                            $is_sender = ($message['from'] === 'sender');
                            $avatar = get_the_author_meta('author_avatar_image_url', $message['sender_id']);
                        ?>
                            <div class="history-item <?php echo esc_attr($is_sender ? 'sender' : 'receiver'); ?>">
                                <div class="info-history">
                                    <?php if (!empty($avatar)) : ?>
                                        <a href="<?php echo esc_url(get_permalink($message['sender_id'])); ?>">
                                            <img class="avatar" src="<?php echo esc_attr($avatar); ?>" alt="" />
                                        </a>
                                    <?php endif; ?>

                                    <div class="info">
                                        <h5><?php echo esc_html($is_sender ? $user_name : get_the_author_meta('display_name', $message['sender_id'])); ?></h5>
                                        <?php echo esc_html($is_sender ? __('Employer', 'felan-framework') : __('Freelancer', 'felan-framework')); ?>
                                    </div>
                                </div>

                                <div class="content">
                                    <?php if (!empty($message['message_content'])) : ?>
                                        <?php echo esc_html($message['message_content']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if ($status == 'open') : ?>
                    <form class="reply" id="felan-disputes-message">
                        <h5><?php echo esc_html__("Reply", 'felan-framework') ?></h5>
                        <textarea name="message" rows="4" id="message_content" placeholder="<?php esc_attr_e('Message', 'felan-framework'); ?>"></textarea>

                        <?php if ($user_demo == 'yes') { ?>
                            <a class="felan-button button-block btn-add-to-message"
                                data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#">
                                <?php esc_html_e('Send', 'felan-framework') ?>
                            </a>
                        <?php } else { ?>
                            <a href="#" class="felan-button button-block btn-send-message">
                                <?php echo esc_html('Send', 'felan-framework'); ?>
                                <span class="btn-loading"><i class="far fa-spinner fa-spin medium"></i></span>
                            </a>
                        <?php } ?>

                        <input type="hidden" name="recipient_id" id="recipient_id" value="<?php echo esc_attr($author_id); ?>">
                        <input type="hidden" name="disputes_id" id="disputes_id" value="<?php echo esc_attr($disputes_id); ?>">
                        <input type="hidden" name="user_role" id="user_role" value="employer">
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>