<?php

require_once OCRMF_INCLUDES_DIR . '/functions.php';

add_action( 'init', 'ocrmf_init' );
add_filter( 'map_meta_cap', 'ocrmf_map_meta_cap', 10, 4 );

if (is_admin())
	include OCRMF_ADMIN_DIR . '/admin.php';
else
	include OCRMF_INCLUDES_DIR . '/controller.php';

function ocrmf_init() {
	ocrmf_register_post_types();
	do_action( 'ocrmf_init' );
}

