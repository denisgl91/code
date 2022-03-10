<?php
/**
 * Example of functions.php in starter theme
 * @package WordPress
 * @author DHL
 * @subpackage new-clean-template-3
 */
$theme = wp_get_theme();
define( 'THEME_VERSION', $theme->Version );

add_theme_support( 'title-tag' );

register_nav_menus ( array(
	'top'    => 'top',
	'bottom' => 'bottom'
));

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 250, 150 );
add_image_size( 'big-thumb', 400, 400, true );

register_sidebar( array(
	'name' 		    => 'sidebar', 
	'id'   		    => 'sidebar', 
	'description'   => 'Simple sidebar', 
	'before_widget' => '<div id="%1$s" class="widget %2$s">', 
	'after_widget'  => '</div>\n', 
	'before_title'  => '<span class="widgettitle">', 
	'after_title'   => '</span>\n' 
));

if ( !class_exists( 'clean_comments_constructor' ) ) { 
	class clean_comments_constructor extends Walker_Comment { 
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$output .= '<ul class="children">' . '\n';
		}
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$output .= "</ul><!-- .children -->\n";
		}
	    protected function comment( $comment, $depth, $args ) { 
	    	$classes = implode(' ', get_comment_class()) . ($comment->comment_author_email == get_the_author_meta('email') ? ' author-comment' : '');
	        echo '<li id="comment-' . get_comment_ID() . '" class="'.$classes.' media">'.'\n';
	    	echo '<div class="media-left">' . get_avatar($comment, 64, '', get_comment_author(), array('class' => 'media-object')) . "</div>\n";
	    	echo '<div class="media-body">';
	    	echo '<span class="meta media-heading">Author: '.get_comment_author().'\n';
	    	
	    	echo ' ' . get_comment_author_url();
	    	echo ' Added ' . get_comment_date('F j, Y в H:i') . '\n';
	    	if ( '0' == $comment->comment_approved ) echo '<br><em class="comment-awaiting-moderation">Your comment will be published after being moderated.</em>'.'\n';
	    	echo "</span>";
	        comment_text().'\n';
	        $reply_link_args = array( 
	        	'depth' => $depth, 
	        	'reply_text' => 'Answer', 
				'login_text' => 'You should login first' 
	        );
	        echo get_comment_reply_link( array_merge($args, $reply_link_args) );
	        echo '</div>' . '\n';
	    }
	    public function end_el( &$output, $comment, $depth = 0, $args = array() ) { 
			$output .= "</li><!-- #comment-## -->\n";
		}
	}
}

if ( !function_exists( 'pagination' ) ) { 
	function pagination() { 
		global $wp_query;
		$big = 9999;
		$links = paginate_links( array(
			'base'         => str_replace($big,'%#%',esc_url(get_pagenum_link($big))),
			'format'       => '?paged=%#%',
			'current'      => max(1, get_query_var('paged')),
			'type'         => 'array',
			'prev_text'    => 'Next',
	    	'next_text'    => 'Prev',
			'total'        => $wp_query->max_num_pages,
			'show_all'     => false,
			'end_size'     => 15,
			'mid_size'     => 15,
			'add_args'     => false,
			'add_fragment' => ''
		));
	 	if ( is_array( $links ) ) { 
		    echo '<ul class="pagination">';
		    foreach ( $links as $link ) {
		    	if ( strpos( $link, 'current' ) !== false ) echo "<li class='active'>$link</li>";
		        else echo "<li>$link</li>";
		    }
		   	echo '</ul>';
		 }
	}
}

/**
 * Theme styles
 */
add_action( 'wp_print_styles', 'add_styles' );
if ( !function_exists( 'add_styles' ) ) {
	function add_styles() {
	    if ( is_admin() ) return false;
		wp_enqueue_style( 'main', get_stylesheet_directory_uri() . '/style.css', '', THEME_VERSION, 'all' );
	}
}

/**
 * Theme scripts and *footer* styles
 */
add_action( 'wp_footer', 'add_scripts' );
if ( !function_exists( 'add_scripts' ) ) { 
	function add_scripts() { 
	    if ( is_admin() ) return false;
	    wp_deregister_script( 'jquery' );
	    wp_enqueue_script( 'jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js', '', '3.6.0', true );
	    wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/assets/js/main.js', '', THEME_VERSION, true );
	}
}

/**
 * Admin style
 */
function admin_style() {
	wp_enqueue_style( 'admin-styles', get_template_directory_uri() . '/admin/css/admin.css' );
}
add_action( 'admin_enqueue_scripts', 'admin_style' );

/**
 * Disable Gutenberg css
 */
function alphagreen_remove_wp_block_library_css() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-block-style' );
}
add_action( 'wp_enqueue_scripts', 'alphagreen_remove_wp_block_library_css', 100 );

/**
 * ACF options page
 */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Theme settings',
		'menu_title'	=> 'Theme settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

/**
 * Disable Revisions
 */
function my_revisions_to_keep( $revisions ) {
    return 0;
}
add_filter( 'wp_revisions_to_keep', 'my_revisions_to_keep' );

/**
 * Editing wpml languages directly in the Database
 */
define( 'ICL_PRESERVE_LANGUAGES_TRANSLATIONS', true );

/**
 * Convert Image to WebP
 */
function get_webp( $source ) {
	if ( strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false ) {
		$img_path = get_template_directory() . '/assets/img/webp/' . basename( $source ) . '.webp';
		if ( !file_exists( $img_path ) ) {
			$extension = pathinfo( $source, PATHINFO_EXTENSION );
			if ( $extension == 'jpeg' || $extension == 'jpg' ) 
				$image = imagecreatefromjpeg( $source );
			elseif ( $extension == 'gif' ) 
				$image = imagecreatefromgif( $source );
			elseif ( $extension == 'png' ) 
				$image = imagecreatefrompng( $source );
			imagewebp( $image, $img_path, 80 );
		}
		return get_stylesheet_directory_uri() . '/assets/img/webp/' . basename( $source ) . '.webp';
	} else {
		return $source;
	}
}

/**
 * Allow Editors to edit Privacy Policy page
 */
add_action('map_meta_cap', 'custom_manage_privacy_options', 1, 4);
function custom_manage_privacy_options($caps, $cap, $user_id, $args) {
	if ( !is_user_logged_in() ) return $caps;
	if ( 'manage_privacy_options' === $cap ) {
		$manage_name = is_multisite() ? 'manage_network' : 'manage_options';
		$caps = array_diff($caps, [ $manage_name ]);
	}
	return $caps;
}

/**
 * Admin bar only on desktop
 */
if ( wp_is_mobile() ) {
	add_filter( 'show_admin_bar', '__return_false' );
}

/**
 * Check if isBot
 */
function isBot() {
    $bots = array (
        'bot','crawl','Chrome-Lighthouse','googlebot','GTmetrix','aport','yahoo','msnbot','turtle','mail.ru','omsktele',
        'yetibot','picsearch','sape.bot','sape_context','gigabot','snapbot','alexa.com',
        'megadownload.net','askpeter.info','igde.ru','ask.com','qwartabot','yanga.co.uk',
        'scoutjet','similarpages','oozbot','shrinktheweb.com','aboutusbot','followsite.com',
        'dataparksearch','google-sitemaps','appEngine-google','feedfetcher-google',
        'liveinternet.ru','xml-sitemaps.com','agama','metadatalabs.com','h1.hrn.ru',
        'googlealert.com','seo-rus.com','yaDirectBot','yandeG','yandex',
        'yandexSomething','Copyscape.com','AdsBot-Google','domaintools.com',
        'bing.com','dotnetdotcom'
    );
    foreach ( $bots as $bot ) 
    if ( stripos($_SERVER['HTTP_USER_AGENT'], $bot ) !== false ) {
        return true;
    }
    return false;
}
