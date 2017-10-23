<?php

global $post;

$group = groups_get_group( $post->ID );

/**
 * The return value of bp_core_fetch_avatar() can contain badges and other markup.
 * We only want the <img>.
 */
$bp_avatar = bp_get_group_avatar( $group->ID );
preg_match( '/<img.*>/', $bp_avatar, $matches );
$avatar_img = $matches[0];

$common_term_names = array_intersect(
	wpmn_get_object_terms( get_current_user_id(), HC_Suggestions_Widget::TAXONOMY, [ 'fields' => 'names' ] ),
	wpmn_get_object_terms( $user->ID, HC_Suggestions_Widget::TAXONOMY, [ 'fields' => 'names' ] )
);

?>

<a href="<?php echo $post->permalink ?>">
	<span class="left">
		<?php echo $avatar_img; ?>
	</span>

	<span class="right">
		<span class="name"><?php echo $group->name; ?></span>
		<span class="description"><?php echo $group->description; ?></span>
	</span>
</a>
