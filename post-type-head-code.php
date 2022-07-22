<?php 
/*
Plugin Name: Post Type Head Code
Description: It add a metabox to all page and post type to insert code in head section
Author: Simone Manfredini
Author URI: https://trapstudio.it/
Domain Path: /languages/
Text Domain: pt-head-code
Version: 1.0.0
*/
/*  This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

function add_pt_head_code_metabox() {
	//get all post types
    $post_types = get_post_types( array( 'publicly_queryable' => true,'public' => true ) );

    //remove attachment post type
    if( isset( $post_types['attachment'] ) ):
        unset( $post_types['attachment'] );
    endif;

    //add meta box in all page and post type
    $screens = array_merge( array( 'page' ), $post_types );
    foreach ( $screens as $screen ):
        add_meta_box(
            'pt_head_code',
            'Head Snippet',
            'pt_meta_box_callback',
            $screen,
            'normal',
            'default',
        );
    endforeach;
}
add_action( 'add_meta_boxes', 'add_pt_head_code_metabox' );

function pt_meta_box_callback($post) {
    wp_nonce_field( 'eos_dp_setts', 'eos_dp_setts' );
?>

    <div>
		<label for="head_code">Snippet di codice da aggiungere alla sezione head</label>
		<br />
		<textarea name="head_code" id="head_code"><?php //echo esc_attr( get_post_meta( $post->ID, 'head_code', true ) ); ?></textarea>
	</div>

<?php
}
?>