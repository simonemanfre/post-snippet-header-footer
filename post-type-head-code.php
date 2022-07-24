<?php 
/**
 * Plugin Name: Post Snippet - Insert Header e Footer Code in Page, Post and Post Type
 * Version: 1.0.0
 * Requires at least: 4.6
 * Requires PHP: 5.5
 * Tested up to: 6.0
 * Author: Simone Manfredini
 * Author URI: https://trapstudio.it/
 * Description: Insert code snippet and script to header and footer section of any page, post or custom post type.
 * License: GPLv2 or later
 *
 * Text Domain: post-snippet
 * Domain Path: /languages/
 *
 * @package WPCode
 */

/*
	Copyright 2019 Simone Manfredini

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

define( 'POST_SNIPPET_FILE', __FILE__ );

define( 'POST_SNIPPET_PLUGIN_URL', plugin_dir_url( POST_SNIPPET_FILE ) );

//ADMIN STYLE E SCRIPT
function admin_scripts() {
    wp_enqueue_style( 'post-snippet-admin', POST_SNIPPET_PLUGIN_URL . '/assets/css/post-snippet-admin.css', array(), null);
}
add_action( 'admin_enqueue_scripts', 'admin_scripts' );

//ADD METABOX
function add_post_snippet_metabox() {
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
            'post_snippet',
            'Post Snippet',
            'post_snippet_metabox_callback',
            $screen,
            'normal',
            'default',
        );
    endforeach;
}
add_action( 'add_meta_boxes', 'add_post_snippet_metabox' );

function post_snippet_metabox_callback($post) {
    wp_nonce_field( 'post_snippet', 'post_type_snippet' );
?>

    <section class="c-post-snippet">
		<label for="post_snippet_head">Add custom code to the HEAD section</label>
		<textarea name="post_snippet_head" id="post_snippet_head" placeholder="Type code here..."><?php echo esc_attr( get_post_meta( $post->ID, 'post_snippet_head', true ) ); ?></textarea>
	</section>

    <section class="c-post-snippet">
		<label for="post_snippet_footer">Add custom code to the FOOTER section</label>
		<textarea name="post_snippet_footer" id="post_snippet_footer" placeholder="Type code here..."><?php echo esc_attr( get_post_meta( $post->ID, 'post_snippet_footer', true ) ); ?></textarea>
	</section>

<?php
}

//SAVE METABOX
function save_head_snippet_metabox( $post_id, $post ) {

    // Verify the nonce
	if ( ! isset( $_POST['post_type_snippet'] ) || ! wp_verify_nonce( $_POST['post_type_snippet'], 'post_snippet' ) )
        return;

	$post_type = get_post_type_object( $post->post_type );
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return;

    $meta_fields = array('post_snippet_head', 'post_snippet_footer');
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
add_action('wp_head', 'print_post_snippet_head');
function print_post_snippet_head(){

    if(is_singular()):

        $head_snippet = get_post_meta(get_the_ID(), 'post_snippet_head', true);

        if($head_snippet):
            echo $head_snippet;
        endif;
    endif;
}
//PRINT SNIPPET IN FOOTER

add_action('wp_footer', 'print_post_snippet_footer', 20);
function print_post_snippet_footer(){

    if(is_singular()):

        $footer_snippet = get_post_meta(get_the_ID(), 'post_snippet_footer', true);

        if($footer_snippet):
            echo $footer_snippet;
        endif;
    endif;
}
?>