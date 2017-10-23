<?php

global $post;

$group = groups_get_group( $post->ID );

/**
 * The return value of bp_core_fetch_avatar() can contain badges and other markup.
 * We only want the <img>.
 */
$bp_avatar =  bp_core_fetch_avatar( [
	'item_id'    => $group->id,
	'avatar_dir' => 'group-avatars',
	'object'     => 'group',
] );
preg_match( '/<img.*>/', $bp_avatar, $matches );
$avatar_img = $matches[0];

$bp_join_button = bp_get_group_join_button( $group );
preg_match( '/<a.*\/a>/', $bp_join_button, $matches );
$join_button = $matches[0]; // Only need <a>, no container.
$join_button = preg_replace( '/Join Group/', 'Join', $join_button ); // Replace button text.
$join_button = preg_replace( '/group-button/', 'group-button btn', $join_button ); // Add consistent btn class.

?>

<div class="result">
	<a href="<?php echo $post->permalink ?>">
		<span class="left">
			<?php echo $avatar_img; ?>
		</span>

		<span class="right">
			<span class="name"><?php echo $group->name; ?></span>
			<span class="description"><?php echo wp_trim_words( $group->description, 20 ); ?></span>
		</span>

	</a>

	<div class="actions">
			<a class="btn" href="<?php echo $post->permalink ?>">View</a>
			<?php echo $join_button; ?>
	</div>
</div>
