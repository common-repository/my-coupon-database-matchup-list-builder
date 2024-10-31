<?php $options = get_option('mcd_list'); ?>

<div class="wrap">
	<div class="icon32" id="icon-edit-pages"><br /></div><h2>Coupon List - Settings</h2><br />
	<div class="updated" id="message"></div>
	
	<form method="post" action="#" id="settings_form" class="settings_form">
		<!-- General Settings -->
		<div id="poststuff">
			<div class="stuffbox">
				<h3>General Settings</h3>
				<div class="inside">
					<!-- API Key -->
					<label>API Token</label>
					<input type="text" value="<?php if($options['api_key']): echo $options['api_key']; endif; ?>" size="50" name="settings[api_key]" maxlength="36" /><!--<div style="display: inline;"><span style="color: green; font-weight: bold;">Valid Token!</span><span style="color: red; font-weight: bold;">Invalid Token!</span></div> -->
					<p>Please enter your API Key given to you from your My Coupon Database account. (<a href="http://www.mycoupondatabase.com" target="_blank">Website</a>)</p>
					
					<!-- Logo URL -->
					<label>Logo URL</label>
					<input type="text" value="<?php if($options['logo_url']): echo $options['logo_url']; endif; ?>" size="80" name="settings[logo_url]" maxlength="100" />
					<p>Upload your logo through "Media", then simply copy and paste the url of the uploaded file into this field. This will be used for the print list page that your viewers can see.</p>
				
					<!-- Print Page Footer Code -->
					<label>Print Page Footer Code</label>
					<textarea name="settings[print_page_footer_code]" cols="70" rows="8"><?php if($options['print_page_footer_code']): echo $options['print_page_footer_code']; endif; ?></textarea>
					<p>This text or HTML will display in the footer of the print list page for the viewer.</p>
				</div>
			</div>
		</div>
		<input type="submit" value="Save Settings" class="button" name="submit">
		<br /><br />

		<!-- Preferences -->
		<div id="poststuff">
			<div class="stuffbox">
				<h3>Preferences</h3>
				<div class="inside">
				
					<!-- Expiration Text -->
					<label>Expiration Text</label>
					<input type="text" value="<?php if($options['text_expiration']): echo $options['text_expiration']; endif; ?>" size="50" name="settings[text_expiration]" />
					<p>What text would you like to use for when a coupon contains an expiration? The default text is "exp". Other common text options are "expiration" and "expires".</p>
					
					<!-- Net Price Text -->
					<label>Item Price Text</label>
					<input type="text" value="<?php if($options['text_net_price']): echo $options['text_net_price']; endif; ?>" size="50" name="settings[text_net_price]" />
					<p>What text would you like to use for for the Item Price? The default text is "Net Price". Other common options may be "Final Price" and "Total After Savings".</p>
					
					<!-- Coupon Stacking Text -->
					<label>Coupon Stacking Text</label>
					<input type="text" value="<?php if($options['text_coupon_stack']): echo $options['text_coupon_stack']; endif; ?>" size="50" name="settings[text_coupon_stack]" />
					<p>What text would you like to use for when a coupon stacks with another coupon? The default text is "STACKS". This will only show up when you click on the word to the left of the coupon.</p>
				
				</div>
			</div>
		</div>
		<input type="submit" value="Save Settings" class="button" name="submit">
		<br /><br />
		

		<!-- Style Settings -->
		<div id="poststuff">
			<div class="stuffbox">
				<h3>Frontend CSS Styles</h3>
				<div class="inside">
					<h4>Disabling The Built In Plugin Styles</h4>
					Distable Plugin Stylesheet? 
					<select name="settings[disable_frontend_css]">
						<?php $selected = ' selected="selected"'; ?>
						<option value="0"<?php if($options['disable_frontend_css'] == 0): echo $selected; endif; ?>>No</option>
						<option value="1"<?php if($options['disable_frontend_css'] == 1): echo $selected; endif; ?>>Yes</option>
					</select>
				
					<h4>Further Customize The Plugin Styles</h4>
					<p>The CSS shown below is already pre-loaded with this plugin. This plugin is highly customizable as almost all of the html elements contain a class so that you may use them as a CSS selector for further customization.</p>
					
					<h4>Tips For Making Your Own Styling</h4>
					<ul class="mcd_cl_admin_list">
						<li>If you need to modify the default plugin CSS, please copy and paste the styles below into your <a href="theme-editor.php" target="_blank">themes style file</a> and modify them accordingly.</li>
						<li>Important* Make sure you disabled the plugin stylesheet from the option above and save the setting.</li>
						<li>You may need to add higher specificity to the existing plugin selectors to make them compatible with your theme.</li>
						<li>These styles may change from one plugin version to the next. Be sure to check back with these styles against the styles you have set in your theme to see if there have been any major changes.</li>
					</ul>
					
					<h4>Default Plugin CSS</h4>
<pre class="brush: css; toolbar: false;">
/********************************************
START MCD LIST PLUGIN STYLES
********************************************/
/**********************
Full List Page Styling
**********************/
.mcd_cl_img_align { vertical-align: middle; }

/* Coupon Container */
.mcd_cl_container { font-size: 12px !important; padding: 0 0 20px 0 !important; }

/* Header Container */
.mcd_cl_headerContainer { margin: 0 0 10px 0 !important; }

/* Category Styling */
.mcd_cl_CategoryName { font-size: 16px !important; padding: 0 0 5px 0 !important; border-bottom: 1px solid #666 !important; margin: 0 0 10px 0 !important; }

/* Coupon Header Styling */
.mcd_cl_container input[type='checkbox'] { width: auto !important; }
.mcd_cl_headerLine { margin: 0 0 0 0 !important; padding: 0 !important; }
.mcd_cl_headerName { font-weight: bold !important; }
.mcd_cl_headerValue { padding: 0 0 0 0 !important; }
.mcd_cl_moreInfoText { }

/* Coupon List Styling */
ul.mcd_cl_couponsAddedList { margin: 0 0 0 0 !important; padding: 0 !important; }
ul.mcd_cl_couponsAddedList li { line-height: 20px !important; list-style: none !important; margin: 0 !important; }

/* Coupoin Styling */
.mcd_cl_couponStore { font-style: italic !important; }
.mcd_cl_couponName { }
.mcd_cl_couponValue { color: blue !important; }
.mcd_cl_couponExpiration { color: red !important; }
.mcd_cl_couponSource { color: #666 !important; }
.mcd_cl_couponNetPrice { color: blue !important; padding: 0 0 10px 0 !important; }
.mcd_cl_printURL { }
.mcd_cl_printURL:hover { }

/* Submit Button */
.mcd_cl_submitButtton { }

/* Select All Coupons */
.mcd_cl_select_all_coupons_container { margin: 0 !important; list-style: none !important; overflow: hidden !important; padding: 0 0 10px 0 !important; }
.mcd_cl_select_all_coupons_container li { 
	float: left !important; padding: 0 0 0 10px !important; margin: 0 0 0 10px !important; border-left: 1px solid #C0C0C0 !important; 
	list-style: none !important;
}
.mcd_cl_select_all_coupons_container li:first-child { padding: 0 !important; margin: 0 !important; border: none !important; }
.mcd_cl_select_all { }
.mcd_cl_unselect_all { }
/**********************
MCD Coupon List Panel
**********************/
#mcd_list_container {
position: fixed; z-index: 6000; top: 50px; width: 500px; height: 500px; overflow: hidden; 
/* -466px to hide it */
}
.mcd_list_container_show { right: 0; }
.mcd_list_container_hide { right: -466px; }
#mcd_list_tab { 
float: left; display: block; width: 36px; height: 180px; position: relative; z-index: 10; 
}
.mcd_list_tab_show { background: url('../images/list-tab-expanded.png') no-repeat; }
.mcd_list_tab_hide { background: url('../images/list-tab-collapsed.png') no-repeat; }
#mcd_list_coupons {
float: left; background: #555; border: 2px solid #000; width: 442px; height: 476px; border-right: none; margin: 0 0 0 -2px; color: #fff;
padding: 10px; font-size: 12px;
}
#mcd_list_panel_nav { overflow: hidden; }
#mcd_list_functions_left, #mcd_list_functions_right { overflow: hidden; list-style: none; margin: 0; font-size: 14px; padding: 0 0 20px 0; }
#mcd_list_functions_left { float: left; }
#mcd_list_functions_right { float: right; }
#mcd_list_functions_left li, #mcd_list_functions_right li { float: left; padding: 0 20px 0 0; }
#mcd_list_functions_left li a, #mcd_list_functions_right li a  { display: block; width: 32px; height: 32px; }
#mcd_list_print_coupons a { background: url('../images/print_icon.png') no-repeat; }
#mcd_list_clear_list a { background: url('../images/clear_list_icon.png') no-repeat; }
#mcd_list_minimize_panel a { background: url('../images/minimize_icon.png') no-repeat; }
#mcd_list_my_selected_coupons { font-size: 14px; padding: 0 0 5px 0; }
.mcd_list_added_coupons { list-style: none; overflow: hidden; height: 365px; margin: 0 0 5px 0; }
.mcd_list_added_coupons li { border-bottom: 1px solid #777; padding: 0 0 5px 0; margin: 0 0 5px 0; }
.mcd_list_added_coupons li a.mcd_list_remove_coupon { color: #fd2525; }
#panel_scroll_buttons { display: none; list-style: none; margin: 0; padding: 0; overflow: hidden; }
#panel_scroll_buttons li { float: left; padding: 0 5px 0 0; }
#mcd_panel_scroll_up, #mcd_panel_scroll_down { 
border: 1px solid #333; background: #eee;
-moz-border-radius: 20px; -webkit-border-radius: 20px; -khtml-border-radius: 20px; border-radius: 20px;
}
/**********************
Print Page Styling
**********************/
#mcd_cl_print_header { padding: 30px; background: #EEE; border-bottom: 2px dashed #DDD; }
#mcd_cl_print_header h1 { font-size: 30px; padding: 0 0 5px 0; }
#mcd_logo_container { padding: 0 0 20px 0; text-align: center; }
#mcd_list_print_page_actions { list-style: none; margin: 0; font-size: 14px; padding: 0 0 20px 0; }
#mcd_list_print_page_actions li:first-child { padding: 0 0 0 50px; background: url('../images/print_icon.png') no-repeat; line-height: 32px; }
#mcd_cl_print_list_button { color: #000; cursor: pointer; }

#mcd_cl_print_container { padding: 30px; }
#mcd_cl_print_container h2 { font-size: 24px; color: #33C6F4; padding: 20px 0 5px 0; margin: 0 0 10px 0; border-bottom: 1px solid #33C6F4; }

#mcd_cl_user_added_coupons_list { display: none; margin: 10px 0 0 0; padding: 5px; border: 1px dashed #DDD; }
#mcd_cl_user_added_coupons_list li { list-style: none; line-height: 20px; }

#mcd_cl_print_footer { background: #EEE; border-top: 2px dashed #DDD; min-height: 200px; padding: 30px;  }
/********************************************
END MCD LIST PLUGIN STYLES
********************************************/
</pre>		
				</div>
			</div>
			<input type="submit" value="Save Settings" class="button" name="submit">
		</div>
	</form>
	
</div>