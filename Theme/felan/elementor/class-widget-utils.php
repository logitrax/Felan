<?php

namespace Felan_Elementor;

defined('ABSPATH') || exit;

class Widget_Utils
{
	public static function get_control_options_horizontal_alignment()
	{
		return [
			'left'   => [
				'title' => esc_html__('Left', 'felan'),
				'icon'  => 'eicon-h-align-left',
			],
			'center' => [
				'title' => esc_html__('Center', 'felan'),
				'icon'  => 'eicon-h-align-center',
			],
			'right'  => [
				'title' => esc_html__('Right', 'felan'),
				'icon'  => 'eicon-h-align-right',
			],
		];
	}

	public static function get_control_options_horizontal_alignment_full()
	{
		return [
			'left'    => [
				'title' => esc_html__('Left', 'felan'),
				'icon'  => 'eicon-h-align-left',
			],
			'center'  => [
				'title' => esc_html__('Center', 'felan'),
				'icon'  => 'eicon-h-align-center',
			],
			'right'   => [
				'title' => esc_html__('Right', 'felan'),
				'icon'  => 'eicon-h-align-right',
			],
			'stretch' => [
				'title' => esc_html__('Stretch', 'felan'),
				'icon'  => 'eicon-h-align-stretch',
			],
		];
	}

	public static function get_control_options_vertical_alignment()
	{
		return [
			'top'    => [
				'title' => esc_html__('Top', 'felan'),
				'icon'  => 'eicon-v-align-top',
			],
			'middle' => [
				'title' => esc_html__('Middle', 'felan'),
				'icon'  => 'eicon-v-align-middle',
			],
			'bottom' => [
				'title' => esc_html__('Bottom', 'felan'),
				'icon'  => 'eicon-v-align-bottom',
			],
		];
	}

	public static function get_control_options_vertical_full_alignment()
	{
		return [
			'top'     => [
				'title' => esc_html__('Top', 'felan'),
				'icon'  => 'eicon-v-align-top',
			],
			'middle'  => [
				'title' => esc_html__('Middle', 'felan'),
				'icon'  => 'eicon-v-align-middle',
			],
			'bottom'  => [
				'title' => esc_html__('Bottom', 'felan'),
				'icon'  => 'eicon-v-align-bottom',
			],
			'stretch' => [
				'title' => esc_html__('Stretch', 'felan'),
				'icon'  => 'eicon-v-align-stretch',
			],
		];
	}

	public static function get_control_options_text_align()
	{
		return [
			'left'   => [
				'title' => esc_html__('Left', 'felan'),
				'icon'  => 'eicon-text-align-left',
			],
			'center' => [
				'title' => esc_html__('Center', 'felan'),
				'icon'  => 'eicon-text-align-center',
			],
			'right'  => [
				'title' => esc_html__('Right', 'felan'),
				'icon'  => 'eicon-text-align-right',
			],
		];
	}

	public static function get_control_options_flex_align()
	{
		return [
			'flex-start'   => [
				'title' => esc_html__('Left', 'felan'),
				'icon'  => 'eicon-text-align-left',
			],
			'center' => [
				'title' => esc_html__('Center', 'felan'),
				'icon'  => 'eicon-text-align-center',
			],
			'flex-end'  => [
				'title' => esc_html__('Right', 'felan'),
				'icon'  => 'eicon-text-align-right',
			],
		];
	}

	public static function get_control_options_text_align_full()
	{
		return [
			'left'    => [
				'title' => esc_html__('Left', 'felan'),
				'icon'  => 'eicon-text-align-left',
			],
			'center'  => [
				'title' => esc_html__('Center', 'felan'),
				'icon'  => 'eicon-text-align-center',
			],
			'right'   => [
				'title' => esc_html__('Right', 'felan'),
				'icon'  => 'eicon-text-align-right',
			],
			'justify' => [
				'title' => esc_html__('Justified', 'felan'),
				'icon'  => 'eicon-text-align-justify',
			],
		];
	}

	public static function get_button_style()
	{
		return [
			'classic' => esc_html__('Classic', 'felan'),
			'outline' => esc_html__('Outline', 'felan'),
			'link'    => esc_html__('Link', 'felan'),
			'border-bottom' => esc_html__('Border Bottom', 'felan'),
		];
	}

	public static function get_button_shape()
	{
		return [
			'rounded' => esc_html__('Rounded', 'felan'),
			'square'  => esc_html__('Square', 'felan'),
			'round'   => esc_html__('Round', 'felan'),
		];
	}

	public static function get_button_size()
	{
		return [
			'xs' => esc_html__('Extra Small', 'felan'),
			'sm' => esc_html__('Small', 'felan'),
			'md' => esc_html__('Medium', 'felan'),
			'lg' => esc_html__('Large', 'felan'),
			'xl' => esc_html__('Extra Large', 'felan'),
		];
	}

	/**
	 * Get recommended social icons for control ICONS.
	 *
	 * @return array
	 */
	public static function get_recommended_social_icons()
	{
		return [
			'fa-brands' => [
				'android',
				'apple',
				'behance',
				'bitbucket',
				'codepen',
				'delicious',
				'deviantart',
				'digg',
				'dribbble',
				'envelope',
				'facebook',
				"facebook-f",
				"facebook-messenger",
				"facebook-square",
				'flickr',
				'foursquare',
				'free-code-camp',
				'github',
				'gitlab',
				'globe',
				'houzz',
				'instagram',
				'jsfiddle',
				'link',
				'linkedin',
				'medium',
				'meetup',
				'mix',
				'mixcloud',
				'odnoklassniki',
				'pinterest',
				'product-hunt',
				'reddit',
				'rss',
				'shopping-cart',
				'skype',
				'slideshare',
				'snapchat',
				'soundcloud',
				'spotify',
				'stack-overflow',
				'steam',
				'telegram',
				'thumb-tack',
				'tripadvisor',
				'tumblr',
				'twitch',
				'twitter',
				'viber',
				'vimeo',
				'vk',
				'weibo',
				'weixin',
				'whatsapp',
				'wordpress',
				'xing',
				'yelp',
				'youtube',
				'500px',
			],
		];
	}

	public static function get_grid_metro_size()
	{
		return [
			'1:1'   => esc_html__('Width 1 - Height 1', 'felan'),
			'1:2'   => esc_html__('Width 1 - Height 2', 'felan'),
			'1:0.7' => esc_html__('Width 1 - Height 70%', 'felan'),
			'1:1.3' => esc_html__('Width 1 - Height 130%', 'felan'),
			'2:1'   => esc_html__('Width 2 - Height 1', 'felan'),
			'2:2'   => esc_html__('Width 2 - Height 2', 'felan'),
		];
	}

    public static function saved_templates()
    {

        $elementor_library = array();

        $args = array(
            'post_type'			=> 'elementor_library',
            'posts_per_page'	=> -1,
        );

        $the_query = new \WP_Query($args);
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $elementor_library[get_the_ID()] = get_the_title();
            }
        }
        wp_reset_postdata();

        return $elementor_library;
    }
}
