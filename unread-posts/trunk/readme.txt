=== Unread Posts ===
Contributors: danielpataki
Tags: posts, related, widget
Requires at least: 3.5.0
Tested up to: 4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add flexible unread post lists to your sidebar or under single posts. Easily extendable for developers wanting to leverage it.

== Description ==

Unread posts is a plugin for regular users and developers alike. For regular users it allows for the creation of widgets that display posts the user has not yet read. It will work for logged in and logged out users as well. You can also add unread posts just below your single post content.

For logged out users the plugin uses cookies so please make sure your users consent to cookies by using a plugin like [Cookie Law Info](https://wordpress.org/plugins/cookie-law-info/screenshots/).

The plugin contains a bunch of developer friendly features so you can easily leverage the core functionality, the management of read/unread posts. The class may be used alone, you can use some built in functions and I've added a bunch of filters for you to modify everything from what shows up in lists to how the lists are actually displayed.

For more information on developer features take a look at the Other Notes section.

= Usage =

The plugin offers a widget and an optional section below posts. The widget can be added in Appearance->Widgets and has a number of options. Hopefully these are all self explanatory, let me know if you have any questions.

There is also a settings section under Settings->Unread Posts. This page contains settings for the unread posts section to be placed under posts. You can enable this section by ticking the "Show Below Post Content?" box. The options are much the same as the widgets but you have a bit more control over when and what is shown.

First of all, use "Show Below Post Types" setting to set where this section shows up at all. The unread posts section will only show up under posts with the set post type(s).

The "Post Types To List" section allows you to control which post types are shown in the list. If "Same as the current post" is selected the post types listed will match the post type shown. Ie: On pages only pages will be shown, on posts only posts will be shown.

If you select "Select from a list" you will get a dropdown which allows you to specify the post types you would like shown in the list.

= Thanks =

* [FontAwesome](fontawesome.io) for the icons used in the plugin featured image and icon

== Installation ==

= Automatic Installation =

Installing this plugin automatically is the easiest option. You can install the plugin automatically by going to the plugins section in WordPress and clicking Add New. Type "Unread Posts" in the search bar and install the plugin by clicking the Install Now button.

= Manual Installation =

To manually install the plugin you'll need to download the plugin to your computer and upload it to your server via FTP or another method. The plugin needs to be extracted in the `wp-content/plugins` folder. Once done you should be able to activate it as usual.

If you are having trouble, take a look at the [Managing Plugins](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation) section in the WordPress Codex, it has more information on this topic.

== Other Notes ==

The plugin was written in a completely object oriented fashion to make it easy to extend. If you need to use the raw functionality of unread posts you'll want to use the $up_unread_posts variable which can be globalized and used anywhere.

= Using The Unread Posts Class =

This has the following methods you can use:

* set_posts_as_read( $posts )
* set_posts_as_unread( $posts )
* get_read_posts()
* get_unread_posts()
* delete_read_posts()

Please see the appropriate files for more information on these functions and how to use them. All files have extensive phpDoc which should help you out.

Depending on the user state different classes handle these functions, you can also create your own handler. Each handler must adhere to the UP_Unread_Posts_Handler_Interface interface.

Also note that some functions may not be usable everywhere. For example, cookies are used to store read posts. The value of cookies must be set before any HTML is sent so you can't just use the set_posts_as_read() function anywhere you'd like.

= Hooks =

The plugin has lots of hooks which help you extend it. Here is a full list of the ones available right now:

**up/cookie_name**
The default cookie name used will be '[blog-name]-up_read_posts'. If you'd like to change it you can use this filter. The only parameter it receives is the cookie name.

`add_filter( 'up/cookie_name', 'my_up_cookie_name' );
function my_up_cookie_name() {
    return 'my-awesome-up-cookie';
}`

**up/meta_field**
The default user meta key used will be 'up_read_posts'. If you'd like to change it you can use this filter. The only parameter it receives is the meta key name.

`add_filter( 'up/meta_field', 'my_up_meta_field' );
function my_up_meta_field() {
    return 'my_read_posts';
}`

**up/unread_query_args**
This filter modifies the query arguments passed to the WP_Query object when querying for unread posts. Unread posts are retrieved by passing the array of read post IDs to the `post__not_in` parameter. If you would like to place additional restrictions you can do so with this filter. It receives the arguments as the first parameter.

`add_filter( 'up/unread_query_args', 'my_up_unread_query_args' );
function my_up_unread_query_args( $args ) {
    $args['cat'] = 10;
    return $args;
}`

**up/unread_widget_display**
This filter can modify the display of the post list in the widget. It receives three parameters. The original HTML to be displayed, the WP_Query object of unread posts and the instance variables for the widget. You can use it to completely customize the display of the posts.

`add_filter( 'up/unread_widget_display', 'my_unread_widget_display', 10, 3 );
function my_unread_widget_display( $display, $unread, $instance ) {
    $output = '<ul>';
    while( $unread->have_posts() ) {
        $unread->the_post();
        $output = '<li>' . the_title( '', '', false ) . '</li>';
    }
    $output = '</ul>';
    return $output;
}`

**up/show_widget**
If you want to hide the widget in the admin because you are only using the functionality of the plugin to build something yourself you can do so with this filter. Return false to hide the widget.

`add_filter( 'up/show_widget', 'hide_up_widget' );
function hide_up_widget() {
    return false;
}`

**up/show_settings_page**
If you want to hide the settings page in the admin because you are only using the functionality of the plugin to build something yourself you can do so with this filter. Return false to hide the settings page.

`add_filter( 'up/show_settings_page', 'hide_settings_page' );
function hide_settings_page() {
    return false;
}`


**up/posts_section_settings**
This filter can be used to modify the settings for the unread posts section shown below single posts. It's only parameter is the settings it received, print that parameter to figure out all the options.

`add_filter( 'up/posts_section_settings', 'my_posts_section_settings' );
function my_posts_section_settings( $settings ) {
    $settings['count'] = 10;
    return $settings;
}`


**up/unread_section_display**
This filter can modify the display of the unread posts list shown below single posts. It receives three parameters. The original HTML to be displayed, the WP_Query object of unread posts and the settings for the section. You can use it to completely customize the display of the posts.

`add_filter( 'up/unread_section_display', 'my_unread_section_display', 10, 3 );
function my_unread_section_display( $display, $unread, $settings ) {
    $output = '<ul>';
    while( $unread->have_posts() ) {
        $unread->the_post();
        $output = '<li>' . the_title( '', '', false ) . '</li>';
    }
    $output = '</ul>';
    return $output;
}`

**up/usable_post_types**
If you would like to restrict the post types available to the plugin you can use this filter to do so. It takes the currently available post types as the first parameter.

`add_filter( 'up/usable_post_types', 'my_usable_post_types' );
function my_usable_post_types( $post_types ) {
    $post_types = array( 'post', 'product' );
    return $post_types;
}`

== Screenshots ==

1. Widget Settings
2. Unread post section settings
3. Widget display in Twenty Fifteen
4. Unread post section display in Twenty Fifteen
5. Widget display in Twenty Fourteen
6. Unread post section display in Twenty Fourteen
7. Widget display in No Nonsense
8. Unread post section display in No Nonsense

== Changelog ==

= 1.0.3 (2014-10-14) =
* Fixed a warning from a missing array check

= 1.0.2 (2014-09-14) =
* Fixed a loop error in the widget

= 1.0.1 (2014-05-12) =
* Added widget_title filter to widget title


= 1.0.0 =

* Initial Release.
