<?php
class mcdl_print_list {
	######################################################################
	# VARS
	######################################################################
	var $options;
	var $token;
	var $atts;
	var $mcd_coupond_id_string;
	var $coupon_feed;
	var $user_feed;
	var $html_output;
	######################################################################
	# CONSTRUCT
	######################################################################
	function __construct() {
		$this->options = get_option('mcd_list'); 
		$this->token = $this->options['api_key'];
		$this->mcd_coupon_id_string(); // set a string of coupon id's to fetch from the MCD database
		$this->mcd_coupon_feed(); // mcd coupon feed
		$this->mcd_user_feed(); // mcd user feed - blog format options
		$this->html_output = $this->html_output(); // Output HTML
	}
	######################################################################
	# CREATE ID STRING OF MCD COUPONS USED ON LIST TO PASS TO XML
	######################################################################
	function mcd_coupon_id_string() {
		global $wpdb;
		session_start();
		//echo '<pre>'; var_dump($_SESSION); echo '</pre>';
		$placeholders = implode(',', array_fill(0, count($_SESSION['mcd_list_plugin']['header_ids']), '%d'));
		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT mcd_id 
			FROM mcd_cl_coupons c
			LEFT JOIN mcd_cl_coupon_group g ON c.coupon_group_id = g.id 
			WHERE g.id IN ($placeholders)", $_SESSION['mcd_list_plugin']['header_ids']
		));
		
		$mcd_id = ''; 
		foreach($results as $row): 
			$mcd_id .= '-' . $row->mcd_id; 
		endforeach;
		$this->mcd_coupond_id_string = substr($mcd_id, 1);
	}
	######################################################################
	# GRAB COUPON XML FEED WITH SPECIFIC COUPONS
	######################################################################
	function mcd_coupon_feed() {
		// If there are MCD Coupons Pull Coupons Feed
		if($this->mcd_coupond_id_string):
			libxml_use_internal_errors(true);
			$xml_file = 'http://www.mycoupondatabase.com/api/coupons-xml.php?token=' . $this->token . '&id_string=' . $this->mcd_coupond_id_string;
			// returns false
			if(libxml_get_errors()): 
			// returns false if xml didn't return anything, true if it does
			else:
				$this->coupon_feed = @simplexml_load_file($xml_file);
			endif;
		// Else, Don't Execute Feed
		else:
			return false;
		endif;
	}
	######################################################################
	# GRAB USER XML FEED
	######################################################################
	function mcd_user_feed() {
		// If there are MCD Coupons Pull User Feed
		if($this->mcd_coupond_id_string):
			$xml_file = 'http://www.mycoupondatabase.com/api/users-xml.php?token=' . $this->token . '&id_string=' . $this->mcd_coupond_id_string;
			// returns false
			if(libxml_get_errors()):
			// returns false if xml didn't return anything, true if it does
			else:
				$this->user_feed = @simplexml_load_file($xml_file);
			endif;
		// Else, Don't Execute Feed
		else:
			return false;
		endif;
	}
	######################################################################
	# BEGIN HTML OUTPUT METHODS
	######################################################################
	######################################################################
	# COUPON HEADER METHOD
	# CHILD METHOD(S) USED: coupon_li_output()
	######################################################################
	function coupon_header($row, $coupon_results){ ?>
		<div class="mcd_cl_headerContainer">
			<!-- Coupon Header Information -->
			<p class="mcd_cl_headerLine">
				<span class="mcd_cl_headerName"><?php echo stripslashes($row->name); ?></span>
				<?php if(!empty($row->price)): 
					echo ' - <span class="mcd_cl_headerValue"><strong>' . stripslashes($row->price) . '</strong></span>'; 
				endif; ?>
				<?php if(!empty($row->more_info)): 
					echo '<br /><span class="mcd_cl_moreInfoText">' . nl2br(stripslashes($row->more_info)) .'</span>'; 
				endif; ?>
			</p>
			
			<!-- Grab All Coupons for this Header -->
			<ul class="mcd_cl_couponsAddedList" id="<?php $row->id; ?>">
				<?php
				foreach ($coupon_results as $coupon_row):
					$this->coupon_li_output($coupon_row);
				endforeach;
				?>
			</ul>
			<?php if(!empty($row->net_price)): 
				echo '<p class="mcd_cl_couponNetPrice">' . $this->options['text_net_price'] . ' ' . $row->net_price . '</p>'; 
			endif; ?>
		</div> <!-- end mcd_cl_headerContainer -->
	<?php }
	######################################################################
	# RETURN COUPON LI
	# CHILD METHOD(S) USED: manual_coupon(), mcd_coupon()
	######################################################################
	function coupon_li_output($coupon_row) {
		if($coupon_row->created_by == "client"):
			$this->manual_coupon($coupon_row);
		elseif($coupon_row->created_by == "mcd"):
			$this->mcd_coupon($coupon_row);
		endif;		
	}
	######################################################################
	# IF COUPON WAS MANUALLY CREATED - EXECUTE THIS CODE
	######################################################################
	function manual_coupon($coupon_row) {
		$coupon_format = stripslashes($coupon_row->value) . ' <span class="mcd_cl_couponName">' . stripslashes($coupon_row->name) . '</span>';
		if($coupon_row->expiration != '0000-00-00 00:00:00'): 
			$coupon_format .= ' (' . $this->options['text_expiration'] . ' ' . date("m/d/y", strtotime($coupon_row->expiration)) . ')'; 
		endif;
		if(!empty($coupon_row->source)): 
			$coupon_format .= ' ' . stripslashes($coupon_row->source); 
		endif;
		?>
		<!-- COUPON HTML OUTPUT -->
		<li>
			<?php 
			if($coupon_row->stacks == 1): echo '<strong>' . $this->options['text_coupon_stack'] . '</strong> '; endif;
			if(!empty($coupon_row->coupon_url)): 
				echo '<a class="mcd_cl_printURL" href="' . stripslashes($coupon_row->coupon_url) . '" target="_new">' . $coupon_format . '</a>';
			else:
				echo $coupon_format;
			endif; ?>
		</li>
	<?php
	}
	######################################################################
	# IF COUPON WAS MCD CREATED - EXECUTE THIS CODE
	# CHILD METHOD(S) USED: blog_format()
	######################################################################
	function mcd_coupon($coupon_row) {
		// Set Coupon ID
		$cid = 'coupon' . $coupon_row->mcd_id;
		
		// Get Affiliate Link or Printable Link
		if(!empty($this->coupon_feed->$cid->affiliate)): 
			$coupon_link = stripslashes($this->coupon_feed->$cid->affiliate);
		elseif(!empty($this->coupon_feed->$cid->couponurl1)):
			$coupon_link = stripslashes($this->coupon_feed->$cid->couponurl1);
		else:
			$coupon_link = '';
		endif;

		// Set Blogformat Var
		$my_blogformat = $this->blog_format($cid);
		?>
		
		<!-- COUPON HTML OUTPUT -->
		<li>
			<?php
			if($coupon_row->stacks == 1): echo '<strong>' . $this->options['text_coupon_stack'] . '</strong> '; endif;
			if($coupon_link): 
				echo '<a class="mcd_cl_printURL" href="' . $coupon_link . '" target="_new">' . $my_blogformat . '</a>';
			else:
				echo $my_blogformat;
			endif;
			?>
		</li>
		
	<?php }
	######################################################################
	# BLOG FORMAT 
	######################################################################
	function blog_format($cid) {
		// Attempt to Grab Blogformat from MCD Database
		$storeid = 'storeid' . $this->coupon_feed->$cid->storeid;
		$blogformat = $this->user_feed->blog_format->$storeid;
		$my_blogformat = '';

		// If Blog Format Was Successfuly Grabbed, Set it
		if($blogformat):
			foreach($blogformat as $order):
				foreach($order as $key => $val):
					if(!empty($this->coupon_feed->$cid->$key)):
						if($key == 'expiration'):
							$my_blogformat .= '(' . $this->options['text_expiration'] . ' ' . date("m/d/y", strtotime($this->coupon_feed->$cid->$key)) . ') ';
						elseif($key == 'sourcedate'): 
							$my_blogformat .= date("m/d/y", strtotime($this->coupon_feed->$cid->$key)) . ' ';
						elseif($key == 'couponname'):
							$my_blogformat .= '<span class="mcd_cl_couponName">' . $this->coupon_feed->$cid->$key . '</span> '; 
						else: 
							$my_blogformat .= $this->coupon_feed->$cid->$key . ' '; 
						endif;
					endif;
				endforeach;
			endforeach;
		
		// Else, use a Default Blog Format
		else:	
			$my_blogformat = $this->coupon_feed->$cid->value . ' <span class="mcd_cl_couponName">' . $this->coupon_feed->$cid->couponname . '</span> ' . $this->coupon_feed->$cid->mainsource;
		endif;
		
		// Return Blogformat
		return $my_blogformat;
	}
	######################################################################
	# HTML OUTPUT - PULLS ALL THE METHODS TOGETHER
	# CHILD METHOD(S) USED: coupon_header()
	######################################################################
	function html_output() {
		ob_start();
		global $wpdb;
		session_start();
		$department = '';
		######################################################################
		# QUERY COUPON HEADERS FROM SESSION
		######################################################################
		$placeholders = implode(',', array_fill(0, count($_SESSION['mcd_list_plugin']['header_ids']), '%d'));
		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT h.id, h.name, h.coupon_list_id, h.price, h.net_price, h.more_info, cat.name as department, cat.description, l.name as list_name, l.id as list_id
			FROM mcd_cl_coupon_group h
			LEFT JOIN mcd_cl_categories cat ON h.cat_id = cat.id
			LEFT JOIN mcd_cl_lists l ON h.coupon_list_id = l.id
			WHERE h.id IN ($placeholders)
			ORDER BY h.coupon_list_id, cat.cat_order, cat.name ASC", $_SESSION['mcd_list_plugin']['header_ids']
		));
		?>
		<!-- Begin HTML Header -->
		<!-- <html>
		<head>
			<title>My Coupon List</title>
			<link rel='stylesheet' href='<?php echo plugins_url(); ?>/mcd-list/css/reset.css' type='text/css' media='all' />
			<link rel='stylesheet' href='<?php echo plugins_url(); ?>/mcd-list/css/styles-frontend.css' type='text/css' media='all' />
		</head>
		<body> -->
		
		<!-- Begin Print Container -->	
		<div id="mcd_cl_print_page_container" class="mcd_cl_container">

			<!-- Print Page Header -->
			<div id="mcd_cl_print_header">
				<?php if($this->options['logo_url']): ?>
					<div id="mcd_logo_container"><img src="<?php echo $this->options['logo_url']; ?>" alt="" /></div>
				<?php endif; ?>
				<h1><?php echo get_bloginfo("name"); ?></h1>
				<ul id="mcd_list_print_page_actions">
					<li><a href="#" id="mcd_cl_print_list_button">Print My Coupons</a></li>
				</ul>
			</div>
			
			<!-- Print Page Body -->
			<div id="mcd_cl_print_container">
				<?php
				foreach ($results as $row):
					######################################################################
					# SHOW TITLE OF LIST IF NEEDED
					######################################################################
					if(!isset($list_name) || $list_name != $row->list_name):
						echo '<h2>' . $row->list_name . '</h2>';
						$list_name = $row->list_name;
					endif;
					######################################################################
					# QUERY COUPONS FOR THIS HEADER
					######################################################################
					$coupon_results = $wpdb->get_results(
						"SELECT id, store, name, value, expiration, source, created_by, mcd_id, coupon_url, stacks 
						FROM mcd_cl_coupons 
						WHERE coupon_group_id = $row->id
						ORDER BY id DESC"
					);
					######################################################################
					# DISPLAY CATEGORY
					######################################################################
					if($department == $row->department):
					else:
						// Need this to Determine when to Load New Category Header
						$department = $row->department; 
						
						// Display Category name
						echo '<h3 class="mcd_cl_CategoryName">' . stripslashes($row->department) . '</h3>';
						
						// Display Category Description if Exists
						if($row->description): echo '<p class="mcd_cl_CategoryDescription">' . $row->description . '</p>'; endif;
					endif;
					######################################################################
					# DISPLAY COUPON HEADER
					######################################################################
					
					$this->coupon_header($row, $coupon_results);
				endforeach;
				?>
			</div><!-- end #mcd_cl_print_container -->
			
			<!-- Print Page Footer -->
			<div id="mcd_cl_print_footer">
				<?php if($this->options['print_page_footer_code']): 
					echo stripslashes($this->options['print_page_footer_code']); 
				endif; ?>
			</div>

			
		</div><!-- end #mcd_cl_container -->
		
		<!-- Begin HTML Footer -->
		<!-- </body>
		</html> -->
		<?php
		return ob_get_clean();
	} // html_output() method
}