<?php
/*
Plugin Name: My Coupon Database - List Plugin
Plugin URI: http://www.mycoupondatabase.com
Description: The My Coupon Database Coupon List and Coupon Match plugin allows you to create customized lists and add coupon matches in one place.
Author: My Coupon Database
Version: 1.0.3
Author URI: http://www.mycoupondatabase.com
*/
######################################################################
# MCDLIST CLASS FOR SHARED ITEMS BETWEEN ADMIN AND FRONTEND
######################################################################
class mcdlist {
	######################################################################
	# VARIABLES
	######################################################################
	var $version = '1.0.3';
	var $db_version = '1.0.2';
	var $pages = array('mcd-list','mcdl-new-list','mcdl-categories','mcdl-settings');
	var $plugin_url;
	var $options;
	######################################################################
	# CONSTRUCT
	######################################################################
	function __construct() {
		$this->options = get_option('mcd_list');
		$this->plugin_url = plugins_url('', __FILE__);
		$this->plugin_basename = dirname (__FILE__);
	}
}
######################################################################
# INITIATE ADMIN CLASS OR FRONTEND CLASS
######################################################################
if(is_admin()):
	include (dirname (__FILE__) . '/core/admin.php');
	new mcdlist_admin();
elseif($GLOBALS['pagenow'] != 'wp-login.php'):
	include (dirname (__FILE__) . '/core/frontend.php');
	new mcdlist_frontend();
endif;
######################################################################
# UNINSTALL PLUGIN
######################################################################
if (function_exists('register_uninstall_hook')):
	register_uninstall_hook(__FILE__, 'mcdl_uninstall_hook');
endif;
function mcdl_uninstall_hook() {
	// Remove Options
	delete_option('mcd_list');
	
	// Remove mySQL Tables
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS `mcd_cl_settings`, `mcd_cl_categories`, `mcd_cl_category_group`, `mcd_cl_coupons`, `mcd_cl_coupon_group`, `mcd_cl_lists`;");
	//echo '<p style="color: #f00;">uninstall is disabled</p>';
}
