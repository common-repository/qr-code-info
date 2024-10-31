<?php
/**
 * Plugin Name: QR Code info
 * Plugin URI: 
 * Description: This plugin is design for making Custom Qr code or Printing Page and post information to the Bottom.
 * Version: 0.1a
 * Author: Javed mahmud 
 * Author URI: 
 * License: GPL2
 */

	add_action( 'admin_menu', 'QR_plugin_menu' );
	function QR_plugin_menu() {
		add_menu_page( 'QR Code info', 'QR Code info' , 'manage_options', 'Qr-Code-info', 'qr_admin_page',plugins_url('icon.png',__FILE__),10 );
	}

	function qr_admin_page(){
		if ( !current_user_can('manage_options'))  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if(isset($_POST['form_submit']) AND wp_verify_nonce( $_POST['nonces_form_1'], 'option_form')){ 
			if ($_POST) {
				$pages_ck = get_pages(); 
				foreach ($pages_ck as $page_data) {
					$page_id = $page_data->ID  ;
					delete_post_meta($page_id,'show_qr');
				}

				$posts_ck = get_posts(); 
				foreach ($posts_ck as $post_data) {
					$post_id = $post_data->ID  ;
					delete_post_meta($post_id,'show_qr');
				}

			  	$kv = array();

				foreach ($_POST as $key => $value) {

					$sanitize_key= sanitize_text_field($key);
					$sanitize_value= sanitize_text_field($value);

				   	if(! add_post_meta( $sanitize_key,'show_qr', $sanitize_value, true ) ) { 
				   	   update_post_meta( $sanitize_key, 'show_qr', $sanitize_value);
				   	}
			    	$kv[$sanitize_key] = $sanitize_value;
				}
			}else {
				wp_die( __( 'Form 1 have issues' ) );
			}
		}


		if (isset($_POST['style_options_form_submit']) AND wp_verify_nonce( $_POST['nonces_form_2'], 'qrcode_style_option_form') ) { 
			if ($_POST) {
			  $post_submit_data = array();
			  foreach ($_POST as $key1 => $value1) {
			    $post_submit_data[$key1] = $value1;
			  }
			  extract($post_submit_data);
			}

			if(isset($background_color) && !empty($background_color)){
				$qr_style_options_array['background_color'] = sanitize_text_field($background_color);
			}

			if(isset($text_color) && !empty($text_color)){
				$qr_style_options_array[ 'text_color' ] =  sanitize_text_field($text_color);
			}

			if(isset($border_color) && !empty($border_color)){
				$qr_style_options_array[ 'border_color' ] = sanitize_text_field($border_color);
			}

			if(isset($box_shadow) && !empty($box_shadow)){
				$qr_style_options_array[ 'box_shadow' ] =  sanitize_text_field($box_shadow);
			}

			update_option( 'qrcode_style_options', $qr_style_options_array );
		}

		// gatting set Options Data
		$qr_style_options_array = get_option( 'qrcode_style_options' );


		?>

		<div class="wrap">

			<div id="icon-options-general" class="icon32"></div>
			<h1><?php esc_attr_e( 'QR Code Settings', 'wp_admin_style' ); ?></h1>

			<div id="poststuff">

				<div id="post-body" class="metabox-holder columns-2">

					<!-- main content -->
					<div id="post-body-content">

						<div class="meta-box-sortables ui-sortable">

							<div class="postbox">

								<div class="handlediv" title="Click to toggle"><br></div>
								<!-- Toggle -->

								<h2 class="hndle"><span><?php esc_attr_e( 'Select Page and Post to show QR ', 'wp_admin_style' ); ?></span>
								</h2>

								<div class="inside">
									<form name="option_form"  method='post' action='' >
									<?php  wp_nonce_field( 'option_form','nonces_form_1' ); ?> 
									<?php
									$pages = get_pages();
									echo "<h3> Pages :  </h3>"; 
									echo "<ul class='page_ul'>";
									foreach ($pages as $page_data) {
									   $content = apply_filters('the_title', $page_data->post_content); 
									   $title = $page_data->post_title; 
									   $page_id = $page_data->ID ;
									   $page_show_type = get_post_meta($page_id, 'show_qr', true);
									    if($page_show_type == 1){
									   		echo "<li class='page_li'><input type='checkbox' value='1' name='$page_id'/ checked  />". $title . "</li>";
										}else{
											echo "<li class='page_li'><input type='checkbox' value='1' name='$page_id'/  />". $title . "</li>";
										}
									}
									echo "</ul>";

									echo"<hr/>";

									$posts = get_posts();
									echo "<h3> Posts :  </h3>";
									echo "<ul class='post_ul'>";
									foreach ( $posts as $post ) {
										$post_id_form = $post->ID ;
										$post_title_form = $post->post_title;
										$post_show_type = get_post_meta($post_id_form, 'show_qr', true);
										if($post_show_type == 1){
											echo "<li class='post_li'><input type='checkbox' value='1' name=".$post_id_form." checked />".$post_title_form."</li>";
										}else{
											echo "<li class='post_li'><input type='checkbox' value='1' name=".$post_id_form."  />".$post_title_form."</li>";
										}
									}
									echo "</ul>";

									?>
									<input type="submit" class="button-primary" name="form_submit" value="save settings">
									</form>
								</div>
								<!-- .inside -->
							</div>
							<!-- .postbox -->

							<!-- new Postbox Start  -->
							<div class="postbox">

								<div class="handlediv" title="Click to toggle"><br></div>
								<!-- Toggle -->

								<h2 class="hndle">
									<span><?php esc_attr_e( 'Style Options :', 'wp_admin_style' ); ?></span>
								</h2>

								<div class="inside">
									
									<form name="qrcode_style_option_form"  method='post' action='' >
									<?php  wp_nonce_field( 'qrcode_style_option_form','nonces_form_2'); ?>
									<h3 class='box_shadow_h3'> Colors : </h3>

									<ul class='style_options_ul'>
										<li class='style_options_li'>  
										Background color :
										</li>
										<li class='style_options_li'>  
								<?php
									if (isset($qr_style_options_array[ 'background_color' ]) && !empty($qr_style_options_array[ 'background_color' ])) {
										echo"<input type='text' class='my-color-field' maxlength='8' name='background_color' value='".$qr_style_options_array[ 'background_color' ]. "'>";
									}else{
										echo"<input type='text' class='my-color-field' maxlength='8' name='background_color' value='#FCFCFC'>";
									}
								?>
										</li>
									</ul>

									<ul class='style_options_ul'>
										<li class='style_options_li'>  
										Text color Code :
										</li>
										<li class='style_options_li'>  
									<?php
										if (isset($qr_style_options_array[ 'text_color' ]) && !empty($qr_style_options_array[ 'text_color' ])) {
											echo"<input type='text' class='my-color-field' maxlength='8' name='text_color' value='".$qr_style_options_array[ 'text_color' ]."'>";
										}else{
											echo"<input type='text' class='my-color-field' maxlength='8' name='text_color' value='#171717'>";
										}
									?>
										</li>
									</ul>

									<ul class='style_options_ul'>
										<li class='style_options_li'>  
										Border color Code :
										</li>
										<li class='style_options_li'>
									<?php
										if (isset($qr_style_options_array[ 'border_color' ]) && !empty($qr_style_options_array[ 'border_color' ])) {
											echo"<input type='text' class='my-color-field' maxlength='8' name='border_color' value='".$qr_style_options_array[ 'border_color' ]."'>";
										}else{
											echo"<input type='text' class='my-color-field' maxlength='8' name='border_color' value='#AAAAAA'>";
										}
									?>  
										</li>
									</ul>

									</br>

									<h3 class='box_shadow_h3'> Box shadow : </h3>

									<div class='box_shadow_div'>     <!-- start of box_shadow_div  -->
										<ul class='box_shadow_ul_1'>
											<li class='style_options_li'>  
											Box shadow style 1 : 
											<?php
												if (isset($qr_style_options_array[ 'box_shadow' ]) && $qr_style_options_array[ 'box_shadow' ] == 'd1' && !empty($qr_style_options_array[ 'box_shadow' ])) {
													echo "<input type='radio' name='box_shadow' value='d1' checked />";
												}else{
													echo "<input type='radio' name='box_shadow' value='d1' />";
												}
											?>  
											</li>	
										</ul>

										<ul class='box_shadow_ul_2'>
											<li class='style_options_li'>  
											Box shadow style 2 : 
											<?php
												if (isset($qr_style_options_array[ 'box_shadow' ]) && $qr_style_options_array[ 'box_shadow' ] == 'd2' && !empty($qr_style_options_array[ 'box_shadow' ])) {
													echo "<input type='radio' name='box_shadow' value='d2' checked />";
												}else{
													echo "<input type='radio' name='box_shadow' value='d2' />";
												}
											?>  
											</li>
										</ul>

										<ul class='box_shadow_ul_3'>
											<li class='style_options_li'>  
											Box shadow style 3 : 
											<?php
												if (isset($qr_style_options_array[ 'box_shadow' ]) && $qr_style_options_array[ 'box_shadow' ] == 'd3' && !empty($qr_style_options_array[ 'box_shadow' ])) {
													echo "<input type='radio' name='box_shadow' value='d3' checked />";
												}else{
													echo "<input type='radio' name='box_shadow' value='d3'  />";
												}
											?>  
											</li>
											
										</ul>
									</div>  <!-- End of box_shadow_div  -->
									<input type="submit" class="button-primary hmmlook" name="style_options_form_submit" value="save settings">
									</form>
								</div>
								<!-- .inside -->
							</div>
							<!--new Postbox ends .postbox -->

							<div class="postbox">

								<div class="handlediv" title="Click to toggle"><br></div>
								<!-- Toggle -->

								<h2 class="hndle"> <span><?php esc_attr_e( 'Custom QR code :', 'wp_admin_style' ); ?></span> </h2>

								<div class="inside">
									<p> <?php esc_attr_e('To use Custom QR code use this Short Code');?> <b> <?php esc_attr_e(' [qr text=" Your Text is Here "] ');?> </b> </p>

									<p><?php esc_attr_e(" e.g. If you'r going to Create a QR code on this text");?> 
									<i> <?php esc_attr_e("' Scane this for 20 %  Discount '"); ?>  </i> 
									<?php esc_attr_e("your short code will be :");?>
									<b><?php esc_attr_e(' [qr text="Scane this for 20 %  Discount"]')?></b> </p>
								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

							<!-- Bison End -->

						</div>
						<!-- .meta-box-sortables .ui-sortable -->

					</div>
					<!-- post-body-content -->

					<!-- sidebar -->
					<div id="postbox-container-1" class="postbox-container">

						<div class="meta-box-sortables">

							<div class="postbox">

								<div class="handlediv" title="Click to toggle"><br></div>
								<!-- Toggle -->

								<h2 class="hndle ui-sortable-handle"><span><?php esc_attr_e(
											'About Plugin', 'wp_admin_style'
										); ?></span></h2>

								<div class="inside">
									<ul>  
										<li>
										<?php esc_attr_e("Name Of Plugin : QR code ");?>
										</li>

										<li>
										<?php esc_attr_e("Version : 0.1a");?>
										</li>

										<li>
										<?php esc_attr_e("Published on : 20 Aug 2016. ");?>
										</li>

									</ul>


									<p><i><?php esc_attr_e( 'If you have any problem regarding this Plugin please let me know .If you like this plugin, Please rate this. And fell free to contact me for any kind of Wordpress custom worke, thanks & njoy -jm   jaedmah(at)gmail.com', 'wp_admin_style' ); ?> </i></p>

								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

						</div>
						<!-- .meta-box-sortables -->

					</div>
					<!-- #postbox-container-1 .postbox-container -->

				</div>
				<!-- #post-body .metabox-holder .columns-2 -->

				<br class="clear">
			</div>
			<!-- #poststuff -->

		</div> <!-- .wrap -->

		<?php		
	}


	add_filter( 'the_content', 'qr_meta_display', 99 );
	function qr_meta_display( $content ) {

		$link =  empty( get_permalink()) ? "Link didn't found" : get_permalink() ;
		$title =  the_title('', '',FALSE);
		$postid =  get_the_ID();
		$pfx_date =  get_the_date( 'F j, Y g:i a',$postid );
		$post_categories = wp_get_post_categories( $postid );
		$page_show_type = get_post_meta($postid, 'show_qr', true);

		if ( (is_single() || is_page()) && ($page_show_type == 1) ) {
			$chain_string ="Title :".$title ." Url : ".$link." Date : ".$pfx_date;
			$visisble_text = "<div class='visisble_text'><p>Title :".$title."</br>Url : ".$link."</br>Date : ".$pfx_date."</p></div>";

			$width  = 100;
			$height = 100;
			$url    = urlencode($chain_string);
			$error  = "x"; // handle up to 30% data loss, or "L" (7%), "M" (15%), "Q" (25%) H x
			$border = 1;
			$pr_path = "<div class='qr_img'><img class='qr' src=\"http://chart.googleapis.com/chart?"."chs={$width}x{$height}&cht=qr&chld=$error|$border&chl=$url\" /> </div>";
			return $content ."<div class='qr_meta'>" .$pr_path .$visisble_text ."</div>";
		}else{
			return $content; 
		}
	}


	// [qr text="foo-value"]
	function Custom_qr_display($atts){
		$a = shortcode_atts( array(
	        'text' => 'something'
	    ), $atts );

	    $width  = 100;
		$height = 100;
		$url    = urlencode($a['text']);
		$error  = "x";
		$border = 1;
		$pr_path = "<div class='qr_img'><img class='qr' src=\"http://chart.googleapis.com/chart?"."chs={$width}x{$height}&cht=qr&chld=$error|$border&chl=$url\" /> </div>";

	    // return $a['text'];
	    return $pr_path;
	}
	add_shortcode( 'qr', 'Custom_qr_display' );

	//Admin style
	add_action('admin_enqueue_scripts', 'admin_style_sheet'); 
	function admin_style_sheet() {
	  wp_enqueue_style( 'admin_styles', plugins_url('qr_admin_style_sheet.css', __FILE__ ));
	}

	// Client style 
	add_action( 'wp_enqueue_scripts', 'qr_code_meta' );
	function qr_code_meta(){
		wp_enqueue_style( 'qr_style', plugins_url('qr_style_sheet.css', __FILE__ ));
	}

	// Woardpress Color picker 
	add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );
	function mw_enqueue_color_picker( $hook_suffix ) {
	    wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	}


	// Dynamic Client side Style 
	add_action('wp_head', 'my_custom_styles', 100);
	function my_custom_styles(){
		$qr_style_options_array = get_option( 'qrcode_style_options' );
		echo "<style>";

		echo ".qr_meta {";

			if (isset($qr_style_options_array[ 'border_color' ]) && !empty($qr_style_options_array[ 'border_color' ])) {
			  echo"border: 1px solid ".$qr_style_options_array[ 'border_color' ]. ";" ;
			}else{
			  echo"border: 1px solid #AAAAAA;" ;
			}


			if (isset($qr_style_options_array[ 'background_color' ]) && !empty($qr_style_options_array[ 'background_color' ])) {
			  echo"background-color:".$qr_style_options_array[ 'background_color' ]. ";" ;
			}else{
			  echo"background-color: #FCFCFC  #F5F5F5 #DCDCDC ;" ;
			}

			if (isset($qr_style_options_array[ 'box_shadow' ]) && !empty($qr_style_options_array[ 'box_shadow' ])) {
			  switch ($qr_style_options_array[ 'box_shadow' ]) {
			    case "d1":
			      echo "
			        -webkit-box-shadow: 0 10px 6px -6px #777;
			        -moz-box-shadow: 0 10px 6px -6px #777;
			        box-shadow: 0 10px 6px -6px #777;
			      ";
			      break;
			    case "d2":
			      echo "
			        -webkit-box-shadow: 0 8px 6px -6px black;
			        -moz-box-shadow: 0 8px 6px -6px black;
			        box-shadow: 0 8px 6px -6px black;
			      ";
			      break;
			    default:
			      echo "
			        -moz-box-shadow:    inset 0 0 10px #000000;
			        -webkit-box-shadow: inset 0 0 10px #000000;
			        box-shadow:         inset 0 0 10px #000000;
			      ";
			  }
			}else{
			  echo"
			    -webkit-box-shadow: 0 10px 6px -6px #777;
			    -moz-box-shadow: 0 10px 6px -6px #777;
			    box-shadow: 0 10px 6px -6px #777;
			  " ;
			}
		echo "}";

		echo "div.visisble_text{";
			
				  if (isset($qr_style_options_array['text_color']) && !empty($qr_style_options_array['text_color'])){
				    echo"color: ".$qr_style_options_array['text_color']. ";" ;
				  }else{
				    echo"color: #171717;" ;
				  }	  
		echo "}";	
		echo"</style>";
	}