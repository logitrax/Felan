<?php

/**
 * Success content for popup after importing
 *
 * @package Felan_Framework
 */

if (!defined('ABSPATH')) {
	exit();
}

$regenerate_thumbnails = apply_filters('felan_regenerate_thumbnails', false);
?>
<div class="animated fadeInRight" id="refresh-data">
	<h4 class="felan-popup__title"><?php esc_html_e('Refresh done!', 'felan-framework'); ?></h4>
	<p class="felan-popup__subtitle"><?php esc_html_e('Refresh is successful! Now customization is as easy as pie. Enjoy it!', 'felan-framework'); ?></p>
	<div class="felan-popup__footer">
		<div class="felan-popup__buttons">
			<a href="#" class="felan-popup__close-button"><?php esc_html_e('Close', 'felan-framework'); ?></a>
			<a href="<?php echo esc_url(site_url('/')); ?>" target="_blank" class="felan-popup__next-button"><?php esc_html_e('View your website', 'felan-framework'); ?></a>
		</div>
	</div>
</div>