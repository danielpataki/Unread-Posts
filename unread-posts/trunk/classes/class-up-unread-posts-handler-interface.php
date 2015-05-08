<?php

/**
 * Unread Posts Handler Interface File
 *
 * This file holds the interface that defines the functions
 * classes that implement it must contain
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

/**
 * Unread Posts Handler Interface
 *
 * The interface makes sure that all classes that implement it create
 * the necessary functions.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

interface UP_Unread_Posts_Handler_Interface {

    /**
     * Set posts as read
     *
     * @param array|int $posts post/posts to set as read
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function set_posts_as_read( $posts );

    /**
     * Sets posts as unread
     *
     * @param array|int $posts post/posts to set as unread
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function set_posts_as_unread( $posts );

    /**
     * Get read posts
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function get_read_posts();

    /**
     * Get unread posts
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function get_unread_posts();

    /**
     * Delete read posts
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function delete_read_posts();
}

?>
