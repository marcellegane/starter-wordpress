<?php

//------------------------------------------------------------------------
//  $Remove wp_head junk
//------------------------------------------------------------------------


remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);


//------------------------------------------------------------------------
//  $Define constants
//------------------------------------------------------------------------


define('THEME_TEMPLATE_DIRECTORY', get_template_directory_uri());


//------------------------------------------------------------------------
//  $Prevent File Modifications
//------------------------------------------------------------------------


define('DISALLOW_FILE_MODS', true);
show_admin_bar(false);


//------------------------------------------------------------------------
//  $Custom login image
//------------------------------------------------------------------------


function my_login_logo_url() {
    return 'SITE_URL';
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'SITE_TITLE';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

function my_login_logo() { ?>
    <style type="text/css">
        .login h1 a {
            width: 250px;
            height: 80px;
            background-image: url(<?php echo get_template_directory_uri(); ?>/assets/img/login-logo.png);
            background-size: 100%;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );


//------------------------------------------------------------------------
//  $Setup function
//------------------------------------------------------------------------


function theme_setup() {
    register_nav_menu('main','Main');
    add_theme_support( 'post-thumbnails' );
}
add_action( 'init', 'theme_setup' );


//------------------------------------------------------------------------
//  $Remove menu items
//------------------------------------------------------------------------


function theme_admin_menu() {
    foreach (array(
        'edit-comments.php'
    ) as $item) {
        remove_menu_page($item);
    }
}
add_action('admin_menu', 'theme_admin_menu');


//------------------------------------------------------------------------
//  $Include stylesheets / javascript
//------------------------------------------------------------------------


function jquery_footer_load() {
    if( !is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', '/wp-includes/js/jquery/jquery.js', FALSE, '1.11.0', TRUE);
        wp_enqueue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'jquery_footer_load');

function theme_scripts() {
    wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/header/header.js', '', '', false);
    wp_enqueue_script( 'theme-script', get_template_directory_uri() . '/js/scripts.js', array('jquery'), '', true);
}
add_action( 'wp_enqueue_scripts', 'theme_scripts');


//------------------------------------------------------------------------
//  $Filters
//------------------------------------------------------------------------


function add_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );

function new_excerpt_more( $more ) {
    return ' ...';
}
add_filter('excerpt_more', 'new_excerpt_more');

function custom_excerpt_length( $length ) {
    return 40;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );


//------------------------------------------------------------------------
//  $Custom MCE Editor Styles
//------------------------------------------------------------------------


function my_mce_buttons_2( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );

function my_mce_before_init( $settings ) {
    $style_formats = array(
        array(
        'title' => 'Large Text',
        'selector' => 'p',
        'classes' => 't-l'
        )
    );

    $settings['style_formats'] = json_encode( $style_formats );
    return $settings;
}
add_filter( 'tiny_mce_before_init', 'my_mce_before_init' );

function add_my_editor_style() {
  add_editor_style();
}
add_action( 'admin_init', 'add_my_editor_style' );


//------------------------------------------------------------------------
//  $Numeric Pagination
//------------------------------------------------------------------------


function numeric_pagination() {

    if (is_singular()) {
        return;
    }

    global $wp_query;

    /** Stop execution if there's only 1 page */
    if( $wp_query->max_num_pages <= 1 ) {
        return;
    }

    $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
    $max   = intval( $wp_query->max_num_pages );

    /** Add current page to the array */
    if ( $paged >= 1 ) {
        $links[] = $paged;
    }

    /** Add the pages around the current page to the array */
    if ( $paged >= 3 ) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }

    if ( ( $paged + 2 ) <= $max ) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }

    echo '<ul class="pag">' . "\n";

    /** Previous Post Link */
    if ( get_previous_posts_link() )
        printf( '<li class="pag-previous">%s</li>' . "\n", get_previous_posts_link('Previous') );

    /** Link to first page, plus ellipses if necessary */
    if ( ! in_array( 1, $links ) ) {
        $class = 1 == $paged ? ' class="pag__item is-active"' : ' class="pag__item"';

        printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

        if ( ! in_array( 2, $links ) )
            echo '<li>…</li>';
    }

    /** Link to current page, plus 2 pages in either direction if necessary */
    sort( $links );
    foreach ( (array) $links as $link ) {
        $class = $paged == $link ? ' class="pag__item is-active"' : ' class="pag__item"';
        printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
    }

    /** Link to last page, plus ellipses if necessary */
    if ( ! in_array( $max, $links ) ) {
        if ( ! in_array( $max - 1, $links ) )
            echo '<li class="pag__item">…</li>' . "\n";

        $class = $paged == $max ? ' class="pag__item is-active"' : ' class="pag__item"';
        printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
    }

    /** Next Post Link */
    if ( get_next_posts_link() )
        printf( '<li class="pag-next">%s</li>' . "\n", get_next_posts_link('Next') );

    echo '</ul>' . "\n";

}


?>