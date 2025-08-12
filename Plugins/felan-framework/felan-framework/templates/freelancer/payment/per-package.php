<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-payment');
wp_enqueue_script( 'razorpay_checkout', 'https://checkout.razorpay.com/v1/checkout.js', null, null );

global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
if($enable_post_type_jobs == '1'){
    $freelancer_package_id = isset($_GET['candidate_package_id']) ? absint(wp_unslash($_GET['candidate_package_id']))  : '';
} else {
    $freelancer_package_id = isset($_GET['freelancer_package_id']) ? absint(wp_unslash($_GET['freelancer_package_id']))  : '';
}
$user_freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
$enable_post_type_service = felan_get_option('enable_post_type_service','1');
$felan_freelancer = new Felan_freelancer_package();
$check_freelancer_package = $felan_freelancer->user_freelancer_package_available($user_id);
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
$enable_post_type_service = felan_get_option('enable_post_type_service','1');
$enable_post_type_project = felan_get_option('enable_post_type_project','1');

$freelancer_package_free = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_free', true);

if ($freelancer_package_free == 1) {
    $freelancer_package_price = 0;
} else {
    $freelancer_package_price = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_price', true);
}
$freelancer_package_time_unit = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_time_unit', true);
$freelancer_package_number_service = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service', true);
$freelancer_package_period = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_period', true);
$enable_package_service_unlimited = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited', true);
$enable_package_service_unlimited_time = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited_time', true);
$freelancer_package_featured_freelancer = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_featured_unlimited', true);
$freelancer_package_number_service_featured = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service_featured', true);
$freelancer_package_featured = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_featured', true);
$freelancer_package_title = get_the_title($freelancer_package_id);
$freelancer_package_additional = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_additional_details', true);
if ($freelancer_package_additional > 0) {
    $freelancer_package_additional_text = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_details_text', true);
}

if ($freelancer_package_period > 1) {
    $freelancer_package_time_unit .= 's';
}
if ($freelancer_package_featured == 1) {
    $is_featured = ' active';
} else {
    $is_featured = '';
}
$terms_condition = felan_get_option('terms_condition');
$allowed_html = array(
    'a' => array(
        'href' => array(),
        'title' => array(),
        'target' => array()
    ),
    'strong' => array()
);
$freelancer_enable_paypal = felan_get_option('freelancer_enable_paypal', 1);
$freelancer_enable_stripe = felan_get_option('freelancer_enable_stripe', 1);
$freelancer_enable_woocheckout = felan_get_option('freelancer_enable_woocheckout', 1);
$freelancer_enable_wire_transfer = felan_get_option('freelancer_enable_wire_transfer', 1);
$freelancer_enable_razor = felan_get_option('freelancer_enable_razor', 1);
$select_freelancer_packages_link = felan_get_permalink('freelancer_package');
$field_package = array('jobs_apply','project_apply', 'jobs_wishlist', 'company_follow', 'contact_company', 'info_company', 'send_message', 'review_and_commnent');
?>

<div class="row">
    <div class="col-lg-8 col-md-7 col-sm-6">
        <?php if ($freelancer_package_price > 0) : ?>
            <div class="felan-payment-method-wrap">
                <div class="entry-heading">
                    <h2 class="entry-title"><?php esc_html_e('Payment Method', 'felan-framework'); ?></h2>
                </div>
                <?php if ($freelancer_enable_wire_transfer != 0) : ?>
                    <div class="radio wire-transfer active">
                        <label>
                            <input type="radio" name="felan_freelancer_payment_method" value="wire_transfer" checked>
                            <i class="far fa-window-restore"></i><?php esc_html_e('Wire Transfer', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>

                <?php if ($freelancer_enable_paypal != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-paypal" name="felan_freelancer_payment_method" value="paypal">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/paypal.png'); ?>" alt="<?php esc_html_e('Paypal', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay With Paypal', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>

                <?php if ($freelancer_enable_stripe != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-stripe" name="felan_freelancer_payment_method" value="stripe">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/stripe.png'); ?>" alt="<?php esc_html_e('Stripe', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay with Credit Card', 'felan-framework'); ?>
                        </label>
                        <?php
                        $felan_payment = new Felan_Freelancer_Payment();
                        $felan_payment->freelancer_stripe_payment_per_package($freelancer_package_id);
                        ?>
                    </div>
                <?php endif; ?>

				<?php if ( $freelancer_enable_razor != 0 ) : ?>
					<div class="radio">
						<label>
							<input type="radio" class="payment-razor" name="felan_freelancer_payment_method" value="razor">
							<img src="https://cdn.razorpay.com/static/assets/logo/payment.svg" alt="<?php esc_html_e('Razor', 'felan-framework'); ?>">
							<?php esc_html_e('Pay with Razor', 'felan-framework'); ?>
						</label>
						<?php
						$felan_payment_razor = new Felan_Freelancer_Payment();
						$felan_payment_razor->felan_razor_payment_project_addons( $freelancer_package_id );
						?>
					</div>
				<?php endif; ?>

                <?php if ($freelancer_enable_woocheckout != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-woocheckout" name="felan_freelancer_payment_method" value="woocheckout">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/woocommerce-logo.png'); ?>" alt="<?php esc_html_e('Woocommerce', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay with Woocommerce', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>
        <input type="hidden" name="felan_freelancer_package_id" value="<?php echo esc_attr($freelancer_package_id); ?>">

        <p class="terms-conditions"><i class="fa fa-hand-o-right"></i> <?php echo sprintf(wp_kses(__('Please read <a target="_blank" href="%s"><strong>Terms & Conditions</strong></a> first', 'felan-framework'), $allowed_html), get_permalink($terms_condition)); ?>
        </p>
        <?php if ($freelancer_package_price > 0) : ?>
            <button id="felan_payment_freelancer_package" type="submit" class="btn btn-success btn-submit gl-button"><?php esc_html_e('Pay Now', 'felan-framework'); ?></button>
            <?php else :
            $user_free_freelancer_package = get_the_author_meta(FELAN_METABOX_PREFIX . 'free_freelancer_package', $user_id);
            if ($user_free_freelancer_package == 'yes' && $check_freelancer_package == 1) : ?>
                <div class="felan-message alert alert-warning" role="alert"><?php esc_html_e('You have already used your first free package, please choose different package.', 'felan-framework'); ?></div>
            <?php else : ?>
                <button id="felan_free_freelancer_package" type="submit" class="btn btn-success btn-submit felan-button"><?php esc_html_e('Get Free Listing Package', 'felan-framework'); ?></button>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="col-lg-4 col-md-5 col-sm-6">
        <div class="felan-payment-for felan-package-wrap panel panel-default">
            <div class="entry-heading">
                <h2 class="entry-title"><?php esc_html_e('Selected Package', 'felan-framework'); ?></h2>
            </div>
            <div class="felan-package-item panel panel-default <?php echo esc_attr($is_featured); ?>">
                <?php if (has_post_thumbnail($freelancer_package_id)) : ?>
                    <div class="felan-package-thumbnail"><?php echo get_the_post_thumbnail($freelancer_package_id); ?></div>
                <?php endif; ?>

                <div class="felan-package-title">
                    <h2 class="entry-title"><?php echo get_the_title($freelancer_package_id); ?></h2>
                </div>

                <ul class="list-group custom-scrollbar">
                    <li class="list-group-item">
                        <i class="far fa-check"></i>
                        <?php esc_html_e('Package live for', 'felan-framework'); ?>
                        <span class="badge">
                            <?php if ($enable_package_service_unlimited_time == 1) {
                                esc_html_e('never expires', 'felan-framework');
                            } else {
                                esc_html_e($freelancer_package_period . ' ' . Felan_Package::get_time_unit($freelancer_package_time_unit));
                            }
                            ?>
                        </span>
                    </li>
                    <?php if (!empty($freelancer_package_number_service) && $enable_post_type_service == '1') : ?>
                        <li class="list-group-item">
                            <i class="far fa-check"></i>
                            <span class="badge">
                                <?php if ($enable_package_service_unlimited == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    esc_html_e($freelancer_package_number_service);
                                } ?>
                            </span>
                            <?php esc_html_e('service postings', 'felan-framework'); ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($freelancer_package_number_service_featured) && $enable_post_type_service == '1') : ?>
                        <li class="list-group-item">
                            <i class="far fa-check"></i>
                            <span class="badge">
                                <?php if ($freelancer_package_featured_freelancer == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    esc_html_e($freelancer_package_number_service_featured);
                                } ?>
                            </span>
                            <?php esc_html_e('service postings', 'felan-framework') ?>
                        </li>
                    <?php endif; ?>
                    <?php foreach ($field_package as $field) :
                        $show_field = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'show_package_' . $field, true);
                        $field_number = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_' . $field, true);
                        $field_unlimited = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_' . $field . '_unlimited', true);
                        $is_check = false;

                        if ($field == 'jobs_apply' && $enable_post_type_jobs != '1') {
                            continue;
                        }

                        if ($field == 'jobs_wishlist' && $enable_post_type_jobs != '1') {
                            continue;
                        }

                        if ($field == 'project_apply' && $enable_post_type_project != '1') {
                            continue;
                        }

                        switch ($field) {
                            case 'jobs_apply':
                                $name = esc_html__('job applications', 'felan-framework');
                                break;
                            case 'jobs_wishlist':
                                $name = esc_html__('jobs in wishlist', 'felan-framework');
                                break;
                            case 'company_follow':
                                $name = esc_html__(' companies followed', 'felan-framework');
                                break;
                            case 'contact_company':
                                $name = esc_html__('View company in jobs', 'felan-framework');
                                $is_check = true;
                                break;
                            case 'info_company':
                                $name = esc_html__('View company contact information', 'felan-framework');
                                $is_check = true;
                                break;
                            case 'send_message':
                                $name = esc_html__('Send messages', 'felan-framework');
                                $is_check = true;
                                break;
                            case 'review_and_commnent':
                                $name = esc_html__('Review and commnet', 'felan-framework');
                                $is_check = true;
                                break;
                            case 'project_apply':
                                $name = esc_html__('proposals submitted', 'felan-framework');
                                break;
                        }
                        if (intval($show_field) == 1 && !empty($field_number)) : ?>
                            <li class="list-group-item">
                                <i class="far fa-check"></i>
                                <?php if ($is_check == true) { ?>
                                    <span class="badge">
                                        <?php esc_html_e($name); ?>
                                    </span>
                                <?php } else { ?>
                                    <span class="badge">
                                        <?php if ($field_unlimited == 1) { ?>
                                            <?php esc_html_e('Unlimited', 'felan-framework'); ?>
                                        <?php } else { ?>
                                            <?php echo $field_number; ?>
                                        <?php } ?>
                                    </span>
                                    <?php echo $name; ?>
                                <?php } ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($freelancer_package_additional > 0) {
                        foreach ($freelancer_package_additional_text as $value) { ?>
                            <?php if (!empty($value)) : ?>
                                <li class="list-group-item">
                                    <i class="far fa-check"></i>
                                    <span class="badge">
                                        <?php esc_html_e($value); ?>
                                    </span>
                                </li>
                            <?php endif; ?>
                    <?php }
                    } ?>
                </ul>
                <div class="felan-total-price">
                    <span><?php esc_html_e('Total', 'felan-framework'); ?></span>
                    <span class="price">
                        <?php
                        if ($freelancer_package_price > 0) {
                            echo felan_get_format_money($freelancer_package_price, '', 2, true);
                        } else {
                            esc_html_e('Free', 'felan-framework');
                        }
                        ?>
                    </span>
                </div>
                <a class="felan-button" href="<?php echo esc_url($select_freelancer_packages_link); ?>"><?php esc_html_e('Change Package', 'felan-framework'); ?></a>
            </div>
        </div>
    </div>
</div>