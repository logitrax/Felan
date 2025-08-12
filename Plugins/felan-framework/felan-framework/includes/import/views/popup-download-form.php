<?php

/**
 * Download form
 *
 * @package Felan_Framework
 */

if (!defined('ABSPATH')) {
	exit();
}
?>
<i class="las la-circle-notch la-spin felan-loading__icon"></i>
<form action="#" method="POST" id="download-media-package-form">
	<h4 class="felan-popup__title animated fadeInRight"><?php esc_html_e('Download media package', 'felan-framework'); ?></h4>
	<p class="felan-error-text">&nbsp;</p>
	<div class="felan-progress-bar animated fadeInRight">
		<span class="felan-progress-bar__text"><?php esc_html_e('Initializing', 'felan-framework'); ?></span>
		<div class="felan-progress-bar__wrapper">
			<div class="felan-progress-bar__inner">&nbsp;</div>
		</div>
	</div>
	<?php if (isset($selected_steps_str) && !empty($selected_steps_str)) : ?>
		<input type="hidden" name="selected_steps" value="<?php echo esc_attr($selected_steps_str); ?>">
	<?php endif; ?>
	<input type="hidden" name="media_package_url" id="media_package_url" value="<?php echo esc_attr($media_package_url); ?>">
	<input type="hidden" name="demo_slug" id="demo_slug" value="<?php echo esc_attr($demo_slug); ?>">
	<input type="hidden" name="_wpnonce" id="_wpnonce" value="<?php echo esc_attr(wp_create_nonce('download_media_package')); ?>">
	<div class="felan-popup__footer animated fadeInRight">
		<i class="felan-popup__note"><?php esc_html_e('Please do not close this window until the process is completed', 'felan-framework'); ?></i>
		<a href="#" class="felan-popup__close-button"><?php esc_html_e('Close', 'felan-framework'); ?></a>
	</div>
</form>