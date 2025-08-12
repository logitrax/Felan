<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script( 'razorpay_checkout', 'https://checkout.razorpay.com/v1/checkout.js', null, null );

global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$package_id = isset($_GET['package_id']) ? felan_clean(wp_unslash($_GET['package_id'])) : '';
$user_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
$felan_profile = new Felan_Profile();
$check_package = $felan_profile->user_package_available($user_id);

$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$package_free = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_free', true);

if ($package_free == 1) {
    $package_price = 0;
} else {
    $package_price = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_price', true);
}
$package_time_unit = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_time_unit', true);
$package_period = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_period', true);
$package_unlimited_time = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_time', true);

$package_num_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_job', true);
$package_unlimited_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job', true);
$package_featured_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job_featured', true);
$package_num_featured_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_featured', true);

$package_num_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_project', true);
$package_unlimited_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project', true);
$package_featured_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project_featured', true);
$package_num_featured_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_project_featured', true);

$package_featured = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_featured', true);
$package_title = get_the_title($package_id);
$package_additional = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_additional_details', true);
if ($package_additional > 0) {
    $package_additional_text = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_details_text', true);
}

if ($package_period > 1) {
    $package_time_unit .= 's';
}
if ($package_featured == 1) {
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

$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
$enable_post_type_project = felan_get_option('enable_post_type_project','1');
$enable_paypal = felan_get_option('enable_paypal', 1);
$enable_stripe = felan_get_option('enable_stripe', 1);
$enable_woocheckout = felan_get_option('enable_woocheckout', 1);
$enable_wire_transfer = felan_get_option('enable_wire_transfer', 1);
$enable_razor = felan_get_option('enable_razor', 1);
$select_packages_link = felan_get_permalink('package');
$field_package = array('freelancer_follow', 'download_cv', 'invite', 'send_message', 'print', 'review_and_commnent', 'info');
?>

<div class="row">
    <div class="col-lg-8 col-md-7 col-sm-6">


        <?php if ($package_price > 0) : ?>
            <div class="felan-payment-method-wrap">
                <div class="entry-heading">
                    <h2 class="entry-title"><?php esc_html_e('Payment Method', 'felan-framework'); ?></h2>
                </div>
                <?php if ($enable_wire_transfer != 0) : ?>
                    <div class="radio wire-transfer active">
                        <label>
                            <input type="radio" name="felan_payment_method" value="wire_transfer" checked>
                            <i class="far fa-window-restore"></i><?php esc_html_e('Wire Transfer', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>

                <?php if ($enable_paypal != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-paypal" name="felan_payment_method" value="paypal">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/paypal.png'); ?>" alt="<?php esc_html_e('Paypal', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay With Paypal', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>

                <?php if ($enable_stripe != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-stripe" name="felan_payment_method" value="stripe">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/stripe.png'); ?>" alt="<?php esc_html_e('Stripe', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay with Credit Card', 'felan-framework'); ?>
                        </label>
                        <?php
                        $felan_payment = new Felan_Payment();
                        $felan_payment->stripe_payment_per_package($package_id);
                        ?>
                    </div>
                <?php endif; ?>

				<?php if ( $enable_razor != 0 ) : ?>
					<div class="radio">
						<label>
							<input type="radio" class="payment-razor" name="felan_payment_method" value="razor">
							<img src="https://cdn.razorpay.com/static/assets/logo/payment.svg" alt="<?php esc_html_e('Razor', 'felan-framework'); ?>">
							<?php esc_html_e('Pay with Razor', 'felan-framework'); ?>
						</label>
						<?php
						$felan_payment_razor = new Felan_Payment();
						$felan_payment_razor->felan_razor_payment_project_addons( $package_id );
						?>
					</div>
				<?php endif; ?>

                <?php if ($enable_woocheckout != 0) : ?>
                    <div class="radio">
                        <label>
                            <input type="radio" class="payment-woocheckout" name="felan_payment_method" value="woocheckout">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/woocommerce-logo.png'); ?>" alt="<?php esc_html_e('Woocommerce', 'felan-framework'); ?>">
                            <?php esc_html_e('Pay with Woocommerce', 'felan-framework'); ?>
                        </label>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>
        <input type="hidden" name="felan_package_id" value="<?php echo esc_attr($package_id); ?>">

        <p class="terms-conditions"><i class="fa fa-hand-o-right"></i> <?php echo sprintf(wp_kses(__('Please read <a target="_blank" href="%s"><strong>Terms & Conditions</strong></a> first', 'felan-framework'), $allowed_html), get_permalink($terms_condition)); ?>
        </p>
        <?php if ($package_price > 0) : ?>
            <?php if ($user_demo == 'yes') : ?>
                <button type="submit" class="btn btn-success btn-submit gl-button btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant change it', 'felan-framework'); ?>">
                    <?php esc_html_e('Pay Now', 'felan-framework'); ?>
                </button>
            <?php else : ?>
                <button id="felan_payment_package" type="submit" class="btn btn-success btn-submit gl-button"><?php esc_html_e('Pay Now', 'felan-framework'); ?></button>
            <?php endif; ?>
            <?php else :
            $user_free_package = get_the_author_meta(FELAN_METABOX_PREFIX . 'free_package', $user_id);
            if ($user_free_package == 'yes' && ($check_package == 1 || $check_package == 2)) : ?>
                <div class="felan-message alert alert-warning" role="alert"><?php esc_html_e('You have already used your first free package, please choose different package.', 'felan-framework'); ?></div>
            <?php else : ?>
                <button id="felan_free_package" type="submit" class="btn btn-success btn-submit felan-button"><?php esc_html_e('Get Free Listing Package', 'felan-framework'); ?></button>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="col-lg-4 col-md-5 col-sm-6">
        <div class="felan-payment-for felan-package-wrap panel panel-default">
            <div class="entry-heading">
                <h2 class="entry-title"><?php esc_html_e('Selected Package', 'felan-framework'); ?></h2>
            </div>
            <div class="felan-package-item panel panel-default <?php echo esc_attr($is_featured); ?>">
                <?php if (has_post_thumbnail($package_id)) : ?>
                    <div class="felan-package-thumbnail"><?php echo get_the_post_thumbnail($package_id); ?></div>
                <?php endif; ?>

                <div class="felan-package-title">
                    <h2 class="entry-title"><?php echo get_the_title($package_id); ?></h2>
                </div>

                <ul class="list-group custom-scrollbar">
                    <?php if (!empty($package_num_job) && $enable_post_type_jobs == '1') : ?>
                        <li class="list-group-item">
                            <i class="far fa-check"></i>
                            <span class="badge">
                                <?php if ($package_unlimited_job == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    esc_html_e($package_num_job);
                                } ?>
                            </span>
                            <?php esc_html_e('job postings', 'felan-framework'); ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($package_num_featured_job) && $enable_post_type_jobs == '1') : ?>
                        <li class="list-group-item">
                            <i class="far fa-check"></i>
                            <span class="badge">
                                <?php if ($package_featured_job == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    esc_html_e($package_num_featured_job);
                                } ?>
                            </span>
                            <?php esc_html_e('featured jobs', 'felan-framework') ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($package_num_project) && $enable_post_type_project == '1') : ?>
                        <li class="list-group-item">
                            <i class="far fa-check"></i>
                            <span class="badge">
                                <?php if ($package_unlimited_project == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    esc_html_e($package_num_project);
                                } ?>
                            </span>
                            <?php esc_html_e('project postings', 'felan-framework'); ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($package_num_featured_project) && $enable_post_type_project == '1') : ?>
                        <li class="list-group-item">
                            <i class="far fa-check"></i>
                            <span class="badge">
                                <?php if ($package_featured_project == 1) {
                                    esc_html_e('Unlimited', 'felan-framework');
                                } else {
                                    esc_html_e($package_num_featured_project);
                                } ?>
                            </span>
                            <?php esc_html_e('featured projects', 'felan-framework') ?>
                        </li>
                    <?php endif; ?>
                    <?php foreach ($field_package as $field) :
                        $show_option = felan_get_option('enable_company_package_' . $field);
                        $show_field = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'show_package_company_' . $field, true);
                        $field_unlimited = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'enable_package_' . $field . '_unlimited', true);
                        $field_number = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'company_package_number_' . $field, true);
                        $is_check = true;
                        switch ($field) {
                            case 'freelancer_follow':
                                $name = esc_html__('freelancers follow', 'felan-framework');
                                $is_check = false;
                                break;
                            case 'download_cv':
                                $name = esc_html__('Download CV', 'felan-framework');
                                $is_check = false;
                                break;
                            case 'invite':
                                $name = esc_html__('Invite Freelancers', 'felan-framework');
                                break;
                            case 'send_message':
                                $name = esc_html__('Send Messages', 'felan-framework');
                                break;
                            case 'print':
                                $name = esc_html__('Print freelancer profiles', 'felan-framework');
                                break;
                            case 'review_and_commnent':
                                $name = esc_html__('Review and comment', 'felan-framework');
                                break;
                            case 'info':
                                $name = esc_html__('View freelancer information', 'felan-framework');
                                break;
                        }
                        if ($show_field == 1 && $show_option == 1) :
                    ?>
                            <?php if (!empty($field_number)) : ?>
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
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if ($package_additional > 0) {
                        foreach ($package_additional_text as $value) { ?>
                            <li class="list-group-item">
                                <i class="far fa-check"></i>
                                <span class="badge">
                                    <?php esc_html_e($value); ?>
                                </span>
                            </li>
                    <?php }
                    } ?>
                    <li class="list-group-item">
                        <i class="far fa-check"></i>
                        <?php esc_html_e('Package live for', 'felan-framework'); ?>
                        <span class="badge">
                            <?php if ($package_unlimited_time == 1) {
                                esc_html_e('never expires', 'felan-framework');
                            } else {
                                esc_html_e($package_period . ' ' . Felan_Package::get_time_unit($package_time_unit));
                            }
                            ?>
                        </span>
                    </li>
                </ul>

                <div class="felan-total-price">
                    <span><?php esc_html_e('Total', 'felan-framework'); ?></span>
                    <span class="price">
                        <?php
                        if ($package_price > 0) {
                            echo felan_get_format_money($package_price, '', 2, true);
                        } else {
                            esc_html_e('Free', 'felan-framework');
                        }
                        ?>
                    </span>
                </div>

                <a class="felan-button" href="<?php echo esc_url($select_packages_link); ?>"><?php esc_html_e('Change Package', 'felan-framework'); ?></a>
            </div>
        </div>
    </div>
</div>