<?php

/*
 * IARC L.Alteyrac 20160830
 * Customization of the plugin "Post Notification"
 *
 * 	- Replace filter post_notification_filter_content by iarc_post_notification_filter_content,
 *    to call iarc_post_notification_page_content instead of post_notification_page_content
 *      @see File: frontend.php
 *      Function:  post_notification_filter_content
 *  - Replace function post_notification_page_content to build a custom Subscribe page
 *	  Call iarc_post_notification_get_catselect instead of post_notification_get_catselect
 *      @see File: frontend.php
 *      Function:  post_notification_page_content
 *  - Replace function post_notification_get_catselect to build a specific Walker and to remove the check box "All"
 *    $walker is now an Iarc_Walker_post_notification, which extend Walker_post_notification
 * 		@see File: functions.php
 *      Function:  post_notification_get_catselect
 *  - The class Iarc_Walker_post_notification extends Walker_post_notification to override only 1 function: start_el
*/

remove_filter('the_content', 'post_notification_filter_content');
add_filter('the_content','iarc_post_notification_filter_content');

function iarc_post_notification_filter_content($content){
	if(strpos($content, '@@post_notification_')!== false){ //Just looking for the start
		$fe = iarc_post_notification_page_content();
		$content = str_replace('@@post_notification_header', $fe['header'], $content);
		$content = str_replace('@@post_notification_body', $fe['body'], $content);
	}
	
	return $content;
}

function iarc_post_notification_page_content(){
	global $post_notification_page_content_glob, $wpdb;
	//if($post_notification_page_content_glob) return $post_notification_page_content_glob;
	
	//It doesn't matter where this goes:
	
	
	$content = & $post_notification_page_content_glob;
	$content = array();
	$content['header'] = '';
	$content['body'] = '';
	
	
		
	// ******************************************************** //
	//                  GET VARIABLES FROM URL
	// ******************************************************** //
	
	
	$action = $_GET['action'];
	$addr   = $wpdb->escape($_GET['addr']);
	$code   = $wpdb->escape($_GET['code']); 

	
	if ($_POST['addr'] != '') {
		$action = $_POST['action'];
		$addr = $wpdb->escape($_POST['addr']);
		$code = $wpdb->escape($_POST['code']);
		$pn_cats = $_POST['pn_cats']; //Security is handled in the function.
	}

	$msg = &$content['body'];
	
	// ******************************************************** //
	//                  DEFINE OTHER VARS NEEDED
	// ******************************************************** //
	require(post_notification_get_profile_dir(). '/strings.php');
	
	
	$t_emails = $wpdb->prefix . 'post_notification_emails';
	$t_cats = $wpdb->prefix . 'post_notification_cats';
	
	$from_email = get_option('post_notification_from_email');
	$pnurl      = post_notification_get_link();
	if(get_option('post_notification_hdr_nl') == "rn")
		$hdr_nl = "\r\n";
	else
		$hdr_nl = "\n";
	$blogname     = get_option('blogname');
	
	// ******************************************************** //
	//                      Code Check
	// ******************************************************** //	
	
	//This code is not very nice in performance, but I wanted to keep it as easy to understand as possible. It's not called that often.
	if(($code != '') && $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr' AND act_code = '" . $code . "'")){
		// ******************************************************** //
		//                   WITH AUTH
		// ******************************************************** //
			
		if(1 != $wpdb->get_var("SELECT gets_mail FROM $t_emails WHERE email_addr = '$addr'")){
			//The user just subscribed, so let's set him up
			$now = post_notification_date2mysql();
			$wpdb->query("UPDATE $t_emails SET gets_mail = 1, date_subscribed = '$now' WHERE email_addr = '$addr'");
 	        $mailid = $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr'");
			$selected_cats = explode(',', get_option('post_notification_selected_cats'));
 			$queryCats = '';
			if (! empty($selected_cats)) {
 			    $queryCats = "";
 			    foreach ($selected_cats as $category) {
 			        if(is_numeric($category)) $queryCats .= ", ($mailid, $category)";
 			    }
 			    if(strlen($queryCats) > 0)
 			    	$wpdb->query("INSERT INTO $t_cats (id, cat_id) VALUES" . substr($queryCats, 1));
 			}
 			if(isset($post_notification_strings['welcome'])){
 				$msg =  '<h3>' . str_replace('@@blogname' , get_option(blogname), $post_notification_strings['welcome']).  '</h3>';
 			}  else {
 				$msg =  '<h3>' . $post_notification_strings['saved'] .  '</h3>';
 			} 
			
		}
	
		
		// ******************************************************** //
		//                      Select Cats
		// ******************************************************** //
		if ($action == "subscribe") { 
			
			$wpdb->query("UPDATE $t_emails SET gets_mail = 1 WHERE email_addr = '$addr'");
			$mid = $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr'"); 

			if(get_option('post_notification_show_cats') == 'yes'){ 
				//Delete all entries
				$wpdb->query("DELETE FROM $t_cats WHERE id = $mid");
				
				if(!is_array($pn_cats)) $pn_cats = array(); //Just to make shure it doesn't crash
				
				//Let's see what cats we have
				$queryCats = '';
				foreach($pn_cats as $cat){
					if(is_numeric($cat)) $queryCats .= ", ($mid, $cat)";//Security		
				}
				
				if(strlen($queryCats) > 0)
					$wpdb->query("INSERT INTO $t_cats (id, cat_id) VALUES" . substr($queryCats, 1));
			}
			$msg .= '<h3>' . $post_notification_strings['saved'] .  '</h3>';

		}
		
		
		// ******************************************************** //
		//                    UNSUBSCRIBE
		// ******************************************************** //
		if ($action == "unsubscribe" AND is_email($addr)) {

			$mid = $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr'"); 
			if($mid != ''){
				$wpdb->query("DELETE FROM $t_emails WHERE id = $mid");
				$wpdb->query("DELETE FROM $t_cats WHERE id = $mid");
			}
			
			$content['header'] = $post_notification_strings['deaktivated'];
			$msg = str_replace(array('@@addr', '@@blogname'), array($addr, $blogname),
							$post_notification_strings['no_longer_activated']);		
			return $content; 
		}
		
		
		// ********************************************************//
		//                     Subscribe-page
		// ********************************************************//
		
		
		$content['header'] = get_option('post_notification_page_name');
		
		

		
		$id = $wpdb->get_var("SELECT id FROM $t_emails  WHERE email_addr = '$addr'");
		
		
		if(get_option('post_notification_show_cats') == 'yes'){ 
			$subcats_db = $wpdb->get_results("SELECT cat_id FROM $t_cats  WHERE id = $id");
			$subcats = array();
			if(isset($subcats_db)){
				foreach($subcats_db as $subcat){
					$subcats[] =  $subcat->cat_id;
				}
			}
			
			
			// Get cats listing
			$cats_str = iarc_post_notification_get_catselect($post_notification_strings['all'], $subcats);
		} else {
			$cats_str = '';
		}
		$vars = '<input type="hidden" name="code" value="' . $code . '" /><input type="hidden" name="addr" value="' . $addr . '" />';
		
		if($action == "subscribe" && get_option('post_notification_saved_tmpl') == 'yes'){
			$msg = 	post_notification_ldfile('saved.tmpl');
		} else {
			$msg .= post_notification_ldfile('select.tmpl');
		}
		$msg = str_replace('@@action',post_notification_get_link(),$msg);
		$msg = str_replace('@@addr',$addr,$msg);
		$msg = str_replace('@@cats',$cats_str,$msg);
		$msg = str_replace('@@vars',$vars,$msg);

		
		
	
	} else {
		// ******************************************************** //
		//                   WITHOUT AUTH
		// ******************************************************** //
		$code = '';
		if(is_email($addr) && post_notification_check_captcha()){
			// ******************************************************** //
			//                      SUBSCRIBE
			// ******************************************************** //
			if ($action == "subscribe" || $action == '') {				
				$conf_url = post_notification_get_mailurl($addr);
						
				// Build  mail
				$mailmsg = post_notification_ldfile('confirm.tmpl');
				
				$mailmsg = str_replace('@@addr',$addr,$mailmsg);
				$mailmsg = str_replace('@@conf_url',$conf_url,$mailmsg);

				wp_mail($addr, "$blogname - " . get_option('post_notification_page_name'), $mailmsg, post_notification_header());
				
				//Output Page
				$content['header'] = $post_notification_strings['registration_successful'];
				$msg = post_notification_ldfile('reg_success.tmpl');
				return $content; //here it ends - We don't want to show the selection screen.
	
			}
			// ******************************************************** //
			//                    UNSUBSCRIBE
			// ******************************************************** //
			if ($action == "unsubscribe") {
				if ($wpdb->get_var("SELECT email_addr FROM $t_emails WHERE email_addr = '$addr'")){ //There is a mail in the db	
					$conf_url = post_notification_get_mailurl($addr);
					$conf_url .= "action=unsubscribe";
					
					$mailmsg = post_notification_ldfile('unsubscribe.tmpl');
					
					$mailmsg = str_replace(array('@@addr','@@conf_url'), array($addr, $conf_url), $mailmsg);
					wp_mail($addr, "$blogname - " . $post_notification_strings['deaktivated'], $mailmsg, post_notification_header());
				}
				$content['header'] = $post_notification_strings['deaktivated'];
				$msg = str_replace(array('@@addr', '@@blogname'), array($addr, $blogname),
								$post_notification_strings['unsubscribe_mail']);
				return $content; //here it ends - We don't want to show the selection screen.
			}
				
		}
		
		if($addr != ''){
			if(!is_email($addr))
				$msg .= '<p class="error">' . $post_notification_strings['check_email'] . '</p>';
			if(!post_notification_check_captcha() && action != '')
				$msg .= '<p class="error">' . $post_notification_strings['wrong_captcha'] . '</p>';
		} 
		
		//Try to get the email addr
		if($addr == ''){
			$addr = post_notification_get_addr();
		}
		
		$content['header'] = get_option('post_notification_page_name');
		
	
		$msg .= post_notification_ldfile('subscribe.tmpl');
		$msg = str_replace('@@action',post_notification_get_link($addr),$msg);
		$msg = str_replace('@@addr',$addr,$msg);
		$msg = str_replace('@@cats','',$msg);
		$msg = str_replace('@@vars',$vars,$msg);
		
		//Do Captcha-Stuff
		if(get_option('post_notification_captcha') == 0){ 
			$msg = preg_replace('/<!--capt-->(.*?)<!--cha-->/is', '', $msg); //remove captcha
		} else {
			require_once( POST_NOTIFICATION_PATH . 'class.captcha.php' );
			$captcha_code = md5(round(rand(0,40000))); 
			$my_captcha = new captcha($captcha_code, POST_NOTIFICATION_PATH . '_temp');
			$captchaimg = POST_NOTIFICATION_PATH_URL . '_temp/cap_' . $my_captcha->get_pic(get_option('post_notification_captcha')) . '.jpg';
			$msg = str_replace('@@captchaimg',$captchaimg,$msg);
			$msg = str_replace('@@captchacode',$captcha_code,$msg);
			
		}
	}
		
	return $content;
	
}

function iarc_post_notification_get_catselect($all_str = '', $subcats = array()){
	if(!is_array($subcats)) $subcats = array();
	
	$walkparam = array('pn_ids' => $subcats);
	
	if($all_str == '') $all_str = __('All', 'post_notification');
	if(get_option('post_notification_empty_cats') == 'yes'){
		$cats = get_categories(array('hide_empty' => false));
	} else {
		$cats = get_categories();
	}
	
	$walker = new Iarc_Walker_post_notification;
	
	$cats_str  = '<script src="'. POST_NOTIFICATION_PATH_URL . '/pncats.js" type="text/javascript" ></script>';	
	
	$cats_str .= '<ul class="children">' . call_user_func_array(array(&$walker, 'walk'), array($cats, 0, $walkparam)) . '</ul>';
	$cats_str .= '</ul>';
	$cats_str .= '<script type="text/javascript"><!--' . "\n  post_notification_cats_init();\n //--></script>";
	return $cats_str;
}

if ( class_exists( 'Walker_post_notification' ) ) {
	
	class Iarc_Walker_post_notification extends Walker_post_notification {
		
		function start_el(&$output, $category, $depth, $args) {
			
			// IARC L.Alteyrac/B.Bouchereau 20150826
			if($category->cat_ID == 1) { //= ID de la catégorie Uncategorized
				; //on n'affiche rien
			}
			else if (!(get_category_children($category->cat_ID) == '')) { //si la categorie enfants n'est pas vide, on ne met pas la checkbox
				$output .= str_repeat("\t", $depth * 3);
				$output .= "<li>";
				$output .= apply_filters('list_cats', $category->cat_name, $category);
				$output .= "</li>\n";
			}
			else {
				$output .= str_repeat("\t", $depth * 3);
				$output .= "<li>";
				
				$output .= "\t" . '<input type="checkbox" name="pn_cats[]" value="' .$category->cat_ID . 
					'" id="cat.' . implode('.', $this->id_list) . '.' . $category->cat_ID. '" ';
				if ( in_array($category->cat_ID, $args['pn_ids'])) $output .= ' checked="checked"'; 
				$output .= ' onclick = "post_notification_cats_init()" />';
			
				$output .= apply_filters('list_cats', $category->cat_name, $category);


				$output .= "</li>\n";
				$this->last_id = $category->cat_ID;
			}
			return $output;
		}
		
	}
}

?>