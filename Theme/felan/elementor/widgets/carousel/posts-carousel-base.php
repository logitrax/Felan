<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

abstract class Posts_Carousel_Base extends Carousel_Base
{

	/**
	 * @var \WP_Query
	 */
	private $_query      = null;
	private $_query_args = null;

	abstract protected function get_post_type();

	public function query_posts()
	{
		$settings          = $this->get_settings_for_display();
		$post_type         = $this->get_post_type();
		$this->_query      = Module_Query_Base::instance()->get_query($settings, $post_type);
		$this->_query_args = Module_Query_Base::instance()->get_query_args();
	}

	protected function get_query()
	{
		return $this->_query;
	}

	protected function get_query_args()
	{
		return $this->_query_args;
	}

	abstract protected function print_slide(array $settings);

	protected function register_controls()
	{
		parent::register_controls();

		$this->register_query_section();
	}

	protected function get_query_orderby_options()
	{
		$options = [
			'date'           => esc_html__('Date', 'felan'),
			'ID'             => esc_html__('Post ID', 'felan'),
			'author'         => esc_html__('Author', 'felan'),
			'title'          => esc_html__('Title', 'felan'),
			'modified'       => esc_html__('Last modified date', 'felan'),
			'parent'         => esc_html__('Post/page parent ID', 'felan'),
			'comment_count'  => esc_html__('Number of comments', 'felan'),
			'menu_order'     => esc_html__('Menu order/Page Order', 'felan'),
			'meta_value'     => esc_html__('Meta value', 'felan'),
			'meta_value_num' => esc_html__('Meta value number', 'felan'),
			'rand'           => esc_html__('Random order', 'felan'),
		];

		return $options;
	}

	protected function register_query_section()
	{
		$this->start_controls_section('query_section', [
			'label' => esc_html__('Query', 'felan'),
		]);

		$this->add_control('query_source', [
			'label'   => esc_html__('Source', 'felan'),
			'type'    => Controls_Manager::SELECT,
			'options' => array(
				'custom_query'  => esc_html__('Custom Query', 'felan'),
				'current_query' => esc_html__('Current Query', 'felan'),
			),
			'default' => 'custom_query',
		]);

		$this->start_controls_tabs('query_args_tabs', [
			'condition' => [
				'query_source!' => ['current_query'],
			],
		]);

		$this->start_controls_tab('query_include_tab', [
			'label' => esc_html__('Include', 'felan'),
		]);

		$this->add_control('query_include', [
			'label'       => esc_html__('Include By', 'felan'),
			'label_block' => true,
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'options'     => [
				'terms'   => esc_html__('Term', 'felan'),
				'authors' => esc_html__('Author', 'felan'),
			],
			'condition'   => [
				'query_source!' => ['current_query'],
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('query_exclude_tab', [
			'label' => esc_html__('Exclude', 'felan'),
		]);

		$this->add_control('query_exclude', [
			'label'       => esc_html__('Exclude By', 'felan'),
			'label_block' => true,
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'options'     => [
				'terms'   => esc_html__('Term', 'felan'),
				'authors' => esc_html__('Author', 'felan'),
			],
			'condition'   => [
				'query_source!' => ['current_query'],
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control('query_number', [
			'label'       => esc_html__('Items per page', 'felan'),
			'description' => esc_html__('Number of items to show per page. Input "-1" to show all posts. Leave blank to use global setting.', 'felan'),
			'type'        => Controls_Manager::NUMBER,
			'min'         => -1,
			'max'         => 100,
			'step'        => 1,
			'condition'   => [
				'query_source!' => ['current_query'],
			],
			'separator'   => 'before',
		]);

		$this->add_control('query_orderby', [
			'label'       => esc_html__('Order by', 'felan'),
			'description' => esc_html__('Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'felan'),
			'type'        => Controls_Manager::SELECT,
			'options'     => $this->get_query_orderby_options(),
			'default'     => 'date',
			'condition'   => [
				'query_source!' => ['current_query'],
			],
		]);

		$this->add_control('query_sort_meta_key', [
			'label'     => esc_html__('Meta key', 'felan'),
			'type'      => Controls_Manager::TEXT,
			'condition' => [
				'query_orderby' => [
					'meta_value',
					'meta_value_num',
				],
				'query_source!' => ['current_query'],
			],
		]);

		$this->add_control('query_order', [
			'label'     => esc_html__('Sort order', 'felan'),
			'type'      => Controls_Manager::SELECT,
			'options'   => array(
				'DESC' => esc_html__('Descending', 'felan'),
				'ASC'  => esc_html__('Ascending', 'felan'),
			),
			'default'   => 'DESC',
			'condition' => [
				'query_source!' => ['current_query'],
			],
		]);

		$this->end_controls_section();
	}

	protected function print_slides(array $settings)
	{
		$settings = $this->get_settings_for_display();
		$this->query_posts();
		/**
		 * @var $query \WP_Query
		 */
		$query = $this->get_query();
?>
		<?php if ($query->have_posts()) : ?>

			<?php $this->before_loop(); ?>

			<?php while ($query->have_posts()) : $query->the_post(); ?>
				<?php $this->print_slide($settings); ?>
			<?php endwhile; ?>

			<?php $this->after_loop(); ?>

		<?php endif;
		wp_reset_postdata();
	}

	protected function before_loop() {}

	protected function after_loop() {}
}
