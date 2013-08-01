<?php
/*
Section: Transparent Title
Author: Enrique Chavez
Author URI: http://tmeister.net
Version: 1.0
Description: Inline Description
Class Name: TMTransparenTitle
Filter: full-width
*/


/**
* 
*/
class TMTransparenTitle extends PageLinesSection
{
	
	var $domain               = 'tmTransparentTitle';

	function section_head(){

	}

	function section_template(){
		global $post, $pagelines_ID;
		if( is_front_page() && !pl_draft_mode()  ){
 			return;
 		}
	?>
		
	<div class="pl-content">
		<?php if (!$this->is_blog()): ?>
			<h1 class="pl-animation pl-appear">
				<?php echo get_the_title($pagelines_ID); ?>
			</h1>
		<?php else: ?>
			<h1 class="pl-animation pl-appear" data-sync="tmso_blog_title"><?php echo ( $this->opt('tmso_blog_title') )? $this->opt('tmso_blog_title') : 'Blog' ?></h1>
		<?php endif ?>
	</div>
	
	<?php 
	}
	
	function section_opts(){

		$opts = array(
			array(
				'key' => 'tmso_blog_title',
				'type' 			=> 'text',
				'label' 		=> __('Custom page title', $this->domain),
				'default' => "Some Random Stuff"
			),
			array(
				'key' => 'tmso_blog_title_color',
				'type' => 'color',
				'default' => '#ffffff',
				'label' => __('Blog title color', $this->domain)
			)
		);

		return $opts;

	}
	function is_blog () {
		global  $post;
		$posttype = get_post_type($post);
		if( $posttype == 'page' ){
			return false;
		}
		if( $posttype == 'post' ){
			return ( (is_archive() || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag()) ) ) ? true : false ;
		}
	}
}