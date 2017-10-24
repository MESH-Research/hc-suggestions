<?php

global $post;

// CORE deposit icons & download URLs depend on file data set in post meta.
$post_meta = get_post_meta( $post->ID );
$file_metadata = json_decode( $post_meta['_deposit_file_metadata'][0], true );

// CORE icon.
$file_type_data = wp_check_filetype( $file_metadata['files'][0]['filename'], wp_get_mime_types() );
$avatar_img = sprintf( '<img class="deposit-icon" src="%s" alt="%s" />',
	'/app/plugins/humcore/assets/' . esc_attr( $file_type_data['ext'] ) . '-icon-48x48.png',
	esc_attr( $file_type_data['ext'] )
);

// CORE download URL.
$download_url = sprintf( '/deposits/download/%s/%s/%s/',
	$file_metadata['files'][0]['pid'],
	$file_metadata['files'][0]['datastream_id'],
	$file_metadata['files'][0]['filename']
);
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
			<a class="btn" href="<?php echo $download_url ?>">Download</a>
			<?php if ( is_user_logged_in() ) {
				printf(
					'<a class="hide btn" data-post-id="%s" data-post-type="%s" href="#">Hide</a>',
					$post->ID,
					$post->post_type
				);
			} ?>
	</div>
</div>
