<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $project_data, $hide_project_fields;
$project_faq = get_post_meta($project_data->ID, FELAN_METABOX_PREFIX . 'project_tab_faq', false);
$project_faq = !empty($project_faq) ? $project_faq[0] : '';
?>
<?php if (!in_array('fields_project_faq', $hide_project_fields)) : ?>
    <div class="form-group col-md-12">
        <div class="addons-info block-from">
            <h6><?php esc_html_e('FAQs', 'felan-framework') ?></h6>
            <div class="felan-addons-warpper">
                <?php if (!empty($project_faq)) :
                    foreach ($project_faq as $index => $faqs) : ?>
                        <div class="row">
                            <div class="group-title col-md-12">
                                <i class="delete-group fas fa-times"></i>
                                <h6 class="education">
                                    <?php echo esc_html_e('Faq', 'felan-framework') ?>
                                    <span><?php echo $index + 1 ?></span>
                                </h6>
                                <i class="far fa-angle-up"></i>
                            </div>
                            <div class="form-group col-md-12">
                                <label><?php esc_html_e('Question', 'felan-framework') ?></label>
                                <input type="text" name="project_faq_title[]" placeholder="<?php esc_attr_e('Enter Question', 'felan-framework'); ?>" value="<?php echo $faqs['felan-project_faq_title'] ?>">
                            </div>
                            <div class="form-group col-md-12">
                                <label><?php esc_html_e('Answer', 'felan-framework') ?></label>
                                <textarea name="project_faq_description[]" cols="30" placeholder="<?php esc_attr_e('Enter Answer', 'felan-framework'); ?>" rows="7"><?php echo $faqs['felan-project_faq_description'] ?></textarea>
                            </div>
                        </div>
                <?php endforeach;
                endif;
                ?>

                <button type="button" class="btn-more project-fields"><i class="far fa-angle-down"></i><?php esc_html_e('Add more', 'felan-framework') ?></button>
                <template id="template-project-addons">
                    <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="education">
                                <?php echo esc_html_e('Faq', 'felan-framework') ?>
                                <span>1</span>
                            </h6>
                            <i class="far fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Question', 'felan-framework') ?></label>
                            <input type="text" name="project_faq_title[]" placeholder="<?php esc_attr_e('Enter Question', 'felan-framework'); ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Answer', 'felan-framework') ?></label>
                            <textarea name="project_faq_description[]" cols="30" placeholder="<?php esc_attr_e('Enter Answer', 'felan-framework'); ?>" rows="7"></textarea>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
<?php endif; ?>