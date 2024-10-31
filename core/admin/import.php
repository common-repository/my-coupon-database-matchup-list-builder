<?php if(is_admin()): ?>

<div class="wrap">
	<div class="icon32" id="icon-edit-pages"><br /></div><h2>Import</h2><br />
	<div class="updated" id="message"></div>

	<h3>Import a List</h3>
	<p>Select a list to import from your computer. This will be stored as a XML file and should have been exported from another site that contains the same version of the MCD List Plugin.</p>
	<form id="file-form" action="<?php echo get_admin_url(); ?>admin.php?page=mcdl-import" method="post" enctype="multipart/form-data">
		<input size="50" type="file" id="file" name="file" />
		<input type="hidden" name="mcd-import" value="true" />
		<input type="submit" value="Upload" class="button" id="html-upload" name="html-upload" />
	</form>

	<?php require_once('inc/mcd_import.class.php'); ?>

</div>

<?php endif; ?>