<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'jobs-dashboard');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'jobs-dashboard',
    'felan_jobs_dashboard_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_jobs' => esc_html__('No jobs found', 'felan-framework'),
    )
);

global $current_user;
$user_id = $current_user->ID;
$current_date = date('Y-m-d');
$felan_package = new Felan_Package();
$package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
$expired_date = $felan_package->get_expired_date($package_id, $user_id);
$paid_submission_type = felan_get_option('paid_submission_type', 'no');
$extend_expired_jobs = felan_get_option('enable_extend_expired_jobs');
$posts_per_page = 10;

$jobs_classes = array('felan-jobs', 'grid', 'columns-4');
$tax_query = $meta_query = array();
global $current_user;
wp_get_current_user();
$user_id = $current_user->ID;
$felan_profile = new Felan_Profile();

$args_ex = array(
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => -1,
    'author' => $user_id,
);
$data_ex = new WP_Query($args_ex);
$id_ex = array();
if ($data_ex->have_posts()) {
    while ($data_ex->have_posts()) : $data_ex->the_post();
        $id_ex[] = get_the_ID();
    endwhile;
}

if (!empty($id_ex) && $paid_submission_type == 'per_package') {
    if ($current_date >= $expired_date) {
        foreach ($id_ex as $value) {
            update_post_meta($value, FELAN_METABOX_PREFIX . 'enable_jobs_package_expires', 1);
        }
    } else {
        foreach ($id_ex as $value) {
            update_post_meta($value, FELAN_METABOX_PREFIX . 'enable_jobs_package_expires', 0);
        }
    }
}

$args = array(
    'post_type' => 'jobs',
    'post_status' => array('publish', 'expired', 'pending', 'pause'),
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'author' => $user_id,
    'orderby' => 'date',
);

$data = new WP_Query($args); ?>
<?php if ($current_date >= $expired_date && $paid_submission_type == 'per_package') : ?>
    <p class="notice"><i class="far fa-exclamation-circle"></i>
        <?php esc_html_e("Package expired. Please select a new one.", 'felan-framework'); ?>
        <a href="<?php echo felan_get_permalink('package'); ?>">
            <?php esc_html_e('Add Package', 'felan-framework'); ?>
        </a>
    </p>
<?php endif; ?>

<div class="search-dashboard-warpper">
    <div class="search-left">
        <div class="select2-field">
            <select class="search-control felan-select2" name="jobs_status">
                <option value=""><?php esc_html_e('All Status', 'felan-framework') ?></option>
                <option value="publish"><?php esc_html_e('Opening', 'felan-framework') ?></option>
                <option value="pause"><?php esc_html_e('Paused', 'felan-framework') ?></option>
                <option value="expired"><?php esc_html_e('Closed', 'felan-framework') ?></option>
                <option value="pending"><?php esc_html_e('Pending', 'felan-framework') ?></option>
                <option value="featured"><?php esc_html_e('Featured', 'felan-framework') ?></option>
            </select>
        </div>
        <div class="action-search">
            <input class="jobs-search-control" type="text" name="jobs_search"
                placeholder="<?php esc_attr_e('Search jobs title', 'felan-framework') ?>">
            <button class="btn-search">
                <i class="far fa-search"></i>
            </button>
        </div>
    </div>
    <div class="search-right">
        <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
        <div class="select2-field">
            <select class="search-control action-sorting felan-select2" name="jobs_sort_by">
                <option value="newest"><?php esc_html_e('Newest', 'felan-framework') ?></option>
                <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework') ?></option>
            </select>
        </div>
    </div>
</div>
<?php if ($data->have_posts()) { ?>
    <div class="table-dashboard-wapper">
        <table class="table-dashboard" id="my-jobs">
            <thead>
                <tr>
                    <th><?php esc_html_e('TITLE', 'felan-framework') ?></th>
                    <th><?php esc_html_e('APPLICANTS', 'felan-framework') ?></th>
                    <th><?php esc_html_e('STATUS', 'felan-framework') ?></th>
                    <th><?php esc_html_e('POSTED', 'felan-framework') ?></th>
                    <th><?php esc_html_e('EXPIRED', 'felan-framework') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $ids = $jobs_expires = array(); ?>
                <?php while ($data->have_posts()) : $data->the_post(); ?>
                    <?php
                    $id = get_the_ID();
                    $ids[] = $id;
                    global $current_user;
                    wp_get_current_user();
                    $user_id = $current_user->ID;
                    $status = get_post_status($id);
                    $jobs_categories = get_the_terms($id, 'jobs-categories');
                    $jobs_location = get_the_terms($id, 'felan_location');
                    $public_date = get_the_date('Y-m-d');
                    $current_date = date('Y-m-d');
                    $jobs_days_single = get_post_meta($id, FELAN_METABOX_PREFIX . 'jobs_days_closing', true);
                    $enable_jobs_expires = get_post_meta($id, FELAN_METABOX_PREFIX . 'enable_jobs_expires', true);
                    if ($enable_jobs_expires == '1') {
                        $jobs_days_closing = '0';
                    } else {
                        if ($jobs_days_single) {
                            $jobs_days_closing = $jobs_days_single;
                        } else {
                            $jobs_days_closing = felan_get_option('jobs_number_days', true);
                        }
                    }
                    $expiration_date = date('Y-m-d', strtotime($public_date . '+' . $jobs_days_closing . ' days'));
                    $jobs_featured = get_post_meta($id, FELAN_METABOX_PREFIX . 'jobs_featured', true);

                    $val_expiration_date = date(get_option('date_format'), strtotime($public_date . '+' . $jobs_days_closing . ' days'));
                    $val_public_date = get_the_date(get_option('date_format'));
                    $company_id = get_post_meta($id, FELAN_METABOX_PREFIX . 'jobs_select_company', true);
                    $company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo', true);

                    $withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
                    $wallet_fee_amount = felan_get_option('wallet_featured_jobs_fee_amount', '');
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
                                        <li><a class="btn-extend"
                                                jobs-id="<?php echo esc_attr($id); ?>"><?php esc_html_e('Extend', 'felan-framework'); ?></a>
                                        </li>
                                    </ul>
                                <?php else : ?>
                                    <a href="#" class="icon-setting btn-add-to-message"
                                        data-text="<?php echo esc_attr('Jobs has expired so you can not change it', 'felan-framework'); ?>"><i
                                            class="far fa-ellipsis-h"></i></a>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
    </div>
<?php } else { ?>
    <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
<?php } ?>
<?php $max_num_pages = $data->max_num_pages;
$total_post = $data->found_posts;
if ($total_post > $posts_per_page) { ?>
    <div class="pagination-dashboard">
        <?php felan_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
        wp_reset_postdata(); ?>
    </div>
<?php } ?>