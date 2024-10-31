<?php
class mcdlist_upgrade extends mcdlist {
	function __construct() {
		parent::__construct(); // Grab Parent Class's Vars/Functions
		$this->check_upgrade(); // Check for Upgrades
	}

	function check_upgrade() {
		// Version Specific Upgrades
		if (version_compare($this->options['db_version'], '0.0.3', '<')) $this->upgrade('0.0.3');
		if (version_compare($this->options['db_version'], '0.0.3', '=')) $this->upgrade('0.0.4');
		if (version_compare($this->options['db_version'], '0.0.4', '>=') && version_compare($this->options['db_version'], '1.0.0', '<')) $this->upgrade('1.0.0');
		if (version_compare($this->options['db_version'], '1.0.0', '=')) $this->upgrade('1.0.1');
		if (version_compare($this->options['db_version'], '1.0.1', '=')) $this->upgrade('1.0.2');
		
		// Upgrade to Current if There is Not a Version Specific Upgrade
		if (version_compare($this->options['version'], $this->version, '<')) $this->upgrade('current');
	}

	function upgrade($ver) {
		global $wpdb;
		######################################################################
		# UPGRADE TO CURRENT VERSION
		######################################################################
		if($ver == 'current'):
			// Update Options
			$newopts = array('version' => $this->version);
			$this->options = array_merge($this->options, $newopts);
			update_option('mcd_list', $this->options);
		endif;
		######################################################################
		# SPECIFIC VERSION UPGRADE
		######################################################################
		switch($ver) {
			######################################################################
			# UPGRADE TO VERSION 0.0.3
			######################################################################
			case '0.0.3':
				// Update Lists Table (mcd_cl_lists)
				$wpdb->query("ALTER TABLE `mcd_cl_lists` ADD `cat_group_id` INT NOT NULL AFTER `shortcode`, ADD `date_updated` DATETIME NOT NULL AFTER `cat_group_id`");
				$wpdb->query("UPDATE `mcd_cl_lists` SET cat_group_id = 1, date_updated = NOW()");
				
				// Update Categories Table (mcd_cl_categories)
				$wpdb->query("ALTER TABLE `mcd_cl_categories` ADD `description` TEXT NOT NULL AFTER `cat_order`, ADD `group_id` INT NOT NULL AFTER `description`");
				$wpdb->query("ALTER TABLE `mcd_cl_categories` DROP `created_by`;");
				$wpdb->query("UPDATE mcd_cl_categories SET group_id = 1");
				
				// Create Category Group Table (mcd_cl_category_group)
				$wpdb->query("
					CREATE TABLE `savings_lifestyle_wp`.`mcd_cl_category_group` (
					`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`name` VARCHAR( 255 ) NOT NULL ,
					`description` TEXT NOT NULL ,
					`date` DATETIME NOT NULL
					) ENGINE = InnoDB;
				");
				
				// Add Entry to Category Group Table (mcd_cl_category_group)
				$wpdb->query("INSERT INTO `mcd_cl_category_group` VALUES(1, 'Default Categories', '', '2011-06-29 17:39:55');");
				
				// Drop Settings Table (mcd_cl_settings)
				$api_key = ''; $logo_url = ''; $print_page_footer_code = '';
				$settings = $wpdb->get_results("SELECT meta_key, meta_value FROM mcd_cl_settings");
				for($i = 0; $i < count($settings); $i++):
					if($settings[$i]->meta_key == 'token'): $api_key = $settings[$i]->meta_value; endif;
					if($settings[$i]->meta_key == 'logo_url'): $logo_url = $settings[$i]->meta_value; endif;
					if($settings[$i]->meta_key == 'print_page_footer'): $print_page_footer_code = $settings[$i]->meta_value; endif;
				endfor;
				$wpdb->query("DROP TABLE IF EXISTS `mcd_cl_settings`");
				
				// Update Options
				$newopts = array(
					'version' => '0.0.3',
					'db_version' => '0.0.3',
					'api_key' => $api_key,
					'logo_url' => $logo_url,
					'print_page_footer_code' => $print_page_footer_code
				);
				$this->options = array_merge($this->options, $newopts);
				update_option('mcd_list', $this->options);
			break;
			######################################################################
			# UPGRADE TO VERSION 0.0.4
			######################################################################
			case '0.0.4':
				// Add stacks column to mcd_cl_coupons
				$wpdb->query("ALTER TABLE `mcd_cl_coupons` ADD `stacks` TINYINT NOT NULL AFTER `coupon_url`");
			
				// Update Options
				$newopts = array(
					'version' => '0.0.4',
					'db_version' => '0.0.4',
					'text_expiration' => 'exp',
					'text_net_price' => 'Net Price',
					'text_coupon_stack' => 'STACKS',
					'disable_frontend_css' => 0
				);
				$this->options = array_merge($this->options, $newopts);
				update_option('mcd_list', $this->options);
			break;
			######################################################################
			# UPGRADE TO VERSION 1.0.0
			######################################################################
			case '1.0.0':
				// mcd_cl_categories NOT NULL to NULL on non required fields
				$wpdb->query("
				ALTER TABLE  `mcd_cl_categories` 
					CHANGE  `name`  `name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
					CHANGE  `description`  `description` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL
				");
				
				// mcd_cl_category_group NOT NULL to NULL on non required fields
				$wpdb->query("
				ALTER TABLE  `mcd_cl_category_group` 
					CHANGE  `name`  `name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
					CHANGE  `description`  `description` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL
				");
				
				// mcd_cl_coupons NOT NULL to NULL on non required fields
				$wpdb->query("
				ALTER TABLE  `mcd_cl_coupons` 
					CHANGE  `name`  `name` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
					CHANGE  `store`  `store` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
					CHANGE  `value`  `value` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
					CHANGE  `source`  `source` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
					CHANGE  `coupon_url`  `coupon_url` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL
				");
				
				// mcd_cl_coupon_group NOT NULL to NULL on non required fields
				$wpdb->query("
				ALTER TABLE  `mcd_cl_coupon_group` 
					CHANGE  `name`  `name` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
					CHANGE  `price`  `price` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
					CHANGE  `more_info`  `more_info` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,
					CHANGE  `net_price`  `net_price` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL
				");
	
				// Update Options
				$newopts = array(
					'version' => '1.0.0',
					'db_version' => '1.0.0'
				);
				$this->options = array_merge($this->options, $newopts);
				update_option('mcd_list', $this->options);
			break;
			######################################################################
			# UPGRADE TO VERSION 1.0.1
			######################################################################
			case '1.0.1':
				// Add orgin column to the mcd_cl_category_group table
				$wpdb->query("ALTER TABLE `mcd_cl_category_group` ADD `origin` ENUM( 'native', 'imported' ) NULL AFTER `description`");
				
				// Update all Category Groups
				$wpdb->query("UPDATE mcd_cl_category_group SET origin = 'native'");
	
				// Update Options
				$newopts = array(
					'version' => '1.0.1',
					'db_version' => '1.0.1'
				);
				$this->options = array_merge($this->options, $newopts);
				update_option('mcd_list', $this->options);
			break;
			######################################################################
			# UPGRADE TO VERSION 1.0.2
			######################################################################
			case '1.0.2':
				// Add orgin column to the mcd_cl_category_group table
				$wpdb->query("ALTER TABLE `mcd_cl_coupons` CHANGE `expiration` `expiration` DATETIME NULL, CHANGE `mcd_id` `mcd_id` INT(11) NULL");
	
				// Update Options
				$newopts = array(
					'version' => '1.0.3',
					'db_version' => '1.0.2'
				);
				$this->options = array_merge($this->options, $newopts);
				update_option('mcd_list', $this->options);
			break;
		######################################################################
		# END SPECIFIC VERSION UPGRADE
		######################################################################
		}
		######################################################################
		# CHECK FOR ANOTHER UPGRADE
		######################################################################
		$this->check_upgrade();
	}
}