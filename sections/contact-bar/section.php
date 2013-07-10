<?php
/*
Section: Contact Bar
Author: Enrique Chavez
Author URI: http://tmeister.net
Version: 1.0
Description: Inline Description
Class Name: SOContactBar
Filter: full-width
*/

/*
 * PageLines Headers API
 *
 *  Sections support standard WP file headers (http://codex.wordpress.org/File_Header) with these additions:
 *  -----------------------------------
 * 	 - Section: The name of your section.
 *	 - Class Name: Name of the section class goes here, has to match the class extending PageLinesSection.
 *	 - Cloning: (bool) Enable cloning features.
 *	 - Depends: If your section needs another section loaded first set its classname here.
 *	 - Workswith: Comma seperated list of template areas the section is allowed in.
 *	 - Failswith: Comma seperated list of template areas the section is NOT allowed in.
 *	 - Demo: Use this to point to a demo for this product.
 *	 - External: Use this to point to an external overview of the product
 *	 - Long: Add a full description, used on the actual store page on http://www.pagelines.com/store/
 *
 */

class SOContactBar extends PageLinesSection {

	var $domain = 'tm_contact_bar';

	function section_persistent(){
	}
	function section_head(){}

	function section_styles(){
		wp_enqueue_script( 'contact-bar', $this->base_url . '/js/cbar.js', array( 'jquery' ), '1.0', true );
	}

 	function section_template()
 	{
		$first_icon = $this->opt( 'tm_cb_first_icon' ) ? $this->opt( 'tm_cb_first_icon' ) : 'icon-th';
		$first_info = $this->opt( 'tm_cb_first_label') ? $this->opt( 'tm_cb_first_label') : 'Call Us: (001) 030-234-567-890';
		$sec_icon   = $this->opt( 'tm_cb_sec_icon' ) ? $this->opt( 'tm_cb_sec_icon' ) : 'icon-envelope';
		$sec_info   = $this->opt( 'tm_cb_sec_label') ? $this->opt( 'tm_cb_sec_label') : 'your@email.com';
		$socials    = array();
		foreach ($this->get_valid_social_sites() as $key => $social) {
			if( $this->opt( $social . '-url' ) ){
				array_push($socials, array('site' => $social, 'url' => $this->opt( $social . '-url' )));
			}
		}
 	?>
		<div class="pl-content">
			<div class="row cb-container">
				<div class="span3 cb-first-row">
					<div class="cb-holder">
						<i class="icon <?php echo $first_icon ?>">
							<span data-sync="tm_cb_first_label"><?php echo $first_info ?></span>
						</i>
					</div>
				</div>
				<div class="span3 cb-second-row">
					<div class="cb-holder">
						<i class="icon <?php echo $sec_icon ?>">
							<span data-sync="tm_cb_sec_label"><?php echo $sec_info ?></span>
						</i>
					</div>
				</div>
				<div class="span6 cb-icons">
					<div class="social-holder">
						<ul class="cb-menu">
							<?php foreach ($socials as $social): ?>
								<li class="<?php echo $social['site'] ?>">
									<a href="<?php echo $social['url'] ?>" title="<?php echo ucfirst($social['site']) ?>" target="_blank"></a>
								</li>
							<?php endforeach ?>
						</ul>
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</div>
	<?php
 	}

 	/**
	 *
	 * Section Page Options
	 *
	 * Section optionator is designed to handle section options.
	 */
	function section_optionator( $settings ){

		$settings = wp_parse_args($settings, $this->optionator_default);
		$opt_array = array(

			'tm_cb_phone' =>	array(
				'type'			=> 'multi_option',
				'title'			=> __('First Information Box', $this->domain),
				'shortexp'		=> __('Please fill the follow fields.', $this->domain),
				'selectvalues'	=> array(
					'tm_cb_first_icon'	=> array(
						'inputlabel'   	=> __( 'Select the icon to show beside the text - Icons Preview <a target="_blank" href="http://twitter.github.com/bootstrap/base-css.html#icons">bootstrap site.</a>', $this->domain ),
						'type'         	=> 'select',
						'selectvalues' 	=> $this->get_icons_select(),
					),
					'tm_cb_first_label'	=> array(
						'type' => 'text',
						'inputlabel' 	=> __( 'Enter the information to show in the information text, eg. "Call Us: (001) 030-234-567-890"', $this->domain ),
					),
				),
			),

			'tm_cb_email' =>	array(
				'type'			=> 'multi_option',
				'title'			=> __('Second Information Box', $this->domain),
				'shortexp'		=> __('Please fill the follow fields.', $this->domain),
				'selectvalues'	=> array(
					'tm_cb_sec_icon'	=> array(
						'inputlabel'   	=> __( 'Select the icon to show beside the text - Icons Preview <a target="_blank" href="http://twitter.github.com/bootstrap/base-css.html#icons">bootstrap site.</a>', $this->domain ),
						'exp'      		=> 'All the available icons can be see at the ',
						'type'         	=> 'select',
						'selectvalues' 	=> $this->get_icons_select(),
					),
					'tm_cb_sec_label'	=> array(
						'type' => 'text',
						'inputlabel' 	=> __( 'Enter the information to show in the information text, eg. "youremail@domain.com"', $this->domain ),
					),
				),
			),

			'tm_cb_social' =>	array(
				'type'			=> 'multi_option',
				'title'			=> __('Social Sites URL', $this->domain),
				'shortexp'		=> __('In the follow fields please, enter the social URL, if the URL field is empty, nothing will show.', $this->domain),
				'selectvalues'	=> $this->get_social_fields()
			),

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


	function get_icons_select()
	{
		$out = array();
		foreach ($this->get_valid_icons() as $icon => $name)
		{
			$out[$name] = array('name' => str_replace('icon-', '', $name));
		}

		return $out;
	}

	function get_social_fields()
	{
		$out = array();
		foreach ($this->get_valid_social_sites() as $social => $name)
		{
			$out[$name . '-url'] = array(
				'inputlabel' => __(ucfirst($name)),
				'type' => 'text'
			);
		}
		return $out;
	}

	function get_valid_social_sites()
	{
		return array("digg","dribbble","facebook","flickr","forrst","googleplus","html5","lastfm","linkedin","paypal","picasa","pinterest","rss","skype","stumbleupon","tumblr","twitter","vimeo","wordpress","yahoo","youtube","behance","instagram"
		);
	}

	function get_valid_icons()
    {
        return array( "icon-glass", "icon-music", "icon-search", "icon-envelope", "icon-heart", "icon-star", "icon-star-empty", "icon-user", "icon-film", "icon-th-large", "icon-th", "icon-th-list", "icon-ok", "icon-remove", "icon-zoom-in", "icon-zoom-out", "icon-off", "icon-signal", "icon-cog", "icon-trash", "icon-home", "icon-file", "icon-time", "icon-road", "icon-download-alt", "icon-download", "icon-upload", "icon-inbox", "icon-play-circle", "icon-repeat", "icon-refresh", "icon-list-alt", "icon-lock", "icon-flag", "icon-headphones", "icon-volume-off", "icon-volume-down", "icon-volume-up", "icon-qrcode", "icon-barcode", "icon-tag", "icon-tags", "icon-book", "icon-bookmark", "icon-print", "icon-camera", "icon-font", "icon-bold", "icon-italic", "icon-text-height", "icon-text-width", "icon-align-left", "icon-align-center", "icon-align-right", "icon-align-justify", "icon-list", "icon-indent-left", "icon-indent-right", "icon-facetime-video", "icon-picture", "icon-pencil", "icon-map-marker", "icon-adjust", "icon-tint", "icon-edit", "icon-share", "icon-check", "icon-move", "icon-step-backward", "icon-fast-backward", "icon-backward", "icon-play", "icon-pause", "icon-stop", "icon-forward", "icon-fast-forward", "icon-step-forward", "icon-eject", "icon-chevron-left", "icon-chevron-right", "icon-plus-sign", "icon-minus-sign", "icon-remove-sign", "icon-ok-sign", "icon-question-sign", "icon-info-sign", "icon-screenshot", "icon-remove-circle", "icon-ok-circle", "icon-ban-circle", "icon-arrow-left", "icon-arrow-right", "icon-arrow-up", "icon-arrow-down", "icon-share-alt", "icon-resize-full", "icon-resize-small", "icon-plus", "icon-minus", "icon-asterisk", "icon-exclamation-sign", "icon-gift", "icon-leaf", "icon-fire", "icon-eye-open", "icon-eye-close", "icon-warning-sign", "icon-plane", "icon-calendar", "icon-random", "icon-comment", "icon-magnet", "icon-chevron-up", "icon-chevron-down", "icon-retweet", "icon-shopping-cart", "icon-folder-close", "icon-folder-open", "icon-resize-vertical", "icon-resize-horizontal", "icon-hdd", "icon-bullhorn", "icon-bell", "icon-certificate", "icon-thumbs-up", "icon-thumbs-down", "icon-hand-right", "icon-hand-left", "icon-hand-up", "icon-hand-down", "icon-circle-arrow-right", "icon-circle-arrow-left", "icon-circle-arrow-up", "icon-circle-arrow-down", "icon-globe", "icon-wrench", "icon-tasks", "icon-filter", "icon-briefcase", "icon-fullscreen"
        );
	}

} /* End of section class - No closing php tag needed */