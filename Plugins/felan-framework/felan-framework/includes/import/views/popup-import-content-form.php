<?php

/**
 * Import content form
 *
 * @package Felan_Framework
 */

if (!defined('ABSPATH')) {
	exit();
}

?>
<div id="import-content-wrapper">
	<h4 class="felan-popup__title animated fadeInRight"><?php esc_html_e('Import data', 'felan-framework'); ?></h4>
	<p class="felan-error-text animated fadeInRight">&nbsp;</p>
	<?php if (isset($import_content_steps) && !empty($import_content_steps)) : ?>
		<ul class="felan-import-content-list animated fadeInRight">
			<?php
			$i             = 0;
			$content_steps = '';
			foreach ($import_content_steps as $key => $text) :
				$content_steps .= $key . ',';
			?>
				<li id="<?php echo esc_attr($key); ?>" class="felan-import-content__item" data-action="<?php echo esc_attr("import_{$key}"); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce("import_{$key}")); ?>">
					<i class="las la-circle-notch la-spin"></i><span class="felan-import-content__text"><?php esc_html_e($text); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" name="import_content_steps" id="import_content_steps" value="<?php echo esc_attr($content_steps); ?>">
	<?php endif; ?>
	<input type="hidden" name="demo_slug" id="demo_slug" value="<?php echo esc_attr($demo_slug); ?>">
	<div class="felan-popup__footer animated fadeInRight">
		<i class="felan-popup__note"><?php esc_html_e('Please do not close this window until the process is completed', 'felan-framework'); ?></i>
		<a href="#" class="felan-popup__close-button"><?php esc_html_e('Close', 'felan-framework'); ?></a>
	</div>
	</form>