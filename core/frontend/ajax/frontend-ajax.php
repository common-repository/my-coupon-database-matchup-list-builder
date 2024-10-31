<?php
if(!empty($_POST) && $_POST['action'] == "mcdl_frontend" && is_admin()):
	if(isset($_POST['method'])):
		######################################################################
		# UNIVERSAL
		######################################################################
		global $wpdb; // WordPress DB Class
		session_start(); // Start Options
		$options = get_option('mcd_list');
		######################################################################
		# PANEL METHODS
		######################################################################
		// Return a List of LI's (coupons) to Add to Panel
		if($_POST['method'] == "add_coupons_to_panel"):
			// Run This if $_SESSION['mcd_list_plugin']['header_ids'] has already been set
			if($_SESSION['mcd_list_plugin']['header_ids']):
				// Find Headers that are Not Already in the Array
				for($i = 0; $i < count($_POST['header_id']); $i++):
					if(!in_array($_POST['header_id'][$i], $_SESSION['mcd_list_plugin']['header_ids'])):
						$new_headers[] = $_POST['header_id'][$i];
					endif;
				endfor;
				// Update Session Array with New Values
				$_SESSION['mcd_list_plugin']['header_ids'] = array_unique(array_merge($_SESSION['mcd_list_plugin']['header_ids'], $_POST['header_id']));
			// Run This if $_SESSION['mcd_list_plugin']['header_ids'] has NOT been set
			else:
				$_SESSION['mcd_list_plugin']['header_ids'] = $_POST['header_id'];
				$_SESSION['mcd_list_plugin']['panel_orientation'] = 'show';
				$new_headers = $_POST['header_id'];
			endif;
		
			if($new_headers):
				$placeholders = implode(',', array_fill(0, count($new_headers), '%d'));
				$results = $wpdb->get_results($wpdb->prepare(
					"SELECT id, name, net_price FROM mcd_cl_coupon_group WHERE id IN ($placeholders)", $new_headers
				));
				$data['line_items'] = '';
				for($i = 0; $i < count($new_headers); $i++):
					if($results[$i]->net_price): $net_price = ' - ' . $options['text_net_price'] . ' ' . $results[$i]->net_price; else: $net_price = ''; endif;
					$data['line_items'] .= '<li id="' . $results[$i]->id . '">' . stripslashes($results[$i]->name) . $net_price . ' <a href="#" class="mcd_list_remove_coupon">remove</a></li>';
				endfor;
				$data['header_count'] = count($_SESSION['mcd_list_plugin']['header_ids']);
			else:
				$data['message'] = 'No new headers were added.';
			endif;
		endif;
		
		// Remove A Coupon From Panel
		if($_POST['method'] == "remove_header"):
			$key = array_search($_POST['header_id'], $_SESSION['mcd_list_plugin']['header_ids']);
			unset($_SESSION['mcd_list_plugin']['header_ids'][$key]);
			$data['message'] = 'Removed header from $_SESSION';
			$data['header_count'] = count($_SESSION['mcd_list_plugin']['header_ids']);
		endif;
		
		// Clear List - Unset $_SESSION['mcd_list_plugin']['header_ids']
		if($_POST['method'] == 'clear_list'):
			unset($_SESSION['mcd_list_plugin']['header_ids']);
			$data['message'] = 'All coupons have been removed from the list.';
		endif;
		
		// Return the Panel HTML
		if($_POST['method'] == "get_panel"):
			
			// Grab LI's for List if ['mcd_list_plugin']['header_ids'] is Set
			if($_SESSION['mcd_list_plugin']['header_ids']):
				$new_headers = $_SESSION['mcd_list_plugin']['header_ids'];
				$placeholders = implode(',', array_fill(0, count($new_headers), '%d'));
				$results = $wpdb->get_results($wpdb->prepare(
					"SELECT id, name, net_price FROM mcd_cl_coupon_group WHERE id IN ($placeholders)", $new_headers
				));
				$data['line_items'] = '';
				for($i = 0; $i < count($new_headers); $i++):
					if($results[$i]->net_price): $net_price = ' - ' . $options['text_net_price'] . ' ' . $results[$i]->net_price; else: $net_price = ''; endif;
					$data['line_items'] .= '<li id="' . $results[$i]->id . '">' . stripslashes($results[$i]->name) . $net_price . ' <a href="#" class="mcd_list_remove_coupon">remove</a></li>';
				endfor;
			
				// Return Panel via AJAX
				$data['panel'] = '<div id="mcd_list_container" class="mcd_list_container_' . $_SESSION['mcd_list_plugin']['panel_orientation'] . '">
					<a href="#" id="mcd_list_tab" class="mcd_list_tab_' . $_SESSION['mcd_list_plugin']['panel_orientation'] . '"></a>
					<div id="mcd_list_coupons">
						<div id="mcd_list_panel_nav">
							<ul id="mcd_list_functions_left">
								<li id="mcd_list_print_coupons"><a href="#mcd-print-list-data" title="Print My List"></a></li>
							</ul>
						
							<ul id="mcd_list_functions_right">
								<li id="mcd_list_minimize_panel"><a href="#" title="Minimize Panel"></a></li>
								<li id="mcd_list_clear_list"><a href="#" title="Clear My List"></a></li>
							</ul>
						</div>
						
						<div id="mcd_list_my_selected_coupons"><strong>My Selected Coupons</strong> (<span class="mcd_panel_count"></span>)</div>
						
						<ul class="mcd_list_added_coupons"></ul>
						
						<ul id="panel_scroll_buttons">
							<li><button id="mcd_panel_scroll_up">Scroll Up</button></li>
							<li><button id="mcd_panel_scroll_down"">Scroll Down</button></li>
						</ul>
					</div>
				</div>
				<div style="display: none;"><div id="mcd-print-list-data"></div></div>
				<div style="display: none"><div id="mcd-plugin-location">' . plugins_url() . '/mcd-list' . '</div></div>';
				
				$data['header_count'] = count($_SESSION['mcd_list_plugin']['header_ids']);
			else:
				$data['message'] = 'No headers have been added';
			endif;
		endif;
		
		// Save Panel Orientation
		if($_POST['method'] == "panel_orientation"):
			if($_POST['class'] == 'mcd_list_container_show') $_SESSION['mcd_list_plugin']['panel_orientation'] = 'show';
			if($_POST['class'] == 'mcd_list_container_hide') $_SESSION['mcd_list_plugin']['panel_orientation'] = 'hide';
		endif;
		######################################################################
		# PRINT PAGE METHODS
		######################################################################
		if($_POST['method'] == "print_popup"):
			$include_file = str_replace('ajax', '', dirname (__FILE__)) . 'classes/print-page-class.php';
			$html = include $include_file;
			$return_html = new mcdl_print_list();
			//echo '<pre>'; var_dump($return_html); echo '</pre>';
			$data['html'] = $return_html->html_output;
		endif;
		######################################################################
		# RETURN JSON
		######################################################################
		$json = json_encode($data);
		echo $json;
	else:
		echo 'No Method Specified';
	endif;
endif;