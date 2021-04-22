/*--------------------------------------------------------------
 Copyright (C) pixelemu.com
 License: http://www.pixelemu.com/license.html PixelEmu Proprietary Use License
 Website: http://www.pixelemu.com
 Support: info@pixelemu.com
 ---------------------------------------------------------------*/

(function ($) {

	"use strict";

	/* -------------------------------------------------------- */
	/* FUNCTIONS                                                */
	/* -------------------------------------------------------- */
	
	// checking for types
	$.urlParam = function(name){
	    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	    if(results){
	    	return results[1] || 0;
	    }
	}

	// equla height for matched elements
	function tcfEqualHeight (group) {
	   let tallest = 0;
	   let thisHeight = 0;
	   group.each(function() {
	      thisHeight = $(this).height();
	      if(thisHeight > tallest) {
	         tallest = thisHeight;
	      }
	   });
	   group.height(tallest);
	}

	// get locations with AJAX in a search
	function tcfLocationsAjax() {
		var input = $('.location-tax-input');
		var taxonomy = input.attr('data-taxonomy');
		var nonce = $("input#location_nonce").attr('value');
		var inputLocationNoLocation = $(".terraclassifieds-location-no-location");

		input.autocomplete({
			minLength: 3,
			source: function(name, response) {
				// set ajax data
				var data = {
					'action'   : 'terraclassifiedsLocationsAjax',
					'taxonomy' : taxonomy,
					'nonce'    : nonce,
					'name'     : name['term'],
				};
				//console.log(settings.ajaxurl, data);
				$.post( settings.ajaxurl, data, function( data ) {
					if(data.data.length > 0){
						inputLocationNoLocation.removeClass("show");
					} else{
						inputLocationNoLocation.addClass("show");
					}
					//console.log(data.data);
					response(data.data);
				});
			},
			select:function(event, ui) {

			},
			close:function(event, ui) {

			}
		});
	}
	
	function tcfAddAdvertForm() {
	   let addAdvertForm = $('form.cmb-form');
	   if (addAdvertForm.length > 0) {
		   
		   // cancel button redirects to previous page
		   var baseUrl = document.referrer;
		   $( "<a class='button tcf-cancel-button' href='" + baseUrl + "'>" + php_vars.cancelButtonText + "</a>" ).insertAfter( ".button-primary" );
		   
		   // add classes active for inputs
			$( ".cmb-form .cmb2-wrap input[type='text'], .cmb-form .cmb2-wrap input[type='email'],  .cmb-form .cmb2-wrap textarea" ).focus(function() {
			  $( this ).siblings( ".cmb2-metabox-description" ).addClass("active");
			});
			$( ".cmb-form .cmb2-wrap input[type='text'], .cmb-form .cmb2-wrap input[type='email'], .cmb-form .cmb2-wrap textarea" ).focusout(function() {
				$( this ).siblings( ".cmb2-metabox-description" ).removeClass("active");
			});
	
			$('.cmb2-id--tc-gallery .cmb2-upload-button').hover(
			       function(){ $(this).next( ".cmb2-metabox-description" ).addClass('active') },
			       function(){ $(this).next( ".cmb2-metabox-description" ).removeClass('active') }
			)
			
			// open categories
			$( ".tcf-add-category-button, .tcf-change-category-button" ).click(function() {
			  $( this ).next("ul.cmb2-radio-list").toggle();
			  $( ".tcf-add-category-done" ).toggle();
			  $(".cmb2-id--tc-category").toggleClass("tcf-categories-down");
			  //$( this ).hide();
				// equal height for 1. level categories
				tcfEqualHeight($(".cmb2-id--tc-category ul.cmb2-list > li"));
			});
			
			// hide categories after click DONE
			$( ".tcf-add-category-done" ).click(function() {
			  $(".cmb2-id--tc-category .cmb2-radio-list").hide();
			  $(".cmb2-id--tc-category").toggleClass("tcf-categories-down");
			  $( this ).hide();
			});
			
			$( "ul.cmb2-list > li > span > label" ).click(function() {
				$(".cmb2-id--tc-category ul.cmb2-list > li ul").hide();
			});
			
			$( "ul.cmb2-list li label" ).click(function() {
				$( "ul.cmb2-list li label" ).removeClass("active");
				$( this ).addClass("active");
				//$( this ).parent().parent().siblings().children("ul").hide();
			});
			
			$( "ul.cmb2-list > li > span > label" ).click(function() {
				// add class for expanded categories
				 $(".cmb2-id--tc-category").addClass("tcf-some-category-selected");
				 $( ".tcf-add-category-button").remove();
				 $('.cmb2-id--tc-category .cmb-th').removeClass("error");
			});
			
			// toggle for second UL level
			$( "ul.cmb2-list > li.parent > span > label" ).click(function() {
			    $( this ).parent().parent().find("> ul").toggle();
			});
			
			// toggle for third and more UL level
			$( "ul.cmb2-indented-hierarchy > li.parent > span > label" ).click(function() {
				$( this ).parent().parent().siblings().find("ul").hide();
			    $( this ).parent().parent().find("> ul").toggle();
			});
			
			// add X to close categories submenu
			$( ".cmb2-id--tc-category ul.cmb2-list > li > ul" ).append( "<div class='tcf-close'>X</div>" );
			
			// close categories submenu
			$( ".cmb2-id--tc-category ul.cmb2-list > li > ul .tcf-close" ).click(function() {
				$( this ).parent().hide();
			});
			
			// draft - move filed next to buttons
			$( ".cmb2-id--tc-draft-status" ).insertAfter( ".cmb2-wrap" );
			
            // change submit button label if is checked on load
            var $drafCheckbox = $('input[name="_tc_draft_status"]');
            var inputObjectID = $('.cmb-form input[name="object_id"]');
            if(inputObjectID.val() == 'fake-oject-id'){
            	var adStatus = 'new-advert';
            } else{
            	var adStatus = 'edited-advert';
            }
            
            if($drafCheckbox.is(':checked')){
            	$('.button-primary').attr('value', php_vars.addAdvertSubmitButtonDraft);
            }
            
            // change submit button label if is checked after user action
            $($drafCheckbox).change(function() {
        	   if ($(this).is(':checked')) {
        		   $('.button-primary').attr('value', php_vars.addAdvertSubmitButtonDraft);
        	   } else {
        		   if(adStatus == 'new-advert'){
        			   $('.button-primary').attr('value', php_vars.addAdvertSubmitButtonPublish);
        		   } else{
        			   $('.button-primary').attr('value', php_vars.addAdvertSubmitButtonSaveChanges);
        		   }
        	   }
        	});
            
			// price
            if(adStatus == 'new-advert'){
            	$( ".cmb2-id--tc-sell-type ul li:first-child .cmb2-option" ).click();
            }
			if($( ".cmb2-id--tc-sell-type ul li .cmb2-option[value='price']" ).length == 0){
				$( ".cmb2-id--tc-price, .cmb2-id--tc-negotiable").hide();
			}
			$( ".cmb2-id--tc-sell-type ul li .cmb2-option[value='price']" ).click(function() {
				$("input#_tc_price").addClass("input-focus");
				$("input#_tc_price").prop('disabled', false);
				$("input#_tc_negotiable").prop('disabled', false);
			});
			$( ".cmb2-id--tc-sell-type ul li .cmb2-option:not([value='price'])" ).click(function() {
				$("input#_tc_price").removeClass("input-focus");
				$("input#_tc_price").removeClass("error");
				$("input#_tc_price").prop('disabled', true);
				$("input#_tc_negotiable").prop('disabled', true);
				$("input#_tc_price").attr('value', ''); 
				$("input#_tc_negotiable").prop('checked', ''); 
			});
			if(!($( ".cmb2-id--tc-sell-type ul li .cmb2-option[value='price']" ).prop( "checked" ))){
				$("input#_tc_price").prop('disabled', true);
			}
			
			// categories breadcrumb
			$('.cmb2-list li span label').on('click', function() {
			  let $this = $(this),
		      $bc = $('<div class="tcf-categories-breadcrumb-in"></div>');
			
			  $this.parents('li').each(function(n, li) {
			      let $a = $(li).children("span").children("label").children('span').clone();
			      $bc.prepend('<span class="tcf-categories-breadcrumb-separator"> / </span>', $a);
			  });
			  $('.tcf-categories-breadcrumb').html( $bc.prepend('') );
			})
			
			// categories breadcrumb - show breadcrumb on edited ads
			if($( ".cmb2-id--tc-category ul.cmb2-list li span input[type=radio]:checked" ).length > 0){
			  let $this = $( ".cmb2-id--tc-category ul.cmb2-list li span input[type=radio]:checked + label span" ),
		      $bc = $('<div class="tcf-categories-breadcrumb-in"></div>');
			
			  $this.parents('li').each(function(n, li) {
			      let $a = $(li).children("span").children("label").children('span').clone();
			      $bc.prepend('<span class="tcf-categories-breadcrumb-separator"> / </span>', $a);
			  });
			  $('.tcf-categories-breadcrumb').html( $bc.prepend('') );
			}
	
			// move agreements to the form
			$( ".agreements" ).insertBefore( $( "form.cmb-form .button-primary" ) );
		        
		    // check if location are required
            if(php_vars.locationRequired == 1){
            	var tc_location_req = true;
            } else {
            	var tc_location_req = false;
            }
            if(tc_location_req){
            	$('.cmb2-id--tc-locations .cmb-th label').append(' *');
            }
            
		    // check if type are required
            if(php_vars.typeRequired == 1){
            	var tc_type_req = true;
            } else {
            	var tc_type_req = false;
            }
            if(tc_type_req){
            	$('.cmb2-id--tc-types .cmb-th label').append(' *');
            }
            
		    // check if user can add price = 0
            if(php_vars.allowPriceZero == 1){
            	var tc_allow_price_zero = 0;
            } else {
            	var tc_allow_price_zero = 0.01;
            }
             
            // override 'min' from jQuery Validation Plugin to accept comma
            $.validator.methods.min = function ( value, element, param ) {
            	var globalizedValue = value.replace(",", ".");
            	return this.optional( element ) || globalizedValue >= param;
            }
        	
			// add advert form validation
		    $('.cmb-form').validate({
				errorElement: "label",
				errorPlacement: function () { },
				highlight: function(element) {
				    $(element).addClass("error");
				    $(element).parent().siblings(".cmb-th").addClass("error");
				},
				unhighlight: function(element) {
				    $(element).removeClass("error");
				    $(element).parent().siblings(".cmb-th").removeClass("error");
				},
		    	ignore: [],
		        rules: {
		            _tc_post_title: {
		                required: true,
		            },
		            _tc_post_content: {
		                required: true,
		            },
		            _tc_price: {
		                required: true,
		                min: tc_allow_price_zero
		            },
		            _tc_category: {
		                required: true,
		            },
		            tc_terms_and_conditions: {
		                required: true,
		            },
		            tc_privacy_policy: {
		                required: true,
		            },
		            tc_gdpr: {
		                required: true,
		            },
		            _tc_locations: {
		                required: tc_location_req,
		            },
		            _tc_types: {
		                required: tc_type_req,
		            },
		            '_tc_types[]': {
		                required: tc_type_req,
		            },
		        },
		        messages: {
		        	_tc_post_title: '',
		        	_tc_post_content: '',
		        	_tc_price: '',
		        	_tc_category: '',
		        }
		    });
		    
		    // check if images are required
            if(php_vars.galleryRequired == 1){
            	var tc_gallery_req = true;
            } else {
            	var tc_gallery_req = false;
            }
            if(tc_gallery_req){
            	$('.cmb2-id--tc-gallery label').append(' *');
            }
            
		    // add advert - custom validation
			$('form.cmb-form').submit(function( event ) {
				
				// remove error messages
				$(".cmb2-id--tc-validate-message, .cmb2-id--tc-too-short-description").hide();
				// add error message if errors detected
				if($(".cmb-form input").hasClass("error")){
					$(".cmb2-id--tc-validate-message").show();
				}
				// show error message when description is too short
				var numbOfcharsDescriptionValue = $(".cmb2_textarea").val();
	            var numbOfcharsDescriptionLength = numbOfcharsDescriptionValue.length;
	            if(numbOfcharsDescriptionLength < php_vars.numbOfcharsDescriptionLength){
	            	$(".cmb2-id--tc-too-short-description").show();
	            }
				// check if price input is disabled
				if($('input#_tc_price').is(':disabled')){
				     $( 'input#_tc_price' ).removeClass("error")
				}
				// show error message if required fields are not filled
				if($( ".cmb2-id--tc-category ul.cmb2-list li span input[type=radio]:checked" ).length == 0){
					$('.tcf-add-category-button').addClass("error");
					$('.cmb2-id--tc-category .cmb-th').addClass("error");
				}
				
				// check if location is selected (if location is required)
				if(tc_location_req){
					if($( ".cmb2-id--tc-locations ul li input[type=radio]:checked" ).length == 0){
						$('.cmb2-id--tc-locations .cmb-th').addClass("error");
					}
				}
				
				// check if type  is selected (if type is required)
				if(tc_type_req){
					if($( ".cmb2-id--tc-types ul li input[type=checkbox]:checked" ).length == 0){
						$('.cmb2-id--tc-types .cmb-th').addClass("error");
					}
				}
				
				// check if there are some images (if images are required)
				if(tc_gallery_req){
					if (!($('#_tc_gallery-status .cmb2-media-item').length > 0)){
						$('.cmb2-upload-button').addClass("error");
						$('.cmb2-id--tc-gallery .cmb-th').addClass("error");
						return false;
					} else {
						$('.cmb2-upload-button').removeClass("error");
						$('.cmb2-id--tc-gallery .cmb-th').removeClass("error");
					}
				}
			});
			
			// gallery - limit files
			$( document ).on( 'cmb_media_modal_select', function( e ) {
				let countImages = $('.cmb-type-file-list .cmb2-media-status .img-status').length; 
				if(countImages > php_vars.imagesLimit){
					alert(php_vars.imagesLimitMessage1 + php_vars.imagesLimit + php_vars.imagesLimitMessage2);
					$( ".cmb-type-file-list .cmb2-media-status .img-status:nth-child(" + php_vars.imagesLimit + ")" ).nextAll().remove();
				}
				let imagesLeftNumber = php_vars.imagesLimit - countImages;
				if(imagesLeftNumber < 0){
					imagesLeftNumber = 0;
				}
				$( ".tcf-images-left" ).remove();
				$( ".cmb2-id--tc-gallery .cmb2-upload-button" ).after( "<div class='tcf-images-left'>" + imagesLeftNumber + php_vars.imagesLeft + "</div>" );
			});
			
			// characters limit - title
			$('#_tc_post_title').after('<div class="tcf-title-characters-limit"></div>');
			$('#_tc_post_title').keyup(function () {
			  let max_title = this.getAttribute("maxlength");
			  if(max_title != 524288){
				  let len_title = $(this).val().length;
				  if (len_title >= max_title) {
				    $('.tcf-title-characters-limit').text(php_vars.charactersLimit);
				  } else {
				    let char_title = max_title - len_title;
				    $('.tcf-title-characters-limit').text(char_title + php_vars.charactersLeft);
				  }
			  }
			});
			
			// characters limit - description
			$('#_tc_post_content').after('<div class="tcf-desc-characters-limit"></div>');
			$('#_tc_post_content').keyup(function () {
			  let max_desc = this.getAttribute("maxlength");
			  let descriptionMinLength = php_vars.numbOfcharsDescriptionLength;
			  let descriptionMinLengthWholeText = php_vars.descriptionMinimumCharacters;
			  if(max_desc != 9999999999){
				  let len_desc = $(this).val().length;
				  if (len_desc >= max_desc) {
				    $('.tcf-desc-characters-limit').text(php_vars.charactersLimit);
				  } else {
				    let char_desc = max_desc - len_desc;
					if(len_desc >= descriptionMinLength){
						descriptionMinLengthWholeText = '';
					}
				    $('.tcf-desc-characters-limit').text(descriptionMinLengthWholeText + char_desc + php_vars.charactersLeft);
				  }
			  }
			});
			
			// add expire date to new advert
			if($('#_tc_expire_date').val().length == 0){
				let numberOfDaysToAdd = php_vars.expireTime;
				numberOfDaysToAdd = parseInt(numberOfDaysToAdd);
				let d = new Date();
				d.setDate(d.getDate() + numberOfDaysToAdd);
				let curr_day = d.getDate();
				let curr_month = d.getMonth();
				curr_month++;
				let curr_year = d.getFullYear();
				let completeDate = curr_month + "/" + curr_day + "/" + curr_year;
				$("#_tc_expire_date").val(completeDate);
			}
			
			if(!($('body').hasClass('logged-in'))){
			    // terms and conditions modal
				var tc_modal = document.getElementById('terms-and-conditions-modal'); // Get the modal
				var tc_btn = document.getElementById("tcf-modal-terms-and-conditions"); // Get the button that opens the modal
				var tc_span = document.getElementsByClassName("tcf-modal-terms-and-conditions-close")[0]; // Get the <span> element that closes the modal
				tc_btn.onclick = function() {
				    tc_modal.style.display = "block"; // When the user clicks on the button, open the modal 
				}
				tc_span.onclick = function() {
				    tc_modal.style.display = "none"; // When the user clicks on <span> (x), close the modal
				}
				
			    // privacy policy modal
				var pp_modal = document.getElementById('privacy-policy-modal'); // Get the modal
				var pp_btn = document.getElementById("tcf-modal-privacy-policy"); // Get the button that opens the modal
				var pp_span = document.getElementsByClassName("tcf-modal-privacy-policy-close")[0]; // Get the <span> element that closes the modal
				pp_btn.onclick = function() {
				    pp_modal.style.display = "block"; // When the user clicks on the button, open the modal 
				}
				pp_span.onclick = function() {
				    pp_modal.style.display = "none"; // When the user clicks on <span> (x), close the modal
				}
	
				// security - close modal after click on anywhere outside of the modal
				window.onclick = function(event) {
				    if (event.target == tc_modal) {
				        tc_modal.style.display = "none"; // When the user clicks anywhere outside of the modal, close it
				    }
				    if (event.target == pp_modal) {
				        pp_modal.style.display = "none"; // When the user clicks anywhere outside of the modal, close it
				    }
				}
			}
			
			// Locations
			if($(".cmb2-id--tc-locations").length > 0){
				// locations show - when click
				$( ".cmb2-id--tc-locations > .cmb-td" ).prepend( "<span class='tcf-clear-locations'><i class='fas fa-times'></i> <strong>" + php_vars.clearAllLocations + "</strong></span>" );
				
				$( ".tcf-clear-locations" ).click(function() {
					$( ".cmb2-id--tc-locations > .cmb-td li" ).attr('style','');
					$( ".cmb2-id--tc-locations > .cmb-td .cmb2-indented-hierarchy" ).attr('style','');
					$( ".cmb2-id--tc-locations > .cmb-td li" ).removeClass("clicked");
					$(".cmb2-id--tc-locations input").attr('checked', false);
				});
				
				$( ".cmb2-id--tc-locations > .cmb-td ul li" ).click(function(e) {

					if (e.target.tagName != 'LABEL') return;

					var $this = $( this );
					$(".cmb2-id--tc-locations input").attr('checked', false); //reset all radios
					$this.children("input").attr('checked', true);

					if($this.next(".cmb2-indented-hierarchy").length > 0) {
						if($this.hasClass("clicked")){
							$this.siblings().hide(300);
							$this.siblings("li").show(300);
							$this.removeClass("clicked");
						} else {
						  $this.addClass("clicked");
						  $this.siblings().hide(300);
						  $this.next(".cmb2-indented-hierarchy").show(300);
						}
					}

					$('.cmb2-id--tc-locations .cmb-th').removeClass("error");
				});


				
				// locations show - on load
				$( ".cmb2-id--tc-locations input[type=radio]:checked").parentsUntil('.cmb2-id--tc-locations .cmb2-list').show();
			}

			
			
			//types
			$( ".cmb2-id--tc-types > .cmb-td ul li" ).click(function() {
				$('.cmb2-id--tc-types .cmb-th').removeClass("error");
			});
			
			// locations tree expanded if advert has already location set
			if($( ".cmb2-id--tc-locations ul.cmb2-list li input[type=checkbox]:checked" ).length > 0){
				$('.cmb2-id--tc-locations ul.cmb2-list li input[type=checkbox]:checked').parents("ul").show();
				$('.cmb2-id--tc-locations ul.cmb2-list li input[type=checkbox]:checked').parents("ul").siblings().hide();
				$('.cmb2-id--tc-locations ul.cmb2-list li input[type=checkbox]:checked').parents("ul").prev().show().addClass("clicked");
			}
	   }
	}
	
	function tcfSingleAdd(){
	   let singleAdvert = $('.single-classified');
	   if (singleAdvert.length > 0) {
	   		// light slider
		    $('.lightSlider').lightSlider({
		        gallery:true,
		        item:1,
		        loop:true,
		        thumbItem:9,
		        slideMargin:0,
		        enableDrag: false,
		        currentPagerPosition:'left',
		        onSliderLoad: function(el) {
		            el.lightGallery({
		                selector: '.lightSlider .lslide'
		            });
		        }   
		    });
		    
			// contact form validation
			$('.contact-form button.terraclassifieds-contact-advertiser').click(function() {
				var redirect = $(this).attr('data-redirect');
				if( redirect ) {
					Swal.fire({
						title: php_vars.loginPopupText,
						text: "",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: php_vars.loginPopupBtnConfirm,
						cancelButtonText: php_vars.loginPopupBtnCancel,
					}).then((result) => {
						if (result.isConfirmed && redirect) {
							window.location.href = redirect;
						}
					});
				}
			});

		    $('#terraclassifieds-contact-form').validate({ // initialize the plugin
				errorPlacement: function () { },
				highlight: function(element) {
				    $(element).addClass("error");
				},
				unhighlight: function(element) {
				    $(element).removeClass("error");
				},
		    	ignore: [],
		        rules: {
		            tc_username: {
		                required: true,
		            },
		            tc_email: {
		                required: true,
		                email: true,
		            },
		            tc_message: {
		                required: true,
		            },
		            tc_terms_and_conditions: {
		                required: true,
		            },
		            tc_privacy_policy: {
		                required: true,
		            },
		            tc_gdpr: {
		                required: true,
		            },
		        },
		        messages: {
		        	tc_username: '',
		        	tc_email: '',
		        	tc_message: '',
		        }
		    });
		    
			// contact form validation
			$('.abuse-form button.terraclassifieds-contact-advertiser').click(function() {
				var redirect = $(this).attr('data-redirect');
				if( redirect ) {
					Swal.fire({
						title: php_vars.loginPopupText,
						text: "",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: php_vars.loginPopupBtnConfirm,
						cancelButtonText: php_vars.loginPopupBtnCancel,
					}).then((result) => {
						if (result.isConfirmed && redirect) {
							window.location.href = redirect;
						}
					});
				}
			});

		    $('#terraclassifieds-abuse-form').validate({ // initialize the plugin
				errorPlacement: function () { },
				highlight: function(element) {
				    $(element).addClass("error");
				},
				unhighlight: function(element) {
				    $(element).removeClass("error");
				},
		    	ignore: [],
		        rules: {
		            tc_username_abuse: {
		                required: true,
		            },
		            tc_email_abuse: {
		                required: true,
		                email: true,
		            },
		            tc_message_abuse: {
		                required: true,
		            },
		            tc_terms_and_conditions_abuse: {
		                required: true,
		            },
		            tc_privacy_policy_abuse: {
		                required: true,
		            },
		            tc_gdpr_abuse: {
		                required: true,
		            },
		        },
		        messages: {
		        	tc_username_abuse: '',
		        	tc_email_abuse: '',
		        	tc_message_abuse: '',
		        }
		    });
		    
		    // whole phone numer after click
			$( ".terraclassifieds-phone-more" ).click(function() {
				var redirect = $(this).attr('data-redirect');
				if( redirect ) {
					Swal.fire({
						title: php_vars.loginPopupText,
						text: "",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: php_vars.loginPopupBtnConfirm,
						cancelButtonText: php_vars.loginPopupBtnCancel,
					}).then((result) => {
						if (result.isConfirmed && redirect) {
							window.location.href = redirect;
						}
					});
				} else {
					$( this ).hide();
					$( this ).siblings(".terraclassifieds-value").addClass("expanded");
				}

			});

			$('.terraclassifieds-author-name').click(function() {
				var redirect = $(this).attr('data-redirect');
				if( redirect ) {
					Swal.fire({
						title: php_vars.loginPopupText,
						text: "",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: php_vars.loginPopupBtnConfirm,
						cancelButtonText: php_vars.loginPopupBtnCancel,
					}).then((result) => {
						if (result.isConfirmed && redirect) {
							window.location.href = redirect;
						}
					});
				}
			});

			$('.terraclassifieds-website-more').click(function() {
				var redirect = $(this).attr('data-redirect');
				if( redirect ) {
					Swal.fire({
						title: php_vars.loginPopupText,
						text: "",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: php_vars.loginPopupBtnConfirm,
						cancelButtonText: php_vars.loginPopupBtnCancel,
					}).then((result) => {
						if (result.isConfirmed && redirect) {
							window.location.href = redirect;
						}
					});
				}
			});

		    // terms and conditions modal - contact form
		    if($( "#terms-and-conditions-modal" ).length > 0){
		    	var tc_modal = document.getElementById('terms-and-conditions-modal'); // Get the modal
				var tc_btn = document.getElementById("tcf-modal-terms-and-conditions"); // Get the button that opens the modal
				var tc_span = document.getElementsByClassName("tcf-modal-terms-and-conditions-close")[0]; // Get the <span> element that closes the modal
				tc_btn.onclick = function() {
				    tc_modal.style.display = "block"; // When the user clicks on the button, open the modal 
				}
				tc_span.onclick = function() {
				    tc_modal.style.display = "none"; // When the user clicks on <span> (x), close the modal
				}
		    }
			
		    // privacy policy modal - contact form
		    if($( "#privacy-policy-modal" ).length > 0){
				var pp_modal = document.getElementById('privacy-policy-modal'); // Get the modal
				var pp_btn = document.getElementById("tcf-modal-privacy-policy"); // Get the button that opens the modal
				var pp_span = document.getElementsByClassName("tcf-modal-privacy-policy-close")[0]; // Get the <span> element that closes the modal
				pp_btn.onclick = function() {
				    pp_modal.style.display = "block"; // When the user clicks on the button, open the modal 
				}
				pp_span.onclick = function() {
				    pp_modal.style.display = "none"; // When the user clicks on <span> (x), close the modal
				}
			}
			
			
		    // terms and conditions modal - abuse form
		    if($( "#terms-and-conditions-modal-abuse" ).length > 0){
		    	var tc_modal_abuse = document.getElementById('terms-and-conditions-modal-abuse'); // Get the modal
				var tc_btn_abuse = document.getElementById("tcf-modal-terms-and-conditions-abuse"); // Get the button that opens the modal
				var tc_span_abuse = document.getElementsByClassName("tcf-modal-terms-and-conditions-close-abuse")[0]; // Get the <span> element that closes the modal
				tc_btn_abuse.onclick = function() {
				    tc_modal_abuse.style.display = "block"; // When the user clicks on the button, open the modal 
				}
				tc_span_abuse.onclick = function() {
				    tc_modal_abuse.style.display = "none"; // When the user clicks on <span> (x), close the modal
				}
		    }
			
		    // privacy policy modal - abuse form
		    if($( "#privacy-policy-modal-abuse" ).length > 0){
				var pp_modal_abuse = document.getElementById('privacy-policy-modal-abuse'); // Get the modal
				var pp_btn_abuse = document.getElementById("tcf-modal-privacy-policy-abuse"); // Get the button that opens the modal
				var pp_span_abuse = document.getElementsByClassName("tcf-modal-privacy-policy-close-abuse")[0]; // Get the <span> element that closes the modal
				pp_btn_abuse.onclick = function() {
				    pp_modal_abuse.style.display = "block"; // When the user clicks on the button, open the modal 
				}
				pp_span_abuse.onclick = function() {
				    pp_modal_abuse.style.display = "none"; // When the user clicks on <span> (x), close the modal
				}
			}

			// security - close modal after click on anywhere outside of the modal - contact and abuse form
			window.onclick = function(event) {
			    if (event.target == tc_modal) {
			        tc_modal.style.display = "none"; // When the user clicks anywhere outside of the modal, close it
			    }
			    if (event.target == pp_modal) {
			        pp_modal.style.display = "none"; // When the user clicks anywhere outside of the modal, close it
			    }
			    if (event.target == tc_modal_abuse) {
			        tc_modal_abuse.style.display = "none"; // When the user clicks anywhere outside of the modal, close it
			    }
			    if (event.target == pp_modal_abuse) {
			        pp_modal_abuse.style.display = "none"; // When the user clicks anywhere outside of the modal, close it
			    }
			}
		}
	   
	   // add class to body if classifieds is archived
	   var bodyContainer = $( "body");
	   var singleAdContainer = $( ".single .classified.type-classified");
	   if(singleAdContainer.hasClass('status-archived')){
		   bodyContainer.addClass('single-ad-archived');
	   }
	}
	
	function tcfRegistration(){
		let registrationPage = $('.terraclassifieds-registration-content');
		if (registrationPage.length > 0) {
		    $('#terraclassifieds-signup-form').validate({ // initialize the plugin
				errorPlacement: function (error, element) { 
					error.appendTo(element.next());
				},
				highlight: function(element) {
				    $(element).addClass("error");
				    $(element).closest("p").addClass("error");
				},
				unhighlight: function(element) {
				    $(element).removeClass("error");
				    $(element).closest("p").removeClass("error");
				},
		    	ignore: [],
		        rules: {
		            user_login: {
		                required: true,
		            },
		            user_email: {
		                required: true,
		                email: true,
		            },
		            user_password: {
		                required: true,
		                minlength: 5
		            },
		            tc_terms_and_conditions: {
		                required: true,
		            },
		            tc_privacy_policy: {
		                required: true,
		            },
		            tc_gdpr: {
		                required: true,
		            },
		        },
		        messages: {
					user_password: {
						required: "",
						minlength: jQuery.validator.format(php_vars.registrationPasswordMinimumCharacters1 + " {0} " + php_vars.registrationPasswordMinimumCharacters2)
					},
					tc_terms_and_conditions: '',
					tc_privacy_policy: '',
					tc_gdpr: '',
		        }
		    });
		    
		    // terms and conditions modal
			var tc_modal = document.getElementById('terms-and-conditions-modal'); // Get the modal
			var tc_btn = document.getElementById("tcf-modal-terms-and-conditions"); // Get the button that opens the modal
			var tc_span = document.getElementsByClassName("tcf-modal-terms-and-conditions-close")[0]; // Get the <span> element that closes the modal
			tc_btn.onclick = function() {
			    tc_modal.style.display = "block"; // When the user clicks on the button, open the modal 
			}
			tc_span.onclick = function() {
			    tc_modal.style.display = "none"; // When the user clicks on <span> (x), close the modal
			}
			
		    // privacy policy modal
			var pp_modal = document.getElementById('privacy-policy-modal'); // Get the modal
			var pp_btn = document.getElementById("tcf-modal-privacy-policy"); // Get the button that opens the modal
			var pp_span = document.getElementsByClassName("tcf-modal-privacy-policy-close")[0]; // Get the <span> element that closes the modal
			pp_btn.onclick = function() {
			    pp_modal.style.display = "block"; // When the user clicks on the button, open the modal 
			}
			pp_span.onclick = function() {
			    pp_modal.style.display = "none"; // When the user clicks on <span> (x), close the modal
			}

			// security - close modal after click on anywhere outside of the modal
			window.onclick = function(event) {
			    if (event.target == tc_modal) {
			        tc_modal.style.display = "none"; // When the user clicks anywhere outside of the modal, close it
			    }
			    if (event.target == pp_modal) {
			        pp_modal.style.display = "none"; // When the user clicks anywhere outside of the modal, close it
			    }
			}
		}
	}
	
	function tcfEditProfile(){
		let editProfilePage = $('.terraclassifieds-edit-profile');
		if (editProfilePage.length > 0) {
		    $('#edituser').validate({ // initialize the plugin
				errorPlacement: function () { },
				highlight: function(element) {
				    $(element).addClass("error");
				    $(element).parent().addClass("error");
				},
				unhighlight: function(element) {
				    $(element).removeClass("error");
				    $(element).parent().removeClass("error");
					editProfilePage.find('.terraclassifieds-message').remove();
				},
		    	ignore: [],
		        rules: {
		            user_login: {
		                required: true,
		            },
		            user_email: {
		                required: true,
		                email: true,
		            },
		            tc_phone: {
		            	number: true
		            },
		        },
		    });
		}
	}
	
	function tcfSearch(){
		
		let inputLocation = $("#terraclassifieds-product-search-location-field");
		let inputLocationMoreCharacters = $(".terraclassifieds-location-more-characters");
		let inputLocationNoLocation = $(".terraclassifieds-location-no-location");
		
		// nice select for location select
		$("#ad_location").select2({});
		
		if (inputLocation.length > 0) {
	
			// AJAX search in not empty locations 
			tcfLocationsAjax();
			
			// removes hidden field so it will be removed form search URL too
			$("input[name='_wp_http_referer'], input[name='location_nonce']").remove();
		
			// make names from slugs of locations
			let locationInputValue = inputLocation.val();
			var locationTextNameToSlug = locationInputValue.replace(/-/g, ' ');
			$(inputLocation).val(locationTextNameToSlug);
				
			// make slugs from names of locations before form is submitted (location input option)
			$('#terraclassifieds-search-form').submit(function() {
				let locationInputValue = inputLocation.val();
				var locationTextNameToSlug = locationInputValue.replace(/ /g,'-');
				$(inputLocation).val(locationTextNameToSlug);
			});
		}
		
		// show info message on keyup and hide it after entering 3 characters
		inputLocation.bind('keyup', function(e){
		     if($(this).val().length >= 3){
		     	inputLocationMoreCharacters.removeClass("show");
		     } else{
		     	inputLocationMoreCharacters.addClass("show");
		     	inputLocationNoLocation.removeClass("show");
		     }
		});
		
		// type select -> type buttons - BEGIN
		var selectName = $('select#ad_type').attr('name');
		// add a hidden element with the same name as the select
		var hidden = $('<input type="hidden" name="'+selectName+'">');
		if($.urlParam('ad_type') != 0){
			hidden.val($.urlParam('ad_type'));
		}
		hidden.insertAfter($('select#ad_type'));
		
		$("select#ad_type option").unwrap().each(function() {
		    var btn = $('<div class="terraclassifieds-type-val terraclassifieds-type-' + $(this).attr( "value" ) + '" data-attr="' + $(this).attr( "value" ) + '">'+$(this).text()+'</div>');
		   	let hiddenValue = hidden.val();
		   	
		    if(typeof($.urlParam('ad_type')) != 'undefined' && $.urlParam('ad_type') != 0){
		    	if($.urlParam('ad_type').indexOf($(this).attr( "value" )) != -1){
		    		btn.addClass('on');
		    	}
		    }
			
			hiddenValue = hiddenValue.replace("%2B", "+");
			hidden.val(hiddenValue);
		    $(this).replaceWith(btn);
		});
		
		$(document).on('click', '.terraclassifieds-type-val', function() {
		    if($(this).hasClass("on")){
		    	$(this).removeClass("on");
		    	$('input[name="'+selectName+'"]').val($('input[name="'+selectName+'"]').val().replace($(this).attr( "data-attr" ) + "+", ""));
		    	$('input[name="'+selectName+'"]').val($('input[name="'+selectName+'"]').val().replace($(this).attr( "data-attr" ), ""));
				if ($('input[name="'+selectName+'"]').val().substr(-1) === "+") {
				    $('input[name="'+selectName+'"]').val($('input[name="'+selectName+'"]').val().substring(0,$('input[name="'+selectName+'"]').val().length - 1));
				}
		    } else{
		    	$(this).addClass('on');
		    	if($('input[name="'+selectName+'"]').val() == ""){
		    		$('input[name="'+selectName+'"]').val($(this).attr( "data-attr" ));
		    	} else{
		    		$('input[name="'+selectName+'"]').val($('input[name="'+selectName+'"]').val() + "+" + $(this).attr( "data-attr" ));
		    	}
		    }
		});
		// type select -> type buttons - END


		// selling types search - BEGIN
		if($.urlParam('sell_type') != 0){
			$('select#sell_type option[value="' + $.urlParam('sell_type') + '"]').attr("selected","selected");
		}
		// selling types search - END
		
		$('.terraclassifieds-clear-button').click(function() {
			$('.terraclassifieds-product-search-field').attr('value','');
			$('.terraclassifieds-search-filter-price input').attr('value','');
			$("select#ad_category").val(0);
			$("select#sell_type").val(0);
			$("select#ad_location").val('').trigger('change');
			$("#select2-ad_location-container").text(php_vars.allLocations);
			$(".terraclassifieds-search-type input").val('');
			$(".terraclassifieds-search-type .terraclassifieds-type-val").removeClass('on');
		});
		
		$('.terraclassifieds-search form .terraclassifieds-more').click(function() {
			$(this).toggleClass("more-open");
			$(this).next('.terraclassifieds-search-type').slideToggle();
		});
		
		// placeholders for price filter - BEGIN
		let priceFilterInput = $('.terraclassifieds-search-filter-price-field input');
		let priceFilterInputFrom = $('.terraclassifieds-search-filter-price-field input[name="price-min"]');
		let priceFilterInputTo = $('.terraclassifieds-search-filter-price-field input[name="price-max"]');
		if(priceFilterInput.length > 0){
			priceFilterInput.focus(function() {
				$(this).removeAttr('placeholder');
			});
			priceFilterInputFrom.blur(function() {
				$(this).attr("placeholder", php_vars.priceFilterInputFrom);
			});
			priceFilterInputTo.blur(function() {
				$(this).attr("placeholder", php_vars.priceFilterInputTo);
			});
		}
		// placeholders for price filter - END
	}
	
	function tcfLatestAds(){
		var latestAdsConatiner = $('.terraclassifieds-latest-ads-list');
		if(latestAdsConatiner.length > 0){
			tcfEqualHeight(latestAdsConatiner.children().children().children('.terraclassifieds-image'));
		}
	}
	
	function tcfMenu(){
		let tcfMenu = $('.terraclassifieds-usermenu');
		if (tcfMenu.length > 0) {
			$('ul.terraclassifieds-usermenu > li > span').click(function() {
				$(this).next('.nav-dropdown').toggleClass('active');
			});
		}
	}
	
	
	
	/* -------------------------------------------------------- */
	/* READY state                                              */
	/* -------------------------------------------------------- */

	$(document).ready(function(){

		$('.terraclassifieds-contact-form .terraclassifieds-contact-advertiser').click(function() {
			$(this).next('form').slideToggle().toggleClass('open');
		});
	   
	   // ADD ADVERT FORM
	   tcfAddAdvertForm();
	   
	   // SINGLE AD
		tcfSingleAdd();
		
		// REGISTRATION
		tcfRegistration();
		
		// EDIT PROFILE
		tcfEditProfile();
		
		// TCF MENU
		tcfMenu();
		
		// SEARCH
		tcfSearch();
		
	});

	/* -------------------------------------------------------- */
	/* end READY state                                          */
	/* -------------------------------------------------------- */
	
	/* -------------------------------------------------------- */
	/* LOAD state                                               */
	/* -------------------------------------------------------- */
	$(window).on("load", function () {
		
		// LATEST ADS - widget
		tcfLatestAds();
		
	});
	/* -------------------------------------------------------- */
	/* end LOAD state                                           */
	/* -------------------------------------------------------- */
	
})(jQuery);
