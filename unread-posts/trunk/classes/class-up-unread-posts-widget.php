<?php

/**
 * Widget Class
 *
 * This file holds the class that extends the WP_Widget class,
 * creating the widget.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

/**
 * Unread Posts Widget Class
 *
 * This creates the widget options in the backend and the widget UI
 * in the front-end.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */


class UP_Unread_Posts_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * The widget constructor uses the parent class constructor
     * to add the widget to WordPress, we just provide the basic
     * details
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function __construct() {
        $widget_details = array(
            'classname' => 'up-unread-posts-widget',
            'description' => __( 'A customizable widget that displays unread posts', 'unread-posts' )
        );

        parent::__construct( 'up-unread-posts', __( 'Unread Posts', 'unread-posts' ), $widget_details );

    }

    /**
     * Widget Form
     *
     * The form shown in the admin when building the widget.
     * It contains the options we need to build our widget in the front-end
     *
     * Based on the value of the widget type dropdown, some fields are
     * hidden/shown, this is handled via JS.
     *
     * @param array $instance The widget details
     * @uses UP_Unread_Posts::get_usable_post_types()
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public function form( $instance ) {
        $title = ( !empty( $instance['title'] ) ) ? $instance['title'] : '';
        $orderby = ( !empty( $instance['orderby'] ) ) ? $instance['orderby'] : 'date';
        $count = ( !empty( $instance['count'] ) ) ? $instance['count'] : 5;
        $post_types = ( !empty( $instance['post_types'] ) ) ? $instance['post_types'] : array( 'post' );

        $orderby_options = array(
            'date' => __( 'Latest Posts', 'unread-posts' ),
            'rand' => __( 'Random Posts', 'unread-posts' ),
        );

        ?>

        <div class='unread-posts'>
            <p>
                <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'unread-posts' ) ?> </label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_name( 'count' ); ?>"><?php _e( 'Posts To Show:', 'unread-posts' ) ?> </label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
            </p>

            <p>
                <?php $usable_post_types = UP_Unread_Posts::get_usable_post_types(); ?>
                <label for="<?php echo $this->get_field_name( 'post_types' ); ?>"><?php _e( 'Post Types:', 'unread-posts' ) ?> </label>
                <select multiple='multiple' class='post-type-select' id="<?php echo $this->get_field_id( 'post_types' ); ?>" name="<?php echo $this->get_field_name( 'post_types' ); ?>[]">
                <?php
                foreach( $usable_post_types as $post_type => $data ) {
                    $selected = ( in_array( $post_type, $post_types ) ) ? 'selected="selected"' : '';
                    echo '<option ' . $selected . ' value="' . $post_type . '">' . $data->label . '</option>';
                }
                ?>
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_name( 'orderby' ); ?>"><?php _e( 'Post Ordering:', 'unread-posts' ) ?> </label>
                <select class='widefat'  id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
                    <?php foreach( $orderby_options as $value => $name ) : ?>
                        <option <?php selected( $orderby, $value ) ?> value='<?php echo $value ?>'><?php echo $name ?></option>
                    <?php endforeach ?>
                </select>
            </p>

        </div>
        <?php
    }


    /**
     * Front End Output
     *
     * Handles the visitor-facing side of the widget. It displays a list
     * of posts to the user. The display may be modified with the
     * up/unread_widget_display filter.
     *
     * @global object $up_unread_posts The unread posts object
     * @uses $up_unread_posts->get_unread_posts()
     * @uses UP_Unread_Posts::show_unread_widget_list()
     * @param array $args The widget area details
     * @param array $instance The widget details
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
     public function widget( $args, $instance ) {

         global $up_unread_posts, $post;
         $main_post = $post;

         $query_args = array(
             'posts_per_page' => $instance['count'],
             'orderby' => $instance['orderby'],
             'fields' => 'all',
             'post_type' => $instance['post_types']
         );

         $unread = $up_unread_posts->get_unread_posts( $query_args );

         if( $unread->found_posts == 0 ) {
             return;
         }

         echo $args['before_widget'];

         if( !empty( $instance['title'] ) ) {
             echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
         }

         $display = UP_Unread_Posts::unread_widget_list( $unread );
         echo apply_filters( 'up/unread_widget_display', $display, $unread, $instance );

         echo $args['after_widget'];
         $post = $main_post;

     }



}
