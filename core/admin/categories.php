<?php global $wpdb; ?>

<div class="wrap">
	<div class="icon32" id="icon-edit-pages"><br /></div>
	<h2>Manage Categories</h2><br />
	
	<div id="mcd_cl_container">
		<div id="poststuff" class="meta-box-sortables">
		
			<!-- Add a New Category Group -->
			<form id="addCategoryGroupForm" method="post" action="#">
				<div class="stuffbox postbox">
					<div title="Click to toggle" class="handlediv"><br /></div>
					<h3><label for="link_name">Add a New Category Group</label></h3>
					<div class="inside">
						<input type="text" value="" size="50" name="name" /> 
						<input type="submit" value="Add Category Group" class="button" name="submit">
					</div>
				</div>
			</form>
			
			<!-- Expand/Collapse Options -->
			<p class="groupsExpandCollapse"><a href="#" class="mcd_cl_blueLink expandCatGroups">expand all groups</a> | <a href="#" class="mcd_cl_blueLink collapseCatGroups">collapse all groups</a></p>
			
			<!-- Loop Through Category Groups -->
			<div id="mcd_cl_category_groups">
				<?php echo mcdl_category_groups(); ?>
			</div>
			
			<p class="groupsExpandCollapse"><a href="#" class="mcd_cl_blueLink expandCatGroups">expand all groups</a> | <a href="#" class="mcd_cl_blueLink collapseCatGroups">collapse all groups</a></p>
			
		</div> <!-- END #poststuff -->	
	</div> <!-- END #mcd_cl_container -->
</div>