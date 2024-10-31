<?php
######################################################################
# ADMIN CLASS
######################################################################
class mcdlist_admin extends mcdlist {
	######################################################################
	# VARIABLES
	######################################################################

	######################################################################
	# CONSTRUCT
	######################################################################
	function __construct() {
		parent::__construct (); // Grab Parent Class's Vars/Functions
		add_action('init', array($this, 'init')); // Initialize Plugin
		add_action('admin_menu', array($this,'admin_menu')); // Admin Menu
		$this->admin_includes(); // Admin File Includes
		$this->admin_ajax(); // Admin AJAX Calls
		$this->frontend_ajax(); // Frontend AJAX Calls
	}
	######################################################################
	# INIT PLUGIN
	######################################################################
	function init() {
		// Check for an Upgrade
		if($this->options):
			if(version_compare($this->options['version'], $this->version, '<')):
				$this->upgrade_check();
			endif;
		// Run Install
		else:
			include $this->plugin_basename . '/install.php';
			new mcdlist_install();
		endif;
	}
	######################################################################
	# UPGRADE PLUGIN
	######################################################################
	function upgrade_check() {
		include($this->plugin_basename . '/upgrade.php');
		new mcdlist_upgrade();
	}
	######################################################################
	# ADMIN INCLUDES
	######################################################################
	function admin_includes(){ 
		// General Include
		if(isset($_GET['page']) && in_array($_GET['page'], $this->pages)):
			include($this->plugin_basename . '/core/admin/inc/functions.php');
		endif;
		
		// Export Function
		if(($_GET['page'] && $_GET['page'] == 'mcd-list') && isset($_POST['export-list'])):
			$atts['id'] = $_POST['id'];
			$atts['name'] = $_POST['name'];
			$atts['cat_group_name'] = $_POST['cat_group_name'];
			//$atts['cat_group_description'] = $_POST['cat_group_description'];
			include($this->plugin_basename . '/core/admin/inc/mcd_export.class.php');
			new mcd_export($atts);
		endif;
	}
	######################################################################
	# ADMIN CSS AND JS STYLE/SCRIPT ENQUEUE - ADDED FROM MENU
	######################################################################
	// CSS Function to Enqueue CSS Files for Plugin Admin pages Only
	function admin_css() {
	    wp_enqueue_style('mcdl-admin', $this->plugin_url .'/css/styles-admin.css');
	    wp_enqueue_style('syntax-highlighter-core', $this->plugin_url .'/core/admin/js/shCore.css');
	    wp_enqueue_style('syntax-highlighter-theme-default', $this->plugin_url .'/core/admin/js/shThemeDefault.css');
	}
	// Javascript Function to Enqueue JS Files for Plugin Admin pages Only
	function admin_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable', '', array('jquery'));
		wp_enqueue_script('syntax-highlighter-js', $this->plugin_url . '/core/admin/js/shCore.js');
		wp_enqueue_script('syntax-highlighter-brush-css-js', $this->plugin_url . '/core/admin/js/shBrushCss.js');
		wp_enqueue_script('mcdl-admin-js', $this->plugin_url . '/core/admin/js/mcdl-admin.js', array('jquery'));
	}
	######################################################################
	# CREATE ADMIN MENU - ALSO LOADS ADMIN CSS/JS FUNCTIONS FROM ABOVE
	######################################################################
	function admin_menu() {
		// Set Admin Access Level
		if(!$this->options['access_level']): 
			$access = 'edit_published_posts';
		else: 
			$access = $this->options['access_level'];
		endif;
		// Create Menu
		$menu_item[] = add_menu_page('Current Lists', 'Current Lists', $access, 'mcd-list', array($this, 'list_page'));
		$menu_item[] = add_submenu_page('mcd-list', __('Create a New List'), __('Create a New List'), $access, 'mcdl-new-list', array($this, 'new_list_page'));
		$menu_item[] = add_submenu_page('mcd-list', __('Categories'), __('Categories'), $access, 'mcdl-categories', array($this, 'categories_page'));
		$menu_item[] = add_submenu_page('mcd-list', __('Import List'), __('Import List'), $access, 'mcdl-import', array($this, 'import_page'));
		$menu_item[] = add_submenu_page('mcd-list', __('Settings'), __('Settings'), $access, 'mcdl-settings', array($this, 'settings_page'));
		// Loop through Menu Items and Enqueue CSS/Scripts
		for($i = 0; $i < count($menu_item); $i++):
			add_action('admin_print_styles-' . $menu_item[$i], array($this,'admin_css'));
			add_action('admin_print_scripts-' . $menu_item[$i], array($this,'admin_scripts'));
		endfor;
	}
	// Set What Page to Load for Menu Callback Function
	function list_page() { include($this->plugin_basename . '/core/admin/manage-list.php'); }
	function new_list_page() { include($this->plugin_basename . '/core/admin/new-list.php'); }
	function categories_page() { include($this->plugin_basename . '/core/admin/categories.php'); }
	function import_page() { include($this->plugin_basename . '/core/admin/import.php'); }
	function settings_page() { include($this->plugin_basename . '/core/admin/settings.php'); }
	######################################################################
	# AJAX REQUESTS FOR ADMIN
	######################################################################
	function mcdl_list_functions_ajax_callback() { include($this->plugin_basename . '/core/admin/ajax/list-functions.php'); die(); }
	function mcdl_search_ajax_callback() { include($this->plugin_basename . '/core/admin/ajax/search-mcd.php'); die(); }
	function mcdl_categories_ajax_callback() { include($this->plugin_basename . '/core/admin/ajax/categories.php'); die(); }
	function mcdl_settings_ajax_callback() { include($this->plugin_basename . '/core/admin/ajax/settings.php'); die(); }
	function admin_ajax() {
		add_action('wp_ajax_mcdl_list_functions', array($this,'mcdl_list_functions_ajax_callback'));
		add_action('wp_ajax_mcdl_search', array($this,'mcdl_search_ajax_callback'));
		add_action('wp_ajax_mcdl_categories', array($this,'mcdl_categories_ajax_callback'));
		add_action('wp_ajax_mcdl_settings', array($this,'mcdl_settings_ajax_callback'));
	}
	######################################################################
	# AJAX REQUESTS FOR FRONTEND
	######################################################################
	function mcdl_print_page_ajax_callback() { include($this->plugin_basename . '/core/frontend/ajax/print-page.php'); die(); }
	function mcdl_frontend_ajax_callback() { include($this->plugin_basename . '/core/frontend/ajax/frontend-ajax.php'); die(); }
	function frontend_ajax() {
		add_action('wp_ajax_mcdl_print_page', array($this, 'mcdl_print_page_ajax_callback'));
		add_action('wp_ajax_nopriv_mcdl_print_page', array($this, 'mcdl_print_page_ajax_callback'));
		add_action('wp_ajax_mcdl_frontend', array($this, 'mcdl_frontend_ajax_callback'));
		add_action('wp_ajax_nopriv_mcdl_frontend', array($this, 'mcdl_frontend_ajax_callback'));
	}
}