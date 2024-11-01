<?php
/*
Plugin Name:WP E-Commerce Best Sellers
Plugin URI: http://www.dedoro.com
Description: A widget that provides the best-sellers product Visit the <a href='http://www.dedoro.com'>cheap designer handbags</a> for support.
Version: 0.1
Author: Dedoro 
Author URI: http://www.dedoro.com
*/

class WPSC_Best_Sellers_Widget extends WP_Widget
{
	/**
	* Declares the WPSC_Best_Sellers_Widget class.
	*
	*/
	function WPSC_Best_Sellers_Widget(){
		$widget_ops = array('best_sellers' => '', 'description' => __( "Show Best Sellers Products") );
		$this->WP_Widget(false, __('Best Sellers Product'), $widget_ops);
	}
	
	/**
	* Displays the Widget
	*
	*/
	function widget($args, $instance){
		global $wpdb;
		
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? 'Best Sellers' : $instance['title']);
		$txt_img = empty($instance['text_or_image']) ? 'text' : $instance['text_or_image'];
		$best_sellers_id = empty($instance['best_sellers_id']) ? '' : $instance['best_sellers_id'];

		# Before the widget
		echo $before_widget;		
		# The title
		if ( $title )
			echo $before_title . $title . $after_title;
			
		if ($best_sellers_id == "") {
			$sql = "select prodid, count(prodid) as prodnum from " . $wpdb->prefix. "wpsc_cart_contents group by prodid order by prodnum desc LIMIT 10";
			$best_sellers_list = $wpdb->get_results($sql,ARRAY_A);			
			
			echo '<ol class="wpsc_best_sellers">';
			foreach((array)$best_sellers_list as $best_sellers) {
  			$sql="SELECT list.id,list.name,list.price,image.image,list.special,list.special_price
        FROM ".$wpdb->prefix."wpsc_product_list AS list
        LEFT JOIN ".$wpdb->prefix."wpsc_product_images AS image
        ON list.image=image.id WHERE list.id=" . $best_sellers['prodid'];
  
  			$product_list = $wpdb->get_results($sql,ARRAY_A);				
  			$product = $product_list[0];
  			if ($txt_img == "text") {
  				echo '<li><a href="' . wpsc_product_url($product['id']) . '">' . stripslashes($product['name']) . '</a></li>';  				  				
  			}else{
  				$output = "<img src='".WPSC_THUMBNAIL_URL.$product['image']."' title='".$product['name']."' alt='".$product['name']."' />";
  				echo '<li><a href="' . wpsc_product_url($product['id']) . '">' . $output . '</a></li>';  	  				
  			}				
				
			}	
			echo '</ol>';
			
		}else{
			$productids = split(",",$best_sellers_id);
			echo '<ol class="wpsc_best_sellers">';
			foreach((array)$productids as $product_id) {
  			$sql="SELECT list.id,list.name,list.price,image.image,list.special,list.special_price
        FROM ".$wpdb->prefix."wpsc_product_list AS list
        LEFT JOIN ".$wpdb->prefix."wpsc_product_images AS image
        ON list.image=image.id WHERE list.id=" . $product_id;
  
  			$product_list = $wpdb->get_results($sql,ARRAY_A);				
  			$product = $product_list[0];
  			if ($txt_img == "text") {
  				echo '<li><a href="' . wpsc_product_url($product['id']) . '">' . stripslashes($product['name']) . '</a></li>';  				  				
  			}else{
  				$output = "<img src='".WPSC_THUMBNAIL_URL.$product['image']."' title='".$product['name']."' alt='".$product['name']."' />";
  				echo '<li><a href="' . wpsc_product_url($product['id']) . '">' . $output . '</a></li>';  	  				
  			}
				
			}
			echo '</ol>';
		}	
    			
		echo $after_widget;
	}
	
	/**
	* Saves the widgets settings.
	*
	*/
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['text_or_image'] = strip_tags(stripslashes($new_instance['text_or_image']));
		$instance['best_sellers_id'] = strip_tags(stripslashes($new_instance['best_sellers_id']));
		
		return $instance;
	}
	
	/**
	* Creates the edit form for the widget.
	*
	*/
	function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title'=>'Best Sellers', 'text_or_image'=>'text', 'best_sellers_id'=>'') );
		
		$title = htmlspecialchars($instance['title']);
		$txt_img = htmlspecialchars($instance['text_or_image']);
		$best_sellers_id = htmlspecialchars($instance['best_sellers_id']);
		
		# Output the options
		echo '<p><label for="' . $this->get_field_name('title') . '">' . __('Title:') . ' </label> <input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
		# Text or Image
		if ($txt_img == "text"){
		   $text = '<INPUT TYPE=RADIO ' . 'id="' . $this->get_field_id('text_or_image') . '" NAME="' . $this->get_field_name('text_or_image') . '" VALUE="text" CHECKED>TEXT';
		   $image = '<INPUT TYPE=RADIO ' . 'id="' . $this->get_field_id('text_or_image') . '" NAME="' . $this->get_field_name('text_or_image') . '" VALUE="image">IMAGE';		   
		}
		else{
		   $text = '<INPUT TYPE=RADIO ' . 'id="' . $this->get_field_id('text_or_image') . '" NAME="' . $this->get_field_name('text_or_image') . '" VALUE="text">TEXT';			
		   $image = '<INPUT TYPE=RADIO ' . 'id="' . $this->get_field_id('text_or_image') . '" NAME="' . $this->get_field_name('text_or_image') . '" VALUE="image" CHECKED>IMAGE';
	  }
		echo '<p><label for="' . $this->get_field_name('text_or_image') . '">' . __('Text or Image?') . '</label><br/>' . $text . '&nbsp;&nbsp;' . $image . '</p>';
    
    # Best Sellers Product ID
		echo '<p><label for="' . $this->get_field_name('best_sellers_id') . '">' . __('Product IDs:') . ' </label><input id="' . $this->get_field_id('best_sellers_id') . '" name="' . $this->get_field_name('best_sellers_id') . '" type="text" value="' . $best_sellers_id . '" /></p>';     
	}

}// END class
	
	/**
	* Register Best Sellers widget.
	*
	*/
	function WPSC_Best_Sellers_Init() {
	register_widget('WPSC_Best_Sellers_Widget');
	}	
	add_action('widgets_init', 'WPSC_Best_Sellers_Init');
	
?>