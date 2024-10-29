<?php
/*
Plugin Name: AdLuge Visitor Tracker
Plugin URI: http://www.adluge.com
Description: Allows you to insert AdLuge Visitor Tracker Script to the footer of your blog by hooking into wp_footer.
Version: 1.0
Author: AdLuge
Author URI: http://www.adluge.com
*/


if ( !class_exists( 'AdLugeVT' ) ) {
	
	define('AdLugeVT_PLUGIN_URL', plugins_url('', __FILE__));
	if (is_admin()) {
		wp_register_style('AdLugeVTStyleSheet1', AdLugeVT_PLUGIN_URL . '/css/AdLugeVT.css');
		wp_enqueue_style( 'AdLugeVTStyleSheet1');
		wp_register_style('AdLugeVTStyleSheet2', AdLugeVT_PLUGIN_URL . '/css/multi-select.css');
		wp_enqueue_style( 'AdLugeVTStyleSheet2');
		
		
	}
	
	class AdLugeVT {

		function AdLugeVT() {
		
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			add_action( 'admin_footer',array( &$this, 'my_action_javascript') );
			add_action( 'wp_footer', array( &$this, 'wp_footer' ),1000 );
		}
			
		function admin_init() {
			register_setting( 'AVT-Scripts', 'AdLugeVT_script', 'trim' );
			register_setting( 'AVT-Scripts', 'AdLugeVT_pages', 'trim' );
		}
		static function install() {
             	
			$option_name ='AdLugeVT_script';
			$new_value = '<script type="text/javascript" src="http://www.adluge.com/trackerjs/visitors-tracker.js"></script>';
				
				if ( get_option( $option_name ) !== false ) {
					update_option( $option_name, $new_value );
				} else {
					$deprecated = null;
					$autoload = 'no';
					add_option( $option_name, $new_value, $deprecated, $autoload );
				}
		}
                
        static function uninstall(){
		
			delete_option( 'AdLugeVT_script'); 
        	delete_option( 'AdLugeVT_pages');	
		}
                
        static function deact(){
                    
            update_option( 'AdLugeVT_script', '' );
			update_option( 'AdLugeVT_pages', '' );				
        }
                
                
		function admin_menu() {
			$hook = add_menu_page('Visitor Tracker','Visitor Tracker','manage_options',__FILE__, array( &$this, 'options_panel' ),AdLugeVT_PLUGIN_URL.'/img/favicon.ico');
			
		}
		
		function wp_footer() {
			if ( !is_admin() && !is_feed() && !is_robots() && !is_trackback() ) {
				$comment_starts = " <!-- AdLuge Visitor Tracking Code Starts Here -->";
				$comment_ends = "<!-- AdLuge Visitor Tracking Code Ends Here -->";
				$text = get_option( 'AdLugeVT_script', '' );
				$text = convert_smilies( $text );
				$text = do_shortcode( $text );
				$page_list = get_option( 'AdLugeVT_pages');		
				$text = $comment_starts."\n".$text."\n".$comment_ends."\n"; 
			if($page_list=='' )
			  {
						echo $text; 
			  }
			  else
			  {
				 $pages = array_diff(get_all_page_ids(),explode(',',$page_list));
				 foreach($pages as $pages_on):
					if(is_page($pages_on))
					{	
						echo $text; 
					}	
				 endforeach;
				if(!is_page())
					echo $text; 
				}
			
			}
		}
		
		
		
		function options_panel() { ?>
		
		<div class="wrap columns-2 dd-wrap">
			<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br /></div>
				<h2><span><img src="<?php echo AdLugeVT_PLUGIN_URL."/img/logo-wp-admin.png"  ?> " width="38px" height="38px"/></span> &nbsp; AdLuge Visitor Tracker</h2>
				<?php 
						if ( isset( $_GET['settings-updated'] ) ) {
							echo "<div class='updated'><p><b>Settings saved successfully.</b></p></div>";
							}
							
						?>
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<?php include("AdLugeVT-sidebar.php"); ?>
					<div id="post-body">
						
						<div id="post-body-content"  >
						
						<form name="dofollowing"  action="options.php" method="post">
						
							
						<div class="stuffbox">
						<h3><label for="link_name">Tracking Script Configuration</label></h3>
						
						
						<div class="inside">

								<table class="form-table">

							        <tr valign="top">
										<th scope="row">Tracking Script:</th>
								        <td>
											<p>The tracking script is placed above the <code>&lt;/body&gt;</code> tag by default.</p>
											<textarea rows="4" cols="45"  id="AdLugeVT_script" name="AdLugeVT_script" readOnly="true" ><?php echo esc_html(get_option( 'AdLugeVT_script' )); ?>
											</textarea>
										</td>
									</tr>						
						<tr valign="top">

								        <th scope="row">
						Pages<br/>
						</th>

								        <td>
										<p>Select the appropriate page(s) to omit the tracking:-</p>
										
										<?php 
								settings_fields( 'AVT-Scripts' ); 
								$insert_page = explode(',', get_option( 'AdLugeVT_pages'));
								$args = array(
															'sort_order' => 'ASC',
															'sort_column' => 'post_title',
															'hierarchical' => 1,
															'exclude' => '',
															'include' => '',
															'meta_key' => '',
															'meta_value' => '',
															'authors' => '',
															'child_of' => 0,
															'parent' => -1,
															'exclude_tree' => '',
															'number' => '',
															'offset' => 0,
															'post_type' => 'page',
															'post_status' => 'publish');
										 
										 
										 $pages = '<select id="list_pages"  name="list_pages[]" multiple="multiple" style="width:200px" size="6"  >';
										 foreach(get_pages($args) as $arr):
											if(is_array($insert_page)){$selected = in_array($arr->ID,$insert_page)?'selected="selected"':'';}
											 $pages.="<option value='$arr->ID'  $selected>$arr->post_title</option>";
										 endforeach;
										 $pages.="</select>";
										 echo $pages;
										 ?>
										 </td>
						</tr>	
						</table>
										
								 <div class="clear"></div>		
								<input type="hidden" id="AdLugeVT_pages" name="AdLugeVT_pages" value=""/>
								<div class="submit">
										<input class="button-primary" id="Reset" onclick="if (confirm('Reset will insert AdLuge Tracking Script in all pages.Are you sure you want to reset ?'))return true;return false" name="reset" value="Reset" type="submit" />	
										<input class="button-primary" id="SaveChanges"  name="SaveChanges" value="SaveChanges" type="submit" />	
								</div>
										
						</div>
					
					</div>
					
					</form>
					<div class="copy_rights">
								<a class="feedback" href="mailto:support@adluge.com?subject=AdLuge%20Visitor%20Tracker%20:%20Feedback&body=%0D%0A">Feedback</a>
								|&copy; <?php echo date("Y"); ?> AdLuge - All Rights Reserved
								</div>
					</div>
				</div>
					</div>
				</div>
				
				<?php
		}
		function my_action_javascript() {
		?>
		<script>
		if(!jQuery){
		<?php
		
		wp_enqueue_script('jquery-1.9.1',"//code.jquery.com/jquery-1.9.1.js");
		wp_enqueue_script('multi-select', AdLugeVT_PLUGIN_URL.'/js/jquery.multi-select.js');
		wp_enqueue_script('main', AdLugeVT_PLUGIN_URL.'/js/main.js');
		
		?>
		}
		</script>
		
		
		<?php
		}
		function do_on_my_plugin_settings_save()
		{
		  
		}	
		
	}
		

	$wp_headers_and_footers = new AdLugeVT();
	 
		register_activation_hook( __FILE__, array( 'AdLugeVT', 'install' ) );	
        register_deactivation_hook( __FILE__, array( 'AdLugeVT', 'deact' ) );
        register_uninstall_hook( __FILE__, array( 'AdLugeVT', 'uninstall' ) );
        
}
?>
