<?php
/*
Plugin Name: wpSUBpages Redirect
Text Domain: redirect-page
Plugin URI: http://codus.mho.org.in/wordpress-mu/wp-plugin-redirect-to-subpage/
Description: redirect to page does, what the name says and a little bit more. it redirects pages to pages, subpages and external uris.
Author: mho->codus()
Author URI: http://codus.mho.org.in
Version: 1.0.4
License: GPL
*/

/**
License:
==============================================================================
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
=========================================== Copyright 2011 codus@mho.org.in ==
*/

if (!defined('R2S_PLUGIN_URL')) define('R2S_PLUGIN_URL', plugin_dir_url( __FILE__ ));

add_action ('get_header','mho_wpsubpages_redirect');
add_action( 'admin_init','mho_wpsubpages_add_custom_box', 5 );
add_action( 'save_post', 'mho_wpsubpages_save_postdata' );

function mho_wpsubpages_redirect()
	{
	global $wp,$post;
	$r2s_value = get_post_meta( $post->ID , 'redirect2subpage_id', true);
	$r2s_type  = get_post_meta( $post->ID , 'redirect2subpage_type', true);
	if ( $r2s_value && $r2s_type ) :
		switch ($r2s_type) :
			case 'r2s_type_page'	:	wp_redirect(get_permalink($r2s_value)); break;
			case 'r2s_type_subpage'	:	wp_redirect(get_permalink($r2s_value)); break;
			case 'r2s_type_url'	:	wp_redirect($r2s_value); break;
		endswitch;
	endif;
	}

function mho_wpsubpages_add_custom_box() 
	{
	add_meta_box(
		'mho_wpsubpages_sectionid',
		__( 'Redirect Page', 'mho_wpsubpages_textdomain' ), 
		'mho_wpsubpages_inner_custom_box',
		'page',
		'side');
	wp_register_script('redirect2subpages.js', R2S_PLUGIN_URL . 'redirect2subpages.js', array('jquery'));
	wp_enqueue_script('redirect2subpages.js');
	wp_register_style('redirect2subpages.css', R2S_PLUGIN_URL . 'redirect2subpages.css');
	wp_enqueue_style('redirect2subpages.css');
	}

function mho_wpsubpages_inner_custom_box ( $post ) 
	{
	wp_nonce_field( plugin_basename( __FILE__ ), 'mho_wpsubpages_noncename' );
	$redirect2subpage_id 	= get_post_meta($post->ID, 'redirect2subpage_id', true);
	$redirect2subpage_type 	= get_post_meta($post->ID, 'redirect2subpage_type', true);
	echo "\n";
	echo "\t" . '<p><input type="checkbox" id="mho_wpsubpages_redirect" name="mho_wpsubpages_redirect" value="1" ' . ( ($redirect2subpage_id) ? 'checked="checked" ' : '' ) . '/>';
	echo '<label for="mho_wpsubpages_redirect">' . __('Activate') . '</label><br /></p>' ."\n";
	echo "\t" . '<div id="r2s_container">' ."\n";
	if (function_exists('wp_dropdown_pages') && ($r2spagemenu = wp_dropdown_pages( 'name=r2s_pagemenu&echo=0&child_of=0&exclude=' . $post->ID . '&selected=' . $redirect2subpage_id))) :
		echo "\t" . __('Page') .'<input type="radio" name="r2s_type" value="r2s_type_page" ' . (( $redirect2subpage_type == 'r2s_type_page' ) ? 'checked=checked' : '') . '/> ' ."\n";
	endif;
	if (function_exists('wp_dropdown_pages') && ($r2ssubpagemenu = wp_dropdown_pages( 'name=r2s_subpagemenu&echo=0&child_of=' .$post->ID . '&selected=' . $redirect2subpage_id))) :
		echo "\t" . __('Sub Page') . ' <input type="radio" name="r2s_type" value="r2s_type_subpage" ' . (( $redirect2subpage_type == 'r2s_type_subpage' ) ? 'checked=checked' : '') . '/>' ."\n";
	endif;
	echo "\t" . __('URL') . '<input type="radio" name="r2s_type" value="r2s_type_url" ' . (( $redirect2subpage_type == 'r2s_type_url' ) ? 'checked=checked' : '') . '/>' ."\n";
	echo "\t" . '<div id="r2s_type_page" class="r2s_subcontainer">' ."\n";	
	if ( $r2spagemenu ) echo $r2spagemenu;
	echo "\t" . '</div>' ."\n";
	echo "\t" . '<div id="r2s_type_subpage" class="r2s_subcontainer">' ."\n";
	if ( $r2ssubpagemenu ) echo $r2ssubpagemenu;
	echo "\t" . '</div>' ."\n";
	echo "\t" . '<div id="r2s_type_url" class="r2s_subcontainer">' ."\n";
	echo "\t" . '<label>URL:</label><input type="text" id="r2s_pageurl" name="r2s_pageurl" value="' . (( $redirect2subpage_type == 'r2s_type_url' ) ? attribute_escape($redirect2subpage_id) : '') . '" />' . "\n";
	echo "\t" . '<p>' . __('Dont forget the host prefix: <strong>http:// | ftp:// </strong>') . '</p>' . "\n";
	echo "\t" . '</div>' ."\n";	
	echo "\t" . '</div>' ."\n";
	}

function mho_wpsubpages_save_postdata ( $post_id ) 
	{
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( !wp_verify_nonce( $_POST['mho_wpsubpages_noncename'], plugin_basename( __FILE__ ) ) ) return;
	if ( !current_user_can( 'edit_page', $post_id ) ) return;
	
	if ( $_POST['mho_wpsubpages_redirect'] == '1' ) :
		switch ($_POST['r2s_type']) :
			case 'r2s_type_page'	:	$r2s_value = $_POST['r2s_pagemenu']; break;
			case 'r2s_type_subpage'	:	$r2s_value = $_POST['r2s_subpagemenu']; break;
			case 'r2s_type_url'	:	$r2s_value = $_POST['r2s_pageurl']; break;
		endswitch;
		update_post_meta( $post_id ,  'redirect2subpage_id' , $r2s_value );
		update_post_meta( $post_id ,  'redirect2subpage_type' , $_POST['r2s_type'] );
	else :
		delete_post_meta( $post_id, 'redirect2subpage_id' );
		delete_post_meta( $post_id, 'redirect2subpage_type' );
	endif;
	return $mydata;
	}

?>
