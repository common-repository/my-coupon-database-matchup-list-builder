<?php
if(!empty($_POST) && $_POST['action'] == "mcdl_categories" && is_admin()):
	#######################################################################
	# UNIVERSAL
	#######################################################################
	global $wpdb; // Grab WordPress Global DB Class
	include WP_PLUGIN_DIR .'/my-coupon-database-matchup-list-builder/core/admin/inc/functions.php'; // Include Functions
	if($_POST['coupon_list_id']): mcdl_date_updated('coupon-list', $_POST['coupon_list_id']); endif; // Update Last Updated in Database
	if(isset($_POST['method'])):
		#######################################################################
		# CATEGORY FUNCTIONS
		#######################################################################
		// Add New Category Group
		if($_POST['method'] == "add_category_group"):
			$wpdb->query($wpdb->prepare(
				"INSERT INTO mcd_cl_category_group (name, origin, date) VALUES(%s, 'native', NOW())", array($_POST['name'])
			));
			$data['newCategoryGroup'] = mcdl_category_groups($wpdb->insert_id);
		endif;
		
		// Delete Category Group
		if($_POST['method'] == "delete_category_group"):
			// Remove Categories with this Group ID
			$wpdb->query($wpdb->prepare("DELETE FROM mcd_cl_categories WHERE group_id = %d", array($_POST['group_id'])));
			
			// Remove Category Group
			$wpdb->query($wpdb->prepare("DELETE FROM mcd_cl_category_group WHERE id = %d", array($_POST['group_id'])));
			
			// Send Back a Response
			$data['response'] = 'Category Group Removed';
		endif;
		
		// Add New Category
		if($_POST['method'] == "add_category"):
			$cat_order = 0000000000;
			$wpdb->query($wpdb->prepare(
				"INSERT INTO mcd_cl_categories (name, cat_order, date, group_id) VALUES(%s, $cat_order, NOW(), %d)", array($_POST['name'], $_POST['group_id'])
			));
			$data['li'] = '<li><input type="hidden" name="id[]"  value="' . $wpdb->insert_id . '" />
				<span class="mcd_cl_red mcd_cl_unordered">(Unordered) </span> <span class="mcd_cl_categoryName">' . $_POST['name'] . '</span>
				 - <span class="mcd_cl_lightGrey">Created ' . date("F d, Y", time()) . '</span> 
				 <a href="#" class="mcd_cl_update_cat_description_link">update description</a> 
				 
				(<a href="#" class="mcd_cl_blueLink renameCategoryLink" rel="' . $wpdb->insert_id . '">rename category</a> | 
				<a href="#" class="mcd_cl_redLink deleteCategoryLink" rel="' . $wpdb->insert_id . '">delete category</a>)
				
				<p class="mcd_cl_renameCategoryContainer">
					<input name="rename_category" value="' . $_POST['name'] . '" size="30" /> 
					<input name="rename_category_button" type="button" value="Rename Category" />
				</p>
				<p class="mcd_cl_cat_description_container">
					<input type="text" name="rename_category" value="" size="80" /> 
					<input type="button" name="submit" value="Update Description" />
				</p>
			</li>';
		endif;
		
		// Rename Category
		if($_POST['method'] == "rename_category"):
			$wpdb->query($wpdb->prepare(
				"UPDATE mcd_cl_categories SET name = %s WHERE id = %d", array($_POST['name'], $_POST['id'])
			));
			$data['response'] = 'Category Renamed';
		endif;
		
		// Delete Category
		if($_POST['method'] == "delete_category"):
			$wpdb->query($wpdb->prepare(
				"DELETE FROM mcd_cl_categories WHERE id = %d", array($_POST['id'])
			));
			$data['response'] = 'Category Deleted';
		endif;
		
		// Update Category Description
		if($_POST['method'] == "category_description"):
			$wpdb->query($wpdb->prepare(
				"UPDATE mcd_cl_categories SET description = %s WHERE id = %d", array($_POST['description'], $_POST['id'])
			));
			$data['response'] = 'Category Description Updated';
		endif;
		
		// Save Sort Order
		if($_POST['method'] == "save_order"):
			for($i = 0; $i < count($_POST['id']); $i++):
				$cat_order = $i + 1;
				$wpdb->query($wpdb->prepare(
					"UPDATE mcd_cl_categories SET cat_order = $cat_order WHERE id = %d", array($_POST['id'][$i])
				));
			endfor;
			$data['response'] = 'Coupon List Order Saved';
		endif;
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