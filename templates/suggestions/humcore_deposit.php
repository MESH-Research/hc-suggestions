<?php

global $post;

printf(
	'<a href="%s" title="%s">%s</a>',
	$post->permalink, // the_permalink() is wrong for fake post types, access directly instead
	the_title_attribute( 'echo=0' ),
	get_the_title()
);

