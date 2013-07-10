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

 	function section_template( $clone_id = null )
 	{
		if( !has_nav_menu('primary')  ){
	            echo setup_section_notify($this, 'Please, set up the "Primary Website Navigation" to show it in this section.', get_admin_url(). 'nav-menus.php', 'Configure Menu');
	            return;
	        }

	    ?>
	    	<div class="pl-content">
		    	<div class="row somenu-container">
		    		<div class="span3">
		    			<img src="<?php echo pl_setting('so_logotype') ?>" alt="" data-sync="so_logotype">
		    		</div>
		    		<div class="span9">
		    			<nav class="nav-sophis">
				            <?php
				                wp_nav_menu(
				                    array(
				                        'menu_class'  => 'menu-sophis',
				                        'container' => 'div',
				                        'container_class' => 'nav-sophis-holder clear',
				                        'depth' => 3,
				                        'theme_location'=>'primary',
				                        'walker' => new Sophistique_walker
				                    )
				                );
				            ?>
				        </nav>
		    		</div>
		    	</div>
		    </div>

	    <?php
	}


	function before_section_template( $clone_id = null ){}

	function after_section_template( $clone_id = null ){}

	function section_optionator( $settings ){
		$settings = wp_parse_args($settings, $this->optionator_default);

		$opt_array = array(
			'pullquote_text' 	=> array(
				'type' 			=> 'text',
				'inputlabel'	=> 'Pullquote Text',
				'title' 		=> 'Pullquote Text',
				'shortexp'		=> 'The primary quote text for your pullquote',
			)
		);

		$settings = array(
			'id' 		=> $this->id.'_meta',
			'name' 		=> $this->name,
			'icon' 		=> $this->icon,
			'clone_id'	=> $settings['clone_id'],
			'active'	=> $settings['active']
		);

		register_metatab($settings, $opt_array);
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



