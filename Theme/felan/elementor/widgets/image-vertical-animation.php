<?php

namespace Felan_Elementor;

use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;

defined('ABSPATH') || exit;

class Widget_Image_Vertical_Animation extends Base
{

    public function get_name()
    {
        return 'felan-image-vertical-animation';
    }

    public function get_title()
    {
        return esc_html__('Image Vertical Animation', 'felan');
    }

    public function get_icon_part()
    {
        return 'eicon-animation';
    }

    public function get_keywords()
    {
        return ['image', 'vertical', 'photo', 'box'];
    }

    public function get_style_depends()
    {
        return ['felan-el-widget-image-vertical-animation'];
    }

    protected function register_controls()
    {
        $this->add_image_animation_section();
    }

    private function add_image_animation_section()
    {
        $this->start_controls_section('image_section', [
            'label' => esc_html__('Image Animation', 'felan'),
        ]);

        $this->add_control('effect', [
            'label'        => esc_html__('Effect', 'felan'),
            'type'         => Controls_Manager::SELECT,
            'options'      => [
                'btt'  => esc_html__('Bottom To Top', 'felan'),
                'ttb' => esc_html__('Top To Bottom', 'felan'),
            ],
            'default' => 'effect-rtl',
        ]);

        $repeater = new Repeater();

        $repeater->add_control('image', [
            'label'   => esc_html__('Image', 'felan'),
            'type'    => Controls_Manager::MEDIA,
            'default' => [
                'url' => Utils::get_placeholder_image_src(),
            ],
        ]);

        $repeater->add_control('link', [
            'label'         => esc_html__('Link', 'felan'),
            'type'          => Controls_Manager::URL,
            'placeholder'   => esc_html__('https://your-link.com', 'felan'),
            'show_external' => true,
            'default'       => [
                'url'         => '',
                'is_external' => true,
                'nofollow'    => true,
            ],
        ]);

        $this->add_control(
            'list',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'image' => Utils::get_placeholder_image_src(),
                    ],
                    [
                        'image' => Utils::get_placeholder_image_src(),
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', [
            'felan-image-vertical-animation',
            $settings['effect']
        ]);
        ?>
        <div <?php $this->print_render_attribute_string('wrapper') ?>>
            <div class="felan-image-item-container">
                <?php $this->print_image_content($settings); ?>
                <?php $this->print_image_content($settings); ?>
            </div>
        </div>
        <?php
    }

    private function print_image_content(array $settings)
    {

        if (!empty($settings['list'])) {
            foreach ($settings['list'] as $item) {
                $item_id = $item['_id'];
                $item_key = 'item_' . $item_id;
                $item_link_key = $item_key . '_link';

                if (!empty($item['link']['url'])) {
                    $this->add_link_attributes($item_link_key, $item['link']);
                }
                ?>
                <div class="felan-image-item">
                    <?php if (!empty($item['link']['url'])) { ?>
                    <a <?php $this->print_render_attribute_string($item_link_key); ?>>
                        <?php } ?>
                        <div class="image">
                            <img src="<?php echo esc_url($item['image']['url']); ?>" alt="">
                        </div>
                        <?php if (!empty($item['link']['url'])) { ?>
                    </a>
                <?php } ?>
                </div>
                <?php
            }
        }
    }
}
