<?php
/**
 * Example of custom Gutenberg Blocks
 * @package WordPress
 * @subpackage new-clean-template-3
 * @author DHL
 */

add_action('acf/init', 'lpb_acf_init');
function lpb_acf_init() {
	
	if ( function_exists('acf_register_block') ) {
		
		acf_register_block(array(
			'name'				=> 'main-banner',
			'title'				=> __('Homepage banner'),
			'description'		=> __('The main section of homepage'),
			'render_callback'	=> 'lpb_block_render_callback',
			'category'			=> 'homepage',
			'icon'				=> 'format-image',
			'example'           => array(
                'attributes'    => array(
                    'mode'      => 'preview',
                    'data'      => array(
                        'bg_image'    => '/wp-content/themes/lpb/public/img/main-bg.jpg',
                        'title'       => 'LPB Bank banner',
                        'description' => 'LPB Bank e-commerce solutions - a convenient way to accept payments on your company`s website'
                    )
                )
            )
		));
		acf_register_block(array(
			'name'				=> 'featured-posts',
			'title'				=> __('Featured posts'),
			'description'		=> __('Section with last news/posts'),
			'render_callback'	=> 'lpb_block_render_callback',
			'category'			=> 'homepage',
			'icon'				=> 'format-image',
			'example'           => []
		));
		acf_register_block(array(
			'name'				=> 'common-title',
			'title'				=> __('Title'),
			'description'		=> __('Title with decorative line'),
			'render_callback'	=> 'lpb_block_render_callback',
			'category'			=> 'homepage',
			'example'           => []
		));
		acf_register_block(array(
			'name'				=> 'quote',
			'title'				=> __('Quote'),
			'description'		=> __('Text in quote design'),
			'render_callback'	=> 'lpb_block_render_callback',
			'category'			=> 'homepage',
			'icon'				=> 'format-quote',
			'example'           => []
		));
		acf_register_block(array(
			'name'				=> 'button',
			'title'				=> __('Button'),
			'description'		=> __('Simple button'),
			'render_callback'	=> 'lpb_block_render_callback',
			'category'			=> 'homepage',
			'example'           => []
		));
	}
}

function lpb_block_render_callback( $block ) {
	$slug = str_replace( 'acf/', '', $block['name'] );
	
	// include a template part from within the "template-parts/block" folder
	if ( file_exists( get_theme_file_path( '/templates/blocks/content-{$slug}.php' ) ) ) {
		include( get_theme_file_path("/templates/blocks/content-{$slug}.php") );
	}
}

?>
