<?php

/**
 * Unread Posts Cookie Handler File
 *
 * This file holds the class responsible for managing unread posts for logged
 * out users using cookies.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

/**
 * Unread Posts Cookie Handler
 *
 * This handler uses cookies to record post views for logged out users
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

class UP_Unread_Posts_Handler_Cookie extends UP_Unread_Posts_Handler implements UP_Unread_Posts_Handler_Interface {

    /**
     * Cookie Name
     *
     * The name of the cookie used to store the read posts
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    var $cookie_name;

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
     * Sets the name of the cookie and the read posts
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function __construct() {
        parent::__construct();
        $this->set_cookie_name();
        $this->set_read_posts();
    }

    /**
     * Set Cookie Name
     *
     * Sets the name of the cookie. By default this is [website-title]-up_read_posts but
     * it can be modified by the up/cookie_name filter
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function set_cookie_name() {
        $cookie_name = sanitize_title_with_dashes( get_bloginfo('title') . '-up_read_posts' );
        $cookie_name = apply_filters( 'up/cookie_name', $cookie_name );
        $this->cookie_name = $cookie_name;
    }

    /**
     * Set Read Posts
     *
     * Sets the read posts by getting the value from the cookie.
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function set_read_posts() {
        if( !empty( $_COOKIE[$this->cookie_name] ) ) {
            $this->read_posts = explode( ', ', gzuncompress( $_COOKIE[$this->cookie_name] ) );
        }
        else {
            $this->read_posts = array();
        }
    }

    /**
     * Set Posts As Read
     *
     * Sets one or more posts as read by resetting the cookie
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

        if( !is_array( $posts ) ) {
            return;
        }

        // If the articles are already in the read list, don't do anything
        if( array_intersect( $this->read_posts, $posts ) == $posts ) {
            return ;
        }

        $this->read_posts = array_unique( array_merge( $this->read_posts, $posts ) );
        $cookie_value = gzcompress( implode( ',', $this->read_posts ) );

        setcookie( $this->cookie_name, $cookie_value, time() + 3600 * 24 * 365 * 10 , COOKIEPATH, COOKIE_DOMAIN, false );
    }

    /**
     * Set Posts As Unread
     *
     * Sets one or more posts as unread by resetting the cookie
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
        $cookie_value = gzcompress( implode( ',', $this->read_posts ) );

        setcookie( $this->cookie_name, $cookie_value, time() + 3600 * 24 * 365 * 10 , COOKIEPATH, COOKIE_DOMAIN, false );

    }


    /**
     * Delete Read Posts
     *
     * Deletes all read posts by deleting the cookie
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function delete_read_posts() {
        if( isset( $_COOKIE[$this->cookie_name] ) ) {
            unset( $_COOKIE[$this->cookie_name] );
            setcookie( $this->cookie_name, null, -1, '/' );
        }
    }

}
