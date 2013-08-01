<?php
/*
Section: Revolution Slider
Author: Enrique Chavez
Author URI: http://tmeister.net
Version: 1.0
Description: Inline Description
Class Name: TMSORevolution
Filter: full-width

*/


class TMSORevolution extends PageLinesSection
{

	
	var $domain               = 'tmRevolution';
	/**************************************************************************
	* SLIDES
	**************************************************************************/
	var $tax_id               = 'tm_so_tax';
	var $custom_post_type     = 'tm_so_slider';
	/**************************************************************************
	* CAPTIONS
	**************************************************************************/
	var $tax_cap_id           = 'tm_so_cap_tax';
	var $custom_cap_post_type = 'tm_so_caption';

	var $slides = null;

	function section_persistent()
	{
		$this->post_type_slider_setup();
		$this->post_type_caption_setup();
		$this->post_meta_setup();
	}
	function section_styles(){
		wp_enqueue_script( 'common-plugins', $this->base_url . '/js/jquery.plugins.min.js', array( 'jquery' ), '1.0',true );
		wp_enqueue_script( 'trslider', $this->base_url . '/js/jquery.revolution.min.js', array( 'common-plugins' ), '1.0', true );
	}

	function section_head($clone_id){
		if( !is_front_page() && !pl_draft_mode() ){
 			return;
 		}
		global $post, $pagelines_ID;
		$oset            = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);
		$tmrv_width      = ( $this->opt('tmrv_width', $oset) ) ? $this->opt('tmrv_width', $oset) : '900';
		$tmrv_height     = ( $this->opt('tmrv_height', $oset) ) ? $this->opt('tmrv_height', $oset) : '350';
		$tmrv_shadow     = ( $this->opt('tmrv_shadow', $oset) == 'on' ) ? '0' : '1';
		$tmrv_touch      = ( $this->opt('tmrv_touch', $oset) == 'on' ) ? 'off' : 'on';
		$tmrv_pause_over = ( $this->opt('tmrv_pause_over', $oset) == 'on' ) ? 'off' : 'on';
		$tmrv_items      = ( $this->opt('tmrv_items', $oset) ) ? $this->opt('tmrv_items', $oset) : '10';
		$tmrv_set        = ( $this->opt('tmrv_set', $oset) ) ? $this->opt('tmrv_set', $oset) : '';
		$tmrv_time       = ( $this->opt('tmrv_time', $oset) ) ? $this->opt('tmrv_time', $oset) : '8000';
		$this->slides    = $this->get_posts($this->custom_post_type, $this->tax_id, $tmrv_set, $tmrv_items);

		if( !count( $this->slides ) ){
			return;
		}

	?>
		<script type="text/javascript">
      		jQuery(document).ready(function() {
      			if (jQuery.fn.cssOriginal!=undefined)
					jQuery.fn.css = jQuery.fn.cssOriginal;

	            jQuery('.banner').revolution(
				{
					delay: <?php echo $tmrv_time; ?>,
					startheight: <?php echo $tmrv_height ?>,
					startwidth: <?php echo $tmrv_width ?>,
					navigationType:"bullet",
					navigationStyle:'navbar',
					navigationArrows:'verticalcentered',
					touchenabled: '<?php echo $tmrv_touch ?>',
					onHoverStop: '<?php echo $tmrv_pause_over ?>',
					shadow: '<?php echo $tmrv_shadow ?>',
					fullWidth: 'off'
                });
           });
		</script>
	<?php
	}

 	function section_template( $clone_id = null ) {
 		if( !is_front_page() && !pl_draft_mode()  ){
 			return;
 		}
		global $post, $pagelines_ID;
		$oset         = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);
		$tmrv_items   = ( $this->opt('tmrv_items', $oset) ) ? $this->opt('tmrv_items', $oset) : '10';
		$tmrv_set     = ( $this->opt('tmrv_set', $oset) ) ? $this->opt('tmrv_set', $oset) : '';

		$slides = ( $this->slides == null ) ? $this->get_posts($this->custom_post_type, $this->tax_id, $tmrv_set, $tmrv_items) : $this->slides;
		$current_page_post = $post;

		if( !count($slides) ){
			echo setup_section_notify($this, __('Sorry,there are no slides to display.', $this->domain), get_admin_url().'edit.php?post_type='.$this->custom_post_type, __('Please create some slides', $this->domain));
			return;
		}
 	?>
		<div class="fullwidthbanner-container">
			<div class="banner">
				<ul>
					<?php
						foreach ($slides as $post):
							$io          = array('post_id' => $post->ID);
							$transition  = ( plmeta('tmrv_transition', $io ) )  ? plmeta('tmrv_transition', $io) : 'boxfade';
							$slots       = ( plmeta('tmrv_slots', $io ) )  ? plmeta('tmrv_slots', $io) : '1';
							$use_image   = (plmeta('tmrv_transparent', $io) == 'off') ? true : false;
							$image       = ( plmeta('tmrv_background_slider', $io) ) ? plmeta('tmrv_background_slider', $io) : false;
							$img_src     = ( $image || ($use_image && $image ) ) ? plmeta('tmrv_background_slider', $io) : '/wp-content/themes/sophistique/images/transparent.png';
							$masterspeed = ( plmeta('tmrv_masterspeed', $io ) )  ? plmeta('tmrv_masterspeed', $io) : '300';
							$link        = (plmeta('tmrv_link', $io)) ? 'data-link="' . plmeta('tmrv_link', $io). '"' : '';
							$link_target = (plmeta('tmrv_link_target', $io)) ? 'data-target="'. plmeta('tmrv_link_target', $io) . '"' : '';
							/**************************************************
							* CAPTIONS
							**************************************************/
							$caption_set = strlen( trim( plmeta('tmrv_caption_set', $io)) ) ? plmeta('tmrv_caption_set', $io) : 'null';
							$captions = $this->get_posts($this->custom_cap_post_type, $this->tax_cap_id, $caption_set);
					?>
						<li data-transition="<?php echo $transition ?>" data-slotamount="<?php echo $slots ?>" data-masterspeed="<?php echo $masterspeed ?>" <?php echo $link ?> <?php echo $link_target ?>>
							<img src="<?php echo $img_src ?>">
							<?php if ( count( $captions ) ): ?>
								<?php $current_inner_page_post = $post; ?>
								<?php foreach ( $captions as $post ):
									$ioc               = array('post_id' => $post->ID);
									//Types
									$tmrv_caption_type = ( plmeta('tmrv_caption_type', $ioc) ) ? plmeta('tmrv_caption_type', $ioc) : 'text';
									$tmrv_text         = ( plmeta('tmrv_text', $ioc) ) ? plmeta('tmrv_text', $ioc) : '';
									$tmrv_image        = ( plmeta('tmrv_image', $ioc) ) ? plmeta('tmrv_image', $ioc) : '';
									// Styles
									$tmrv_c_style      = ( plmeta('tmrv_c_style', $ioc) ) ? plmeta('tmrv_c_style', $ioc) : 'big_white';
									$tmrv_video        = ( plmeta('tmrv_video', $ioc) ) ? plmeta('tmrv_video', $ioc) : '';
									$tmrv_i_animation  = ( plmeta('tmrv_incomming_animation', $ioc) ) ? plmeta('tmrv_incomming_animation', $ioc) : 'sft';
									$tmrv_o_animation  = ( plmeta('tmrv_outgoing_animation', $ioc) ) ? plmeta('tmrv_outgoing_animation', $ioc) : 'stt';
									// Datas
									$tmrv_start_x      = ( plmeta('tmrv_start_x', $ioc) ) ? plmeta('tmrv_start_x', $ioc) : '0';
									$tmrv_start_y      = ( plmeta('tmrv_start_y', $ioc) ) ? plmeta('tmrv_start_y', $ioc) : '0';
									$tmrv_speed_intro  = ( plmeta('tmrv_speed_intro', $ioc) ) ? plmeta('tmrv_speed_intro', $ioc) : '300';
									$tmrv_speed_end    = ( plmeta('tmrv_speed_end', $ioc) ) ? plmeta('tmrv_speed_end', $ioc) : '300';
									$tmrv_start_after  = ( plmeta('tmrv_start_after', $ioc) ) ? plmeta('tmrv_start_after', $ioc) : '0';
									$tmrv_easing_intro = ( plmeta('tmrv_easing_intro', $ioc) ) ? plmeta('tmrv_easing_intro', $ioc) : 'linear';
									$tmrv_easing_out   = ( plmeta('tmrv_easing_out', $ioc) ) ? plmeta('tmrv_easing_out', $ioc) : 'linear';
								?>
									<div
										class="caption <?php echo $tmrv_i_animation; ?> <?php echo $tmrv_o_animation ?> <?php echo $tmrv_c_style ?>"
										data-x="<?php echo $tmrv_start_x ?>"
										data-y="<?php echo $tmrv_start_y ?>"
										data-speed="<?php echo $tmrv_speed_intro ?>"
										data-start="<?php echo $tmrv_start_after ?>"
										data-easing="<?php echo $tmrv_easing_intro ?>"
										data-endspeed="<?php echo $tmrv_speed_end ?>"
										data-endeasing="<?php echo $tmrv_easing_out?>"
									>
										<?php switch ($tmrv_caption_type) {
											case 'text':
												echo $tmrv_text;
												break;
											case 'image':
												echo "<img src='".$tmrv_image."' />";
												break;
											case 'video':
												echo $tmrv_video;
												break;
										} ?>
									</div>
								<?php endforeach; $post = $current_inner_page_post; ?>
							<?php endif ?>
						</li>
					<?php endforeach; $post = $current_page_post; ?>
				</ul>
				<div class="tp-bannertimer"></div>
			</div>
		</div>
 	<?php
	}

	function before_section_template( $clone_id = null ){}

	function after_section_template( $clone_id = null ){}

	function post_meta_setup()
	{
		/**********************************************************************
		* Slider meta options
		**********************************************************************/
		$pt_tab_options = array(

			'tmrv_background_slider' => array(
				'type'       => 'image_upload',
				'inputlabel' => __('Slide Background', $this->domain),
				'title'      => __('Slide Background', $this->domain),
				'shortexp'   => __('Background Image.', $this->domain),
				'exp'        => __('Please select a image to use as a slide background.', $this->domain)
			),
			'tmrv_transparent' => array(
				'type'         => 'select',
				'inputlabel'   => __('', $this->domain),
				'title'        => __('Transparent Backgound', $this->domain),
				'shortexp'     => __('Do not use a background image', $this->domain),
				'exp'          => __('With this option youcan choose if you don\'t want to use a background in the slide. If a image is upload this setting is override and will use the image as a background', $this->domain),
				'selectvalues' => array(
					'off' => array('name' => __('Use the image provided', $this->domain)),
					'on'  => array('name' => __('Do not use a background', $this->domain))
				)
			),
			'tmrv_transition' => array(
				'type' => 'select',
				'inputlabel' => __('Select the slide transition effect', $this->domain),
				'title' => __('Slide transition effect', $this->domain),
				'shortexp' => __('Transition effect', $this->domain),
				'exp' => __('Every slide can have a different transition you can choose it in this option.', $this->domain),
				'selectvalues' => array(
					'boxslide'             => array('name' => __('Box Slide', $this->domain)),
					'boxfade'              => array('name' => __('Box Fade', $this->domain)),
					'slotzoom-horizontal'  => array('name' => __('Slot Zoom Horizontal', $this->domain)),
					'slotslide-horizontal' => array('name' => __('Slot Slide Horizontal', $this->domain)),
					'slotfade-horizontal'  => array('name' => __('Slot Fade Horizontal', $this->domain)),
					'slotzoom-vertical'    => array('name' => __('Slot Zoom Vertical', $this->domain)),
					'slotslide-vertical'   => array('name' => __('Slot Slide Vertical', $this->domain)),
					'slotfade-vertical'    => array('name' => __('Slot Fade Vertical', $this->domain)),
					'curtain-1'            => array('name' => __('Curtain 1', $this->domain)),
					'curtain-2'            => array('name' => __('Curtain 2', $this->domain)),
					'curtain-3'            => array('name' => __('Curtain 3', $this->domain)),
					'slideleft'            => array('name' => __('Slide Left', $this->domain)),
					'slideright'           => array('name' => __('Slide Right', $this->domain)),
					'slideup'              => array('name' => __('Slide Up', $this->domain)),
					'slidedown'            => array('name' => __('Slide Down', $this->domain)),
					'fade'                 => array('name' => __('Fade', $this->domain)),
					'random'               => array('name' => __('Random', $this->domain)),
					'slidehorizontal'      => array('name' => __('Slide Horizontal', $this->domain)),
					'slidevertical'        => array('name' => __('Slide Vertical', $this->domain)),
					'papercut'             => array('name' => __('Papercut', $this->domain)),
					'flyin'                => array('name' => __('Flyin', $this->domain)),
					'turnoff'              => array('name' => __('Turnoff', $this->domain)),
					'cube'                 => array('name' => __('Cube', $this->domain)),
					'3dcurtain-vertical'   => array('name' => __('3d Curtain Vertical', $this->domain)),
					'3dcurtain-horizontal' => array('name' => __('3d Curtain Horizontal', $this->domain)),
				)
			),
			'tmrv_masterspeed' => array(
				'type'         => 'select',
				'inputlabel'   => __('Time', $this->domain),
				'title'        => __('Slide Transition Duration', $this->domain) ,
				'shortexp'     => __('Default: 300', $this->domain) ,
				'exp'          => __('Transition speed.', $this->domain),
				'selectvalues' => $this->getMasterSpeedOptions()
			),
			'tmrv_slots' => array(
				'type'         => 'count_select',
				'inputlabel'   => __('Slot Amount', $this->domain),
				'title'        => __('Slot Amount', $this->domain),
				'shortexp'     => __('How many slot use in the slide', $this->domain),
				'exp'          => __('The number of slots or boxes the slide is divided into. If you use Box Fade, over 7 slots can be juggy', $this->domain),
				'count_start'  => 1,
				'count_number' => 20
			),
			'tmrv_caption_set' 	=> array(
				'type' 			=> 'select_taxonomy',
				'taxonomy_id'	=> $this->tax_cap_id,
				'title' 		=> __('Caption Set', $this->domain),
				'shortexp'		=> __('Select which <strong>caption set</strong> you want to show over the image.', $this->domain),
				'inputlabel'	=> __('Caption Set', $this->domain),
				'exp' 			=> __('Each slide can have several captions on it, choose a caption set to show on this slide.', $this->domain)
			),
			/*'tmrv_link' => array(
				'type'       => 'text',
				'inputlabel' => __('Slide link', $this->domain),
				'title'      => __('Slide link', $this->domain),
				'shortexp'   => __('Optional link for the slide', $this->domain),
				'exp'        => __('A link on the whole slide pic', $this->domain)
			),
			'tmrv_link_target' => array(
				'type'         => 'select',
				'inputlabel'   => __('Slide link target', $this->domain),
				'title'        => __('Slide link target', $this->domain),
				'shortexp'     => __('Default: _self', $this->domain),
				'exp'          => __('Link Target', $this->domain),
				'selectvalues' => array(
					'_blank' => array('name' => '_blank'),
					'_self'  => array('name' => '_self')
				)
			),*/
		);

		$pt_panel = array(
			'id' 		=> $this->id . '-metapanel',
			'name' 		=> __('Slider Options', $this->domain),
			'posttype' 	=> array( $this->custom_post_type ),
		);
		$pt_panel =  new PageLinesMetaPanel( $pt_panel );
		$pt_tab = array(
			'id' 		=> $this->id . '-metatab',
			'name' 		=> "Slider Options",
			'icon' 		=> $this->icon,
		);
		$pt_panel->register_tab( $pt_tab, $pt_tab_options );

		/**********************************************************************
		* Captions meta options
		**********************************************************************/
		$pt_tab_options_captions = array(

			'tmrv_caption_type' => array(
				'type'         => 'select',
				'inputlabel'   => __('Caption type', $this->domain),
				'title'        => __('Caption type', $this->domain),
				'shortexp'     => __('What kind of caption will be?, Default: "Text"', $this->domain),
				'exp'          => __('The "Caption" can be one of three types (Text, Image or Video) please, choose what type of caption you will use, be aware, if you choose "Caption text" only the text\'s field value will be use, if you choose "Caption image" only the image\'s field value will be use and so on.', $this->domain),
				'selectvalues' => array(
					'text'  => array('name' => __('Text', $this->domain)),
					'image' => array('name' => __('Image', $this->domain)),
					'Video' => array('name' => __('Video', $this->domain)),
				)
			),
			'tmrv_text' => array(
				'type'       => 'text',
				'inputlabel' => __('Caption Text', $this->domain),
				'title'      => __('Caption Text', $this->domain),
				'shortexp'   => __('The caption text value', $this->domain),
				'exp'        => __('If you chose "Text" in the "Caption type" option, the value on this field will be use, regardless of the value of the image or video fields.', $this->domain)
			),
			'tmrv_image' => array(
				'type'       => 'image_upload',
				'inputlabel' => __('Caption Image'),
				'title'      => 'Caption Image',
				'shortexp'   => __('The caption image value', $this->domain),
				'exp'        => __('If you chose "Image" in the "Caption type" option, the value on this field will be use, regardless of the value of the text or video fields.', $this->domain)
 			),
 			'tmrv_video' => array(
 				'type'       => 'textarea',
				'inputlabel' => __('Caption Video'),
				'title'      => 'Caption Video',
				'shortexp'   => __('The caption video value', $this->domain),
				'exp'        => __('If you chose "Video" in the "Caption type" option, the value on this field will be use, regardless of the value of the text or image fields.', $this->domain)
 			),
			'tmrv_incomming_animation' => array(
				'type'         => 'select',
				'inputlabel'   => __('Incoming Animation', $this->domain),
				'title'        => __('Incoming Animation', $this->domain),
				'shortexp'     => __('Select the incoming animation for the caption.', $this->domain),
				'exp'          => __('You can set a incoming animation for each of the caption.',$this->domain),
				'selectvalues' => array(
					'sft'          => array('name' => __('Short from Top', $this->domain) ),
					'sfb'          => array('name' => __('Short from Bottom', $this->domain) ),
					'sfr'          => array('name' => __('Short from Right', $this->domain) ),
					'sfl'          => array('name' => __('Short from Left', $this->domain) ),
					'lft'          => array('name' => __('Long from Top', $this->domain) ),
					'lfb'          => array('name' => __('Long from Bottom', $this->domain) ),
					'lfr'          => array('name' => __('Long from Right', $this->domain) ),
					'lfl'          => array('name' => __('Long from Left', $this->domain) ),
					'fade'         => array('name' => __('Fading', $this->domain) ),
					'randomrotate' => array('name' => __('Fade in, Rotate from a Random position and Degree') )
				)
			),
			'tmrv_outgoing_animation' => array(
				'type'         => 'select',
				'inputlabel'   => __('Outgoing Animation', $this->domain),
				'title'        => __('Outgoing Animation', $this->domain),
				'shortexp'     => __('Select the outgoing animation for the caption.', $this->domain),
				'exp'          => __('You can set a outgoing animation for each of the caption.',$this->domain),
				'selectvalues' => array(
					'stt'             => array('name' => __('Short to Top', $this->domain)),
					'stb'             => array('name' => __('Short to Bottom', $this->domain)),
					'str'             => array('name' => __('Short to Right', $this->domain)),
					'stl'             => array('name' => __('Short to Left', $this->domain)),
					'ltt'             => array('name' => __('Long to Top', $this->domain)),
					'ltb'             => array('name' => __('Long to Bottom', $this->domain)),
					'ltr'             => array('name' => __('Long to Right', $this->domain)),
					'ltl'             => array('name' => __('Long to Left', $this->domain)),
					'fadeout'         => array('name' => __('Fading', $this->domain)),
					'randomrotateout' => array('name' => __('Fade in, Rotate from a Random position and Degree', $this->domain))
				)
			),
			'tmrv_start_x' => array(
				'type'       => 'text',
				'inputlabel' => __('Horizontal Position', $this->domain),
				'title'      => __('Horizontal Position', $this->domain),
				'shortexp'   => __('The initial horizontal position for the caption.', $this->domain),
				'exp'        => __('The horizontal position based on the slider size, in the resposive view this position will be calculated.', $this->domain)
			),
			'tmrv_start_y' => array(
				'type'       => 'text',
				'inputlabel' => __('Vertical Position', $this->domain),
				'title'      => __('Vertical Position', $this->domain),
				'shortexp'   => __('The initial vertical position for the caption.', $this->domain),
				'exp'        => __('The vertical position based on the slider size, in the resposive view this position will be calculated.', $this->domain)
			),
			'tmrv_c_style' => array(
				'type'         => 'select',
				'inputlabel'   => __('Caption style', $this->domain),
				'title'        => __('Caption style', $this->domain),
				'shortexp'     => __('Select the caption style'),
				'exp'          => __('This option will be used only for text captions.', $this->domain),
				'selectvalues' => array(
					'big_white'       => array('name' => __('Big White')),
					'big_orange'      => array('name' => __('Big Orange')),
					'big_black'       => array('name' => __('Big Black')),
					'medium_white'    => array('name' => __('Medium Grey')),
					'medium_text'     => array('name' => __('Medium White')),
					'small_white'     => array('name' => __('Small White')),
					'large_text'      => array('name' => __('Large White')),
					'very_large_text' => array('name' => __('Very Large White')),
					'very_big_white'  => array('name' => __('Very Big White')),
					'very_big_black'  => array('name' => __('Very Big Black')),
				)
			),
			'tmrv_speed_intro' => array(
				'type'       => 'text',
				'inputlabel' => __('Animation duration intro', $this->domain),
				'title'      => __('Animation duration intro', $this->domain),
				'shortexp'   => __('Duration of the animation in milliseconds', $this->domain),
				'exp'        => __('Take note that 1 second is equal to 1000 milliseconds.', $this->domain)
			),
			'tmrv_speed_end' => array(
				'type'       => 'text',
				'inputlabel' => __('Animation duration out', $this->domain),
				'title'      => __('Animation duration out', $this->domain),
				'shortexp'   => __('Duration of the out animation in milliseconds', $this->domain),
				'exp'        => __('Take note that 1 second is equal to 1000 milliseconds.', $this->domain)
			),
			'tmrv_start_after' => array(
				'type'       => 'text',
				'inputlabel' => __('Time to wait', $this->domain),
				'title'      => __('Time to wait to show this caption', $this->domain),
				'shortexp'   => __('How many time should this caption start to show in milliseconds', $this->domain),
				'exp'        => __('Take note that 1 second is equal to 1000 milliseconds.', $this->domain)
			),
			'tmrv_easing_intro' => array(
				'type'         => 'select',
				'inputlabel'   => __('Easing intro effect', $this->domain),
				'title'        => __('Easing intro effect', $this->domain),
				'shortexp'     => __('Easing effect of the intro animation', $this->domain),
				'exp'          => __('You can set a different easing effect for each caption, default is linear', $this->domain),
				'selectvalues' => $this->getEasing()
			),
			'tmrv_easing_out' => array(
				'type'         => 'select',
				'inputlabel'   => __('Easing out effect', $this->domain),
				'title'        => __('Easing out effect', $this->domain),
				'shortexp'     => __('Easing effect of the out animation', $this->domain),
				'exp'          => __('You can set a different easing effect for each caption, default is linear', $this->domain),
				'selectvalues' => $this->getEasing()
			),
		);
		$pt_panel_cap = array(
			'id' 		=> $this->id . 'cap-metapanel',
			'name' 		=> __('Revolution Caption Options', $this->domain),
			'posttype' 	=> array( $this->custom_cap_post_type ),
		);
		$pt_panel_cap =  new PageLinesMetaPanel( $pt_panel_cap );
		$pt_tab_cap = array(
			'id' 		=> $this->id . 'cap-metatab',
			'name' 		=> "Caption Options",
			'icon' 		=> $this->icon,
		);
		$pt_panel_cap->register_tab( $pt_tab_cap, $pt_tab_options_captions );

	}

	function section_opts(){
		$opts = array(
			array(
				'key' => 'tmrv_size',
				'type'         => 'multi',
				'title'        => __('Slider Size', $this->domain) ,
				'help'          => __('Fully resizable, you can set any size.', $this->domain),
				'opts' => array(
					array(
						'key' => 'tmrv_width',
						'type' => 'text',
						'label' => 'Width',
					),
					array(
						'key' => 'tmrv_height',
						'type' => 'text',
						'label' => 'Height',
					)
				)
			),
			array(
				'key' => 'tmrv_set',
				'type' 			=> 'select_taxonomy',
				'taxonomy_id'	=> $this->tax_id,
				'title' 		=> __('Sliders Set', $this->domain),
				'help'		=> __('Select the set you want to show.', $this->domain),
				'ref' 			=> __('If don\'t select a set or you have not created a set, the slider will show all slides', $this->domain)
			),
			array(
				'key' => 'tmrv_items',
				'type' 			=> 'count_select',
				'label'	=> __('Number of Slides', $this->domain),
				'title' 		=> __('Number of Slides', $this->domain),
				'help'		=> __('Default value is 10', $this->domain),
				'count_start'	=> 2,
 				'count_number'	=> 20,
			),
			array(
				'key' => 'tmrv_time',
				'type' 			=> 'select',
				'label'			=> __('Delay ', $this->domain),
				'title' 		=> __('Slide delay time', $this->domain),
				'shortexp'		=> __('Default value is 8000', $this->domain),
				'help'			=> __('The time one slide stays on the screen in Milliseconds.', $this->domain),
				'opts'			=> $this->getMasterSpeedOptions(20, 1000)
			),
			array(
				'key' => 'tmrv_shadow',
				'type'       => 'check',
				'label' => __('Disable shadow?', $this->domain),
				'title'      => __('Shadow', $this->domain) ,
				'help'   => __('Set whether to use the shadow of the slider', $this->domain) 
			),
			array(
				'key' => 'tmrv_touch',
				'type'       => 'check',
				'label' => __('Disable touch support for mobiles?', $this->domain),
				'title'      => __('Touch Wipe', $this->domain) ,
				'help'   => __('Set whether to use the touch support for mobiles', $this->domain)

			),
			array(
				'key' => 'tmrv_pause_over',
				'type'       => 'check',
				'inputlabel' => __('Disable Pause on hover?', $this->domain),
				'title'      => __('Pause on hover', $this->domain) ,
				'help'   => __('Set whether to use the pause on hover feature', $this->domain)

			)
		);
		return $opts;
	}

	function post_type_slider_setup()
	{
		$args = array(
			'label'          => __('Revolution slides', $this->domain),
			'singular_label' => __('Slide', $this->domain),
			'description'    => __('', $this->domain),
			'taxonomies'     => array( $this->tax_id ),
			'menu_icon'      => $this->icon,
			'supports'       => 'title'
		);
		$taxonomies = array(
			$this->tax_id => array(
				'label'          => __('Revolution Sets', $this->domain),
				'singular_label' => __('Revolution Set', $this->domain),
			)
		);
		$columns = array(
			"cb"              => "<input type=\"checkbox\" />",
			"title"           => "Title",
			$this->tax_id     => "Revolution Set"
		);
		$this->post_type = new PageLinesPostType( $this->custom_post_type, $args, $taxonomies, $columns, array(&$this, 'column_display') );
	}

	function post_type_caption_setup()
	{
		$args = array(
			'label'          => __('Revolution captions', $this->domain),
			'singular_label' => __('Caption', $this->domain),
			'description'    => __('', $this->domain),
			'taxonomies'     => array( $this->tax_cap_id ),
			'menu_icon'      => $this->icon,
			'supports'       => 'title'
		);
		$taxonomies = array(
			$this->tax_cap_id => array(
				'label'          => __('Caption Sets', $this->domain),
				'singular_label' => __('Caption Set', $this->domain),
			)
		);
		$columns = array(
			"cb"              => "<input type=\"checkbox\" />",
			"title"           => "Title",
			$this->tax_cap_id => "Caption Set"
		);
		$this->post_type_cap = new PageLinesPostType( $this->custom_cap_post_type, $args, $taxonomies, $columns, array(&$this, 'column_cap_display') );
	}

	function column_display($column){
		global $post;
		switch ($column){
			case $this->tax_id:
				echo get_the_term_list($post->ID, $this->tax_id, '', ', ','');
				break;
		}
	}

	function column_cap_display($column){
		global $post;
		switch ($column){
			case $this->tax_cap_id:
				echo get_the_term_list($post->ID, $this->tax_cap_id, '', ', ','');
				break;
		}
	}

	function get_posts( $custom_post, $tax_id, $set = null, $limit = null){
		$query                 = array();
		$query['orderby']      = 'ID';
		$query['post_type']    = $custom_post;
		$query[ $tax_id ] = $set;

		if(isset($limit)){
			$query['showposts'] = $limit;
		}

		$q = new WP_Query($query);

		if(is_array($q->posts))
			return $q->posts;
		else
			return array();
	}

	function getMasterSpeedOptions($times = 20, $multiple = 100)
	{
		$out = array();
		for ($i=2; $i <= $times ; $i++) {
			$mill = $i * $multiple;
			$out[(string)$mill] = array('name' => $mill);
		}
		return $out;
	}

	function getEasing()
	{
		return array(
			'easeEasOutBack'      => array('name' => __('OutBack', $this->domain)),
			'easeInQuad'       => array('name' => __('InQuad', $this->domain)),
			'easeOutQuad'      => array('name' => __('OutQuad', $this->domain)),
			'easeInOutQuad'    => array('name' => __('InOutQuad', $this->domain)),
			'easeInCubic'      => array('name' => __('InCubic', $this->domain)),
			'easeOutCubic'     => array('name' => __('OutCubic', $this->domain)),
			'easeInOutCubic'   => array('name' => __('InOutCubic', $this->domain)),
			'easeInQuart'      => array('name' => __('InQuart', $this->domain)),
			'easeOutQuart'     => array('name' => __('OutQuart', $this->domain)),
			'easeInOutQuart'   => array('name' => __('InOutQuart', $this->domain)),
			'easeInQuint'      => array('name' => __('InQuint', $this->domain)),
			'easeOutQuint'     => array('name' => __('OutQuint', $this->domain)),
			'easeInOutQuint'   => array('name' => __('InOutQuint', $this->domain)),
			'easeInSine'       => array('name' => __('InSine', $this->domain)),
			'easeOutSine'      => array('name' => __('OutSine', $this->domain)),
			'easeInOutSine'    => array('name' => __('InOutSine', $this->domain)),
			'easeInExpo'       => array('name' => __('InExpo', $this->domain)),
			'easeOutExpo'      => array('name' => __('OutExpo', $this->domain)),
			'easeInOutExpo'    => array('name' => __('InOutExpo', $this->domain)),
			'easeInCirc'       => array('name' => __('InCirc', $this->domain)),
			'easeOutCirc'      => array('name' => __('OutCirc', $this->domain)),
			'easeInOutCirc'    => array('name' => __('InOutCirc', $this->domain)),
			'easeInElastic'    => array('name' => __('InElastic', $this->domain)),
			'easeOutElastic'   => array('name' => __('OutElastic', $this->domain)),
			'easeInOutElastic' => array('name' => __('InOutElastic', $this->domain)),
			'easeInBack'       => array('name' => __('InBack', $this->domain)),
			'easeOutBack'      => array('name' => __('OutBack', $this->domain)),
			'easeInOutBack'    => array('name' => __('InOutBack', $this->domain)),
			'easeInBounce'     => array('name' => __('InBounce', $this->domain)),
			'easeOutBounce'    => array('name' => __('OutBounce', $this->domain)),
			'easeInOutBounce'  => array('name' => __('InOutBounce', $this->domain))
		);
	}


}