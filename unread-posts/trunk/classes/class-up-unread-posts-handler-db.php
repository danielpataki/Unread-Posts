<?php

/**
 * Unread Posts Database Handler File
 *
 * This file holds the class responsible for managing unread posts for logged
 * in users using the usermeta fields.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

/**
 * Unread Posts Database Handler
 *
 * This handler uses the usermeta fields to record post views for logged in
 * users.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */


class UP_Unread_Posts_Handler_DB extends UP_Unread_Posts_Handler implements UP_Unread_Posts_Handler_Interface {

    /**
     * Meta Field
     *
     * The name of the meta field used to store the read posts
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public $meta_field;

    /**
     * User ID
     *
     * ID of the currenlty logged in user
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public $user_id;

    /**
     * Read Posts
     *
     * The list of read posts
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    protected $read_posts;


    /**
     * Class Constructor
     *
     * Sets the name of the meta field, the user ID and the read posts
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function __construct() {
        parent::__construct();
        $this->set_meta_field();
        $this->set_user_id();
        $this->set_read_posts();
    }

    /**
     * Set Meta Field
     *
     * Sets the name of the meta field. By default this is up_read_posts but
     * it can be modified by the up/meta_field filter
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
     protected function set_meta_field() {
        $this->meta_field = apply_filters( 'up/meta_field', 'up_read_posts' );
    }

    /**
     * Set User ID
     *
     * Sets the ID of the currently logged in user
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
     protected function set_user_id() {
        $current_user = wp_get_current_user();
        $this->user_id = $current_user->ID;
    }

    /**
     * Set Read Posts
     *
     * Sets the read posts by getting the value from the user meta field.
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
     protected function set_read_posts() {
        $read_posts = get_user_meta( $this->user_id, $this->meta_field, true );
        $read_posts = ( empty( $read_posts ) ) ? array() : $read_posts;
        $this->read_posts = $read_posts;
    }

    /**
     * Set Posts As Read
     *
     * Sets one or more posts as read by updating the user meta field
     *
     * @param int|array $posts The post/posts to mark as read
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function set_posts_as_read( $posts ) {
        if( is_numeric( $posts ) ) {
            $posts = array( $posts );
        }

        // If the articles are already in the read list, don't do anything
        if( array_intersect( $this->read_posts, $posts ) == $posts ) {
            return ;
        }

        $this->read_posts = array_unique( array_merge( $this->read_posts, $posts ) );

        update_user_meta( $this->user_id, $this->meta_field, $this->read_posts );

    }

    /**
     * Set Posts As Unread
     *
     * Sets one or more posts as unread by updating the user meta field
     *
     * @param int|array $posts The post/posts to mark as unread
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function set_posts_as_unread( $posts ) {
        if( is_numeric( $posts ) ) {
            $posts = array( $posts );
        }

        $this->read_posts = array_diff( $this->read_posts, $posts );

        update_user_meta( $this->user_id, $this->meta_field, $this->read_posts );

    }

    /**
     * Delete Read Posts
     *
     * Deletes all read posts by deleting the usermeta field altogether
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function delete_read_posts() {
        delete_user_meta( $this->user_id, $this->meta_field );
    }

}

?>
