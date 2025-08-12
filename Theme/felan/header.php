<?php

/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php if (is_singular() && pings_open(get_queried_object())) : ?>
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
	<?php endif; ?>

	<?php wp_head(); ?>
</head>

<?php
$dir = '';
$id = get_the_ID();
$enable_rtl_mode  = Felan_Helper::felan_get_option('enable_rtl_mode', 0);
$show_page_rtl   = get_post_meta($id, 'felan-show_page_rtl', true);

if (is_rtl() || $enable_rtl_mode || $show_page_rtl == '1') {
	$dir = 'dir=rtl';
}

use \Elementor\Plugin;
?>

<body <?php body_class() ?> <?php echo esc_attr($dir); ?>>

	<?php wp_body_open(); ?>

	<?php
	$layout_content         = Felan_Helper::get_setting('layout_content');
	$header_type 			= Felan_Helper::get_setting("header_type");
	$header_dashboard_type 	= Felan_Helper::get_setting("header_dashboard_type");
	$sticky_header          = Felan_Helper::get_setting('sticky_header');
	$float_header           = Felan_Helper::get_setting('float_header');
	$top_bar_enable         = Felan_Helper::get_setting("top_bar_enable");
	$page_header_show = $page_header_float = $page_header_type = $page_header_sticky = $page_top_bar = $header_style = $page_header_rtl = $show_border_bottom = '';
	if (!empty($id)) {
		$page_header_show   = get_post_meta($id, 'felan-header_show', true);
		$page_header_type  = get_post_meta($id, 'felan-header_type', true);
		$page_header_float  = get_post_meta($id, 'felan-show_header_float', true);
		$page_header_sticky = get_post_meta($id, 'felan-show_header_sticky', true);
		$page_top_bar       = get_post_meta($id, 'felan-show_top_bar', true);
		$show_border_bottom       = get_post_meta($id, 'felan-show_border_bottom', true);
		$header_style       = get_post_meta($id, 'felan-header_style', true);
	}
	$header_classes = $topbar_classes = array();

	if ($header_style == 'light') {
		$header_classes[] = 'header-light';
	} else {
		$header_classes[] = 'header-dark';
	}

	if ($sticky_header) {
		if ($page_header_sticky == '0') {
			$header_classes[] = '';
		} else {
			$header_classes[] = 'sticky-header';
		}
	} else {
		if ($page_header_sticky == '1') {
			$header_classes[] = 'sticky-header';
		} else {
			$header_classes[] = '';
		}
	}

	if ($float_header) {
		if ($page_header_float == '0') {
			$header_classes[] = '';
		} else {
			$header_classes[] = 'float-header';
		}
	} else {
		if ($page_header_float == '1') {
			$header_classes[] = 'float-header';
		} else {
			$header_classes[] = '';
		}
	}

	if ($show_border_bottom == '0') {
		$header_classes[] = 'no-border-bottom';
	}

    if ((Felan_Helper::felan_page_shortcode('[felan_dashboard]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_dashboard]')
            || Felan_Helper::felan_page_shortcode('[felan_jobs]')
            || Felan_Helper::felan_page_shortcode('[felan_jobs_performance]')
            || Felan_Helper::felan_page_shortcode('[felan_jobs_submit]')
            || Felan_Helper::felan_page_shortcode('[felan_applicants]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancers]')
            || Felan_Helper::felan_page_shortcode('[felan_projects]')
            || Felan_Helper::felan_page_shortcode('[felan_user_package]')
            || Felan_Helper::felan_page_shortcode('[felan_messages]')
            || Felan_Helper::felan_page_shortcode('[felan_projects_submit]')
            || Felan_Helper::felan_page_shortcode('[felan_project_payment]')
            || Felan_Helper::felan_page_shortcode('[felan_project_payment_completed]')
            || Felan_Helper::felan_page_shortcode('[felan_company]')
            || Felan_Helper::felan_page_shortcode('[felan_submit_company]')
            || Felan_Helper::felan_page_shortcode('[felan_my_project]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_wallet]')
            || Felan_Helper::felan_page_shortcode('[felan_meetings]')
            || Felan_Helper::felan_page_shortcode('[felan_settings]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_settings]')
            || Felan_Helper::felan_page_shortcode('[felan_package]')
            || Felan_Helper::felan_page_shortcode('[felan_payment]')
            || Felan_Helper::felan_page_shortcode('[felan_payment_completed]')
            || Felan_Helper::felan_page_shortcode('[felan_my_jobs]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_company]')
            || Felan_Helper::felan_page_shortcode('[felan_disputes]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_disputes]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_profile]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_my_review]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_meetings]')
            || Felan_Helper::felan_page_shortcode('[felan_submit_service]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_service]')
            || Felan_Helper::felan_page_shortcode('[felan_employer_service]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_user_package]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_package]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_payment]')
            || Felan_Helper::felan_page_shortcode('[felan_freelancer_payment_completed]')
            || Felan_Helper::felan_page_shortcode('[woocommerce_checkout]')
        ) && $header_dashboard_type
    ) {
        $header_classes[] = 'header-' . esc_attr($header_dashboard_type);
        $header_type = $header_dashboard_type ? $header_dashboard_type : $header_type;
    } else {
        if ($page_header_type) {
            $header_classes[] = 'header-' . esc_attr($page_header_type);
        }
        $header_type = $page_header_type ? $page_header_type : $header_type;
    }

    if (class_exists('Elementor\Plugin')) {
        if (Elementor\Plugin::$instance->db->is_built_with_elementor($header_type)) {
            $header_classes[] = 'header-elementor';
        }
    }
	?>

	<div id="wrapper" class="<?php echo esc_attr($layout_content); ?>">

		<?php if ($page_header_show !== '0') : ?>
			<?php if ($top_bar_enable || $page_top_bar == '1') : ?>
				<div class="felan-top-bar <?php echo join(' ', $topbar_classes); ?>">
					<?php get_template_part('templates/top-bar/top-bar'); ?>
				</div>
			<?php endif; ?>
			<header class="site-header <?php echo join(' ', $header_classes); ?>">
				<?php
				if (defined('ELEMENTOR_VERSION') && Plugin::$instance->db->is_built_with_elementor($header_type)) {
					echo \Elementor\Plugin::$instance->frontend->get_builder_content($header_type);
				} else {
					get_template_part('templates/header/header', $header_type);
				}
				?>

			</header>
		<?php endif; ?>

		<div id="content" class="site-content">