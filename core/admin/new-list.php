<?php 
global $wpdb; // WordPress DB Class Global
$category_groups = $wpdb->get_results("SELECT id, name FROM mcd_cl_category_group ORDER BY date DESC"); // Select Category Groups
?>

<div class="wrap">
	<div class="icon32" id="icon-edit-pages"><br /></div><h2>Create a List <a class="button add-new-h2" href="admin.php?page=mcd-list">View My Lists</a></h2><br />

	<div class="error below-h2" id="message"><ul></ul></div>

	<div id="poststuff">
		<div class="stuffbox">
			<h3><label for="link_name">Create a New List</label></h3>
			<div class="inside">
				<form method="post" action="#" id="mcd_cl_create_list">
				
					<!-- List Name Field -->
					<input type="text" value="" tabindex="1" size="60" name="coupon-list-name">
					<p>* Give your list a name so you can identify it later.</p>
					
					<!-- Category Group Field -->
					<select name="group_id" id="group_id">
						<option value=""></option>
						<?php 
						foreach ($category_groups as $group):
							echo '<option value="' . $group->id . '">' . $group->name . '</option>';
						endforeach;
						?>
					</select>
					<p>* Select a Category Group to be used with this list. You cannot choose another group for the list once the list has been created. <a href="admin.php?page=mcdl-categories">Manage Category Groups</a></p>
					<input type="submit" value="Create List" class="button" name="create_list">
				</form>
			</div>
		</div>
	</div>
	
</div>