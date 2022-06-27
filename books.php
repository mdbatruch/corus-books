<?php

    /*
    Plugin Name: Books Plugin
    Description: Plug-In test for Corus Entertainment WordPress Developer Role.
    Plugin URI:  
    Author:      Michael Batruch
    Version:     1.0
    License:     GPLv2 or later
    License URI: https://www.gnu.org/licenses/gpl-2.0.txt
    */

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 *  Create Custom Post Type.
 *  
 */

function post_book() {
    $labels = array(
      'name'               => _x( 'Books', 'post type general name' ),
      'singular_name'      => _x( 'Book', 'post type singular name' ),
      'add_new'            => _x( 'Add New', 'book' ),
      'add_new_item'       => __( 'Add New Book' ),
      'edit_item'          => __( 'Edit Book' ),
      'new_item'           => __( 'New Book' ),
      'all_items'          => __( 'All Books' ),
      'view_item'          => __( 'View Book' ),
      'search_items'       => __( 'Search Books' ),
      'not_found'          => __( 'No books found' ),
      'not_found_in_trash' => __( 'No books found in the Trash' ),
      'menu_name'          => 'Books'
    );
    $args = array(
      'labels'        => $labels,
      'description'   => 'Holds our books and product specific data',
      'public'        => true,
      'menu_position' => 5,
      'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
      'has_archive'   => true,
    );
    register_post_type( 'book', $args ); 
  }
  
  add_action( 'init', 'post_book' );

/**
 *
 *  Register Styles.
 *  
 */


function styles() {
    wp_register_style('books', plugins_url( '', __FILE__) . '/css/styles.css');
    wp_enqueue_style( 'books' );
 }

add_action( 'init',  'styles' );

/**
 *
 *  Register Color Picker Styles and Scripts.
 *  
 */

function color_picker_scripts() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( '/js/wp-color-picker-alpha.min.js',  __FILE__ ), array( 'wp-color-picker' ), '', true );
    wp_enqueue_script( 'wp-color-picker-init',  plugins_url( '/js/wp-color-picker-init.js',  __FILE__ ), array( 'wp-color-picker-alpha' ), '', true );
 }

add_action( 'init',  'color_picker_scripts' );

/**
 *
 *  Add Meta fields.
 *  
 */

function register_meta_boxes() {

    add_meta_box(
        'author',
        'Author',
        'author_meta_box_callback',
        'book'
    );

    add_meta_box(
        'background',
        'Background',
        'background_meta_box_callback',
        'book'
    );

}

add_action( 'add_meta_boxes', 'register_meta_boxes' );

/**
 *
 *  Background Colour Meta fields callback.
 *  
 *  @param int $post_id
 */

function background_meta_box_callback( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'background_nonce', 'background_nonce' );

    $value = get_post_meta( $post->ID, '_background', true );

    // output and escape value
    echo '<input type="text" id="background" class="color-picker" data-alpha-enabled="true" data-default-color="rgba(0,0,0,0.85)" name="background" value="' . esc_attr($value) . '"/>';
}

/**
 *
 *  Author Meta fields callback.
 *  
 * @param int $post_id
 */

function author_meta_box_callback( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'author_nonce', 'author_nonce' );

    $value = get_post_meta( $post->ID, '_author', true );

    // output and escape value
    echo '<input type="text" style="width:100%" id="author" name="author" value="' . esc_attr( $value ) . '" />';
}


/**
 * Save Custom Meta Field Data.
 *
 * @param int $post_id
 */

function save_meta_box_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['background_nonce'] ) || ! isset( $_POST['author_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['background_nonce'], 'background_nonce' ) || ! wp_verify_nonce( $_POST['author_nonce'], 'author_nonce' ) ) {
        return;
    }

    // Dont submit if an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // If user is authorized to update
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    }
    else {

        // don't let user update then
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }


    // is there is a value
    if ( ! isset( $_POST['background'] ) || ! isset( $_POST['author'] )) {
        return;
    }

    // Sanitize like requested!
    $background = sanitize_text_field( $_POST['background'] );

    $author = sanitize_text_field( $_POST['author'] );

    // Here you update.
    update_post_meta( $post_id, '_background', $background );

    update_post_meta( $post_id, '_author', $author );
}

add_action( 'save_post', 'save_meta_box_data' );


/**
 * [book] returns a Book.
 * @return string Book HTML Code
*/

add_shortcode( 'book', 'book_shortcode' );

function book_shortcode($atts, $content = null) {

    if(isset($atts['id'])){
    
        $book = shortcode_atts( array(
            'id' => $atts['id'],
        ), $atts );
        
		$single = $atts['id'];
	} else {
		$single = '';
    }

    // echo '<pre>';
    // print_r($atts);

    if(isset($single) && $single !== ''){
        include( "views/single.php" );
    } else {
        include( "views/all.php" );
    }

}