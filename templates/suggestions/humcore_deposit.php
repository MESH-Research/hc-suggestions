<?php

global $post;

/**
 * The return value of bp_core_fetch_avatar() can contain badges and other markup.
 * We only want the <img>.
 */
$bp_avatar = '<img src="/app/plugins/humcore/assets/doc-icon-48x48.png" />';
preg_match( '/<img.*>/', $bp_avatar, $matches );
$avatar_img = $matches[0];

?>

<div class="result">
	<a href="<?php echo $post->permalink ?>">
		<span class="left">
			<?php echo $avatar_img; ?>
		</span>

		<span class="right">
			<span class="name"><?php the_title(); ?></span>
			<span class="description"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></span>
		</span>
	</a>

	<div class="actions">
			<a class="btn" href="<?php echo $post->permalink ?>">View</a>
	</div>
</div>
