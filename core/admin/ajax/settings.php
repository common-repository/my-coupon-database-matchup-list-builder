<?php
if(!empty($_POST) && is_admin()):
	global $wpdb;

	if(isset($_POST['method'])):
		if($_POST['method'] == 'update_settings'):
			$options = get_option('mcd_list');
			$new_options = array_merge($options, $_POST['settings']);
			update_option('mcd_list', $new_options);
			$data['settings'] = $_POST['settings'];
		endif;
	endif;
	
	// Return the data as JSON
	$json = json_encode($data);
	echo str_replace("null", "\"\"", $json);
endif;

