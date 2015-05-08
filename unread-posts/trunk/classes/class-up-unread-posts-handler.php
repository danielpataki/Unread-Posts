<?php

/**
 * Unread Posts Handler Base File
 *
 * This file holds the base unread posts handler class
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

/**
 * Unread Posts Handler Base Class
 *
 * The base class for the handlers implements common functionality which
 * is not dependent on the sub-classes.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

class UP_Unread_Posts_Handler {

    function __construct() {
        add_action( 'wp', array( $this, 'add_post_to_read' ) );
    }

    /**
     * Get read posts
     *
     * Retrieves a list of unread posts. It passes all read posts to the
     * post__not_in parameter of the WP_Query. The final query args can be
     * modified by the up/unread_query_args filter.
     *
     * @param array $args WP_Query parameters to use
     * @uses get_read_posts()
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function get_unread_posts( $args = array() ) {
        $defaults = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'post__not_in' => $this->get_read_posts(),
            'fields' => 'ids',
            'posts_per_page' => -1,
            'orderby' => 'DESC'
        );

        $args = wp_parse_args( $args, $defaults );
        $args = apply_filters( 'up/unread_query_args', $args );

        $unread = new WP_Query( $args );

        if( $unread->found_posts == 0 ) {
            return array();
        }

        else {
            return $unread;
        }

    }

    /**
     * Get Read Posts
     *
     * Gets the read posts from the read_posts method
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function get_read_posts() {
        return $this->read_posts;
    }


    /**
     * Add To Read
     *
     * Adds a visited single post to the read posts list
     *
     * @uses set_posts_as_read()
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function add_post_to_read() {
        if( is_singular() ) {
            global $post;
            $this->set_posts_as_read( $post->ID );
        }
    }

}
