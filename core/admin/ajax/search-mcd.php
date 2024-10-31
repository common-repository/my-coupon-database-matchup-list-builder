<?php
if(!empty($_POST) && (isset($_POST['search']) && !empty($_POST['search'])) && is_admin()):
	// Retreive XML
	global $wpdb;
	$options = get_option('mcd_list');
	$token = $options['api_key'];
	$xml_file = 'http://www.mycoupondatabase.com/api/coupons-xml.php?token=' . $token . '&search=' . urlencode($_POST['search']);
	$xml = simplexml_load_file($xml_file);
	
	// Loop Through Results
	$data['coupon_results'] = '';
	foreach($xml as $key => $val):
		$clean_key = str_replace('coupon', '', $key);
		$data['coupon_results'] .= '
			<li style="padding: 5px;" id="' . $key . '">
				(<em>' . $val->storename . '</em>) ' . $val->couponname . ' - 
				<span style="color: blue">' . $val->value . '</span> - 
				<span style="color: red">(' . $options['text_expiration'] . ' ' . date("m/d/y", strtotime($val->expiration)) . ')</span> 
				<span style="color: #666">(' . $val->mainsource . ')</span> - 
				<a href="#" class="addCoupon" rel=' . $clean_key . '>Add Coupon</a>
				<span class="mcd_cl_remove_coupon_container"></span>';
				if(!empty($val->couponnotes)):
					$data['coupon_results'] .= '<div class="mcd_cl_coupon_notes"><em>Coupon Note: ' . $val->couponnotes . '</em></div>';
				endif;
			$data['coupon_results'] .= '</li>';
	endforeach;
	
	// Set Number of Rows Found
	$data['number_of_records'] = count($xml);
	
	// Return the data as JSON
	$json = json_encode($data);
	echo str_replace("null", "\"\"", $json);
endif;