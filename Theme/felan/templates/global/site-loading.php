<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$type_loading_effect      = Felan_Helper::get_setting('type_loading_effect');
$animation_loading_effect = Felan_Helper::get_setting('animation_loading_effect');
$image_loading_effect     = Felan_Helper::get_setting('image_loading_effect');

$args = array('css-1'  => '<span class="felan-ldef-circle felan-ldef-loading"><span></span></span>', 'css-2'  => '<span class="felan-ldef-dual-ring felan-ldef-loading"></span>', 'css-3' => '<span class="felan-ldef-facebook felan-ldef-loading"><span></span><span></span><span></span></span>', 'css-4'  => '<span class="felan-ldef-heart felan-ldef-loading"><span></span></span>', 'css-5'  => '<span class="felan-ldef-ring felan-ldef-loading"><span></span><span></span><span></span><span></span></span>', 'css-6'  => '<span class="felan-ldef-roller felan-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>', 'css-7'  => '<span class="felan-ldef-default felan-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>', 'css-8'  => '<span class="felan-ldef-ellipsis felan-ldef-loading"><span></span><span></span><span></span><span></span></span>', 'css-9'  => '<span class="felan-ldef-grid felan-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>', 'css-10'  => '<span class="felan-ldef-hourglass felan-ldef-loading"></span>', 'css-11'  => '<span class="felan-ldef-ripple felan-ldef-loading"><span></span><span></span></span>', 'css-12'  => '<span class="felan-ldef-spinner felan-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>');

?>

<?php if ($type_loading_effect !== 'none') { ?>

	<div class="page-loading-effect">
		<div class="bg-overlay"></div>

		<div class="entry-loading">
			<?php if ($type_loading_effect == 'css_animation') { ?>
				<?php echo wp_kses($args[$animation_loading_effect], Felan_Helper::felan_kses_allowed_html()); ?>
			<?php } ?>

			<?php if ($type_loading_effect == 'image') { ?>
				<img src="<?php echo esc_url($image_loading_effect); ?>" alt="<?php esc_attr_e('Image Effect', 'felan'); ?>">
			<?php } ?>
		</div>
	</div>

<?php } ?>