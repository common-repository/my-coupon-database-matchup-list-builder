<?php 
class mcd_import { 
	public $errors = array();
	private $json;
	private $options;
	private $category_group_id;
	private $category_ids;
	private $list_id;
	private $item_ids;

	// Construct
	public function __construct() {
		if(isset($_POST['mcd-import'])):
			// Set Vars
			$this->options = get_option('mcd_list');
	
			// If Validation Passes - Import List
			if($this->validate()) $this->import();
			
			// If Validation Fails - Display Errors
			else $this->display_errors();
		else:
			return false;
		endif;
	}
	######################################################################
	# VALIDATION
	######################################################################
	private function validate() {
		// If Validation Errors - Return False (Validation Failed)
		if($this->validate_check_file_errors()) return false;
		if($this->validate_check_file_type()) return false;
		if($this->validate_json()) return false;
		if($this->validate_version()) return false;
		
		// If No Errors - Return True (Validation Passed)
		return true;
	}
	
	// Make Sure There were no $_FILES Errors
	private function validate_check_file_errors() {
		if($_FILES['file']['error'] > 0) return $this->errors[] = '$_FILE Error Code ' . $_FILES['file']['error'];
		else return false;
	}
	
	// Check for correct File Type (text/plain)
	private function validate_check_file_type() {
		if($_FILES['file']['type'] != 'text/plain') return $this->errors[] = 'The file you are uploading is not a valid file type.';
		else return false;
	}
	
	// Make Sure The File is Readable via JSON & Set $json Class Property
	private function validate_json() {
		if($this->json = json_decode(file_get_contents($_FILES['file']['tmp_name']), TRUE)) return false;
		else return $this->errors[] = 'There was a problem reading the uploaded file.';
	}
	
	// Make sure the exported list version matches the currently installed plugins version
	private function validate_version() {
		if($this->json['basic_info']['plugin_version'] != $this->options['version']) 
			return $this->errors[] = 'The plugin version of the exported list was running a different plugin version. Plugin versions must be the same for import/export to function.';
		else return false;
	}
	
	// Display Errors if didn't Pass Validation
	private function display_errors() {
		echo '<h3 class="mcd_cl_red">Error(s) With File Upload</h3>';
		echo '<ul class="mcd_cl_file_upload_errors">';

		foreach($this->errors as $error):
			echo '<li>' . $error . '</li>';
		endforeach;

		echo '</ul>';
	}
	######################################################################
	# IMPORT FUNCTIONALITY
	######################################################################
	private function import(){
		$this->add_category_group();
		$this->add_categories();
		$this->add_list();
		$this->add_items();
		$this->add_coupons();
		
		echo '<h3 style="color: #009900">Your list was successfully imported.</h3>
		<p><a href="admin.php?page=mcd-list&id=' . $this->list_id . '">Click Here to View the Imported List</a></p>';
		//echo '<pre>'; var_dump($this->json); echo '</pre>';
	}
	
	// Import the Category Group
	private function add_category_group() {
		global $wpdb;
		$wpdb->query($wpdb->prepare(
			"INSERT INTO mcd_cl_category_group (name, origin, date) VALUES (%s, 'imported', NOW())", array($this->json['basic_info']['cat_group_name'])
		));
		
		// Set Category Group ID
		$this->category_group_id = $wpdb->insert_id;
	}
	
	// Add Categories
	private function add_categories() {
		global $wpdb;
		
		foreach($this->json['categories'] as $category):
			// Insert Categories one at a time
			$wpdb->query($wpdb->prepare(
				"INSERT INTO mcd_cl_categories
				(name, cat_order, description, group_id, date)
				VALUES (%s, %d, %s, %d, NOW())",
				array($category['name'], $category['cat_order'], $category['description'], $this->category_group_id)
			));
			
			// Create Category ID Array/Map
			$this->category_ids[$category['id']] = $wpdb->insert_id;
		endforeach;
	}
	
	private function add_list() {
		global $wpdb;
		$wpdb->query($wpdb->prepare(
			"INSERT INTO mcd_cl_lists
			(name, date, date_updated, cat_group_id)
			VALUES (%s, NOW(), NOW(), %d)",
			array($this->json['basic_info']['name'], $this->category_group_id)
		));
		
		// Set List Id
		$this->list_id = $wpdb->insert_id;
	}
	
	private function add_items() {
		global $wpdb;
		
		foreach($this->json['items'] as $item):
			// Insert Items one at a time
			$wpdb->query($wpdb->prepare(
				"INSERT INTO mcd_cl_coupon_group
				(name, price, cat_id, more_info, coupon_list_id, net_price, date)
				VALUES (%s, %s, %d, %s, %d, %s, NOW())",
				array($item['name'], $item['price'], $this->category_ids[$item['cat_id']], $item['more_info'], $this->list_id, $item['net_price'])
			));
			
			// Create Item ID Array/Map
			$this->item_ids[$item['id']] = $wpdb->insert_id;
		endforeach;
	}
	
	private function add_coupons() {
		global $wpdb;
		
		foreach($this->json['coupons'] as $coupon):
			// Insert Coupons one at a time
			$wpdb->query($wpdb->prepare(
				"INSERT INTO mcd_cl_coupons
				(name, store, value, expiration, source, created_by, coupon_group_id, coupon_list_id, mcd_id, coupon_url, stacks, date)
				VALUES (%s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, NOW())",
				array(
					$coupon['name'], 
					$coupon['store'], 
					$coupon['value'],
					$coupon['expiration'], 
					$coupon['source'], 
					$coupon['created_by'], 
					$this->item_ids[$coupon['coupon_group_id']],
					$this->list_id,
					$coupon['mcd_id'],
					$coupon['coupon_url'],
					$coupon['stacks']
				)
			));
		endforeach;
	}
}
new mcd_import();