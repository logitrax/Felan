<?php
while ($felan_query->have_posts()) :
	$felan_query->the_post();
	$classes = array('grid-item elementor-grid-item', 'post-item');
    $categories = get_the_category();
    ?>
	<div <?php post_class(implode(' ', $classes)); ?>>
		<div class="post-wrapper felan-box">

			<?php if (has_post_thumbnail()) { ?>
				<div class="post-feature post-thumbnail felan-image">
					<a href="<?php the_permalink(); ?>">
						<?php
						$size = Felan_Image::elementor_parse_image_size($settings, '480x325');
						Felan_Image::the_post_thumbnail(array('size' => $size));
						?>
					</a>

					<?php if ('yes' === $settings['show_overlay']) : ?>
						<?php get_template_part('templates/loop/blog/overlay', $settings['overlay_style']); ?>
					<?php endif; ?>
				</div>
			<?php } ?>

            <div class="content">
                <div class="post-date">
                    <?php if ($categories) : ?>
                        <ul class="post-categories">
                            <?php foreach ($categories as $cat) : ?>
                                <li><a href="<?php echo get_category_link($cat); ?>"><?php echo esc_html($cat->name); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <span><?php esc_html_e(get_the_date('F j, Y')) ?></span>
                </div>
                <div class="entry-title">
                    <?php the_title('<h3 class="post-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h3>'); ?>
                </div>
            </div>

			<?php if ('yes' === $settings['show_caption']) : ?>
				<?php get_template_part('templates/loop/blog/caption', $settings['caption_style']); ?>
			<?php endif; ?>
		</div>
	</div>
<?php endwhile;
