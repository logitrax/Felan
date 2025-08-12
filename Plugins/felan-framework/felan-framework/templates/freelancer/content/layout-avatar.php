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
$enable_freelancer_des = felan_get_option('enable_freelancer_show_des');
$enable_freelancer_review = felan_get_option('enable_single_freelancer_review', '1');
$enable_freelancer_single_popup = felan_get_option('enable_freelancer_single_popup', '0');
$default_avatar = FELAN_THEME_URI . '/assets/images/default-user-image.png';

$verify_status = get_the_author_meta( 'user_verify', $author_id );
$show_badge    = felan_get_option( 'show_freelancer_verification', '1' );
?>
<div class="<?php echo join(' ', $freelancer_item_class); ?>">
    <div class="freelancer-top">
        <div class="freelancer-header">
            <a class="freelancer-avatar-inner" href="<?php echo get_the_permalink($freelancer_id); ?>">
                <?php if (!empty($freelancer_avatar)) : ?>
                    <img src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
                <?php else: ?>
                    <img src="<?php echo esc_attr($default_avatar) ?>" alt="" />
                <?php endif; ?>
            </a>
        </div>
        <?php if (!empty(get_the_title($freelancer_id))) : ?>
            <h2 class="freelancers-title">
                <a href="<?php echo get_the_permalink($freelancer_id); ?>"><?php echo get_the_title($freelancer_id); ?></a>
                <?php if ($freelancer_featured == 1) : ?>
                    <span class="felan-label felan-label-yellow"><?php echo esc_html__('Featured', 'felan-framework') ?></span>
                <?php endif; ?>

				<?php if ( $verify_status == 1 && $show_badge == 1 ) : ?>
					<img style="width: 20px" class="image-freelancers-verify" src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/check-badge-verified.png'); ?>" alt="" />
				<?php endif; ?>
            </h2>
        <?php endif; ?>
        <div class="freelancer-inner">
            <?php if ($enable_freelancer_review) : ?>
                <?php echo felan_get_total_rating('freelancer', $freelancer_id); ?>
            <?php endif; ?>
            <?php if ($freelancer_current_position) : ?>
                <span class="current-position">
                    <?php echo esc_html($freelancer_current_position); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="freelancer-center">
        <div class="freelancer-price">
            <?php echo esc_html__('From', 'felan-framework'); ?>
            <?php felan_get_salary_freelancer($freelancer_id); ?>
        </div>
    </div>
    <div class="freelancer-bottom">
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
</div>