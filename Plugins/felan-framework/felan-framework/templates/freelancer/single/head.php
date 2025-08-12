<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$freelancer_id     = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$freelancer_yoe             = get_the_terms($freelancer_id, 'freelancer_yoe');
$freelancer_gender          = get_the_terms($freelancer_id, 'freelancer_gender');
$freelancer_qualification   = get_the_terms($freelancer_id, 'freelancer_qualification');
$freelancer_ages            = get_the_terms($freelancer_id, 'freelancer_ages');
?>
<div class="block-archive-inner freelancer-head-details">
    <?php if (is_array($freelancer_yoe)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Experience time', 'felan-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($freelancer_yoe as $yoe) {
                    $yoe_link = get_term_link($yoe, 'freelancer_yoe'); ?>
                    <a href="<?php echo esc_url($yoe_link); ?>">
                        <?php esc_attr_e($yoe->name); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (is_array($freelancer_qualification)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Qualification', 'felan-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($freelancer_qualification as $qualification) {
                    echo '<span>' . esc_attr($qualification->name) . '</span>';
                } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($freelancer_gender)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Gender', 'felan-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($freelancer_gender as $gender) {
                    echo esc_attr_e($gender->name);
                } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (is_array($freelancer_ages)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Age', 'felan-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($freelancer_ages as $ages) {
                    echo esc_attr_e($ages->name);
                } ?>
            </div>
        </div>
    <?php endif; ?>
</div>