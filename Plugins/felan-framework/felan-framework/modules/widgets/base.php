<?php
if (!class_exists('Felan_Base_Widget')) {
	class Felan_Base_Widget
	{

		public function __construct()
		{
			add_action('widgets_init', array($this, 'register_widget'), 1);
			$this->includes();
			spl_autoload_register(array($this, 'autoload'));
		}

		public function autoload($class_name)
		{
			$class = preg_replace('/^Felan_Widget_/', '', $class_name);
			if ($class != $class_name) {
				$class = str_replace('_', '-', $class);
				$class = strtolower($class);
				include_once(FELAN_PLUGIN_DIR . 'modules/widgets/includes/' . $class . '.php');
			}
		}

		private function includes()
		{
			include_once(FELAN_PLUGIN_DIR . 'modules/widgets/widget-config.php');
		}

		public function register_widget()
		{
			register_widget('Felan_Widget_Popular_Posts');
		}
	}

	new Felan_Base_Widget();
}
