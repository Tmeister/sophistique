<?php
	
require_once( dirname(__FILE__) . '/setup.php' );

/******************************************************************************
*  Sophistique Main Class
******************************************************************************/

class Sophistique
{
	function __construct(){
		add_filter( 'pagelines_foundry', array( &$this, 'google_fonts' ) );
		$this->create_theme_options();

	}

	/**
	 * Adding a custom font from Google Fonts
	 * @param type $thefoundry 
	 * @return type
	 */
	function google_fonts( $thefoundry ) {
		
		if ( ! defined( 'PAGELINES_SETTINGS' ) )
			return;

		$fonts = $this->get_fonts();
		return array_merge( $thefoundry, $fonts );	
	}

	/**
	 * Parse the external file for the fonts source
	 * @return type
	 */
	function get_fonts( ) {
		$fonts = pl_file_get_contents( dirname(__FILE__) . '/fonts.json' );
		$fonts = json_decode( $fonts );
		$fonts = $fonts->items;
		$fonts = ( array ) $fonts;
		$out = array();
		foreach ( $fonts as $font ) {
			$out[ str_replace( ' ', '_', $font->family ) ] = array(
				'name'		=> $font->family,
				'family'	=> sprintf( '"%s"', $font->family ),
				'web_safe'	=> true,
				'google' 	=> $font->variants,
				'monospace' => ( preg_match( '/\sMono/', $font->family ) ) ? 'true' : 'false',
				'free'		=> true
			);
		}
		return $out;
	}

	function create_theme_options(){
		$soptions = array();
		$soptions['Sophistique'] = array(
			'pos'   => 1,
		    'name'  => 'Sophistique',
		    'icon'  => 'icon-pagelines',
		    'opts'  => array(
		        array(
		        	'type'          => 'image_upload',
		            'title'         => 'Site Logotype',
		            'key'           => 'so_logotype',
		            'label'         => 'Please select the site logotype.',
		        ),
		    )
		);
		pl_add_theme_tab( $soptions );
	}

}

new Sophistique;