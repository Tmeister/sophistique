<?php
/*
Section: Sophistique Menu
Author: Enrique Chavez
Author URI: http://tmeister.net
Version: 1.0
Description: Inline Description
Class Name: TMSOMenu
Filter: full-width
*/

class TMSOMenu extends PageLinesSection {

	function section_persistent(){
		register_nav_menus( array( 'primary' => __( 'Primary Website Navigation', 'pagelines' ) ) );
	}

	function section_head(){

	}

 	function section_template()
 	{
		$menu = ( $this->opt( 'tm_sm_menu' ) ) ? $this->opt( 'tm_sm_menu' ) : null;
	    ?>
	    	<div class="pl-content">
		    	<div class="row somenu-container">
		    		<div class="span3">
		    			<img src="<?php echo $this->opt('so_logotype') ?>" alt="" data-sync="so_logotype">
		    		</div>
		    		<div class="span9">
		    			<nav class="nav-sophis">
				            <?php
				            	if ( is_array( wp_get_nav_menu_items( $menu ) ) || has_nav_menu( 'primary' ) ) {
					                wp_nav_menu(
					                    array(
					                        'menu_class'  => 'menu-sophis',
					                        'container' => 'div',
					                        'container_class' => 'nav-sophis-holder clear',
					                        'depth' => 3,
					                        'menu' => $menu,
					                        'walker' => new Sophistique_walker
					                    )
					                );
					            }else{
					           		$this->so_nav_fallback( 'menu-sophis', 3 );
								}
				            ?>
				        </nav>
		    		</div>
		    	</div>
		    </div>

	    <?php
	}

	function section_opts()
	{
		$opts = array(
			array(
				'type'  => 'image_upload',
				'title' => 'Site Logotype',
				'key'   => 'so_logotype',
				'label' => 'Please select the site logotype.',
	        ),
		);
		return $opts;
	}

	function so_nav_fallback($class = '', $limit = 6){

		$pages = wp_list_pages('echo=0&title_li=&sort_column=menu_order&depth=1');

		$pages_arr = explode("\n", $pages);

		$pages_out = '';
		for($i=0; $i < $limit; $i++){

			if(isset($pages_arr[$i]))
				$pages_out .= $pages_arr[$i];

		}

		printf('<div class="nav-sophis-holder"><ul class="%s">%s</ul></div>', $class, $pages_out);
	}


} /* End of section class - No closing php tag needed */

/**
* Walker Class for build Delicone Menu
*/
class Sophistique_walker extends Walker_Nav_Menu
{
 	function start_el(&$output, $item, $depth, $args) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . '"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		$description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

		if($depth != 0) {
			$description = $append = $prepend = "";
		}

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
		$item_output .= $description.$args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}



