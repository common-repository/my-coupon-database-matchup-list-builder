jQuery(document).ready(function($) {
	/*######################################################################
	# UNIVERSAL
	######################################################################*/
     
	/*######################################################################
	# LIST PAGE
	######################################################################*/
	// Hide Submit button until the document has finished loading
	$(".mcd_cl_submit_button_container").each(function(){
		if($(this).is(':visible')) {
			$(this).empty().html('<input class="mcd_cl_submitButtton" type="submit" name="submit" value="Add to My Coupon List" />');
		}
	});
	
	// Select All Coupons
	$(".mcd_cl_select_all").click(function(){
		$(this).parents('form').find("input[type='checkbox']").attr('checked','checked');
		//$(this).parents('form').find("input[type='checkbox']").prop('checked',true);
		return false;
	});
	
	// Unselect All Coupons
	$(".mcd_cl_unselect_all").click(function(){
		$(this).parents('form').find("input[type='checkbox']").removeAttr('checked');
		return false;
	});
	/*######################################################################
	# FRONTEND PANEL
	######################################################################*/
	// Check to See if Session Exist, and if So Load Panel
	$.ajax({
		url: mcd_list_frontend.ajaxurl,
		type: 'POST', 
		dataType: 'json', 
		data: { 'action': 'mcdl_frontend', 'method': 'get_panel' },
		success: function(data) {
			// Add Panel to Dom
			$('body').append(data.panel);
			
			// Add New Coupon Headers to Panel
			$('.mcd_list_added_coupons').append(data.line_items);
			
			// Update Coupon count in Panel
			$('.mcd_panel_count').text(data.header_count);
			
			// Show Scroll Buttons if Enough Items
			if(data.header_count > 12) { $('#panel_scroll_buttons').show();	} else { $('#panel_scroll_buttons').hide(); }
		}
	});
	
	// Submit List Functionality
	$(".mcd_cl_custom_list").submit(function(){
		myPostData = $(this).serializeArray();
		myPostData.push({ name: "action", value: "mcdl_frontend" });
		myPostData.push({ name: "method", value: "add_coupons_to_panel" });
		$.ajax({
			url: mcd_list_frontend.ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: myPostData,
			success: function(data) {
				var lineItems = data.line_items;
				// First See if Panel Exists on DOM
				if(!$('#mcd_list_container').length) { 
					$.ajax({
						url: mcd_list_frontend.ajaxurl,
						type: 'POST', 
						dataType: 'json', 
						data: { 'action': 'mcdl_frontend', 'method': 'get_panel' },
						success: function(data) {
							// Add Panel to Dom
							$('body').append(data.panel);
							
							// Add New Coupon Headers to Panel
							$('.mcd_list_added_coupons').append(data.line_items);
							
							// Update Coupon count in Panel
							$('.mcd_panel_count').text(data.header_count);
							
							// Show Scroll Buttons if Enough Items
							if(data.header_count > 12) { $('#panel_scroll_buttons').show();	} else { $('#panel_scroll_buttons').hide(); }
						}
					});
				} else {
					//if(data.message != 'No new headers were added.'){
						// Add New Coupon Headers to Panel
						$('.mcd_list_added_coupons').append(data.line_items);
						
						// Update Coupon count in Panel
						$('.mcd_panel_count').text(data.header_count);
						
						// Show Scroll Buttons if Enough Items
						if(data.header_count > 12) { $('#panel_scroll_buttons').show();	} else { $('#panel_scroll_buttons').hide(); }
					//}
				}
			}
		});
		return false;
	});
	
	// Remove Coupon From List
	$('.mcd_list_remove_coupon').live("click", function(){
		var thisLI = $(this).parents('li');
		var thisID = thisLI.attr('id');
		$.ajax({
			url: mcd_list_frontend.ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: { 'action': 'mcdl_frontend', 'method': 'remove_header', 'header_id': thisID },
			success: function(data) {
				// Remove Coupon Header From Panel
				thisLI.remove();
				
				// Update Coupon count in Panel
				$('.mcd_panel_count').text(data.header_count);
				
				// Show Scroll Buttons if Enough Items
				if(data.header_count > 12) { $('#panel_scroll_buttons').show();	} else { $('#panel_scroll_buttons').hide(); }
			}
		});
		return false;
	})
	
	// Print Coupons Link
	$('#mcd_list_print_coupons a').live('click', function() {
		// Variables
		$this = $(this);
		
		// Loading Image
		//$('#mcd_list_print_coupons a').css({ 'background': 'none' })
		$('#mcd_list_print_coupons a').addClass('mcd_cl_print_page_loading').text('Loading...');
	
		// Grab Printable List and Show in Lightbox
		$.ajax({
			url: mcd_list_frontend.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: { 'action': 'mcdl_frontend', 'method': 'print_popup' },
			success: function(data) {
				// Append Data;
				$('#mcd-print-list-data').html(data.html);
				
				// Fancybox Lightbox
				$.fancybox({
					href: $this.attr('href'),
					type: 'inline'
					//height: '100%',
					//width: '100%'
				});
				
				// Remove Loading Icon From Print Button
				$('#mcd_list_print_coupons a').removeClass('mcd_cl_print_page_loading').text('');
				
			}
		});
		return false;
	});
	
	// Scroll Up Functionality
    $('#mcd_panel_scroll_up').live('mousedown', function(){
    	$('.mcd_list_added_coupons').stop();
        var height = $('.mcd_list_added_coupons').height();
        var offset = $('.mcd_list_added_coupons').scrollTop();
        $('.mcd_list_added_coupons').animate({scrollTop: offset-=10000 }, 18000);
    });
    $('#mcd_panel_scroll_up').live('mouseup', function(){
        $('.mcd_list_added_coupons').stop();
    });
    
    // Scroll Down Functionality
    $('#mcd_panel_scroll_down').live('mousedown', function(){
    	$('.mcd_list_added_coupons').stop();
        var height = $('.mcd_list_added_coupons').height();
        var offset = $('.mcd_list_added_coupons').scrollTop();
        $('.mcd_list_added_coupons').animate({scrollTop: offset+=10000 }, 18000);
    });
    $('#mcd_panel_scroll_down').live('mouseup', function(){
        $('.mcd_list_added_coupons').stop();
    });
    
    // Panel Expand/Collapse
	$("#mcd_list_tab").live('click', function(){
		var ele = $(this).parents('#mcd_list_container');
		
		// Show Panel
		if (ele.hasClass('mcd_list_container_hide')) {
			$('#mcd_list_tab').addClass('mcd_list_tab_show').removeClass('mcd_list_tab_hide');
			ele.addClass('mcd_list_container_show').removeClass('mcd_list_container_hide');
			$.ajax({
				url: mcd_list_frontend.ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'action': 'mcdl_frontend', 'method': 'panel_orientation', 'class': 'mcd_list_container_show' },
				success: function(data) {
					// do nothing
				}
			});
			
		// Collapse Panel
		} else {
			$('#mcd_list_tab').addClass('mcd_list_tab_hide').removeClass('mcd_list_tab_show');
			ele.addClass('mcd_list_container_hide').removeClass('mcd_list_container_show');
			$.ajax({
				url: mcd_list_frontend.ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'action': 'mcdl_frontend', 'method': 'panel_orientation', 'class': 'mcd_list_container_hide' },
				success: function(data) {
					// do nothing
				}
			});
		}
		return false;
	})
	
	// Clear List
	$('#mcd_list_clear_list a').live("click", function(){
		if (confirm('Are you sure you want to clear your list?')) {
			$.ajax({
				url: mcd_list_frontend.ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'action': 'mcdl_frontend', 'method': 'clear_list' },
				success: function(data) {
					// Remove All Coupon Headers from the List
					$('.mcd_list_added_coupons li').remove();
					
					// Set Panel Count to 0
					$('.mcd_panel_count').text(0);
				}
			});
		}
		return false;
	})
	
	// Minimize Panel
	$('#mcd_list_minimize_panel').live("click", function(){
		$('#mcd_list_container').addClass('mcd_list_container_hide').removeClass('mcd_list_container_show');
		$.ajax({
			url: mcd_list_frontend.ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: { 'action': 'mcdl_frontend', 'method': 'panel_orientation', 'class': 'mcd_list_container_hide' },
			success: function(data) {
				// do nothing
			}
		});
		return false;
	});
	/*######################################################################
	# PRINT PAGE
	######################################################################*/
	// Print List Button
	$("#mcd_cl_print_list_button").live("click", function(){
		var printThis = $("#mcd-print-list-data").html();
		$("body").children().addClass('mcd_cl_hide_all');
		$("body").append('<div id="mcd-print-temp">' + printThis + '</div>');
		window.print();
		$("body").children().removeClass('mcd_cl_hide_all');
		$("#mcd-print-temp").remove();
		return false;
	});
	
	// Add Coupons Form
	$("#mcd_cl_user_added_coupons_form").live("submit", function(){
		var inputVal = $(this).find("input[name='add_coupon']").val();
		$(this).find("input[name='add_coupon']").val('');
		$("#mcd_cl_user_added_coupons_list").append('<li>' + inputVal + ' <a class="mcd_cl_remove_added_item" href="#"><img class="mcd_cl_img_align" src="' + blogURL + '/wp-content/plugins/mcd-list/images/remove.png"></a></li>');
		$("#mcd_cl_user_added_coupons_list").show();
		return false;
	});
	
	// Remove Coupon
	$(".mcd_cl_remove_added_item").live("click", function(){
		$(this).parent().remove();
		return false;
	});
	
});