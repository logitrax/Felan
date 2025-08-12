<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_details_prints = felan_get_option('freelancer_details_prints');
foreach ($freelancer_details_prints as $print) {
    if (!in_array('enable_print_sp_projects', $freelancer_details_prints)) {
        return;
    }
}

$freelancer_project = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_project_list', false);
$freelancer_project = !empty($freelancer_project) ? $freelancer_project[0] : '';
if (empty($freelancer_project[0][FELAN_METABOX_PREFIX . 'freelancer_project_image_id']['url'])) {
    return;
}
?>
<div class="block-archive-inner freelancer-project-details">
    <h4 class="title-freelancer"><?php esc_html_e('Projects', 'felan-framework') ?></h4>
    <div class="entry-freelancer-element">
        <div class="row">
            <?php
            foreach ($freelancer_project as $project) :
                $thumb_src = $project[FELAN_METABOX_PREFIX . 'freelancer_project_image_id']['url'];
                if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_link'])) {
                    $project_link = $project[FELAN_METABOX_PREFIX . 'freelancer_project_link'];
                } else {
                    $project_link = '#';
                }
            ?>
                <?php if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_image_id']['url'])) : ?>
                    <div class="col-6">
                        <figure>
                            <a href="<?php echo esc_url($project_link); ?>" target="_blank" class="project">
                                <img src="<?php echo esc_url($thumb_src); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
                                <div class="content-project">
                                    <?php if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_title'])) : ?>
                                        <h4><?php echo $project[FELAN_METABOX_PREFIX . 'freelancer_project_title']; ?></h4>
                                    <?php endif; ?>
                                    <div class="project-inner">
                                        <?php if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_description'])) : ?>
                                            <p><?php echo $project[FELAN_METABOX_PREFIX . 'freelancer_project_description']; ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($project[FELAN_METABOX_PREFIX . 'freelancer_project_title'])) : ?>
                                            <span class="felan-button button-border-bottom"><?php esc_html_e('View Portfolio', 'felan-framework') ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </figure>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    $custom_field_freelancer = felan_render_custom_field('freelancer');
    $freelancer_meta_data = get_post_custom($freelancer_id);
    $freelancer_data = get_post($freelancer_id);
    $check_tabs = false;
    foreach ($custom_field_freelancer as $field) {
        if ($field['tabs'] == 'projects') {
            $check_tabs = true;
        }
    }

    if (count($custom_field_freelancer) > 0) {
        if ($check_tabs == true) : ?>
            <?php foreach ($custom_field_freelancer as $field) {
                if ($field['tabs'] == 'projects') { ?>
            <?php felan_get_template("freelancer/print/additional/field.php", array(
                        'field' => $field,
                        'freelancer_data' => $freelancer_data,
                        'freelancer_meta_data' => $freelancer_meta_data
                    ));
                }
            } ?>
    <?php endif;
    }
    ?>
</div>