<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb;

$id = get_the_ID();
if (!empty($freelancer_id)) {
    $id = $freelancer_id;
}

$author_id = get_post_field('post_author', $freelancer_id);
$freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$freelancer_featured = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
$freelancer_location = get_the_terms($freelancer_id, 'felan_location');
$freelancer_languages = get_the_terms($freelancer_id, 'freelancer_languages');
$freelancer_skills = get_the_terms($freelancer_id, 'freelancer_skills');
$freelancer_current_position = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);

$freelancer_item_class[] = 'felan-freelancers-item';

if (!empty($layout)) {
    $freelancer_item_class[] = $layout;
}
$freelancer_item_class[] = 'freelancer-' . $id;
if ($freelancer_featured == 1) {
    $freelancer_item_class[] = 'felan-freelancers-featured';
}
$enable_freelancer_review = felan_get_option('enable_single_freelancer_review', '1');
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
$enable_post_type_project = felan_get_option('enable_post_type_project', '1');

$verify_status = get_the_author_meta( 'user_verify', $author_id );
$show_badge    = felan_get_option( 'show_freelancer_verification', '1' );
?>
<div class="<?php echo join(' ', $freelancer_item_class); ?>">
    <div class="freelancer-top">
        <a class="company-img" href="<?php echo get_the_permalink($freelancer_id); ?>">
            <?php if (!empty($freelancer_avatar)) : ?>
                <img class="freelancer-avatar" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
            <?php else : ?>
                <div class="freelancer-avatar"><i class="far fa-camera"></i></div>
            <?php endif; ?>
        </a>
        <?php if (!empty(get_the_title($freelancer_id))) : ?>
            <h2 class="freelancers-title">
                <a href="<?php echo get_the_permalink($freelancer_id); ?>"><?php echo get_the_title($freelancer_id); ?></a>
            </h2>
			<?php if ( $verify_status == 1 && $show_badge == 1 ) : ?>
				<img style="width: 20px" class="image-freelancers-verify" src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/check-badge-verified.png'); ?>" alt="" />
			<?php endif; ?>
        <?php endif; ?>
        <?php if ($freelancer_current_position) : ?>
            <span class="current-position">
                <?php echo esc_html($freelancer_current_position); ?>
            </span>
        <?php endif; ?>
        <div class="freelancer-status-inner">
            <?php if ($freelancer_featured == 1) : ?>
                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.2703 16.2024C18.0927 19.0199 15.3079 21 12.0599 21C7.74661 21 4.25 17.5078 4.25 13.2C4.25 8.89218 5.89054 7.13076 8.45533 3C13.2614 5.09993 13.2614 11.4 13.2614 11.4C13.2614 11.4 14.8399 8.36201 18.0675 6.9C19.1024 9.94186 20.4978 13.2652 19.2703 16.2024Z" fill="#FFC402" stroke="#FFC402" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 18C9.23858 18 7 15.7614 7 13" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            <?php endif; ?>
            <?php felan_get_template('freelancer/follow.php', array(
                'freelancer_id' => $freelancer_id,
            )); ?>
        </div>
    </div>
    <div class="freelancer-center">
        <div class="center-inner">
            <div class="salary-freelancer">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="9" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M15.3333 7.72222H12M12 7.72222H10.3333C9.04467 7.72222 8 8.67984 8 9.86111C8 11.0424 9.04467 12 10.3333 12H12M12 7.72222V6.5M12 7.72222V12M12 12H13.6667C14.9553 12 16 12.9576 16 14.1389C16 15.3202 14.9553 16.2778 13.6667 16.2778H12M12 12V16.2778M12 16.2778H8M12 16.2778V17.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <?php felan_get_salary_freelancer($freelancer_id); ?>
            </div>
            <?php if (is_array($freelancer_location)) { ?>
                <div class="freelancer-location">
                    <?php foreach ($freelancer_location as $location) {
                        $location_link = get_term_link($location, 'freelancer-size'); ?>
                        <a href="<?php echo esc_url($location_link); ?>" class="cate">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <?php esc_html_e($location->name); ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if (is_array($freelancer_languages)) { ?>
                <div class="freelancer-language">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3M12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3M12 21C14.7614 21 15.9413 15.837 15.9413 12C15.9413 8.16303 14.7614 3 12 3M12 21C9.23858 21 8.05895 15.8369 8.05895 12C8.05895 8.16307 9.23858 3 12 3M3.49988 8.99998C10.1388 8.99998 13.861 8.99998 20.4999 8.99998M3.49988 15C10.1388 15 13.861 15 20.4999 15" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php foreach ($freelancer_languages as $language) {
                        $language_link = get_term_link($language, 'freelancer_languages'); ?>
                        <a href="<?php echo esc_url($language_link); ?>" class="cate">
                            <?php esc_html_e($language->name); ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php if (is_array($freelancer_skills)) { ?>
            <div class="freelancer-skills">
                <?php foreach ($freelancer_skills as $skill) {
                    $skill_link = get_term_link($skill, 'freelancer_skills'); ?>
                    <a href="<?php echo esc_url($skill_link); ?>" class="label label-skills">
                        <?php esc_html_e($skill->name); ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <?php if ($enable_post_type_service == '1') { ?>
        <div class="freelancer-bottom">
            <?php if ($enable_freelancer_review) : ?>
                <?php echo felan_get_total_rating('freelancer', $freelancer_id); ?>
            <?php endif; ?>
            <?php echo sprintf(esc_html__('%s services', 'felan-framework'), felan_total_services_to_freelancer($freelancer_id)) ?>
        </div>
    <?php } ?>
</div>