<?php

/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 */

get_header();

$type = Felan_Helper::get_setting('page_404_type');
$title = Felan_Helper::get_setting('page_404_title');
$des = Felan_Helper::get_setting('page_404_des');
$btn = Felan_Helper::get_setting('page_404_btn');
$image = Felan_Helper::get_setting('page_404_image');

if ($type !== '') {
	if (defined('ELEMENTOR_VERSION')) {
		echo \Elementor\Plugin::$instance->frontend->get_builder_content($type);
	} else {
		$page404 = get_post($type);
		if (!empty($page404->post_content)) {
			echo wp_kses_post($page404->post_content);
		}
	}
} else { ?>
    <div class="main-content content-page page-404">
        <div class="container">
            <div class="site-layout">
                <div class="area-404">
                    <div class="left-404">
                        <p class="text"><?php echo esc_html('404', 'felan'); ?></p>
                        <h2><?php echo esc_html($title); ?></h2>
                        <p><?php echo esc_html($des); ?></p>
                        <a class="felan-button button-outline-accent button-icon-right" href="<?php echo esc_url(home_url()); ?>">
                            <?php echo esc_html($btn); ?>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                    <div class="right-404">
                        <img src="<?php echo esc_html($image); ?>" alt="<?php esc_attr_e('Image 404', 'felan'); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php
get_footer();
