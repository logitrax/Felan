<?php

namespace Felan_Elementor;

defined('ABSPATH') || exit;

class Widget_Notification extends Base
{

    public function get_name()
    {
        return 'felan-notification';
    }

    public function get_title()
    {
        return esc_html__('Notification', 'felan');
    }

    public function get_icon_part()
    {
        return 'eicon-woocommerce-notices';
    }

    public function get_keywords()
    {
        return ['modern', 'notification'];
    }

    public function get_script_depends()
    {
        return ['felanotification'];
    }

    protected function register_controls()
    {
        $this->add_notification_section();
    }

    private function add_notification_section()
    {
        $this->start_controls_section('notification_section', [
            'label' => esc_html__('Notification', 'felan'),
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        if (!class_exists('Felan_Framework')) {
            return;
        }

        global $current_user;
        $data_notification = felan_get_data_notification();
        if (
            in_array('felan_user_freelancer', (array)$current_user->roles)
            || in_array('felan_user_employer', (array)$current_user->roles)
        ) {
?>
            <div class="felan-notification">
                <?php felan_get_template('dashboard/notification/count.php', array(
                    'data_notification' => $data_notification,
                )); ?>
                <div class="content-noti custom-scrollbar">
                    <?php felan_get_template('dashboard/notification/content.php', array(
                        'data_notification' => $data_notification,
                    )); ?>
                </div>
            </div>
<?php }
    }
}
