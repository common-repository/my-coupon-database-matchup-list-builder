SyntaxHighlighter.all();
jQuery(function($) {
/*######################################################################
# UNIVERSAL FUNCTIONS
######################################################################*/
	// Box Heading Collapse
	$(".stuffbox h3, .stuffbox .handlediv").live("click", function(){
		$(this).parent().find(".inside").toggle();
	});
	
	// Current TimeStamp Function
	function theCurrentTime() {
	    var currentTime = new Date(); 
	    var month = currentTime.getMonth() + 1; 
	    var day = currentTime.getDate(); 
	    var year = currentTime.getFullYear(); 
	    var hour = currentTime.getHours(); 
	    var minute = currentTime.getMinutes();
	    var second = currentTime.getSeconds();
	    var meridiem = "am";
		if (hour > 11) { meridiem = "pm"; }
		if (hour > 12) { hour = hour - 12; }
		if (hour == 0) { hour = 12; }
		//if (hour < 10) { hour   = "0" + hour; }
		if (minute < 10) { minute = "0" + minute; }
		if (second < 10) { second = "0" + second; }
	    var timeString = month + "/" + day + "/" + year + " @ " + hour + ":" + minute + meridiem;
	    return timeString;
	};
/*######################################################################
# CREATE A LIST
######################################################################*/
	// Create a Coupon List Form
	$("#mcd_cl_create_list").submit(function() {
		$("#message ul li").remove();
		var validationErrors = [];
		var couponListName = $("input[name='coupon-list-name']").val();
		var couponListGroupID = $("select#group_id").val();
		if (!couponListName.length) { validationErrors.push('The Name Field Cannot be Left Blank'); }
		if (!couponListGroupID.length) { validationErrors.push('The Category Group Field Cannot be Left Blank'); }
		if (validationErrors.length) {
			$.each(validationErrors, function(i, elem) { $("#message ul").append('<li>' + elem + '</li>'); });
			$("#message").fadeIn();
			return false;
		} else { 
			$("#message").hide();
			$.ajax({
				url: ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'action' : 'mcdl_list_functions', 'method' : 'create_list', 'name' : couponListName, 'group_id' : couponListGroupID,  },
				success: function(data) {
					window.location = 'admin.php?page=mcd-list&id=' + (data.list_id);
				}
			}); // end AJAX request
			return false;
		}
	});
/*######################################################################
# MANAGE ALL LISTS
######################################################################*/
	// Rename a Coupon List Link
	$(".renameListLink").click(function(){
		if($(this).parent().find(".deleteList").is(':visible')) {
			$(this).parent().find(".deleteList").hide();
		} else {
			$(this).parent().find(".deleteList").show();
		}
		return false;
	});
	
	// Rename a Coupon List Form
	$("#renameListForm").submit(function(){
		var listName = $(this).parent().find(".listNameLink");
		var newName = $(this).find(".listNameInput").val();
		myPostData = $(this).serializeArray();
		myPostData.push({name: "action", value: "mcdl_list_functions"});
		myPostData.push({name: "method", value: "rename_list" });
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: myPostData,
			success: function(data) {
				listName.text(newName);
			}
		}); // end AJAX request
		return false;
	});
	
	// Delete a Coupon List
	$(".deleteListLink").click(function(){
		var listID = $(this).next().find("input[name='id']").val();
		var listItem = $(this).parent();
		if (confirm('Are you sure to delete this list? By deleting this list it will also delete all of the coupon headers and coupons associated with it.')) {
			$.ajax({
				url: ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'action' : 'mcdl_list_functions', 'method' : 'delete_list', 'coupon_list_id' : listID },
				success: function(data) {
					listItem.remove();
				}
			}); // end AJAX request
		} // end confirm
		return false;
	});
/*######################################################################
# MANAGE A SINGLE LIST - HEADER FUNCTIONS
######################################################################*/
	// Multiple Headers Form
	// Update List Save Date from this Function
	$("#createMultipleHeaders").submit(function(){
		var thisElement = $(this);
		var listID = $(".wrap").attr("id");
		myPostData = $(this).serializeArray();
		myPostData.push({name: "action", value: "mcdl_list_functions"});
		myPostData.push({name: "method", value: "multiple_headers"});
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: myPostData,
			success: function(data) {
				window.location = "admin.php?page=mcd-list&id=" + listID;
			}
		}); // end AJAX request
		return false;
	});

	// Add Header
	// Update List Save Date from this Function
	$("#createHeader").submit(function(){
		myPostData = $(this).serializeArray();
		myPostData.push({name: "action", value: "mcdl_list_functions"});
		myPostData.push({name: "method", value: "new_header"});
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: myPostData,
			success: function(data) {
				//$("#message").html(data.response).show();
				$('#createHeader').get(0).reset();
				$('#couponHeaders').prepend(data.couponHeader);
				$('.mcd_cl_list_save_date_span').hide().text(theCurrentTime()).fadeIn('slow');
			}
		}); // end AJAX request
		return false;
	});
	
	// Edit Header Link
	$(".editCouponHeaderLink").live("click", function(){
		$(this).parents(".headerContentContainer").find(".editHeaderContainer").toggle();
		return false;
	});
	
	// Edit Header Form
	// Update List Save Date from this Function
	$("#editHeader").live("submit", function(){
		myPostData = $(this).serializeArray();
		myPostData.push({name: "action", value: "mcdl_list_functions"});
		myPostData.push({name: "method", value: "edit_header" });
		var headerCont = $(this).parents(".headerContentContainer");
		var editHeaderCont = $(this).parents(".editHeaderContainer");
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: myPostData,
			success: function(data) {
				headerCont.find(".productName").text(data.header_name);
				headerCont.find(".productPrice").text(data.header_value);
				headerCont.find(".productCategory").text(data.header_category);
				editHeaderCont.hide();
				$('.mcd_cl_list_save_date_span').hide().text(theCurrentTime()).fadeIn('slow');
			}
		}); // end AJAX request
		return false;
	});
	
	// Delete Header
	// Update List Save Date from this Function
	$(".deleteCouponHeaderLink").live("click", function(){
		var headerID = $(this).attr("rel");
		var parentCont = $(this).parents(".headerContentContainer");
		var listID = $(".wrap").attr("id");
		if (confirm('Are you sure to delete this header? The items attached to this header will no longer be displayed unless attached to another header.')) {
			$.ajax({
				url: ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'header_id' : headerID, 'action' : 'mcdl_list_functions', 'method' : 'remove_header', 'coupon_list_id' : listID },
				success: function(data) {
					parentCont.remove();
					$('.mcd_cl_list_save_date_span').hide().text(theCurrentTime()).fadeIn('slow');
				}
			}); // end AJAX request
		}
		return false;
	});
	
	// Net Price - Edit Link
	$(".editNetPriceLink").live("click", function(){
		var netPriceCont = $(this).parent().parent().parent().next();
		if(netPriceCont.is(':visible')) {
			netPriceCont.hide();
		} else {
			netPriceCont.show();
		}
		return false;
	});
	
	// Net Price - Close Link
	$(".closeNetPriceLink").live("click", function(){
		var netPriceCont = $(this).parent().parent();
		netPriceCont.hide();
		return false;
	});
	
	// Net Price - Form 
	// Update List Save Date from this Function
	$("#editNetPriceForm").live("submit", function(){
		var netPriceCont = $(this).parent();
		var netPrice = $(this).find("input[name='net_price']").val();
		var headerID = $(this).find("input[name='header_id']").val();
		var listID = $(".wrap").attr("id");
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: { 'net_price' : netPrice, 'header_id' : headerID, 'action' : 'mcdl_list_functions', 'method' : 'net_price', 'coupon_list_id' : listID },
			success: function(data) {
				netPriceCont.hide();
				$('.mcd_cl_list_save_date_span').hide().text(theCurrentTime()).fadeIn('slow');
			}
		}); // end AJAX request
		return false;
	});
	
	// Add Coupons Link on Header Name
	$(".addCoupons").live("click", function(){
		var addCouponsContainer = $(this).parent().parent().next().next();
		
		if(addCouponsContainer.is(':visible')) { 
			addCouponsContainer.hide();
			addCouponsContainer.find(".addYourOwnCouponContainer").hide();
			return false;
		} else {
			addCouponsContainer.find(".searchInput").show();
			addCouponsContainer.show();
			var search = addCouponsContainer.find(".searchInput").val();
			var searchResults = addCouponsContainer.find(".searchResults");
			var couponResults = addCouponsContainer.find(".couponResults");
			var couponRecords = addCouponsContainer.find(".couponRecords");
			$.ajax({
				url: ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'search' : search, 'action' : 'mcdl_search' },
				success: function(data) {
					couponResults.html(data.coupon_results);
					couponRecords.html('- Found ' + data.number_of_records + ' Coupons from MyCouponDatabase.com');
					searchResults.show();
				}
			}); // end AJAX request
			return false;
		}
	});
	
	// Search MyCouponDatabase.com Link
	$(".searchCouponLink").live("click", function(){	
		if($(this).parent().next().find(".addYourOwnCouponContainer").is(':visible')) { 
			$(this).parent().next().find(".addYourOwnCouponContainer").hide();
			$(this).parent().next().find(".searchInput").show();
			$(this).parent().next().find(".searchResults").show();
		}
		return false;
	});
	
	// Search My Coupon Database for Coupons Function
	var t; 
	$("input[name='search']").live("keyup", function() {  
		var search = $(this).val(); // what the user types
		var searchLength = search.length; // determine search length
		// Don't Search Until at Least 3 Characters have been Entered
		if(searchLength >= 2) {
			var searchResults = $(this).next();
			var couponResults = $(this).next().find(".couponResults");
			var couponRecords = $(this).next().find(".couponRecords");
			
			if (t) { clearTimeout(t); }
			t = setTimeout(function() { 
				// Run AJAX Request
				$(".numberOfRecords").html('<img src="../wp-content/plugins/my-coupon-database/images/ajax-loader.gif" width="16" height="16" alt="Searching" />');
				$.ajax({
					url: ajaxurl,
					type: 'POST', 
					dataType: 'json', 
					data: { 'search' : search, 'action' : 'mcdl_search' },
					success: function(data) {
						couponResults.html(data.coupon_results);
						couponRecords.html('- Found ' + data.number_of_records + ' Coupons from MyCouponDatabase.com');
						searchResults.show();
					}
				}); // end AJAX request
			}, 500);
		} // end searchLength
	});
	
	// Link that Hides Search Results
	$(".closeSearch").live("click", function(){
		$(this).parent().parent().fadeOut();
		return false;
	});
	
	// Add Your Own Coupon Link
	$(".addYourOwnCouponLink").live("click", function(){	
		if($(this).parent().next().find(".searchResults").is(':visible')) { 
			$(this).parent().next().find(".searchResults").hide(); 
		}
		if($(this).parent().next().find(".addYourOwnCouponContainer").is(':visible') === false) {
			$(this).parent().next().find(".addYourOwnCouponContainer").show();
			$(this).parent().next().find(".searchInput").hide();
		}
		return false;
	});
	
	// Export List Functionality
	/* $('.mcd_cl_list_export_link').click(function(){
		var listID = $(this).attr('title');
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: { 'action' : 'mcdl_list_functions', 'method' : 'export_list', 'id' : listID },
			success: function(data) {
				
			}
		}); // end AJAX request
		return false;
	}) */
/*######################################################################
# MANAGE A SINGLE LIST - COUPON FUNCTIONS
######################################################################*/
	// Add Coupon from MCD Functions
	// Update List Save Date from this Function
	$(".addCoupon").live("click", function(){
		// Used in AJAX CB
		var addedCouponsBox = $(this).parents(".headerContentContainer").find(".couponsAdded");
		// Grabs the Coupon
		var addedCoupon = '<li>' + $(this).parent().html() + '</li>';
		// Grabs Coupon ID
		var mcdID = $(this).attr("rel");
		// Grabs Coupon Group ID
		var couponGroupID = $(this).parents(".headerContentContainer").find("input[name='coupon_group_id']").val();
		// Grabs Coupon List ID
		var listID = $(".wrap").attr("id");
		
		// Hide this coupon after clicking Add Coupon
		$(this).parent().fadeOut();
		
		// Insert Coupon into DOM + Remove Coupon
		addedCouponsBox.prepend(addedCoupon).find(".addCoupon").remove();
		
		// Insert Coupon ID into Database
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: { 
				'action' : 'mcdl_list_functions', 
				'method' : 'add_mcd_coupon', 
				'mcd_id' : mcdID, 
				'coupon_group_id' : couponGroupID, 
				'coupon_list_id' : listID 
			},
			success: function(data) {
				addedCouponsBox.find("li:first-child").prepend('<a href="#" class="stackCoupon mcd_cl_greyLink" rel="' + data.insert_id + '">' + data.stack_text + '</a> ');
				addedCouponsBox.find("li:first-child .mcd_cl_remove_coupon_container").html('<a rel="' + data.insert_id + '" class="deleteCoupon mcd_cl_redLink" href="#">remove</a>');
				$('.mcd_cl_list_save_date_span').hide().text(theCurrentTime()).fadeIn('slow');
			}
		});
		return false;
	});
	
	// Add Your Own Coupon DB Insert AJAX
	// Update List Save Date from this Function
	$("#addYourOwnCoupon").live("submit", function(){
		var thisElement = $(this);
		var appendElement = $(this).parents(".headerContentContainer").find(".couponsAdded");
		var listID = $(".wrap").attr("id");
		myPostData = $(this).serializeArray();
		myPostData.push({name: "action", value: "mcdl_list_functions"});
		myPostData.push({name: "method", value: "add_user_coupon"});
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: myPostData,
			success: function(data) {
				thisElement.get(0).reset();
				appendElement.prepend(data.li_item);
				$('.mcd_cl_list_save_date_span').hide().text(theCurrentTime()).fadeIn('slow');
			}
		}); // end AJAX request
		return false;
	});
	
	// Delete Coupon
	// Update List Save Date from this Function
	$(".deleteCoupon").live("click", function(){
		var couponID = $(this).attr("rel");
		var parentLI = $(this).parents('li');
		var listID = $(".wrap").attr("id");
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: { 'action' : 'mcdl_list_functions', 'method' : 'remove_coupon', 'coupon_id' : couponID, 'coupon_list_id' : listID },
			success: function(data) {
				parentLI.remove();
				$('.mcd_cl_list_save_date_span').hide().text(theCurrentTime()).fadeIn('slow');
			}
		}); // end AJAX request
		return false;
	});
	
	// Coupon Stack Link
	$(".stackCoupon").live("click", function(){
		var couponID = $(this).attr("rel");
		var listID = $(".wrap").attr("id");
		if($(this).hasClass("mcd_cl_greyLink")) {
			$(this).removeClass("mcd_cl_greyLink");
			$(this).addClass("mcd_cl_redLinkBold");
			$.ajax({
				url: ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'action' : 'mcdl_list_functions', 'method' : 'stack_coupon', 'coupon_id' : couponID, 'coupon_list_id' : listID },
				success: function(data) {
					$('.mcd_cl_list_save_date_span').hide().text(theCurrentTime()).fadeIn('slow');
				}
			});
		} else {
			$(this).removeClass("mcd_cl_redLinkBold");
			$(this).addClass("mcd_cl_greyLink");
			$.ajax({
				url: ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'action' : 'mcdl_list_functions', 'method' : 'unstack_coupon', 'coupon_id' : couponID, 'coupon_list_id' : listID },
				success: function(data) {
					$('.mcd_cl_list_save_date_span').hide().text(theCurrentTime()).fadeIn('slow');
				}
			});
		}
		return false;
	})
/*######################################################################
# SETTINGS PAGE JAVASCRIPT
######################################################################*/
	$("#settings_form").submit(function() {
		myPostData = $(this).serializeArray();
		myPostData.push({name: "action", value: "mcdl_settings"});
		myPostData.push({name: "method", value: "update_settings"});
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: myPostData,
			success: function(data) {
				$("#message").text('Settings Saved').show();
			}
		}); // end AJAX request
		return false;
	});
/*######################################################################
# CATEGORIES JAVASCRIPT
######################################################################*/
	// Add Category Group Form
	$("#addCategoryGroupForm").submit(function(){
		myPostData = $(this).serializeArray();
		myPostData.push({name: "action", value: "mcdl_categories"});
		myPostData.push({name: "method", value: "add_category_group"});
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: myPostData,
			success: function(data) {
				$("#mcd_cl_category_groups").prepend(data.newCategoryGroup);
			}
		}); // end AJAX request
		return false;
	});
	
	// Delete Category Group Link
	$(".deleteCatGroupLink").live("click", function(){
		var groupID = $(this).attr('rel');
		var thisGroup = $("#" + groupID);
		if (confirm('Are you sure to delete this category group? By doing this it will also remove all of the categories in the group. Any items that were using those categories will no longer be categorized.')) {
			$.ajax({
				url: ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'action' : 'mcdl_categories', 'method' : 'delete_category_group', 'group_id' : groupID },
				success: function(data) {
					thisGroup.remove();
				}
			}); // end AJAX request
		}
		return false;
	});
	
	// Add Category Form
	$(".addCategoryForm").live("submit", function(){
		var thisCatListForm = $(this).parent().find(".saveSortOrderForm");
		var thisCatList = $(this).parent().find(".couponCatsList");
		var thisCatInput = $(this).parent().find(".catName");
		myPostData = $(this).serializeArray();
		myPostData.push({name: "action", value: "mcdl_categories"});
		myPostData.push({name: "method", value: "add_category"});
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: myPostData,
			success: function(data) {
				thisCatInput.val('');
				thisCatList.prepend(data.li);
				thisCatListForm.fadeIn();
				$(".couponCatsList").sortable();
			}
		}); // end AJAX request
		return false;
	});
	
	// Rename Category Link
	$(".renameCategoryLink").live("click", function(){
		$(this).parent().find(".mcd_cl_renameCategoryContainer").toggle();
		return false;
	});
	
	// Rename Category AJAX
	$(".mcd_cl_renameCategoryContainer input[type='button']").live("click", function(){
		var catID = $(this).parent().parent().find(".renameCategoryLink").attr("rel");
		var originalCatName = $(this).parent().parent().find(".mcd_cl_categoryName");
		var newCatName = $(this).parent().parent().find("input[name='rename_category']").val();
		var parentContainer = $(this).parent();
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: { 'action' : 'mcdl_categories', 'method' : 'rename_category', 'id' : catID, 'name' : newCatName }, 
			success: function(data) {
				originalCatName.text(newCatName);
				parentContainer.hide();
			}
		}); // end AJAX request
		return false;
	});
	
	// Delete Category
	$(".deleteCategoryLink").live("click", function(){
		var eleParent = $(this).parent();
		var catID = $(this).attr("rel");
		if (confirm('Are you sure to delete this category? Any items that were using this category will no longer be categorized.')) {
			$.ajax({
				url: ajaxurl,
				type: 'POST', 
				dataType: 'json', 
				data: { 'action' : 'mcdl_categories', 'method' : 'delete_category', 'id' : catID }, 
				success: function(data) {
					eleParent.remove();
				}
			}); // end AJAX request
		}
		return false;
	});
	
	// Edit Category Description Link
	$(".mcd_cl_update_cat_description_link").live("click", function(){
		$(this).parent().find('.mcd_cl_cat_description_container').toggle();
		return false;
	})
	
	// Edit Category Description AJAX
	$(".mcd_cl_cat_description_container input[type='button']").live("click", function(){
		var thisParent = $(this).parent();
		var catID = $(this).parents('li').find("input[name='id[]']").val();
		var description = $(this).prev().val();
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: { 'action' : 'mcdl_categories', 'method' : 'category_description', 'id' : catID, 'description' : description }, 
			success: function(data) {
				thisParent.hide();
			}
		}); // end AJAX request
		return false;
	})
	
	// Sort Categories
	$(".couponCatsList").sortable();
	//$(".couponCatsList").disableSelection();
	
	// Save Category Order
	$(".saveSortOrderForm").live("submit", function(){
		var unorderedText = $(this).find(".mcd_cl_unordered");
		var notification = $(this).find('.mcd_cl_savedListNotification');
		myPostData = $(this).serializeArray();
		myPostData.push({name: "action", value: "mcdl_categories"});
		myPostData.push({name: "method", value: "save_order"});
		$.ajax({
			url: ajaxurl,
			type: 'POST', 
			dataType: 'json', 
			data: myPostData,
			success: function(data) {
				unorderedText.remove();
				notification.fadeIn(function(){
					notification.fadeOut(3000);
				});
			}
		}); // end AJAX request
		return false;
	});
	
	// Expand All Cat Groups
	$(".expandCatGroups").click(function(){
		$("#mcd_cl_category_groups .inside").show();
	});
	
	// Collapse All Cat Groups
	$(".collapseCatGroups").click(function(){
		$("#mcd_cl_category_groups .inside").hide();
	});
});