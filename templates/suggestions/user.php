<?php

use \MLA\Commons\Profile;

global $post;

$user = get_userdata( $post->ID );

/**
 * The return value of bp_core_fetch_avatar() can contain badges and other markup.
 * We only want the <img>.
 */
$bp_avatar = bp_core_fetch_avatar( [
	'item_id' => $user->ID,
	'type' => 'thumb',
] );
preg_match( '/<img.*>/', $bp_avatar, $matches );
$avatar_img = $matches[0];

$name = xprofile_get_field_data( Profile::XPROFILE_FIELD_NAME_NAME, $user->ID );
$title = xprofile_get_field_data( Profile::XPROFILE_FIELD_NAME_TITLE, $user->ID );
$affiliation = xprofile_get_field_data( Profile::XPROFILE_FIELD_NAME_INSTITUTIONAL_OR_OTHER_AFFILIATION, $user->ID );

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
		<span class="name"><?php echo $name; ?></span>
		<span class="title"><?php echo $title; ?></span>
		<span class="affiliation"><?php echo $affiliation; ?></span>

		<span class="terms">
			<?php
				foreach ( $common_term_names as $term_name ) {
					printf( '<span class="term">%s</span>', $term_name );
				}
			?>
		</span>

	</span>
</a>
