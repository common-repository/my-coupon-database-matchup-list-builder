<?php
######################################################################
# INSTALL MYSQL TABLES
######################################################################
class mcdlist_install extends mcdlist {
	######################################################################
	# CONSTRUCT
	######################################################################
	function __construct() {
		parent::__construct (); // Grab Parent Class's Vars/Functions
		$this->set_options(); // Set Plugin Default Options
		$this->queries(); // Run Install Queries
	}
	######################################################################
	# PLUGIN DEFAULT OPTIONS
	######################################################################
	// Set Default Options
	function defaults() {
		$defaults = array(
			'version' => $this->version,
			'db_version' => $this->db_version,
			'access_level' => 'edit_published_posts',
			'api_key' => '',
			'logo_url' => '',
			'print_page_footer_code' => '',
			'text_expiration' => 'exp',
			'text_net_price' => 'Net Price',
			'text_coupon_stack' => 'STACKS',
			'disable_frontend_css' => 0
		);
		return $defaults;
	}
	######################################################################
	# SET OPTIONS
	######################################################################
	function set_options() {
		$this->options = $this->defaults();
		add_option('mcd_list', $this->options);
	}
	######################################################################
	# RUN QUERIES
	######################################################################
	function queries(){
		global $wpdb;
		
		// Table structure for table `mcd_cl_categories`
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `mcd_cl_categories` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NULL,
			  `cat_order` int(11) NOT NULL,
			  `description` text NULL,
			  `group_id` int(11) NOT NULL,
			  `date` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
		");
		
		// Dumping data for table `mcd_cl_categories`
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(1, 'Baby & Children', 1, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(2, 'Baking', 2, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(3, 'Beverages', 3, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(4, 'Bread & Bakery', 4, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(5, 'Breakfast', 5, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(6, 'Canned Goods', 6, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(7, 'Condiments & Dressings', 7, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(8, 'Dairy & Refrigerated', 8, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(10, 'Health & Medicine', 10, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(11, 'Household Cleaning', 11, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(12, 'Household Maintenance', 12, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(13, 'Laundry', 13, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(14, 'Meat', 14, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(15, 'Natural & Organic', 15, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(16, 'Office', 16, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(17, 'Other', 17, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(18, 'Pantry Necessities & Spices', 18, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(19, 'Paper & Plastic', 19, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(20, 'Pasta & Quick Meals', 20, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(21, 'Personal Hygiene & Beauty', 21, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(22, 'Pet', 22, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(23, 'Produce', 23, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(24, 'Restaurants', 24, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(25, 'Snacks', 26, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(26, 'Vegetarian', 28, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(27, 'Retail', 25, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(28, 'Store Only', 27, '', 1, '2010-11-11 17:53:33');");
		$wpdb->query("INSERT INTO `mcd_cl_categories` VALUES(9, 'Frozen Foods', 9, '', 1, '2010-11-30 23:32:36');");
		 
		 // Table structure for table `mcd_cl_category_group`
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `mcd_cl_category_group` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NULL,
			  `description` text NULL,
			  `origin` ENUM( 'native', 'imported' ) NULL,
			  `date` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
		");
		
		// Dumping data for table `mcd_cl_category_group`
		$wpdb->query("INSERT INTO `mcd_cl_category_group` VALUES(1, 'Default Categories', '', 'native', '2011-06-29 17:39:55');");
		
		// Table structure for table `mcd_cl_coupons`
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `mcd_cl_coupons` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` text NULL,
			  `store` text NULL,
			  `value` text NULL,
			  `expiration` datetime NULL,
			  `source` text NULL,
			  `created_by` enum('client','mcd') NOT NULL,
			  `coupon_group_id` int(11) NOT NULL,
			  `coupon_list_id` int(11) NOT NULL,
			  `mcd_id` int(11) NULL,
			  `coupon_url` text NULL,
			  `stacks` TINYINT NOT NULL,
			  `date` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
		");		
	
		// Table structure for table `mcd_cl_coupon_group`
		$wpdb->query("	
			CREATE TABLE IF NOT EXISTS `mcd_cl_coupon_group` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` text NULL,
			  `price` text NULL,
			  `cat_id` int(11) NOT NULL,
			  `more_info` text NULL,
			  `coupon_list_id` int(11) NOT NULL,
			  `net_price` text NULL,
			  `date` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		");
	
		// Table structure for table `mcd_cl_lists`
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `mcd_cl_lists` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` text NOT NULL,
			  `date` datetime NOT NULL,
			  `date_updated` datetime NOT NULL,
			  `shortcode` text NOT NULL,
			  `cat_group_id` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
		");
	} // end queries function
} // end mcdlist_install class