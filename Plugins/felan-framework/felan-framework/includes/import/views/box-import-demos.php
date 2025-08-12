<?php

/**
 * Import Demos Box
 *
 * @package Felan_Framework
 */

if (!defined('ABSPATH')) {
	exit();
}

$demos       = Felan_Importer::get_import_demos();
$demos_count = count($demos);
?>
<div class="felan-box felan-box--green felan-box--import-demos">
	<div class="felan-box__header">
		<span class="felan-box__icon"><i class="fad fa-download"></i></span>
		<h3>
			<?php
			if (!empty($demos) && 1 < $demos_count) {
				esc_html_e('Select a demo to import', 'felan-framework');
			} elseif (1 === $demos_count) {
				$demo     = reset($demos);
				$name     = isset($demo['name']) ? $demo['name'] : esc_html__('Import Demo', 'felan-framework');
				$imported = get_option(GLF_THEME_SLUG . '_' . key($demos) . '_imported', false);

				if (!$imported) :
					esc_html_e($name);
				else :
					esc_html_e($name);
			?>
					<small><?php esc_html_e('(has been imported before)', 'felan-framework'); ?></small>
			<?php
				endif;
			}
			?>
		</h3>
		<?php if (1 === $demos_count) : ?>
			<a href="#" class="button felan-import-demo__button" data-demo-slug="<?php echo esc_attr(key($demos)); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('fetch_demo_steps')); ?>"><?php esc_html_e('Import Demo Data', 'felan-framework'); ?></a>
		<?php endif; ?>

		<a href="#" class="button felan-import-refresh__button"><?php esc_html_e('Refresh Data', 'felan-framework'); ?></a>
	</div>
	<div class="felan-box__body<?php echo esc_attr(1 < $demos_count) ? ' felan-box__body--flex' : ''; ?>">

		<?php
		/**
		 * Hook: felan_box_import_demos_before_content
		 */
		do_action('felan_box_import_demos_before_content');
		?>

		<p class="felan-error-text"></p>

		<?php if (!empty($demos)) : ?>

			<?php
			$grid_class = '';
			if (0 < $demos_count) {
				$grid_class .= ' grid columns-3';
			}
			?>
			<div class="list-demo <?php echo esc_attr($grid_class); ?>">

				<?php foreach ($demos as $demo_slug => $demo) : ?>
					<?php $imported = get_option(FELAN_PLUGIN_DIR . '_' . $demo_slug . '_imported', false); ?>
					<?php if (isset($demo['name'], $demo['preview_image_url'])) : ?>
						<?php
						$css_class = "felan-import-demo felan-import-demo--{$demo_slug}";
						?>
						<div class="<?php echo esc_attr($css_class); ?>">
							<div class="felan-import-demo__inner">
								<div class="felan-import-demo__preview">
									<img src="<?php echo esc_attr($demo['preview_image_url']); ?>" alt="<?php echo esc_attr($demo['name']); ?>" />
								</div>

								<?php if (1 < $demos_count) : ?>
									<div class="felan-import-demo__footer">
										<p class="felan-import-demo__name">
											<?php if (!$imported) : ?>
												<span><?php esc_html_e($demo['name']); ?></span>
											<?php else : ?>
												<span>
													<?php esc_html_e($demo['name']); ?>
													<small><?php esc_html_e('(has been imported before)', 'felan-framework'); ?></small>
												</span>
											<?php endif; ?>
											<?php if (isset($demo['description'])) : ?>
												<span class="felan-import-demo__help hint--right" aria-label="<?php echo esc_attr($demo['description']); ?>"><i class="fad fa-question-circle"></i></span>
											<?php endif; ?>
										</p>
										<a href="#" class="button felan-import-demo__button" data-demo-slug="<?php echo esc_attr($demo_slug); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('fetch_demo_steps')); ?>">
											<?php esc_html_e('Import', 'felan-framework'); ?>
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<!-- /Import <?php esc_html_e($demo['name']); ?> -->
					<?php endif; ?>
				<?php endforeach; ?>

			</div>
		<?php endif; ?>

		<?php
		/**
		 * Hook: felan_box_import_demos_after_content
		 */
		do_action('felan_box_import_demos_after_content');
		?>

	</div>

	<div id="felan-import-demo-popup" class="felan-popup mfp-hide">
	</div>
</div>