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

//ADD METABOX
function add_head_snippet_metabox() {
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
            'head_snippet',
            'Head Snippet',
            'head_snippet_metabox_callback',
            $screen,
            'normal',
            'default',
        );
    endforeach;
}
add_action( 'add_meta_boxes', 'add_head_snippet_metabox' );

function head_snippet_metabox_callback($post) {
    wp_nonce_field( 'head_snippet', 'post_type_head_snippet' );
?>

    <div>
		<label for="head_code">Add custom code to HEAD section</label>
		<br />
		<textarea name="head_code" id="head_code"><?php echo esc_attr( get_post_meta( $post->ID, 'head_code', true ) ); ?></textarea>
	</div>

<?php
}

//SAVE METABOX
function save_head_snippet_metabox( $post_id, $post ) {

    // Verify the nonce
	if ( ! isset( $_POST['post_type_head_snippet'] ) || ! wp_verify_nonce( $_POST['post_type_head_snippet'], 'head_snippet' ) )
        return;

	$post_type = get_post_type_object( $post->post_type );
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return;

    $meta_fields = array('head_code');
    foreach ($meta_fields as $field):
        $new_meta_value = ( isset(  $_POST[$field] ) ?  $_POST[$field] : '' );

        $meta_value = get_post_meta( $post_id, $field, true );

        if ( $new_meta_value && '' == $meta_value ):

            add_post_meta( $post_id, $field, $new_meta_value, true );

        elseif ( $new_meta_value && $new_meta_value != $meta_value ):

            update_post_meta( $post_id, $field, $new_meta_value );

        elseif ( '' == $new_meta_value && $meta_value ):

            delete_post_meta( $post_id, $field, $meta_value );

        endif;

    endforeach;
}
add_action( 'save_post', 'save_head_snippet_metabox', 10, 2 );


//PRINT SNIPPET IN HEAD
add_action('wp_head', 'print_head_snippet');
function print_head_snippet(){

    if(is_singular()):

        $head_snippet = get_post_meta(get_the_ID(), 'head_code', true);

        if($head_snippet):
            echo "<script>{$head_snippet}</script>";
        endif;
    endif;
}
?>