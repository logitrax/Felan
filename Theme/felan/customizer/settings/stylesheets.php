<?php

// Add style to style.css mytheme
function felan_add_customizer_styles()
{

	$enable_rtl_mode = Felan_Helper::felan_get_option(
		"enable_rtl_mode",
		0
	);
	if (is_rtl() || $enable_rtl_mode) {
		wp_enqueue_style('felan_custom-rtl-style', get_stylesheet_uri());
		$custom_css = felan_get_customizer_css();
		wp_add_inline_style('felan_custom-rtl-style', $custom_css);
	} else {
		wp_enqueue_style('felan_main-style', get_stylesheet_uri());
		$custom_css = felan_get_customizer_css();
		wp_add_inline_style('felan_main-style', $custom_css);
	}
}
add_action('wp_enqueue_scripts', 'felan_add_customizer_styles', 99);

function felan_get_customizer_css()
{

	ob_start();

	// Variables --------------------------------------------------------------------------------------------
	$primary_color 		   = Felan_Helper::get_setting('primary_color');
	$text_color 		   = Felan_Helper::get_setting('text_color');
	$accent_color 	   	   = Felan_Helper::get_setting('accent_color');
	$secondary_color 	   = Felan_Helper::get_setting('secondary_color');
	$border_color          = Felan_Helper::get_setting('border_color');
	$body_background_color = Felan_Helper::get_setting('body_background_color');
	$bg_body_image 		   = Felan_Helper::get_setting('bg_body_image');
	$bg_body_size 		   = Felan_Helper::get_setting('bg_body_size');
	$bg_body_repeat 	   = Felan_Helper::get_setting('bg_body_repeat');
	$bg_body_position 	   = Felan_Helper::get_setting('bg_body_position');
	$bg_body_attachment    = Felan_Helper::get_setting('bg_body_attachment');

	$content_width 		   = Felan_Helper::get_setting('content_width');
	$sidebar_width 		   = Felan_Helper::get_setting('sidebar_width');

	$top_bar_color = Felan_Helper::get_setting('top_bar_color');
	$top_bar_background = Felan_Helper::get_setting('top_bar_bg_color');

	$header_background = Felan_Helper::get_setting('header_background');
	$header_sticky_background = Felan_Helper::get_setting('header_sticky_background');

	$logo_width            = Felan_Helper::get_setting('logo_width');
	$header_padding_top    = Felan_Helper::get_setting('header_padding_top');
	$header_padding_bottom = Felan_Helper::get_setting('header_padding_bottom');

	$blog_sidebar_width    = Felan_Helper::get_setting('blog_sidebar_width');

	$page_title_bg_color       = Felan_Helper::get_setting('page_title_bg_color');
	$page_title_text_color     = Felan_Helper::get_setting('page_title_text_color');
	$page_title_bg_image       = Felan_Helper::get_setting('page_title_bg_image');
	$page_title_bg_size        = Felan_Helper::get_setting('page_title_bg_size');
	$page_title_bg_repeat      = Felan_Helper::get_setting('page_title_bg_repeat');
	$page_title_bg_position    = Felan_Helper::get_setting('page_title_bg_position');
	$page_title_bg_attachment  = Felan_Helper::get_setting('page_title_bg_attachment');
	$page_title_font_size      = Felan_Helper::get_setting('page_title_font_size');
	$page_title_letter_spacing = Felan_Helper::get_setting('page_title_letter_spacing');
	if (empty($page_title_letter_spacing)) {
		$page_title_letter_spacing = 'normal';
	} else {
		$page_title_letter_spacing = $page_title_letter_spacing . 'px';
	}

	$style_page_title_blog          = Felan_Helper::get_setting('style_page_title_blog');
	$bg_page_title_blog             = Felan_Helper::get_setting('bg_page_title_blog');
	$color_page_title_blog          = Felan_Helper::get_setting('color_page_title_blog');
	$bg_image_page_title_blog       = Felan_Helper::get_setting('bg_image_page_title_blog');
	$bg_size_page_title_blog        = Felan_Helper::get_setting('bg_size_page_title_blog');
	$bg_repeat_page_title_blog      = Felan_Helper::get_setting('bg_repeat_page_title_blog');
	$bg_position_page_title_blog    = Felan_Helper::get_setting('bg_position_page_title_blog');
	$bg_attachment_page_title_blog  = Felan_Helper::get_setting('bg_attachment_page_title_blog');
	$font_size_page_title_blog      = Felan_Helper::get_setting('font_size_page_title_blog');
	$letter_spacing_page_title_blog = Felan_Helper::get_setting('letter_spacing_page_title_blog');
	if (empty($letter_spacing_page_title_blog)) {
		$letter_spacing_page_title_blog = 'normal';
	} else {
		$letter_spacing_page_title_blog = $letter_spacing_page_title_blog . 'px';
	}

	//Color ----------------------------------------------------------------------------------------
?>
	:root {
	--felan-color-text: <?php echo esc_attr($text_color); ?>;
	--felan-color-accent: <?php echo esc_attr($accent_color); ?>;
	--felan-color-primary: <?php echo esc_attr($primary_color); ?>;
	--felan-color-secondary: <?php echo esc_attr($secondary_color); ?>;
	--felan-color-border: <?php echo esc_attr($border_color); ?>;
	--felan-color-bg: <?php echo esc_attr($body_background_color); ?>;
	}
	<?php

	// Body Background Custom ------------------------------------------------------------------------------
	$id = get_the_ID();
	if (!empty($id)) {
		$page_body_bg = get_post_meta($id, 'felan-page_body_bg', true);
	}
	?>
	body {
	<?php if (!empty($page_body_bg)) : ?>
		background-color: <?php echo esc_attr($page_body_bg); ?>;
	<?php else : ?>
		<?php if (!empty($body_background_color)) : ?>
			background-color: <?php echo esc_attr($body_background_color); ?>;
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!empty($bg_body_image)) : ?>
		background-image: url(<?php echo esc_attr($bg_body_image); ?>);
	<?php endif; ?>
	<?php if (!empty($bg_body_size)) : ?>
		background-size: <?php echo esc_attr($bg_body_size); ?>;
	<?php endif; ?>
	<?php if (!empty($bg_body_repeat)) : ?>
		background-repeat: <?php echo esc_attr($bg_body_repeat); ?>;
	<?php endif; ?>
	<?php if (!empty($bg_body_position)) : ?>
		background-position: <?php echo esc_attr($bg_body_position); ?>;
	<?php endif; ?>
	<?php if (!empty($bg_body_attachment)) : ?>
		background-attachment: <?php echo esc_attr($bg_body_attachment); ?>;
	<?php endif; ?>
	}
	<?php

	// Content Width ---------------------------------------------------------------------------------------
	$id = get_the_ID();
	if (!empty($id)) {
		$page_pt_deskop = get_post_meta($id, 'felan-page_pt_deskop', true);
		$page_pb_deskop = get_post_meta($id, 'felan-page_pb_deskop', true);
		$page_pt_mobie = get_post_meta($id, 'felan-page_pt_mobie', true);
		$page_pb_mobie = get_post_meta($id, 'felan-page_pb_mobie', true);
	} ?>
	body.page .main-content{
	<?php if (!empty($page_pt_deskop)) : ?>
		padding-top: <?php echo esc_attr($page_pt_deskop); ?>;
	<?php endif; ?>
	<?php if (!empty($page_pb_deskop)) : ?>
		padding-bottom: <?php echo esc_attr($page_pb_deskop); ?>;
	<?php endif; ?>
	}

	@media(max-width:767px){
	body.page .main-content{
	<?php if (!empty($page_pt_mobie)) : ?>
		padding-top: <?php echo esc_attr($page_pt_mobie); ?>;
	<?php endif; ?>
	<?php if (!empty($page_pb_mobie)) : ?>
		padding-bottom: <?php echo esc_attr($page_pb_mobie); ?>;
	<?php endif; ?>
	}}

	<?php if (!empty($content_width)) {
	?>
		#page.fullwidth {
		max-width: <?php echo esc_attr($content_width); ?>px;
		}
	<?php
	}

	// Sidebar Width ---------------------------------------------------------------------------------------
	if (!empty($sidebar_width)) {
	?>
		.content-page .site-layout.has-sidebar aside#secondary {
		flex: 0 0 <?php echo esc_attr($sidebar_width); ?>px;
		max-width: <?php echo esc_attr($sidebar_width); ?>px;
		}
		.content-page .site-layout.has-sidebar #primary {
		flex: 1;
		max-width: calc(100% - <?php echo esc_attr($sidebar_width); ?>px);
		}
	<?php
	}

	// Top Bar ---------------------------------------------------------------------------
	if (!empty($top_bar_color)) {
	?>
		.felan-top-bar,.felan-top-bar a {
		color: <?php echo esc_attr($top_bar_color); ?>!important;
		}
	<?php
	}

	if (!empty($top_bar_background)) {
	?>
		.felan-top-bar {
		background-color: <?php echo esc_attr($top_bar_background); ?>!important;
		}
	<?php
	}

	// Header Background ---------------------------------------------------------------------------
	if (!empty($header_background)) {
	?>
		header.site-header {
		background-color: <?php echo esc_attr($header_background); ?>!important;
		}
	<?php
	}

	// Header Sticky Background ---------------------------------------------------------------------------
	if (!empty($header_sticky_background)) {
	?>
		header.site-header.sticky-header.on {
		background-color: <?php echo esc_attr($header_sticky_background); ?>!important;
		}
	<?php
	}

	// Logo Width ---------------------------------------------------------------------------------------
	if (!empty($logo_width)) {
	?>
		header.site-header .site-logo img {
		max-width: <?php echo esc_attr($logo_width); ?>px;
		}
		.nav-dashboard-header .site-logo img {
		max-width: <?php echo esc_attr($logo_width); ?>px;
		}
	<?php
	}

	// Header Padding Top ---------------------------------------------------------------------------------------
	if (!empty($header_padding_top)) {
	?>
		header.site-header {
		padding-top: <?php echo esc_attr($header_padding_top); ?>px;
		}
	<?php
	}

	// Header Padding Bottom ---------------------------------------------------------------------------------------
	if (!empty($header_padding_bottom)) {
	?>
		header.site-header {
		padding-bottom: <?php echo esc_attr($header_padding_bottom); ?>px;
		}
	<?php
	}

	if (!empty($blog_sidebar_width)) {
	?>
		.content-blog .site-layout.has-sidebar aside#secondary {
		flex: 0 0 <?php echo esc_attr($blog_sidebar_width); ?>px;
		max-width: <?php echo esc_attr($blog_sidebar_width); ?>px;
		}
		.content-blog .site-layout.has-sidebar #primary {
		flex: 1;
		max-width: calc(100% - <?php echo esc_attr($blog_sidebar_width); ?>px);
		}
	<?php
	}

	// Page Title ------------------------------------------------------------------------------------------
	?>
	.page-title-blog {
	<?php if (!empty($bg_image_page_title_blog)) : ?>
		background-image: url(<?php echo esc_attr($bg_image_page_title_blog); ?>);
		padding-top:80px;
		padding-bottom:80px;
	<?php endif; ?>
	<?php if (!empty($bg_page_title_blog)) : ?>
		background-color: <?php echo esc_attr($bg_page_title_blog); ?>;
		padding-top:80px;
		padding-bottom:80px;
	<?php endif; ?>
	background-size: <?php echo esc_attr($bg_size_page_title_blog); ?>;
	background-repeat: <?php echo esc_attr($bg_repeat_page_title_blog); ?>;
	background-position: <?php echo esc_attr($bg_position_page_title_blog); ?>;
	background-attachment: <?php echo esc_attr($bg_attachment_page_title_blog); ?>
	}
	.page-title-blog,.page-title-blog .entry-detail .entry-title {
	font-style: <?php echo esc_attr($style_page_title_blog); ?>;
	color: <?php echo esc_attr($color_page_title_blog); ?>;
	}
	.page-title-blog .entry-title {
	font-size: <?php echo esc_attr($font_size_page_title_blog); ?>px;
	letter-spacing: <?php echo esc_attr($letter_spacing_page_title_blog); ?>;
	}

	<?php $id = get_the_ID();
	if (!empty($id)) {
		$page_title_color = get_post_meta($id, 'felan-page_title_color', true);
		$page_title_bg = get_post_meta($id, 'felan-page_title_bg', true);
		$page_title_image = get_post_meta($id, 'felan-page_title_image', true);
	}
	?>
	.page-title-orther,
	.page-title-other {
	<?php if (!empty($page_title_image['url'])) : ?>
		background-image: url(<?php echo esc_attr($page_title_image['url']); ?>);
		padding-top:80px;
		padding-bottom:80px;
	<?php else : ?>
		<?php if (!empty($page_title_bg_image)) : ?>
			background-image: url(<?php echo esc_attr($page_title_bg_image); ?>);
			padding-top:80px;
			padding-bottom:80px;
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!empty($page_title_bg)) : ?>
		background-color: <?php echo esc_attr($page_title_bg); ?>;
		padding-top:80px;
		padding-bottom:80px;
	<?php else : ?>
		<?php if (!empty($page_title_bg_color)) : ?>
			background-color: <?php echo esc_attr($page_title_bg_color); ?>;
			padding-top:80px;
			padding-bottom:80px;
		<?php endif; ?>
	<?php endif; ?>

	background-size: <?php echo esc_attr($page_title_bg_size); ?>;
	background-repeat: <?php echo esc_attr($page_title_bg_repeat); ?>;
	background-position: <?php echo esc_attr($page_title_bg_position); ?>;
	background-attachment: <?php echo esc_attr($page_title_bg_attachment); ?>
	}
	.page-title-orther,
	.page-title-other,
	.page-title-orther .entry-detail .entry-title,
	.page-title-other .entry-detail .entry-title {
	<?php if (!empty($page_title_color)) : ?>
		color: <?php echo esc_attr($page_title_color); ?>;
	<?php else : ?>
		<?php if (!empty($page_title_text_color)) : ?>
			color: <?php echo esc_attr($page_title_text_color); ?>;
		<?php endif; ?>
	<?php endif; ?>
	}
	.page-title-orther .entry-title,
	.page-title-other .entry-title {
	font-size: <?php echo esc_attr($page_title_font_size); ?>px;
	letter-spacing: <?php echo esc_attr($page_title_letter_spacing); ?>;
	}
<?php

	$css = ob_get_clean();
	return $css;
}
