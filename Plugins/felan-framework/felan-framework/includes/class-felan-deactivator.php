<?php

/**
 * Fired during plugin deactivation
 *
 */
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Felan_Deactivator')) {
	require_once FELAN_PLUGIN_DIR . 'includes/admin/class-felan-schedule.php';
	/**
	 * Fired during plugin deactivation
	 * Class Felan_Deactivator
	 */
	class Felan_Deactivator
	{
		/**
		 * Run when plugin deactivated
		 */
		public static function deactivate()
		{
			Felan_Schedule::clear_scheduled_hook();
		}
	}
}
