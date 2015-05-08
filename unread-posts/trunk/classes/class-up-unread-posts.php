<?php

/**
 * Main Plugin Class File
 *
 * This file holds the main plugin class.
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */

/**
 * UP_Unread_Posts
 *
 * The main plugin class. This creates the widget and does all the other
 * things a plugin usually does
 *
 * @author Daniel Pataki
 * @since 1.0.0
 *
 */


class UP_Unread_Posts {

    /**
     * Unread Posts Handler
     *
     * An instance of an unread post handler object
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    public $handler;


    /**
     * Class Constructor
     *
     * Creates a new cookie handler if one is not
     * passed to it and performs the actions and filters required for the plugin
     * to function.
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function __construct( UP_Unread_Posts_Handler_Interface $handler = null ) {
        $this->handler = $handler ?: new UP_Unread_Posts_Handler_Cookie;

        add_action( 'widgets_init', array( $this, 'widget_init' ) );
        add_action( 'admin_menu', array( $this, 'settings_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
        add_filter( 'the_content', array( $this, 'unread_posts_section' ), 99 );

    }


    /**
     * Backend Assets
     *
     * This function takes care of enqueueing all the assets we need. Right
     * now this consists of the SumoSelect Javascript and CSS and our own
     * backend styles.
     *
     * @link https://github.com/HemantNegi/jquery.sumoselect
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function admin_assets($hook) {
        $plugin_pages = array(
            'settings_page_unread-posts-settings',
            'widgets.php'
        );

        if ( !in_array( $hook, $plugin_pages ) ) {
            return;
        }

        wp_enqueue_script( 'jquery-sumoselect', plugin_dir_url( dirname( __FILE__ ) ) . '/js/jquery.sumoselect.min.js', array('jquery'), '1.0.0', true );

    	wp_enqueue_script( 'unread-posts', plugin_dir_url( dirname( __FILE__ ) ) . '/js/scripts.js', array('jquery-sumoselect'), '1.0.0', true );

    	wp_enqueue_style( 'jquery-sumoselect', plugin_dir_url( dirname( __FILE__ ) ) . '/css/sumoselect.css' );

    	wp_enqueue_style( 'unread-posts', plugin_dir_url( dirname( __FILE__ ) ) . '/css/styles.css' );

        wp_localize_script( 'unread-posts', 'up', array(
    		'post_type_select_placeholder' => __( 'Select post types to include', 'unread-posts' )
    	));

    }


    /**
     * Widget Initializer
     *
     * This function registers the unread posts widget with WordPress
     * The UP_Unread_Posts_Widget class must be included beforehand of course.
     * The widget may be disabled using the up/show_widget filter.
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function widget_init() {
        $show_widget = apply_filters( 'up/show_widget', true );
        if( $show_widget === true ) {
    	       register_widget( 'UP_Unread_Posts_Widget' );
        }
    }

    /**
     * Unread Posts Section
     *
     * This function outputs an unread posts section with a title and a list
     * of posts. It is used to display unread posts under single posts, it is
     * tied to the the_content filter so must return the full content.
     *
     * The settings used to display the list can be modified with the
     * up/posts_section_settings filter, the display itself can be modified
     * with the
     *
     * @param string $content Original content
     * @global object $post The WordPress post object
     * @global object $up_unread_posts The unread posts object
     * @return string The modified content
     * @uses show_unread_section_list()
     * @uses UP_Unread_Posts::get_unread_posts()
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function unread_posts_section( $content ) {

        if( !is_singular() ) {
            return $content;
        }

        $settings = get_option( 'up_below_posts_settings' );
        $settings = ( empty( $settings ) ) ? array() : $settings;
        $defaults = array(
            'show' => 'hide',
            'title' => __( 'Read Next', 'unread-posts' ),
            'count' => 5,
            'show_for' => array( 'post' ),
            'post_type_type' => 'same',
            'post_types' => array( 'post' ),
            'orderby' => 'date'
        );

        $settings = wp_parse_args( $settings, $defaults );
        $settings = apply_filters( 'up/posts_section_settings', $settings );

        global $up_unread_posts, $post;

        if( $settings['show'] != 'show' || ( $settings['show'] == 'show' AND !in_array( $post->post_type, $settings['show_for'] ) )  ) {
            return $content;
        }

        $post_types = ( $settings['post_type_type'] == 'same' ) ? array( $post->post_type ) : $settings['post_types'];

        $query_args = array(
            'posts_per_page' => $settings['count'],
            'post_type' => $post_types,
            'orderby' => $settings['orderby']
        );
        $unread = $up_unread_posts->get_unread_posts( $query_args );

        $output = self::unread_section_display( $unread, $settings );
        $output = apply_filters( 'up/unread_section_display', $output, $unread, $settings );

        echo $content . $output;
    }


    /**
     * Add Setting Page
     *
     * Adds the settings page which contains the settings for the plugin. It
     * can be disabled using the up/show_settings_page filter.
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function settings_page() {
        $show_settings_page = apply_filters( 'up/show_settings_page', true );

        if( $show_settings_page === true ) {
            add_options_page( _x( 'Unread Posts', 'In the title tag of the page', 'unread-posts'  ), _x( 'Unread Posts', 'Menu title',  'unread-posts' ), 'manage_options', 'unread-posts-settings', array( $this, 'settings_page_content' ) );

            add_action( 'admin_init', array( $this, 'register_settings' ) );
        }

    }


    /**
     * Register Settings
     *
     * Registers plugin-wide settings
     *
     * @author Daniel Pataki
     * @since 1.0.0
     *
     */
    function register_settings() {
    	register_setting( 'up_unread_posts_settings', 'up_below_posts_settings' );
    }


    /**
    * Settings Page Content
    *
    * The UI for the settings page.
    *
    * @uses get_usable_post_types()
    * @author Daniel Pataki
    * @since 1.0.0
    *
    */
   function settings_page_content() {
   ?>
   <div class="wrap">
   <h2><?php _e( 'Unread Posts', 'unread-posts' ) ?></h2>

   <form method="post" action="options.php" class='unread-posts'>
       <?php
            settings_fields( 'up_unread_posts_settings' );
            do_settings_sections( 'up_unread_posts_settings' );
            $settings = get_option( 'up_below_posts_settings' );
            $settings['title'] = ( empty( $settings['title'] ) ) ? __( 'Read Next', 'unread-posts' ) : $settings['title'];
            $settings['show'] = ( empty( $settings['show'] ) ) ? '' : $settings['show'];
            $settings['show_for'] = ( empty( $settings['show_for'] ) ) ? array( 'post' ) : $settings['show_for'];
            $settings['count'] = ( empty( $settings['count'] ) ) ? 5 : $settings['count'];
            $settings['post_types'] = ( empty( $settings['post_types'] ) ) ? array('post') : $settings['post_types'];
            $settings['orderby'] = ( empty( $settings['orderby'] ) ) ? 'date' : $settings['orderby'];

            $settings['post_type_type'] = ( empty( $settings['post_type_type'] ) ) ? 'same' : $settings['post_type_type'];

            $post_type_type_options = array(
                'same' => __( 'Same as the current post', 'unread-posts' ),
                'selected' => __( 'Select from a list', 'unread-posts' )
            );

            $orderby_options = array(
                'date' => __( 'Latest Posts', 'unread-posts' ),
                'rand' => __( 'Random Posts', 'unread-posts' ),
            );

            $usable_post_types = self::get_usable_post_types();
        ?>

       <h3><?php _e( 'Unread Posts Below Post Content', 'unread-posts' ) ?></h3>
       <table class="form-table">
           <tr valign="top">
           <th scope="row"><?php _e( 'Show Below Post Content?', 'unread-posts' ) ?></th>
           <td><input type="checkbox" <?php checked( $settings['show'], 'show' ) ?> name="up_below_posts_settings[show]" value="show" /></td>
           </tr>

           <tr valign="top">
           <th scope="row"><?php _e( 'Section Title', 'unread-posts' ) ?></th>
           <td><input type="text" name="up_below_posts_settings[title]" value="<?php echo $settings['title'] ?>" /></td>
           </tr>

           <tr valign="top">
           <th scope="row"><?php _e( 'Posts To Show', 'unread-posts' ) ?></th>
           <td><input type="text" name="up_below_posts_settings[count]" value="<?php echo $settings['count'] ?>" /></td>
           </tr>

           <tr valign="top">
           <th scope="row"><?php _e( 'Show Below Post Types', 'unread-posts' ) ?></th>
           <td>
               <select multiple='multiple' class='post-type-select' name="up_below_posts_settings[show_for][]">
               <?php
               foreach( $usable_post_types as $post_type => $data ) {
                   $selected = ( in_array( $post_type, $settings['show_for'] ) ) ? 'selected="selected"' : '';
                   echo '<option ' . $selected . ' value="' . $post_type . '">' . $data->label . '</option>';
               }
               ?>
               </select>
           </td>
           </tr>


           <tr valign="top">
           <th scope="row"><?php _e( 'Post Types To List', 'unread-posts' ) ?></th>
           <td class='post-type-type-select'>
               <?php
               $i = 0;
               foreach( $post_type_type_options as $value => $name ) {
                   echo "<label class='checkbox-label' for='post_type_type_".$i."'><input id='post_type_type_".$i."' " . checked( $settings['post_type_type'], $value, false ) . " type='radio' name='up_below_posts_settings[post_type_type]' value='" . $value . "'> " . $name . "</label>";
                   $i++;
               }
               ?>

               <?php
                    $hidden = ( $settings['post_type_type'] == 'same' ) ? 'hidden' : '';
               ?>
               <div class='<?php echo $hidden ?> post-type-type-selector'>
               <select multiple='multiple' class='post-type-select' name="up_below_posts_settings[post_types][]">
               <?php
               foreach( $usable_post_types as $post_type => $data ) {
                   $selected = ( in_array( $post_type, $settings['post_types'] ) ) ? 'selected="selected"' : '';
                   echo '<option ' . $selected . ' value="' . $post_type . '">' . $data->label . '</option>';
               }
               ?>
               </select>
            </div>

           </td>
           </tr>


           <tr valign="top">
           <th scope="row"><?php _e( 'Post Ordering', 'unread-posts' ) ?></th>
           <td>
               <select  name="up_below_posts_settings[orderby]">
                   <?php foreach( $orderby_options as $value => $name ) : ?>
                       <option <?php selected( $settings['orderby'], $value ) ?> value='<?php echo $value ?>'><?php echo $name ?></option>
                   <?php endforeach ?>
               </select>
               </td>
           </tr>


       </table>

       <?php submit_button(); ?>

   </form>
   </div>
   <?php
   }

   /**
    * Get Usable Post Types
    *
    * Gets public post types that have proper authors
    *
    * @return array Usable post types
    * @author Daniel Pataki
    * @since 1.0.0
    *
    */
   public static function get_usable_post_types() {
       $post_types = get_post_types( array( 'public' => true ), 'objects' );
       $post_types = apply_filters( 'up/usable_post_types', $post_types );

       return $post_types;
   }

   /**
    * Display Widget List
    *
    * The default view for displaying the post list in the widget. It can be
    * modified with the up/unread_widget_display filter.
    *
    * @param object $unread The unread posts WP_Query object
    * @return string The display output
    * @author Daniel Pataki
    * @since 1.0.0
    *
    */
   public static function unread_widget_list( $unread ) {
       $output = '<ul>';
       while( $unread->have_posts() ) {
           $unread->the_post();
           $output .= '<li><a href="' . get_permalink( get_the_ID() ) . '">' . the_title( '','', false) . '</a></li>';
       }
       $output .= '</ul>';

       return $output;
   }

   /**
    * Display Section List
    *
    * The default view for displaying the unread section list. It can be
    * modified with the up/show_unread_section_list filter.
    *
    * @param object $unread The unread posts WP_Query object
    * @param array $settings The settings for unread sections
    * @return string The display output
    * @author Daniel Pataki
    * @since 1.0.0
    *
    */
   public static function unread_section_display( $unread, $settings ) {

       $output = '<h4>' . $settings['title'] . '</h4> <ul>';
       while( $unread->have_posts() ) {
           $unread->the_post();
           $output .= '<li><a href="' . get_permalink( get_the_ID() ) . '">' . the_title( '','', false) . '</a></li>';
       }
       $output .= '</ul>';

       return $output;

   }

}
