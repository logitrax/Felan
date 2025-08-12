<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$jobs_id = get_the_ID();
if (!empty($job_id)) {
    $jobs_id = $job_id;
}
$jobs_type = get_the_terms($jobs_id, 'jobs-type');
$jobs_categories =  get_the_terms($jobs_id, 'jobs-categories');
$jobs_location =  get_the_terms($jobs_id, 'felan_location');
$jobs_location_city = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_location_city', true);
$jobs_featured    = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_featured', true);
$jobs_select_company    = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_company');
$company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
$company_logo   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
$mycompany = get_post($company_id);
$social_sharing = felan_get_option('social_sharing');
$type_single_jobs = felan_get_option('single_job_layout', '01');
$content_jobs = felan_get_option('archive_jobs_layout', 'layout-list');
$content_jobs = !empty($_GET['layout']) ? felan_clean(wp_unslash($_GET['layout'])) : $content_jobs;
$content_jobs = !empty($_POST['layout']) ? felan_clean(wp_unslash($_POST['layout'])) : $content_jobs;
if (!empty($layout)) {
    $type_single_jobs = $layout;
}

if (!empty($_GET['layout'])) {
    if ($_GET['layout'] == 'layout-full') {
        $type_single_jobs = '03';
    } else {
        $type_single_jobs = felan_clean(wp_unslash($_GET['layout']));
    }
}
?>
<?php if ($type_single_jobs != '01') : ?>
    <div class="section-sticky">
        <div class="title-wapper">
            <?php
            if (!empty($company_logo[0]['url'])) :
                echo '<div class="avatar">';
                echo '<img class="logo-comnpany" src="' . $company_logo[0]['url'] . '" alt="" />';
                echo '</div>';
            endif;
            ?>
            <div class="info">
                <?php if (!empty(get_the_title($jobs_id))) : ?>
                    <h1>
                        <?php echo get_the_title($jobs_id); ?>
                        <?php if ($jobs_featured == '1') : ?>
                            <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="">
                            </span>
                        <?php endif; ?>
                    </h1>
                <?php endif; ?>
                <?php if (!empty($company_id)) : ?>
                    <div class="author-name">
                        <span><?php esc_html_e('by', 'felan-framework') ?></span>
                        <a class="authour" href="<?php echo get_post_permalink($company_id) ?>"><?php echo get_the_title($company_id); ?></a>
                        <span><?php esc_html_e('in', 'felan-framework') ?></span>
                        <?php if (is_array($jobs_categories)) { ?>
                            <?php foreach ($jobs_categories as $categories) {
                                $cate_link = get_term_link($categories, 'jobs-categories'); ?>
                                <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                    <?php echo $categories->name; ?>
                                </a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="info-apply">
            <p class="days">
                <span style="<?php if (intval(felan_get_expiration_apply($jobs_id)) <= 3) {
                                    echo 'color:red';
                                } else {
                                    echo 'color:green';
                                } ?>"> <?php echo felan_get_expiration_apply($jobs_id); ?> </span><?php esc_html_e('days left', 'felan-framework') ?>
            </p>
            <?php felan_get_status_apply($jobs_id); ?>
        </div>
    </div>
<?php endif; ?>
<?php
if ($type_single_jobs == '01' || $content_jobs == 'layout-full') {
    felan_get_template('jobs/single/thumbnail.php', array(
        'job_id' => $job_id,
    ));
}
?>
<div class="block-archive-inner jobs-head-details <?php echo 'layout-' . $type_single_jobs; ?>">
    <div class="felan-jobs-header-top <?php if ($type_single_jobs == '02') echo 'container'; ?>">
        <div class="felan-header-top">
            <div class="info">
                <div class="title-wapper">
                    <?php
                    if (!empty($company_logo[0]['url'])) :
                        echo '<div class="avatar">';
                        echo '<img class="logo-comnpany" src="' . $company_logo[0]['url'] . '" alt="" />';
                        echo '</div>';
                    endif;
                    ?>
                    <div class="info">
                        <?php if (!empty(get_the_title($jobs_id))) : ?>
                            <h1>
                                <?php echo get_the_title($jobs_id); ?>
                                <?php if ($jobs_featured == '1') : ?>
                                    <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                        <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="">
                                    </span>
                                <?php endif; ?>
                            </h1>
                        <?php endif; ?>
                        <?php if (!empty($company_id)) : ?>
                            <div class="author-name">
                                <span><?php esc_html_e('by', 'felan-framework') ?></span>
                                <a class="authour" href="<?php echo get_post_permalink($company_id) ?>"><?php echo get_the_title($company_id); ?></a>
                                <span><?php esc_html_e('in', 'felan-framework') ?></span>
                                <?php if (is_array($jobs_categories)) { ?>
                                    <?php foreach ($jobs_categories as $categories) {
                                        $cate_link = get_term_link($categories, 'jobs-categories'); ?>
                                        <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                            <?php echo $categories->name; ?>
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="social">
                <?php felan_get_template('jobs/wishlist.php', array(
                    'jobs_id' => $jobs_id,
                )); ?>
                <?php if (!empty($social_sharing)) : ?>
                    <div class="toggle-social">
                        <a href="#" class="jobs-share btn-share tooltip" data-title="<?php esc_attr_e('Share', 'felan-framework') ?>">
                            <i class="far fa-share-alt"></i>
                        </a>
                        <?php felan_get_template('global/social-share.php', array(
                            'post_id' => $jobs_id,
                        )); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="felan-header-bottom">
            <div class="author-wrapper">
                <div class="author-meta">
                    <?php if (is_array($jobs_location)) { ?>
                        <div class="location-wrapper">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <?php foreach ($jobs_location as $location) {
                                $location_link = get_term_link($location, 'felan_location');
                            ?>
                                <a class="cate-location" href="<?php echo esc_url($location_link); ?>">
                                    <?php esc_html_e($location->name); ?>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if (is_array($jobs_type)) { ?>
                        <div class="type-wrapper">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 6V12L16 14" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="12" r="9" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <?php foreach ($jobs_type as $type) {
                                $type_link = get_term_link($type, 'jobs-type'); ?>
                                <a class="cate-type" href="<?php echo esc_url($type_link); ?>"><?php echo trim($type->name); ?></a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="info-apply">
                <p class="days">
                    <span style="<?php if (intval(felan_get_expiration_apply($jobs_id)) <= 3) {
                                        echo 'color:red';
                                    } else {
                                        echo 'color:green';
                                    } ?>"> <?php echo felan_get_expiration_apply($jobs_id); ?> </span><?php esc_html_e('days left', 'felan-framework') ?>
                </p>
                <?php felan_get_status_apply($jobs_id); ?>
            </div>
        </div>
    </div>
</div>