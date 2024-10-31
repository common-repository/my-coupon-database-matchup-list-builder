<?php
######################################################################
# FRONTEND CLASS
######################################################################
class mcdlist_frontend extends mcdlist {
	######################################################################
	# CONSTRUCT
	######################################################################
	function __construct () {
		parent::__construct (); // Grab Parent Class's Vars/Functions
		$this->frontend_enqueue(); // Enqueue Frontend Scripts
		$this->includes(); // Include Proper Files
		$this->shortcode(); // Frontend Shortcode
	}
	######################################################################
	# FRONTEND CSS AND JS ENQUEUES
	######################################################################
	function frontend_enqueue(){
			
		// Enqueue Frontend CSS
		if($this->options['disable_frontend_css'] == 0):
			wp_enqueue_style('mcdl-frontend-css', $this->plugin_url . '/css/styles-frontend.css');
		endif;
		wp_enqueue_style('mcdl-fancybox-css', $this->plugin_url . '/core/frontend/js/fancybox/jquery.fancybox-1.3.4.css');
		
		// Enqueue Plugin Frontend Scripts
		//DUPLICATE wp_enqueue_script('mcdl-frontend-js', $this->plugin_url . '/core/frontend/js/coupon-list.js', array('jquery'));
		wp_enqueue_script('mcdl-fancybox', $this->plugin_url . '/core/frontend/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'));
		
		// Make a Global Frontend AJAX URL
		wp_enqueue_script('mcd_list_frontend-ajax', $this->plugin_url . '/core/frontend/js/coupon-list.js', array('jquery'));
		wp_localize_script('mcd_list_frontend-ajax', 'mcd_list_frontend', array('ajaxurl' => admin_url( 'admin-ajax.php')));
	}
	function includes(){
		// Include Frontend Class
		include $this->plugin_basename . '/core/frontend/classes/load-list-class.php';
	}
	######################################################################
	# GENERATE FRONTEND SHORTCODES
	######################################################################
	function shortcode_cb($atts) {
		$shortcode_obj = new mcdl_load_list($atts);
		$shortcode = $shortcode_obj->html_output;
		return $shortcode;
	}
	function shortcode() {
		add_shortcode('coupon-list', array($this, 'shortcode_cb'));
	}
}