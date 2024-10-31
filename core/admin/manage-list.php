<?php
global $wpdb; 
$mcd_list_categories = mcdl_list_categories($_GET['id']); // Grab Categories for this list
?>

<div class="wrap" id ="<?php if(isset($_GET['id'])): echo $_GET['id']; endif; ?>">
	<div class="icon32" id="icon-edit-pages"><br /></div>
	<h2>Manage My Lists<a class="button add-new-h2" href="admin.php?page=mcdl-new-list" target="_blank">Create a List</a></h2><br />
	
	<?php 
	if(isset($_GET['id']) && !empty($_GET['id'])):		
		$stmt = $wpdb->get_row($wpdb->prepare(
			"SELECT l.id, l.name, l.date, l.date_updated, l.cat_group_id, cg.name as cat_group_name, cg.description as cat_group_description
			FROM mcd_cl_lists l
			LEFT JOIN mcd_cl_category_group cg ON l.cat_group_id = cg.id
			WHERE l.id = %s", array($_GET['id'])
		));
	?>
		<h3>
			<?php echo stripslashes($stmt->name); ?> - 
			<span class="mcd_cl_shortcode_span">[coupon-list id="<?php echo $stmt->id; ?>"]</span> - 
			<span class="mcd_cl_list_created_span">Created <?php echo date("m/d/y", strtotime($stmt->date)); ?></span> - 
			<span class="mcd_cl_list_savedtext_span">Last Saved </span><span class="mcd_cl_list_save_date_span"><?php echo date("m/d/y @ g:ia", strtotime($stmt->date_updated)); ?></span>
		
			<form action="" method="post" id="export-filters" style="display: inline;">
				<input type="hidden" name="export-list" value="true" />
				<input type="hidden" name="id" value="<?php echo $stmt->id; ?>" />
				<input type="hidden" name="name" value="<?php echo $stmt->name; ?>" />
				<input type="hidden" name="cat_group_name" value="<?php echo $stmt->cat_group_name; ?>" />
				<!-- <input type="hidden" name="cat_group_description" value="<?php echo $stmt->cat_group_description; ?>" /> -->
				<input type="submit" value="Export List to File" class="button-secondary" id="submit" name="submit">
			</form>
			
		</h3>
		
		<div class="updated" id="message"><p style="font-weight: bold;"></p></div>
		
		<div id="mcd_cl_container">
			<div id="poststuff" class="meta-box-sortables">
			
				<!-- Add Multiple New Headers -->
				<form id="createMultipleHeaders" method="post" action="#">
					<div class="stuffbox postbox">
						<div title="Click to toggle" class="handlediv"><br /></div>
						<h3><label for="link_name">Add Multiple Items</label></h3>
						<div class="inside multipleHeaders">
							<table>
								<tr>
									<th></th>
									<th>Product Name</th>
									<th>Value</th>
									<th>Category</th>
									<th>More Info</th>
								</tr>
								
								<?php for($i = 1; $i <= 20; $i++): ?>
								<tr>
									<td><?php echo $i . '.'; ?></td>
									<td><input type="text" name="name[]" size="30" value=""  /></td>
									<td><input type="text" name="value[]" size="10" value=""  /></td>
									<td>
										<select name="department[]">
											<option></option>
											<?php
											foreach($mcd_list_categories as $row):
												echo '<option value="' . $row->id . '">' . $row->name . '</option>';
											endforeach;
											?>
										</select>
									</td>
									<td><input type="text" name="more_info[]" size="50" value="" /></td>
								</tr>
								<?php endfor; ?>
								
							</table>
							
							<p class="submit">
								<input type="hidden" name="coupon_list_id" value="<?php echo $_GET['id']; ?>" />
								<input type="submit" value="Create Item Headers" class="button" name="submit">
							</p>
							
						</div> <!-- END .inside -->
					</div> <!-- END .stuffbox -->
				</form> <!-- END #createMultipleHeaders form -->
			
				<!-- Add a New Header -->
				<form id="createHeader" method="post" action="admin.php?page=mcd-list&id=<?php $_GET['page']; ?>">
					<div class="stuffbox postbox">
						<div title="Click to toggle" class="handlediv"><br /></div>
						<h3><label for="link_name">Add a Single Item</label></h3>
						<div class="inside">
							<ul class="couponHeaderList">
								<li>
									<label for="name">Item Name: </label>
									<input type="text" value="" size="50" name="name" maxlength="60" />
									<p class="dateColor">This will be the item name to show in your list.</p>
								</li>
								<li>
									<label for="value">Value: </label>
									<input type="text" value="" size="50" name="value" maxlength="36" />
									<p class="dateColor"> This will be the price, measurement, or other qualifiers for the Item name. It will be displayed after the Item Name as: "Item Name Value" within your post.</p>
								</li>
								<li>
									<label for="department">Category: </label>
									<select name="department" id="categories">
										<option></option>
										<?php
										foreach($mcd_list_categories as $row):
											echo '<option value="' . $row->id . '">' . $row->name . '</option>';
										endforeach;
										?>
									</select>
									<p class="dateColor">Select the category or other distinction to group Items together.</p>
								</li>
								<li>
									<label for="department">More Info: </label>
									<textarea name="more_info"></textarea>
									<p class="dateColor">Additional notes or details regarding this Item. This field will be displayed under the Item Name within your post.</p>
								</li>
							</ul>
							
							<p class="submit">
								<input type="hidden" name="coupon_list_id" value="<?php echo $_GET['id']; ?>" />
								<input type="submit" value="Create Item Header and Add Another Item" class="button" name="submit">
							</p>
							
						</div> <!-- END .inside -->
					</div> <!-- END .stuffbox -->
				</form> <!-- END #createHeader -->
				
				<!-- Header and Coupons for this List -->
				<div class="stuffbox postbox">
					<div title="Click to toggle" class="handlediv"><br /></div>
					<h3><label for="link_name">Coupon Headers and Coupons For This List</label></h3>
					<div class="inside">
						<div id="couponHeaders">
							<?php echo mcdl_list_headers($_GET['id']); ?>
						</div>
					</div>
				</div>
			</div> <!-- End #poststuff -->
		</div> <!-- End #mcd_cl_container -->
	
	<?php
	else: // Return All Lists Created
		$result = $wpdb->get_results("SELECT id, name, date FROM mcd_cl_lists ORDER BY date DESC");
		if (count($result) > 0):
	?>
			<h3>Select a List to Manage</h3>
			<ul id="my_lists">
				<?php foreach($result as $row): ?>
				<li>
					<a href="admin.php?page=mcd-list&id=<?php echo $row->id; ?>" class="listNameLink"><?php echo stripslashes($row->name); ?></a> - 
					<span class="shortCode">[coupon-list id="<?php echo $row->id; ?>"]</span>
					<span class="dateColor">Created <?php echo date("F d, Y", strtotime($row->date)); ?></span> - 
					<a href="#" class="renameListLink">rename</a> | 
					<a href="#" class="mcd_cl_redLink deleteListLink">delete</a>
					<form method="post" action="#" id="renameListForm">
						<div class="deleteList">
							<input class="listNameInput" type="text" name="name" value="<?php echo stripslashes($row->name); ?>" size="60" />
							<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
							<input type="submit" name="Submit" value="Rename List" />
						</div>
					</form>
				</li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<h3>No Lists Found</h3>
			Looks like you do not have any lists created yet. <a href="?page=mcdl-new-list">Create your first list!</a>
		<?php endif; ?>
	<?php endif; ?>
</div>