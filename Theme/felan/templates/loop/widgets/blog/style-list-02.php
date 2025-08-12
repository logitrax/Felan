<?php
$i = 0;
$sum = $felan_query->post_count;
while ($felan_query->have_posts()) :
	$felan_query->the_post();
	$classes = array('grid-item', 'post-item');
	$i++;
?>
	<?php
	if ($i == 1 && $settings['big_first']) {
		echo '<div class="big-post">';
	} elseif ($i == 2 && $settings['big_first']) {
		echo '<div class="list-post">';
	}
	?>
	<div <?php post_class(implode(' ', $classes)); ?>>
		<div class="post-wrapper felan-box <?php if ($i == 1 && $settings['big_first'] == 1) {
												echo ' post-big';
											} ?>">

			<?php if (has_post_thumbnail()) { ?>
				<div class="post-thumbnail-wrapper">
					<div class="post-feature post-thumbnail felan-image">
						<a href="<?php the_permalink(); ?>">
							<?php
							$size = Felan_Image::elementor_parse_image_size($settings, '540x330');
							Felan_Image::the_post_thumbnail(array('size' => $size));
							?>
						</a>

						<?php if ('yes' === $settings['show_overlay']) : ?>
							<?php get_template_part('templates/loop/blog/overlay', $settings['overlay_style']); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php } ?>

			<?php if ('yes' === $settings['show_caption']) : ?>
				<?php get_template_part('templates/loop/blog/caption', $settings['caption_style']); ?>
			<?php endif; ?>
		</div>
	</div>
	<?php
	if ($i == 1 && $settings['big_first']) {
		echo '</div>';
	} elseif ($i == $sum && $settings['big_first']) {
		echo '</div>';
	}
	?>
<?php endwhile;
