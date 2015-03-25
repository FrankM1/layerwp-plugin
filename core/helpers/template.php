<?php
/**
 * Template helper functions
 *
 * This file is used to display general template elements, such as breadcrumbs, site-wide pagination,  etc.
 *
 * @package Layers
 * @since Layers 1.0.0
 */

/**
* Print pagination
*
* @param    array           $args           Arguments for this function, including 'query', 'range'
* @param    string         $wrapper        Type of html wrapper
* @param    string         $wrapper_class  Class of HTML wrapper
* @echo     string                          Post Meta HTML
*/
if( !function_exists( 'layers_pagination' ) ) {
	function layers_pagination( $args = NULL , $wrapper = 'div', $wrapper_class = 'pagination' ) {

		// Set up some globals
		global $wp_query, $paged;

		// Get the current page
		if( empty($paged ) ) $paged = ( get_query_var('page') ? get_query_var('page') : 1 );

		// Set a large number for the 'base' argument
		$big = 99999;

		// Get the correct post query
		if( !isset( $args[ 'query' ] ) ){
			$use_query = $wp_query;
		} else {
			$use_query = $args[ 'query' ];
		} ?>

		<<?php echo $wrapper; ?> class="<?php echo $wrapper_class; ?>">
			<?php echo paginate_links( array(
				'base' => str_replace( $big, '%#%', get_pagenum_link($big) ),
				'prev_next' => true,
				'mid_size' => ( isset( $args[ 'range' ] ) ? $args[ 'range' ] : 3 ) ,
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'type' => 'list',
				'current' => $paged,
				'total' => $use_query->max_num_pages
			) ); ?>
		</<?php echo $wrapper; ?>>
	<?php }
} // layers_pagination
 
/**
 * Retrieve theme modification value for the current theme.
 *
 * @param string $name Theme modification name.
 * @return string
 */
if( !function_exists( 'layers_get_theme_mod' ) ) {
	function layers_get_theme_mod( $name = '' ) {

		global $layers_customizer_defaults;

		// Add the theme prefix to our layers option
		$name = LAYERS_THEME_SLUG . '-' . $name;

		// Set theme option default
		$default = ( isset( $layers_customizer_defaults[ $name ][ 'value' ] ) ? $layers_customizer_defaults[ $name ][ 'value' ] : FALSE );


		// If color control always return a value
		if (
				isset( $layers_customizer_defaults[ $name ][ 'type' ] ) &&
				'layers-color' == $layers_customizer_defaults[ $name ][ 'type' ]
			){
			$default = '';
		}

		// Get theme option
		$theme_mod = get_theme_mod( $name, $default );

		// Return theme option
		return $theme_mod;
	}
} // layers_get_theme_mod

/**
 * Check customizer and page template settings before allowing a sidebar to display
 *
 * @param   int     $sidebar                Sidebar slug to check
 */
if( !function_exists( 'layers_can_show_sidebar' ) ) {
	function layers_can_show_sidebar( $sidebar = 'left-sidebar' ){

		if ( is_page_template( 'template-blog.php' ) ) {

			// Check the arhive page option
		   $can_show_sidebar = layers_get_theme_mod( 'archive-' . $sidebar );

		} else if( is_page() ) {

			// Check the pages use page templates to decide which sidebars are allowed
			$can_show_sidebar =
				(
					is_page_template( 'template-' . $sidebar . '.php' ) ||
					is_page_template( 'template-both-sidebar.php' )
				);

		} elseif ( is_single() ) {

			// Check the single page option
		   $can_show_sidebar = layers_get_theme_mod( 'single-' . $sidebar );

		} else {

			// Check the arhive page option
		   $can_show_sidebar = layers_get_theme_mod( 'archive-' . $sidebar );

		}

		return $classes = apply_filters( 'layers_can_show_sidebar', $can_show_sidebar, $sidebar );
	}
}

/**
 * Check customizer and page template settings before displaying a sidebar
 *
 * @param   int     $sidebar                Sidebar slug to check
 * @param   string $container_class       Sidebar container class
 * @return  html    $sidebar                Sidebar template
 */
if( !function_exists( 'layers_maybe_get_sidebar' ) ) {
	function layers_maybe_get_sidebar( $sidebar = 'left', $container_class = 'column', $return = FALSE ) {

		global $post;

		$show_sidebar = layers_can_show_sidebar( $sidebar );

		if( TRUE == $show_sidebar ) { ?>
			<?php if( is_active_sidebar( LAYERS_THEME_SLUG . '-' . $sidebar ) ) { ?>
				<div class="<?php echo esc_attr( $container_class ); ?>">
			<?php } ?>
				<?php dynamic_sidebar( LAYERS_THEME_SLUG . '-' . $sidebar ); ?>
			<?php if( is_active_sidebar( LAYERS_THEME_SLUG . '-' . $sidebar ) ) { ?>
				</div>
			<?php } ?>
		<?php }
	}
} // layers_maybe_get_sidebar
 
/**
* Style Generator
*
* @param    string     $type   Type of style to generate, background, color, text-shadow, border
* @param    array       $args
*
* @return   string     $layers_inline_css CSS to append to the inline widget styles that have been generated
*/
if( !function_exists( 'layers_inline_styles' ) ) {
	function layers_inline_styles( $container_id = NULL, $type = 'background' , $args = array() ){

		// Get the generated CSS
		global $layers_inline_css;

		$css = '';

		if( empty( $args ) || ( !is_array( $args ) && '' == $args ) ) return;

		switch ( $type ) {

			case 'background' :

				// Set the background array
				$bg_args = $args['background'];

				if( isset( $bg_args['color'] ) && '' != $bg_args['color'] ){
					$css .= 'background-color: ' . $bg_args['color'] . '; ';
				}

				if( isset( $bg_args['repeat'] ) && '' != $bg_args['repeat'] ){
					$css .= 'background-repeat: ' . $bg_args['repeat'] . ';';
				}

				if( isset( $bg_args['position'] ) && '' != $bg_args['position'] ){
					$css .= 'background-position: ' . $bg_args['position'] . ';';
				}

				if( isset( $bg_args['stretch'] ) && '' != $bg_args['stretch'] ){
					$css .= 'background-size: cover;';
				}

				if( isset( $bg_args['fixed'] ) && '' != $bg_args['fixed'] ){
					$css .= 'background-attachment: fixed;';
				}

				if( isset( $bg_args['image'] ) && '' != $bg_args['image'] ){
					$image = wp_get_attachment_image_src( $bg_args['image'] , 'full' );
					$css.= 'background-image: url(\'' . $image[0] .'\');';
				}
			break;

			case 'margin' :
			case 'padding' :

				// Set the Margin or Padding array
				$trbl_args = $args[ $type ];

				if( isset( $trbl_args['top'] ) && '' != $trbl_args['top'] ){
					$css .= $type . '-top: ' . $trbl_args['top'] . '; ';
				}

				if( isset( $trbl_args['right'] ) && '' != $trbl_args['right'] ){
					$css .= $type . '-right: ' . $trbl_args['right'] . '; ';
				}

				if( isset( $trbl_args['bottom'] ) && '' != $trbl_args['bottom'] ){
					$css .= $type . '-bottom: ' . $trbl_args['bottom'] . '; ';
				}

				if( isset( $trbl_args['left'] ) && '' != $trbl_args['left'] ){
					$css .= $type . '-left: ' . $trbl_args['left'] . '; ';
				}

			break;

			case 'color' :

				if( '' == $args[ 'color' ] ) return ;
				$css .= 'color: ' . $args[ 'color' ] . ';';

			break;

			case 'font-family' :

				if( '' == $args[ 'font-family' ] ) return ;
				$css .= 'font-family: ' . $args[ 'font-family' ] . ', "Helvetica Neue", Helvetica, sans-serif;';

			break;

			case 'text-shadow' :

				if( '' == $args[ 'text-shadow' ] ) return ;
				$css .= 'text-shadow: 0px 0px 10px rgba(' . implode( ', ' , layers_hex2rgb( $args[ 'text-shadow' ] ) ) . ', 0.75);';

			break;

			case 'css' :

				$css .= $args['css'];

			break;

			default :
				$css .= $args['css'];
			break;

		}

		$inline_css = '';

		// If there is a container ID specified, append it to the beginning of the declaration
		if( NULL != $container_id ) {
			$inline_css = ' ' . $container_id . ' ' . $inline_css;
		}

		if( isset( $args['selectors'] ) ) {

			if ( is_string( $args['selectors'] ) && '' != $args['selectors'] ) {
				$inline_css .= $args['selectors'];
			} else if( !empty( $args['selectors'] ) ){
				$inline_css .= implode( ', ' .$inline_css . ' ',  $args['selectors'] );
			}
		}

		if( '' == $inline_css) {
			$inline_css .= $css;
		} else {
			$inline_css .= '{' . $css . '} ';
		}

		$layers_inline_css .= $inline_css;

		return apply_filters( 'layers_inline_css', $layers_inline_css );
	}
} // layers_inline_styles

if( !function_exists( 'layers_apply_inline_styles' ) ) {
	function layers_apply_inline_styles(){
		global $layers_inline_css;

		wp_enqueue_style(
				LAYERS_THEME_SLUG . '-inline-styles',
				get_template_directory_uri() . '/assets/css/inline.css'
			);

		wp_add_inline_style(
				LAYERS_THEME_SLUG . '-inline-styles',
				$layers_inline_css
			);
	}
} // layers_apply_inline_styles
add_action( 'get_footer' , 'layers_apply_inline_styles', 100 );

/**
* Feature Image / Video Generator
*
* @param int $attachmentid ID for attachment
* @param int $size Media size to use
* @param int $video oEmbed code
*
* @return   string     $media_output Feature Image or Video
*/
if( !function_exists( 'layers_get_feature_media' ) ) {
	function layers_get_feature_media( $attachmentid = NULL, $size = 'medium' , $video = NULL, $postid = NULL ){

		// Return dimensions
		$image_dimensions = layers_get_image_sizes( $size );

		// Check for an image
		if( NULL != $attachmentid && '' != $attachmentid ){
			$use_image = wp_get_attachment_image( $attachmentid , $size);
		}

		// Check for a video
		if( NULL != $video && '' != $video ){
			$embed_code = '[embed width="'.$image_dimensions['width'].'" height="'.$image_dimensions['height'].'"]'.$video.'[/embed]';
			$wp_embed = new WP_Embed();
			$use_video = $wp_embed->run_shortcode( $embed_code );
		}

		// Set which element to return
		if( NULL != $postid &&
				(
					( is_single() && isset( $use_video ) ) ||
					( ( !is_single() && !is_page_template( 'template-blog.php' ) ) && isset( $use_video ) && !isset( $use_image) )
				)
		) {
			$media = $use_video;
		} else if( NULL == $postid && isset( $use_video ) ) {
			$media = $use_video;
		} else if( isset( $use_image ) ) {
			$media = $use_image;
		} else {
			return NULL;
		}

		$media_output = do_action( 'layers_before_feature_media' ) . $media . do_action( 'layers_after_feature_media' );

		return $media_output;
	}
} //if layers_get_feature_media

/**
* Get Available Image Sizes for specific Image Type
*
* @param    string     $size 	Image size slug
*
* @return   array     $sizes 	Array of image dimensions
*/
if( !function_exists( 'layers_get_image_sizes' ) ) {
	function layers_get_image_sizes( $size = 'medium' ) {

		global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {

            if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                    $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                    $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                    $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                    $sizes[ $_size ] = array(
                            'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                            'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                            'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
                    );
            }
        }

        // Get only 1 size if found
        if ( $size ) {

            if( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
            } else {
				return $sizes[ 'large' ];
            }

        }

        return $sizes;
	}
} // if layers_get_image_sizes

/**
 * Translates an image ratio input into a nice clean image ratio we can use
 *
 * @param string $value Value of the input
 * @return string Image size
 *
 */
if( !function_exists( 'layers_translate_image_ratios' ) ) {
	function layers_translate_image_ratios( $value = '' ) {

		if( 'image-round' == $value ){
			$image_ratio = 'square';
		} else if( 'image-no-crop' == $value ) {
			$image_ratio = '';
		} else {
			$image_ratio = str_replace( 'image-' , '', $value );
		}

		return 'layers-' . $image_ratio;
	}
} // layers_translate_image_ratios

/**
 * Convert hex value to rgb array.
 *
 * @param	string	$hex
 * @return	array	implode(",", $rgb); returns the rgb values separated by commas
 */

if(!function_exists('layers_hex2rgb') ) {
	function layers_hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);

	   return $rgb; // returns an array with the rgb values
	}
}

/**
 * Detect if we should use a light or dark colour on a background colour
 *
 * @param mixed $color
 * @param string $dark (default: '#000000')
 * @param string $light (default: '#FFFFFF')
 * @return string
 */

if ( ! function_exists( 'layers_light_or_dark' ) ) {
	function layers_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {

		$hex = str_replace( '#', '', $color );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155 ? $dark : $light;
	}
} // layers_light_or_dark
 
/**
 * Standard menu fallback
 */

if ( ! function_exists( 'layers_show_html5_video' ) ) {
	function layers_show_html5_video( $src = NULL , $width = 490 ) {
		if( NULL == $src ) return; ?>
		<video width="<?php echo $width;?>" height="auto" controls>
			<source src="<?php echo $src; ?>?v=<?php echo LAYERS_VERSION; ?>" type="video/<?php echo substr( $src, -3, 3); ?>">
			<?php _e( 'Your browser does not support the video tag.' , 'layerswp' ); ?>
		</video>
<?php }
} // layers_show_html5_video
 
/**
 * Return a list of stock standard WP taxonomies
 */

if ( ! function_exists( 'layers_get_standard_wp_taxonomies' ) ) {
	function layers_get_standard_wp_taxonomies(){
		return array( 'category', 'nav_menu', 'category', 'link_category', 'post_format' );
	}
} // layers_get_standard_wp_taxonomies
