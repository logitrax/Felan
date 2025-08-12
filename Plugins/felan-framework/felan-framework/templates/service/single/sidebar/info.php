<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$author_id = get_post_field('post_author', $service_id);
$args_freelancer = array(
    'post_type' => 'freelancer',
    'posts_per_page' => 1,
    'author' => $author_id,
);
$current_user_posts = get_posts($args_freelancer);
$freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
$freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$freelancer_featured = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
$freelancer_languages       = get_the_terms($freelancer_id, 'freelancer_languages');
$freelancer_location        = get_the_terms($freelancer_id, 'felan_location');
$freelancer_phone           = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_phone', true);
$freelancer_email           = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_email', true);
$freelancer_current_position = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);

$classes = array();
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_service_sidebar_type');
$currency_sign_default = felan_get_option('currency_sign_default');
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
}

$verify_status = get_the_author_meta( 'user_verify', $author_id );
$show_badge    = felan_get_option( 'show_freelancer_verification', '1' );
?>
<div class="service-info-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?>">
    <div class="service-info-warpper">
        <div class="info-inner">
            <?php if (!empty($freelancer_avatar)) : ?>
                <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
            <?php else : ?>
                <div class="image-freelancers"><i class="far fa-camera"></i></div>
            <?php endif; ?>
            <div class="inner-right">
                <?php if (!empty(get_the_title($freelancer_id))) : ?>
                    <h4 style="display: inline-block;">
                        <a href="<?php echo esc_url(get_permalink($freelancer_id)); ?>">
                            <?php echo get_the_title($freelancer_id); ?>
                            <?php if ($freelancer_featured == 1) : ?>
                                <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'felan-framework') ?>"><i class="far fa-check"></i></span>
                            <?php endif; ?>
                        </a>
                    </h4>
                <?php endif; ?>

				<?php if ( $verify_status == 1 && $show_badge == 1 ) : ?>
					<img style="width: 20px" class="image-freelancers-verify" src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/check-badge-verified.png'); ?>" alt="" />
				<?php endif; ?>

                <div class="review-inner">
                    <p class="position"><?php echo esc_html($freelancer_current_position); ?></p>
                    <?php echo felan_get_total_rating('freelancer', $freelancer_id); ?>
                </div>
            </div>
        </div>
        <div class="info-content freelancer-sidebar">
            <?php if (is_array($freelancer_location)) { ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php foreach ($freelancer_location as $location) {
                        $cate_link = get_term_link($location, 'felan_location'); ?>
                        <div class="cate-warpper">
                            <a href="<?php echo esc_url($cate_link); ?>" class="cate">
                                <?php echo $location->name; ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if (is_array($freelancer_languages)) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3M12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3M12 21C14.7614 21 15.9413 15.837 15.9413 12C15.9413 8.16303 14.7614 3 12 3M12 21C9.23858 21 8.05895 15.8369 8.05895 12C8.05895 8.16307 9.23858 3 12 3M3.49988 8.99998C10.1388 8.99998 13.861 8.99998 20.4999 8.99998M3.49988 15C10.1388 15 13.861 15 20.4999 15" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php foreach ($freelancer_languages as $language) {
                        $cate_link = get_term_link($language, 'felan_location'); ?>
                        <div class="cate-warpper">
                            <a href="<?php echo esc_url($cate_link); ?>" class="cate"><?php echo trim($language->name); ?></a>
                        </div>
                    <?php } ?>
                </div>
            <?php endif; ?>
            <?php if ($freelancer_phone) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.6 14.5215C13.205 17.0421 7.09582 10.9878 9.49995 8.45753C10.9678 6.91263 9.30963 5.14707 8.3918 3.84934C6.66924 1.41378 2.88771 4.77641 3.00256 6.91544C3.36473 13.6609 10.6615 21.6546 17.7275 20.9574C19.9381 20.7393 22.4778 16.7471 19.9423 15.2882C18.6745 14.5587 16.9342 13.1172 15.6 14.5215Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info"><a href="tel:<?php esc_attr_e($freelancer_phone); ?>"><?php esc_attr_e($freelancer_phone); ?></a></p>
                </div>
            <?php endif; ?>
            <?php if ($freelancer_email) : ?>
                <div class="info">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 12C2 8.22876 2 6.34315 3.46447 5.17157C4.92893 4 7.28595 4 12 4C16.714 4 19.0711 4 20.5355 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.5355 18.8284C19.0711 20 16.714 20 12 20C7.28595 20 4.92893 20 3.46447 18.8284C2 17.6569 2 15.7712 2 12Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M20.6667 5.31018L15.8412 9.79909C14.0045 11.3296 13.0862 12.0949 12.0001 12.0949C10.9139 12.0949 9.99561 11.3296 8.15897 9.7991L3.3335 5.31018" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="details-info email"><a href="mailto:<?php esc_attr_e($freelancer_email) ?>"><?php esc_attr_e($freelancer_email); ?></a></p>
                </div>
            <?php endif; ?>
            <div class="button-warpper">
                <a href="<?php echo get_the_permalink($freelancer_id); ?>" class="felan-button view-profile">
                    <?php esc_html_e('View profile', 'felan-framework'); ?>
                </a>
                <?php felan_get_template('freelancer/messages.php', array(
                    'freelancer_id' => $freelancer_id,
                )); ?>
            </div>
        </div>
    </div>
</div>