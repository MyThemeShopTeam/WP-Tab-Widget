<?php
/*
Plugin Name: WP Tab Widget
Plugin URI: http://mythemeshop.com/plugins/wp-tab-widget/
Description: WP Tab Widget is the AJAXified plugin which loads content by demand, and thus it makes the plugin incredibly lightweight.
Author: MyThemeShop
Version: 1.2.7
Author URI: http://mythemeshop.com/
*/
if ( !class_exists('wpt_widget') ) {
	class wpt_widget extends WP_Widget {
		function __construct() {
	        
	        // add image sizes and load language file
	        add_action( 'init', array(&$this, 'wpt_init') );
	        
			// ajax functions
			add_action('wp_ajax_wpt_widget_content', array(&$this, 'ajax_wpt_widget_content'));
			add_action('wp_ajax_nopriv_wpt_widget_content', array(&$this, 'ajax_wpt_widget_content'));
	        
	        // css
	        add_action('wp_enqueue_scripts', array(&$this, 'wpt_register_scripts'));
	        add_action('admin_enqueue_scripts', array(&$this, 'wpt_admin_scripts'));

			$widget_ops = array('classname' => 'widget_wpt', 'description' => __('Display popular posts, recent posts, comments, and tags in tabbed format.', 'wp-tab-widget'));
			$control_ops = array('width' => 300, 'height' => 350);
			parent::__construct('wpt_widget', __('WP Tab Widget by MyThemeShop', 'wp-tab-widget'), $widget_ops, $control_ops);
	    }	
	    
	    function wpt_init() {
	        load_plugin_textdomain('wp-tab-widget', false, dirname(plugin_basename(__FILE__)) . '/languages/' );
	        
	        add_image_size( 'wp_review_small', 65, 65, true ); // small thumb
	        add_image_size( 'wp_review_large', 320, 240, true ); // large thumb
	    }
	    function wpt_admin_scripts($hook) {
	        if ($hook != 'widgets.php')
	            return;
	        wp_register_script('wpt_widget_admin', plugins_url('js/wpt-admin.js', __FILE__), array('jquery'));  
	        wp_enqueue_script('wpt_widget_admin');
	    }
	    function wpt_register_scripts() { 
			// JS    
			wp_register_script('wpt_widget', plugins_url('js/wp-tab-widget.js', __FILE__), array('jquery'));     
			wp_localize_script( 'wpt_widget', 'wpt',         
				array( 'ajax_url' => admin_url( 'admin-ajax.php' )) 
			);        
			// CSS     
			wp_register_style('wpt_widget', plugins_url('css/wp-tab-widget.css', __FILE__), true);
	    }  
	    	
		function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, array( 
				'tabs' => array('recent' => 1, 'popular' => 1, 'comments' => 0, 'tags' => 0), 
				'tab_order' => array('popular' => 1, 'recent' => 2, 'comments' => 3, 'tags' => 4), 
				'allow_pagination' => 1, 
				'post_num' => '5', 
				'comment_num' => '5', 
				'show_thumb' => 1, 
				'thumb_size' => 'small', 
				'show_date' => 1, 
				'show_excerpt' => 0, 
				'excerpt_length' => apply_filters( 'wpt_excerpt_length_default', '15' ), 
				'show_comment_num' => 0, 
				'show_avatar' => 1, 
				'title_length' => apply_filters( 'wpt_title_length_default', '15' ) ,
				'show_love' => 0, 
			) );
			
			extract($instance);

			?>
	        <div class="wpt_options_form">
	        
	        <h4><?php _e('Select Tabs', 'wp-tab-widget'); ?></h4>
	        
			<div class="wpt_select_tabs">
				<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px" for="<?php echo $this->get_field_id("tabs"); ?>_popular">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("tabs"); ?>_popular" name="<?php echo $this->get_field_name("tabs"); ?>[popular]" value="1" <?php if (isset($tabs['popular'])) { checked( 1, $tabs['popular'], true ); } ?> />
					<?php _e( 'Popular Tab', 'wp-tab-widget'); ?>
				</label>
				<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px;" for="<?php echo $this->get_field_id("tabs"); ?>_recent">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("tabs"); ?>_recent" name="<?php echo $this->get_field_name("tabs"); ?>[recent]" value="1" <?php if (isset($tabs['recent'])) { checked( 1, $tabs['recent'], true ); } ?> />		
					<?php _e( 'Recent Tab', 'wp-tab-widget'); ?>
				</label>
				<label class="alignleft" style="display: block; width: 50%;" for="<?php echo $this->get_field_id("tabs"); ?>_comments">
					<input type="checkbox" class="checkbox wpt_enable_comments" id="<?php echo $this->get_field_id("tabs"); ?>_comments" name="<?php echo $this->get_field_name("tabs"); ?>[comments]" value="1" <?php if (isset($tabs['comments'])) { checked( 1, $tabs['comments'], true ); } ?> />
					<?php _e( 'Comments Tab', 'wp-tab-widget'); ?>
				</label>
				<label class="alignleft" style="display: block; width: 50%;" for="<?php echo $this->get_field_id("tabs"); ?>_tags">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("tabs"); ?>_tags" name="<?php echo $this->get_field_name("tabs"); ?>[tags]" value="1" <?php if (isset($tabs['tags'])) { checked( 1, $tabs['tags'], true ); } ?> />
					<?php _e( 'Tags Tab', 'wp-tab-widget'); ?>
				</label>
			</div>
	        <div class="clear"></div>
	        
	        <h4 class="wpt_tab_order_header"><a href="#"><?php _e('Tab Order', 'wp-tab-widget'); ?></a></h4>
	        
	        <div class="wpt_tab_order" style="display: none;">
	            
	            <label class="alignleft" for="<?php echo $this->get_field_id('tab_order'); ?>_popular" style="width: 50%;">
					<input id="<?php echo $this->get_field_id('tab_order'); ?>_popular" name="<?php echo $this->get_field_name('tab_order'); ?>[popular]" type="number" min="1" step="1" value="<?php echo $tab_order['popular']; ?>" style="width: 48px;" />
	                <?php _e('Popular', 'wp-tab-widget'); ?>
	            </label>
	            <label class="alignleft" for="<?php echo $this->get_field_id('tab_order'); ?>_recent" style="width: 50%;">
					<input id="<?php echo $this->get_field_id('tab_order'); ?>_recent" name="<?php echo $this->get_field_name('tab_order'); ?>[recent]" type="number" min="1" step="1" value="<?php echo $tab_order['recent']; ?>" style="width: 48px;" />
	                <?php _e('Recent', 'wp-tab-widget'); ?>
	            </label>
	            <label class="alignleft" for="<?php echo $this->get_field_id('tab_order'); ?>_comments" style="width: 50%;">
					<input id="<?php echo $this->get_field_id('tab_order'); ?>_comments" name="<?php echo $this->get_field_name('tab_order'); ?>[comments]" type="number" min="1" step="1" value="<?php echo $tab_order['comments']; ?>" style="width: 48px;" />
				    <?php _e('Comments', 'wp-tab-widget'); ?>
	            </label>
	            <label class="alignleft" for="<?php echo $this->get_field_id('tab_order'); ?>_tags" style="width: 50%;">
					<input id="<?php echo $this->get_field_id('tab_order'); ?>_tags" name="<?php echo $this->get_field_name('tab_order'); ?>[tags]" type="number" min="1" step="1" value="<?php echo $tab_order['tags']; ?>" style="width: 48px;" />
				    <?php _e('Tags', 'wp-tab-widget'); ?>
	            </label>
	        </div>
			<div class="clear"></div>
	        
	        <h4 class="wpt_advanced_options_header"><a href="#"><?php _e('Advanced Options', 'wp-tab-widget'); ?></a></h4>
	        
	        <div class="wpt_advanced_options" style="display: none;">
	        <p>
				<label for="<?php echo $this->get_field_id("allow_pagination"); ?>">				
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("allow_pagination"); ?>" name="<?php echo $this->get_field_name("allow_pagination"); ?>" value="1" <?php if (isset($allow_pagination)) { checked( 1, $allow_pagination, true ); } ?> />
					<?php _e( 'Allow pagination', 'wp-tab-widget'); ?>
				</label>
			</p>
			
			<div class="wpt_post_options">

	        <p>
				<label for="<?php echo $this->get_field_id('post_num'); ?>"><?php _e('Number of posts to show:', 'wp-tab-widget'); ?>
					<br />
					<input id="<?php echo $this->get_field_id('post_num'); ?>" name="<?php echo $this->get_field_name('post_num'); ?>" type="number" min="1" step="1" value="<?php echo $post_num; ?>" />
				</label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('title_length'); ?>"><?php _e('Title length (words):', 'wp-tab-widget'); ?>
					<br />
					<input id="<?php echo $this->get_field_id('title_length'); ?>" name="<?php echo $this->get_field_name('title_length'); ?>" type="number" min="1" step="1" value="<?php echo $title_length; ?>" />
				</label>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id("show_thumb"); ?>">
					<input type="checkbox" class="checkbox wpt_show_thumbnails" id="<?php echo $this->get_field_id("show_thumb"); ?>" name="<?php echo $this->get_field_name("show_thumb"); ?>" value="1" <?php if (isset($show_thumb)) { checked( 1, $show_thumb, true ); } ?> />
					<?php _e( 'Show post thumbnails', 'wp-tab-widget'); ?>
				</label>
			</p>   
			
			<p class="wpt_thumbnail_size"<?php echo (empty($show_thumb) ? ' style="display: none;"' : ''); ?>>
				<label for="<?php echo $this->get_field_id('thumb_size'); ?>"><?php _e('Thumbnail size:', 'wp-tab-widget'); ?></label> 
				<select id="<?php echo $this->get_field_id('thumb_size'); ?>" name="<?php echo $this->get_field_name('thumb_size'); ?>" style="margin-left: 12px;">
					<option value="small" <?php selected($thumb_size, 'small', true); ?>><?php _e('Small', 'wp-tab-widget'); ?></option>
					<option value="large" <?php selected($thumb_size, 'large', true); ?>><?php _e('Large', 'wp-tab-widget'); ?></option>    
				</select>       
			</p>	
			
			<p>			
				<label for="<?php echo $this->get_field_id("show_date"); ?>">	
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_date"); ?>" name="<?php echo $this->get_field_name("show_date"); ?>" value="1" <?php if (isset($show_date)) { checked( 1, $show_date, true ); } ?> />	
					<?php _e( 'Show post date', 'wp-tab-widget'); ?>	
				</label>	
			</p>
	        
			<p>		
				<label for="<?php echo $this->get_field_id("show_comment_num"); ?>">		
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_comment_num"); ?>" name="<?php echo $this->get_field_name("show_comment_num"); ?>" value="1" <?php if (isset($show_comment_num)) { checked( 1, $show_comment_num, true ); } ?> />	
					<?php _e( 'Show number of comments', 'wp-tab-widget'); ?>		
				</label>	
			</p>    
			
			<p>			
				<label for="<?php echo $this->get_field_id("show_excerpt"); ?>">	
					<input type="checkbox" class="checkbox wpt_show_excerpt" id="<?php echo $this->get_field_id("show_excerpt"); ?>" name="<?php echo $this->get_field_name("show_excerpt"); ?>" value="1" <?php if (isset($show_excerpt)) { checked( 1, $show_excerpt, true ); } ?> />
					<?php _e( 'Show post excerpt', 'wp-tab-widget'); ?>
				</label>		
			</p>
			
			<p class="wpt_excerpt_length"<?php echo (empty($show_excerpt) ? ' style="display: none;"' : ''); ?>>
				<label for="<?php echo $this->get_field_id('excerpt_length'); ?>">
					<?php _e('Excerpt length (words):', 'wp-tab-widget'); ?>   
					<br />
					<input type="number" min="1" step="1" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" value="<?php echo $excerpt_length; ?>" />
				</label>
			</p>	
			  
			</div>
	        <div class="clear"></div>
	        
	        <div class="wpt_comment_options"<?php echo (empty($tabs['comments']) ? ' style="display: none;"' : ''); ?>>
			
	        <p>
				<label for="<?php echo $this->get_field_id('comment_num'); ?>">
					<?php _e('Number of comments on Comments Tab:', 'wp-tab-widget'); ?>
					<br />
					<input type="number" min="1" step="1" id="<?php echo $this->get_field_id('comment_num'); ?>" name="<?php echo $this->get_field_name('comment_num'); ?>" value="<?php echo $comment_num; ?>" />
				</label>			
			</p>      
			
			<p>			
				<label for="<?php echo $this->get_field_id("show_avatar"); ?>">			
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_avatar"); ?>" name="<?php echo $this->get_field_name("show_avatar"); ?>" value="1" <?php if (isset($show_avatar)) { checked( 1, $show_avatar, true ); } ?> />
					<?php _e( 'Show avatars on Comments Tab', 'wp-tab-widget'); ?>	
				</label>	
			</p>
			</div><!-- .wpt_comment_options -->
			</div><!-- .wpt_advanced_options -->
			<p>
				<label for="<?php echo $this->get_field_id("show_love"); ?>">			
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_love"); ?>" name="<?php echo $this->get_field_name("show_love"); ?>" value="1" <?php if (isset($show_love)) { checked( 1, $show_love, true ); } ?> />
					<?php _e( 'Show Some Love (Powered by Tab Widget Pro)', 'wp-tab-widget'); ?>
				</label>
			</p>
	        <a href="https://mythemeshop.com/plugins/wp-tab-widget-pro/?utm_source=WP+Tab+Widget+Free&utm_medium=Banner+CPC&utm_content=WP+Tab+Widget+Pro+LP&utm_campaign=WordPressOrg&wpmts" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>/img/wp-tab-widget-pro.jpg" style="width:100%; max-width: 100%; margin-bottom: 10px;"></a>
			</div><!-- .wpt_options_form -->
			<?php 
		}	
		
		function update( $new_instance, $old_instance ) {	
			$instance = $old_instance;    
			$instance['tabs'] = $new_instance['tabs'];  
	        $instance['tab_order'] = $new_instance['tab_order'];  
			$instance['allow_pagination'] = $new_instance['allow_pagination'];	
			$instance['post_num'] = $new_instance['post_num'];	
			$instance['title_length'] = $new_instance['title_length'];	
			$instance['comment_num'] =  $new_instance['comment_num'];		
			$instance['show_thumb'] = $new_instance['show_thumb'];     
			$instance['thumb_size'] = $new_instance['thumb_size'];		
			$instance['show_date'] = $new_instance['show_date'];    
			$instance['show_excerpt'] = $new_instance['show_excerpt'];  
			$instance['excerpt_length'] = $new_instance['excerpt_length'];	
			$instance['show_comment_num'] = $new_instance['show_comment_num'];  
			$instance['show_avatar'] = $new_instance['show_avatar'];	
			$instance['show_love'] = $new_instance['show_love'];	
			return $instance;	
		}	
		function widget( $args, $instance ) {	
			extract($args);     
			extract($instance);    
			wp_enqueue_script('wpt_widget'); 
			wp_enqueue_style('wpt_widget');  
			if (empty($tabs)) $tabs = array('recent' => 1, 'popular' => 1);    
			$tabs_count = count($tabs);     
			if ($tabs_count <= 1) {       
				$tabs_count = 1;       
			} elseif($tabs_count > 3) {   
				$tabs_count = 4;      
			}
	        
	        $available_tabs = array('popular' => __('Popular', 'wp-tab-widget'), 
	            'recent' => __('Recent', 'wp-tab-widget'), 
	            'comments' => __('Comments', 'wp-tab-widget'), 
	            'tags' => __('Tags', 'wp-tab-widget'));
	            
	        array_multisort($tab_order, $available_tabs);
	        
	        $show_love = !empty($instance['show_love']);
			?>	
			<?php echo $before_widget; ?>	
			<div class="wpt_widget_content" id="<?php echo $widget_id; ?>_content" data-widget-number="<?php echo esc_attr( $this->number ); ?>">	
				<ul class="wpt-tabs <?php echo "has-$tabs_count-"; ?>tabs">
	                <?php foreach ($available_tabs as $tab => $label) { ?>
	                    <?php if (!empty($tabs[$tab])): ?>
	                        <li class="tab_title"><a href="#" id="<?php echo $tab; ?>-tab"><?php echo $label; ?></a></li>	
	                    <?php endif; ?>
	                <?php } ?> 
				</ul> <!--end .tabs-->	
				<div class="clear"></div>  
				<div class="inside">        
					<?php if (!empty($tabs['popular'])): ?>	
						<div id="popular-tab-content" class="tab-content">				
						</div> <!--end #popular-tab-content-->       
					<?php endif; ?>       
					<?php if (!empty($tabs['recent'])): ?>	
						<div id="recent-tab-content" class="tab-content"> 		 
						</div> <!--end #recent-tab-content-->		
					<?php endif; ?>                     
					<?php if (!empty($tabs['comments'])): ?>      
						<div id="comments-tab-content" class="tab-content"> 	
							<ul>                    		
							</ul>		
						</div> <!--end #comments-tab-content-->     
					<?php endif; ?>            
					<?php if (!empty($tabs['tags'])): ?>       
						<div id="tags-tab-content" class="tab-content"> 	
							<ul>                    	
							</ul>			 
						</div> <!--end #tags-tab-content-->  
					<?php endif; ?>
					<div class="clear"></div>
				</div> <!--end .inside -->
				<?php if ( $show_love ) { ?>
				<a href="https://mythemeshop.com/plugins/wp-review-pro/?utm_source=WP+Tab+Widget&amp;utm_medium=Link+CPC&amp;utm_content=WP+Tab+Widget+Pro+LP&amp;utm_campaign=WordPressOrg" class="wpt_show_love"><?php _e('Powered by WP Tab Widget', 'wp-tab-widget'); ?></a>
				<?php } ?>
				<div class="clear"></div>
			</div><!--end #tabber -->
			<?php    
			// inline script 
			// to support multiple instances per page with different settings   
			
			unset($instance['tabs'], $instance['tab_order']); // unset unneeded  
			?>  
			<script type="text/javascript">  
				jQuery(function($) {    
					$('#<?php echo $widget_id; ?>_content').data('args', <?php echo json_encode($instance); ?>);  
				});  
			</script>  
			<?php echo $after_widget; ?>
			<?php 
		}  
		
		 
		function ajax_wpt_widget_content() {     
			$tab = $_POST['tab'];       
			$args = $_POST['args'];  
	    	$number = intval( $_POST['widget_number'] );
			$page = intval($_POST['page']);    
			if ($page < 1)        
				$page = 1;

			if ( !is_array( $args ) || empty( $args ) ) { // json_encode() failed
				$wpt_widgets = new wpt_widget();
				$settings = $wpt_widgets->get_settings();

				if ( isset( $settings[ $number ] ) ) {
					$args = $settings[ $number ];
				} else {
					die( __('Unable to load tab content', 'wp-tab-widget') );
				}
			}
	    	
	        
			// sanitize args		
			$post_num = (empty($args['post_num']) ? 5 : intval($args['post_num']));    
			if ($post_num > 20 || $post_num < 1) { // max 20 posts
				$post_num = 5;   
			}      
			$comment_num = (empty($args['comment_num']) ? 5 : intval($args['comment_num']));   
			if ($comment_num > 20 || $comment_num < 1) {  
				$comment_num = 5;    
			}       
			$show_thumb = !empty($args['show_thumb']);
			$thumb_size = $args['thumb_size'];
	        if ($thumb_size != 'small' && $thumb_size != 'large') {
	            $thumb_size = 'small'; // default
	        }
			$show_date = !empty($args['show_date']);     
			$show_excerpt = !empty($args['show_excerpt']);  
			$excerpt_length = intval($args['excerpt_length']);
	        if ($excerpt_length > 50 || $excerpt_length < 1) {  
				$excerpt_length = 10;   
			}   
			$show_comment_num = !empty($args['show_comment_num']);  
			$show_avatar = !empty($args['show_avatar']);   
			$allow_pagination = !empty($args['allow_pagination']);

			$title_length = ! empty($args['title_length']) ? $args['title_length'] : apply_filters( 'wpt_title_length_default', '15' );
	        
			/* ---------- Tab Contents ---------- */    
			switch ($tab) {        
			  
				/* ---------- Popular Posts ---------- */   
				case "popular":      
					?>       
					<ul>				
						<?php 
						$popular = new WP_Query( array('ignore_sticky_posts' => 1, 'posts_per_page' => $post_num, 'post_status' => 'publish', 'orderby' => 'meta_value_num', 'meta_key' => '_wpt_view_count', 'order' => 'desc', 'paged' => $page));         
						$last_page = $popular->max_num_pages;      
						while ($popular->have_posts()) : $popular->the_post(); ?>	
							<li>
								<?php if ( $show_thumb == 1 ) : ?>			
									<div class="wpt_thumbnail wpt_thumb_<?php echo $thumb_size; ?>">	
	                                    <a title="<?php the_title(); ?>" href="<?php the_permalink() ?>">		
	    									<?php if(has_post_thumbnail()): ?>	
	    										<?php the_post_thumbnail('wp_review_'.$thumb_size, array('title' => '')); ?>		
	    									<?php else: ?>							
	    										<img src="<?php echo plugins_url('img/'.$thumb_size.'thumb.png', __FILE__); ?>" alt="<?php the_title(); ?>"  class="wp-post-image" />					
	    									<?php endif; ?>
	                                    </a>
									</div>				
								<?php endif; ?>					
								<div class="entry-title"><a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php echo $this->post_title( $title_length ); ?></a></div>		
								<?php if ( $show_date == 1 || $show_comment_num == 1) : ?>	
									<div class="wpt-postmeta">						
										<?php if ( $show_date == 1 ) : ?>			
											<?php the_time('F j, Y'); ?>		
										<?php endif; ?>						
										<?php if ( $show_date == 1 && $show_comment_num == 1) : ?>		
											&bull; 						
										<?php endif; ?>					
										<?php if ( $show_comment_num == 1 ) : ?>			
											<?php echo comments_number(__('No Comment','wp-tab-widget'), __('One Comment','wp-tab-widget'), '<span class="comments-number">%</span> '.__('Comments','wp-tab-widget'));?>				
										<?php endif; ?>						
									</div> <!--end .entry-meta--> 				
								<?php endif; ?>
	                            
	                            <?php if ( $show_excerpt == 1 ) : ?>	
	                                <div class="wpt_excerpt">
	                                    <p><?php echo $this->excerpt($excerpt_length); ?></p>
	                                </div>
	                            <?php endif; ?>	
	                            						
								<div class="clear"></div>			
							</li>				
						<?php $post_num++; endwhile; wp_reset_query(); ?>		
					</ul>
	                <div class="clear"></div>
					<?php if ($allow_pagination) : ?>         
						<?php $this->tab_pagination($page, $last_page); ?>      
					<?php endif; ?>                      
					<?php           
				break;              
	            
				/* ---------- Recent Posts ---------- */      
				case "recent":           
					?>         
					<ul>			
						<?php              
						$recent = new WP_Query('posts_per_page='. $post_num .'&orderby=post_date&order=desc&post_status=publish&paged='. $page);       
						$last_page = $recent->max_num_pages;      
						while ($recent->have_posts()) : $recent->the_post();    
							?>						         
							<li>
								<?php if ( $show_thumb == 1 ) : ?>					
									<div class="wpt_thumbnail wpt_thumb_<?php echo $thumb_size; ?>">	
	                                    <a title="<?php the_title(); ?>" href="<?php the_permalink() ?>">		
	    									<?php if(has_post_thumbnail()): ?>	
	    										<?php the_post_thumbnail('wp_review_'.$thumb_size, array('title' => '')); ?>		
	    									<?php else: ?>							
	    										<img src="<?php echo plugins_url('img/'.$thumb_size.'thumb.png', __FILE__); ?>" alt="<?php the_title(); ?>"  class="wp-post-image" />					
	    									<?php endif; ?>
	                                    </a>
									</div>				
								<?php endif; ?>					
								<div class="entry-title"><a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php echo $this->post_title( $title_length ); ?></a></div>		
								<?php if ( $show_date == 1 || $show_comment_num == 1) : ?>			
									<div class="wpt-postmeta">										
										<?php if ( $show_date == 1 ) : ?>						
											<?php the_time('F j, Y'); ?>						
										<?php endif; ?>								
										<?php if ( $show_date == 1 && $show_comment_num == 1) : ?>		
											&bull; 										
										<?php endif; ?>								
										<?php if ( $show_comment_num == 1 ) : ?>	
											<?php echo comments_number(__('No Comment','wp-tab-widget'), __('One Comment','wp-tab-widget'), '<span class="comments-number">%</span> '.__('Comments','wp-tab-widget'));?>									
										<?php endif; ?>		
									</div> <!--end .entry-meta--> 		
								<?php endif; ?>
	                            
	                            <?php if ( $show_excerpt == 1 ) : ?>	
	                                <div class="wpt_excerpt">
	                                    <p><?php echo $this->excerpt($excerpt_length); ?></p>
	                                </div>
	                            <?php endif; ?>	
	                            	
								<div class="clear"></div>		
							</li>				
						<?php endwhile; wp_reset_query(); ?>		
					</ul>
	                <div class="clear"></div>
					<?php if ($allow_pagination) : ?>       
						<?php $this->tab_pagination($page, $last_page); ?>    
					<?php endif; ?>                 
					<?php       
				break;     
	            
				/* ---------- Latest Comments ---------- */        
				case "comments":         
					?>          
					<ul>            
						<?php              
						$no_comments = false;         
						$avatar_size = 65;            
						$comment_length = 90; // max length for comments   
						$comment_args = apply_filters(
							'wpt_comments_tab_args',
							array(
								'type' => 'comments',
								'status' => 'approve'
							)
						);     
						$comments_total = new WP_Comment_Query();
						$comments_total_number = $comments_total->query( array_merge( array('count' => 1 ), $comment_args ) );
						$last_page = (int) ceil($comments_total_number / $comment_num);
						$comments_query = new WP_Comment_Query();
						$offset = ($page-1) * $comment_num;
						$comments = $comments_query->query( array_merge( array( 'number' => $comment_num, 'offset' => $offset ), $comment_args ) );
						if ( $comments ) : foreach ( $comments as $comment ) : ?>       
							<li>          
								<?php if ($show_avatar) : ?>                       
									<div class="wpt_avatar">
	                                    <a href="<?php echo get_comment_link($comment->comment_ID); ?>">
											<?php echo get_avatar( $comment->comment_author_email, $avatar_size ); ?>     
	                                    </a>                               
									</div>                   
								<?php endif; ?>              
								<div class="wpt_comment_meta">
	                                <a href="<?php echo get_comment_link($comment->comment_ID); ?>">   
										<span class="wpt_comment_author"><?php echo get_comment_author( $comment->comment_ID ); ?> </span> - <span class="wpt_comment_post"><?php echo get_the_title($comment->comment_post_ID); ?></span>                   
								    </a>
	                            </div>                   
								<div class="wpt_comment_content">          
									<p><?php echo $this->truncate(strip_tags(apply_filters( 'get_comment_text', $comment->comment_content )), $comment_length);?></p>
								</div>                                   
								<div class="clear"></div>      
							</li>           
						<?php endforeach; else : ?>           
							<li>                   
								<div class="no-comments"><?php _e('No comments yet.', 'wp-tab-widget'); ?></div>        
							</li>                             
							<?php $no_comments = true; 
						endif; ?>       
					</ul>       
					<?php if ($allow_pagination && !$no_comments) : ?>           
						<?php $this->tab_pagination($page, $last_page); ?>      
					<?php endif; ?>                     
					<?php           
				break;             
	            
				/* ---------- Tags ---------- */   
				case "tags":        
					?>           
					<ul>         
						<?php        
						$tags = get_tags(array('get'=>'all'));             
						if($tags) {               
							foreach ($tags as $tag): ?>    
								<li><a href="<?php echo get_term_link($tag); ?>"><?php echo $tag->name; ?></a></li>           
								<?php            
							endforeach;       
						} else {          
							_e('No tags created.', 'wp-tab-widget');           
						}            
						?>           
					</ul>            
					<?php            
				break;            
			}              
			die(); // required to return a proper result  
		}    
	    function tab_pagination($page, $last_page) {  
			?>   
			<div class="wpt-pagination">     
				<?php if ($page > 1) : ?>               
					<a href="#" class="previous"><span><?php _e('&laquo; Previous', 'wp-tab-widget'); ?></span></a>      
				<?php endif; ?>        
				<?php if ($page != $last_page) : ?>     
					<a href="#" class="next"><span><?php _e('Next &raquo;', 'wp-tab-widget'); ?></span></a>      
				<?php endif; ?>          
			</div>                   
			<div class="clear"></div>
			<input type="hidden" class="page_num" name="page_num" value="<?php echo $page; ?>" />    
			<?php   
		}
	    
	    function excerpt($limit = 10) {
	    	  $limit++;
	          $excerpt = explode(' ', get_the_excerpt(), $limit);
	          if (count($excerpt)>=$limit) {
	            array_pop($excerpt);
	            $excerpt = implode(" ",$excerpt).'...';
	          } else {
	            $excerpt = implode(" ",$excerpt);
	          }
	          $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
	          return $excerpt;
	    }
	    function post_title($limit = 10) {
	    	  $limit++;
	          $title = explode(' ', get_the_title(), $limit);
	          if (count($title)>=$limit) {
	            array_pop($title);
	            $title = implode(" ",$title).'...';
	          } else {
	            $title = implode(" ",$title);
	          }
	          return $title;
	    }
	    function truncate($str, $length = 24) {
	        if (mb_strlen($str) > $length) {
	            return mb_substr($str, 0, $length).'...';
	        } else {
	            return $str;
	        }
	    }
	}
}
add_action( 'widgets_init', create_function( '', 'register_widget( "wpt_widget" );' ) );

// post view count
// AJAX is used to support caching plugins
add_filter('the_content', 'wpt_view_count_js'); // outputs JS for AJAX call on single
add_action('wp_ajax_wpt_view_count', 'ajax_wpt_view_count');
add_action('wp_ajax_nopriv_wpt_view_count','ajax_wpt_view_count');
// prevent additional ajax call if theme has view counter already
add_action('mts_view_count_after_update', 'wpt_update_view_count'); 

function wpt_view_count_js( $content ) {
	global $post;
	$id = $post->ID;
	$use_ajax = apply_filters( 'mts_view_count_cache_support', true );
	
	$exclude_admins = apply_filters( 'mts_view_count_exclude_admins', false ); // pass in true or a user capaibility
	if ($exclude_admins === true) $exclude_admins = 'edit_posts';
	if ($exclude_admins && current_user_can( $exclude_admins )) return $content; // do not count post views here
	if (!is_single()) return $content; // Only on single posts

	if ($use_ajax) { // prevent additional ajax call if theme has view counter already
		// enqueue jquery
		wp_enqueue_script( 'jquery' );
		
		$url = admin_url( 'admin-ajax.php' );
		$content .= "
<script type=\"text/javascript\">
jQuery(document).ready(function($) {
	$.post('{$url}', {action: 'wpt_view_count', id: '{$id}'});
});
</script>";
	} else {
		wpt_update_view_count($id);
	}

	remove_filter('the_content', 'wpt_view_count_js');

	return $content;
}

function ajax_wpt_view_count() {
	// do count
	$post_id = $_POST['id'];
	wpt_update_view_count( $post_id );
}
function wpt_update_view_count( $post_id ) {
	$sample_rate = intval( apply_filters( 'wpt_sampling_rate', 100 ) ) / 100;
	if ( ( mt_rand() / mt_getrandmax() ) <= $sample_rate ) {
		$count = get_post_meta( $post_id, '_wpt_view_count', true );
		update_post_meta( $post_id, '_wpt_view_count', $count + 1 );
	}
}

// Add meta for all existing posts that don't have it
// to make them show up in Popular tab
function wpt_add_views_meta_for_posts() {
	$allposts = get_posts( 'numberposts=-1&post_type=post&post_status=any' );

	foreach( $allposts as $postinfo ) {
		add_post_meta( $postinfo->ID, '_wpt_view_count', 0, true );
	}
}

// Reset post count for specific post or all posts
function wpt_reset_post_count($post_id = 0) {
	if ($post_id == 0) {
		$allposts = get_posts( 'numberposts=-1&post_type=post&post_status=any' );
		foreach( $allposts as $postinfo ) {
			update_post_meta( $postinfo->ID, '_wpt_view_count', '0' );
		}
	} else {
		update_post_meta( $post_id, '_wpt_view_count', '0' );
	}
}

// add post meta on plugin activation
function wpt_plugin_activation() {
	wpt_add_views_meta_for_posts();
}
register_activation_hook( __FILE__, 'wpt_plugin_activation' );

// unregister MTS Tabs Widget and Tabs Widget v2
add_action('widgets_init', 'unregister_mts_tabs_widget', 100);
function unregister_mts_tabs_widget() {
    unregister_widget('mts_Widget_Tabs_2');
    unregister_widget('mts_Widget_Tabs');
}

/* Display a admin notice */

add_action('admin_notices', 'wp_tab_widget_admin_notice');
function wp_tab_widget_admin_notice() {
    global $current_user ;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    if ( ! get_user_meta($user_id, 'wp_tab_widget_ignore_notice') ) {
        echo '<div class="updated notice-info wp-tab-widget-pro-notice" style="position:relative;"><p>'; 
        printf(__('Like WP Tab Widget? You will <strong>LOVE WP Tab Widget Pro</strong>!','wp-tab-widget').'<a href="https://mythemeshop.com/plugins/wp-tab-widget-pro/?utm_source=WP+Tab+Widget+Free&utm_medium=Notification+Link&utm_content=WP+Tab+Widget+Pro+LP&utm_campaign=WordPressOrg&wpmts" target="_blank">&nbsp;'.__('Click here for all the exciting features.','wp-tab-widget').'</a><a href="%1$s" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important;"></a>', '?wp_tab_widget_notice_ignore=0');
        echo "</p></div>";
    }
}

add_action('admin_init', 'wp_tab_widget_notice_ignore');
function wp_tab_widget_notice_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['wp_tab_widget_notice_ignore']) && '0' == $_GET['wp_tab_widget_notice_ignore'] ) {
            add_user_meta($user_id, 'wp_tab_widget_ignore_notice', 'true', true);
    }
}
?>
