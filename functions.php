<?php
	
require_once( dirname(__FILE__) . '/setup.php' );

/******************************************************************************
*  Sophistique Main Class
******************************************************************************/

class Sophistique
{
	function __construct(){
		add_filter( 'pagelines_foundry', array( &$this, 'google_fonts' ) );
		add_filter( 'pl_activate_url',   array( &$this, 'activation_url') );
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


	function activation_url($url){
		return $url = home_url() . '?tablink=Sophistique&tabsublink=welcome';
	}

	function create_theme_options(){
		$hi = "
			<h4>Thanks for your purchase.</h4>
			<div>Your new and shiny theme is ready to be used. <br/>Please be aware the instructions for a optimal setup.</div>
		";

		$step1 = "
			<h4>Import the configuration</h4>
			<div>
					<p>
						1. Please click in the \"Import Config\" menu item in the left.<br>
						2. Locate the yellow button \"Load Child  Theme Config\" and click on it.<br>
						3. A popup will show, click on the \"Ok\" button.<br>
						4. Once you've completed this action, you may want to publish these changes to your live site.<br>
					</p>
			</div>	
		";

		$step2 = "
			<h4>Import demo content</h4>
			<div>
					<p>
						1. Please <a href=\"" .home_url( "/wp-content/themes/sophistique/sophistique-demo-content.zip")."\">click here</a> to get the demo content file.<br>
						2. Unzip the file. A new file called sophistique-demo-content.xml will be created.<br>
						3. Within your wp admin area, go to the Menu Tool -> Import.<br>
						4. From the list options, click on WordPress.<br>
						5. A popup will show asking for install the \"WordPress Importer\" plugin, click \"Install Now\".<br>
						6. Activate plugin and Run Importer<br>
						7. In the \"Choose a file from your computer: \" choose the file from the point 2.<br>
						8. Click Upload file and import.<br>
						9. In the \"Assign Authors\" check the \"Download and import file attachments\".
						10. Click Submit.
					</p>
			</div>	
		";
		$soptions = array();
		$soptions['Sophistique'] = array(
			'pos'   => 1,
		    'name'  => 'Sophistique',
		    'icon'  => 'icon-pagelines',
		    'opts'  => array(
		        array(
		        	'key' => 'welcome',
		        	'type' => 'template',
		        	'template' => $hi,
		        	'title' => 'Hi, Welcome to Sophistique'
		        ),
		        array(
		        	'key' => 'step1',
		        	'type' => 'template',
		        	'template' => $step1,
		        	'title' => 'Step 1 - Child Theme configuration'
		        ),
		        array(
		        	'key' => 'step2',
		        	'type' => 'template',
		        	'template' => $step2,
		        	'title' => 'Step 2 - Demo content'
		        )

		    )
		);
		pl_add_theme_tab( $soptions );
	}
}

new Sophistique;