<?php

/**
 * Enqueue script for live preview customizer.
 */
function felan_customizer_live_preview()
{
	wp_enqueue_script('felan-customize-preview', get_template_directory_uri() . '/customizer/assets/js/customize-preview.js', array('jquery', 'customize-preview'), '', true);
}
add_action('customize_preview_init', 'felan_customizer_live_preview');

/**
 * Enqueue script for custom customize control.
 */
function felan_customize_enqueue()
{
	wp_enqueue_style('font-awesome', FELAN_THEME_URI . '/assets/fonts/font-awesome/css/fontawesome-all.min.css', array(), '5.1.0', 'all');

	wp_enqueue_style('felan_customizer', get_template_directory_uri() . '/customizer/assets/css/custom.css', array());
}
add_action('customize_controls_enqueue_scripts', 'felan_customize_enqueue', 10);

/**
 * Register customizer
 */
function felan_customizer_register($wp_customize)
{
	/**
	 * Register controls
	 */
	$wp_customize->get_control('blogname')->section = 'site_identity';
	$wp_customize->get_control('blogdescription')->section = 'site_identity';
	$wp_customize->get_control('site_icon')->section = 'site_identity';

	if (get_pages()) {
		$wp_customize->get_control('show_on_front')->section = 'system';
		$wp_customize->get_control('page_on_front')->section = 'system';
		$wp_customize->get_control('page_for_posts')->section = 'system';
	}

	/**
	 * Remove default sections
	 */
	$wp_customize->remove_section('title_tagline');
	$wp_customize->remove_section('colors');

	/**
	 * The custom control class
	 */
	if (class_exists('Felan_Framework')) {
		class Kirki_Controls_Notice_Control extends Kirki_Control_Base
		{
			public $type = 'notice';
			public function render_content()
			{
?>
				<h3 class="entry-notice"><?php echo esc_html($this->label); ?></h3>
<?php
			}
		}
	}
	// Register our custom control with Kirki
	add_filter('kirki_control_types', function ($controls) {
		$controls['notice'] = 'Kirki_Controls_Notice_Control';
		return $controls;
	});
}
add_action('customize_register', 'felan_customizer_register');

/**
 * Get list sidebars
 * *******************************************************
 */
if (!function_exists('felan_get_sidebars')) {
	function felan_get_sidebars()
	{
		$sidebars = array('default' => '-- Select Sidebar --');
		if (is_array($GLOBALS['wp_registered_sidebars'])) {
			foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
				$sidebars[$sidebar['id']] = ucwords($sidebar['name']);
			}
		}
		return $sidebars;
	}
}

/**
 * Get footer elementor
 * *******************************************************
 */
if (!function_exists('felan_get_footer_elementor')) {
	function felan_get_footer_elementor()
	{
		$footers = get_posts(array(
			'post_type'      => 'felan_footer',
			'posts_per_page' => -1,
		));

		$arr_footer = array('' => esc_html('Default', 'felan'));

		foreach ($footers as $footer) {
			$arr_footer[$footer->ID] = ucwords($footer->post_title);
		}

		return $arr_footer;
	}
}

/**
 * Get header elementor
 * *******************************************************
 */
if (!function_exists('felan_get_header_elementor')) {
	function felan_get_header_elementor($default = true)
	{
		$header = get_posts(array(
			'post_type'      => 'felan_header',
			'posts_per_page' => -1,
		));

		if ($default) {
			$arr_header = array('' => esc_html('Default', 'felan'));
		} else {
			$arr_header = array();
		}
		$arr_header['01'] = esc_html('Header 01', 'felan');

		foreach ($header as $header) {
			$arr_header[$header->ID] = ucwords($header->post_title);
		}

		return $arr_header;
	}
}

/**
 * Get elementor library
 * *******************************************************
 */
if (!function_exists('felan_get_elementor_library')) {
	function felan_get_elementor_library()
	{
		$elementors = get_posts(array(
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
		));

		$arr_elementors = array('' => esc_html('Default', 'felan'));

		foreach ($elementors as $elementor) {
			$arr_elementors[$elementor->ID] = ucwords($elementor->post_title);
		}

		return $arr_elementors;
	}
}

/**
 * Register_block_style
 * *******************************************************
 */
if (function_exists('register_block_style')) {
	function felan_register_block_styles()
	{
		// Columns: Overlap.
		register_block_style(
			'core/columns',
			array(
				'name'  => 'felan-columns-overlap',
				'label' => esc_html__('Overlap', 'felan'),
			)
		);

		// Cover: Borders.
		register_block_style(
			'core/cover',
			array(
				'name'  => 'felan-border',
				'label' => esc_html__('Borders', 'felan'),
			)
		);

		// Group: Borders.
		register_block_style(
			'core/group',
			array(
				'name'  => 'felan-border',
				'label' => esc_html__('Borders', 'felan'),
			)
		);

		// Image: Borders.
		register_block_style(
			'core/image',
			array(
				'name'  => 'felan-border',
				'label' => esc_html__('Borders', 'felan'),
			)
		);

		// Image: Frame.
		register_block_style(
			'core/image',
			array(
				'name'  => 'felan-image-frame',
				'label' => esc_html__('Frame', 'felan'),
			)
		);

		// Latest Posts: Dividers.
		register_block_style(
			'core/latest-posts',
			array(
				'name'  => 'felan-latest-posts-dividers',
				'label' => esc_html__('Dividers', 'felan'),
			)
		);

		// Latest Posts: Borders.
		register_block_style(
			'core/latest-posts',
			array(
				'name'  => 'felan-latest-posts-borders',
				'label' => esc_html__('Borders', 'felan'),
			)
		);

		// Media & Text: Borders.
		register_block_style(
			'core/media-text',
			array(
				'name'  => 'felan-border',
				'label' => esc_html__('Borders', 'felan'),
			)
		);

		// Separator: Thick.
		register_block_style(
			'core/separator',
			array(
				'name'  => 'felan-separator-thick',
				'label' => esc_html__('Thick', 'felan'),
			)
		);

		// Social icons: Dark gray color.
		register_block_style(
			'core/social-links',
			array(
				'name'  => 'felan-social-icons-color',
				'label' => esc_html__('Dark gray', 'felan'),
			)
		);
	}
	add_action('init', 'felan_register_block_styles');
}


/**
 * Register Block Patterns.
 */
if (function_exists('register_block_pattern')) {
	function felan_register_block_pattern()
	{
		// Large Text.
		register_block_pattern(
			'felan/large-text',
			array(
				'title'         => esc_html__('Large text', 'felan'),
				'categories'    => array('felan'),
				'viewportWidth' => 1440,
				'content'       => '<!-- wp:heading {"align":"wide","fontSize":"gigantic","style":{"typography":{"lineHeight":"1.1"}}} --><h2 class="alignwide has-text-align-wide has-gigantic-font-size" style="line-height:1.1">' . esc_html__('A new portfolio default theme for WordPress', 'felan') . '</h2><!-- /wp:heading -->',
			)
		);

		// Links Area.
		register_block_pattern(
			'felan/links-area',
			array(
				'title'         => esc_html__('Links area', 'felan'),
				'categories'    => array('felan'),
				'viewportWidth' => 1440,
				'description'   => esc_html_x('A huge text followed by social networks and email address links.', 'Block pattern description', 'felan'),
				'content'       => '<!-- wp:cover {"overlayColor":"green","contentPosition":"center center","align":"wide","className":"is-style-felan-border"} --><div class="wp-block-cover alignwide has-green-background-color has-background-dim is-style-felan-border"><div class="wp-block-cover__inner-container"><!-- wp:spacer {"height":20} --><div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer --><!-- wp:paragraph {"fontSize":"huge"} --><p class="has-huge-font-size">' . wp_kses_post(__('Let&#8217;s Connect.', 'felan')) . '</p><!-- /wp:paragraph --><!-- wp:spacer {"height":75} --><div style="height:75px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p><a href="#" data-type="URL">' . esc_html__('Twitter', 'felan') . '</a> / <a href="#" data-type="URL">' . esc_html__('Instagram', 'felan') . '</a> / <a href="#" data-type="URL">' . esc_html__('Dribbble', 'felan') . '</a></p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p><a href="#">' . esc_html__('example@example.com', 'felan') . '</a></p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:spacer {"height":20} --><div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer --></div></div><!-- /wp:cover --><!-- wp:paragraph --><p></p><!-- /wp:paragraph -->',
			)
		);

		// Media & Text Article Title.
		register_block_pattern(
			'felan/media-text-article-title',
			array(
				'title'         => esc_html__('Media and text article title', 'felan'),
				'categories'    => array('felan'),
				'viewportWidth' => 1440,
				'description'   => esc_html_x('A Media & Text block with a big image on the left and a heading on the right. The heading is followed by a separator and a description paragraph.', 'Block pattern description', 'felan'),
				'content'       => '<!-- wp:media-text {"mediaId":1752,"mediaLink":"' . esc_url(get_template_directory_uri()) . '/assets/images/playing-in-the-sand.jpg","mediaType":"image","className":"is-style-felan-border"} --><div class="wp-block-media-text alignwide is-stacked-on-mobile is-style-felan-border"><figure class="wp-block-media-text__media"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/playing-in-the-sand.jpg" alt="' . esc_attr__('&#8220;Playing in the Sand&#8221; by Berthe Morisot', 'felan') . '" class="wp-image-1752"/></figure><div class="wp-block-media-text__content"><!-- wp:heading {"align":"center"} --><h2 class="has-text-align-center">' . esc_html__('Playing in the Sand', 'felan') . '</h2><!-- /wp:heading --><!-- wp:separator {"className":"is-style-dots"} --><hr class="wp-block-separator is-style-dots"/><!-- /wp:separator --><!-- wp:paragraph {"align":"center","fontSize":"small"} --><p class="has-text-align-center has-small-font-size">' . wp_kses_post(__('Berthe Morisot<br>(French, 1841-1895)', 'felan')) . '</p><!-- /wp:paragraph --></div></div><!-- /wp:media-text -->',
			)
		);

		// Overlapping Images.
		register_block_pattern(
			'felan/overlapping-images',
			array(
				'title'         => esc_html__('Overlapping images', 'felan'),
				'categories'    => array('felan'),
				'viewportWidth' => 1024,
				'description'   => esc_html_x('Three images inside an overlapping columns block.', 'Block pattern description', 'felan'),
				'content'       => '<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"is-style-felan-columns-overlap"} --><div class="wp-block-columns alignwide are-vertically-aligned-center is-style-felan-columns-overlap"><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"align":"full","sizeSlug":"full"} --><figure class="wp-block-image alignfull size-full"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/roses-tremieres-hollyhocks-1884.jpg" alt="' . esc_attr__('&#8220;Roses Trémières&#8221; by Berthe Morisot', 'felan') . '"/></figure><!-- /wp:image --><!-- wp:spacer --><div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer --><!-- wp:image {"align":"full","sizeSlug":"full"} --><figure class="wp-block-image alignfull size-full"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/in-the-bois-de-boulogne.jpg" alt="' . esc_attr__('&#8220;In the Bois de Boulogne&#8221; by Berthe Morisot', 'felan') . '"/></figure><!-- /wp:image --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center"><!-- wp:spacer --><div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer --><!-- wp:image {"align":"full",sizeSlug":"full"} --><figure class="wp-block-image alignfull size-full"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/young-woman-in-mauve.jpg" alt="' . esc_attr__('&#8220;Young Woman in Mauve&#8221; by Berthe Morisot', 'felan') . '"/></figure><!-- /wp:image --></div><!-- /wp:column --></div><!-- /wp:columns -->',
			)
		);

		// Two Images Showcase.
		register_block_pattern(
			'felan/two-images-showcase',
			array(
				'title'         => esc_html__('Two images showcase', 'felan'),
				'categories'    => array('felan'),
				'viewportWidth' => 1440,
				'description'   => esc_html_x('A media & text block with a big image on the left and a smaller one with bordered frame on the right.', 'Block pattern description', 'felan'),
				'content'       => '<!-- wp:media-text {"mediaId":1747,"mediaLink":"' . esc_url(get_template_directory_uri()) . '/assets/images/Daffodils.jpg","mediaType":"image"} --><div class="wp-block-media-text alignwide is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/Daffodils.jpg" alt="' . esc_attr__('&#8220;Daffodils&#8221; by Berthe Morisot', 'felan') . '" size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:image {"align":"center","width":400,"height":512,"sizeSlug":"large","className":"is-style-felan-image-frame"} --><figure class="wp-block-image aligncenter size-large is-resized is-style-felan-image-frame"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/self-portrait-1885.jpg" alt="' . esc_attr__('&#8220;Self portrait&#8221; by Berthe Morisot', 'felan') . '" width="400" height="512"/></figure><!-- /wp:image --></div></div><!-- /wp:media-text -->',
			)
		);

		// Overlapping Images and Text.
		register_block_pattern(
			'felan/overlapping-images-and-text',
			array(
				'title'         => esc_html__('Overlapping images and text', 'felan'),
				'categories'    => array('felan'),
				'viewportWidth' => 1440,
				'description'   => esc_html_x('An overlapping columns block with two images and a text description.', 'Block pattern description', 'felan'),
				'content'       => '<!-- wp:columns {"verticalAlignment":null,"align":"wide","className":"is-style-felan-columns-overlap"} --> <div class="wp-block-columns alignwide is-style-felan-columns-overlap"><!-- wp:column --> <div class="wp-block-column"><!-- wp:image {sizeSlug":"full"} --> <figure class="wp-block-image size-full"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/the-garden-at-bougival-1884.jpg" alt="' . esc_attr__('&#8220;The Garden at Bougival&#8221; by Berthe Morisot', 'felan') . '"/></figure> <!-- /wp:image --></div> <!-- /wp:column --> <!-- wp:column {"verticalAlignment":"bottom"} --> <div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:group {"className":"is-style-felan-border","backgroundColor":"green"} --> <div class="wp-block-group is-style-felan-border has-green-background-color has-background"><div class="wp-block-group__inner-container"><!-- wp:paragraph {"fontSize":"extra-large","style":{"typography":{"lineHeight":"1.4"}}} --> <p class="has-extra-large-font-size" style="line-height:1.4">' . esc_html__('Beautiful gardens painted by Berthe Morisot in the late 1800s', 'felan') . '</p> <!-- /wp:paragraph --></div></div> <!-- /wp:group --></div> <!-- /wp:column --> <!-- wp:column --> <div class="wp-block-column"><!-- wp:image {sizeSlug":"full"} --> <figure class="wp-block-image size-full"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/villa-with-orange-trees-nice.jpg" alt="' . esc_attr__('&#8220;Villa with Orange Trees, Nice&#8221; by Berthe Morisot', 'felan') . '"/></figure> <!-- /wp:image --></div> <!-- /wp:column --></div> <!-- /wp:columns -->',
			)
		);

		// Portfolio List.
		register_block_pattern(
			'felan/portfolio-list',
			array(
				'title'       => esc_html__('Portfolio list', 'felan'),
				'categories'  => array('felan'),
				'description' => esc_html_x('A list of projects with thumbnail images.', 'Block pattern description', 'felan'),
				'content'     => '<!-- wp:separator {"className":"is-style-felan-separator-thick"} --> <hr class="wp-block-separator is-style-felan-separator-thick"/> <!-- /wp:separator --> <!-- wp:columns --> <div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":80} --> <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:80%"><!-- wp:paragraph {"fontSize":"extra-large"} --> <p class="has-extra-large-font-size"><a href="#">' . esc_html__('Roses Trémières', 'felan') . '</a></p> <!-- /wp:paragraph --></div> <!-- /wp:column --> <!-- wp:column {"verticalAlignment":"center"} --> <div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"align":"right","width":85,"height":67,"sizeSlug":"large"} --> <figure class="wp-block-image alignright size-large is-resized"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/roses-tremieres-hollyhocks-1884.jpg" alt="' . esc_attr__('&#8220;Roses Trémières&#8221; by Berthe Morisot', 'felan') . '" width="85" height="67"/></figure> <!-- /wp:image --></div> <!-- /wp:column --></div> <!-- /wp:columns --> <!-- wp:separator {"className":"is-style-default"} --> <hr class="wp-block-separator is-style-default"/> <!-- /wp:separator --> <!-- wp:columns --> <div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":80} --> <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:80%"><!-- wp:paragraph {"fontSize":"extra-large"} --> <p class="has-extra-large-font-size"><a href="#">' . esc_html__('Villa with Orange Trees, Nice', 'felan') . '</a></p> <!-- /wp:paragraph --></div> <!-- /wp:column --> <!-- wp:column {"verticalAlignment":"center"} --> <div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"align":"right","width":53,"height":67,"className":"alignright size-large is-resized"} --><figure class="wp-block-image is-resized alignright size-large"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/villa-with-orange-trees-nice.jpg" alt="&#8220;Villa with Orange Trees, Nice&#8221; by Berthe Morisot" width="53" height="67"/></figure><!-- /wp:image --></div> <!-- /wp:column --></div> <!-- /wp:columns --> <!-- wp:separator {"className":"is-style-default"} --> <hr class="wp-block-separator is-style-default"/> <!-- /wp:separator --> <!-- wp:columns --> <div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":80} --> <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:80%"><!-- wp:paragraph {"fontSize":"extra-large"} --> <p class="has-extra-large-font-size"><a href="#">' . esc_html__('In the Bois de Boulogne', 'felan') . '</a></p> <!-- /wp:paragraph --></div> <!-- /wp:column --> <!-- wp:column {"verticalAlignment":"center"} --> <div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"align":"right","width":81,"height":67,"sizeSlug":"large"} --> <figure class="wp-block-image alignright size-large is-resized"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/in-the-bois-de-boulogne.jpg" alt="' . esc_attr__('&#8220;In the Bois de Boulogne&#8221; by Berthe Morisot', 'felan') . '" width="81" height="67"/></figure> <!-- /wp:image --></div> <!-- /wp:column --></div> <!-- /wp:columns --> <!-- wp:separator {"className":"is-style-default"} --> <hr class="wp-block-separator is-style-default"/> <!-- /wp:separator --> <!-- wp:columns --> <div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":80} --> <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:80%"><!-- wp:paragraph {"fontSize":"extra-large"} --> <p class="has-extra-large-font-size"><a href="#">' . esc_html__('The Garden at Bougival', 'felan') . '</a></p> <!-- /wp:paragraph --></div> <!-- /wp:column --> <!-- wp:column {"verticalAlignment":"center"} --> <div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"align":"right","width":85,"height":67,"sizeSlug":"large"} --> <figure class="wp-block-image alignright size-large is-resized"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/the-garden-at-bougival-1884.jpg" alt="' . esc_attr__('&#8220;The Garden at Bougival&#8221; by Berthe Morisot', 'felan') . '" width="85" height="67"/></figure> <!-- /wp:image --></div> <!-- /wp:column --></div> <!-- /wp:columns --> <!-- wp:separator {"className":"is-style-default"} --> <hr class="wp-block-separator is-style-default"/> <!-- /wp:separator --> <!-- wp:columns --> <div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":80} --> <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:80%"><!-- wp:paragraph {"fontSize":"extra-large"} --> <p class="has-extra-large-font-size"><a href="#">' . esc_html__('Young Woman in Mauve', 'felan') . '</a></p> <!-- /wp:paragraph --></div> <!-- /wp:column --> <!-- wp:column {"verticalAlignment":"center"} --> <div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"align":"right","width":54,"height":67,"sizeSlug":"large"} --> <figure class="wp-block-image alignright size-large is-resized"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/young-woman-in-mauve.jpg" alt="' . esc_attr__('&#8220;Young Woman in Mauve&#8221; by Berthe Morisot', 'felan') . '" width="54" height="67"/></figure> <!-- /wp:image --></div> <!-- /wp:column --></div> <!-- /wp:columns --> <!-- wp:separator {"className":"is-style-default"} --> <hr class="wp-block-separator is-style-default"/> <!-- /wp:separator --> <!-- wp:columns --> <div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":80} --> <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:80%"><!-- wp:paragraph {"fontSize":"extra-large"} --> <p class="has-extra-large-font-size"><a href="#">' . esc_html__('Reading', 'felan') . '</a></p> <!-- /wp:paragraph --></div> <!-- /wp:column --> <!-- wp:column {"verticalAlignment":"center"} --> <div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"align":"right","width":84,"height":67,"sizeSlug":"large"} --> <figure class="wp-block-image alignright size-large is-resized"><img src="' . esc_url(get_template_directory_uri()) . '/assets/images/Reading.jpg" alt="' . esc_attr__('&#8220;Reading&#8221; by Berthe Morisot', 'felan') . '" width="84" height="67"/></figure> <!-- /wp:image --></div> <!-- /wp:column --></div> <!-- /wp:columns --> <!-- wp:separator {"className":"is-style-felan-separator-thick"} --> <hr class="wp-block-separator is-style-felan-separator-thick"/> <!-- /wp:separator -->',
			)
		);

		register_block_pattern(
			'felan/contact-information',
			array(
				'title'       => esc_html__('Contact information', 'felan'),
				'categories'  => array('felan'),
				'description' => esc_html_x('A block with 3 columns that display contact information and social media links.', 'Block pattern description', 'felan'),
				'content'     => '<!-- wp:columns {"align":"wide"} --><div class="wp-block-columns alignwide"><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p><a href="mailto:#">' . esc_html_x('example@example.com', 'Block pattern sample content', 'felan') . '<br></a>' . esc_html_x('123-456-7890', 'Block pattern sample content', 'felan') . '</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">' . esc_html_x('123 Main Street', 'Block pattern sample content', 'felan') . '<br>' . esc_html_x('Cambridge, MA, 02139', 'Block pattern sample content', 'felan') . '</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center"><!-- wp:social-links {"align":"right","className":"is-style-felan-social-icons-color"} --><ul class="wp-block-social-links alignright is-style-felan-social-icons-color"><!-- wp:social-link {"url":"https://wordpress.org","service":"wordpress"} /--><!-- wp:social-link {"url":"https://www.facebook.com/WordPress/","service":"facebook"} /--><!-- wp:social-link {"url":"https://twitter.com/WordPress","service":"twitter"} /--><!-- wp:social-link {"url":"https://www.youtube.com/wordpress","service":"youtube"} /--></ul><!-- /wp:social-links --></div><!-- /wp:column --></div><!-- /wp:columns -->',
			)
		);
	}
	add_action('init', 'felan_register_block_pattern');
}
