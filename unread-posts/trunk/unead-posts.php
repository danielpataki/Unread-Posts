<?php
/*
Plugin Name:       Unread Posts
Description:       A plugin that can display unread posts
Version:           1.0.3
Author:            Daniel Pataki
Author URI:        http://danielpataki.com/
License:           GPLv2 or later
*/


include( 'classes/class-up-unread-posts.php' );
include( 'classes/class-up-unread-posts-handler-interface.php' );
include( 'classes/class-up-unread-posts-handler.php' );
include( 'classes/class-up-unread-posts-handler-db.php' );
include( 'classes/class-up-unread-posts-handler-cookie.php' );
include( 'classes/class-up-unread-posts-widget.php' );

add_action( 'plugins_loaded', 'up_initialize_plugin' );
function up_initialize_plugin() {
    global $up_unread_posts;
    $up_unread_posts_handler = ( is_user_logged_in() ) ? "UP_Unread_Posts_Handler_DB" : "UP_Unread_Posts_Handler_Cookie";

    add_filter( 'up/handler', $up_unread_posts_handler );

    $up_unread = new UP_Unread_Posts( new $up_unread_posts_handler );
    $up_unread_posts = $up_unread->handler;
    load_plugin_textdomain( 'unread-posts', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

}
