<?php
if(!empty($_POST) && $_POST['action'] == "mcdl_list_functions" && is_admin()):
	if(isset($_POST['method'])):
		#######################################################################
		# UNIVERSAL
		#######################################################################
		global $wpdb; // Grab WordPress Global DB Class
		include WP_PLUGIN_DIR .'/my-coupon-database-matchup-list-builder/core/admin/inc/functions.php'; // Include Functions
		if($_POST['coupon_list_id']): mcdl_date_updated('coupon-list', $_POST['coupon_list_id']); endif; // Update Last Updated in Database
		$options = get_option('mcd_list');
		#######################################################################
		# MANAGE ALL LISTS
		#######################################################################
		// Create New List
		if($_POST['method'] == "create_list"):
			// Insert Coupon Name Into Database
			$shortcode = 'couponlist' . time();
			$wpdb->query( $wpdb->prepare(
				"INSERT INTO mcd_cl_lists (name, shortcode, date, date_updated, cat_group_id) VALUES (%s, %s, NOW(), NOW(), %d)",
				array($_POST['name'], $shortcode, $_POST['group_id'])
			));
			// Grab ID of the last insert
			$data['list_id'] = $wpdb->insert_id;
		endif;
		
		// Rename List
		if($_POST['method'] == "rename_list"):
			$wpdb->query($wpdb->prepare(
				"UPDATE mcd_cl_lists SET name = %s, date_updated = NOW() WHERE id = %d", array($_POST['name'], $_POST['id'])
			));
		endif;
		
		// Delete List and All Children Headers and Coupons
		if($_POST['method'] == "delete_list"):
			// Find All Coupon Groups Belonging to this List
			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT id FROM mcd_cl_coupon_group WHERE coupon_list_id = %d", array($_POST['coupon_list_id'])
			));
			
			// Loop Through Each Coupon Group and Delete Coupons
			foreach($results as $row):
				$wpdb->query($wpdb->prepare("DELETE FROM mcd_cl_coupons WHERE coupon_group_id = %d", array($row->id)));
			endforeach;
			
			// Delete Coupon Groups
			$wpdb->query($wpdb->prepare("DELETE FROM mcd_cl_coupon_group WHERE coupon_list_id = %d", array($_POST['coupon_list_id'])));
			
			// Delete Actual Coupon List
			$wpdb->query($wpdb->prepare("DELETE FROM mcd_cl_lists WHERE id = %d", array($_POST['coupon_list_id'])));
		endif;
		#######################################################################
		# MANAGE HEADERS - SINGLE LIST
		#######################################################################
		// Add Multiple Headers
		if($_POST['method'] == "multiple_headers"):
			for($i = 0; $i < count($_POST['name']); $i++):
				if(empty($_POST['name'][$i]) && empty($_POST['value'][$i]) && empty($_POST['department'][$i]) && empty($_POST['more_info'][$i])): else:
					$wpdb->query($wpdb->prepare(
						"INSERT INTO mcd_cl_coupon_group (name, price, cat_id, more_info, coupon_list_id, date) VALUES (%s, %s, %d, %s, %s, NOW())", 
						array($_POST['name'][$i], $_POST['value'][$i], $_POST['department'][$i], $_POST['more_info'][$i], $_POST['coupon_list_id'])
					));
				endif;
			endfor;
		endif;
		
		// Add a Header
		if($_POST['method'] == "new_header"):
			$wpdb->query($wpdb->prepare(
				"INSERT INTO mcd_cl_coupon_group (name, price, cat_id, more_info, coupon_list_id, date) VALUES (%s, %s, %d, %s, %s, NOW())", 
				array($_POST['name'], $_POST['value'], $_POST['department'], $_POST['more_info'], $_POST['coupon_list_id'])
			));
			
			$data['response'] = 'Added the product header <strong>' . stripslashes($_POST['name']) . '</strong>';
			$data['id'] = $wpdb->insert_id;
			
			// Fetch Header + HTML and store as a var to be appended
			$data['couponHeader'] = mcdl_list_headers($_POST['coupon_list_id'], $data['id']);		
		endif; // end new header
		
		// Update A Header
		if($_POST['method'] == "edit_header"):
			$wpdb->query($wpdb->prepare(
				"UPDATE mcd_cl_coupon_group 
				SET name = %s, price = %s, cat_id = %d, more_info = %s WHERE id = %d", 
				array($_POST['header_name'], $_POST['header_value'], $_POST['header_category'], $_POST['header_more_info'], $_POST['header_id'])
			));
			
			// Grab New Category Name
			$row = $wpdb->get_row($wpdb->prepare(
				"SELECT name FROM mcd_cl_categories WHERE id = %d", array($_POST['header_category'])
			));
			$data['header_category'] = $row->name;
			
			// Return Data for Population
			$data['header_name'] = $_POST['header_name'];
			$data['header_value'] = $_POST['header_value'];
			$data['header_more_info'] = $_POST['header_more_info'];
		endif;
		
		// Delete Header and Its Coupons from Database
		if($_POST['method'] == "remove_header"):
			// Remove Header
			$wpdb->query($wpdb->prepare("DELETE FROM mcd_cl_coupon_group WHERE id = %d", array($_POST['header_id'])));
		
			// Remove Coupons
			$wpdb->query($wpdb->prepare("DELETE FROM mcd_cl_coupons WHERE coupon_group_id = %d", array($_POST['header_id'])));
		endif;
		
		// Update Net Price
		if($_POST['method'] == "net_price"):
			$wpdb->query($wpdb->prepare(
				"UPDATE mcd_cl_coupon_group SET net_price = %s WHERE id = %d", array($_POST['net_price'], $_POST['header_id'])
			));
		endif;
		#######################################################################
		# MANAGE COUPONS - SINGLE LIST
		#######################################################################
		// Insert New Coupon into Database - User Submitted
		if($_POST['method'] == "add_user_coupon"):
			if(!empty($_POST['expiration'])): 
				$expiration = date("Y-m-d H:i:s", strtotime($_POST['expiration']));
				$wpdb->query($wpdb->prepare(
					"INSERT INTO mcd_cl_coupons 
					(name, store, value, expiration, source, created_by, coupon_group_id, coupon_list_id, coupon_url, stacks, date)
					VALUES (%s, %s, %s, %s, %s, 'client', %d, %d, %s, 0, NOW())", 
					array($_POST['name'], $_POST['store'], $_POST['value'], $expiration, $_POST['source'], $_POST['coupon_group_id'], $_POST['coupon_list_id'], $_POST['coupon_url'])
				));
			else:
				$wpdb->query($wpdb->prepare(
					"INSERT INTO mcd_cl_coupons 
					(name, store, value, source, created_by, coupon_group_id, coupon_list_id, coupon_url, stacks, date)
					VALUES (%s, %s, %s, %s, 'client', %d, %d, %s, 0, NOW())", 
					array($_POST['name'], $_POST['store'], $_POST['value'], $_POST['source'], $_POST['coupon_group_id'], $_POST['coupon_list_id'], $_POST['coupon_url'])
				));
			endif;
			
			$data['id'] = $wpdb->insert_id;
			
			// Create a new <li> to be added to the DOM
			$data['li_item'] = 
				'<li>
					<a href="#" class="stackCoupon mcd_cl_greyLink" rel="' . $wpdb->insert_id . '">' . $options['text_coupon_stack'] . '</a> ';
					if(!empty($_POST['store'])): $data['li_item'] .= '(<em>' . stripslashes($_POST['store']) . '</em>) '; endif;
					$data['li_item'] .= stripslashes($_POST['name']) . ' ';
					if(!empty($_POST['value'])): $data['li_item'] .= ' - <span style="color: blue">' . stripslashes($_POST['value']) . '</span> '; endif;
					if(!empty($_POST['expiration'])): $data['li_item'] .= ' - <span style="color: red">(' . $options['text_expiration'] . ' ' . date("m/d/y", strtotime($_POST['expiration'])) . ')</span> '; endif;
					if(!empty($_POST['source'])): $data['li_item'] .= '<span style="color: #666">(' . stripslashes($_POST['source']) . ')</span> - '; endif;
					$data['li_item'] .= '<a href="#" class="deleteCoupon mcd_cl_redLink" rel=' . $wpdb->insert_id . '>remove</a>
				</li>';
		endif;
		
		// Insert New Coupon into Database - MCD Provided
		if($_POST['method'] == "add_mcd_coupon"):
			$wpdb->query($wpdb->prepare(
				"INSERT INTO mcd_cl_coupons (created_by, coupon_group_id, coupon_list_id, mcd_id, stacks, date) 
				VALUES ('mcd', %d, %d, %d, 0, NOW())", array($_POST['coupon_group_id'], $_POST['coupon_list_id'], $_POST['mcd_id'])
			));
			$data['insert_id'] = $wpdb->insert_id;
			$data['stack_text'] = $options['text_coupon_stack'];
		endif;
		
		// Delete Coupon from Header and Database
		if($_POST['method'] == "remove_coupon"):
			$wpdb->query($wpdb->prepare("DELETE FROM mcd_cl_coupons WHERE id = %d", array($_POST['coupon_id'])));
		endif;
		
		// Stack Coupon
		if($_POST['method'] == "stack_coupon"):
			$wpdb->query($wpdb->prepare("UPDATE mcd_cl_coupons SET stacks = 1 WHERE id = %d", array($_POST['coupon_id'])));
		endif;
		
		// Unstack Coupon
		if($_POST['method'] == "unstack_coupon"):
			$wpdb->query($wpdb->prepare("UPDATE mcd_cl_coupons SET stacks = 0 WHERE id = %d", array($_POST['coupon_id'])));
		endif;
		#######################################################################
		# EXPORT LIST
		#######################################################################
		/* if($_POST['method'] == "export_list"):
			header('Content-type: text/xml');
			header('Content-Disposition: attachment; filename="text.xml"');
			echo $xml_contents;
		endif; */
	#######################################################################
	# UNIVERSAL
	#######################################################################
		// Return the data as JSON
		$json = json_encode($data);
		echo str_replace("null", "\"\"", $json);
	else:
		echo 'No Method Specified';
	endif;
endif;