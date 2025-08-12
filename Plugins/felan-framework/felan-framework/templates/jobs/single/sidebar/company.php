<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$check_company_package = felan_get_field_check_freelancer_package('contact_company');
$jobs_id = get_the_ID();
if (!empty($job_id)) {
    $jobs_id = $job_id;
};
$jobs_select_company    = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_company');
$enable_social_twitter = felan_get_option('enable_social_twitter', '1');
$enable_social_linkedin = felan_get_option('enable_social_linkedin', '1');
$enable_social_facebook = felan_get_option('enable_social_facebook', '1');
$enable_social_instagram = felan_get_option('enable_social_instagram', '1');
$company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
if ($company_id !== '') {
    $company_logo   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
    $company_founded =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_founded');
    $company_phone =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_phone');
    $company_email =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_email');
    $company_website =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_website');
    $company_twitter   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_twitter');
    $company_facebook   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_facebook');
    $company_instagram   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_instagram');
    $company_linkedin   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_linkedin');
    $company_categories =  get_the_terms($company_id, 'company-categories');
    $company_size =  get_the_terms($company_id,  'company-size');
    $meta_query = felan_posts_company($company_id);
    $meta_query_post = felan_posts_company($company_id, 5);
    $company_location =  get_the_terms($company_id, 'felan_location');
}

$classes = array();
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
};

$hide_contact_company_fields = felan_get_option('hide_freelancer_contact_company_fields', array());
if (!is_array($hide_contact_company_fields)) {
    $hide_contact_company_fields = array();
}
if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
    $notice =  esc_attr__("Please renew the package to view", "felan-framework");
} else {
    $notice =  esc_attr__("Please access the role Freelancer and purchase the package to view", "felan-framework");
}
?>
<?php if ($check_company_package == -1 || $check_company_package == 0) { ?>
    <?php if (!empty($meta_query->post_count)) : ?>
        <div class="jobs-company-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?>">
            <div class="jobs-company-inner">
                <div class="company-header">
                    <?php if (!empty($company_logo[0]['url'])) : ?>
                        <img src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                    <?php endif; ?>
                    <?php if (get_the_title($company_id)) : ?>
                        <div class="name">
                            <h2> <a href="<?php echo get_post_permalink($company_id) ?>"><?php echo get_the_title($company_id); ?></a></h2>
                            <?php felan_company_green_tick($company_id); ?>
                            <div><a href="<?php echo get_post_permalink($company_id) ?>">
                                    <?php echo felan_get_total_rating('company', $company_id); ?>
                                </a></div>
                        </div>
                    <?php endif; ?>
                </div>
                <ul class="tab-company">
                    <li class="tab-item"><a href="#tab-sidebar-overview"><?php esc_html_e('Overview', 'felan-framework'); ?></a></li>
                    <li class="tab-item">
                        <a href="#tab-sidebar-jobs"><?php esc_html_e('Jobs', 'felan-framework'); ?>
                            <span><?php echo $meta_query->post_count ?></span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-info-company" id="tab-sidebar-overview">
                        <?php if (is_array($company_categories)) : ?>
                            <div class="info">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.47222 5.5H14.7697C15.2914 5.5 15.7924 5.70382 16.1659 6.068L21.5156 11.284C21.9182 11.6765 21.9182 12.3235 21.5156 12.716L16.1659 17.932C15.7924 18.2962 15.2914 18.5 14.7697 18.5H4.47222C3.52253 18.5 2.25 17.9588 2.25 16.875V7.125C2.25 6.04117 3.52253 5.5 4.47222 5.5Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="list-cate">
                                    <?php if (!in_array("categories", $hide_contact_company_fields)) : ?>
                                        <?php foreach ($company_categories as $categories) {
                                            $cate_link = get_term_link($categories, 'company-categories'); ?>
                                            <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                                <?php echo $categories->name; ?>
                                            </a>
                                        <?php } ?>
                                    <?php else : ?>
                                        *************
                                        <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (is_array($company_location)) : ?>
                            <div class="info">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="details-info">
                                    <?php if (!in_array("location", $hide_contact_company_fields)) : ?>
                                        <?php foreach ($company_location as $location) {
                                            $cate_link = get_term_link($location, 'felan_location'); ?>
                                            <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                                <?php echo $location->name; ?>
                                            </a>
                                        <?php } ?>
                                    <?php else : ?>
                                        *************
                                        <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($company_phone[0])) : ?>
                            <div class="info">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.6 14.5215C13.205 17.0421 7.09582 10.9878 9.49995 8.45753C10.9678 6.91263 9.30963 5.14707 8.3918 3.84934C6.66924 1.41378 2.88771 4.77641 3.00256 6.91544C3.36473 13.6609 10.6615 21.6546 17.7275 20.9574C19.9381 20.7393 22.4778 16.7471 19.9423 15.2882C18.6745 14.5587 16.9342 13.1172 15.6 14.5215Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="details-info company-phone">
                                    <?php if (!in_array("phone", $hide_contact_company_fields)) : ?>
                                        <a href="tel:<?php echo $company_phone[0]; ?>" data-phone="<?php echo $company_phone[0]; ?>"><?php echo substr($company_phone[0], 0, strlen($company_phone[0]) - 4); ?>****</a><i class="fal fa-eye"></i>
                                    <?php else : ?>
                                        ***********
                                        <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($company_email[0])) : ?>
                            <div class="info">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 12C2 8.22876 2 6.34315 3.46447 5.17157C4.92893 4 7.28595 4 12 4C16.714 4 19.0711 4 20.5355 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.5355 18.8284C19.0711 20 16.714 20 12 20C7.28595 20 4.92893 20 3.46447 18.8284C2 17.6569 2 15.7712 2 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M20.6667 5.31018L15.8412 9.79909C14.0045 11.3296 13.0862 12.0949 12.0001 12.0949C10.9139 12.0949 9.99561 11.3296 8.15897 9.7991L3.3335 5.31018" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="details-info email">
                                    <?php if (!in_array("email", $hide_contact_company_fields)) : ?>
                                        <a href="mailto:<?php echo $company_email[0]; ?>"><?php echo $company_email[0]; ?></a>
                                    <?php else : ?>
                                        *************
                                        <a class="btn-add-to-message" href="#" data-text="<?php echo $notice; ?>">
                                            <i class="far fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <div class="button-warpper">
                            <a href="<?php echo get_post_permalink($company_id); ?>" class="felan-button button-block view-profile">
                                <?php echo esc_html__('View Profile ', 'felan-framework'); ?><i class="fas fa-external-link"></i>
                            </a>
                            <?php felan_get_template('company/messages.php', array(
                                'company_id' => $company_id,
                            )); ?>
                        </div>
                    </div>
                    <div class="tab-info-company" id="tab-sidebar-jobs">
                        <ul class="list-jobs">
                            <?php foreach ($meta_query_post->posts as $post) {
                                $id_job = $post->ID;
                            ?>
                                <li class="list-items">
                                    <h6 class="title"><a href="<?php echo get_post_permalink($id_job) ?>"><?php echo get_the_title($id_job); ?></a></h6>
                                    <div class="info-company">
                                        <?php $jobs_categories = get_the_terms($post->ID, 'jobs-categories'); ?>
                                        <?php if (is_array($jobs_categories)) { ?>
                                            <div class="categories-warpper">
                                                <?php foreach ($jobs_categories as $categories) {
                                                    $cate_link = get_term_link($categories, 'jobs-categories'); ?>
                                                    <div class="cate-warpper">
                                                        <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                                            <?php echo $categories->name; ?>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php }; ?>
                        </ul>
                        <a href="<?php echo esc_url(get_post_type_archive_link('jobs')) . '/?company_id=' . $company_id ?>" class="felan-button button-outline button-block">
                            <?php esc_html_e('View all jobs', 'felan-framework'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php } ?>
<?php if ($company_id !== '' && ($check_company_package == 1 || $check_company_package == 2)) : ?>
    <div class="jobs-company-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?>">
        <div class="jobs-company-inner">
            <div class="company-header">
                <?php if (!empty($company_logo[0]['url'])) : ?>
                    <img src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                <?php endif; ?>
                <?php if (get_the_title($company_id)) : ?>
                    <div class="name">
                        <h2> <a href="<?php echo get_post_permalink($company_id) ?>"><?php echo get_the_title($company_id); ?></a></h2>
                        <?php felan_company_green_tick($company_id); ?>
                        <div><a href="<?php echo get_post_permalink($company_id) ?>">
                                <?php echo felan_get_total_rating('company', $company_id); ?>
                            </a></div>
                    </div>
                <?php endif; ?>
            </div>
            <ul class="tab-company">
                <li class="tab-item"><a href="#tab-sidebar-overview"><?php esc_html_e('Overview', 'felan-framework'); ?></a></li>
                <li class="tab-item">
                    <a href="#tab-sidebar-jobs"><?php esc_html_e('Jobs', 'felan-framework'); ?>
                        <span><?php echo $meta_query->post_count ?></span>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-info-company" id="tab-sidebar-overview">
                    <?php if (is_array($company_categories)) : ?>
                        <div class="info">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.47222 5.5H14.7697C15.2914 5.5 15.7924 5.70382 16.1659 6.068L21.5156 11.284C21.9182 11.6765 21.9182 12.3235 21.5156 12.716L16.1659 17.932C15.7924 18.2962 15.2914 18.5 14.7697 18.5H4.47222C3.52253 18.5 2.25 17.9588 2.25 16.875V7.125C2.25 6.04117 3.52253 5.5 4.47222 5.5Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="list-cate">
                                <?php foreach ($company_categories as $categories) {
                                    $cate_link = get_term_link($categories, 'company-categories'); ?>
                                    <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                        <?php echo $categories->name; ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (is_array($company_location)) : ?>
                        <div class="info">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="details-info">
                                <?php foreach ($company_location as $location) {
                                    $cate_link = get_term_link($location, 'felan_location'); ?>
                                    <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                        <?php echo $location->name; ?>
                                    </a>
                                <?php } ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($company_phone[0])) : ?>
                        <div class="info">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.6 14.5215C13.205 17.0421 7.09582 10.9878 9.49995 8.45753C10.9678 6.91263 9.30963 5.14707 8.3918 3.84934C6.66924 1.41378 2.88771 4.77641 3.00256 6.91544C3.36473 13.6609 10.6615 21.6546 17.7275 20.9574C19.9381 20.7393 22.4778 16.7471 19.9423 15.2882C18.6745 14.5587 16.9342 13.1172 15.6 14.5215Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="details-info company-phone"><a href="tel:<?php echo $company_phone[0]; ?>" data-phone="<?php echo $company_phone[0]; ?>"><?php echo substr($company_phone[0], 0, strlen($company_phone[0]) - 4); ?>****</a><i class="fal fa-eye"></i></p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($company_email[0])) : ?>
                        <div class="info">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 12C2 8.22876 2 6.34315 3.46447 5.17157C4.92893 4 7.28595 4 12 4C16.714 4 19.0711 4 20.5355 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.5355 18.8284C19.0711 20 16.714 20 12 20C7.28595 20 4.92893 20 3.46447 18.8284C2 17.6569 2 15.7712 2 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M20.6667 5.31018L15.8412 9.79909C14.0045 11.3296 13.0862 12.0949 12.0001 12.0949C10.9139 12.0949 9.99561 11.3296 8.15897 9.7991L3.3335 5.31018" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="details-info email"><a href="mailto:<?php echo $company_email[0]; ?>"><?php echo $company_email[0]; ?></a></p>
                        </div>
                    <?php endif; ?>
                    <div class="button-warpper">
                        <a href="<?php echo get_post_permalink($company_id); ?>" class="felan-button button-block view-profile">
                            <?php echo esc_html__('View Profile ', 'felan-framework'); ?><i class="fas fa-external-link"></i>
                        </a>
                        <?php felan_get_template('company/messages.php', array(
                            'company_id' => $company_id,
                        )); ?>
                    </div>
                </div>
                <div class="tab-info-company" id="tab-sidebar-jobs">
                    <ul class="list-jobs">
                        <?php foreach ($meta_query_post->posts as $post) {
                            $id_job = $post->ID;
                        ?>
                            <li class="list-items">
                                <h6 class="title"><a href="<?php echo get_post_permalink($id_job) ?>"><?php echo get_the_title($id_job); ?></a></h6>
                                <div class="info-company">
                                    <?php $jobs_categories = get_the_terms($post->ID, 'jobs-categories'); ?>
                                    <?php if (is_array($jobs_categories)) { ?>
                                        <div class="categories-warpper">
                                            <?php foreach ($jobs_categories as $categories) {
                                                $cate_link = get_term_link($categories, 'jobs-categories'); ?>
                                                <div class="cate-warpper">
                                                    <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                                        <?php echo $categories->name; ?>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php }; ?>
                    </ul>
                    <a href="<?php echo esc_url(get_post_type_archive_link('jobs')) . '/?company_id=' . $company_id ?>" class="felan-button button-outline button-block">
                        <?php esc_html_e('View all jobs', 'felan-framework'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>