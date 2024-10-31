<?php
class mcd_export {
	// Define Properties
	private $atts;
	private $options;
	
	// Construct
	function __construct($atts) {
		$this->atts = $atts;
		$this->options = get_option('mcd_list');
		$this->export();
	}
	
	// Execute Headers and Construct/Output Json
	function export() {
		$this->php_headers();
		$this->construct_json();
	}
	
	// Set PHP Headers
	function php_headers() {
		$filename = 'mcd-coupon-list-' . time() . '.txt';
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Content-Type: text/plain');
	}
	
	function data_basic_info(){
		$data = array(
			'plugin_version' => $this->options['version'],
			'siteurl' => get_bloginfo('url'),
			'name' => $this->atts['name'],
			'cat_group_name' => $this->atts['cat_group_name']
		);
		
		return $data;
	}
	
	function data_items(){
		global $wpdb;
		
		$data = $wpdb->get_results($wpdb->prepare(
			"SELECT h.id, h.name, h.price, h.cat_id, h.net_price, h.more_info, cat.name as department
			FROM mcd_cl_coupon_group h
			LEFT JOIN mcd_cl_categories cat ON h.cat_id = cat.id
			WHERE h.coupon_list_id = %d
			ORDER BY h.id DESC", array($this->atts['id'])
		), ARRAY_A);
		
		return $data;
	}
	
	function data_categories() {
		global $wpdb;
		
		// Pull All Categories for the list Category Group
		$data = $wpdb->get_results($wpdb->prepare(
			"SELECT c.id, c.name, c.cat_order, c.description, c.group_id, c.date
			FROM mcd_cl_lists l
			LEFT JOIN mcd_cl_categories c ON l.cat_group_id  = c.group_id
			WHERE l.id = %d", array($this->atts['id'])
		), ARRAY_A);
		
		/*
		// Pull Only the categories used for this list from the category group
		$data = $wpdb->get_results($wpdb->prepare(
			"SELECT DISTINCT c.id, c.name, c.cat_order, c.description, c.group_id, c.date
			FROM mcd_cl_coupon_group h
			LEFT JOIN mcd_cl_categories c ON h.cat_id = c.id
			WHERE h.coupon_list_id = %d AND h.cat_id != 0
			ORDER BY h.id DESC", array($this->atts['id'])
		), ARRAY_A);
		*/
		
		return $data;
	}
	
	function data_coupons() {
		global $wpdb;
		
		$data = $wpdb->get_results($wpdb->prepare(
			"SELECT id, name, store, value, expiration, source, created_by, coupon_group_id, coupon_list_id, mcd_id, coupon_url, stacks, date
			FROM mcd_cl_coupons
			WHERE coupon_list_id = %d", array($this->atts['id'])
		), ARRAY_A);
		
		return $data;
	}
		
	function construct_json() {
		// Set Array Var
		$data = array();
		
		// Compile Data Array
		$data['basic_info'] = $this->data_basic_info();
		$data['items'] = $this->data_items();
		$data['categories'] = $this->data_categories();
		$data['coupons'] = $this->data_coupons();
		
		// Return as JSON
		echo json_encode($data);
		
		// Kill to remove All Future Output
		die();
	}
}