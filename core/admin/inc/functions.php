<?php
// Last Saved Function
function mcdl_date_updated($what, $id) {
	global $wpdb;
	if($what == 'coupon-list') $wpdb->query($wpdb->prepare("UPDATE mcd_cl_lists SET date_updated = NOW() WHERE id = %d", array($id)));
}

// Fetch Coupon Headers and Coupons
function mcdl_list_headers($coupon_list_id, $header_id = ''){
	ob_start();
	global $wpdb;
	$categories = mcdl_list_categories($coupon_list_id); // Store Categories in a Var
	$options = get_option('mcd_list'); // Grab Options
	
	// Grab Just This Header
	if($header_id):
		// Query Headers and Coupons
		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT h.id, h.price, h.name, h.net_price, h.more_info, cat.name as department, cat.id as cat_id
			FROM mcd_cl_coupon_group h
			LEFT JOIN mcd_cl_categories cat ON h.cat_id = cat.id
			WHERE h.id = %d 
			ORDER BY h.id DESC", array($header_id)
		));
	// Grab All Coupon Headers
	else:
		// XML Feed for MCD Coupons
		$results = $wpdb->get_results($wpdb->prepare("SELECT mcd_id FROM mcd_cl_coupons WHERE coupon_list_id = %d AND created_by = 'mcd'", array($coupon_list_id)));
		$mcd_id = ''; foreach($results as $row): $mcd_id .= '-' . $row->mcd_id; endforeach; $mcd_id = substr($mcd_id, 1);
		if($mcd_id): 
			$token = $options['api_key'];
			$xml_file = 'http://www.mycoupondatabase.com/api/coupons-xml.php?token=' . $token . '&id_string=' . $mcd_id;
			$xml = simplexml_load_file($xml_file);
		endif;
		
		// Query Headers and Coupons
		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT h.id, h.price, h.name, h.net_price, h.more_info, cat.name as department, cat.id as cat_id
			FROM mcd_cl_coupon_group h
			LEFT JOIN mcd_cl_categories cat ON h.cat_id = cat.id
			WHERE h.coupon_list_id = %d
			ORDER BY h.id DESC", array($coupon_list_id)
		));
	endif;
	
	// Loop Through Results
	foreach($results as $row):
	?>
		<div class="headerContentContainer" id="<?php echo $row->id; ?>">
			<!-- Header Display Bar -->
			<h4 class="headerLine">(<a href="#" class="mcd_cl_blueLink editCouponHeaderLink" rel="<?php echo $row->id; ?>">edit</a>) <span class="productName"><?php echo stripslashes($row->name); ?></span>
				<div class="noStrong">
					<span class="productPrice"><?php echo $row->price; ?></span> 
					<span class="mcd_cl_lightGrey productCategory">(<?php echo $row->department; ?>)</span> <a href="#" style="font-weight: normal" class="addCoupons">Add Coupons</a> - 
					<span><a href="#" class="mcd_cl_blueLink editNetPriceLink">enter <?php echo strtolower($options['text_net_price']); ?></a> | 
				<a href="#" class="mcd_cl_redLink deleteCouponHeaderLink" rel="<?php echo $row->id; ?>">delete item</a></span>
				</div>
			</h4>
	
			<!-- Edit Net Price -->
			<div class="editNetPriceContainer">
				<span class="netPriceHeader"><strong>Enter <?php echo $options['text_net_price']; ?></strong> - <a href="#" class="mcd_cl_redLink closeNetPriceLink">close <?php echo strtolower($options['text_net_price']); ?></a></span>
				<form method="post" action="#" id="editNetPriceForm">
					<input type="text" name="net_price" value="<?php echo $row->net_price; ?>" size="20">
					<input type="hidden" name="header_id" value="<?php echo $row->id; ?>" />
					<input type="submit" name="submit" value="Set <?php echo $options['text_net_price']; ?>" />
				</form>
			</div>
			
			<!-- Add Coupons -->
			<div class="addCouponsContainer">
				<div style="font-size: 12px; margin-bottom: 5px;">
					<a href="#" class="addYourOwnCouponLink" style="text-decoration: none;">Add Your Own Coupon</a> or <a href="#" class="searchCouponLink" style="text-decoration: none;">Search MyCouponDatabase.com</a>
				</div>
				
				<div class="mcdOrUserAddCouponsContainer">
					<input class="searchInput" type="text" name="search" size="80" value="<?php echo stripslashes($row->name); ?>" />
					
					<div class="searchResults">
						<h5 style="margin-bottom: 5px;">Search Results <span class="couponRecords"></span> - <a  href="#" class="closeSearch mcd_cl_redLink normal">close search results</a></h5>
						<ol class="couponResults"></ol>
					</div>
					
					<form method="post" action="#" id="addYourOwnCoupon">
						<div class="addYourOwnCouponContainer">
							<h5 style="margin: 10px 0 0 0;">Add Your Own Coupon</h5>
							<ul class="addYourOwnCouponFormList">
								<li><label>Coupon Name:</label><input type="text" name="name" value="" /></li>
								<li><label>Coupon Value:</label><input type="text" name="value" value="" /></li>
								<li><label>Expiration:</label><input type="text" name="expiration" value="" /></li>
								<li><label>Coupon Store:</label><input type="text" name="store" value="" /></li>
								<li><label>Coupon Source:</label><input type="text" name="source" value="" /></li>
								<li><label>Coupon URL:</label><input type="text" name="coupon_url" value="" /> (Full URL including http://)</li>
								<li><input type="submit" name="submit" value="Add Coupon" /></li>
							</ul>
						</div>
						<input type="hidden" name="coupon_group_id" value="<?php echo $row->id; ?>" />
						<input type="hidden" name="coupon_list_id" value="<?php echo $coupon_list_id; ?>" />
					</form>
					
				</div>
			</div>
			
			<!-- Edit Header -->
			<div class="editHeaderContainer">
				<form method="post" action="#" id="editHeader">
					<ul>
						<li><label>Item Name:</label><input type="text" name="header_name" value="<?php echo stripslashes($row->name); ?>" size="50"></li>
						<li><label>Value:</label><input type="text" name="header_value" value="<?php echo $row->price; ?>" size="10"></li>
						<li><label>Category:</label>
							<select name="header_category" class="header_categories">
								<?php
								if($categories):
									echo '<option></option>';
									foreach($categories as $cat_row):
										$selected = ''; if($row->cat_id == $cat_row->id): $selected = ' selected="selected"'; endif;
										echo '<option value="' . $cat_row->id . '"' . $selected . '>' . $cat_row->name . '</option>';
									endforeach;
								endif;
								?>
							</select>
						</li>
						<li><label>More Info:</label><textarea name="header_more_info"><?php echo stripslashes($row->more_info); ?></textarea></li>
						<li><input type="submit" name="submit" value="Update Header" /></li>
					</ul>
					<input type="hidden" name="header_id" value="<?php echo $row->id; ?>" />
					<input type="hidden" name="coupon_list_id" value="<?php echo $coupon_list_id; ?>" />
				</form>
			</div>
		
			<!-- Show Coupons That are Attached to this Header -->
			<ul class="couponsAdded" id="<?php echo $row->id; ?>">
			<?php
			// Grab All Coupons for this Header
			$coupon_results = $wpdb->get_results(
				"SELECT id, store, name, value, expiration, source, created_by, mcd_id, stacks 
				FROM mcd_cl_coupons 
				WHERE coupon_group_id = $row->id
				ORDER BY id DESC"
			);
			foreach ($coupon_results as $coupon_row):
				mcdl_coupons($coupon_row, $xml, $options);
			endforeach; // end looping through coupons for this header
				?>
			</ul>
			
			<!-- End of Header Parts -->
			
		</div> <!-- End .headerContentContainer -->
	<?php endforeach; // end looping through headers
	return ob_get_clean();
} // end mcdl_list_headers function

function mcdl_coupons($coupon_row, $xml, $options){
	if($coupon_row->created_by == "client"):
	?>
		<li>
			<a href="#" class="stackCoupon <?php if($coupon_row->stacks == 0): echo 'mcd_cl_greyLink'; else: echo 'mcd_cl_redLinkBold'; endif; ?>" rel="<?php echo $coupon_row->id; ?>"><?php echo $options['text_coupon_stack']; ?></a>
			<?php if(!empty($coupon_row->store)): ?> (<em><?php echo stripslashes($coupon_row->store); ?></em>) <?php endif; ?>
			<?php echo stripslashes($coupon_row->name); ?> 
			<?php if(!empty($coupon_row->value)): ?> - <span style="color: blue"><?php echo stripslashes($coupon_row->value); ?></span> <?php endif; ?>
			<?php if($coupon_row->expiration != '0000-00-00 00:00:00'): ?> - <span style="color: red">(<?php echo $options['text_expiration'] . ' ' . date("m/d/y", strtotime($coupon_row->expiration)); ?>)</span> <?php endif; ?>
			<?php if(!empty($coupon_row->source)): ?><span style="color: #666">(<?php echo stripslashes($coupon_row->source); ?>)</span> - <?php endif; ?>
			<a href="#" class="deleteCoupon mcd_cl_redLink" rel=<?php echo $coupon_row->id; ?>>remove</a>
		</li>
	<?php else:	$cid = 'coupon' . $coupon_row->mcd_id; ?>
		<li>
			<a href="#" class="stackCoupon <?php if($coupon_row->stacks == 0): echo 'mcd_cl_greyLink'; else: echo 'mcd_cl_redLinkBold'; endif; ?>" rel="<?php echo $coupon_row->id; ?>"><?php echo $options['text_coupon_stack']; ?></a>
			(<em><?php echo $xml->$cid->storename; ?></em>) <?php echo $xml->$cid->couponname; ?> - 
			<span style="color: blue"><?php echo $xml->$cid->value; ?></span> - 
			<span style="color: red">(<?php echo $options['text_expiration'] . ' ' . date("m/d/y", strtotime($xml->$cid->expiration)); ?>)</span> 
			<span style="color: #666">(<?php echo stripslashes($xml->$cid->mainsource); ?>)</span> - 
			<a href="#" class="deleteCoupon mcd_cl_redLink" rel=<?php echo $coupon_row->id; ?>>remove</a>
			<?php if(!empty($xml->$cid->couponnotes)): ?>
				<div class="mcd_cl_coupon_notes"><em>Coupon Note: <?php echo $xml->$cid->couponnotes; ?></em></div>
			<?php endif; ?>
		</li>
	<?php endif;
}
 
function mcdl_category_groups($group_id = '') {
	ob_start();
	global $wpdb;
	if($group_id): 
		$category_groups = $wpdb->get_results($wpdb->prepare("SELECT id, name, origin FROM mcd_cl_category_group WHERE id = %d ORDER BY date DESC", array($group_id)));
	else: 
		$category_groups = $wpdb->get_results("SELECT id, name, origin FROM mcd_cl_category_group ORDER BY date DESC"); 
	endif;
	foreach($category_groups as $group):
		$categories = $wpdb->get_results("SELECT id, name, cat_order, description, date FROM mcd_cl_categories WHERE group_id = $group->id ORDER BY cat_order");
?>
	<!-- Display Category Group -->
	<div class="stuffbox postbox" id="<?php echo $group->id; ?>">
		<div title="Click to toggle" class="handlediv"><br /></div>
		<h3><label for="link_name"><?php echo $group->name; if($group->origin == 'imported') echo ' (imported)'; ?></label></h3>
		<div class="inside"<?php if(count($category_groups) == 1): echo ' style="display: block;"'; endif; ?>>
			
			<!-- Add a New Category -->
			<form class="addCategoryForm" method="post" action="#">
				<input type="text" value="" size="50" name="name" class="catName" /> 
				<input type="hidden" value="<?php echo $group->id; ?>" name="group_id" />
				<input type="submit" value="Add Category" class="button" name="submit">
			</form>
			
			<!-- Sort Categories Markup -->
			<form class="saveSortOrderForm" method="post" action="#" style="display: <?php if($categories): echo 'block'; else: echo 'none'; endif; ?>">
				<div class="mcd_cl_save_order_submit_container">
					<div style="float: left"><input type="submit" value="Save List Order" class="button" name="submit"></div><div style="float: left; padding: 7px 0 0 5px;"><span class="mcd_cl_savedListNotification">Category List Order Has Been Saved</span></div>
				</div>
				
				<ul class="couponCatsList">
				<?php 
				foreach($categories as $category):
					echo '<li>';
						if($category->cat_order == 0): echo '<span class="mcd_cl_red mcd_cl_unordered">(Unordered) </span>'; endif;
				?>
						<input type="hidden" name="id[]"  value="<?php echo $category->id; ?>" />
						<span class="mcd_cl_categoryName"><?php echo $category->name; ?></span> - 
						<span class="mcd_cl_lightGrey">Created <?php echo date("F d, Y", strtotime($category->date)); ?></span> - 
						<a href="#" class="mcd_cl_update_cat_description_link">update description</a> 
						
						(<a href="#" class="mcd_cl_blueLink renameCategoryLink" rel="<?php echo $category->id; ?>">rename category</a> | 
						<a href="#" class="mcd_cl_redLink deleteCategoryLink" rel="<?php echo $category->id; ?>">delete category</a>)
						
						<p class="mcd_cl_renameCategoryContainer">
							<input type="text" name="rename_category" value="<?php echo $category->name; ?>" size="30" /> 
							<input type="button" name="button" value="Rename Category" />
						</p>
						
						<p class="mcd_cl_cat_description_container">
							<input type="text" name="rename_category" value="<?php echo $category->description; ?>" size="80" /> 
							<input type="button" name="submit" value="Update Description" />
						</p>
					</li>
				<?php endforeach; ?>
				</ul>
				
				<div class="mcd_cl_save_order_submit_container">
					<div style="float: left"><input type="submit" value="Save List Order" class="button" name="submit"></div>
					<div style="float: left; padding: 7px 0 0 5px;"><span class="mcd_cl_savedListNotification">Category List Order Has Been Saved</span></div>
				</div>
				<input type="hidden" value="<?php echo $group->id; ?>" name="group_id" />
			</form>
			
			<!-- Delete Category Group -->
			<p id="deleteGroupContainer"><a href="#" class="mcd_cl_redLink deleteCatGroupLink" rel="<?php echo $group->id; ?>">Delete this Category Group</a></p>
			
		</div> <!-- END .inside -->
	</div> <!-- END .stuffbox -->
	<?php endforeach; // stop looping through groups
	return ob_get_clean();
} // mcdl_coupon_groups function

// Grab Categories for the Current List
function mcdl_list_categories($id) {
	global $wpdb;
	$categories = $wpdb->get_results($wpdb->prepare(
		"SELECT c.id, c.name, c.group_id 
		FROM mcd_cl_lists l
		LEFT JOIN mcd_cl_categories c ON l.cat_group_id = c.group_id
		WHERE l.id = %d
		ORDER BY name ASC", array($id)
	));
	return $categories;
}